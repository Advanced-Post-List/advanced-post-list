=== Advanced Post List ===
Contributors: EkoJr
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VFUYQGQ7VXEDC
Tags: Post List, Tool, Editor, Featured, Related Posts, Author, Authors, Auto, Automate, Builder, Categories, Category, Child Pages, CMS, Content, Comment, Comments, CPT, CSS, Custom, Custom Post Type, Custom Post Types, Custom Taxonomy, Custom Taxonomies, Design, Developer, Draft, Draft Posts, Excerpt, Feature, Featured, Featured Content, Filter, Future, Future Posts, HTML, Image, Images, Links, List, Links, Magazine, Magazines, News, Page, Pages, Parent, Parent Pages, Photos, PHP, Popular Posts, Post, Posts, Private, Private Posts, Programming, Published, Related Post, Related Posts, Recent, Recent Post, Recent Posts, Shortcode, Shortcodes, Simple, Tag, Tags, Thumbnail, Web Design, Web Development, Webmaster, Widget, Widgets, WPML
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 0.5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Post List builder with highly customizable filter & custom designs. Whether it's displaying Recent Posts, Related Posts, Dynamic Posts, or a list of posts in general.

== Description ==
[wp kpl]:               https://wordpress.org/plugins/kalins-post-list/
                        "WordPress.org - Kalin's Post List"
[apl getting started]:  https://support.advancedpostlist.com/getting-started/
                        "Getting Started w/ APL"
[apl qa]:               https://support.advancedpostlist.com/qa/
                        "Questions & Answers"
[apl docs]:             https://support.advancedpostlist.com/documentation/
                        "APL Documentation"
[apl shortcodes]:       https://support.advancedpostlist.com/doc/internal-shortcodes/
                        "List of Internal Shortcodes"
[wp apl support]:       https://wordpress.org/support/plugin/advanced-post-list
                        "WordPress.org - Support for APL"
[github issues]:        https://github.com/Advanced-Post-List/advanced-post-list/issues
                        "Report an Issue"
[github wiki]:          https://github.com/Advanced-Post-List/advanced-post-list/wiki
                        "Learn or Contribute to APL"

This tool functions much like a Post List builder to give Admins the most amount of control over managing content to display. Developed with Web Designers & Developers in mind, makes this a highly customizable tool to have around. However, there is a sharp learning curve for most.

You must know:

* HTML
* CSS
* _(Optional)_
  * JavaScript
  * PHP

## Summary

Advanced Post List (aka APL) is designed to operate much like The Loop in WordPress; which is most notable in Themes. However, that concept is taken and turned into an Admin tool that can easily be changed, moved, or added/removed. This eliminates much of the backend work, and prevents being limited to what is hardcoded into a theme.

This also makes multiple Featured Content with different configurations more of a breeze, and the complexity of it is a challenge that APL achieves unlike any other.

APL can accomplish a large degree of variations, but can be broken down to 3 main types of tasks. With Filter, Design, and Placement, here are some of the key features.

### Filter

* **Custom Post Type and Taxonomy Support (CPT)** - Displays Posts/Pages from custom post types, taxonomies, and terms. This includes other plugins with post data. but may store its data differently than post_content. (**Advanced Users, see php_function shortcode.**)
* **Enhanced/Diverse Queries** - Capable of diverse filter configuration when displaying posts. This feature allows...
  * Cross Filtering with Custom Post Types.
  * Diverse Post Types -> Taxonomies query configurations.
  * Multiple Query configurations with include and require.
* **Optimized with Complex Queries** - Once deemed an Achilles Heel to WP Query, steps are taken to reduce the server load as much as possible.
* **Dynamic Filters** - Grabs values to filter by based on the current posts/pages being viewed. _Ex. Displaying Related Posts in a Header, Footer, Sidebar, etc.._
* **Show Content from Published, Private, Future, etc.** - This is a *development feature* and may not provide intended results. Display content on the frontend so users don’t have to navigate to the admin side. Allowing private landing pages to be created. _Note: Requires advanced knowledge on how WP Query and User Perms operate._


### Design

* **Layout and Style 99.9% Customizable** - There's nearly no limitations to the design, with some exceptions that may surface with (Child) Theme's CSS. This does require some knowledge in Web Design. *Note: This is NOT to be confused with Drag and Drop UI/UX.*
* **Internal Shortcodes** - Adds various data from Post object, and is one of the extensive features. Being able to extend to shortcodes with custom PHP functions make the possibilities practically limitless. [See full list & details][apl shortcodes].
* **Encapsulated for Zero Conflicts** - With a large number of shortcodes on any given site. Isolating Internal Shortcodes to its own instance eliminates any plugin conflicts.
* **Custom Formats to Loop ( The Loop )** - Themes follow this concept, but APL turns that concept into a tool. The “List Content” loop, as well as the before & after, is where most of the work is done with preset HTML, CSS, JS, & PHP designs. It is also optional to set an Empty Message (No Posts Found) to display.

