<?php

/**
 * bp_grouptypes_admin_newgroup()
 *
 * Adds form to create a new category group.
 */
function bp_grouptypes_admin_newgroup() {
	global $bp;
	
	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) /*&& check_admin_referer('grouptypes-settings')*/ ) {
		
		$groupid=groups_create_group( array(
			'creator_id'	=> $bp->loggedin_user->id,
			'name'			=> $_POST['name'],
			'slug'			=> sanitize_title_with_dashes($_POST['name']),
			'description'	=> $_POST['description'],
			'status'		=> $_POST['group-status'],
			'enable_wire'	=> true,
			'enable_forum'	=> $_POST['group-show-forum'],
			'date_created'	=> gmdate('Y-m-d H:i:s')
			)
		);
		if(groups_update_groupmeta($groupid,'parent_cat',$_POST['parent-cat']) ) 
		$updated = true;
		groups_update_groupmeta( $groupid, 'total_member_count', 1 );
		groups_update_groupmeta($groupid,'group_type','category');
	}

?>
	<div class="wrap">
		<h2><?php _e( 'Add new category group(all fields required)', 'bp-grouptypes' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-grouptypes' ) . "</p></div>" ?><?php endif; ?>

		<form action="" name="grouptypes-new-cat-form" id="grouptypes-new-cat-form" method="post">

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e( 'Group Name', 'bp-grouptypes' ) ?></label></th>
					<td>
						<input name="name" type="text" id="grouptypes-group-name" size="60" />
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Group Description', 'bp-grouptypes' ) ?></label></th>
					<td>
						<textarea name="description" id="grouptypes-group-desc" rows="2" cols="20" > </textarea>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="group-show-forum"><?php _e( 'Enable Discussion Forum', 'bp-grouptypes' ) ?></label></th>
					<td>
						<input type="checkbox" name="group-show-forum" id="group-show-forum" />
					</td>
				</tr>
				
				<tr>
					<td>
						<label> 
							<input type="radio" name="group-status" value="public" checked="checked" /> 
							<strong>This is a public group</strong> 
							<ul> 
								<li>Any site member can join this group.</li> 
								<li>This group will be listed in the groups directory and in search results.</li> 
								<li>Group content and activity will be visible to any site member.</li> 
							</ul> 
						</label> 
				 
						<label> 
							<input type="radio" name="group-status" value="private" /> 
							<strong>This is a private group</strong> 
							<ul> 
								<li>Only users who request membership and are accepted can join the group.</li> 
								<li>This group will be listed in the groups directory and in search results.</li> 
								<li>Group content and activity will only be visible to members of the group.</li> 
							</ul> 
						</label> 
				 
						<label> 
							<input type="radio" name="group-status" value="hidden" /> 
							<strong>This is a hidden group</strong> 
							<ul> 
								<li>Only users who are invited can join the group.</li> 
								<li>This group will not be listed in the groups directory or search results.</li> 
								<li>Group content and activity will only be visible to members of the group.</li> 
							</ul> 
						</label>

					</td>
				</tr>
				<tr>
					<th scope="row"><label for="target_uri"><?php _e('Parent Categories Group Id (-1 if global category)', 'buddypress') ?></label></th>
					<td>
						<input type="text" name="parent-cat" id="parent-cat" value="0" />
						<ul> 
						<li>Enter -1 if you want it to be the highest in the hiarchy.</li> 
						<li>Enter 0 if you want it to not be included in the hiarchy page, but still have sub groups.</li> 
						<li>Or just enter the id of another category group in order to make this a sub group.</li> 
					</ul> 
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-grouptypes' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'grouptypes-settings' );
			?>
		</form>
	</div>
<?php
}
?>