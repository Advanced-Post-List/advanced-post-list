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
 * @return array Core option settings
 */
function apl_options_default() {
	// New name ( default_options ).
	// Step 1.
	$options = array();
	// Step 2.
	$options['version']              = APL_VERSION;
	$options['ignore_post_types']    = array();
	$options['delete_core_db']       = false;
	$options['default_empty_enable'] = false;
	$options['default_empty_output'] = '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>';

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
 * @param array $options Core option settings.
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

if ( ! function_exists( 'apl_get_post_lists' ) ) {
	/**
	 * Get Post Lists
	 *
	 * @since 0.4.4
	 *
	 * @param array  $args  Arguments for WP_Query.
	 * @param string $field The type of value to return.
	 * @return array
	 */
	function apl_get_post_lists( $args = array(), $field = 'id' ) {
		$default_args     = array(
			'post_type'   => 'apl_post_list',
			'post_status' => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
		);
		$args             = wp_parse_args( $args, $default_args );
		$query_post_lists = new WP_Query( $args );

		$rtn_post_lists = array();
		switch ( $field ) {
			case 'id':
				foreach ( $query_post_lists->posts as $v1_post ) {
					$rtn_post_lists[] = $v1_post->ID;
				}
				break;
			case 'slug':
				foreach ( $query_post_lists->posts as $v1_post ) {
					$rtn_post_lists[] = $v1_post->post_name;
				}
				break;
			case 'wp_post':
				$rtn_post_lists = $query_post_lists->posts;
				break;
			case 'apl_post_list':
			default:
				foreach ( $query_post_lists->posts as $v1_post ) {
					$rtn_post_lists[] = new APL_Post_List( $v1_post->post_name );
					// TODO Change to ID.
					//$rtn_post_lists[] = new APL_Post_List( $v1_post->ID );
				}
				break;
		}

		return $rtn_post_lists;
	}
}

if ( ! function_exists( 'apl_get_designs' ) ) {
	/**
	 * Get Designs
	 *
	 * @since 0.4.4
	 *
	 * @param array  $args  Arguments for WP_Query.
	 * @param string $field The type of value to return.
	 * @return array
	 */
	function apl_get_designs( $args = array(), $field = 'id' ) {
		$default_args  = array(
			'post_type'   => 'apl_design',
			'post_status' => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
		);
		$args          = wp_parse_args( $args, $default_args );
		$query_designs = new WP_Query( $args );

		$rtn_designs = array();
		switch ( $field ) {
			case 'id':
				foreach ( $query_designs->posts as $v1_post ) {
					$rtn_designs[] = $v1_post->ID;
				}
				break;
			case 'slug':
				foreach ( $query_designs->posts as $v1_post ) {
					$rtn_designs[] = $v1_post->post_name;
				}
				break;
			case 'wp_post':
				$rtn_designs = $query_designs->posts;
				break;
			case 'apl_design':
			default:
				foreach ( $query_designs->posts as $v1_post ) {
					$rtn_designs[] = new APL_Design( $v1_post->ID );
				}
				break;
		}

		return $rtn_designs;
	}
}