### Placement

* **Post List Shortcode** - User friendly method of adding Preset Post Lists to a section of a page.
* **Sidebar Widget** - Easier to use. However, adding post list shortcodes to the text sidebar also produces the same results.
* **PHP Hardcode** - For more extensive design work. There is a public function for displaying Preset Post Lists where WordPress support and functions for admins aren’t fully present.

## Other Plugins Tested/Supported

There's various plugins that offer a unique capability, and some of which APL can use as intended, but there are also some don't. Here is a list of popular plugins that have been tested and verified by Advanced Post List.

* **Advanced Custom Fields** - Requires php_function shortcode.
* **WPML** - With WordPress Multilingual installed, additional Designs can be created for rendering a Post List in different languages. Posts/Pages are handled automatically and will display content in various languages.

APL is open to development requests and welcomes those willing to report on any issues.

## Help & Support

Currently, information can be found in 2 different locations. This is due to recent changes, and is only temporary.

* [Getting Started][apl getting started]
* [Documentation][apl docs]
	* [Internal Shortcodes][apl shortcodes]
* [GitHub Wiki][github wiki]

Questions/Bug Report submit thread / ticket at [WordPress][wp apl support] or [GitHub Issue / Tickets][github issues].


== Frequently Asked Questions ==

= For FAQ =
Go to [Q&A](https://support.advancedpostlist.com/qa/).


== Screenshots ==
1. Using a simple shortcode, you can display amazing content.
2. A Few more examples.
3. Complex lists coupled together.
4. Admin Screen for All Post Lists.
5. Admin UI for Creating/Editing Post lists.
6. Admin Screen for Settings.

== Changelog ==

= 0.5.1 =
* Fix tinyMCE issue with visual editor.

= 0.5.0 =
* Fix double offset.
* Fix Check-All on All Post Lists screen.
* Change submenu name to be indented.
* Add additional extended edit support to APL Design post type.
* Add Template Operations/Functions.
* Add APL_Core->display_preset_list_content() for better encapsulation.
* Fix possible issues with tax_query not being set and optimized correctly.
* Change "any" terms operations to get & add all terms in APL_Query->query_wp. Prevents a loose bug.
* Add filter support to Preset > Before content.
* Change/Refactor Internal Shortcodes for better extendability.
* Change APL_Core to a global variable.
* Change display functions to be wrapper functions.
* Change Notices display time to display at exact time.
* Add WP community standard DISABLE_NAG_NOTICES.
* Re-add install to add default presets when APL's Preset Db is empty.
* Re-add Restore Defaults in Settings page.
* Change location of APL_Core file.
* Fix JS apl_tinyMCE is not defined when Gutenberg is active.

= 0.4.4 =
* Added CodeMirror to Display metabox in Post List editor.
* Added stricter handling with APL_Design data to fix post lists not rendering.
* Fixed WPML Compatability with translated posts.
* Fixed APL Notice displaying when no JS is present to delay/dismiss.
* Fixed APL Updater missing custom post types and taxonomies that exist.
* Changed Internal Shortcodes Dialog.
* Changed Settings Info metabox.

= 0.4.3.1 =
* Fixed Delay/Dismiss Operation.

= 0.4.3 =
* Fixed Review Notice not delaying or dismissing.
* Fixed Legacy Hardcode function not working.

= 0.4.2.1 =
* Fixed PHP Error on Admin Screen.

= 0.4.2 =
* Fixed Post Lists not Displaying with 'parse_query' being used by some plugins.
* Added TinyMCE Button to Post Editor to insert post list shortcodes.
* Added APL_Notices class for admin notifications.

= 0.4.1 =
* Fixed PHP Error with APL_Query.
* Added stricter updating.
* Fixed type case with Metabox.

= 0.4.0 =
* Added APL_Post_List Class for post data database structure with presets.
* Added APL_Design Class for post data database structure with preset designs; before, content, after, & empty message.
* Changed Preset data to use post data database structure (APL_Post_List & APL_Design).
* Added Admin Singleton Class.
* Changed to Custom Admin Menu.
* Added New Admin templates and multiple UI changes.
* Changed Info/Help to Tooltips instead of Dialogs.
* Added Post_Type filter by 'Any'.
* Changed 'Require Taxonomy' to 'Require Taxonomies' as a Post Type based filter instead.
* Added Sort by 'None' and 'Slug'.
* Added Post Status filter by 'None' and 'Any'.
* Added Perms filter by 'None'.
* Added Offset filter.
* Added additional Notice to front-end if attribute 'name' in [post_list] is invalid or missing.
* Added Sanitazion to shortcode attribute 'name'; ex. [post_list name=""].
* Fixed Link attribute in post_category & post_tags shortcode.
* Added additional checks with php_function shortcode.
* Changed General Settings to Settings API w/ Meta Boxes.
* Added Ignore Post Types on Admin Post List screen.
* Changed/Fixed Exporting and Importing.
* Fixed possible defect with Plugin Options not being initiated.
* Added jQuery (UI) compatability.
* Removed jQuery UI Theme setting from APL Options.
* Changed Updater Class param items to update to an array.
* Changed version check to a hook method.
* Added WPML Support.
* Added Internalization for translations.
* Added non-class Functions file for common functions.
* Added hooks.php file to store custom hook examples.

= 0.3.7 =
* Fixed warning with load_plugin_textdomain.

= 0.3.6 = 
* Added load_plugin_textdomain and .pot files.

= 0.3.5 =
* Fixed [post_excerpt] breaking last word.
* Fixed [post_excerpt length=""] not trimming if post excerpt is found.
* Fixed [php_function] Undefined Constant with KALIN_ALLOW_PHP when APL_ALLOW_PHP is defined.
* Added [post_title length=""] attribute.

= 0.3.4 =
* Fixed [final_end] not updating output string.

= 0.3.3 =
* Fixed [final_end] missing a param.
* Fixed Undefined index: post__not_in.
* Added initial Localization to prep support for multiple languages.

= 0.3.2 =
* Changed Internal Shortcodes to a Class Object as a Shortcode API.
* Changed support to Internal Shortcode to allow Attributes to be added in any order.
* Added attributes aliases user_name & user_description for the Labels attribute in [post_author].
* Removed attribute labels that no longer exist in WP for the Labels attribute in [post_author].
* Added custom max size support for the Size attribute in [post_thumb].
* Fixed extract to correctly grab img tags for the Extract attribute in [post_thumb].
* Fixed encoding when creating an excerpt from post/page content in [post_excerpt].
* Added [post_type] shortcode, and label attribute.
* Added error check in [item_number].
* Added check if other plugin is active in [post_pdf].
* Added APL_ALLOW_PHP constant as a required varible for [php_function].
* Removed APLCallback Class Object file.
* Changed Dialog location for Internal Shortcode to a seperate file.
* Added more encapsulation to initializing other class files.

= 0.3.1 =
* Added Assets.
* Added Icon to Assets.
* Changed Screenshots directory to Assets folder.
* Updated jQuery MultiSelect UI Widget files version 1.14 to 1.16.
* Fixed [post_terms] grabbing a wrong param.

= 0.3.0 =
* Fixed error 'Un-Defined Variables and Non-Objects' with Excluding Duplicates.
* Fixed List Amount '-1' Returning Nothing.
* Fixed Any/All Selection not properly querying Terms within the Taxonomy.
* Changed Admin Header Links in APL Admin Settings Page.
* Updated GPLv2 Information.

= 0.3.b9 =
* Added '[post_terms]' Internal Shortcode function to add Custom Taxonomy Terms.
* Fixed MultiSelect Button Width using default width.
* Updated Google APIs jQuery UI CSS version 1.8.21 to 1.11.4 to match WP 4.5.3 jQuery UI JS version.
* Updated jQuery MultiSelect UI Widget files version 1.12 to 1.14.

= 0.3.b8 =
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

= 0.3.b7 =
* Fixed Admin UI Multiselect jQuery plugin not closing.
* Fixed Multiselect not updating values.
* Changed Admin Header Links in the Settings page.
* Changed Screenshots.

= 0.3.b6 =
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

= 0.3.b5 =
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

= 0.3.b4 =
* Fixed dynamics with post lists with 'Include Terms' within taxonomy and 'Current Page' post parent.
* Fixed excluding current page.
* Fixed always deleting database upon deactivation.
* Changed 'get' functions for post_types, taxonomies, and terms.
* Changed primary author.
* Changed Plugin Page URL on admin page.

= 0.3.b3 =
* Fixed some 'scrict' errors that were being tossed.
* Fixed the Activation, Deactivation, and Delete/Uninstall action hooks.

= 0.3.b2 =
* Fixed issue with script interference.
* Fixed installing/restoring default preset settings.

= 0.3.b1 =
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

= 0.3.a1 =
* Initial v0.3 Release

= 0.2.0 =
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

= 0.1.1 =
* Fixed including required files.

= 0.1.0 =
* Basic clean up and reorganizing.
* Added phpdocumentation to created files.

= 0.1.b1 =
* 'Post Parent' combo box was corrected to now display only pages
* 'Orderby' combo box was corrected to pass values that WordPress allows with WP Query
* Edited some of the front end designs to make it current with the plugin
* Post data is now being correctly pulled within APL_run
* A clean up was done on 0.1.a1 

= 0.1.a1 =
* -Very first working version-
* Fixed 'Require all categories'.
* Upgraded core functions.

== Upgrade Notice ==

= 0.5.1 =
* Hotfix for Visual Editor bug in 0.5.0; Bug fixes and added extended support.

= 0.5.0 =
* Bug fixes and added extended support. No database update, but a backup is still recommended.

= 0.4.4 =
* Possible fix with displaying post list. IMPORTANT: Minor database update. PLEASE BACK UP DATA.

= 0.4.2.1 =
* Fixed Delay/Dismiss Operation.

= 0.4.3 =
* Fixed Review Notice not delaying or dismissing.

= 0.4.2.1 =
* Possible fix for post lists not displaying.
* Fixed PHP Error in admin screens.

= 0.4.2 =
* Possible fix for post lists not displaying.

= 0.4.0 =
* Please be sure to Back Up the website. Large database update will occur.
* Completely new Admin UI.
* WPML Support and Plugin Internalization.

= 0.3.6 =
* Stable Release. Changed File Structure which may cause a PHP object error ( Please report PHP errors ). 
* If upgrading from 0.2, make sure to back up the plugin data and website.

= 0.3.5 =
* Stable Release. Changed File Structure which may cause a PHP object error ( Please report PHP errors ).
* If upgrading from 0.2, make sure to back up the plugin data and website.

= 0.3.4 =
* Stable Release. If upgrading from 0.2, make sure to back up the plugin data and website.

= 0.3.3 =
* Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.2 =
* Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.1 =
* Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.0 =
* Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.b9 =
* Beta Version. Candidate for Stable Release (0.3.0). 
* If upgrading from 0.2, make sure you back up the plugin data and/or website.

= 0.3.b8 =
* Beta Version. If upgrading from 0.2, make sure you back up the plugin data
and/or website. This update includes a couple major and blocker bugs to the
Admin UI. Further development may be required before releasing the stable
version (0.3.0).

= 0.3.b7 =
* Beta Version. If upgrading from 0.2, make sure you back up the plugin data
and/or website. This update includes a couple major and blocker bugs to the
Admin UI. Further testing is required before releasing the stable version.

= 0.3.b6 =
* Beta Version. As always, make sure you back up your website since
0.3 has a couple of database updates. This version fixes some critical issues
when being used on a large site. Fixes to the Backup feature have also been
included in this.

= 0.3.b5 =
* Beta Version. Beta 5 has another database upgrade, so it is recommended you
backup your data, and use a test site first. No issues with the database have
been posted. Contains additional filter settings that are built-in the WP_Query
params, and added a couple custom function. Also fixed a few issues. Look at
the changelog for more details.

= 0.3.b4 =
* Beta Version. It is recommended you backup, but no issues with the database
have been posted. Contains fixes for querying posts, and deactivation.

= 0.3.b3 =
* Beta Version. It is recommended you back up data prior to upgrading.Fixed some
'strict' errors that were being tossed that could cause an issue.

= 0.3.b2 =
* Beta Version. It is recommended you back up data prior to upgrading. A few
added preset settings. Fixed a problem with script handling that was
interfering with built-in scripting.

= 0.3.b1 =
* Beta Version. Please back up your plugin data prior to upgrading. This version
introduces custom post type and taxonomy support. Along with a few added
settings.

= 0.3.a1 =
* Alpha Version. Please back up your plugin data prior to upgrading. This version
introduces custom post type and taxonomy support. Along with a few added
settings.

= 0.2.0 =
* Upgrade adds a new export/import feature to back up your data, and fixes the
PHP hardcode, exclude current, and TextArea element. See change log for more
details.

= 0.1.1 =
* The require() functions in advanced-post-list.php didn't have a dynamic value set.

= 0.1.0 =
* First stable version.
