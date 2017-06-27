<?php

/**
 * APL Functions
 *
 * Common APL functions publicly accessible across multiple classes.
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage Advanced Post List
 * @since 0.4.0
 */


/**
 * APL Load Option.
 *
 * Gets APLOptions from WordPress database and returns it. If there is no data,
 * then set to defaults, save, and return options.
 *
 * @since 0.1.0
 * @since 0.4.0 - Moved to non-class function.
 *
 * @see Function/method/class relied on
 * @link URL
 *
 * @return object APL option settings.
 */
function apl_options_load() {
	$options = get_option( 'APL_Options' );

	if ( false !== $options ) {
		return $options;
	} else {
		$options = array();
		$options['version']               = APL_VERSION;
		$options['delete_core_db']        = false;
		$options['default_empty_enable']  = false;
		$options['default_empty_output']  = '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>';

		apl_options_save( $defaults );
		return $defaults;
	}
}

/**
 * APL Save Options
 *
 * Save APL_Options.
 *
 * @since 0.1.0
 * @since 0.4.0 - Moved to non-class function.
 *
 * @see Function/method/class relied on
 * @link URL
 *
 * @param object $options Core option settings.
 */
function apl_options_save( $options ) {
	if ( isset( $options ) ) {
		update_option( 'APL_Options', $options );
	}
}

/**
 * Get Post Types to Display.
 *
 * Displays a *valid* list of post types that also aren't on the global ignore list.
 *
 * @since 0.4.0
 * @access private
 *
 * @see $this->_ignore_post_types.
 *
 * @return array List of Post Types.
 */
function apl_get_display_post_types() {
	$rtn_post_types = array();
	
	$ignore_post_types = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'apl_post_list',
		'apl_design',
	);
	$ignore_post_types = apply_filters( 'apl_display_post_types', $ignore_post_types );
	
	// Get all Post Types.
	$post_type_objs = get_post_types( '', 'objects' );
	// Remove ignored Post Types.
	foreach ( $ignore_post_types as $value ) {
		unset( $post_type_objs[ $value ] );
	}

	foreach ( $post_type_objs as $key => $value ) {
		$rtn_post_types[ $key ] = $value->labels->singular_name;
	}

	return $rtn_post_types;
}
