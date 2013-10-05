<?php

//TODO create a way to check for multiple categories
// and then unset if any of them are false
// create a ((repeating function))

/**
 * @version 0.1.0
 * @package APLCore
 */
class APLCore
{
  //Varibles
  //???MIGHT WANT TO ADD VERSION TO OPTIONS DB

  /**
   * @var string
   * @since 0.1.0
   */
  var $_error;

  /**
   * @since 0.1.0
   * @var string
   */
  var $_errorLog;

  /**
   * @since 0.1.0
   * @var string
   */
  var $plugin_basename;

  /**
   * @since 0.1.0
   * @var string
   */
  var $plugin_dir_path;
  /**
   * @since 0.1.0
   * @var string
   */

  /**
   *
   * @var type 
   * 
   */
  var $plugin_file_path;

  /**
   * @since 0.1.0
   * @var string
   */
  var $plugin_dir_url;

  /**
   * @since 0.1.0
   * @var string
   */
  var $plugin_file_url;

  /**
   * @since 0.1.0
   * @var string
   */
  var $_APL_OPTION_NAME = "APL_Options";

  /**
   * Name: APL Core Constructor
   * Desc: Sets up the core attributes to run ALPCore functions
   * @param string $file - contains data about the file itself 
   * return none
   * 
   * @since 0.1.0
   * @version 0.2.0
   * @filesource 
   * @uses $this->APL_load_plugin_data()
   * @uses $this->APL_options_load()
   * 
   * 1) Set plugin file data/properties
   * 2) Load main APL options
   * 3) Register activation, deactivation, and uninstall hooks 
   *     with WordPress
   * 4) Add Shortcode to WordPress action hooks
   * 5) If the current user has admin rights, do steps 6-7
   * 6) Add APL's menu to WordPress 'admin_menu' action hook
   * 7) Add APL's initial admin action hooks to WordPress 
   *     'admin_menu' action hook
   */
  public function __construct($file)
  {
    //STEP 1
    $this->APL_load_plugin_data($file);

    //STEP 2
    //////// DATABASE ///////
    $APLOptions = $this->APL_options_load();
    if (isset($APLOptions['version']))
    {
      if ($APLOptions['version'] == '1.0.1')
      {
        $APLOptions['version'] = APL_VERSION;
      }
      

      //UPGRADE VERSION
      $oldversion = $APLOptions['version'];
      if (version_compare(APL_VERSION, $oldversion, '>'))
      {
          //Put upgrade database functions in here. Not before.
          // Ex. upgrade_to_X.X.X();
          $APLOptions['version'] = APL_VERSION;
      }
      $this->APL_options_save($APLOptions);
    }
    //Else (Nothing) -Leave it to activation hook to install plugin option data
    
    //ACTION & FILTERS HOOKS
    //STEP 3
    register_activation_hook($this->plugin_file_path, array(&$this, 'APL_handler_activation'));
    register_deactivation_hook($this->plugin_file_path, array(&$this, 'APL_handler_deactivation'));
    register_uninstall_hook($this->plugin_file_path, array(&$this, 'APL_handler_uninstall'));



    //add_action('widgets_init', array($this, 'APL_handler_widget_init'));
    //STEP 4
    add_shortcode('post_list', array($this, 'APL_handler_shortcode'));
    //STEP 5
    if (is_admin())
    {
      //STEP 6
      add_action('admin_menu', array($this, 'APL_handler_admin_menu'));
      //STEP 7
      add_action('admin_init', array($this, 'APL_handler_admin_init'));
    }
  }

