<?php
/*
Plugin Name: BP Group Categories/Types
Description:This plugin is for buddypress enabled sites.  It allows you to create a hierarchy of groups.  Allowing you to categorize your groups on
as many levels as you want. (ex. Systems->Xbox 360->Games->Halo 3->Legendary Map Pack)
Version: 0.1-alpha
Revision Date: July 21, 2010
Requires at least: WordPress 2.9.1, BuddyPress 1.2.4.1
Tested up to: WordPress 3 / BuddyPress 1.2.5
License: GNU
Author: Tyler Rice
Site Wide Only: true
*/
/* Only load the component if BuddyPress is loaded and initialized. */
function bp_grouptypes_init() {
	require( dirname( __FILE__ ) . '/includes/bp-grouptypes-core.php' );
}
add_action( 'bp_init', 'bp_grouptypes_init' );

function bp_grouptypes_activate() {
	global $wpdb;
//TODO add a function here that sets a group type and group category for every group already created on the site
	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );
}
register_activation_hook( __FILE__, 'bp_grouptypes_activate' );

function bp_grouptypes_deactivate() {
//do nothing
}
register_deactivation_hook( __FILE__, 'bp_grouptypes_deactivate' );
?>