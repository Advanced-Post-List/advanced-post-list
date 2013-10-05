=== Advanced Post List ===
Contributors: jokerbr313
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=2E6Z4VQ6NF4CQ&lc=US&item_name=Wordpress%20%2d%20Advanced%20Post%20List&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: Advanced Post List, Custom Post list, Posts, Posts, Related Posts, Shortcode, Page, Pages
Requires at least: 2.0.2
Tested up to: 3.0
Stable tag: 0.1.a1

Create a large variety of post lists with easy to use advanced settings. Highly customizable for designing unique post-list designs.

== Description ==

This plugin gives you the ability to customize how it shows, what it shows, and exactly where it shows up at.

Currently in an Alpha state, so the plugin is working but there are certain bugs that need to be checked before releasing a new version.

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`

== Installation ==
1. Upload zip to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings->Advanced Post List to access the settings.

== Frequently Asked Questions ==

= How do I display the post list that I created? =
You need to locate and copy the shortcode. Which is in the Advance Post List - Settings page on the saved preset table. Then create a page/post and paste the shortcode on your page/post (eg. [post_list name='some-preset-name])' 

== Screenshots ==

1. Advanced Post List admin page.
2. Display an example of the preset post list.
3. Preset post list table.
4. Easy to use shortcodes to add to your page/post.
5. Example of the preset being used.

== Changelog ==

= 0.1.a1 =
Very first working version.
* Fixed 'Require all categories'.
* Upgraded core functions.
== Upgrade Notice ==

= 0.1.a1 =
* When 'Require all categories' was selected, the function would misread the post's category id, and keep it in the post list.
* Code should be easier to upgrade and add features
