<p>
	<b>__(Shortcodes:)</b> __(Use these codes inside the list item content (will 
	throw errors if placed in before or after HTML fields).)
</p>
<ul style="margin: auto 16px;">
	<li><b>[ID]</b> - the ID number of the page/post</li>
	<li><b>[post_author label="display_name"]</b> - post author information. Possible types: ID, user_login, user_pass, user_nicename, user_email, user_url, display_name, user_firstname, user_lastname, nickname, description, primary_blog</li>
	<li><b>[post_permalink]</b> - the page permalink</li>
	<li><b>[post_date format="m-d-Y"]</b> - date page/post was created <b>*</b></li>
	<li><b>[post_date_gmt format="m-d-Y"]</b> - date page/post was created in gmt time <b>*</b></li>
	<li><b>[post_title]</b> - page/post title</li>
	<li><b>[post_content]</b> - page/post content</li>
	<li><b>[post_excerpt length="250"]</b> - page/post excerpt (note the optional character 'length' parameter)</li>
	<li><b>[post_name]</b> - page/post slug name</li>
	<li><b>[post_modified format="m-d-Y"]</b> - date page/post was last modified <b>*</b></li>
	<li><b>[post_modified_gmt format="m-d-Y"]</b> - date page/post was last modified in gmt time <b>*</b></li>
	<li><b>[guid]</b> - original URL of the page/post (post_permalink is probably better)</li>
	<li><b>[comment_count]</b> - number of comments posted for this post/page</li>
	<li><b>[item_number offset="1" increment="1"]</b> - the list index for each page/post. Offset parameter sets start position. Increment sets the number you want to increase on each loop.</li>
	<li><b>[final_end]</b> - on the final list item, everything after this shortcode will be excluded. This will allow you to have commas (or anything else) after each item except the last one.</li>
	<li><b>[post_pdf]</b> - URL to the page/post's PDF file. (Requires Kalin's PDF Creation Station plugin. See help menu for more info.)</li>
	<li><b>[post_meta name="custom_field_name"]</b> - page/post custom field value. Correct 'name' parameter required</li>
	<li><b>[post_tags delimeter=", " links="true"]</b> - post tags list. Optional 'delimiter' parameter sets separator text. Use optional 'links' parameter to turn off links to tag pages</li>
	<li><b>[post_categories delimeter=", " links="true"]</b> - post categories list. Parameters work like tag shortcode.</li>
	<li><b>[post_terms taxonomy="category" delimiter=", " links="true" max="0" empty_message=""]</b> - displays a list of post/page terms list. Required/Optional 'taxonomy' parameter sets what taxonomy terms to return; uses taxonomy slug as param value. Optional 'delimiter' parameter sets separator text. Optional 'links' parameter to turn off links to term pages/permalinks. Optional 'max' parameter to set the maximum amount of terms to list; Values Zero or less are infinite values. Optional 'empty_message' parameter to set a custom message to return if none of the taxonomy's terms exist in the post/page.</li>
	<li><b>[post_parent link="true"]</b> - post parent. Use optional 'link' parameter to turn off link</li>
	<li><b>[post_comments before="" after=""]</b> - post comments. Parameters represent text/HTML that will be inserted before and after comment list but will not be displayed if there are no comments. PHP coders: <a href="http://kalinbooks.com/2011/customize-comments-pdf-creation-station">learn how to customize comment display (kalinbooks site).</a></li>
	<li><b>[post_thumb size="full" extract="none"]</b> - URL to the page/post's featured image (requires theme support). Possible size paramaters: "thumbnail", "medium", "large" or "full". Possible xtract prameters: "on" or "force". Setting extract to "on" will cause the shortcode to attempt to pull the first image from within the post if it cannot find a featured image. Using "force" will cause it to ignore the featured image altogether. Extracted images always return at the same size they appear in the post.</li>
	<li><b>[php_function name="function_name" param=""]</b> - call a user-defined custom function. Refer to <a href="http://kalinbooks.com/2011/custom-php-functions/" target="_blank" >this blog post (kalinbooks site)</a> for instructions.</li>
</ul>
<p>
	<b>*</b> Time shortcodes have an optional format parameter. Format your 
	dates using these possible tokens: m=month, M=text month, F=full text 
	month, d=day, D=short text Day Y=4 digit year, y=2 digit year, H=hour, 
	i=minute, s=seconds. More tokens listed here: 
	<a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php.</a> 
</p>
<p>
	Note: these shortcodes only work in the List item content box on this page.
</p>