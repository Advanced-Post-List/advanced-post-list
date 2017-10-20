<?php
/**
 * Setting's Page
 *
 * Content displayed when the Settings Submenu Callback is executed.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

global $hook_suffix;

?>

<div class="wrap" >
	<h2><?php esc_html_e( 'APL - Settings', 'advanced-post-list' ); ?></h2>
	<?php settings_errors(); ?>
<!--	<form id="apl-settings-form" method="post" action="<?php //admin_url( 'admin-ajax.php' ); ?>" >-->
	<div id="apl-settings-form" >
		<?php
		// Used to save closed meta boxes and their order.
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		// Group name from register_settings.
		settings_fields( 'apl_settings' );
		?>
		<div id="poststuff" >
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>" >
				<div id="post-body-content" > 
					<?php do_meta_boxes( $hook_suffix, 'normal', null ); ?> 
				</div> 
				<div id="postbox-container-1" class="postbox-container" >
					<?php do_meta_boxes( $hook_suffix, 'side', null ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container" >
					<?php do_meta_boxes( $hook_suffix, 'advanced', null ); ?>
				</div>
			</div><!-- #post-body -->
			<!--<br class="clear">-->
		</div><!-- #poststuff -->
	</div>
		
	<!--</form>-->
</div><!-- .wrap -->
