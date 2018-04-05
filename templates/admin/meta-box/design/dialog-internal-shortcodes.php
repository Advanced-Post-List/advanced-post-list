<?php
/**
 * APL Internal Shortcodes Dialog
 *
 * Dialog content with a list of Internal Shortcodes that can be used.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */
?>
<div style="display: none;" >
	<div id="d-shortcodes" class="apl_internal_shortcodes" title="List of Internal Shortcodes.">
		<p>
			<b>Shortcodes:</b> Use these codes inside the list item content (will 
			throw errors if placed in Before or After.
		</p>
		<dl>
			<dt><b>[ID]</b></dt>
			<dd>The post/page's unique ID number.</dd>
			<dt><b>[post_name]</b></dt>
			<dd>The name/slug of the post/page.</dd>
			<dt><b>[post_slug]</b></dt>
			<dd>The post/page's slug.</dd>
			<dt><b>[post_title]</b></dt>
			<dd>The post/page's title.</dd>
		</dl>
		<dl>
			<dt><b>[post_date]</b></dt>
			<dd>Date the Post/Page was Published.</dd>
			<dd>
				<ul>
					<li><b>format="m-d-Y”</b> - Display date as formatted. <b>*1</b></li>
				</ul>
			</dd>
			<dt><b>[post_date_gmt]</b></dt>
			<dd>GMT Date the Post/Page was Published.</dd>
			<dd>
				<ul>
					<li<b>format="m-d-Y”</b> - Display date as formatted. <b>*1</b></li>
				</ul>
			</dd>
			<dt><b>[post_modified]</b></dt>
			<dd>Date Modified the Post/Page was Published.</dd>
			<dd>
				<ul>
					<li><b>format="m-d-Y”</b> - Display date as formatted. <b>*1</b></li>
				</ul>
			</dd>
			<dt><b>[post_modified_gmt]</b></dt>
			<dd>GMT Date the Post/Page was Modified.</dd>
			<dd>
				<ul>
					<li><b>format="m-d-Y”</b> - Display date as formatted. <b>*1</b></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_author]</b></dt>
			<dd>Author of Post/Page shown as a Display Name by default, or a set Label to retrieve.</dd>
			<dd>
				<ul>
					<li><b>label="display_name"</b> - Which label is used to display Author/User data.
						<ul>
							<li>ID</li>
							<li>user_name (user_login)</li>
							<li>user_nicename</li>
							<li>display_name</li>
							<li>user_email</li>
							<li>user_url</li>
						</ul></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_thumb]</b></dt>
			<dd>Post Thumbnail, aka Featured Image, chosen as the representative image for Post/Page. If none are found, then the next uploaded image on the Post will be used. </dd>
			<dd>
				<ul>
					<li><b>size="thumbnail”</b> - Dimension of image file url. <b>*2</b></li>
					<li><b>extract="none”</b> - HTML img in post_content.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_content]</b></dt>
			<dd>Content associated with a given Post/Page.</dd>
			<dt><b>[post_excerpt]</b></dt>
			<dd>Excerpt as an optional Summary/Description. If none is found, Post Text Content will be used.</dd>
			<dd>
				<ul>
					<li><b>length=”250”</b> – Length of Excerpt.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_parent]</b></dt>
			<dd>Parent page a given Child Page is from, as well as the option to link the Parent. This is intended for Hierarchical Post Types only.</dd>
			<dd>
				<ul>
					<li><b>link="true”</b> - Add as HTML link.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_type]</b></dt>
			<dd>Post Types, including CPTs, the Post/Page is published on.</dd>
			<dd>
				<ul>
					<li><b>label="name”</b> - Post type label that is displayed.</li>
				</ul>
			</dd>
			<dt><b>[post_terms]</b></dt>
			<dd>Custom Taxonomies Terms associated with the Post/Page. WP Built-in Taxonomies can be used as well.</dd>
			<dd>
				<ul>
					<li><b>taxonomy="category”</b> - (Required) Display terms from taxonomy (slug). #6</li>
					<li><b>delimiter=", "</b> - Separator to divide terms.</li>
					<li><b>links="true”</b> - Add as HTML link.</li>
					<li><b>max="0”</b> - Total amount of terms to display.</li>
					<li><b>empty_message="”</b> - Display message if no terms are used.</li>
				</ul>
			</dd>
			<dt><b>[post_tags]</b></dt>
			<dd>WP Built-in Tags used with the Post/Page for Non-Hierarchical Terms. Custom Taxonomies use post_terms shortcode instead.</dd>
			<dd>
				<ul>
					<li><b>delimiter=", "</b> - Separator to divide tags.</li>
					<li><b>links="true”</b> - Add as HTML link.</li>
				</ul>
			</dd>
			<dt><b>[post_categories]</b></dt>
			<dd>WP Built-in Categories used with the Post/Page for Hierarchical Terms. Custom Taxonomies use post_terms shortcode instead.</dd>
			<dd>
				<ul>
					<li><b>delimiter=", "</b> - Separator to divide categories.</li>
					<li><b>links="true”</b> - Add as HTML link.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[post_meta]</b></dt>
			<dd>Post Meta data associated with a given Post/Page. Also know as Custom Fields used for custom content.</dd>
			<dd>
				<ul>
					<li><b>name="”</b> - (Required) Display value from meta name.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[item_number]</b></dt>
			<dd>Index/Number of the Current Post List loop to display in a list.</dd>
			<dd>
				<ul>
					<li><b>offset="1”</b> - Numeric value to start from.</li>
					<li><b>increment="1”</b> - Amount to add by.</li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><b>[final_end]</b></dt>
			<dd>On last item in list, everything after this shortcode will be excluded.</dd>
		</dl>
		<dl>
			<dt><b>[php_function]</b></dt>
			<dd>PHP Function used with custom functionality to create custom shortcodes. <b>*3</b></dd>
			<dd>
				<ul>
					<li><b>name="”</b> - (Required) Function name.</li>
					<li><b>param="”</b> - Param(s) to pass to function.</li>
				</ul>
			</dd>
		</dl>
		<p>
			*1 - String formatting can be found at
			<a href="http://php.net/manual/en/function.date.php#refsect1-function.date-parameters">PHP Date – Format</a>.
		</p>
		<p>
			*2 - WP’s default image size/dimension is “<strong>thumbnail</strong>“, “<strong>medium</strong>”,
			“<strong>large</strong>”, and “<strong>full</strong>”. When using custom size “<strong>xx, yy</strong>”,
			if (custom) size is not found, the closest image dimension will be used.&nbsp;
			<a href="https://support.advancedpostlist.com/doc/internal-shortcodes/post_thumb/">More info.</a>
		</p>
		<p>
			*3 - Refer to <a href="https://github.com/Advanced-Post-List/advanced-post-list/wiki/Tutorial---PHP_Function-Shortcode">this tutorial</a>&nbsp;
			for instructions with custom PHP shortcodes.
		</p>
		<p>
			Note: these shortcodes only work in the List item content box on this page. <a href="https://support.advancedpostlist.com/doc/internal-shortcodes/">See documentation</a>.
		</p>
	</div>
</div>
