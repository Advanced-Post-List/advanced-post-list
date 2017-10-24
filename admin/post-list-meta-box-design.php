<?php
/**
 * Design Meta Box Template.
 *
 * Design Meta Box for making new Post Lists.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

/*
 * CLASS VARIABLES
 */
//var_dump( $post );
//var_dump( $metabox );
$apl_help_text = array(
	'before_list' => esc_html__(
		'Used to store any HTML & CSS code that exists before the post/content listings. Useful for div, ul, ol, tables, etc.. As well as storing CSS styling for IDs and Classes.',
		'advanced-post-list'
	),
	'list_content' => esc_html__(
		'This where you design how your posts are going to display in the post list. In here you can use HTML, CSS, PHP (requires the PHP shortcode), and the plugin\'s internal shortcodes. Info can be found at the bottom, or by clicking on the shortcode info found below "List content".',
		'advanced-post-list'
	),
	'after_list' => esc_html__(
		'Used for ending any elements that are still open, or to display a final message to the users/visitors.',
		'advanced-post-list'
	),
	'empty_message' => esc_html__(
		'This container holds the HTML & CSS content and if no posts are found to be listed in the preset. Then the preset post list will display this message. If no Empty Message is found, then the post list will use the Default Empty Message if enabled in the Plugin\'s Admin Settings. Otherwise, the plugin will display nothing like it was originally set as. Please Note: if you are using the Default Empty Message but you don\'t want to display anything in a certain preset post list. Then simple create an empty element to fall back on. For example, an empty "span" HTML element.',
		'advanced-post-list'
	),
);

if ( 'apl_post_list' === $post->post_type ) {
	$apl_post_list = new APL_Post_List( $post->post_name );
	$apl_design = new APL_Design( $apl_post_list->pl_apl_design );
} else if ( defined( 'ICL_SITEPRESS_VERSION' )  && 'apl_design' === $post->post_type ) {
	$apl_design = new APL_Design( $post->post_name );
}

?>
<?php include( APL_DIR . '/admin/admin-dialog-internal-shortcodes.php' ); ?>
<div class="apl-design-box-1">
	<div class="apl-design-column">
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_before"><?php esc_html_e( 'Before list:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['before_list']; ?>"></span>
			</div>
			<div>
				<textarea id="apl_textarea_before" class="apl-textarea-before large-text" name="apl_before" rows="3"><?php echo $apl_design->before; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_content"><?php esc_html_e( 'List content:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['list_content']; ?>"></span>
				<br />
				<a id="info-shortcodes" class="apl-help apl-help-alias">
					<?php esc_html_e( 'List of Shortcodes', 'advanced-post-list' ) ?>
					<span class="dashicons dashicons-clipboard"></span>
				</a>
			</div>
			<div>
				<textarea id="apl_textarea_content" class="apl-textarea-content large-text" name="apl_content" rows="9"><?php echo $apl_design->content; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_after"><?php esc_html_e( 'After list:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['after_list']; ?>"></span>
			</div>
			<div>
				<textarea id="apl_textarea_after" class="apl-textarea-after large-text" name="apl_after" rows="3"><?php echo $apl_design->after; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_empty_message"><?php esc_html_e( 'Empty Message:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['empty_message']; ?>"></span>
			</div>
			<div>
				<div style="margin: 3px 0px 3px 6px;">
					<input type="checkbox" id="apl_empty_message_enable" class="apl-empty-message-enable" name="apl_empty_enable" <?php echo ! empty( $apl_design->empty ) ? 'checked="checked"' : ''; ?> />
					<label for="apl_empty_message_enable"><?php esc_html_e( 'Enable (Overwrites Default)', 'advanced-post-list' ) ?></label>
				</div>
				<textarea id="apl_textarea_empty_message" class="apl-textarea-empty-message large-text" name="apl_empty_message" rows="9" style="<?php echo empty( $apl_design->empty ) ? 'display: none;' : ''; ?>" ><?php echo $apl_design->empty; ?></textarea>
			</div>
		</div>
		<?php if ( defined( 'ICL_SITEPRESS_VERSION' )  && 'apl_post_list' === $post->post_type ) : ?>
			<div class="apl-design-row">
				<div>
					<h3>WPML Manage</h3>
				</div>
				<div>
					<?php if ( 0 !== $apl_design->id ) : ?>
						<div style="margin: 1em 0;">
								<a class="button button-secondary button-edit-post-link" href="<?php echo get_edit_post_link( $apl_design->id ); ?>" target="_blank"><?php _e( 'Edit Translation(s) of this Design.', 'advanced-post-list' ) ?></a>
						</div>
					<?php else : ?>
						<p><?php _e( 'Please save the Post List in order to manage translations of this Design.', 'advanced-post-list' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
