<?php
/**
 * Default Notice Template.
 *
 * @since 0.4.2
 *
 * @see APL_Notice::display_notice_default();
 * @uses $notice in APL_Notice::notices
 * @package Advanced-Post-List
 * @subpackage APL_Notices
 */

$notice = $this->notices[ $a_notice_slug ];
//var_dump( $notice );
$notice_class = 'notice-info';
if ( isset( $notice['class'] ) && ! empty( $notice['class'] ) ) {
	$notice_class = $notice['class'];
}

?>
<div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible apl-notice-container apl-notice-<?php echo esc_attr( $notice['slug'] ); ?>">
	<p><?php echo esc_html( $notice['message'] ); ?></p>
	<p class="apl-notice-actions">
		<?php foreach ( $notice['action_options'] as $key => $action_option ) : ?>
			<?php
			$link   = $action_option['link'];
			$id     = 'apl-notice-action-' . $notice['slug'] . '-' . $key;
			$class  = 'apl-notice-action ';
			$class .= 'apl-notice-action-' . $key . ' ';
			$class .= empty( $action_option['class'] ) ? ' ' : '';
			$class .= $action_option['class'];
			?>
		    <a href="<?php echo esc_url( $link ); ?>" id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" target="_blank" rel="noopener"><?php echo esc_textarea( $action_option['text'] ); ?></a>
		<?php endforeach; ?>
	</p>
</div>
