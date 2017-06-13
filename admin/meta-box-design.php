<?php
/**
 * Design Meta Box Template.
 *
 * Design Meta Box for making new Post Lists.
 *
 * @package WordPress
 * @subpackage Advanced Posr List
 * @since 0.4.0
 */

/*
 * CLASS VARIABLES
 */
//var_dump( $post );
//var_dump( $metabox );

?>
<div class="apl-design-box-1">
	<div class="apl-design-column">
		<div class="apl-design-row">
			<div>
				<label for="apl_before"><?php esc_html_e( 'Before list:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<textarea id="apl_before" class="apl-textarea-before large-text" name="before" rows="3"></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_content"><?php esc_html_e( 'List content:', 'advanced-post-list' ); ?></label><br />
				<a id="info_13" class="info_a_link">
					<span style="float: left;"><?php esc_html_e( 'List of Shortcodes', 'advanced-post-list' ) ?></span>
					<span class="ui-icon ui-icon-info info-icon" style="float: left;"></span>
				</a>
			</div>
			<div>
				<textarea id="apl_content" class="apl-textarea-content large-text" name="content" rows="9"></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_after"><?php esc_html_e( 'After list:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<textarea id="apl_after" class="apl-textarea-after large-text" name="after" rows="3"></textarea>
			</div>
		</div>
		<div class="apl-design-row">
			<div>
				<label for="apl_empty_message"><?php esc_html_e( 'Empty Message:', 'advanced-post-list' ); ?></label>
			</div>
			<div>
				<div style="margin: 3px 0px 3px 6px;">
					<input type="checkbox" id="apl_empty_message_enable" class="apl-empty-message-enable" /><?php esc_html_e( 'Enable (Overwrites Default)', 'advanced-post-list' ) ?>
				</div>
				<textarea id="apl_empty_message" class="apl-textarea-empty-message large-text" name="empty_message" rows="9"></textarea>
			</div>
		</div>
	</div>
</div>