  /**
   * Name: APL load plugin file properties
   * Desc: saves all the file (dir/path) values for defining file
   *        structure/paths
   * @param string $plugin_path 
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Save the plugin location properties
   *     @example $this->plugin_basename = 'advanced-post-list/advanced-post-list.php'
   * 
   *     @example $this->plugin_dir_path = 'C:\xampp\htdocs\wordpress/wp-content/plugins/advanced-post-list/'
   * 
   *     @example $this->plugin_file_path = 'C:\xampp\htdocs\wordpress/wp-content/plugins/advanced-post-list/advanced-post-list.php'
   * 
   *     @example $this->plugin_dir_url = 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/'
   * 
   *     @example $this->plugin_file_url = 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/advanced-post-list.php/'
   * 
   */
  private function APL_load_plugin_data($plugin_path)
  {

    //STEP 1
    $this->plugin_basename = plugin_basename($plugin_path);
    $this->plugin_dir_path = trailingslashit(dirname(trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename));
    $this->plugin_file_path = trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename;
    $this->plugin_dir_url = trailingslashit(plugins_url(dirname($this->plugin_basename)));
    $this->plugin_file_url = trailingslashit(plugins_url($this->plugin_basename));
  }

  /**
   * Name: APL admin init action handler
   * Desc: Adds plugin action hooks to admin_init for loading up
   *        when the user has admin rights
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Add Ajax action hooks for APL Settings page
   * 
   */
  public function APL_handler_admin_init()
  {
    //STEP 1
    add_action('wp_ajax_APL_handler_save_preset', array($this, 'APL_handler_save_preset'));
    add_action('wp_ajax_APL_handler_delete_preset', array($this, 'APL_handler_delete_preset'));
    add_action('wp_ajax_APL_handler_restore_preset', array($this, 'APL_handler_restore_preset'));
    
    add_action('wp_ajax_APL_handler_export', array($this, 'APL_handler_export'));
    add_action('wp_ajax_APL_handler_import', array($this, 'APL_handler_import'));
    
    add_action('wp_ajax_APL_handler_save_settings', array($this, 'APL_handler_save_settings'));
  }

  /**
   * Name: APL install settings/options
   * Desc: Set plugin's initial saved settings and store it in Worpress
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $this->APL_options_set_to_default()
   * @uses $this->APL_options_save(stdClass)
   * 
   * 1) Set APL Options to default settings
   * 2) Save options
   * 
   */
  private function APL_install()
  {
    //STEP 1
    $APLOptions = $this->APL_options_set_to_default();
    //STEP 2
    $this->APL_options_save($APLOptions);
  }

  /**
   * Name: APL activation action handler
   * Desc: Handler all the activation methods when plugin is 
   *        activated in wordpress 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $this->APL_Options_load()
   * @uses $this->APL_install()
   * 
   * 1) Load APL Options, if no options are available (returns false)
   *     do step 2
   * 2) Install APL Options into WordPress
   * 
   */
  public function APL_handler_activation()
  {
    $APLOptions = $this->APL_options_load();
    if ($APLOptions === false)
    {
      $this->APL_install();
    }
  }

  /**
   * Name: APL deactivation action handler
   * Desc: Handles all the deactivation methods when plug in is deactivated
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $this->APL_Options_load()
   * 
   * 1) Load Options
   * 2) If user has delete database set to true, then delete All options
   * 
   */
  public function APL_handler_deactivation()
  {
    //STEP 1
    $APLOptions = $this->APL_options_load();
    //STEP 2
    if ($APLOptions['delete_core_db'] == 'true')
    {

      delete_option($this->_APL_OPTION_NAME);
      delete_option('APL_preset_db-default');
    }
  }

  /**
   * Name: APL uninstall action handler
   * Desc: Handles all the uninstall methods when plug in is uninstalled
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $this->APL_Options_load()
   * 
   * 1) Load APL Options
   * 2) If user has delete database set to true, then delete Core options
   * 
   * @todo set function to completely remove database since the user
   *        decided to delete the plugin anyways
   */
  public function APL_handler_uninstall()
  {
    //uninstall hook. Clear all traces of the Advanced Post List
    // database options.
    delete_option($this->_APL_OPTION_NAME);
    delete_option('APL_preset_db-default');
    //Alt uninstall that uses the 'delete upon deactivation' setting
//    if ($adminOptions['delete_core_db'] == 'true')
//    {
//      //remove all options for admin
//      delete_option($this->_APL_OPTION_NAME);
//      delete_option('APL_preset_db-default');
//    }
  }

