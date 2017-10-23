<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div style="display: inline-block; width: 100%;">
	<h3><?php esc_html_e( 'Export Post List Database', 'advanced-post-list' ); ?></h3>
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label for="apl_export_filename"><?php esc_html_e( 'Filename:', 'advanced-post-list' ); ?></label>
		</div>
		<div class="apl-cell-1of2" >
			<input type="text" id="apl_export_filename" style="width: 100%;" value="APL-<?php echo date('Y-m-d-Hi'); ?>" />
			<?php submit_button( __( 'Export' ), 'secondary', 'apl_export', true ); ?>
		</div>
	</div>
	
	<hr />
	
	<h3><?php esc_html_e( 'Import Post List File', 'advanced-post-list' ); ?></h3>
	<form id="form_settings_import" name="apl_form_import" method="post" enctype="multipart/form-data" action>
		<div class="apl-settings-row" >

			<div class="apl-row-first-cell">
				<label><?php esc_html_e( 'Upload File:', 'advanced-post-list' ); ?></label>
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
	
	<h3><?php esc_html_e( 'Restore Defaults ( WIP )', 'advanced-post-list' ); ?></h3>
	<div class="apl-settings-row" >
		<div class="apl-row-first-cell">
			<label><?php esc_html_e( 'Default Post Lists:', 'advanced-post-list' ); ?></label>
		</div>
		<div class="apl-cell-1of2" >
			<p>
				<?php 
				esc_html_e( 
					'Restores the plugin\'s default preset table only, and will ' .
					'overwrite/add the default Post Lists. This will not delete ' .
					'other Post Lists (as long as the name isn\'t a default name).',
					'advanced-post-list'
				);
				?>
			</p>
			<p>
				<?php 
				esc_html_e( 
					'Note: This is currently a Work In Progress. The functionality ' .
					'is relatively simple, however, many updates have taken place ' .
					'since then, and many of the defaults are relatively old.',
					'advanced-post-list'
				);
				?>
			</p>
			<div>
				<?php submit_button( __( 'Restore' ), 'secondary', 'apl_restore', true ); ?>
			</div>
		</div>
			
	</div>
</div>


