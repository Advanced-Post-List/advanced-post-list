<?php
/**
 * Design Meta Box Template.
 *
 * Design Meta Box for making new Post Lists.
 *
 * @package WordPress
 * @subpackage APL_Admin
 * @since 0.4.0
 */

/*
 * CLASS VARIABLES
 */
//var_dump( $post );
//var_dump( $metabox );
$apl_post_list = new APL_Post_List( $post->post_name );
$apl_design = new APL_Design( $apl_post_list->pl_apl_design );
?>
<div class="apl-design-box-1">
	<div class="apl-design-column">
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_before"><?php esc_html_e( 'Before list:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<textarea id="apl_textarea_before" class="apl-textarea-before large-text" name="apl_before" rows="3"><?php echo $apl_design->before; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_content"><?php esc_html_e( 'List content:', 'advanced-post-list' ); ?></label><br />
				<a id="info_13" class="info_a_link">
					<span style="float: left;"><?php esc_html_e( 'List of Shortcodes', 'advanced-post-list' ) ?></span>
					<span class="ui-icon ui-icon-info info-icon" style="float: left;"></span>
				</a>
			</div>
			<div>
				<textarea id="apl_textarea_content" class="apl-textarea-content large-text" name="apl_content" rows="9"><?php echo $apl_design->content; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_after"><?php esc_html_e( 'After list:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<textarea id="apl_textarea_after" class="apl-textarea-after large-text" name="apl_after" rows="3"><?php echo $apl_design->after; ?></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_textarea_empty_message"><?php esc_html_e( 'Empty Message:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<div style="margin: 3px 0px 3px 6px;">
					<input type="checkbox" id="apl_empty_message_enable" class="apl-empty-message-enable" <?php echo !empty( $apl_design->empty ) ? 'checked="checked"' : ''; ?> />
					<label for="apl_empty_message_enable"><?php esc_html_e( 'Enable (Overwrites Default)', 'advanced-post-list' ) ?></label>
				</div>
				<textarea id="apl_textarea_empty_message" class="apl-textarea-empty-message large-text" name="apl_empty_message" rows="9" style="<?php echo empty( $apl_design->empty ) ? 'display: none;' : ''; ?>" ><?php echo $apl_design->empty; ?></textarea>
			</div>
		</div>
	</div>
</div>
