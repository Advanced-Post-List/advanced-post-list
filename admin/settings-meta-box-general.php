<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump( admin_url( 'admin-post.php' ) );
$options = get_option( 'APL_Options' );

function apl_is_checked() {
	
}
?>
<form id="apl-settings-form" method="post" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" >
	<input type="hidden" name="action" value="apl_save_general_settings">
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label>Delete database on deactivate:</label>
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