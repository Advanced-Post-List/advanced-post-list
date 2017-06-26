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
 * Summary.
 *
 * Gets APLOptions from WordPress database and send the option data back if any.
 *
 * STEP 1 - Get APLOptions from WordPress Database or get false if options
 *          doesn't exist.
 * STEP 2 - If Options exists, then return object. Otherwise return false.
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
	// Step 1.
	$options = get_option( 'APL_Options' );

	// Step 2.
	if ( false !== $options ) {
		return $options;
	} else {
		$options = array();
		$options['version']          = APL_VERSION;
		//$options['preset_db_names']  = array( 'default' );
		$options['delete_core_db']   = true;
		//$options['jquery_ui_theme']  = 'overcast';
		$options['default_exit']     = false;
		$options['default_exit_msg'] = '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>';
		$options['error']            = '';
		
		apl_options_save( $defaults );
		return $defaults;
	}
}

/**
 * Summary.
 *
 * Description.
 *
 * STEP 1 - If option data (param) exists, save option data to
 *               WordPress database.
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
	// STEP 1.
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
