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
	 * @since 0.5 Change notice variable to use function.
	 *
	 * @global APL_Notices $apl_notices
	 *
	 * @param boolean $update Updates the notice with new content and configurations.
	 * @param boolean $reset  Notice are re-initiated.
	 */
	function apl_notice_set_activation_review_plugin( $update = false, $reset = false ) {
		global $apl_notices;

		// TODO Optimize - Create a callback function/method to store most of the configurations (Avoid Database concept).
		// Dynamic variable could be stored in the database. Config functions could go into a config file/folder.
		$notice = apl_notice_review_plugin();

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

	/**
	 * APL Notice - Review Plugin
	 *
	 * @since 0.5
	 *
	 * @return array
	 */
	function apl_notice_review_plugin() {
		return array(
			'slug'           => 'apl_review_plugin',
			'delay_time'     => 1036800,
			'message'        => __( 'Looks like you\'ve been using Advanced Post List for awhile now, and that\'s awesome! By helping with a 5-star review, it also helps to reach out to more people.', 'advanced-post-list' ),
			'target'         => 'user',
			'screens'        => array(),
			'action_options' => array(
				array(
					'time'    => 0,
					'text'    => __( 'Yes, absolutely!', 'advanced-post-list' ),
					'link'    => 'https://wordpress.org/support/plugin/advanced-post-list/reviews?rate=5#new-post',
					'dismiss' => false,
					'class'   => 'apl-notice-actions-left',
				),
				array(
					'text'    => 'Maybe, give me a Week.',
					'time'    => 432000,
					'dismiss' => false,
					'class'   => 'apl-notice-actions-left',
				),
				array(
					'time'    => 0,
					'text'    => 'Already did. Dismiss.',
					'dismiss' => true,
					'class'   => 'apl-notice-actions-left',
				),
			),
		);
	}
}
