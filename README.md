=== Advanced Post List ===
Contributors: EkoJr
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=2E6Z4VQ6NF4CQ&lc=US&item_name=Wordpress%20%2d%20Advanced%20Post%20List&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: Advanced, Post List, Categories, Category, Children, Children Pages, Content, Custom, Custom Post Type, Custom Post Types, Custom Taxonomy, Custom Taxonomies, Draft, Draft Posts, Excerpt, Filter, Future, Future Posts, Links, List, Links, News, Page, Pages, Parent, Parent Pages, Popular Posts, Post, Posts, Private, Private Posts, Related, Related Posts, Recent, Recent Posts, Shortcode, Shortcodes, Simple, Tag, Tags, Thumbnail, Widget, Widgets
Requires at least: 2.0.2
Tested up to: 4.6
Stable tag: 0.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create custom post lists to display various content. Easy to use Filter & Design 
as well as highly configurable and extensive.

== Description ==
[wordpress forum]: https://wordpress.org/support/plugin/advanced-post-list
                "Default WordPress Support"
[wordpress kalins post list]: http://wordpress.org/extend/plugins/kalins-post-list/
		"Kalin's Plugin"
[github issues]: https://github.com/EkoJr/wp-advanced-post-list/issues
		"Ticket Handling"
[github wiki]: https://github.com/EkoJr/wp-advanced-post-list/wiki
		"Contribute or Learn about APL"

Highly customizable plugin for designing a large variety of post lists. Allowing 
the Webmaster to create any design for displaying Recent Posts, Related Posts, 
Future Posts, etc., and easily positioning it with a shortcode inside a Page or Post. 
All that is required is you know HTML, but the plugin can also use CSS, 
JavaScript, and PHP.

Version 0.3 post query was switch to WP_Query to take advantage of the Custom Post 
Types and Taxonomies featured within WordPress, and also has additional filter settings 
added to further reach alternate methods of displaying posts.

APL's Documentation/Wiki is located on [GitHub Wiki][github wiki].

Discovered a bug or an enhancement? Please submit thread/ticket at 
[WordPress][wordpress forum] or [GitHub Issue Tickets][github issues].

When designing site with better navigation. This plugin accomplishes 3 main 
tasks when displaying the site’s content through various lists.

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