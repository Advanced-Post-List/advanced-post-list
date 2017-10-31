Post List builder with highly customizable filter & custom designs. Whether it's displaying Recent Posts, Related Posts, Dynamic Posts, or a list of posts in general.

== Description ==
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

* [Getting Started](https://support.advancedpostlist.com/getting-started/)
* [Documentation](https://support.advancedpostlist.com/documentation/)
	* [Internal Shortcodes](https://support.advancedpostlist.com/doc/internal-shortcodes/)
* [GitHub Wiki](https://github.com/Advanced-Post-List/advanced-post-list/wiki)

Questions/Bug Report submit thread / ticket at [WordPress](https://wordpress.org/support/plugin/advanced-post-list) or [GitHub Issue / Tickets](https://github.com/Advanced-Post-List/advanced-post-list/issues).

----------------------

* [Getting Started][apl getting started]
* [Documentation][apl docs]
	* [Internal Shortcodes][apl shortcodes]
* [GitHub Wiki][github wiki]

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
