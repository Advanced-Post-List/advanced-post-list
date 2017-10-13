=== Advanced Post List ===
Contributors: EkoJr
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VFUYQGQ7VXEDC
Tags: Advanced, Post List, Categories, Category, Children, Children Pages, Content, Custom, Custom Post Type, Custom Post Types, Custom Taxonomy, Custom Taxonomies, Draft, Draft Posts, Excerpt, Filter, Future, Future Posts, Links, List, Links, News, Page, Pages, Parent, Parent Pages, Popular Posts, Post, Posts, Private, Private Posts, Related, Related Posts, Recent, Recent Posts, Shortcode, Shortcodes, Simple, Tag, Tags, Thumbnail, Widget, Widgets
Requires at least: 4.5
Tested up to: 4.8
Stable tag: 0.3.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create Post Lists that are highly customizable in both design and query filter. Which can then be placed multiple times without showing duplicates.

== Description ==
[wp kpl]:     https://wordpress.org/plugins/kalins-post-list/
                  "WordPress.org - Kalin's Post List"
[wp apl support]: https://wordpress.org/support/plugin/advanced-post-list
                  "WordPress.org - Support for APL"
[github issues]:  https://github.com/Advanced-Post-List/advanced-post-list/issues
                  "Report an Issue"
[github wiki]:    https://github.com/Advanced-Post-List/advanced-post-list/wiki
                  "Learn or Contribute to APL"
[wiki shortcodes]: https://github.com/Advanced-Post-List/advanced-post-list/wiki/Internal-Shortcode-Page
                  "Documentaion for Internal Shortcodes"

Advanced Post Lists (APL) sets itself apart from being “Just Another Post List Plugin” by giving admins the most amount of control when displaying Recent Posts, Related Posts, Future Posts, or a list of posts in general. However, there is a learning curve.

You must know:

* HTML
* CSS
* _(Optional)_
  * JavaScript
  * PHP

In many ways, APL is designed to act much like The Loop which is most notable in Themes, but APL takes that concept and turns it into an Admin tool that can easily be changed, moved, or added/removed. This eliminates much of the backend work, and prevents being limited to what is hardcoded into a theme.

APL accomplishes 3 main types of tasks. Filter, Design, and Placement.

# Filter

* **Custom Post Type and Taxonomy Support** - Displays Posts/Pages from custom post types, taxonomies, and terms. This includes other plugins, but may not be compatibly supported.
* **Enhanced/Diverse Queries** - Capable of diverse filter configuration when displaying posts. This feature allows...
  * Different Post Types -> Taxonomies query configurations.
  * Multiple Page Parents from multiple Post Types.
* **Dynamic Filters (Terms & Page Parents)** - Grabs values to filter by that is based on the current page being viewed. _Ex. Displaying Related Posts in a Header, Footer, Sidebar, etc.._
* **Show Content from Published, Private, Future, etc.** - Display content on the frontend so users don’t have to navigate to the admin side. Allowing private landing pages to be created. _Note: Further development may be required._

## Design

* **Internal Shortcodes** - Primarily used to add various data from a given Post/Page, but the capabilities is rather extensive, and being able to extend to custom PHP functions the options are practically limitless. [See documentation for a full list & details][wiki shortcodes].
* **Custom Formats to Loop ( The Loop )** - Themes follow this concept, but APL turns that concept into a tool. The “List Content” loop, as well as the before & after, is where most of the work is done with preset HTML, CSS, JS, & PHP designs. It is also optional to set an Empty Message (No Posts Found) to display.

### Placement

* **Post List Shortcode** - User friendly method of adding Preset Post Lists to a section of a page.
* **Sidebar Widget** - Easier to use. However, adding post list shortcodes to the text sidebar also produces the same results.
* **PHP Hardcode** - For more extensive design work. There is a public function for displaying Preset Post Lists where WordPress support and functions for admins aren’t fully present.

This is an evolved version of [Kalins Post List][wp kpl]. Most of the credit for the idea behind APL goes to that plugin. As you may have noticed, APL is still in the development stages of its target version (1.0.0).

APL’s Documentation is located on [GitHub Wiki][github wiki] (WIP). Which is also open for others to contribute.

