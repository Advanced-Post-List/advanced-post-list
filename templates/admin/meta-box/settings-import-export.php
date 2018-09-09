<?php
/**
 * Setting's Import/Export Metabox
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

$apl_help_text = array(
	'export_filename' => 
		esc_html__( 'Exports the whole APL preset database/table. Illegal (< > : " / \ | , ? *) characters cannot be used as the exported filename.', 'advanced-post-list' ) . '<br />',
	'import_file' => 
		esc_html__( 'Imports data into the database. If there are any pre-existing data, you will be prompted list of overwrite items.', 'advanced-post-list' ) . '<br />',
	'restore_defaults' => 
		esc_html__( 'Designed to restore only the default preset table the plugin initially came with.', 'advanced-post-list' ) . '<br />',
);
?>

<div style="display: inline-block; width: 100%;">
	<h3><?php esc_html_e( 'Export Post List Database', 'advanced-post-list' ); ?></h3>
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label for="apl_export_filename"><?php esc_html_e( 'Filename:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['export_filename']; ?>"></span>
		</div>
		<div class="apl-cell-1of2" >
			<input type="text" id="apl_export_filename" style="width: 100%;" value="APL-<?php echo date( 'Y-m-d-Hi' ); ?>" />
			<?php submit_button( __( 'Export' ), 'secondary', 'apl_export', true ); ?>
		</div>
	</div>
	
	<hr />
	
	<h3><?php esc_html_e( 'Import Post List File', 'advanced-post-list' ); ?></h3>
	<form id="form_settings_import" name="apl_form_import" method="post" enctype="multipart/form-data" action>
		<div class="apl-settings-row" >

			<div class="apl-row-first-cell">
				<label><?php esc_html_e( 'Upload File:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['import_file']; ?>"></span>
			</div>
			<div class="apl-cell-1of2" >
				<input type="file" id="apl_file_import" name="apl_import_file" multiple="multiple" />
				<div>
					<?php submit_button( __( 'Import' ), 'secondary', 'apl_import', true ); ?>
				</div>
			</div>
		</div>
	</form>
	<hr />
	
	<h3><?php esc_html_e( 'Restore Defaults', 'advanced-post-list' ); ?></h3>
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label><?php esc_html_e( 'Default Post Lists:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['restore_defaults']; ?>"></span>
		</div>
		<div class="apl-cell-1of2" >
			<p>
				<?php esc_html_e( 'Restores the plugin\'s default preset table only, and will overwrite/add the default Post Lists. This will not delete other Post Lists (as long as the name isn\'t a default name).', 'advanced-post-list' ); ?>
			</p>
			<div>
				<?php submit_button( __( 'Restore' ), 'secondary', 'apl_restore_defaults', true ); ?>
			</div>
		</div>
			
	</div>
</div>


