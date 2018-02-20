<?php
/**
Plugin Name: Advanced Post List
Plugin URI: http://wordpress.org/plugins/advanced-post-list/
Description: Create highly customizable post lists to display to your users and visitors. Provides a wide array of static settings and dynamic features. Also supports Custom Post Types and Taxonomies.
Version: 0.4.3.1
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

/* **** Core Singleton Class **** */
require_once plugin_dir_path( __FILE__ ) . 'includes/class/class-apl-core.php';
$advanced_post_list = new APL_Core( __FILE__ );

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
