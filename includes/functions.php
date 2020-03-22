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
 * Sets apl_options to default values.
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

	/**
	 * Filter - APL Options Default
	 *
	 * Default array keys and values for APL_Options.
	 *
	 * @since 1.0
	 *
	 * @param array $options
	 */
	$options = apply_filters( 'apl_options_default', $options );

	// Step 3.
	return $options;
}

/**
 * APL Load Option.
 *
 * Gets APLOptions from WordPress database and returns it. If there is no data,
 * then set to defaults, save, and return options.
 *
 * @todo Maybe-Change to apl_get_options.
 *
 * @since 0.1.0
 * @since 0.4.0 - Moved to non-class function.
 *
 * @see Function/method/class relied on
 * @link URL
 *
 * @return array APL option settings.
 */
function apl_options_load() {
	$options = get_option( 'apl_options' );

	if ( false !== $options ) {
		$options = wp_parse_args( $options, apl_options_default() );
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
 * @todo Maybe-Change to apl_set_options.
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

/**
 * Allowed Global Attributes
 *
 * @since 0.5.6.1
 *
 * @return array
 */
function apl_allowed_global_attributes() {
	return array(
		'aria-*'              => true,
		'accesskey'           => true,
		'autocapitalize'      => true,
		'autocomplete'        => true,
		'class'               => true,
		'contenteditable'     => true,
		'data-*'              => true,
		'dir'                 => true,
		'draggable'           => true,
		'dropzone'            => true,
		'exportparts'         => true,
		'hidden'              => true,
		'id'                  => true,
		'inputmode'           => true,
		'is'                  => true,
		'itemid'              => true,
		'intemprop'           => true,
		'itemref'             => true,
		'itemscope'           => true,
		'itemtype'            => true,
		'lang'                => true,
		'part'                => true,
		'slot'                => true,
		'spellcheck'          => true,
		'style'               => true,
		'tabindex'            => true,
		'title'               => true,
		'translate'           => true,
		'onabort'             => true,
		'onautocomplete'      => true,
		'onautocompleteerror' => true,
		'onblur'              => true,
		'oncancel'            => true,
		'oncanplay'           => true,
		'oncanplaythrough'    => true,
		'onchange'            => true,
		'onclick'             => true,
		'onclose'             => true,
		'oncontextmenu'       => true,
		'oncuechange'         => true,
		'ondblclick'          => true,
		'ondrag'              => true,
		'ondragend'           => true,
		'ondragenter'         => true,
		'ondragexit'          => true,
		'ondragleave'         => true,
		'ondragover'          => true,
		'ondragstart'         => true,
		'ondrop'              => true,
		'ondurationchange'    => true,
		'onemptied'           => true,
		'onended'             => true,
		'onerror'             => true,
		'onfocus'             => true,
		'oninput'             => true,
		'oninvalid'           => true,
		'onkeydown'           => true,
		'onkeypress'          => true,
		'onkeyup'             => true,
		'onload'              => true,
		'onloadeddata'        => true,
		'onloadedmetadata'    => true,
		'onloadstart'         => true,
		'onmousedown'         => true,
		'onmouseenter'        => true,
		'onmouseleave'        => true,
		'onmousemove'         => true,
		'onmouseout'          => true,
		'onmouseover'         => true,
		'onmouseup'           => true,
		'onmousewheel'        => true,
		'onpause'             => true,
		'onplay'              => true,
		'onplaying'           => true,
		'onprogress'          => true,
		'onratechange'        => true,
		'onreset'             => true,
		'onresize'            => true,
		'onscroll'            => true,
		'onseeked'            => true,
		'onseeking'           => true,
		'onselect'            => true,
		'onshow'              => true,
		'onsort'              => true,
		'onstalled'           => true,
		'onsubmit'            => true,
		'onsuspend'           => true,
		'ontimeupdate'        => true,
		'ontoggle'            => true,
		'onvolumechange'      => true,
		'onwaiting'           => true,
	);
}

/**
 * Allowed Tags - Default
 *
 * @since 0.5.6.1
 *
 * @return array
 */
function apl_allowed_tags_default() {
	return array(
		// Content Sectioning.
		'address'  => apl_allowed_global_attributes(),
		'articles' => apl_allowed_global_attributes(),
		'aside'    => apl_allowed_global_attributes(),
		'footer'   => apl_allowed_global_attributes(),
		'header'   => apl_allowed_global_attributes(),
		'h1'       => apl_allowed_global_attributes(),
		'h2'       => apl_allowed_global_attributes(),
		'h3'       => apl_allowed_global_attributes(),
		'h4'       => apl_allowed_global_attributes(),
		'h5'       => apl_allowed_global_attributes(),
		'h6'       => apl_allowed_global_attributes(),
		'hgroup'   => apl_allowed_global_attributes(),
		'main'     => apl_allowed_global_attributes(),
		'nav'      => apl_allowed_global_attributes(),
		'section'  => apl_allowed_global_attributes(),

		// Text Content.
		'blockquote' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'cite' => true,
			)
		),
		'dd'         => array_merge(
			apl_allowed_global_attributes(),
			array(
				'nowrap' => true,
			)
		),
		'div'        => apl_allowed_global_attributes(),
		'dl'         => apl_allowed_global_attributes(),
		'dt'         => apl_allowed_global_attributes(),
		'figcaption' => apl_allowed_global_attributes(),
		'figure'     => apl_allowed_global_attributes(),
		'hr'         => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'color'   => true,
				'noshade' => true, // Deprecated.
				'size'    => true, // Deprecated.
				'width'   => true, // Deprecated.
			)
		),
		'li'         => array_merge(
			apl_allowed_global_attributes(),
			array(
				'value' => true,
			)
		),
		'ol'         => array_merge(
			apl_allowed_global_attributes(),
			array(
				'reversed' => true,
				'start'    => true,
			)
		),
		'p'          => apl_allowed_global_attributes(),
		'pre'        => array_merge(
			apl_allowed_global_attributes(),
			array(
				'pre' => true,
			)
		),
		'ul'         => array_merge(
			apl_allowed_global_attributes(),
			array(
				'compact' => true,
				'type'    => true,
			)
		),

		// Inline Text Sematics
		'a'      => array_merge(
			apl_allowed_global_attributes(),
			array(
				'download'       => true,
				'href'           => true,
				'hreflang'       => true,
				'name'           => true, // Deprecated.
				'ping'           => true,
				'referrerpolicy' => true,
				'rel'            => true,
				'rev'            => true, // Deprecated.
				'target'         => true,
				'type'           => true,
			)
		),
		'abbr'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'title' => true,
			)
		),
		'b'      => apl_allowed_global_attributes(),
		'bdi'    => apl_allowed_global_attributes(),
		'bdo'    => apl_allowed_global_attributes(),
		'br'     => array_merge(
			apl_allowed_global_attributes(),
			array(
				'clear' => true, // Deprecated.
			)
		),
		'cite'   => apl_allowed_global_attributes(),
		'code'   => apl_allowed_global_attributes(),
		'data'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'value' => true,
			)
		),
		'dfn'    => apl_allowed_global_attributes(),
		'em'     => apl_allowed_global_attributes(),
		'i'      => apl_allowed_global_attributes(),
		'kbd'    => apl_allowed_global_attributes(),
		'mark'   => apl_allowed_global_attributes(),
		'q'      => array_merge(
			apl_allowed_global_attributes(),
			array(
				'cite' => true,
			)
		),
		'rb'     => apl_allowed_global_attributes(),
		'rp'     => apl_allowed_global_attributes(),
		'rt'     => apl_allowed_global_attributes(),
		'rtc'    => apl_allowed_global_attributes(),
		'ruby'   => apl_allowed_global_attributes(),
		's'      => apl_allowed_global_attributes(),
		'samp'   => apl_allowed_global_attributes(),
		'small'  => apl_allowed_global_attributes(),
		'span'   => apl_allowed_global_attributes(),
		'strong' => apl_allowed_global_attributes(),
		'sub'    => apl_allowed_global_attributes(),
		'sup'    => apl_allowed_global_attributes(),
		'time'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'datetime' => true,
			)
		),
		'u'      => apl_allowed_global_attributes(),
		'var'    => apl_allowed_global_attributes(),
		'wbr'    => apl_allowed_global_attributes(),

		// Image & Media.
		'area'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'alt'            => true,
				'accesskey'      => true, // Deprecated.
				'coords'         => true,
				'download'       => true,
				'href'           => true,
				'hreflang'       => true,
				'media'          => true,
				'name'           => true, // Deprecated.
				'nohref'         => true, // Deprecated.
				'ping'           => true,
				'referrerpolicy' => true,
				'rel'            => true,
				'shape'          => true,
				'tabindex'       => true, // Deprecated.
				'target'         => true,
				'type'           => true, // Deprecated.
			)
		),
		'audio' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'autoplay' => true,
				'buffered' => true,
				'controls' => true,
				'loop'     => true,
				'muted'    => true,
				'played'   => true,
				'preload'  => true,
				'src'      => true,
				'volume'   => true,
			)
		),
		'img'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'          => true, // Deprecated.
				'alt'            => true,
				'border'         => true, // Deprecated.
				'crossorigin'    => true,
				'decoding'       => true,
				'height'         => true,
				'hspace'         => true, // Deprecated.
				'importance'     => true,
				'intrinsicsize'  => true,
				'ismap'          => true,
				'loading'        => true,
				'longdesc'       => true, // Deprecated.
				'name'           => true, // Deprecated.
				'onerror'        => true,
				'referrerpolicy' => true,
				'sizes'          => true,
				'src'            => true,
				'srcset'         => true,
				'usemap'         => true,
				'vspace'         => true, // Deprecated.
				'width'          => true,
			)
		),
		'map'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'map' => true,
			)
		),
		'track' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'default' => true,
				'kind'    => true,
				'label'   => true,
				'src'     => true,
				'srclang' => true,
			)
		),
		'video' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'autoplay'             => true,
				'autoPictureInPicture' => true,
				'buffered'             => true,
				'controls'             => true,
				'controlslist'         => true,
				'crossorigin'          => true,
				'currentTime'          => true,
				'duration'             => true,
				'height'               => true,
				'intrinsicsize'        => true,
				'loop'                 => true,
				'muted'                => true,
				'playinline'           => true,
				'poster'               => true,
				'preload'              => true,
				'src'                  => true,
				'width'                => true,
			)
		),

		// Embedded Content.
		'embed'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'height' => true,
				'src'    => true,
				'type'   => true,
				'width'  => true,
			)
		),
		'iframe'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'           => true, // Deprecated.
				'allow'           => true,
				'allowfullscreen' => true,
				'csp'             => true,
				'frameborder'     => true, // Deprecated.
				'height'          => true,
				'importance'      => true,
				'loading'         => true,
				'longdesc'        => true, // Deprecated.
				'marginheight'    => true, // Deprecated.
				'marginwidth'     => true, // Deprecated.
				'name'            => true,
				'referrerpolicy'  => true,
				'sandbox'         => true,
				'scrolling'       => true, // Deprecated.
				'src'             => true,
				'srcdoc'          => true,
				'width'           => true,
			)
		),
		'object'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'archive' => true, // Deprecated.
				'border' => true, // Deprecated.
				'classid' => true, // Deprecated.
				'codebase' => true, // Deprecated.
				'codetype' => true, // Deprecated.
				'data' => true,
				'declare' => true, // Deprecated.
				'form' => true,
				'height' => true,
				'name' => true,
				'standby' => true, // Deprecated.
				'tabindex' => true, // Deprecated.
				'type' => true,
				'typemustmatch' => true,
				'usemap' => true,
				'width' => true,
			)
		),
		'param'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'name'      => true,
				'type'      => true, // Deprecated.
				'value'     => true,
				'valuetype' => true, // Deprecated.
			)
		),
		'picture' => apl_allowed_global_attributes(),
		'source'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'media'  => true,
				'sizes'  => true,
				'src'    => true,
				'srcset' => true,
				'type'   => true,
			)
		),

		// Scripting.
		'canvas'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'height' => true,
				'width'  => true,
			)
		),
		'noscript' => apl_allowed_global_attributes(),
		'script'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'async'          => true,
				'crossorigin'    => true,
				'defer'          => true,
				'integrity'      => true,
				'language'       => true, // Deprecated.
				'nomodule'       => true,
				'referrerPolicy' => true,
				'src'            => true,
				'text'           => true,
				'type'           => true,
				'type.module'    => true,
			)
		),

		// Demarcating edits.
		'del' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'cite'     => true,
				'datetime' => true,
			)
		),
		'ins' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'cite'     => true,
				'datetime' => true,
			)
		),

		// Table Content.
		'caption'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align' => true, // Deprecated.
			)
		),
		'col'      => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'span'    => true,
				'valign'  => true, // Deprecated.
				'width'   => true, // Deprecated.
			)
		),
		'colgroup'      => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'span'    => true,
				'valign'  => true, // Deprecated.
				'width'   => true, // Deprecated.
			)
		),
		'table'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'       => true, // Deprecated.
				'bgcolor'     => true, // Deprecated.
				'border'      => true, // Deprecated.
				'cellpadding' => true, // Deprecated.
				'cellspacing' => true, // Deprecated.
				'frame'       => true, // Deprecated.
				'rules'       => true, // Deprecated.
				'summary'     => true, // Deprecated.
				'width'       => true, // Deprecated.
			)
		),
		'tbody'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'valign'  => true, // Deprecated.
			)
		),
		'td'       => array_merge(
			apl_allowed_global_attributes(),
			array(
				'abbr'    => true, // Deprecated.
				'align'   => true, // Deprecated.
				'axis'    => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'colspan' => true,
				'headers' => true,
				'rowspan' => true,
				'scope'   => true, // Deprecated.
				'valign'  => true, // Deprecated.
				'width'   => true, // Deprecated.
			)
		),
		'tfoot'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'valign'  => true, // Deprecated.
			)
		),
		'th'       => array_merge(
			apl_allowed_global_attributes(),
			array(
				'abbr'    => true,
				'align'   => true, // Deprecated.
				'axis'    => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'colspan' => true,
				'headers' => true,
				'rowspan' => true,
				'scope'   => true,
				'valign'  => true, // Deprecated.
				'width'   => true, // Deprecated.
			)
		),
		'thead'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'valign'  => true, // Deprecated.
			)
		),
		'tr'       => array_merge(
			apl_allowed_global_attributes(),
			array(
				'align'   => true, // Deprecated.
				'bgcolor' => true, // Deprecated.
				'char'    => true, // Deprecated.
				'charoff' => true, // Deprecated.
				'valign'  => true, // Deprecated.
			)
		),

		// Forms.
		'button'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'autofocus'      => true,
				'disabled'       => true,
				'form'           => true,
				'formaction'     => true,
				'formenctype'    => true,
				'formmethod'     => true,
				'formnovalidate' => true,
				'formtarget'     => true,
				'name'           => true,
				'type'           => true,
				'value'          => true,
			)
		),
		'datalist' => apl_allowed_global_attributes(),
		'fieldset' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'disabled' => true,
				'form'     => true,
				'name'     => true,
			)
		),
		'form'     => array_merge(
			apl_allowed_global_attributes(),
			array(
				'accept'         => true, // Deprecated.
				'accept-charset' => true,
				'action'         => true,
				'enctype'        => true,
				'method'         => true,
				'name'           => true,
				'novalidate'     => true,
				'target'         => true,
			)
		),
		'input'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'accept'         => true,
				'alt'            => true,
				'autocomplete'   => true,
				'autofocus'      => true,
				'capture'        => true,
				'checked'        => true,
				'dirname'        => true,
				'disabled'       => true,
				'form'           => true,
				'formaction'     => true,
				'formenctype'    => true,
				'formmethod'     => true,
				'formnovalidate' => true,
				'formtarget'     => true,
				'height'         => true,
				'list'           => true,
				'max'            => true,
				'maxlength'      => true,
				'min'            => true,
				'minlength'      => true,
				'multiple'       => true,
				'name'           => true,
				'pattern'        => true,
				'placeholder'    => true,
				'readonly'       => true,
				'required'       => true,
				'size'           => true,
				'src'            => true,
				'step'           => true,
				'type'           => true,
				'value'          => true,
				'width'          => true,
			)
		),
		'label'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'for'  => true,
				'form' => true, // Deprecated.
			)
		),
		'legend'   => apl_allowed_global_attributes(),
		'meter'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'form'    => true,
				'high'    => true,
				'low'     => true,
				'max'     => true,
				'min'     => true,
				'optimum' => true,
				'value'   => true,
			)
		),
		'optgroup' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'disabled' => true,
				'label' => true,
			)
		),
		'option'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'disabled' => true,
				'label'    => true,
				'selected' => true,
				'value'    => true,
			)
		),
		'output'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'for' => true,
			)
		),
		'progress' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'max'   => true,
				'value' => true,
			)
		),
		'select'   => array_merge(
			apl_allowed_global_attributes(),
			array(
				'autofocus' => true,
				'disabled'  => true,
				'form'      => true,
				'multiple'  => true,
				'name'      => true,
				'required'  => true,
				'size'      => true,
			)
		),
		/*
		 * (IMPORTANT) Keep disabled to prevent ending the textarea element.
		 *
		'textarea' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'autofocus'   => true,
				'cols'        => true,
				'disabled'    => true,
				'form'        => true,
				'maxlength'   => true,
				'minlength'   => true,
				'name'        => true,
				'placeholder' => true,
				'readonly'    => true,
				'required'    => true,
				'rows'        => true,
				'spellcheck'  => true,
				'wrap'        => true,
			)
		),
		*/

		// Interactive Elements.
		'details' => array_merge(
			apl_allowed_global_attributes(),
			array(
				'open' => true,
			)
		),
		'dialog'  => array_merge(
			apl_allowed_global_attributes(),
			array(
				'open' => true,
			)
		),
		'menu'    => array_merge(
			apl_allowed_global_attributes(),
			array(
				'label' => true,
				'type' => true,
			)
		),
		'summary' => apl_allowed_global_attributes(),

		// Web Components.
		'slot'     => array_merge(
			apl_allowed_global_attributes(),
			array(
				'name' => true,
			)
		),
		'template' => apl_allowed_global_attributes(),
	);
}

