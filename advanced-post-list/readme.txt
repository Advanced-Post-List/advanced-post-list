=== Advanced Post List ===
Contributors: EkoJr
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=2E6Z4VQ6NF4CQ&lc=US&item_name=Wordpress%20%2d%20Advanced%20Post%20List&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: Advanced, Post List, Categories, Category, Children, Children Pages, Content, Custom, Custom Post Type, Custom Post Types, Custom Taxonomy, Custom Taxonomies, Draft, Draft Posts, Excerpt, Filter, Future, Future Posts, Links, List, Links, News, Page, Pages, Parent, Parent Pages, Popular Posts, Post, Posts, Private, Private Posts, Related, Related Posts, Recent, Recent Posts, Shortcode, Shortcodes, Simple, Tag, Tags, Thumbnail, Widget, Widgets
Requires at least: 2.0.2
Tested up to: 3.7
Stable tag: 0.3.b7

Create custom post lists with easy to use advanced settings. Highly customizable 
for designing unique post-list designs.

== Description ==
[wordpress kalins post list]: http://wordpress.org/extend/plugins/kalins-post-list/
		"Kalin's plugin"
[github issues]: https://github.com/EkoJr/wp-advanced-post-list/issues
		"Report any bugs here"

Highly customizable plugin for designing a large variety of post lists. Allowing 
the Webmaster to create any design for displaying Recent Posts, Related Posts, 
Future Posts, etc., and easily placing it with a shortcode inside a Page or Post. 
All that is required is that you know HTML, but the plugin can also use CSS, 
JavaScript, and PHP.

Version 0.3 post query was switch to WP_Query to take advantage of the Custom Post 
Types and Taxonomies featured within WordPress, and also has additional filter settings 
added to further reach alternate methods of displaying posts.

When designing site with better navigation. This plugin accomplishes 3 main 
tasks when displaying the site’s content through various lists.

