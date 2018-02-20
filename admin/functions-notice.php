<?php
/**
 * Notice Functions for APL_Notices
 *
 * @since 0.4.2
 * @package APL_Core
 * @subpackage APL_Notices
 */

if ( class_exists( 'APL_Notices' ) ) {

	/**
	 * Set Notice on Activation to Review Plugin
	 *
	 * A delayed notice that is set during activation, or initialization (old installs),
	 * to later display a review/rate APL plugin. Delay time: 12 days.
	 * Delay "...give me a week." 5 days
	 *
	 * @since 0.4.2
	 *
	 * @global APL_Notices $apl_notices
	 *
	 * @param boolean $update Updates the notice with new content and configurations.
	 * @param boolean $reset  Notice are re-initiated.
	 */
	function apl_notice_set_activation_review_plugin( $update = false, $reset = false ) {
		global $apl_notices;

		$notice = array(
			'slug'           => 'apl_review_plugin',
			'delay_time'     => 1036800,
			'message'        => __( 'Looks like you\'ve been using Advanced Post List for awhile now, and that\'s awesome! By helping with a 5-star review, it also helps to reach out to more people.', 'advanced-post-list' ),
			'action_options' => array(),
			'target'         => 'user',
			'screens'        => array(),
		);

		$notice['action_options'][] = array(
			'time'    => 0,
			'text'    => __( 'Yes, absolutely!', 'advanced-post-list' ),
			'link'    => 'https://wordpress.org/support/plugin/advanced-post-list/reviews?rate=5#new-post',
			'dismiss' => false,
			'class'   => 'apl-notice-actions-left',
		);
		$notice['action_options'][] = array(
			'text'    => 'Maybe, give me a Week.',
			'time'    => 432000,
			'dismiss' => false,
			'class'   => 'apl-notice-actions-left',
		);
//		$notice['action_options'][] = array(
//			'time'    => 0,
//			'text'    => 'No...something isn\'t right',
//			'link'    => 'https://wordpress.org/support/plugin/advanced-post-list/#new-post',
//			'dismiss' => false,
//			'class'   => 'apl-notice-actions-left',
//		);
		$notice['action_options'][] = array(
			'time'    => 0,
			'text'    => 'Already did. Dismiss.',
			'dismiss' => true,
			'class'   => 'apl-notice-actions-left',
		);

		if ( $apl_notices->insert_notice( $notice ) ) {
			//apl_footer_set_review();
		} elseif ( $update ) {
			$apl_notices->update_notice( $notice );

			if ( $reset ) {
				$apl_notices->activate_notice( $notice['slug'] );
				//apl_footer_remove_review();
				//apl_footer_set_review();
			}
		}
	}
}