/**
 * Allowed Tags - Before
 *
 * @since 0.5.6.1
 *
 * @return array
 */
function apl_allowed_tags_before() {
	$allowed_tags = array_merge(
		apl_allowed_tags_default(),
		array(
			// Document metadata.
			'head'  => apl_allowed_global_attributes(),
			'link'  => array_merge(
				apl_allowed_global_attributes(),
				array(
					'as'             => true,
					'disabled'       => true,
					'href'           => true,
					'hreflang'       => true,
					'importance'     => true,
					'integrity'      => true,
					'media'          => true,
					'referrerpolicy' => true,
					'rel'            => true,
					'sizes'          => true,
					'title'          => true,
					'type'           => true,
				)
			),
			'meta'  => array(
				'content' => true,
				'name' => true,
			),
			'style' => array_merge(
				apl_allowed_global_attributes(),
				array(
					'type'  => true,
					'media' => true,
					'nonce' => true,
					'title' => true,
				)
			),
			'title' => apl_allowed_global_attributes(),

			// Sectioning root.
			'body' => array_merge(
				apl_allowed_global_attributes(),
				array(
					'onafterprint'     => true,
					'onbeforeprint'    => true,
					'onbeforeunload'   => true,
					'onblur'           => true,
					'onerror'          => true,
					'onfocus'          => true,
					'onhashchange'     => true,
					'onlanguagechange' => true,
					'onload'           => true,
					'onmessage'        => true,
					'onoffline'        => true,
					'ononline'         => true,
					'onpopstate'       => true,
					'onredo'           => true,
					'onresize'         => true,
					'onstorage'        => true,
					'onundo'           => true,
					'onunload'         => true,
				)
			),
		)
	);

	/**
	 * Allowed Tags - Before
	 *
	 * @since 0.5.6.1
	 *
	 * @param array $allowed_tags
	 */
	return apply_filters( 'apl_allowed_tags_before', $allowed_tags );
}

