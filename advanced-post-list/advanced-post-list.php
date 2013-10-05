<?php

/*
  Plugin Name: Advanced Post List
  Version: 0.1.a1
  Plugin URI: http://code.google.com/p/wordpress-advanced-post-list/
  Description: Creates a shortcode, widget, or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.
  Author: JoKeR
  Author URI: http://someProfilePage.com/

  == Copyright ==
  Advanced Post List by JoKeR (email: jokerbr313@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/*****************************************************************************/
/************************ Compatability Checks *******************************/
/*****************************************************************************/
//Check wordpress version and if it exists (called directly)
global $wp_version;

if ( isset($wp_version) )
{
  if (version_compare($wp_version, "3.0", "<"))
  {
    $exit_msg = "Error 02 - This plugin requires Wordpress 3.0 or higher to operate. <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>";
    exit($exit_msg);
  }
}
else
{
  $exit_msg = "Error 01 - You are attempting to access this plugin directly.";
  exit($exit_msg);
  echo "Error 01 - You are attempting to access this plugin directly.";
}

/*****************************************************************************/
/************************ CONSTANTS ******************************************/
/*****************************************************************************/
//Define constant varibles
DEFINE('APL_NAME', 'Advanced Post List');
define('APL_DIR', plugin_dir_path(__FILE__));
define('APL_URL', plugin_dir_url(__FILE__));
define('APL_VERSION', '0.1.a1');

/*****************************************************************************/
/************************ REQUIRED FILES *************************************/
/*****************************************************************************/
//Include other files
require('/includes/Class/APLCore.php');
require('/includes/Class/APLPresetDbObj.php');
require('/includes/Class/APLPresetObj.php');
require('/includes/Class/APLCallback.php');
//require(APL_DIRECTORY . 'admin.php');
//require(APL_DIRECTORY . 'admin.php');
//require(APL_DIRECTORY . 'admin.php');


/*****************************************************************************/
/************************ LOAD HANDLER ***************************************/
/*****************************************************************************/
//Load Handler
$advanced_post_list = new APLCore(__FILE__);

/*if (!class_exists('APLCore'))
{
  $advanced_post_list = new APLCore(__FILE__);
}*/
?>
