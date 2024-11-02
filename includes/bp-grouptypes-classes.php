<?php
class BP_Category_Group {
	var $group_id;
	var $parent_group_id;
	
	/**
	 * BP_Category_Group()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 */
	function BP_Category_Group( $group_id = null ) {
		global $wpdb, $bp;
		
		if ( $group_id ) {
			$this->group_id = $group_id;
			
			$this->populate( $this->group_id );
		}
	}
	
	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 */
	function populate() {
		global $wpdb, $bp, $creds;
		$this->parent_group_id=groups_get_groupmeta($this->group_id, 'parent_cat');
		
	}
	
	function is_cat_group(){
		global $bp;
		if(groups_get_groupmeta($bp->groups->current_group->id, 'group_type')=='category')
			return true;
		return false;
	}
	
	function has_sub_categories(){
		global $wpdb,$bp;
		$sql = $wpdb->prepare( "SELECT * FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'parent_cat' AND meta_value = %d", $bp->groups->current_group->id );
		if( $wpdb->get_row( $sql ))
			return true;
		return false;
	}
	
	function has_sub_groups(){
		global $wpdb,$bp;
		$sql = $wpdb->prepare( "SELECT gmcat.groupid FROM {$bp->groups->table_name_groupmeta} gmcat, {$bp->groups->table_name_groupmeta} gmtype WHERE gmcat.meta_key = 'parent_cat' AND gmcat.meta_value = %d AND gmtype.meta_key = 'group_type' AND gmtype.meta_value = 'user_group'", $bp->groups->current_group->id );
		if( $wpdb->get_var( $sql ))
			return true;
		return false;
	}
	
	function get_all_cats(){
		global $wpdb, $bp;
		$sql = $wpdb->prepare( "SELECT DISTINCT gmcat.group_id FROM {$bp->groups->table_name_groupmeta} gmcat, {$bp->groups->table_name_groupmeta} gmtype WHERE gmcat.meta_key = 'parent_cat' AND gmtype.meta_key = 'group_type' AND gmtype.meta_value = 'category'" );
		return $wpdb->get_col( $sql );
	}
	
	/**
	 * get_sub_cats()
	 *
	 * Accepts three parameters group type, page number, and limit.
	 * Returns sub category group ids based off of parameters. 
	 */	
	function get_sub_cats($group_type = null, $page = null, $limit = null){
		global $wpdb, $bp;
		if( $page && $limit ){
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) ); 
		}
		$sql = $wpdb->prepare( "SELECT DISTINCT gmcat.group_id, gmcat.meta_key, gmcat.meta_value as parent_cat, gmtype.meta_key, gmtype.meta_value as group_type FROM {$bp->groups->table_name_groupmeta} g, {$bp->groups->table_name_groupmeta} gmcat, {$bp->groups->table_name_groupmeta} gmtype WHERE  gmcat.meta_key = 'parent_cat' AND gmcat.meta_value = {$this->group_id} AND gmtype.meta_key = 'group_type' AND gmtype.meta_value = '{$group_type}'  {$pag_sql}" );
		$paged_groups = $wpdb->get_col( $sql );	
		
		$sql = $wpdb->prepare( "SELECT DISTINCT gmcat.group_id, gmcat.meta_key, gmcat.meta_value as parent_cat, gmtype.meta_key, gmtype.meta_value as group_type FROM {$bp->groups->table_name_groupmeta} g, {$bp->groups->table_name_groupmeta} gmcat, {$bp->groups->table_name_groupmeta} gmtype WHERE  gmcat.meta_key = 'parent_cat' AND gmcat.meta_value = {$this->group_id} AND gmtype.meta_key = 'group_type' AND gmtype.meta_value = '{$group_type}' AND gmcat.group_id = gmtype.group_id" );
		$total_groups = $wpdb->get_col( $sql );
		$count=0;
		//todo get rid of foreach use COUNT DISTINCT instead
		foreach($total_groups as $group_id)
			$count++;
		$total_groups=$count;
		return array( 'groups' => $paged_groups, 'total' => $total_groups );
	}

