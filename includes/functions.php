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
			'nopaging'    => true,
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
			'nopaging'    => true,
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

if ( 'apl_locate_template' ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * $default_path/$template_name
	 *
	 * @since 0.4.4.1
	 *
	 * @access public
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 * @return string
	 */
	function apl_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = 'advanced-post-list/';
		}

		if ( ! $default_path ) {
			$default_path = APL_DIR . 'templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Pro template.
		if ( ! $template && defined( 'APLP_DIR' ) ) {
			if ( file_exists( APLP_DIR . 'templates/' . $template_name ) ) {
				$template = APLP_DIR . 'templates/' . $template_name;
			}
		}

		// Default template.
		if ( ! $template || APL_TEMPLATE_DEBUG_MODE ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'apl_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'apl_get_template' ) ) {
	/**
	 * APL Get Template
	 *
	 * @since 0.4.4.1
	 *
	 * @param string $template_name
	 * @param array  $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function apl_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		// EXTRACT.
		foreach ( $args as $arg_key => $arg_value ) {
			if ( ! is_numeric( $arg_key ) ) {
				$$arg_key = $arg_value;
			} else {
				$arg_key  = 'arg_' . $arg_key;
				$$arg_key = $arg_value;
			}
		}

		$dir = apl_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $dir ) ) {
			/* translators: %s template name */
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Template %s does not exist.', 'advanced-post-list' ), '<code>' . $dir . '</code>' ), APL_VERSION );
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$dir = apply_filters( 'apl_get_template', $dir, $template_name, $args, $template_path, $default_path );

		do_action( 'apl_before_template_part', $template_name, $template_path, $dir, $args );

		include $dir;

		do_action( 'apl_after_template_part', $template_name, $template_path, $dir, $args );
	}
}

if ( 'apl_get_template_part' ) {
	/**
	 * Get template part.
	 *
	 * APL_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
	 *
	 * @since 0.4.4.1
	 *
	 * @param mixed  $slug Template slug.
	 * @param string $name Template name (default: '').
	 * @param array $args
	 */
	function apl_get_template_part( $slug, $name = '', $args = array() ) {
		$template = '';
		$slug_name = $slug . '-' . $name . '.php';

		// EXTRACT.
		//extract( $args );
		foreach ( $args as $arg_key => $arg_value ) {
			if ( ! is_numeric( $arg_key ) ) {
				$$arg_key = $arg_value;
			} else {
				$arg_key  = 'arg_' . $arg_key;
				$$arg_key = $arg_value;
			}
		}


		// Look in yourtheme/slug-name.php and yourtheme/advanced-post-list/slug-name.php.
		if ( $name && ! APL_TEMPLATE_DEBUG_MODE ) {
			$template = locate_template(
				array(
					$slug_name,
					'advanced-post-list/' . $slug_name,
				)
			);
		}

		// Get Pro slug-name.php.
		if ( ! $template && ! empty( $name ) && defined( 'APLP_DIR' ) ) {
			if ( file_exists( APLP_DIR . '/templates/' . $slug_name ) ) {
				$template = APLP_DIR . '/templates/' . $slug_name;
			}
		}

		// Get default slug-name.php.
		if ( ! $template && $name && file_exists( APL_DIR . '/templates/' . $slug_name ) ) {
			$template = APL_DIR . '/templates/' . $slug_name;
		}

		// If template file doesn't exist, use slug.
		// Look in yourtheme/slug.php and yourtheme/advanced-post-list/slug.php.
		if ( ! $template && ! WC_TEMPLATE_DEBUG_MODE ) {
			$template = locate_template( array( $slug . '.php', 'advanced-post-list/' . $slug . '.php' ) );
			//$template = apl_locate_template( array( $slug . '.php', 'advanced-post-list/' . $slug . '.php' ) );
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'apl_get_template_part', $template, $slug, $name );

		if ( $template ) {
			//load_template( $template, false );
			include $template;
		}
	}
}
