<?php

//TODO create a way to check for multiple categories
// and then unset if any of them are false
// create a ((repeating function))

/**
 * <p><b>Desc:</b> Contains all the main operations/methods for Advanced
 *                 Post List plugin.</p>
 * @version 0.2.0
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
     * @var type 
     * @since 0.1.0
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
     * <p><b>Desc:</b> Sets up the core attributes to run ALPCore functions.<p>
     * @param string $file - contains data about the file itself 
     * 
     * @since 0.1.0
     * @version 0.2.0 - Refined how version checking was performed.
     * @uses $this->APL_load_plugin_data($file)
     * @uses $this->APL_options_load()
     * @uses $this->APL_options_save($APLOptions)
     * @tutorial 
     * <ol>
     * <li value="1">Set plugin file data/properties</li>
     * <li value="2">Check database version with the current file versioin</li>
     * <li value="3">Register activation, deactivation, and uninstall hooks 
     *                with WordPress</li>
     * <li value="4">Add Shortcode to WordPress action hooks</li>
     * <li value="5">If the current user has admin rights, do <b>Steps 6-7</b></li>
     * <li value="6">Add APL's menu to WordPress 'admin_menu' action hook</li>
     * <li value="7">Add APL's initial admin action hooks to WordPress 
     *                'admin_menu' action hook</li>
     * </ol>
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
            if ($APLOptions['version'] === '1.0.1')
            {
                $APLOptions['version'] = APL_VERSION;
            }
            
            //////// UPGRADES ////////
            $oldversion = $APLOptions['version'];
            
            //$a1 = version_compare('0.3.0', $oldversion, '>');
            //UPGRADE TO 0.3.X
            if (version_compare('0.3.a1', $oldversion, '>'))
            {
                $this->APL_upgrade_to_030();
            }
            //$this->APL_fix();
            //UPDATE VERSION
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
        register_activation_hook($this->plugin_file_path,
                                 array(&$this, 'APL_handler_activation'));
        register_deactivation_hook($this->plugin_file_path,
                                   array(&$this, 'APL_handler_deactivation'));
        register_uninstall_hook($this->plugin_file_path,
                                array(&$this, 'APL_handler_uninstall'));



        //add_action('widgets_init', array($this, 'APL_handler_widget_init'));
        //STEP 4
        add_shortcode('post_list',
                      array($this, 'APL_handler_shortcode'));
        //STEP 5
        if (is_admin())
        {
            //STEP 6
            add_action('admin_menu',
                       array($this, 'APL_handler_admin_menu'));
            //STEP 7
            add_action('admin_init',
                       array($this, 'APL_handler_admin_init'));
            //add_action('wp_enqueue_scripts', array($this, 'APL_admin_head'));
        }
    }
//    private function APL_fix()
//    {
//        $preset_db_obj = new APLPresetDbObj('default');
//        $preset_db = $preset_db_obj->_preset_db;
//        $tmp_preset_db = new stdClass();
//        foreach($preset_db as $preset_name => $preset_value)
//        {
//            $tmp_preset = new APLPresetObj();
//            $tmp_preset->_postParent = $preset_value->_postParent;
//            foreach ($preset_value->_postTax as $post_type_name01 => $post_type_value01)
//            {
//                foreach ($post_type_value01->taxonomies as $taxonomy_name => $taxonomy_value)
//                {
//                    $tmp_preset->_postTax->$post_type_name01->taxonomies->$taxonomy_name->require_taxonomy = false;
//                    $tmp_preset->_postTax->$post_type_name01->taxonomies->$taxonomy_name->require_terms = $taxonomy_value->require;
//                    $tmp_preset->_postTax->$post_type_name01->taxonomies->$taxonomy_name->include_terms = $taxonomy_value->include;
//                    $tmp_preset->_postTax->$post_type_name01->taxonomies->$taxonomy_name->terms = $taxonomy_value->terms;
//                }
//                
//            }
//            $tmp_preset->_listAmount = $preset_value->_listAmount;
//            $tmp_preset->_postExcludeCurrent = $preset_value->_postExcludeCurrent;
//            $tmp_preset->_listOrderBy = $preset_value->_listOrderBy;
//            $tmp_preset->_listOrder = $preset_value->_listOrder;
//            $tmp_preset->_before = $preset_value->_before;
//            $tmp_preset->_content = $preset_value->_content;
//            $tmp_preset->_after = $preset_value->_after;
//            
//            $tmp_preset_db->$preset_name = $tmp_preset;
//        }
//        $preset_db_obj->_preset_db = $tmp_preset_db;
//        $preset_db_obj->options_save_db();
//    }
    private function APL_upgrade_to_030()
    {
        $APLOptions = $this->APL_options_load();
        $APLOptions['jquery_ui_theme'] = 'overcast';
        
        $preset_db_obj = new APLPresetDbObj('default');
        $preset_db = $preset_db_obj->_preset_db;
        $tmp_preset_db = new stdClass();
        foreach($preset_db as $preset_name => $preset_value)
        {
            $tmp_preset = new APLPresetObj();
            if ($preset_value->_postParent === 'current')
            {
                $tmp_preset->_postParent[0] = "-1";
            }
            else if ($preset_value->_postParent !== 'None' && $preset_value->_postParent !== '')
            {
                $tmp_preset->_postParent[0] = $preset_value->_postParent;
            }
            
            if ($preset_value->_catsSelected !== '')
            {
                
                $tmp_preset->_postTax->post->taxonomies->category->require_taxonomy = false;//NEW
                $tmp_preset->_postTax->post->taxonomies->category->require_terms = true;
                if ($preset_value->_catsRequired === 'false')
                {
                    $tmp_preset->_postTax->post->taxonomies->category->require_terms = false;
                }
                $tmp_preset->_postTax->post->taxonomies->category->include_terms = true;
                if ($preset_value->_catsInclude === 'false')
                {
                    $tmp_preset->_postTax->post->taxonomies->category->include_terms = false;
                }
                $terms = explode(',', $preset_value->_catsSelected);
                $i = 0;
                foreach ($terms as $term)
                {
                    $tmp_preset->_postTax->post->taxonomies->category->terms[$i] = intval($term);
                    $i++;
                }
            }
            if ($preset_value->_tagsSelected !== '')
            {
                
                $tmp_preset->_postTax->post->taxonomies->post_tag->require_taxonomy = false;//NEW
                $tmp_preset->_postTax->post->taxonomies->post_tag->require_terms = true;
                if ($preset_value->_tagsRequired === 'false')
                {
                    $tmp_preset->_postTax->post->taxonomies->post_tag->require_terms = false;
                }
                $tmp_preset->_postTax->post->taxonomies->post_tag->include_terms = true;
                if ($preset_value->_tagsInclude === 'false')
                {
                    $tmp_preset->_postTax->post->taxonomies->post_tag->include_terms = false;
                }
                $terms = explode(',', $preset_value->_tagsSelected);
                $i = 0;
                foreach ($terms as $term)
                {
                    $tmp_preset->_postTax->post->taxonomies->post_tag->terms[$i] = intval($term);
                    $i++;
                }
            }
            $tmp_preset->_listAmount = intval($preset_value->_listAmount);
            
            $tmp_preset->_listOrder = $preset_value->_listOrder;
            $tmp_preset->_listOrderBy = $preset_value->_listOrderBy;
            
            $tmp_preset->_postStatus = 'publish';
            
            $tmp_preset->_postExcludeCurrent = true;
            if ($preset_value->_postExcludeCurrent === 'false')
            {
                $tmp_preset->_postExcludeCurrent = false;
            }
            
            $tmp_preset->_before = $preset_value->_before;
            $tmp_preset->_content = $preset_value->_content;
            $tmp_preset->_after = $preset_value->_after;
            $tmp_preset_db->$preset_name = $tmp_preset;
        }
        $preset_db_obj->_preset_db = $tmp_preset_db;
        $preset_db_obj->options_save_db();
    }
    /**
     * <p><b>Desc:</b> saves all the file (dir/path) values for defining file.</p>
     *        structure/paths
     * @param string $plugin_path 
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Save the plugin location properties</li>
     * </ol>
     * @example
     * <ul>
     * <li value="1">$this->plugin_basename = 'advanced-post-list/advanced-post-list.php'</li>
     * <li value="2">$this->plugin_dir_path = 'C:\xampp\htdocs\wordpress/wp-content/plugins/advanced-post-list/'</li>
     * <li value="3">$this->plugin_file_path = 'C:\xampp\htdocs\wordpress/wp-content/plugins/advanced-post-list/advanced-post-list.php'</li>
     * <li value="4">$this->plugin_dir_url = 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/'</li>
     * <li value="5">$this->plugin_file_url = 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/advanced-post-list.php/'</li>
     * </ul>
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
     * <p><b>Desc:</b> Adds plugin action hooks to admin_init for 
     *                 loading up when the user has admin rights.</p>
     * 
     * @since 0.1.0
     * @version 0.2.0 - Added export, import, and save settings 
     *                  ajax functions.
     * 
     * @tutorial
     * <ol>
     * <li value="1">Add Ajax action hooks for APL Admin settings page.</li>
     * </ol>
     */
    public function APL_handler_admin_init()
    {
        //STEP 1
        add_action('wp_ajax_APL_handler_save_preset',
                   array($this, 'APL_handler_save_preset'));
        add_action('wp_ajax_APL_handler_delete_preset',
                   array($this, 'APL_handler_delete_preset'));
        add_action('wp_ajax_APL_handler_restore_preset',
                   array($this, 'APL_handler_restore_preset'));

        add_action('wp_ajax_APL_handler_export',
                   array($this, 'APL_handler_export'));
        add_action('wp_ajax_APL_handler_import',
                   array($this, 'APL_handler_import'));

        add_action('wp_ajax_APL_handler_save_settings',
                   array($this, 'APL_handler_save_settings'));

        //wp_enqueue_script('jquery');

        /*         * ********************************************************* */
        /*         * ************** REMOVE SCRIPTS & STYLES ****************** */
        /*         * ********************************************************* */
        wp_deregister_script('jquery');
        wp_deregister_script('apl-admin');
        wp_deregister_script('jquery-ui');
        wp_deregister_script('apl-admin-ui');
        wp_deregister_script('jquery-ui-multiselect');
        //wp_register_script('apl-admin', APL_URL . 'includes/js/APL-admin.js', $script_deps, APL_VERSION, false);

        /*         * ********************************************************* */
        /*         * ************** REGISTER SCRIPTS ************************* */
        /*         * ********************************************************* */
        
        $script_deps = array();
        wp_register_script('jquery',
                           'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
                           $script_deps,
                           APL_VERSION,
                           false);
        $script_deps = array('jquery');
        wp_register_script('apl-admin',
                           plugins_url() . '/advanced-post-list/includes/js/APL-admin.js',
                           $script_deps,
                           APL_VERSION,
                           false);

        $script_deps = array('jquery');
        wp_register_script('jquery-ui',
                           'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js',
                           $script_deps,
                           APL_VERSION,
                           false);

        //$script_deps = array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-accordion', 'jquery-ui-tabs');
        $script_deps = array('jquery', 'jquery-ui');
        wp_register_script('apl-admin-ui',
                           plugins_url() . '/advanced-post-list/includes/js/APL-admin_ui.js',
                           $script_deps,
                           APL_VERSION,
                           false);
        $script_deps = array('jquery', 'jquery-ui');
        wp_register_script('jquery-ui-multiselect',
                           plugins_url() . '/advanced-post-list/includes/js/jquery.multiselect.min.js',
                           $script_deps,
                           APL_VERSION,
                           false);
        $script_deps = array('jquery', 'jquery-ui');
        wp_register_script('jquery-ui-multiselect-filter',
                           plugins_url() . '/advanced-post-list/includes/js/jquery.multiselect.filter.min.js',
                           $script_deps,
                           APL_VERSION,
                           false);
        //wp_enqueue_script('apl-admin');
        //wp_print_scripts('apl-admin');
//        
/*         * ********************************************************* */
/*         * ************** REGISTER STYLES ************************** */
/*         * ********************************************************* */
        $APLOptions = $this->APL_options_load();
        wp_deregister_style('apl-admin-css');
        wp_deregister_style('apl-admin-ui-css');

        wp_enqueue_style('apl-admin-css',
                         plugins_url() . '/advanced-post-list/includes/css/APL-admin.css',
                         false,
                         APL_VERSION,
                         false);
        //TODO - CREATE A SETTING FOR THE USER TO CHANGE THE THEME.
        
        if (!isset($APLOptions['jquery_ui_theme']))
        {
            $APLOptions['jquery_ui_theme'] = 'overcast';
            $this->APL_options_save($APLOptions);
        }
        wp_enqueue_style('apl-admin-ui-css',
                         'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/' . $APLOptions['jquery_ui_theme'] . '/jquery-ui.css',
                         false,
                         APL_VERSION,
                         false);
        
        wp_enqueue_style('apl-jquery-ui-multiselect-css',
                         plugins_url() . '/advanced-post-list/includes/css/jquery-ui-multiselect-widget.css',
                         false,
                         APL_VERSION,
                         false);
    }

    /**
     * <p><b>Desc:</b> Set plugin's initial saved settings and store it 
     * in Worpress</p>
     * 
     * @since 0.1.0
     * @uses $this->APL_options_set_to_default()
     * @uses $this->APL_options_save(stdClass)
     * 
     * @tutorial
     * <ol>
     * <li value="1">Set APL Options to default settings</li>
     * <li value="2">Save options to web database</li>
     * </ol>
     */
    private function APL_install()
    {
        //STEP 1
        $APLOptions = $this->APL_options_set_to_default();
        //STEP 2
        $this->APL_options_save($APLOptions);
    }

    /**
     * <p><b>Desc:</b> Handler all the activation methods when plugin is 
     *        activated in wordpress.</p>
     * 
     * @since 0.1.0
     * @uses $this->APL_Options_load()
     * @uses $this->APL_install()
     * 
     * @tutorial
     * <ol>
     * <li value="1">Load APL Options, if no options are available 
     * do <b>step 2</b>; <i>(returns false)</i>.</li>
     * <li value="2">Install APL Options into WordPress</li>
     * </ol>
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
     * <p><b>Desc:</b> Handles all the deactivation methods when plug 
     * in is deactivated</p>
     * 
     * @since 0.1.0
     * @version 0.2.0 - Added delete_option('APL_preset_db-default')
     *                  for deleting preset database data.
     * @uses $this->APL_Options_load()
     * 
     * @tutorial
     * <ol>
     * <li value="1">Load Options from database</li>
     * <li value="2">If user has delete database set to true, then 
     *               delete All options</li>
     * </ol>
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
     * <p><b>Desc:</b> Handles all the uninstall methods when plug in is 
     * uninstalled.</p>
     * 
     * @since 0.1.0
     * @version 0.2.0 - Changed to delete all plugin data, whether 
     *                  'delete plugin data upon deactivation' is set
     *                  to no.
     * @uses $this->APL_Options_load()
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Remove all option data from database.</li>
     * </ol>
     * 
     * @todo set function to completely remove database since the user
     *        decided to delete the plugin anyways
     */
    public function APL_handler_uninstall()
    {
        //uninstall hook. Clear all traces of the Advanced Post List
        // database options.
        //Step 1
        delete_option($this->_APL_OPTION_NAME);
        delete_option('APL_preset_db-default');
        //Alt uninstall that uses the 'delete upon deactivation' setting
    }