  /**
   * Name: APL load core options
   * Desc: Gets core plugin database from from wordpress and sends the
   *        options back. If there is no options, wordpress returns
   *        a false value.
   * @param none
   * @return mixed 
   * 
   * @since 0.1.0
   * 
   * 1) Get APL Options from WordPress Database
   * 2) Return Option value
   * 
   */
  //FIX SET TO DEFAULT THEN OVERWRITE AND RETURN
  private function APL_options_load()
  {
    $APLOptions = get_option($this->_APL_OPTION_NAME);
    //FIX No Point...just return APLOptions
    if ($APLOptions !== false)
    {
      return $APLOptions;
    }
    else
    {
      return false;
    }
  }

  /**
   * Name: APL save APL Options
   * Desc: Overwrites/adds plugin options to WordPress database 
   * @param stdClass $APLOptions - Contains core database settings
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) If Options has data, then update(add) to wordpress database
   * 
   */
  private function APL_options_save($APLOptions)
  {
    //STEP 1
    if (isset($APLOptions))
    {
      update_option($this->_APL_OPTION_NAME, $APLOptions);
    }
  }

  /**
   * Name: APL remove APL options
   * Desc: Completely removes core options from wordpress database
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Delete options from wordpress database
   * 
   */
  private function APL_options_remove()
  {
    delete_option($this->_APL_OPTION_NAME);
  }

  /**
   * Name: APL set options to default
   * Desc: Sets the APL Options to its default values
   * @param none
   * @return stdClass $APLOptions
   * 
   * @since 0.1.0
   * 
   * 1) Save the version number
   * 2) Create and save a default list of preset postlist names
   * 3) Save delete_core_db as true
   * 4) Save a default error log
   * 5) return option settings
   * 
   */
  //simply returns all our default option values
  private function APL_options_set_to_default()
  {
    //TODO CREATE A FILE VERSION AND DB VERSION TO CHECK ON
    $APLOptions = array();
    //STEP 1
    $APLOptions['version'] = APL_VERSION;
    //STEP 2
    $APLOptions['preset_db_names'] = array(0 => 'default');
    //STEP 3
    $APLOptions['delete_core_db'] = true;
    //STEP 4
    $APLOptions['error'] = '';

    //STEP 5
    return $APLOptions;
  }

//  function APL_get_admin_options()
//  {
//
//    $APLOptions = $this->APL_options_set_to_default();
//
//    $tmpOptions = get_option($this->_APL_OPTION_NAME);
//
//    if (!empty($tmpOptions))
//    {
//      foreach ($tmpOptions as $key => $option)
//      {
//        $APLOptions[$key] = $option;
//      }
//    }
//    
//    update_option($this->_APL_OPTION_NAME, $APLOptions);
//
//    return $APLOptions;
//  }
  /**
   * Name: APL widget initial action handler
   * Desc: Adds the plugins widget to wordpress
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Register APL's widget class name
   * 
   */
  public function APL_handler_widget_init()
  {
    //$widget = new APLWidget();
    //register_widget($widget);
    register_widget('APLWidget');
  }

  /**
   * Name: APL admin menu action handler
   * Desc: Adds the plugin's menu links to the wordpress admin page
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @name $APLPost_hook
   * 
   * 1) Add a submenu to Wordpress settings menu
   * 2) Add action scripts to that menu page
   * 
   * @todo create APL's own menu once addition pages are available
   * 
   */
  public function APL_handler_admin_menu()
  {
    global $APLPost_hook;
    //STEP 1
    $APLPost_hook = add_submenu_page('options-general.php', "Advanced Post List", "Advanced Post List", 'manage_options', "advanced-post-list", array($this, 'APL_admin_page'));
    //STEP 2
    add_action("admin_print_scripts-$APLPost_hook", array($this, 'APL_admin_head'));
    //add_filter('contextual_help', 'kalinsPost_contextual_help', 10, 3);
  }

