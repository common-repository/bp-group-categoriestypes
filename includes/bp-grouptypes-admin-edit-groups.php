<?php

/**
 * bp_grouptypes_admin_edit_groups()
 *
 * Populates a table with all groups of type category and allows user to edit parent id.
 */
//TODO add in a dropdown box which lets user select which type of groups they want to edit
// also add in the ability to change the group type
function bp_grouptypes_admin_edit_groups() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) ) {
		if(groups_update_groupmeta($_POST['group-id'], 'parent_cat',$_POST['parent-id']))
		$updated = true;
	}

?>
	<div class="wrap">
		<h2><?php _e( 'Group Types Admin', 'bp-grouptypes' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-example' ) . "</p></div>" ?><?php endif; ?>
	<?php
	if( isset ( $_POST['grouptypes_delete']) && isset ($_POST['grouptypes'])) {
		$catgroup = new BP_Category_Group();
		$checked = $_POST['grouptypes'];
		foreach( $checked as $groupid){
			$catgroup->id=$groupid;
			$updated=$catgroup->delete();
		}
	}
	if( isset ( $_POST['grouptypes_edit']) && isset ($_POST['grouptypes'])):
		$editgroup = new BP_Category_Group();
		$checked = $_POST['grouptypes'];
		if(is_array($checked))
		foreach( $checked as $groupid){
			$editgroup->group_id=$groupid;
			$editgroup->populate();
		}
	?>
			<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-grouptypes-edit-groups' ?>" name="grouptypes-settings-form" id="grouptypes-settings-form" method="post">
		<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Group Id','bp_grouptypes') ?></th>
					<td>
						<?php _e( $editgroup->group_id, 'bp_grouptypes') ?>
						<input name="group-id" type="hidden" id="bp_grouptypes_groupid" value="<?php echo esc_attr( $editgroup->group_id ); ?>" size="60" />
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Group Name', 'bp-grouptypes' ) ?></th>
					<td>
						<?php
							$groupnam= new BP_Groups_Group($editgroup->group_id);
							_e( $groupnam->name, 'bp_grouptypes' );
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Parent Id', 'bp-grouptypes' ) ?></label></th>
					<td>
						<input name="parent-id" type="text" id="parent_id" value="<?php echo esc_attr( $editgroup->parent_group_id ); ?>" size="60" />
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Parent Name' , 'bp_grouptypes') ?></th>
					<td>
						<?php 
							$parentgroup= new BP_Groups_Group($editgroup->parent_group_id);
							_e( $parentgroup->name, 'bp_grouptypes' ); 
						?>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Edit Group', 'bp-mlgfantasy' ) ?>"/>
			</p>
			</form>
	<?php
	endif;
	?>
	
	
	
		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-grouptypes-edit-groups' ?>" name="mlgfantasy-settings-form" id="mlgfantasy-settings-form" method="post">
		<table class="bp-grouptypes"><thead>
		<tr>
			<th scope="col">Select</th>
			<th scope="col">Group Id</th>
			<th scope="col">Group Name</th>
			
			<th scope="col">Parent Id</th>
			<th scope="col">Parent Name</th>

		</tr></thead>

		<tbody>
			<?php 
			$catids=BP_Category_Group::get_all_cats();
			foreach($catids as $catid):
				$catgroup=new BP_Groups_Group($catid);
				$parentgroup=new BP_Groups_Group(groups_get_groupmeta($catid, 'parent_cat'));
				
		?>
			<tr>
				<td><input type="checkbox" value="<?php echo $catid; ?>" name ="grouptypes[<?php echo $catid; ?>]"/></td>
				<td><?php echo $catid; ?></td>
				<td><?php echo $catgroup->name; ?></td>
				<td><?php echo $parentgroup->id; ?></td>
				<td><?php echo $parentgroup->name; ?></td>
			</tr>	
			<?php endforeach;?>	
		</tbody>
		</table>
		<input type="submit" name="grouptypes_delete" value="Delete Selected" class="button" />
		<input type="submit" name="grouptypes_edit" value="Edit Selected (only one)" class="button" />
		</form>
	</div>
<?php
}
?>