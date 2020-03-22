<?php
/**
Plugin Name: Advanced Post List
Plugin URI: http://wordpress.org/plugins/advanced-post-list/
Description: Create highly customizable post lists to display to your users and visitors. Provides a wide array of static settings and dynamic features. Also supports Custom Post Types and Taxonomies.
Version: 0.5.6.1
Author: EkoJR
Author URI: http://ekojr.com
License: GPLv2
License: URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: advanced-post-list
Domain Path: /languages

== Copyright ==
Advanced Post List by EkoJR (email: mail@advancedpostlist.com)
Copyright (C) 2017 EkoJR

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/* **** COMPATABILITY CHECKS **** */
global $wp_version;

if ( isset( $wp_version ) ) {
	if ( version_compare( $wp_version, '4.5', '<' ) ) {
		$error_msg  = '';
		$error_msg .= esc_html__( 'This plugin requires WordPress 4.5 or higher to operate. ', 'advanced-post-list' );
		$error_msg .= esc_html__( '<a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>', 'advanced-post-list' );
		wp_die( esc_html( $error_msg ), esc_html__( 'Advanced Post List: Error', 'advanced-post-list' ) );
	}
} else {
	$error_msg = __( 'You are attempting to access this plugin directly.', 'advanced-post-list' );
	wp_die( esc_html( $error_msg ), esc_html__( 'Advanced Post List: Error', 'advanced-post-list' ) );
}

/* **** DEFINE CONSTANTS **** */
if (
		! defined( 'APL_VERSION' )
) {
	// PHP < 5.2 compatibility for __DIR__.
	// Avoid using `plugin_basename()` with situations that don't store the plugin directory in `WP_PLUGIN_DIR`; ex. unit testing with Travis CI.
	$directory        = dirname( __FILE__ );
	$root_dir         = wp_normalize_path( str_replace( basename( $directory ), '', $directory ) );
	$plugin_basename  = wp_normalize_path( str_replace( str_replace( basename( $directory ), '', $directory ), '', __FILE__ ) );
	$plugin_dir       = $root_dir . $plugin_basename;
	/*
	 * Get plugin-file-data from advanced-post-list.php, and grab
	 * the plugin's meta default_headers.
	 *
	 * @see get_file_data()
	 * @link https://hitchhackerguide.com/2011/02/12/get_plugin_data/
	 */
	$default_headers = array(
		'Name'       => 'Plugin Name',
		'Slug'       => 'Text Domain',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Version'    => 'Version',
	);
	$plugin_data = get_file_data( __FILE__, $default_headers );

	/**
	 * Plugin Basename.
	 *
	 * @since 0.5.6
	 *
	 * @var string APL_PLUGIN_BASENAME Plugin basename on WP platform. Eg. 'advanced-post-list/advanced-post-list.php`.
	 */
	define( 'APL_PLUGIN_BASENAME', $plugin_basename );

	/**
	 * Version Number.
	 *
	 * @since 0.1.0
	 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
	 *                APL_Core::_define_constants().
	 * @var string $APL_VERSION Ex. '1.2.3'.
	 */
	define( 'APL_VERSION', $plugin_data['Version'] );

	/**
	 * APL Display Name.
	 *
	 * @since 0.1.0
	 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
	 *                APL_Core::_define_constants().
	 * @var string $APL_NAME Contains 'Advanced Post List'.
	 */
	define( 'APL_NAME', $plugin_data['Name'] );

	/**
	 * APL Slug.
	 *
	 * @deprecated 0.5.6 Use `APL_TEXTDOMAIN` constant.
	 *
	 * @since 0.3.2
	 *
	 * @var string $APL_SLUG Contains 'advanced-post-list'.
	 */
	define( 'APL_SLUG', $plugin_data['Slug'] );

	/**
	 * APL Text Domain.
	 *
	 * @since 0.5.6
	 * @var string $APL_TEXTDOMAIN Contains 'advanced-post-list'.
	 */
	define( 'APL_TEXTDOMAIN', $plugin_data['TextDomain'] );

	if ( ! defined( 'APL_DIR' ) ) {
		/**
		 * Directory Path.
		 *
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string $APL_DIR Contains 'C:\xampp\htdocs\wordpress\wp-content\plugins\advanced-post-list/'.
		 */
		define( 'APL_DIR', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'APL_URL' ) ) {
		/**
		 * URL Location.
		 *
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string $APL_URL Contains 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/'.
		 */
		define( 'APL_URL', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'APL_DOMAIN_PATH' ) ) {

		/**
		 * Plugin's Text Domain Path
		 *
		 * @since 1.0.0
		 *
		 * @var string $APL_DOMAIN_PATH Directory for storing languages.
		 */
		define( 'APL_DOMAIN_PATH', $plugin_data['DomainPath'] );
	}

	if ( ! defined( 'APL_TEMPLATE_DEBUG_MODE' ) ) {
		/**
		 * APL Template Debug
		 *
		 * @since 0.4.4.1
		 * @var boolean $APL_TEMPLATE_DEBUG_MODE Used for bypassing child theme customizations when debugging.
		 */
		define( 'APL_TEMPLATE_DEBUG_MODE', false );
	}
}

/* **** Core Singleton Class **** */
require_once plugin_dir_path( __FILE__ ) . 'class-apl-core.php';
global $apl_core;

if ( is_null( $apl_core ) ) {
	$apl_core = new APL_Core();
}

// A LIST DEBUGGIN METHODS THAT USERS MAY USE OR BE
// REFERRED TO DURING ANY POSSIBLE TROUBLESHOOTING
// ISSUES THAT MAY OCCUR.
/******************************************************
 *                                       |            *
 *                                       |.===.       *
 *                                       {}o o{}      *
 * .----------------------------------ooO--(_)--Ooo-. *
 * |                                                | *
 * |  ___      _                _____        _      | *
 * | |   \ ___| |__ _  _ __ _  |_   ____ ___| |___  | *
 * | | |) / -_| '_ | || / _` |   | |/ _ / _ | (_-<  | *
 * | |___/\___|_.__/\_,_\__, |   |_|\___\___|_/__/  | *
 * |                    |___/                       | *
 * | DESCRIPTIONS                                   | *
 * |   #1 - Var(iable)_Dump                         | *
 * |   #2 - Callstack                               | *
 * |________________________________________________| *
 *                                                    *
 ******************************************************/
// Tool #1.
//var_dump($example_variable);
//
// Tool #2.
//$e = new Exception;
//var_dump($e->getTraceAsString());