  /**
   * Name: APL admin head action handler
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Add JQuery to wordpress enqueue scripts
   * 
   */
  public function APL_admin_head()
  {
    //STEP 1
    wp_enqueue_script("jquery");
  }

  /**
   * Name: APL admin settings page
   * Desc: Adds the create page content
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Add Settings page contents
   * 
   */
  //FIX set the directory as dynamic
  //
  //REPORTED
  //Fatal error: require_once() [function.require]: Failed opening
  //required 'C:\wamp\www\wordpress/wp-content/plugins/advanced-post-list/APL_admin.php'
  //(include_path='.;C:\php5\pear') in
  //C:\wamp\www\wordpress\wp-content\plugins\advance-post-list\includes\Class\APLCore.php
  //on line 194
  public function APL_admin_page()
  {
    require_once( $this->plugin_dir_path . 'APL_admin.php');
  }

  /**
   * Name: 
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) 
   * 
   */
  //TODO CREATE AN AJAX FUNCTION TO IMPORT DATA TO THE PLUGIN
  // COULDN'T FIND A WAY TO CARRY THE $_FILES GLOBAL VARIBLE
  // THROUGH .post TO TARGET PHP CODE
  public function APL_handler_import()
  {
    check_ajax_referer('APL_handler_import');
    
    echo json_encode('');
  }

  /**
   * Name: 
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) 
   * 
   */
  public function APL_handler_export()
  {
    $check_ajax_referer = check_ajax_referer("APL_handler_export");
    
    $ajax_nonce = $_GET['_ajax_nonce'];
    $filename = $_GET['filename'];
    
    $rtnData = new stdClass();
    $rtnData->_ajax_nonce = $_GET['_ajax_nonce'];
    $rtnData->action - $_GET['action'];
    $rtnData->filename = $_GET['filename'];
    $rtnData->_error = '';
    
    echo json_encode($rtnData);
  }
  /**
   * <p>Desc: </p>
   * <ol>
   *    <li>one</li>
   *    <li>two</li>
   *    <li>three</li>
   * </ol>
   * @since 0.2.0
   */
  public function APL_handler_save_settings()
  {
    
    $check_ajax_referer0 = check_ajax_referer("APL_handler_save_settings");
    $dataRtn = new stdClass();
    $dataRtn->error = '';
    
    $APLOptions = $this->APL_options_load();
    $APLOptions['delete_core_db'] = $_POST['deleteDb'];
    
    $this->APL_options_save($APLOptions);
    
    echo json_encode($dataRtn);
  }
  