//FIX SET TO DEFAULT THEN OVERWRITE AND RETURN
    /**
     * <p><b>Desc:</b> Gets core plugin database from from wordpress and 
     *        sends the options back. If there is no options, wordpress 
     *        returns a false value.</p>
     * @return mixed 
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Get APL Options from WordPress Database.</li>
     * <li value="2">If data exists then <i>return option data</i>, 
     *               otherwise <i>return false</i>.</li>
     * </ol>
     */
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
     * <p><b>Desc:</b> Overwrites/adds plugin options to WordPress 
     * database.</p> 
     * @param stdClass $APLOptions - Contains core database settings
     * 
     * @since 0.1.0
     * 
     * @tutorial 
     * <ol>
     * <li value="1">If Options has data, then update(/add) to 
     *               wordpress database</li>
     * </ol>
     */
    private function APL_options_save($APLOptions)
    {
        //STEP 1
        if (isset($APLOptions))
        {
            update_option($this->_APL_OPTION_NAME,
                          $APLOptions);
        }
    }

    //simply returns all our default option values
    /**
     * <p><b>Desc:</b> Sets the APL Options to its default values.</p>
     * @return stdClass $APLOptions - Core plugin options
     * 
     * @since 0.1.0
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Set the version number with the constant varible.</li>
     * <li value="2">Set additional database names. 'Default' is the 
     * standard preset database.</li>
     * <li value="3">Set delete_core_db as true.</li>
     * <li value="4">Set default error log to empty.</li>
     * <li value="5"><i>Return option array</i>.</li>
     * </ol>
     */
    private function APL_options_set_to_default()
    {
        $APLOptions = array();
        //STEP 1
        $APLOptions['version'] = APL_VERSION;
        //STEP 2
        $APLOptions['preset_db_names'] = array(0 => 'default');
        //STEP 3
        $APLOptions['delete_core_db'] = true;
        
        $APLOptions['jquery_ui_theme'] = 'overcast';
        //STEP 4
        $APLOptions['error'] = '';

        //STEP 5
        return $APLOptions;
    }
    /**
     * <p><b>Desc:</b> Adds the plugins widget to wordpress.</p>
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Register APL's widget class name.</li>
     * </ol>
     */
    public function APL_handler_widget_init()
    {
        //$widget = new APLWidget();
        //register_widget($widget);
        register_widget('APLWidget');
    }

    /**
     * <p><b>Desc:</b> Adds the plugin's menu links to the wordpress 
     * admin page.</p>
     * 
     * @since 0.1.0
     * @global $APLPost_hook
     * 
     * @tutorial
     * <ol>
     * <li value="1">Add a submenu to Wordpress settings menu.</li>
     * <li value="2">Add action scripts to that menu page.</li>
     * </ol>
     * @todo create APL's own menu once addition pages are available
     * 
     */
    public function APL_handler_admin_menu()
    {
        //??? disabled for now
        //global $APLPost_hook;
        //STEP 1
        $APLPost_hook = add_submenu_page('options-general.php',
                                         "Advanced Post List",
                                         "Advanced Post List",
                                         'manage_options',
                                         "advanced-post-list",
                                         array($this, 'APL_admin_page')); //Change to body or content
        //STEP 2
        add_action('admin_print_scripts-' . $APLPost_hook,
                   array($this, 'APL_admin_head'));
        //add_filter('contextual_help', 'kalinsPost_contextual_help', 10, 3);
    }

    /**
     * <p><b>Desc:</b> </p> 
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Add JQuery to wordpress enqueue scripts.</li>
     * </ol>
     */
    public function APL_admin_head()
    {
        $tagL = get_tags('hide_empty=0');
        $tagL = json_encode($tagL);

        $presetDbObj = new APLPresetDbObj('default');
        
        
        $postTax = $this->APL_get_postTax();
        $taxTerms = $this->APL_get_taxonomy_terms();
        $postTax_parent_selector = $this->APL_get_postTax_ui_parent_selection();

        $apl_admin_settings = array(
            'plugin_url' => APL_URL,
            'savePresetNonce' => wp_create_nonce('APL_handler_save_preset'),
            'deletePresetNonce' => wp_create_nonce('APL_handler_delete_preset'),
            'restorePresetNonce' => wp_create_nonce('APL_handler_restore_preset'),
            'exportNonce' => wp_create_nonce('APL_handler_export'),
            'importNonce' => wp_create_nonce('APL_import'),
            'saveSettingsNonce' => wp_create_nonce('APL_handler_save_settings'),
            'presetDb' => json_encode((array) $presetDbObj->_preset_db),
            'postTax' => $postTax,
            'taxTerms' => $taxTerms
        );
        
        $apl_admin_ui_settings = array(
            'post_type_amount' => sizeof((array) $post_taxonomies),
            'postTax' => $postTax, //issue may arise
            'postTax_parent_selector' => $postTax_parent_selector
        );
        
        //JavaScripts
        wp_enqueue_script('jquery');
        wp_enqueue_script('apl-admin');
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script('apl-admin-ui');
        wp_enqueue_script('jquery-ui-multiselect');
        wp_enqueue_script('jquery-ui-multiselect-filter');

        //Attach PHP varibles
        wp_localize_script('apl-admin',
                           'apl_admin_settings',
                           $apl_admin_settings);
        wp_localize_script('apl-admin-ui',
                           'apl_admin_ui_settings',
                           $apl_admin_ui_settings);
        
        //Styles CSS
        wp_enqueue_style('apl-jquery-ui-multiselect-css');
        wp_enqueue_style('apl-admin-css');
        wp_enqueue_style('apl-admin-ui-css');
        
    }
    
    private function  APL_get_postTax()
    {
        $rtnObj = new stdClass();
        $post_type_names = get_post_types('',
                                          'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_names[$value]);
        }
        foreach($post_type_names as $post_type_name)
        {
            $post_type_object = get_post_type_object($post_type_name);
            $post_type_object->taxonomies = $this->APL_get_post_type_taxonomies($post_type_name);
            
            
            $rtnObj->$post_type_name = new stdClass();
            $rtnObj->$post_type_name = $post_type_object->taxonomies;
        }

        
        return $rtnObj;
        
    }
    private function APL_get_post_type_taxonomies($post_type_name)
    {
        $rtnTaxonomyArray = array();
        
        $taxonomy_names = get_taxonomies('', 'names');
        foreach ($taxonomy_names as $taxonomy_name)
        {
            $taxonomy_object = get_taxonomy($taxonomy_name);
            foreach ($taxonomy_object->object_type as $object_type_name)
            {
                if ($object_type_name === $post_type_name)
                {
                    $rtnTaxonomyArray[$taxonomy_name] = $taxonomy_name;
                }
            }
        }
        $skip_taxonomies = array('post_format', 'nav_menu', 'link_category');
        foreach($skip_taxonomies as $value)
        {
            unset($$rtnTaxonomyArray[$value]);
        }
        return $rtnTaxonomyArray;
    }
    private function APL_get_taxonomy_terms()
    {
        $rtnTaxonomyTermsArray = new stdClass();
        
        $taxonomy_names = get_taxonomies('', 'names');
        $skip_taxonomies = array('post_format', 'nav_menu', 'link_category');
        foreach($skip_taxonomies as $value)
        {
            unset($taxonomy_names[$value]);
        }
        foreach($taxonomy_names as $taxonomy_name)
        {
            
            $argTerms = array(
              'hide_empty'  =>  0,
              'taxonomy'    =>  $taxonomy_name  
            );
            $terms = get_categories($argTerms);
            $rtnTaxonomyTermsArray->$taxonomy_name = $terms;
            
        }
         
        
        return $rtnTaxonomyTermsArray;
    }
    private function APL_get_postTax_ui_parent_selection()
    {
        $rtnObj = new stdClass();
        
        $post_type_names = get_post_types('', 'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_names[$value]);
        }
        foreach($post_type_names as $post_type_name)
        {
            $post_type_object = get_post_type_object($post_type_name);
            $rtnObj->$post_type_name->hierarchical = $post_type_object->hierarchical;
        }
        
        return $rtnObj;
    }

    /**
     * <p><b>Desc:</b> Adds the create page content.</p>
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Add Settings page contents.</li>
     * </ol>
     */
    public function APL_admin_page()
    {

        require_once( $this->plugin_dir_path . 'includes/APL-admin.php');
    }

    //TODO CREATE AN AJAX FUNCTION TO IMPORT DATA TO THE PLUGIN
    // COULDN'T FIND A WAY TO CARRY THE $_FILES GLOBAL VARIBLE
    // THROUGH .post TO TARGET PHP CODE
    /**
     * <p><b>Desc:</b> Method used when jQuery.post is called in
     * javascript for $('#frmImport').submit(). Currently not being 
     * used.</p> 
     * 
     * @since 0.2.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Check wp_create_nonce value.</li>
     * <li value="2"><i>Return data</i> (if any) as a json string.</li>
     * </ol>
     */
    public function APL_handler_import()
    {
        //Step 1
        check_ajax_referer('APL_handler_import');
        //Step 2
        echo json_encode('');
    }

    /**
     * <p><b>Desc:</b> Method used when jQuery.post is called in
     * javascript for $('#frmExport').submit().</p>
     * 
     * @since 0.2.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">Check wp_create_nonce value.</li>
     * <li value="2">Store varibles from Global $_GET (_ajax_nonce, 
     * action, and filename) to rntData.</li>
     * <li value="3">Set error message.</li>
     * <li value="4"><i>Return rtnData</i> as a json string.</li>
     * </ol>
     */
    public function APL_handler_export()
    {
        $check_ajax_referer = check_ajax_referer("APL_handler_export");

        $rtnData = new stdClass();
        $rtnData->_ajax_nonce = $_GET['_ajax_nonce'];
        $rtnData->action = $_GET['action'];
        $rtnData->filename = $_GET['filename'];
        $rtnData->_error = '';
        $rtnData->export_url = APL_URL . 'includes/export.php';

        echo json_encode($rtnData);
    }

    /**
     * <p><b>Desc:</b> Method used for saving APL core 'General Settings'
     * to the developer's wordpress database.</p> 
     * 
     * @since 0.2.0
     * @uses $this->APL_options_load()
     * @uses $this->APL_options_save($APLOptions)
     * 
     * @tutorial
     * <ol>
     * <li value="1">Check wp_create_nonce value.</li>
     * <li value="2">Load APL Options.</li>
     * <li value="3">Store 'delete core db' value to APL Options.</li>
     * <li value="4">Save APLOptions to database.</li>
     * <li value="5"><i>Echo/return dataRtn</i> to jQuery.post param function.</li>
     * </ol>
     * 
     */
    public function APL_handler_save_settings()
    {
        //Step 1
        $check_ajax_referer0 = check_ajax_referer("APL_handler_save_settings");
        $dataRtn = new stdClass();
        $dataRtn->error = '';
        //Step 2
        $APLOptions = $this->APL_options_load();
        //Step 3
        $APLOptions['delete_core_db'] = $_POST['deleteDb'];
        
        $APLOptions['jquery_ui_theme'] = $_POST['theme'];
        
        wp_enqueue_style('apl-admin-ui-css',
                         'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/' . $APLOptions['jquery_ui_theme'] . '/jquery-ui.css',
                         false,
                         APL_VERSION,
                         false);
        
        //Step 4
        $this->APL_options_save($APLOptions);
        //Step 5
        $dataRtn->theme = $APLOptions['jquery_ui_theme'];
        echo json_encode($dataRtn);
    }
    
    /**
     * <p><b>Desc:</b> Method used for saving individual preset data
     * to the developer's wordpress database.</p> 
     * 
     * @since 0.1.0
     * @uses $this->APL_options_load()
     * @uses $preset_db->options_save_db()
     * @uses $this->APL_run($preset_name)
     * 
     * @tutorial 
     * <ol>
     * <li value="">Check javascript ajax wp_create_nonce reference.</li>
     * <li value="">Load APL Options & APL Preset DbOptions.</li>
     * <li value="">create a temp varible and save all the values from
     *     the settings page.</li>
     * <li value="">Add temp varible to current preset varible.</li>
     * <li value="">Save current preset varible to plugin options.</li>
     * <li value="">Save delete_core_db value to plugin core options.</li>
     * <li value="">Create an output varible.</li>
     * <li value="">Store APL_run return value to $output.</li>
     * <li value="">echo $output.</li>
     * </ol>
     */
    
    public function APL_handler_save_preset()
    {
        //STEP 1
        check_ajax_referer("APL_handler_save_preset");
        

        //DEFAULT USE
        $preset_db_obj = new APLPresetDbObj('default');
        //MULTI PRESET OPTIONS
        /*
          foreach ($APLOptions['preset_db_names'] as $key => $value)
          {
          $preset_db[$key] = $value;
          }
         */
        //STEP 3
        
        $preset_db = $preset_db_obj->_preset_db;
        
        $preset_name = stripslashes($_POST['presetName']);

        
        $preset_obj = new APLPresetObj();

        $preset_obj->_postParent = json_decode(stripslashes($_POST['postParent']));
        $preset_obj->_postParent = array_unique($preset_obj->_postParent);
        
        $preset_obj->_postTax = json_decode(stripslashes($_POST['postTax']));
        $tmp_postTax = new stdClass();
        foreach ($preset_obj->_postTax as $post_type_name => $post_type_value)
        {
            foreach ($post_type_value->taxonomies as $taxonomy_name => $taxonomy_value)
            {
                $tmp_postTax->$post_type_name->taxonomies->$taxonomy_name->require_taxonomy = $taxonomy_value->require_taxonomy;
                $tmp_postTax->$post_type_name->taxonomies->$taxonomy_name->require_terms = $taxonomy_value->require_terms;
                $tmp_postTax->$post_type_name->taxonomies->$taxonomy_name->include_terms = $taxonomy_value->include_terms;
                foreach ($taxonomy_value->terms as $term_index => $term_value)
                {
                    $tmp_postTax->$post_type_name->taxonomies->$taxonomy_name->terms[$term_index] = intval($term_value);
                }
            }
        }
        $preset_obj->_postTax = $tmp_postTax; 
        
        $preset_obj->_listAmount = intval($_POST['numberPosts']); //(int) howmany to display
        
        $preset_obj->_listOrder = $_POST['order']; //(string)
        $preset_obj->_listOrderBy = $_POST['orderBy']; //(string)
        
        $preset_obj->_postStatus = $_POST['postStatus']; //(string)
        
//        $preset_obj->_ignoreStickyPosts = true; //(boolean)
//        if ($_POST['ignoreStickyPosts'] === 'false')
//        {
//            $preset_obj->_ignoreStickyPosts = false;
//        }
        $preset_obj->_postExcludeCurrent = true; //(boolean)
        if ($_POST['excludeCurrent'] === 'false')
        {
            $preset_obj->_postExcludeCurrent = false;
        }
        
        $preset_obj->_before = stripslashes($_POST['before']); //(string)
        $preset_obj->_content = stripslashes($_POST['content']); //(string)
        $preset_obj->_after = stripslashes($_POST['after']); //(string)

        $a1 = json_encode($preset_obj);
        $preset_db->$preset_name = $preset_obj;

        //STEP 4
        $preset_db_obj->_preset_db = $preset_db;
        //STEP 5
        $preset_db_obj->options_save_db();
        
        
        $outputVar = new stdClass();
        //STEP 7
        $outputVar->status = "success";
        $outputVar->preset_arr = $preset_db;
        //preview saved data
        //STEP 8
        $outputVar->previewOutput = $this->APL_run($preset_name);

        //STEP 9
        echo json_encode($outputVar);
    }

    /**
     * <p><b>Desc:</b> Method handler for deleting presets within
     * the Preset DbOptions.</p> 
     * 
     * @since 0.1.0
     * @uses $presetDbObj->options_save_db()
     * 
     * @tutorial
     * <ol>
     * <li value="1">Check javascript ajax wp_create_nonce reference.</li>
     * <li value="2">Load APL Preset DbOptions.</li>
     * <li value="3">Get postname from page</li>
     * <li value="4">Delete (unset) preset from preset database varible.</li>
     * <li value="5">Save preset database.</li>
     * <li value="6">echo preset database.</li>
     * </ol>
     */
    public function APL_handler_delete_preset()
    {
        //Step 1
        check_ajax_referer("APL_handler_delete_preset");
        //Step 2
        $presetDbObj = new APLPresetDbObj('default');
        //Step 3
        $preset_name = stripslashes($_POST['preset_name']);
        //Step 4
        unset($presetDbObj->_preset_db->$preset_name);
        //Step 5
        $presetDbObj->options_save_db();
        //Step 6
        echo json_encode($presetDbObj->_preset_db);
    }

    /**
     * <p><b>Desc:</b> Method handler for restoring the original plugin
     * preset defaults</p> 
     * 
     * @since 0.1.0
     * @version 0.2.0 - Changed Step 2 to prevent one varible from 
     *                  pointing to another (dynamic/copy values to 
     *                  individual values).
     * 
     * @tutorial 
     *  <ol>
     * <li value="1">Grab the javascript ajax reference.</li>
     * <li value="2">Get preset options for a temp and a current varible.</li>
     * <li value="3">Set temp to default preset_database_object.</li>
     * <li value="4">Add default presets to current preset_database_object.</li>
     * <li value="5">Save current preset database varible.</li>
     * <li value="6"><i>Echo/Return</i> preset values.</li>
     * </ol>
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
     * <p><b>Desc:</b> Method handler for 'post_list' shortcode and 
     * displaying the target post list.</p>
     * @param mixed $att
     * @return string
     * 
     * @since 0.1.0
     * 
     * @tutorial 
     * <ol>
     * <li value="1">If a value is set, do step 2</li>
     * <li value="2"><i>return $this->display($preset_name)</i></li>
     * <li value="3">otherwise <i>return an empty string</i></li>
     * </ol>
     * @todo create a more dynamic function for creating shortcode param
     * created post lists.
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
     * <p><b>Desc:</b> Public function for custom use.</p> 
     * @param string $preset_name
     * @return string
     * 
     * @since 0.1.0
     * 
     * @tutorial
     * <ol>
     * <li value="1">return APL_run().</li>
     * </ol>
     */
    public function APL_display($preset_name)
    {
        return $this->APL_run($preset_name);
    }
    /**
     * <p><b>Desc:</b> Method used for executing the main purpose of the
     * plugin. Creates an HTML post list string to be sent to the page
     * that it was called from. What is displayed is determined by the
     * 'post_list name' being used.</p> 
     * @param string $preset_name
     * @return string
     * 
     * @since 0.1.0
     * @version 0.2.0 - Corrected a typo in the if statement 
     *                  for _postExcludeCurrent
     * @tutorial
     * <ol>
     * <li value="1">Get preset database object</li>
     * <li value="2">Set pre-filters</li>
     * <li value="3">Get website's post data</li>
     * <li value="4">If required categories is selected, check for posts that match all
     *     selected categories</li>
     * <li value="5">If required tags is selected, check for posts that match all
     *     selected tags</li>
     * <li value="6">If no posts are remaining, return an empty string</li>
     * <li value="7">Setup output string with before, content,  and after.</li>
     * <li value="8">Add each post data according to shortcode</li>
     * <li value="9"><i>Return output</i> string</li>
     * </ol>
     */
    private function APL_run($preset_name)
    {
        
        $preset_db_obj = new APLPresetDbObj('default');
        if(isset($preset_db_obj->_preset_db->$preset_name))
        {
            $presetObj = new APLPresetObj();
            $presetObj = $preset_db_obj->_preset_db->$preset_name;
        }
        else if (current_user_can('manage_options'))
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
        //// GET (GLOBAL) POST DATA OF THE CURRENT POST/PAGE THE
        ////  POST LIST IS DISPLAYED ON.
        $post_obj = $this->APL_get_post_attr($presetObj->_postTax);
        
        //// EXCLUDE CURRENT POST FROM DISPLAYING ON THE POST LIST
        if ($presetObj->_postExcludeCurrent == 'true')
        {
            $excludeList = $post_obj->ID;
        }
        //TODO
        //$presetObj->before = APLInternalShortcodeReplace($presetObj->before, $post, 0);
        //$presetObj->after = APLInternalShortcodeReplace($presetObj->after, $post, 0);
        //// ADD THE POST/PAGE TO THE PARENT ARRAY IF CURRENT PAGE
        ////  IS SELECTED.
        $count = count($presetObj->_postParent);
        if (!isset($presetObj->_postParent))
        {
            $presetObj->_postParent = array();
            $count = count($presetObj->_postParent);
        }
        else if ($count > 0)
        {
            foreach($presetObj->_postParent as $index => $value)
            {
                if ($value === "-1")
                {
                    $presetObj->_postParent[$index] = strval($post_obj->ID);
                }
                else
                {
                    $presetObj->_postParent[$index] = $value;
                }
            }
            
            $presetObj->_postParent = array_unique($presetObj->_postParent);
        }
        
        //// ADD OTHER TAXONOMY TERMS IF INCLUDED IS CHECKED
        $post_obj_post_type = $post_obj->post_type;

        if (isset($presetObj->_postTax->$post_obj_post_type))
        {
            
            $a = $presetObj->_postTax->$post_obj_post_type;
            foreach ($post_obj->taxonomies as $post_taxonomy)
            {
                
                if ($presetObj->postTax->$post_obj_post_type->taxonomies->$post_taxonomy->include == true)
                {
                    $count = count($presetObj->postTax->$post_obj_post_type->taxonomies->$post_taxonomy->terms);
                    foreach ($post_obj->taxonomies->$post_taxonomy as $term)
                    {
                        $presetObj->postTax->$post_obj_post_type->taxonomies->$post_taxonomy->terms[$count] = $term;
                        $count++;
                    }
                    $presetObj->postTax->$post_obj_post_type->taxonomies->$post_taxonomy->terms = array_unique($presetObj->postTax->$post_obj_post_type->taxonomies->$post_taxonomy->terms);
                }
                
            }
            
        }
        

        $APL_posts = $this->APL_Query($presetObj);
        
        
        
        
        
        //STEP 6
        //TODO create a custom message for the dev to use
        //return nothing if no results
        if (count($APL_posts) == 0)
        {
            return "";
        }
        //STEP 7
////// BEFORE //////////////////////////////////////////////////////////////////
        $output = $presetObj->_before;

///// CONTENT //////////////////////////////////////////////////////////////////
        $count = 0;
        foreach ($APL_posts as $APL_post)
        {
            //STEP 8
            $output = $output . APLInternalShortcodeReplace($presetObj->_content,
                                                            $APL_post,
                                                            $count);
            $count++;
        }

        $finalPos = strrpos($output,
                            "[final_end]");
        //if ending exists (the last item where we don't want to add any 
        // more commas or ending brackets or whatever)
        if ($finalPos > 0)
        {
            //Cut everything off at the final position of {final_end}
            $output = substr($output,
                             0,
                             $finalPos);
            //Replace all the other instances of {final_end}, 
            // since we only care about the last one
            $output = str_replace("[final_end]",
                                  "",
                                  $output);
        }

////// AFTER ///////////////////////////////////////////////////////////////////
        $output = $output . $presetObj->_after;

        //STEP 9
        return $output;


    }
    private function APL_get_post_attr($postTax)
    {
        $rtnObj = new stdClass();
        global $post;
        
        $rtnObj->ID = $post->ID;
        $rtnObj->post_type = $post->post_type;
        $taxonomies = $this->APL_get_post_type_taxonomies($rtnObj->post_type);
        foreach ($taxonomies as $taxonomy)
        {
            $terms  = wp_get_post_terms($post->ID, $taxonomy);
            if (!empty($terms))
            {
                $rtnObj->taxonomies->$taxonomy = $terms;
            }
            
        }
        return $rtnObj;
        
    }
    private function APL_Query($presetObj)
    {
        
        $post_type_names = get_post_types('',
                                          'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_names[$value]);
        }
        
        
        $tmp_postTax = (array) $presetObj->_postTax;
        if (empty($presetObj->_postParent) && empty($tmp_postTax))
        {
            //// DEFAULT
            foreach ($post_type_names as $post_type_name)
            {
                $arg_query_parents = array();
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = array(
                'post_type' => $post_type_name,
                'post_status' => $presetObj->_postStatus,
                'nopaging' => true
                );
                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = array();
            }
            
        }
        else
        {
            //// POST PARENTS
            $arg_query_parents = array();
            foreach ($presetObj->_postParent as $parent_index => $parentID)
            {
                $arg_query_parents[$parent_index] = array(
                    'post_type' => get_post_type($parentID),
                    'post_parent' => $parentID,
                    'post_status' => $presetObj->_postStatus,
                    'nopaging' => true
                );
            }

            //// REQUIRED AND SELECTED TAXONOMIES

            $arg_query_reqSel = array();
            foreach ($presetObj->_postTax as $post_type_name => $post_type_value)
            {
                
                $arg_selected = array();
                $arg_required = array();
                $count_req = 0;
                $count_sel = 0;
                foreach($post_type_value->taxonomies as $taxonomy_name => $taxonomy_value)
                {

                    if ($taxonomy_value->require_taxonomy == true)
                    {
                        $arg_required['post_status'] = $presetObj->_postStatus;
                        $arg_required['order'] = $presetObj->_listOrder;
                        $arg_required['orderby'] = $presetObj->_listOrderBy;

                        $arg_required['post_type'] = $post_type_name;
                        $arg_required['tax_query']['relation'] = 'AND';
                        $arg_required['tax_query'][$count_req]['taxonomy'] = $taxonomy_name;
                        $arg_required['tax_query'][$count_req]['field'] = 'id';
                        $arg_required['tax_query'][$count_req]['terms'] = $taxonomy_value->terms;
                        $arg_required['tax_query'][$count_req]['include_children'] = false;
                        $arg_required['tax_query'][$count_req]['operator'] = 'IN';
                        if ($taxonomy_value->require_terms == true)
                        {
                            $arg_required['tax_query'][$count_req]['operator'] = 'AND';
                        }

                        $arg_required['nopaging'] = true;
                        $count_req++;
                    }
                    else
                    {
                        $arg_selected['post_status'] = $presetObj->_postStatus;
                        $arg_selected['order'] = $presetObj->_listOrder;
                        $arg_selected['orderby'] = $presetObj->_listOrderBy;

                        $arg_selected['post_type'] = $post_type_name;
                        $arg_selected['tax_query']['relation'] = 'OR';
                        $arg_selected['tax_query'][$count_sel]['taxonomy'] = $taxonomy_name;
                        $arg_selected['tax_query'][$count_sel]['field'] = 'id';
                        $arg_selected['tax_query'][$count_sel]['terms'] = $taxonomy_value->terms;
                        $arg_selected['tax_query'][$count_sel]['include_children'] = false;
                        $arg_selected['tax_query'][$count_sel]['operator'] = 'IN';
                        if ($taxonomy_value->require_terms == true)
                        {
                            $arg_selected['tax_query'][$count_req]['operator'] = 'AND';
                        }

                        $arg_selected['nopaging'] = true;
                        $count_sel++;
                    }

                }

                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = $arg_required;
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = $arg_selected;

            }
        }
        
        
         
        
        
        //// GET WP_QUERIES
        foreach ($arg_query_reqSel as $post_type_name => $post_type_query)
        {
            //$a1 = $post_type_query['selected_taxonomy'];
            
            $APL_Query_selected = new WP_Query($post_type_query['selected_taxonomy']);
            $APL_Query_required = new WP_Query($post_type_query['required_taxonomy']);
            
            
            
            $posts_selected[$post_type_name] = $APL_Query_selected->posts;
            $posts_required[$post_type_name] = $APL_Query_required->posts;
        }
        foreach ($arg_query_parents as $index => $arg_query_parent)
        {
            //$count = count($APL_Query_parents[$arg_query_parent['post_type']]);
            $APL_Query_parents = new WP_Query($arg_query_parent);
            
            
            $count = count($posts_parents[$arg_query_parent['post_type']]);
            foreach ($APL_Query_parents->posts as $post_parent)
            {
                
                $posts_parents[$arg_query_parent['post_type']][$count] = $post_parent;

                $count++;
                
            }
            //$posts_parents[$arg_query_parent['post_type']] = array_unique($posts_parents[$arg_query_parent['post_type']]);
        }
        //// MERGE POSTS
        $rtnPosts = array();
        $tmp_posts = array();
        foreach ($post_type_names as $post_type_name)
        {
            
            $tmp_count = 0;
            if (!empty ($posts_selected[$post_type_name]))
            {
                if (empty ($posts_required[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_selected[$post_type_name];
                }
                else 
                {
                    foreach ($posts_required[$post_type_name] as $post_req)
                    {
                        foreach ($posts_selected[$post_type_name] as $post_sel)
                        {
                            if ($post_req->ID == $post_sel->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $post_req;
                                $tmp_count++;
                            }
                        }
                    }
                }

            }
            else if (!empty ($posts_required[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $posts_required[$post_type_name];
            }
        }
        
        
        $rtnPosts = $tmp_posts;
        $tmp_posts = array();
        foreach($post_type_names as $post_type_name)
        {
            $tmp_count = 0;
            if (!empty($posts_parents[$post_type_name]))
            {
                if(empty($rtnPosts[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_parents[$post_type_name];
                }
                else
                {
                    foreach($rtnPosts[$post_type_name] as $post_rtn)
                    {
                        foreach($posts_parents[$post_type_name] as $post_par)
                        {
                            if ($post_par->ID == $post_rtn->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $parent_post->ID;
                                $tmp_count++;
                            }
                        }
                    }
                }
            }
            
            else if (!empty($rtnPosts[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $rtnPosts[$post_type_name];
            }
            
        }
        
        $rtnPosts = $tmp_posts;
        
        //COMBINE POSTS FROM OTHER POST TYPES
        $tmp_posts = array();
        $tmp_count = 0;
        $sort_post_type_array = array();
        foreach ($rtnPosts as $post_type_name => $post_type_posts)
        {
            $sort_post_type_array[count($sort_post_type_array)] = $post_type_name;
            foreach ($post_type_posts as $post)
            {
                $tmp_posts[$tmp_count] = $post;
                $tmp_count++;
            }
        }
        $rtnPosts = $tmp_posts;
        
        //// SORT
        //THIS IS SIMPLE BUT EFFECTIVE WAY TO SORT ALL THE POSTS
        $tmp_posts = array();
        $tmp_count = 0;
        
        $ex_arg_query = array();
        $ex_arg_query['nopaging'] = true;
        $ex_arg_query['post_type'] = $sort_post_type_array;
        $ex_arg_query['post_status'] = $presetObj->_postStatus;
        //$ex_arg_query['ignore_sticky_posts'] = $presetObj->_ignoreStickyPosts;
        $ex_arg_query['order'] = $presetObj->_listOrder;
        $ex_arg_query['orderby'] = $presetObj->_listOrderBy;
        
        $APL_Query = new WP_Query($ex_arg_query);
        foreach ($APL_Query->posts as $post)
        {
            foreach ($rtnPosts as $rtnPost)
            {
                if ($post->ID == $rtnPost->ID)
                {
                    $tmp_posts[$tmp_count] = $post;
                    $tmp_count++;
                }
            }
        }
        $rtnPosts = $tmp_posts;
        
        if ($presetObj->_listAmount == -1)
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    count($rtnPosts));
        }
        else
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    $presetObj->_listAmount);
        }
        
        return $rtnPosts;
    }
    
    

}

/**
 * <p><b>Desc:</b> NEED MORE RESEARCH TO FULLY UNDERSTAND.</p> 
 * @param none
 * @return string
 * 
 * @since 0.1.0
 * 
 * 1) 
 * <ol>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * <li value=""></li>
 * </ol>
 */
function APLInternalShortcodeReplace($str,
                                     $page,
                                     $count)
{
    //not much left of this array, since there's so little post data that I can still just grab unmodified
    $SCList = array("[ID]", "[post_name]", "[guid]", "[post_content]", "[comment_count]");

    $l = count($SCList);
    for ($i = 0;
            $i < $l;
            $i++)
    {//loop through all possible shortcodes
        $scName = substr($SCList[$i],
                         1,
                         count($SCList[$i]) - 2);
        $str = str_replace($SCList[$i],
                           $page->$scName,
                           $str);
    }
    //post_author requires an extra function call to convert the
    // userID into a name so we can't do it in the loop above
    $str = str_replace("[post_author]",
                       get_userdata($page->post_author)->user_login,
                                    $str);
    $str = str_replace("[post_permalink]",
                       get_permalink($page->ID),
                                     $str);
    $str = str_replace("[post_title]",
                       htmlspecialchars($page->post_title),
                                        $str);

    $postCallback = new APLCallback();
    $postCallback->itemCount = $count;
    $postCallback->page = $page;

    $str = preg_replace_callback('#\[ *item_number *(offset=[\'|\"]([^\'\"]*)[\'|\"])? *(increment=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postCountCallback'),
                                 $str);

////// POST DATA & MODIFIED ////////////////////////////////////////////////////
    $postCallback->curDate = $page->post_date; //change the curDate param and run the regex replace for each type of date/time shortcode
    $str = preg_replace_callback('#\[ *post_date *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postDateCallback'),
                                 $str);
    $postCallback->curDate = $page->post_date_gmt;
    $str = preg_replace_callback('#\[ *post_date_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postDateCallback'),
                                 $str);
    $postCallback->curDate = $page->post_modified;
    $str = preg_replace_callback('#\[ *post_modified *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postDateCallback'),
                                 $str);
    $postCallback->curDate = $page->post_modified_gmt;
    $str = preg_replace_callback('#\[ *post_modified_gmt *(format=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postDateCallback'),
                                 $str);

    if (preg_match('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                   $str))
    {

        $str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                     array(&$postCallback, 'postExcerptCallback'),
                                     $str);

        /* if($page->post_excerpt == ""){//if there's no excerpt applied to the post, extract one
          //$postCallback->pageContent = strip_tags($page->post_content);
          $str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
          }else{//if there is a post excerpt just use it and don't generate our own
          $str = preg_replace('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', $page->post_excerpt, $str);
          } */
    }

    $postCallback->post_type = $page->post_type;
    $postCallback->post_id = $page->ID;
    $str = preg_replace_callback('#\[ *post_pdf *\]#',
                                 array(&$postCallback, 'postPDFCallback'),
                                 $str);

    if (current_theme_supports('post-thumbnails'))
    {
        $arr = wp_get_attachment_image_src(get_post_thumbnail_id($page->ID),
                                                                 'single-post-thumbnail');
        $str = str_replace("[post_thumb]",
                           $arr[0],
                           $str);
    }

    $postCallback->page = $page;
    $str = preg_replace_callback('#\[ *post_meta *(name=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postMetaCallback'),
                                 $str);
    $str = preg_replace_callback('#\[ *post_categories *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postCategoriesCallback'),
                                 $str);
    $str = preg_replace_callback('#\[ *post_tags *(delimeter=[\'|\"]([^\'\"]*)[\'|\"])? *(links=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postTagsCallback'),
                                 $str);
    $str = preg_replace_callback('#\[ *post_comments *(before=[\'|\"]([^\'\"]*)[\'|\"])? *(after=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'commentCallback'),
                                 $str);
    $str = preg_replace_callback('#\[ *post_parent *(link=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'postParentCallback'),
                                 $str);

    $str = preg_replace_callback('#\[ *php_function *(name=[\'|\"]([^\'\"]*)[\'|\"])? *(param=[\'|\"]([^\'\"]*)[\'|\"])? *\]#',
                                 array(&$postCallback, 'functionCallback'),
                                 $str);

    return $str;
}

?>
