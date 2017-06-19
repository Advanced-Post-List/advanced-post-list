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