  /**
   * Name: APL save preset ajax action handler
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $this->APL_options_load()
   * @uses $preset_db->options_save_db()
   * @uses $this->APL_run($preset_name)
   * 
   * 1) Grab the javascript ajax reference
   * 2) Get preset options
   * 3) create a temp varible and save all the values from
   *     the settings page
   * 4) Add temp varible to current preset varible
   * 5) Save current preset varible to plugin options
   * 6) Save delete_core_db value to plugin core options
   * 7) Create a output
   * 8) Store APL_run return value to $output
   * 9) echo $output
   * 
   */
  public function APL_handler_save_preset()
  {
    
    //STEP 1
    check_ajax_referer("APL_handler_save_preset");

    $APLOptions = $this->APL_options_load();
    /*
      //MULTI PRESET OPTIONS


      foreach ($APLOptions['preset_db_names'] as $key => $value)
      {
      $preset_db[$key] = $value;
      }
     */
    //DEFAULT USE
    //STEP 2
    $preset_db = new APLPresetDbObj('default');

    //STEP 3
    $outputVar = new stdClass();
    $valArr = $preset_db->_preset_db;
    $preset_name = stripslashes($_POST['preset_name']);

    $valObj = new APLPresetObj();

    $valObj->_before = stripslashes($_POST['before']);
    $valObj->_content = stripslashes($_POST['content']);
    $valObj->_after = stripslashes($_POST['after']);

    //Holds all the category & tag data that is used for filtering
    $valObj->_catsSelected = $_POST['categories']; //All//(int) array
    $valObj->_tagsSelected = $_POST['tags']; //All
    $valObj->_catsInclude = $_POST['includeCats']; //Boolean Unchecked
    $valObj->_tagsInclude = $_POST['includeTags']; //Boolean Unchecked
    $valObj->_catsRequired = $_POST['requireAllCats']; //Boolean Unchecked
    $valObj->_tagsRequired = $_POST['requireAllTags']; //Boolean Unchecked
    //Settings that will be used for how this is displayed in the list
    $valObj->_listOrder = $_POST['order']; //Desc

    $valObj->_listOrderBy = $_POST['orderby']; //(string) Type
    $valObj->_listAmount = $_POST['numberposts']; //(int) howmany to display
    //Post attributes
    $valObj->_postType = $_POST['post_type'];
    $valObj->_postParent = $_POST['post_parent'];

    //leave out the current page/post that the plugin is displaying on.
    $valObj->_postExcludeCurrent = $_POST['excludeCurrent'];

    $valArr->$preset_name = $valObj;

    //STEP 4
    $preset_db->_preset_db = $valArr;
    //STEP 5
    $preset_db->options_save_db();
    //STEP 6
    //$APLOptions['delete_core_db'] = $_POST['doCleanup'];
    $this->APL_options_save($APLOptions);

    //STEP 7
    $outputVar->status = "success";
    $outputVar->preset_arr = $preset_db->_preset_db;
    //preview saved data
    //STEP 8
    $outputVar->previewOutput = $this->APL_run($preset_name);

    //STEP 9
    echo json_encode($outputVar);
  }

  /**
   * Name: APL delete preset ajax action handler
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * @uses $presetDbObj->options_save_db()
   * 
   * 1) Grab the javascript ajax reference
   * 2) Get preset options
   * 3) Get postname from page
   * 4) Delete (unset) preset from preset database varible
   * 5) Save preset database
   * 6) echo preset database
   * 
   */
  public function APL_handler_delete_preset()
  {

    check_ajax_referer("APL_handler_delete_preset");

    $presetDbObj = new APLPresetDbObj('default');

    $preset_name = stripslashes($_POST['preset_name']);
    unset($presetDbObj->_preset_db->$preset_name);

    $presetDbObj->options_save_db();
    echo json_encode($presetDbObj->_preset_db);
  }

  /**
   * Name: APL restore preset ajax action handler
   * Desc: 
   * @param none
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) Grab the javascript ajax reference
   * 2) Get preset options for a temp and a current varible
   * 3) Set temp to default preset_database_object
   * 4) Add default presets to current preset_database_object
   * 5) Save current preset database varible
   * 6) echo preset values
   *  
   * 
   */
  public function APL_handler_restore_preset()
  {

    //STEP 1
    check_ajax_referer("APL_handler_restore_preset");

    //STEP 2
    $presetDbObj = new APLPresetDbObj('default');
    $tmpDbObj = new APLPresetDbObj('default');

    //STEP 3
    $tmpDbObj->set_to_defaults();

    //STEP 4
    foreach ($tmpDbObj->_preset_db as $key => $value)
    {
      $presetDbObj->_preset_db->$key = $value;
    }

    //STEP 5
    $presetDbObj->options_save_db();

    //STEP 6
    echo json_encode($presetDbObj->_preset_db);
  }

  /**
   * Name: APL shortcode action handler
   * Desc: 
   * @param mixed $att
   * @return string
   * 
   * @since 0.1.0
   * 
   * 1) If a value is set, do step 2 and skip 3
   * 2) return $this->display($preset_name)
   * 3) otherwise return an empty string
   * 
   */
  public function APL_handler_shortcode($att)
  {
    //STEP 1
    if (isset($att['name']))
    {
      //STEP 2
      //return $this->APL_run($att['name']);
      return $this->APL_display($att['name']);
    }
    else
    {
      //STEP 3
      return '';
    }
  }

