=== Advanced Post List ===
Contributors: jokerbr313
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=2E6Z4VQ6NF4CQ&lc=US&item_name=Wordpress%20%2d%20Advanced%20Post%20List&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: Advanced, Post List, Categories, Category, Content, Custom, Excerpt, Filter, Links, List, Links, News, Page, Pages, Post, Posts, Related, Shortcode, Simple, Tag, Tags, Thumbnail, Widget, Widgets
Requires at least: 2.0.2
Tested up to: 3.3
Stable tag: 0.1.1

Create a large variety of post lists with easy to use advanced settings. Highly customizable for designing unique post-list designs.

== Description ==
[forum apl]: http://advanced-post-list.wikiforum.net/
		"Community support forum"
[code google apl]: http://code.google.com/p/wordpress-advanced-post-list/
		"Official bug reports, downloads, and projects."
[wordpress kalins post list]: http://wordpress.org/extend/plugins/kalins-post-list/
		"Kalin's plugin page"

([Community][forum apl]/[Developer's Page][code google apl].

This plugin gives you the ability to customize design, content, and the location of the saved post list.

This is an upgraded version of [Kalins Post List][wordpress kalins post list] that was unfortunately declared abandoned. Most of the credit for creating a great plugin like this goes to **Kalin**. I couldn't see a nice plugin like this get left in the dark. 

This plugin is still in the first stages of its target design. Many of the old bugs have been fixed, excluding the few that require more research to reproduce. A lot of the front end designs have remained the same, while code in the background has been redesigned. Version 1.0.0 will feature many of the functionalities that Kalin and others have mentioned, and will have a completely new layout to accomodate for the extra tools that will be added.

A few features to mention...

* (Complete) Import/export (including importing data from Kalins Post List)
* Custom Taxonomies
* Additional sort methods for 'Orderby' combo box


== Installation ==
1. Upload zip to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings->Advanced Post List to access the settings.

== Frequently Asked Questions ==

= How do I display the post list that I created? =
You need to locate and copy the shortcode. Which is in the Advance Post List - Settings page on the saved preset table. Then create a page/post and paste the shortcode on your page/post (eg. [post_list name='some-preset-name])' 

== Screenshots ==

1. Advanced Post List admin page.
2. Displays an example of what the created preset will look like.
3. Saved Post List table. Now you can download individual presets.
4. Easy to use shortcodes to add to your page/post.
5. Preset in action.
6. Export or import your plugin database data.

== Changelog ==

= 0.2.b1 =
* Designed a 'General Settings' section for core settings.
* Changed 'Upon plugin deactivation clean up all database entries' to a yes/no ratio.
* Added Import/Export feature.
* Added a preset download feature.
* Designed a new default css style button.
* Fixed database version checking.
* Fixed PHP hardcode string that was displayed to the admin.
* Fixed 'Exclude Current'.

= 0.1.1 =
* Fixed including required files.

= 0.1.0 =
* Basic clean up and reorganizing.
* Added phpdocumentation to created files.

= 0.1.b1 =
* 'Post Parent' combo box was corrected to now display only pages
* 'Orderby' combo box was corrected to pass values that wordpress allows with WP Query
* Edited some of the front end designs to make it current with the plugin
* Post data is now being correctly pulled within APL_run
* A clean up was done on 0.1.a1 

= 0.1.a1 =
* -Very first working version-
* Fixed 'Require all categories'.
* Upgraded core functions.

== Upgrade Notice ==

= 0.2.b1 =
Upgrade adds a new export/import feature to back up your data, and fixes the PHP hardcode and exclude current.

= 0.1.1 =
The require() functions in advanced-post-list.php didn't have a dynamic value set.

= 0.1.0 =
First stable version.