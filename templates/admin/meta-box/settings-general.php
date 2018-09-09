<?php
/**
 * Setting's General Settings Metabox
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

//var_dump( admin_url( 'admin-post.php' ) );
$options = get_option( 'apl_options' );

// Post Types.
$ignore_pt = apl_default_ignore_post_types();
$tmp_post_type_objs = get_post_types( '', 'objects' );
foreach ( $ignore_pt as $value ) {
	unset( $tmp_post_type_objs[ $value ] );
}

foreach ( $tmp_post_type_objs as $key => $value ) {
	$tmp_post_types[ $key ] = $value->labels->singular_name;
}

$apl_help_text = array(
	'ignore_post_types' =>
		esc_html__( 'Used for ignoring post types when creating/editing a post list.', 'advanced-post-list' ),
	'delete_on_deactivate' => 
		esc_html__( 'If "No" is selected, then the plugin\'s database data will not be removed when the plugin is deactivated. When re-activated, the plugin data will restored as it was left. Please Note: If the plugin is removed/uninstalled, then the plugin\'s data will be removed regardless.', 'advanced-post-list' ),
	'default_empty_enable' => 
		'<b>' . esc_html__( 'Enable Default Empty Message: ', 'advanced-post-list' ) . '</b>' .
		esc_html__( 'Used as a default option to use if no posts are found and the Empty Message is empty within the preset post list.', 'advanced-post-list' ) . '<br />' .
		'<b>' . esc_html__( 'Enable Global Exit (boolean): ', 'advanced-post-list' ) . '</b>' .
		esc_html__( 'If enabled (yes), the all presets will fallback on the global/default Empty Message.', 'advanced-post-list' ) . '<br />' .
		'<b>' . esc_html__( 'Empty Message: ', 'advanced-post-list' ) . '</b>' .
		esc_html__( 'Contains the message that will be displayed if no posts are found. HTML and CSS can be used.', 'advanced-post-list' ),
);
?>
<form id="apl-settings-form" method="post" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" >
	<input type="hidden" name="action" value="apl_save_general_settings">
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label>Ignore Post Types:</label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['ignore_post_types']; ?>"></span>
		</div>
		<div>
			<?php foreach ( $tmp_post_types as $k1_slug => $v1_name ) : ?>
				<input type="checkbox" id="apl_ignore_pt_<?php echo $k1_slug; ?>" class="apl-chk-ignore-pt" name="apl_ignore_pt_<?php echo $k1_slug; ?>" value="<?php echo $k1_slug; ?>" <?php echo ( in_array( $k1_slug, $options['ignore_post_types'] ) ) ? 'checked="checked"' : ''; ?> />
				<label for="apl_ignore_pt_<?php echo $k1_slug; ?>"><?php echo $v1_name; ?></label>
				<br />
			<?php endforeach;?>
		</div>
	</div>
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label>Delete database on deactivate:</label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['delete_on_deactivate']; ?>"></span>
		</div>
		<div>
			<input type="radio" id="apl_delete_on_deactivate_yes" class="apl-radio-delete-on-deactivate" name="apl_delete_on_deactivate" value="yes" <?php echo $options['delete_core_db'] ? 'checked="checked"' : ''; ?> />
			<label for="apl_delete_on_deactivate_yes">Yes</label>
		</div>
		<div>
			<input type="radio" id="apl_delete_on_deactivate_no" class="apl-radio-delete-on-deactivate" name="apl_delete_on_deactivate" value="no" <?php echo $options['delete_core_db'] ? '' : 'checked="checked"'; ?> />
			<label for="apl_delete_on_deactivate_no">No</label>
		</div>
	</div>
	<div class="apl-settings-row" style="margin-bottom: 3px;">
		<div class="apl-row-first-cell">
			<label>Enable Default Empty Message:</label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['default_empty_enable']; ?>"></span>
		</div>
		<div>
			<input type="radio" id="apl_empty_enable_yes" class="apl-default-empty-enable" name="apl_default_empty_enable" value="yes" <?php echo $options['default_empty_enable'] ? 'checked="checked"' : ''; ?> />
			<label for="apl_empty_enable_yes" >Yes</label>
		</div>
		<div>
			<input type="radio" id="apl_empty_enable_no" class="apl-default-empty-enable" name="apl_default_empty_enable" value="no" <?php echo $options['default_empty_enable'] ? '' : 'checked="checked"'; ?> />
			<label for="apl_empty_enable_no" >No</label>
		</div>
	</div>
	<div class="apl-settings-row" >
		<textarea id="apl_textarea_default_empty" class="apl-default-empty-message" name="apl_default_empty_message" rows="9" ><?php echo $options['default_empty_output']; ?></textarea>
	</div>
	<?php submit_button( __( 'Save Settings' ), 'primary', 'apl_save_settings', false ); ?>
</form>