Questions/Bug Report submit thread / ticket at [WordPress][wp apl support] or [GitHub Issue / Tickets][github issues].

== Installation ==
1. Upload zip to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings->Advanced Post List to access the settings.
 

== Frequently Asked Questions ==

= Where is the settings page? =
Inside your admin dashboard under Adv. Post List.

= How do I display the post list that I created? =
You need to copy & paste the shortcode to the desired location (e.g. [post_list name='some-preset-name']).


== Screenshots ==
1. A few samples of the plugin.
2. Insert saved presets via shortcodes to display it on a page/post.
3. A basic view of the Admin UI.
4. General options and support.

== Changelog ==

= 0.4.0 =
* Changed Preset object to use APL_Post_List & APL_Design post data database structure; adds multiple built-in improvements & support.
* Added APL_Post_List Class for post data database structure.
* Added APL_Design Class for post data database structure.
* Changed Preset variables before, content, after, & empty message to APL_Design object.
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
* Changed General Settings to Settings API w/ Meta Boxes.
* Fixed possible defect with Plugin Options not being initiated.
* Changed/Fixed Exporting and Importing.
* Added jQuery (UI) compatability.
* Removed jQuery UI Theme setting from APL Options.
* Changed Updater Class param items to update to an array.
* Changed version check to a hook method.
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

= 0.4.0 =
Beta Release. Large database update will occurr.

= 0.3.6 =
Stable Release. Changed File Structure which may cause a PHP object error
( Please report PHP errors ). If upgrading from 0.2, make sure to back
up the plugin data and website.

= 0.3.5 =
Stable Release. Changed File Structure which may cause a PHP object error
( Please report PHP errors ). If upgrading from 0.2, make sure to back
up the plugin data and website.

= 0.3.4 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.3 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.2 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.1 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.0 =
Stable Release. If upgrading from 0.2, make sure to back up the plugin data
and website.

= 0.3.b9 =
Beta Version. Candidate for Stable Release (0.3.0). If upgrading from 0.2, make
sure you back up the plugin data and/or website.

= 0.3.b8 =
Beta Version. If upgrading from 0.2, make sure you back up the plugin data
and/or website. This update includes a couple major and blocker bugs to the
Admin UI. Further development may be required before releasing the stable
version (0.3.0).

= 0.3.b7 =
Beta Version. If upgrading from 0.2, make sure you back up the plugin data
and/or website. This update includes a couple major and blocker bugs to the
Admin UI. Further testing is required before releasing the stable version.

= 0.3.b6 =
Beta Version. As always, make sure you back up your website since
0.3 has a couple of database updates. This version fixes some critical issues
when being used on a large site. Fixes to the Backup feature have also been
included in this.

= 0.3.b5 =
Beta Version. Beta 5 has another database upgrade, so it is recommended you
backup your data, and use a test site first. No issues with the database have
been posted. Contains additional filter settings that are built-in the WP_Query
params, and added a couple custom function. Also fixed a few issues. Look at
the changelog for more details.

= 0.3.b4 =
Beta Version. It is recommended you backup, but no issues with the database
have been posted. Contains fixes for querying posts, and deactivation.

= 0.3.b3 =
Beta Version. It is recommended you back up data prior to upgrading.Fixed some
'strict' errors that were being tossed that could cause an issue.

= 0.3.b2 =
Beta Version. It is recommended you back up data prior to upgrading. A few
added preset settings. Fixed a problem with script handling that was
interfering with built-in scripting.

= 0.3.b1 =
Beta Version. Please back up your plugin data prior to upgrading. This version
introduces custom post type and taxonomy support. Along with a few added
settings.

= 0.3.a1 =
Alpha Version. Please back up your plugin data prior to upgrading. This version
introduces custom post type and taxonomy support. Along with a few added
settings.

= 0.2.0 =
Upgrade adds a new export/import feature to back up your data, and fixes the
PHP hardcode, exclude current, and TextArea element. See change log for more
details.

= 0.1.1 =
The require() functions in advanced-post-list.php didn't have a dynamic value
set.

= 0.1.0 =
First stable version.