  /**
   * Name: APL display (run)
   * Desc: 
   * @param string $preset_name
   * @return string
   * 
   * @since 0.1.0
   * 
   * 1) return APL_run()
   * 
   */
  public function APL_display($preset_name)
  {
    return $this->APL_run($preset_name);
  }

  /**
   * Name: APL run
   * Desc: 
   * @param string $preset_name
   * @return string
   * 
   * @since 0.1.0
   * @tutorial
   * 1) Get preset database object
   * @see 2) Set pre-filters
   * 3) Get website's post data
   * 4) If required categories is selected, check for posts that match all
   *     selected categories
   * 5) If required tags is selected, check for posts that match all
   *     selected tags
   * 6) If no posts are remaining, return an empty string
   * 7) Setup output string with before, content,  and after.
   * 8) Add each post data according to shortcode
   * 9) Output create string
   * 
   */
  private function APL_run($preset_name)
  {
    ////////////////////////////////////////////////////////////////////////////
    ////// SETUP CURRENT PRESET ARRAY //////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //STEP 1
    //Get Options to get option database name
    //TODO - for now lets just pass default.
    //$APLOptions = $this->APL_options_load();
    $OptionDb = new APLPresetDbObj('default');
    //$OptionDb->option_load_db();
    //$presetObj = json_decode($presetObj);
    //Check if there is a valid Option Database to pull preset data
    if (isset($OptionDb))
    {
      $presetObj = $OptionDb->_preset_db->$preset_name;
    }
    else
    {
      if (current_user_can('manage_options'))
      {
        //Alert Message for admins in case the wrong preset data was used
        return '<p>Admin Alert - A problem has occured. A non-existent preset name has been passed use.</p>';
      }
      else
      {
        //Users/Visitors won't be able to see the post list if
        // the name isn't set right
        return'';
      }
    }



    ////////////////////////////////////////////////////////////////////////////
    ////// POST/PAGE FILTER SETTINGS ///////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //STEP 2
    /*
      What does this do???
      //if we're not showing a list of anything, only show the content,
      // ignore everything else, and apply the shortcodes to the page
      // being currently viewed
      if ($newVals->post_type == "none")
      {
      $output = APLInternalShortcodeReplace($newVals->content, $post, 0);
      }
     */
    //get current page data that the post-list is displaying on
    global $post;
    if ($presetObj->_postExcludeCurrent == 'true')
    {
      $excludeList = $post->ID;
    }
    //TODO
    //$presetObj->before = APLInternalShortcodeReplace($presetObj->before, $post, 0);
    //$presetObj->after = APLInternalShortcodeReplace($presetObj->after, $post, 0);
    
    ////// ADD SOME DATA FROM THE POST/PAGE IF NEEDED
    //Include other categories from current post that the 
    // post list is displayed on
    $tmpCatsSelected = $presetObj->_catsSelected;
    if ($presetObj->_catsInclude == "true")
    {

      $post_categories = wp_get_post_categories($post->ID);
      foreach ($post_categories as $value)
      {
        $tmpCatsSelected = $tmpCatsSelected . $value . ",";
      }
    }

    $tmpTagsSelected = $presetObj->_tagsSelected;
    if ($presetObj->_tagsInclude == "true")
    {
      //FIX
      $post_tags = wp_get_post_tags($post->ID);
      foreach ($post_tags as $arr)
      {
        $tmpTagsSelected = $tmpTagsSelected . $arr->slug . ",";
      }
    }

    //SET THE POST LIST PARENT IF NEEDED
    if (!isset($presetObj->_postParent))
    {
      $presetObj->_postParent = "None";
    }
    else
    {
      if ($presetObj->_postParent == "current")
      {
        $presetObj->_postParent = $post->ID;
      }
    }


    ////////////////////////////////////////////////////////////////////////////
    ////// GET POST DATA ///////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //STEP 3
    //TODO Change $posts; varible to $postData
    //Questionable, may be pointless
    //FIX ? 'numberposts=' . -1 . to 'numberposts=-1'.
    $posts = get_posts('numberposts=' . -1 .
        '&category=' . $tmpCatsSelected .
        '&post_type=' . $presetObj->_postType .
        '&tag=' . $tmpTagsSelected .
        '&orderby=' . $presetObj->_listOrderBy .
        '&order=' . $presetObj->_listOrder .
        '&exclude=' . $excludeList .
        '&post_parent=' . $presetObj->_postParent);

    ////////////////////////////////////////////////////////////////////////////
    ////// PRESET OPTIONS FILTER SETTINGS //////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //TEST ???
    //$posts = query_posts("orderby=tax_query");
    //
////// Categories //////////////////////////////////////////////////////////////
    //STEP 4
    //if every post must lie in every selected category
    if ($presetObj->_catsRequired == "true")
    {
      //Create array from list of categories
      //Delete the last array which has no value (null)
      $requiredCats = explode(",", $presetObj->_catsSelected);
      $i = sizeof($requiredCats);
      $i -= 1;
      unset($requiredCats[$i]);

      foreach ($posts as $key1 => $page)
      {
        $match = (int) 0;
        $pageCats = wp_get_post_categories($page->ID);
        //If not all the numbers matched require cats then unset
        foreach ($pageCats as $key2 => $PCvalue)
        {
          foreach ($requiredCats as $key3 => $RCvalue)
          {
            if ($PCvalue === $RCvalue)
            {
              $match++;
            }
          }
        }


        if ($match != (int) ($key3 + 1))
        {
          unset($posts[$key1]);
        }
      }
    }
////// Tags ////////////////////////////////////////////////////////////////////
    //STEP 5
    if ($presetObj->_tagsRequired == "true")
    {
      //Set the requiredTags array
      //Delete the last array which has no value (null)
      $requiredTags = explode(",", $presetObj->_tagsSelected);
      $i = sizeof($requiredTags);
      $i -= 1;
      unset($requiredTags[$i]);
      //For some reason this didn't work
      //unset( $requiredCats[sizeof($requiredCats - 1)] );

      foreach ($posts as $key1 => $page)
      {
        $match = (int) 0;
        $pageTags = "";
        //Tags came as an array of objects instead of ID values, so we loop to 
        // create our searchable string, which for tags is based on slugs 
        // instead of IDs
        $tmpPageTags = wp_get_post_tags($page->ID);
        foreach ($tmpPageTags as $tag)
        {
          $pageTags .= $tag->slug . ",";
        }

        //Set the pageTags array
        //Delete the last array which has no value (null)
        $pageTags = explode(",", $pageTags);
        $i = sizeof($pageTags);
        $i -= 1;
        unset($pageTags[$i]);

        foreach ($pageTags as $key2 => $PTvalue)
        {
          foreach ($requiredTags as $key3 => $RTvalue)
          {
            //Note - strcmp($str1, $str2) is case sensitive. Probably won't
            //        matter in this situation though.
            if (strcmp($PTvalue, $RTvalue) == 0)
            {
              $match++;
            }
          }
        }


        if ($match != (int) ($key3 + 1))
        {
          unset($posts[$key1]);
        }
      }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////// PREPARE OUTPUT STRING ///////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //Shorten the posts array so that it can be used to output x number of posts
    $posts = array_slice($posts, 0, $presetObj->_listAmount);
    
    //STEP 6
    //TODO create a custom message for the dev to use
    //return nothing if no results
    if (count($posts) == 0)
    {
      return "";
    }
    //STEP 7
////// BEFORE //////////////////////////////////////////////////////////////////
    $output = $presetObj->_before;

///// CONTENT //////////////////////////////////////////////////////////////////
    $count = 0;
    foreach ($posts as $page)
    {
      //STEP 8
      $output = $output . APLInternalShortcodeReplace($presetObj->_content, $page, $count);
      $count = $count + 1;
    }

    $finalPos = strrpos($output, "[final_end]");
    //if ending exists (the last item where we don't want to add any 
    // more commas or ending brackets or whatever)
    if ($finalPos > 0)
    {
      //Cut everything off at the final position of {final_end}
      $output = substr($output, 0, $finalPos);
      //Replace all the other instances of {final_end}, 
      // since we only care about the last one
      $output = str_replace("[final_end]", "", $output);
    }

////// AFTER ///////////////////////////////////////////////////////////////////
    $output = $output . $presetObj->_after;

    //STEP 9
    return $output;
  }

}

/**
 * Name: APL Internal Shortcodes
 * Desc: 
 * @param none
 * @return string
 * 
 * @since 0.1.0
 * 
 * 1) 
 * 
 */
function APLInternalShortcodeReplace($str, $page, $count)
{
  //not much left of this array, since there's so little post data that I can still just grab unmodified
  $SCList = array("[ID]", "[post_name]", "[guid]", "[post_content]", "[comment_count]");

  $l = count($SCList);
  for ($i = 0; $i < $l; $i++)
  {//loop through all possible shortcodes
    $scName = substr($SCList[$i], 1, count($SCList[$i]) - 2);
    $str = str_replace($SCList[$i], $page->$scName, $str);
  }

  $str = str_replace("[post_author]", get_userdata($page->post_author)->user_login, $str); //post_author requires an extra function call to convert the userID into a name so we can't do it in the loop above
  $str = str_replace("[post_permalink]", get_permalink($page->ID), $str);
  $str = str_replace("[post_title]", htmlspecialchars($page->post_title), $str);

  $postCallback = new APLCallback();
  $postCallback->itemCount = $count;
  $postCallback->page = $page;

  $str = preg_replace_callback('#\[ *item_number *(offset=[\'|\"]([^\'\"]*)[\'|\"])? *(increment=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postCountCallback'), $str);

////// POST DATA & MODIFIED ////////////////////////////////////////////////////
  $postCallback->curDate = $page->post_date; //change the curDate param and run the regex replace for each type of date/time shortcode
  $str = preg_replace_callback('#\[ *post_date *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
  $postCallback->curDate = $page->post_date_gmt;
  $str = preg_replace_callback('#\[ *post_date_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
  $postCallback->curDate = $page->post_modified;
  $str = preg_replace_callback('#\[ *post_modified *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);
  $postCallback->curDate = $page->post_modified_gmt;
  $str = preg_replace_callback('#\[ *post_modified_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postDateCallback'), $str);

  if (preg_match('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', $str))
  {

    $str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);

    /* if($page->post_excerpt == ""){//if there's no excerpt applied to the post, extract one
      //$postCallback->pageContent = strip_tags($page->post_content);
      $str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
      }else{//if there is a post excerpt just use it and don't generate our own
      $str = preg_replace('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', $page->post_excerpt, $str);
      } */
  }

  $postCallback->post_type = $page->post_type;
  $postCallback->post_id = $page->ID;
  $str = preg_replace_callback('#\[ *post_pdf *\]#', array(&$postCallback, 'postPDFCallback'), $str);

  if (current_theme_supports('post-thumbnails'))
  {
    $arr = wp_get_attachment_image_src(get_post_thumbnail_id($page->ID), 'single-post-thumbnail');
    $str = str_replace("[post_thumb]", $arr[0], $str);
  }

  $postCallback->page = $page;
  $str = preg_replace_callback('#\[ *post_meta *(name=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postMetaCallback'), $str);
  $str = preg_replace_callback('#\[ *post_categories *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postCategoriesCallback'), $str);
  $str = preg_replace_callback('#\[ *post_tags *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postTagsCallback'), $str);
  $str = preg_replace_callback('#\[ *post_comments *(before=[\'|\"]([^\'\"]*)[\'|\"])? *(after=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'commentCallback'), $str);
  $str = preg_replace_callback('#\[ *post_parent *(link=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postParentCallback'), $str);

  $str = preg_replace_callback('#\[ *php_function *(name=[\'|\"]([^\'\"]*)[\'|\"])? *(param=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'functionCallback'), $str);

  return $str;
}

?>
