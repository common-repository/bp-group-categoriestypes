=== BP Group Categories/Types ===
Contributors: firetag
Tags: buddypress, BuddyPress, hierarchy, group types, group hierarchy,categories,group categories
Requires at least: WordPress 2.9.1, BuddyPress 1.2.4.1
Tested up to: WordPress 3, BuddyPress 1.2.5
Stable tag: 0.1-alpha

!!alpha-release!!
Allows you to categorize your groups in buddypress. (ex. Systems->Xbox 360->Games->Halo 3->Legendary Map Pack)

== Description ==

!!alpha-release!!
DO NOT install on a live site because the plugin hasn't been very extensively tested.  This plugin allows you to create an unlimited hierarchy of groups.  
This plugin currently allows you to create two different types of groups.  Category and User groups, category groups are what allow you
to create the hierarchy of groups.  Category groups do not have members attached to them.  When you visit a category group you
will see two extra sub nav items, Sub Categories and Groups.  The Sub Categories page displays all the category groups categorized under
the group you are currently viewing.  The Groups page displays all the groups (which are user created) that are also categorized
under the group you're currently viewing.  Category Groups can only be created by the site admin, they can be created in wp-admin
or you can choose to create a category group instead of a user group when you visit /groups/create.


== Installation ==

1. Check your buddypress site and make sure you don't have any groups already created.
2. Download the plugin.
3. Unzip and upload to plugins folder.
4. Activate the plugin.

== Changelog ==
= 0.1-alpha =
* First rlease

== Notes ==

License.txt - contains the licensing details for this component.

== Roadmap ==
* Better activity integration
* Create a super group which everyone belongs to and allow people to post to it via /forums
* Add a tab in the groups directory for each global group created
* Exclude Certain plugins from being active inside certain categories (Allowing you to create different group types)

== Bugs ==
* If a user only finishes the first group creation step the group is created, but group_type and parent_cat meta is never set.
  Causes the effected group to not be included in the hierarchy.  Kind of trivial for now since I have'nt yet added the tabs for global groups
  into the groups direectory.