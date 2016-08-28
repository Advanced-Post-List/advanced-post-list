== Changelog ==

= 0.3.0 = 08/29/2016
* Fixed error 'Un-Defined Variables and Non-Objects' with Excluding Duplicates.
* Fixed List Amount '-1' Returning Nothing.
* Fixed Any/All Selection not properly querying Terms within the Taxonomy.
* Changed Admin Header Links in APL Admin Settings Page.
* Updated GPLv2 Information.

= 0.3.b9 = 07/07/2016
* Added '[post_terms]' Internal Shortcode function to add Custom Taxonomy Terms.
* Fixed MultiSelect Button Width using default width.
* Updated Google APIs jQuery UI CSS version 1.8.21 to 1.11.4 to match WP 4.5.3 jQuery UI JS version.
* Updated jQuery MultiSelect UI Widget files version 1.12 to 1.14.

= 0.3.b8 = 05/31/2014
* Added Widget support.
* Added param label to author shortcode.
* Added param size to post_thumb.
* Fixed warning errors with WP's debug config enabled.
* Fixed Taxonomy Tabs not showing category/tag text.
* Fixed AJAX Save Preset Button not updating text.
* Fixed Save Preset Button enlarging each click.
* Fixed Admin Preview not showing.
* Fixed (preview) post list dynamics on admin side.
* Fixed Delete Database Upon Deactivation no saving to APL database.
* Fixed jQuery UI CSS file not loading with https sites.
* Changed APLQuery class to reduce memory load, and remove nested code.

= 0.3.b7 = 10/08/2013
* Fixed Admin UI Multiselect jQuery plugin not closing.
* Fixed Multiselect not updating values.
* Changed Admin Header Links in the Settings page.
* Changed Screenshots.

= 0.3.b6 = 12/04/2012
* Added Label Elements to Admin Option Settings.
* Added Info Dialogs to the General Options Section.
* Added Updater Class Object.
* Added Backwards Compatibility to APLPresetDbObj & APLPresetObj Class.
* Added More Security to the Backup Feature.
* Added Version Variable to Export Output File.
* Added an Overwrite Confirmation Before Finalizing the Import Operation.
* Fixed 'Maximum execution time of 30 seconds exceeded'.
* Fixed Issue with jQuery UI 'Current Page' Parent Selector.
* Fixed/Changed Import/Export Layout & Elements. 
* Fixed Backup (Export/Download/Import) Feature.
* Fixed JSON Converter to Display PHP Errors Instead of Breaking.
* Fixed Import to Update Preset(s) being imported.
* Changed jQuery UI Dialog Messages to use a Dynamic JS Function.
* Changed APLCore Constructor/Init & Import Upgrading Process.
* Changed APLPresetDbObj Constructor to Optionally Create an Empty Class.
* Changed Back-up Procedures to Use Both a Handler and a Final Function.
* Changed Import File Uploads from Single to Multiple Files.

= 0.3.b5 = 08/17/2012
* Added ‘Any/All’ term setting to taxonomies.
* Added User/Visitor Permissions.
* Added Author filter.
* Added Ignore Sticky Posts filter.
* Added Exclude Posts filter.
* Added Exclude Duplicate Posts from Current Page setting.
* Added an Exit Message.
* Added database upgrade for 0.3.beta5.
* Added additional support to scripts.
* Added empty index.php files to all folders.
* Fixed Post Status grabbing private content.
* Fixed duplicate posts bug.
* Changed ‘Post Status’ filter to carry multiple values.
* Changed Admin Dialogs.
* Changed jQuery register script location.

= 0.3.b4 = 07/17/2012
* Fixed dynamics with post lists with 'Include Terms' within taxonomy and 'Current Page' post parent.
* Fixed excluding current page.
* Fixed always deleting database upon deactivation.
* Changed 'get' functions for post_types, taxonomies, and terms.
* Changed primary author.
* Changed Plugin Page URL on admin page.

= 0.3.b3 = 04/12/2012
* Fixed some 'scrict' errors that were being tossed.
* Fixed the Activation, Deactivation, and Delete/Uninstall action hooks.

= 0.3.b2 = 04/09/2012
* Fixed issue with script interference.
* Fixed installing/restoring default preset settings.

