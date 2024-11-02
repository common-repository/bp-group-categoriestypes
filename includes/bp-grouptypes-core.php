<?php

/* Define a constant that can be checked to see if the component is installed or not. */
define ( 'BP_GROUPTYPES_IS_INSTALLED', 1 );

/* Define a constant that will hold the current version number of the component */
define ( 'BP_GROUPTYPES_VERSION', '0.1-alpha' );

//TODO cleanup the classes file
/* The classes file should hold all database access classes and functions */
require ( dirname( __FILE__ ) . '/bp-grouptypes-classes.php' );

function bp_grouptypes_setup_globals() {
	global $bp, $wpdb;
	//don't need this yet put here as a reminder
}

function bp_grouptypes_add_admin_menu_page(){
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/bp-grouptypes-admin-edit-groups.php' );
	
	add_menu_page( 'Edit Category Groups', 'BP Group Types Admin', 'manage_options', 'bp-grouptypes-edit-groups', 'bp_grouptypes_admin_edit_groups');
}
add_action( 'admin_menu' , 'bp_grouptypes_add_admin_menu_page');

function bp_grouptypes_add_admin_menu_new_cat() {
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/bp-grouptypes-admin-newgroup.php' );

	add_submenu_page( 'bp-grouptypes-edit-groups', __( 'New Category Group', 'bp-grouptypes' ), __( 'New Category Group', 'bp-grouptypes' ), 'manage_options', 'bp-grouptypes-admin-newgroup', 'bp_grouptypes_admin_newgroup' );
}
add_action( 'admin_menu', 'bp_grouptypes_add_admin_menu_new_cat' );

//TODO use this function to create the dropdown.. also seperate the dropdowns into a hierarchy too
//which will make it easier for users to select where to post to in the /forums directory
/**
 * bp_grouptypes_add_types_dropdown()
 *
 * This funtion will add a dropdown box for the different types of groups that were set-up
 * inside of Group Types Admin page.
 */

function bp_grouptypes_add_types_dropdown(){
	global $bp;
	if($bp->is_item_admin): ?>
		<label for="parent-cat"><?php _e('Parent Categories Group Id (-1 if global category)', 'buddypress') ?></label>
		<input type="text" name="parent-cat" id="parent-cat" value="0" />
		
		<p>Type of group to create</p>
			<input type="radio" name="grouptype" value="category" /><?php _e('Category Group','buddypress') ?>
			<input type="radio" name="grouptype" value="user_group" /><?php _e('User Group','buddypress') ?>
	<?php endif; ?>
		<br/>
		<?php _e('Which category is this group under?', 'buddypress') ?>
		<select name="parent-catuser">
			<?php
			$cats = new BP_Category_Group();
			$cat_ids = $cats->get_all_cats();
			if($cat_ids)
			foreach($cat_ids as $cat_id): 
				$group = new BP_Groups_Group($cat_id);
			?>
				<option value="<?php echo $cat_id; ?>"><?php _e($group->name,'buddypress'); ?></option>
			<?php
			endforeach;
			?>
		</select>
		
	<?php
}

//this is used to add in the box in the groups directory telling the user which type the group is
function bp_grouptypes_add_grouptype_box(){
	global $bp, $groups_template;
	$group=new BP_Category_Group($groups_template->group->id);
	$group_type='User';
	if(groups_get_groupmeta($groups_template->group->id, 'group_type')=='category')
		$group_type='Category';
	?>
	<span class="group-type" style="background: #f0f0f0; color: #888; font-size: 10px; padding: 2px 5px; -moz-border-radius: 3px"><?php echo $group_type.' Group'?></span>
	<?php
}
add_action('bp_directory_groups_actions', 'bp_grouptypes_add_grouptype_box');

?>