Discovered a bug or a feature? Please submit issues at [GitHub's version][github issues] 
for ticket handling and contributors.

**Content of the post list**

* **Custom Post Type and Taxonomy Support** – New addition adds the ability to 
add even more posts and/or pages to your lists. Display any page from any 
post type and has even more filter options with any additional taxonomies that 
have been added.
* **Add/Require Any Number of Terms** – Create diverse post lists through any 
configuration of terms within different taxonomies, and show any posts/pages 
that has one related term, but if needed, post lists can be required to have 
all terms selected.
* **Show Page Children** – Once only able to display one page’s children pages 
from one hierarchical post type (WP built-in Pages). This plugin can now display 
multitude of children pages from multiple pages from multiple hierarchical 
post types. Making it easy to display sub-pages
* **Dynamically Add Terms and Page Parent** – Sometimes pages are expected to 
change, and some area’s like the header, footer, and any sidebars are expected 
to change. So it’s just plain simple and nice to have one configuration that 
changes according to the visitor’s/user’s current page/post.
* **Show Content from Private, Future, Published, and More** – A new addition 
added to show posts/page from not only publicly published posts/pages, but from 
any status. Opening up the ability for creating private sections on a website 
for users.

**Style of the post list**

* **Customizable Loop** – Any plugin of this design has to have a loop of some 
kind to list the posts and/or pages. Most have their own style of design, but 
this plugin gives the webmaster the tools to create his own style.
* **Shortcodes for Post/Page Content** – Part of the heart of the Customizable 
Loop, shortcodes have made it possible to pull content from each post/page and 
add it to the post list.

**Location of the post list**

* **Post List Shortcode** – User friendly method of adding any post list to a 
section of a site.
* **PHP Hardcode** – Add post lists where some situations require a more 
technical use where WordPress features and functions aren’t fully present.
* **Sidebar Widget (Coming soon)** – Originally was removed until 0.3 was 
developed. Shortcodes have made it easy to add post lists to a text sidebar, 
but there’s still plans to take full advantage of implementing the widget class.


This is an alternate version of [Kalins Post List][wordpress kalins post list] 
which was unfortunately declared abandoned. Most of the credit for creating an 
extraordinary plugin like this goes to Kalin. Currently, the plugin is still in 
the first stages of its target design. Version 1.0.0 will feature many of the 
functionalities that Kalin and others have mentioned, and will have a completely 
new layout to accommodate for the extra tools that will be added.


**Pre-Release Projects for Version 1.0.0**

* **(Completed)** Import/export - Export is broken until 0.3 stable (including importing data from Kalins Post List)
* **(Completed)** Custom Post Type & Taxonomies Support. Available in the 0.3 release.
* Additional sort methods for 'Orderby' combo box.
* Additional shortcodes.


== Installation ==
1. Upload zip to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings->Advanced Post List to access the settings.
 

== Frequently Asked Questions ==

= Where is the settings page? =
Inside your admin dashboard under Settings->Admin Post List.

= How do I display the post list that I created? =
You need to locate and copy the shortcode which is in the Advance Post List - Settings 
page on the saved preset table. Then create a page/post and paste the shortcode on your 
page/post (e.g. [post_list name='some-preset-name'])


== Screenshots ==
1. A few samples of the plugin.
2. Insert saved presets via shortcodes to display it on a page/post.
3. A basic view of the Admin UI
4. General options and support.

== Changelog ==

= 0.3.b7 =
* Fixed Admin UI Multiselect jQuery plugin not closing.
* Fixed Multiselect not updating values.
* Changed Links in the Settings page.
* Changed Screenshots.

= 0.3.b6 =
* Fixed 'Maximum execution time of 30 seconds exceeded'.
* Fixed Issue with jQuery UI 'Current Page' Parent Selector.
* Added Label Elements to Admin Option Settings.
* Changed jQuery UI Dialog Messages to use a Dynamic JS Function. 
* Added Info Dialogs to the General Options Section.
* Added Updater Class Object.
* Changed APLCore Constructor/Init & Import Upgrading Process.
* Added Backwards Compatibility to APLPresetDbObj & APLPresetObj Class.
* Changed APLPresetDbObj Constructor to Optionally Create an Empty Class.
* Fixed/Changed Import/Export Layout & Elements. 
* Fixed Backup (Export/Download/Import) Feature.
* Changed Back-up Procedures to Use Both a Handler and a Final Function. 
* Added More Security to the Backup Feature.
* Fixed JSON Converter to Display PHP Errors Instead of Breaking.
* Added Version Variable to Export Output File.
* Fixed Import to Update Preset(s) being imported.
* Changed Import File Uploads from Single to Multiple Files.
* Added an Overwrite Confirmation Before Finalizing the Import Operation.

= 0.3.b5 =
* Added ‘Any/All’ term setting to taxonomies.
* Changed ‘Post Status’ filter to carry multiple values.
* Added User/Visitor Permissions.
* Added Author filter.
* Added Ignore Sticky Posts filter.
* Added Exclude Posts filter.
* Added Exclude Duplicate Posts from Current Page setting.
* Added an Exit Message.
* Added database upgrade for 0.3.beta5.
* Changed Admin Dialogs.
* Fixed Post Status grabbing private content.
* Fixed duplicate posts bug.
* Changed jQuery register script location.
* Added additional support to scripts.
* Added empty index.php files to all folders.

= 0.3.b4 =
* Fixed dynamics with post lists with 'Include Terms' within taxonomy and 'Current Page' post parent.
* Fixed excluding current page.
* Changed 'get' functions for post_types, taxonomies, and terms.
* Fixed always deleting database upon deactivation.
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
* Changed "Post Parent" to carry multiple parent pages instead of just one.
* Changed script and style handling to use wp_enqueue.
* Changed from get_posts() to APLQuery (WP_Query) class.
* Changed APLPresetObj class.
* Added APLQuery class to shadow WP_Query.
* Added a plugin database update for the change preset settings.
* Changed import file to accommodate for new preset settings.


= 0.2.0 =
* Designed a 'General Settings' section for core settings.
* Changed 'Upon plugin deactivation clean up all database entries' to a yes/no ratio.
* Added Import/Export feature.
* Added a preset download feature.
* Added admin.css to separate styles from admin.php file.
* Designed a new default css style button.
* Fixed database version checking.
* Fixed PHP hardcode string that was displayed to the admin.
* Fixed 'Exclude Current'.
* Fixed Before, Content, and After TextArea to expand correctly

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

= 0.3.b7 =
Beta Version. If upgrading from 0.2, make sure you back up the plugin data and/or 
website. This update includes a couple major and blocker bugs to the Admin UI.
Further testing is required before releasing the stable version.

= 0.3.b6 =
Beta Version. As always, make sure you back up your website since 
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
