<?php

//TODO create a way to check for multiple categories
// and then unset if any of them are false
// create a ((repeating function))

class APLCore
{

  //Varibles
  //???MIGHT WANT TO ADD VERSION TO OPTIONS DB
  var $_error;
  var $_errorLog;
  var $plugin_basename;
  var $plugin_dir_path;
  var $plugin_file_path;
  var $plugin_dir_url;
  var $plugin_file_url;
  var $_APL_OPTION_NAME = "APL_Options";

  function __construct($file)
  {
    $this->APL_load_plugin_data($file);
    //$APLOptions = array();
    $APLOptions = $this->APL_options_load();
    
    //$APLOptions = get_options(array($this->_APL_OPTION_NAME));
    
    //UPGRADE CODE FOR MODIFICATIONS
    
    //VERSION CHECKING
    
    //ACTION & FILTERS HOOKS
    //For some reason, it requires the base file with new APLCore()
    register_activation_hook($this->plugin_file_path, array(&$this, 'APL_activation_handler'));
    register_deactivation_hook($this->plugin_file_path, array(&$this, 'APL_deactivation_handler'));
    register_uninstall_hook($this->plugin_file_path, array(&$this, 'APL_uninstall_handler'));

    

    add_action('widget_init', array($this, 'APL_widget_init_handler'));
    add_shortcode('post_list', array($this, 'APL_shortcode_handler'));
    add_action('wp_ajax_APL_save_preset_handler', array($this, 'APL_save_preset_handler'));
    add_action('wp_ajax_APL_delete_preset_handler', array($this, 'APL_delete_preset_handler')); 
    add_action('wp_ajax_APL_restore_preset_handler', array($this, 'APL_restore_preset_handler'));
    if (is_admin())
    {

      add_action('admin_init', array($this, 'APL_admin_init_handler'));
      add_action('admin_menu', array($this, 'APL_admin_menu_handler'));
    }
    
  }

  function APL_load_plugin_data($plugin_path)
  {

    //Load plugin path/URL information
    $this->plugin_basename = plugin_basename($plugin_path);
    $this->plugin_dir_path = trailingslashit(dirname(trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename));
    $this->plugin_file_path = trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename;
    $this->plugin_dir_url = trailingslashit(plugins_url(dirname($this->plugin_basename)));
    $this->plugin_file_url = trailingslashit(plugins_url($this->plugin_basename));
    
    //??? Create Core values
//    $this->core_basename = plugin_basename($plugin_path);
//    $this->plugin_core_dir_path = trailingslashit(dirname(trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename));
//    $this->plugin_file_path = trailingslashit(WP_PLUGIN_DIR) . $this->plugin_basename;
//    $this->plugin_dir_url = trailingslashit(plugins_url(dirname($this->plugin_basename)));
//    $this->plugin_file_url = trailingslashit(plugins_url($this->plugin_basename));
  }