/**
 * Allowed Tags - Content
 *
 * @since 0.5.6.1
 *
 * @return array
 */
function apl_allowed_tags_content() {
	$allowed_tags = apl_allowed_tags_default();

	/**
	 * Allowed Tags - Content
	 *
	 * @since 0.5.6.1
	 *
	 * @param array $allowed_tags
	 */
	return apply_filters( 'apl_allowed_tags_content', $allowed_tags );
}

/**
 * Allowed Tags - After
 *
 * @since 0.5.6.1
 *
 * @return array
 */
function apl_allowed_tags_after() {
	$allowed_tags = array_merge(
		apl_allowed_tags_default(),
		array(
			// Sectioning root.
			'body' => array_merge(
				apl_allowed_global_attributes(),
				array(
					'onafterprint'     => true,
					'onbeforeprint'    => true,
					'onbeforeunload'   => true,
					'onblur'           => true,
					'onerror'          => true,
					'onfocus'          => true,
					'onhashchange'     => true,
					'onlanguagechange' => true,
					'onload'           => true,
					'onmessage'        => true,
					'onoffline'        => true,
					'ononline'         => true,
					'onpopstate'       => true,
					'onredo'           => true,
					'onresize'         => true,
					'onstorage'        => true,
					'onundo'           => true,
					'onunload'         => true,
				)
			),
		)
	);

	/**
	 * Allowed Tags - After
	 *
	 * @since 0.5.6.1
	 *
	 * @param array $allowed_tags
	 */
	return apply_filters( 'apl_allowed_tags_after', $allowed_tags );
}

