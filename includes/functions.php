<?php

/**
 * APL Functions
 *
 * Common APL functions publicly accessible across multiple classes.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.4.0
 */

/**
 * APL Option Defaults.
 *
 * Sets options to default values. Deprecated.
 *
 * STEP 1 - Set options as an array.
 * STEP 2 - Add default values to options.
 * STEP 3 - Return Options.
 *
 * @since 0.1.0
 * @access private
 *
 * @return object Core option settings
 */
function apl_options_default() {
	// New name ( default_options ).
	// Step 1.
	$options = array();
	// Step 2.
	$options['version']               = APL_VERSION;
	$options['ignore_post_types']     = array();
	$options['delete_core_db']        = false;
	$options['default_empty_enable']  = false;
	$options['default_empty_output']  = '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>';

	// Step 3.
	return $options;
}

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
	$options = get_option( 'apl_options' );

	if ( false !== $options ) {
		return $options;
	} else {
		$options = apl_options_default();
		apl_options_save( $options );
		return $options;
	}
}

/**
 * APL Save Options.
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
	$default_options = apl_options_default();
	$options = wp_parse_args( $options, $default_options );

	if ( isset( $options ) ) {
		update_option( 'apl_options', $options );
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

	$options = apl_options_load();
	$ignore_post_types = apl_default_ignore_post_types();
	$ignore_post_types = apply_filters( 'apl_display_post_types_ignore', $ignore_post_types );
	$ignore_post_types = wp_parse_args( $ignore_post_types, $options['ignore_post_types'] );

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

/**
 * APL's Default Ignore Post Types
 *
 * @since 0.4.0
 */
function apl_default_ignore_post_types() {
	return array(
		'attachment',
		'revision',
		'nav_menu_item',
		'apl_post_list',
		'apl_design',
	);
}