  function APL_install()
  {
    $APLOptions = $this->APL_options_set_to_default();
    $this->APL_options_save($APLOptions);
    //add_option(array($this->_APL_OPTION_NAME), $APLOptions);
  }
  function APL_activation_handler()
  {
    //$APLOptions = get_option($this->_APL_OPTION_NAME);
    $APLOptions = $this->APL_options_load();
    if ($APLOptions === false)
    {
      $this->APL_install();
    }
    //
  }
  function APL_deactivation_handler()
  {
    $APLOptions = $this->APL_options_load();
    //if user set cleanup to true, remove all options and post meta data
    if ($APLOptions['delete_core_db'] === true)
    {
      
      delete_option($this->_APL_OPTION_NAME);
      //TODO create a get presets array/names
      //$presetOptionsName = get_presets_option_name();
      //foreach ($presetOptionsName as $key)
      //{
      //  delete_option($presetOptionName[$key]);
      //}
    }
  }
  function APL_uninstall_handler()
  {
    //deactivation hook. Clear all traces of Post List
    $adminOptions = $this->APL_options_load();
    //if user set cleanup to true, remove all options and post meta data
    if ($adminOptions['doCleanup'] == 'true')
    {
      //remove all options for admin
      delete_option($this->_APL_OPTION_NAME);
      //delete_option(array($this->_APL_OPTIONS_NAME));
    }
  }
  //replacing APL_get_admin_options
  //protected
  function APL_options_load()
  {
    $exit_msg = 'There was no APL Core options available.';
    $APLOptions = get_option($this->_APL_OPTION_NAME);
    if ($APLOptions !== false)
    {
      return $APLOptions;
    }
    else
    {
      return false;
      //exit($exit_msg);
    }
  }
  //protected
  function APL_options_save($APLOptions)
  {
    
    if (isset ($APLOptions))
    {
      update_option($this->_APL_OPTION_NAME, $APLOptions);
    }
  }
  //protected
  function APL_options_remove()
  {
    delete_option($this->_APL_OPTION_NAME);
  }
  function APL_options_set_to_default()
//protected function kalinsPost_getAdminSettings()
  {//simply returns all our default option values
//    $APLOptions = array();
//    //TODO create a default OptionsDbObj out of preset_arr
//    $APLOptions['preset_arr'] = '{"pageContentDivided_5":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"postExcerptDivided_5":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simpleAttachmentList_10":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"10","before":"<ul>","content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"images_5":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"5","before":"<hr \/>","content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>","after":"<hr \/>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"pageDropdown_100":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"100","before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">","content":"<option value=\"[post_permalink]\">[post_title]<\/option>","after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simplePostList_5":{"categories":"","tags":"","post_type":"post","orderby":"date","order":"DESC","numberposts":"5","before":"<p>","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"footerPageList_10":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"10","before":"<p align=\"center\">","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"everythingNumbered_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"everythingID_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"relatedPosts_5":{"categories":"","tags":"","post_type":"post","orderby":"rand","order":"DESC","numberposts":"5","before":"<p>Related posts: ","content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"true"},"CSSTable":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"15","before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">","content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","post_parent":"None","includeCats":"false","includeTags":"false","requireAllCats":"false","requireAllTags":"false"}}';
//    $APLOptions['default_preset'] = '';
//    $APLOptions['doCleanup'] = 'true';
//
//    return $APLOptions;
    ////////////////////////////////////////////////////
    //TODO CREATE A FILE VERSION AND DB VERSION TO CHECK ON
    $APLOptions = array();
    $APLOptions['version'] = APL_VERSION;
    $APLOptions['preset_db_names'] = array(0 => 'default');
    $APLOptions['delete_core_db'] = true;
    $APLOptions['error'] = '';

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

  function APL_widget_init_handler()
  {
    $widget = new APLWidget();
    register_widget($widget);
    //register_widget('APLWidget');
  }

  function APL_admin_init_handler()
  {
    //TODO Create/edit Handlers
    //???
    //add_action('wp_ajax_APL_save_preset', $this->APL_save_preset_handler());
    add_action('wp_ajax_APL_save_preset_handler', array($this, 'APL_save_preset_handler'));
    add_action('wp_ajax_APL_delete_preset_handler', array($this, 'APL_delete_preset_handler')); 
    add_action('wp_ajax_APL_restore_preset_handler', array($this, 'APL_restore_preset_handler'));
  }

  function APL_admin_menu_handler()
  {
    global $APLPost_hook;
    $APLPost_hook = add_submenu_page('options-general.php', "Advanced Post List", "Advanced Post List", 'manage_options', "advanced-post-list", array($this, 'APL_admin_page'));
    add_action("admin_print_scripts-$APLPost_hook", array($this, 'APL_admin_head'));
    //add_filter('contextual_help', 'kalinsPost_contextual_help', 10, 3);
  }

  function APL_admin_head()
  {
    wp_enqueue_script("jquery");
  }

  function APL_admin_page()
  {
    require_once( WP_PLUGIN_DIR . '/advanced-post-list/APL_admin.php');
  }

  //TODO CREATE A FUNCTION TO IMPORT DATA TO THE PLUGIN
  function APL_import_handler()
  {
    
  }

  //TODO CREATE A FUNCTION TO EXPORT DATA FROM THE PLUGIN
  function APL_export_handler()
  {
    
  }

  function APL_save_preset_handler()
  {

    check_ajax_referer("APL_save_preset_handler");
    $APLOptions = $this->APL_options_load();
    /*
    //MULTI PRESET OPTIONS
    
    
    foreach ($APLOptions['preset_db_names'] as $key => $value)
    {
      $preset_db[$key] = $value;
    }
    */
    //DEFAULT USE
    $preset_db = new APLPresetDbObj('default');
    
    
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

    $preset_db->_preset_db = $valArr;
    $preset_db->options_save_db();
    //$APLOptions['preset_arr'] = json_encode($valArr);

    $APLOptions['delete_core_db'] = $_POST['doCleanup'];
    $this->APL_options_save($APLOptions);
    //update_option(_APL_OPTION_NAME, $APLOptions); //save options to database

    $outputVar->status = "success";
    //$outputVar->preset_arr = json_decode($APLOptions['preset_arr']);
    $outputVar->preset_arr = $preset_db->_preset_db;

    //preview saved data
    $outputVar->previewOutput = $this->APL_run($preset_name);

    //echo $kalinsPostAdminOptions['preset_arr'];

    echo json_encode($outputVar);
  }

  function APL_delete_preset_handler()
  {

    check_ajax_referer("APL_delete_preset_handler");

    //$APLOptions = $this->APL_options_load();
    
    $presetDbObj = new APLPresetDbObj('default');

    //$valArr = json_decode($APLOptions['preset_arr']);
    //$valArr = json_decode($APLOptions['preset_arr']);

    $preset_name = stripslashes($_POST['preset_name']);
    unset($presetDbObj->_preset_db->$preset_name);

    //$APLOptions['preset_arr'] = json_encode($valArr);
    //update_option(_APL_OPTIONS_NAME, $APLOptions); //save options to database
    
    $presetDbObj->options_save_db();
    echo json_encode($presetDbObj->_preset_db);
    //echo (array) $presetDb;
  }

  function APL_restore_preset_handler()
  {

    check_ajax_referer("APL_restore_preset_handler");

    //$APLOptions = $this->APL_options_load();
    $presetDbObj = $tmpDbObj = new APLPresetDbObj('default');
    //TODO create or reformat function
    //$defaultAdminOptions = kalinsPost_getAdminSettings();
    $tmpDbObj->set_to_defaults();
    
    foreach ($tmpDbObj->_preset_db as $key => $value)
    {
      $presetDbObj->_preset_db->$key = $value;
    }
    
    
    
    $presetDbObj->options_save_db();

//    $userValArr = json_decode($APLOptions['preset_arr']);
//    $defValArr = json_decode($APLOptions['preset_arr']);
//
//    foreach ($defValArr as $key => $value)
//    {
//      $userValArr->$key = $value;
//    }
//
//    $APLOptions['preset_arr'] = json_encode($userValArr);

    //update_option(_APL_OPTIONS_NAME, $APLOptions); //save options to database
    echo json_encode($presetDbObj->_preset_db);
    //echo $APLOptions['preset_arr'];
  }

  /*
   * Name: APL Shortcode Handler
   * Desc: function handler for when shortcode(post-list is called)
   * Params: 
   * Return: 
   */

  function APL_shortcode_handler($att)
  {
    if (isset($att['name']))
    {
      //get preset data and send it to run
      //TODO need to get the data and set it first
      return $this->APL_display($att);
    }
    else
    {
      return '';
      //TODO - create default missing preset message
      //TODO - create a method for custom shortcodes
    }
  }

  function APL_display($att)
  {
     return $this->APL_run($att['name']);
  }

  function APL_run($preset_name)
  {
    ////////////////////////////////////////////////////////////////////////////
    ////// SETUP CURRENT PRESET ARRAY //////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //Get Options to get option database name
    //TODO - for now lets just pass default.
    //$APLOptions = $this->APL_options_load();
    $OptionDb = new APLPresetDbObj('default');
    //$OptionDb->option_load_db();
    //$presetObj = json_decode($presetObj);
    //Check if there is a valid Option Database to pull preset data
    if (isset($OptionDb))
    {
      //$presetObj = $OptionDb->load_preset($preset_name);
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
    ////// GET CURRENT POST DATA ///////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

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
    if ($presetObj->_postExcludeCurrent == true)
    {
      $excludeList = $post->ID;
    }
    //TODO
    //$presetObj->before = APLInternalShortcodeReplace($presetObj->before, $post, 0);
    //$presetObj->after = APLInternalShortcodeReplace($presetObj->after, $post, 0);
    ////////////////////////////////////////////////////////////////////////////
    ////// POST/PAGE FILTER SETTINGS ///////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
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
    //////PRESET OPTIONS FILTER SETTINGS ///////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
     * NO NEED FOR THIS WHEN YOU CAN JUST ASSIGN -1 TO THE
     *  get_post() FUNCTION
      if ($newVals->requireAllCats == "true" || $newVals->requireAllTags == "true")
      {
      $origNumberposts = $newVals->numberposts;
      $newVals->numberposts = -1;
      }
     */
    //TODO - Change get_post() params to grab data needed
    //TODO Change $posts; varible to $postData 
    $posts = get_posts('numberposts=' . -1 .
        '&category=' . $tmpCatsSelected .
        '&post_type=' . $presetObj->post_type .
        '&tag=' . $tmpTagsSelected .
        '&orderby=' . $presetObj->orderby .
        '&order=' . $presetObj->order .
        '&exclude=' . $excludeList .
        '&post_parent=' . $presetObj->post_parent);

    //TEST ???
    //$posts = query_posts("orderby=tax_query");
////// Categories //////////////////////////////////////////////////////////////
    //if every post must lie in every selected category
    if ($presetObj->_catsRequired == "true")
    {
      //Create array from list of categories
      //Delete the last array which has no value (null)
      $requiredCats = explode(",", $presetObj->_catsSelected);
      $i = sizeof($requiredCats);
      $i -= 1;
      unset($requiredCats[$i]);
      //For some reason this didn't work
      //unset( $requiredCats[sizeof($requiredCats - 1)] );
      //unset( $requiredCats[sizeof($requiredCats) - 1] );

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
//    if ($presetObj->_catsRequired == "true" || $presetObj->_tagsRequired == "true")
//    {
//      //???
//      //$posts = array_slice($posts, 0, $origNumberposts);
//      $posts = array_slice($posts, 0 , $presetObj->_listAmount);
//      //$posts = array_slice($posts, 0 , 5);
//    }
    $posts = array_slice($posts, 0 , $presetObj->_listAmount);
    //TODO create a custom message for the user to use
    if (count($posts) == 0)
    {//return nothing if no results
      return "";
    }
////// BEFORE //////////////////////////////////////////////////////////////////
    $output = $presetObj->_before;

///// CONTENT //////////////////////////////////////////////////////////////////
    $count = 0;
    foreach ($posts as $page)
    {

      $output = $output . APLInternalShortcodeReplace($presetObj->_content, $page, $count);
      $count = $count + 1;
    }

    $finalPos = strrpos($output, "[final_end]");
    //if ending exists (the last item where we don't want to add any more commas or ending brackets or whatever)
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


    return $output;
  }

}
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
//$advanced_post_list = new APLCore(__FILE__);
?>