= 0.3.b1 = 04/08/2012
* Added Custom Post Type and Taxonomy support.
* Added JQuery UI features.
* Added "Post Status" setting for presets.
* Added APLQuery class to shadow WP_Query.
* Added a plugin database update for the change preset settings.
* Changed "Post Parent" to carry multiple parent pages instead of just one.
* Changed script and style handling to use wp_enqueue.
* Changed from get_posts() to APLQuery (WP_Query) class.
* Changed APLPresetObj class.
* Changed import file to accommodate for new preset settings.

= 0.3.a1 =  04/05/2012
* Initial v0.3 Release

= 0.2.0 = 12/16/2011
* Added a 'General Settings' section for core settings.
* Added Import/Export feature.
* Added a preset download feature.
* Added admin.css to separate styles from admin.php file.
* Added a new default css style button.
* Fixed database version checking.
* Fixed PHP hardcode string that was displayed to the admin.
* Fixed 'Exclude Current'.
* Fixed Before, Content, and After TextArea to expand correctly.
* Changed 'Upon plugin deactivation clean up all database entries' to a yes/no ratio.

= 0.1.1 = 10/03/2011
* Fixed including required files.

= 0.1.0 = 10/02/2011
* Basic clean up and reorganizing.
* Added phpdocumentation to created files.

= 0.1.b1 = 10/02/2011
* 'Post Parent' combo box was corrected to now display only pages
* 'Orderby' combo box was corrected to pass values that WordPress allows with WP Query
* Edited some of the front end designs to make it current with the plugin
* Post data is now being correctly pulled within APL_run
* A clean up was done on 0.1.a1 

= 0.1.a1 = 10/02/2011
* -Very first working version-
* Fixed 'Require all categories'.
* Upgraded core functions.


//VV// REMOVE, INCLUDE, or MOVE? //VV//
== Upgrade Notice ==

= 0.3.0 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data 
and website.

= 0.3.b9 =
Beta Version. Candidate for Stable Release (0.3.0). If upgrading from 0.2, make sure 
you back up the plugin data and/or website.

= 0.3.b8 =
Beta Version. If upgrading from 0.2, make sure you back up the plugin data and/or 
website. This update includes a couple major and blocker bugs to the Admin UI.
Further development may be required before releasing the stable version (0.3.0).

= 0.3.b7 =
Beta Version. If upgrading from 0.2, make sure you back up the plugin data and/or 
website. This update includes a couple major and blocker bugs to the Admin UI.
Further testing is required before releasing the stable version.

= 0.3.b6 =
(Possible) Final Beta Version. As always, make sure you back up your website since 
0.3 has a couple of database updates. This version fixes some critical issues when 
being used on a large site. Fixes to the Backup feature have also been included in
this.

= 0.3.b5 =
Beta Version. Beta 5 has another database upgrade, so it is recommended you backup 
your data, and use a test site first. No issues with the database have been posted. 
Contains additional filter settings that are built-in the WP_Query params, and added 
a couple custom function. Also fixed a few issues. Look at the changelog for more 
details.

= 0.3.b4 =
Beta Version. It is recommended you backup, but no issues with the database have been posted.
Contains fixes for querying posts, and deactivation.

= 0.3.b3 =
Beta Version. It is recommended you back up data prior to upgrading.Fixed some 'strict' 
errors that were being tossed that could cause an issue.

= 0.3.b2 =
Beta Version. It is recommended you back up data prior to upgrading. A few added preset 
settings. Fixed a problem with script handling that was interfering with built-in 
scripting.

= 0.3.b1 =
Beta Version. Please back up your plugin data prior to upgrading. This version 
introduces custom post type and taxonomy support. Along with a few added settings.

= 0.3.a1 =
Alpha Version. Please back up your plugin data prior to upgrading. This version 
introduces custom post type and taxonomy support. Along with a few added settings.

= 0.2.0 = 
Upgrade adds a new export/import feature to back up your data, and fixes the PHP 
hardcode, exclude current, and TextArea element. See change log for more details.

= 0.1.1 = 
The require() functions in advanced-post-list.php didn't have a dynamic value set.

= 0.1.0 = 
First stable version.