/*  Dont need these anymore	save incase needed in later version
	function is_sub_cat($id){
		global $wpdb, $bp;
		$sql = $wpdb->prepare( "SELECT * FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'parent_cat' AND meta_value = %d AND group_id = %d", $this->group_id , $id );
		if($wpdb->get_row( $sql ))
			return true;
		return false;
	}
	
	function is_user_group($id){
		global $wpdb, $bp;
		$sql = $wpdb->prepare( "SELECT * FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'parent_cat' AND meta_value = %d AND group_id = %d", $this->group_id , $id );
		if($wpdb->get_row( $sql ))
			return true;
		return false;
	}
*/	
// TODO combine display_categories() and display_userGroups() into one function and add in a group type parameter
	function display_categories(){
		global $bp;
		$urlstr = $_SERVER["REQUEST_URI"];
		$current=strpos($urlstr,'?page=');
		if(!$current){
			$current=1;
		}
		else
			$current=substr($urlstr,(int)($current)+6);
		$limit = 5;
		$subcats=$this->get_sub_cats('category', $current, $limit);		
		$total_subcats = $subcats['total'];
		$paged_subcats = $subcats['groups'];
		$paged_subcats_num = 0;
		foreach($paged_subcats as $groupid)
			 $paged_subcats_num++;
		$pagination = array(
	  'base' => site_url() . '/' . $bp->groups->slug . '/' . bp_get_group_slug() . '/sub-categories%_%', //%_% is replaced by the format below
	  'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
	  'total' => round(($total_subcats / $limit)+0.4),
	  'current' => $current,
	  'show_all' => false,
	  'prev_next' => true,
	  'prev_text' => __('&larr;'),
	  'next_text' => __('&rarr;'),
	  'end_size' => 5,
	  'mid_size' => 2,
	  'type' => 'plain',
	  'add_args' => false, // array of query args to add
	  'add_fragment' => ''
		);
		?>
		<h4>Sub Categories</h4>
		<?php
		if($current>1){
			$first_group=(($current-1)*$limit)+1;
			$sec_group = ($first_group)+$paged_subcats_num-1;
		} else{
			$first_group=1;
			$sec_group = $paged_subcats_num;
		}
		?>
		<div class="pag-count" id="group-dir-count">Viewing group <?php echo $first_group ?> to <?php echo $sec_group ?> (of <?php echo $total_subcats ?> groups)</div>
		<?php
	  	echo paginate_links( $pagination );
	  	?>
	  	<ul id="groups-list" class="item-list">
	  	<?php
		foreach($paged_subcats as $groupid){
			$group = new BP_Groups_Group($groupid);
			 if ( !$avatar = bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'avatar_dir' => 'group-avatars', 'alt' => __( 'Group avatar', 'buddypress' ), 'id' => $group->id, 'class' => 'avatar' ) ) )
             	$avatar = '<img src="' . attribute_escape( $group->avatar_thumb ) . '" class="avatar" alt="' . attribute_escape( $group->name ) . '" />';
 
			?>
			<li>
					<div class="item-avatar">
						<a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/' . $group->slug . '/'  ?>"><?php echo $avatar;//bp_get_group_avatar( 'id='.$group->id ) ?></a>
					</div>
		
					<div class="item">
						<div class="item-title"><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/' . $group->slug . '/' ?>"><?php echo $group->name ?></a></div>
						<div class="item-meta"><span class="activity"><?php printf( __( 'active %s ago', 'buddypress' ), bp_get_group_last_active($group) ) ?></span></div>
		
						<div class="item-desc"><?php echo bp_create_excerpt( bp_get_group_description($group), 20) ?></div>
		
						<?php do_action( 'bp_directory_groups_item' ) ?>
					</div>
					
					<div class="action">
						<?php bp_group_join_button($group) ?>

					<div class="meta">
						<?php bp_get_group_type($group) ?>
					</div>

						<?php do_action( 'bp_directory_groups_actions' ) ?>
					</div>
		
					<div class="clear"></div>
			</li>
			
			<?php	
		}
		?>
		</ul>
		<?php
		echo paginate_links( $pagination );	
	}
	
	function display_userGroups(){
		global  $bp;
		$urlstr = $_SERVER["REQUEST_URI"];
		$current=strpos($urlstr,'?page=');
		if(!$current){
			$current=1;
		}
		else
			$current=substr($urlstr,(int)($current)+6);
		$limit = 20;
		$subcats=$this->get_sub_cats('user_group', $current, $limit);
		$total_subcats = $subcats['total'];
		$paged_subcats = $subcats['groups'];
		$paged_subcats_num = 0;
		foreach($paged_subcats as $groupid)
			 $paged_subcats_num++;
		$pagination = array(
	  'base' => site_url() . '/' . $bp->groups->slug . '/' . bp_get_group_slug() . '/groups%_%', //%_% is replaced by format (below)
	  'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
	  'total' => round(($total_subcats / $limit)+0.4),
	  'current' => $current,
	  'show_all' => false,
	  'prev_next' => true,
	  'prev_text' => __('&larr;'),
	  'next_text' => __('&rarr;'),
	  'end_size' => 5,
	  'mid_size' => 5,
	  'type' => 'plain',
	  'add_args' => false, // array of query args to add
	  'add_fragment' => ''
		);
		?>
					<h4>Groups</h4>
		<?php
		if(!((int)$total_subcats>0)):
		?>
			<div id="message"><p>Sorry there are no groups filed under this category.  You can create one if you like.</p></div>
		<?php
		else:
		if($current>1){
			$first_group=(($current-1)*$limit)+1;
			$sec_group = ($first_group)+$paged_subcats_num-1;
		} else{
			$first_group=1;
			$sec_group = $paged_subcats_num;
		}
		?>
		<div class="pag-count" id="group-dir-count">Viewing group <?php echo $first_group ?> to <?php echo $sec_group ?> (of <?php echo $total_subcats ?> groups)</div>
		<?php
		endif;
		echo paginate_links($pagination);
		?>
			<ul id="groups-list" class="item-list">
		<?php
		foreach($paged_subcats as $groupid){
			$group = new BP_Groups_Group($groupid);
			if ( !$avatar = bp_core_fetch_avatar( array( 'item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'avatar_dir' => 'group-avatars', 'alt' => __( 'Group avatar', 'buddypress' ), 'id' => $group->id, 'class' => 'avatar' ) ) )
             	$avatar = '<img src="' . attribute_escape( $group->avatar_thumb ) . '" class="avatar" alt="' . attribute_escape( $group->name ) . '" />';
 
			?>
			<li>
					<div class="item-avatar">
						<a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/' . $group->slug . '/'  ?>"><?php echo $avatar;//bp_group_avatar( 'type=thumb&width=50&height=50&class=avatar&id='.$group->id ) ?></a>
					</div>
		
					<div class="item">
						<div class="item-title"><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/' . $group->slug . '/' ?>"><?php echo $group->name ?></a></div>
						<div class="item-meta"><span class="activity"><?php printf( __( 'active %s ago', 'buddypress' ), bp_get_group_last_active($group) ) ?></span></div>
		
						<div class="item-desc"><?php bp_create_excerpt( $group->description, 20 ) ?></div>
		
						<?php do_action( 'bp_directory_groups_item' ) ?>
					</div>
		
					<div class="action">
						<?php bp_group_join_button($group) ?>
		
						<div class="meta">
							<?php echo bp_get_group_type($group) ?> / <?php echo $group->total_member_count; ?>
						</div>
		
						<?php do_action( 'bp_directory_groups_actions' ) ?>
					</div>
		
					<div class="clear"></div>
			</li>
			
			<?php	
		}
		?>
		</ul>
		<?php
		echo paginate_links( $pagination );
	}
	
}
// TODO combine all these group extensions into one if possible and move these out of here
class BP_Group_Categories extends BP_Group_Extension {	

	function BP_Group_Categories() {
		global $bp;
		$this->name = 'Sub Categories';
		$this->slug = 'sub-categories';
		$this->visibilty='public';
		$this->enable_edit_item=false;
		$this->enable_create_step = false;
		$this->nav_item_position = 41;
		$this->enable_nav_item = $this->enable_nav_item(); // make sure this is a categories group and it has sub categories
		bp_core_remove_subnav_item( $bp->groups->slug, 'send-invites' );
		bp_core_remove_subnav_item( $bp->groups->slug, 'members');
	}

	function create_screen() {

	}

	function create_screen_save() {

	}

	function edit_screen() {

	}

	function edit_screen_save() {
	}

	function display() {
		/* Use this function to display the actual content of your group extension when the nav item is selected */
		global $bp,$wp_query;
		$displaygroup= new BP_Category_Group($bp->groups->current_group->id);
		$displaygroup->display_categories();
	}
	
	function enable_nav_item() {
	if ( ( BP_Category_Group::has_sub_categories() ) )
		return true;
	else
		return false;
	}

	function widget_display() { 
		
	}
}
if(BP_Category_Group::is_cat_group())
bp_register_group_extension( 'BP_Group_Categories' );

class BP_Group_Categories_UserGroups extends BP_Group_Extension {	

	function BP_Group_Categories_UserGroups() {
		global $bp;
		$this->name = 'Groups';
		$this->slug = 'groups';
		$this->visibilty='public';
		$this->enable_edit_item=false;
		$this->enable_create_step = false;
		$this->nav_item_position = 51;
		$this->enable_nav_item = true; // make sure this is a categories group and it has sub categories
		bp_core_remove_subnav_item( $bp->groups->slug, 'send-invites' );
		bp_core_remove_subnav_item( $bp->groups->slug, 'members');
	}

	function create_screen() {

	}

	function create_screen_save() {

	}

	function edit_screen() {

	}

	function edit_screen_save() {
	}

	function display() {
		// Use this function to display the actual content of your group extension when the nav item is selected 
		global $bp;
		$displaygroup= new BP_Category_Group($bp->groups->current_group->id);
		$displaygroup->display_userGroups();
	}

	function widget_display() { ?>
		<div class="info-group">
			<h4><?php echo attribute_escape( $this->name ) ?></h4>
			<p>
				You could display a small snippet of information from your group extension here. It will show on the group
				home screen.
			</p>
		</div>
		<?php
	}
}
if(BP_Category_Group::is_cat_group())
bp_register_group_extension( 'BP_Group_Categories_UserGroups' );

	function display_breadcrumb(){
		global $bp;
		?>
		<div style="float:left;"id="bp-group-cat-breadcrumb">
		<div style="float:left;margin:0;padding:0;">
					<a title="Go To Home." href="<?php echo $bp->root_domain . '/'; ?>">Home</a>
		</div>
		<div style="float:left;margin:0;padding:0;">
					&rarr; <a title="Go To Groups." href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/'; ?>">Groups</a>
		</div>
		<?php
		$breadcrumb="";
		$group_id=$bp->groups->current_group->id;
			while($group_id!=-1&&$group_id!=0):
				$group= new BP_Groups_Group($group_id);
				$breadcrumb=substr_replace($breadcrumb,'<div style="float:left;margin:0;padding:0;">&rarr; <a title="Go To Group '.$group->name.'." href="'.bp_get_group_permalink($group).'">'.$group->name.'</a></div>',0,0);
				$group_id=groups_get_groupmeta($group_id, 'parent_cat');
			endwhile;
			echo $breadcrumb;
		?></div>
		<?php
	}
	add_action('bp_after_group_header' , 'display_breadcrumb');
	function display_create(){
		global $bp;
		?>
				<label for="target_uri"><?php _e( 'How to categorize this group?', 'bp-group-types' ) ?></label>
				<select name="bp-groupcats-parent-group">
 				<option selected="selected"value="0">No Category</option>
				 			<?php $catgroups = BP_Category_Group::get_all_cats();
				 			foreach($catgroups as $catid):
				 				$catgroup = new BP_Groups_Group($catid);
				 				$parent = (int)groups_get_groupmeta($bp->groups->current_group->id,'parent_cat');
				 				?>	
				    			<option <?php if($parent==$catid) echo'selected="selected"' ?>value="<?php echo $catid; ?>"><?php echo $catgroup->name; ?></option>
							<?php endforeach; ?>
				</select>
		<?php if(is_site_admin()):
			if(groups_get_groupmeta($bp->groups->current_group->id, 'group_type')=='category')
				$current_type=groups_get_groupmeta($bp->groups->current_group->id, 'group_type');
			else
				$current_type='user_group';
		?>
		<label for="target_uri"><?php _e( 'What type of group?', 'bp-group-types' ) ?></label>
			<input type="radio" name="bp-group-type" value="user_group" <?php if($current_type=='user_group') echo 'CHECKED'  ?> /> User Group<br />
			<input type="radio" name="bp-group-type" value="category" <?php if($current_type=='category') echo 'CHECKED' ?> /> Category
		<?php
		endif;
	}

	function display_create_save(){
		global $bp;
		if(isset($_POST['bp-groupcats-parent-group']))
			groups_update_groupmeta($bp->groups->current_group->id, 'parent_cat',$_POST['bp-groupcats-parent-group']);
		else
		groups_update_groupmeta($bp->groups->current_group->id,'parent_cat', 0);
		if(isset($_POST['bp-group-type']))
		groups_update_groupmeta($bp->groups->current_group->id,'group_type',$_POST['bp-group-type']);
		else
		groups_update_groupmeta($bp->groups->current_group->id,'group_type','user_group');
	}

class BP_Group_Categories_Create extends BP_Group_Extension {	

	function BP_Group_Categories_Create() {
		global $bp;
		$this->name = 'Category';
		$this->slug = 'category';
		$this->visibilty='public';
		$this->enable_edit_item=true;
		$this->create_step_position = 2;
		$this->enable_nav_item = false;
	}

	function create_screen() {
		global $bp;
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		
		display_create();
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {
		global $bp;
		check_admin_referer( 'groups_create_save_' . $this->slug );
		display_create_save();
	}

	function edit_screen() {
		global $bp;
		display_create();
		?>
		<br /><br />
		<input type="submit" name="save" value="Save Category Settings" />
		<?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}

	function edit_screen_save() {
		global $bp;
		if(!isset($_POST['save']))
			return false;
		check_admin_referer( 'groups_edit_save_' . $this->slug );
		display_create_save();
	}

	function display() {

	}

	function widget_display() {

	}
}
bp_register_group_extension( 'BP_Group_Categories_Create' );

?>