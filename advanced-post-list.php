<?php

/*
  Plugin Name: Advanced Post List
  Plugin URI: http://wordpress.org/plugins/advanced-post-list/
  Description: Create highly customizable post lists to display to your users and visitors. Provides a wide array of static settings and dynamic features. Also supports Custom Post Types and Taxonomies.
  Version: 0.4.0 
  Author: EkoJR
  Author URI: http://ekojr.com
  License: GPLv2
  License: URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: advanced-post-list
  Domain Path: /languages

  == Copyright ==
  Advanced Post List by EkoJR (email: ekojr1337@gmail.com)
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
/**
 * @package advanced-post-list
 * @since 0.1.0
 * @version 0.2.0 - Added more require functions for additional pages.
 * @version 0.3.0 - Added APLQuery Class, APLUpdater Class
 * 
 */
/*****************************************************************************/
/************************ Compatability Checks *******************************/
/*****************************************************************************/
//Check wordpress version and if it exists (called directly)
global $wp_version;

if ( isset($wp_version) )
{
  if (version_compare($wp_version, "2.0.2", "<"))
  {
    $exit_msg = "This plugin requires Wordpress 2.0.2 or higher to operate. <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>";
    exit($exit_msg);
  }
}
else
{
  $exit_msg = "You are attempting to access this plugin directly.";
  exit($exit_msg);
  echo "You are attempting to access this plugin directly.";
}


/*****************************************************************************/
/************************ Singleton ******************************************/
/*****************************************************************************/

//Load Handler
//TODO change to APLCore->Run
//  Basically attempt to remove the need for a variable.
require_once(plugin_dir_path(__FILE__) . 'includes/class/apl-core.php');
$advanced_post_list = new APLCore(__FILE__);

// A LIST DEBUGGIN METHODS THAT USERS MAY USE OR BE 
//   REFERRED TO DURING ANY POSSIBLE TROUBLESHOOTING 
//   ISSUES THAT MAY OCCURE
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
 * |   #1 - Var(iable)_Dump
 * |   #2 - Callstack                               | *
 * |________________________________________________| *
 *                                                    *
 ******************************************************/
//Tool #1
//var_dump($example_variable);
//
//Tool #2
//$e = new Exception;
//var_dump($e->getTraceAsString());

?>
