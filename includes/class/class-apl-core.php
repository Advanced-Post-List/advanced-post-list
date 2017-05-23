<?php
/**
 * APL Core Class
 *
 * Core object to Advanced Post List
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.1.0
 */

/**
 * APL Core
 *
 * Main Properties & Methods for Advanced Post List plugin.
 *
 * @since 0.1.0
 * @since 0.2.0
 * @since 0.3.0
 */
class APL_Core {
	// Varibles
	// ???MIGHT WANT TO ADD VERSION TO OPTIONS DB.
	/**
	 * Stores error message when error occurs.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string
	 */
	private $_error;

	/**
	 * Summary.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Change name $_errorLog to $_error_log
	 * @access private
	 * @var string
	 */
	private $_error_log;

	/**
	 * Summary.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Changed name $_APL_OPTION_NAME to $_option_name
	 * @access public
	 * @var string
	 */
	private $_option_name = 'APL_Options';

	/**
	 * Summary.
	 *
	 * @since 0.3.0
	 * @access public
	 * @var array
	 */
	private $_remove_duplicates = array();

	// PHP Doc WP Template.
	/**
	 * Summary.
	 *
	 * Description.
	 *
	 * @since x.x.x
	 * @access (for functions: only use if private)
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param type $var Description.
	 * @param type $var Optional. Description. Default.
	 * @return type Description.
	 */

	/**
	 * Core Constructor
	 *
	 * Constructor for APL_Core functionality.
	 *
	 * STEP 1: Set plugin file data/properties.
	 * STEP 2: Check database version with the current file version.
	 * STEP 3: Register activation, deactivation, and un-install hooks with WordPress.
	 * STEP 4: Add Shortcode to WordPress action hooks.
	 * STEP 5: If the current user has admin rights, do **Step 6**.
	 * STEP 6: Add APL's menu to WordPress 'admin_menu' action hook and
	 *         APL's initial admin action hooks to WordPress 'admin_menu'
	 *         action hook.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Refined how version checking was performed.
	 * @access public
	 *
	 * @param string $file Main plugin file.
	 */
	public function __construct( $file ) {
		// STEP 1.
		$this->_define_constants( $file );
		$this->_requires();

		// STEP 2.
		/***** DATABASE *****/
		$options = $this->apl_options_load();
		if ( isset( $options['version'] ) ) {
			/***** UPGRADES *****/

			/*
			 * Put upgrade database functions in here. Not before.
			 *     Ex. APL_upgrade_to_XXX().
			 */
			if ( version_compare( $options['version'], APL_VERSION, '<' ) ) {
				$options = $this->_update( $options );
			}
		}

		/***** ACTION & FILTERS HOOKS *****/

		// STEP 3.
		add_action( 'widgets_init', array( $this, 'hook_action_widget_init' ) );
		// STEP 4.
		add_shortcode( 'post_list', array( $this, 'hook_shortcode_post_list' ) );
		// STEP 5.
		if ( is_admin() ) {
			// STEP 6.
			add_action( 'admin_menu', array( $this, 'hook_action_admin_menu' ) );
			// STEP 7.
			add_action( 'admin_init', array( $this, 'hook_action_admin_init' ) );

			/***** ACTIVATE/DE-ACTIVATE/UNINSTALL HOOKS *****/
			$file_dir = APL_DIR . 'advanced-post-list/advanced-post-list.php';
			register_activation_hook( $file_dir, array( 'APL_Core', 'hook_activation' ) );
			register_deactivation_hook( $file_dir, array( 'APL_Core', 'hook_deactivation' ) );
			register_uninstall_hook( $file_dir, array( 'APL_Core', 'hook_uninstall' ) );
		}
	}

	/**
	 * Define APL Constants.
	 *
	 * Defines all the constants for APL.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 *
	 * @param string $plugin_file Main plugin file.
	 */
	private function _define_constants( $plugin_file ) {
		/*
		 * Get plugin-file-data from advanced-post-list.php, and grab
		 * the plugin's meta default_headers.
		 */
		$default_headers = array(
			'Name'    => 'Plugin Name',
			'Slug'    => 'Text Domain',
			'Version' => 'Version',
		);
		$plugin_data = get_file_data( $plugin_file, $default_headers );

		/**
		 * APL Display Name.
		 *
		 * @since 0.4.0
		 * @var string APL_NAME 'Advanced Post List'.
		 */
		define( 'APL_NAME', $plugin_data['Name'] );

		/**
		 * APL Slug.
		 *
		 * @since 0.4.0
		 * @var string APL_SLUG 'advanced-post-list'.
		 */
		define( 'APL_SLUG', $plugin_data['Slug'] );

		/**
		 * Version Number.
		 *
		 * @since 0.4.0
		 * @var string APL_VERSION '1.2.3'.
		 */
		define( 'APL_VERSION', $plugin_data['Version'] );

		/**
		 * URL Location.
		 *
		 * @since 0.4.0
		 * @var string APL_URL 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/'.
		 */
		define( 'APL_URL', plugin_dir_url( $plugin_file ) );

		/**
		 * Directory Path.
		 *
		 * @since 0.4.0
		 * @var string APL_DIR 'C:\xampp\htdocs\wordpress\wp-content\plugins\advanced-post-list/'.
		 */
		define( 'APL_DIR', plugin_dir_path( $plugin_file ) );
	}

	/**
	 * Add Required Files.
	 *
	 * Adds the required files to include.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @global string APL_DIR APL file path.
	 */
	private function _requires() {
		require_once( APL_DIR . 'includes/class/class-apl-preset-db.php' );
		require_once( APL_DIR . 'includes/class/class-apl-preset.php' );
		require_once( APL_DIR . 'includes/class/class-apl-widget.php' );
		require_once( APL_DIR . 'includes/class/class-apl-query.php' );
		require_once( APL_DIR . 'includes/class/class-apl-updater.php' );
		// OLD - Remove between 0.4 - 0.6.
		require_once( APL_DIR . 'includes/class/old-APLPresetDbObj.php' );
		require_once( APL_DIR . 'includes/class/old-APLPresetObj.php' );

		// TODO Move to Admin only function/method.
		require_once( APL_DIR . 'admin/import.php' );
		require_once( APL_DIR . 'admin/export.php' );
	}

	/**
	 * APL Updater.
	 *
	 * Updater method for handling the Upgrader Class.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 *
	 * @param object $options APL Options.
	 * @return object APL Options.
	 */
	private function _update( $options ) {
		$preset_db = new APL_Preset_Db( 'default' );
		$updater = new APL_Updater( $options['version'], $preset_db, $options );
		// IN THIS CASE, BOTH MUST HAVE VALUES FILLED.
		if ( null === $updater->options || null === $updater->preset_db ) {
			return $options;
		} else {
			$preset_db = $updater->preset_db;
			$preset_db->options_save_db();
			$options = $updater->options;
			$this->apl_options_save( $options );

			return $options;
		}
	}

	/**
	 * Admin init.
	 *
	 * Adds plugin action hooks to admin_init for loading up when the user
	 * has admin rights.
	 *
	 * STEP 1 - Add action hooks for AJAX.
	 * STEP 2 - De-register scripts and style for a clean register.
	 * STEP 3 - Load APLOptions to load selected JQuery UI theme.
	 * STEP 4 - Register scripts to be enqueued.
	 * STEP 5 - Register styles.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Added export, import, and save settings ajax functions.
	 * @since 0.3.0 - Added wp_enqueue_script & wp_enqueue_style to place them
	 *                in separate files properly. Also added a theme setting
	 *                to be loaded.
	 * @access public
	 *
	 * @see apl_import in '/advanced-post-list/admin/import.php'
	 * @see apl_export in '/advanced-post-list/admin/export.php'
	 * @link URL
	 */
	public function hook_action_admin_init() {
		/*
		 * ************** AJAX ACTION HOOKS ***************************
		 */

		// STEP 1.
		add_action(
			'wp_ajax_APL_handler_save_preset',
			array( $this, 'hook_action_ajax_save_preset' )
		);
		add_action(
			'wp_ajax_APL_handler_delete_preset',
			array( $this, 'hook_action_ajax_delete_preset' )
		);
		add_action(
			'wp_ajax_APL_handler_restore_preset',
			array( $this, 'hook_action_ajax_restore_preset' )
		);

		add_action(
			'wp_ajax_APL_handler_export',
			array( $this, 'hook_action_ajax_export' )
		);
		add_action(
			'wp_ajax_APL_handler_import',
			array( $this, 'hook_action_ajax_import' )
		);
		add_action(
			'wp_ajax_APL_import',
			'apl_import'
		);
		add_action(
			'wp_ajax_APL_export',
			'apl_export'
		);

		add_action(
			'wp_ajax_APL_handler_save_settings',
			array( $this, 'hook_action_ajax_save_settings' )
		);

		/*
		 * ************** REMOVE SCRIPTS & STYLES *********************
		 */

		// Step 2.
		wp_deregister_script( 'apl-admin-js' );
		wp_deregister_script( 'apl-admin-ui-js' );
		wp_deregister_script( 'apl-jquery-ui-multiselect-js' );

		wp_deregister_style( 'apl-admin-css' );
		wp_deregister_style( 'apl-admin-ui-css' );
		wp_deregister_style( 'apl-jquery-ui-multiselect' );
		wp_deregister_style( 'apl-jquery-ui-multiselect-css' );
		wp_deregister_style( 'apl-jquery-ui-multiselect-filter-css' );

		// Step 3.
		$otions = $this->apl_options_load();
		if ( ! isset( $otions['jquery_ui_theme'] ) ) {
			$otions['jquery_ui_theme'] = 'overcast';
			$this->apl_options_save( $otions );
		}

		/*
		 * ************** REGISTER SCRIPTS ****************************
		 */

		// Step 4.
		$script_deps = array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-dialog' );
		wp_register_script(
			'apl-admin-js',
			plugins_url() . '/advanced-post-list/admin/js/admin.js',
			$script_deps,
			APL_VERSION,
			false
		);

		$script_deps = array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-accordion', 'jquery-ui-button','jquery-ui-dialog', 'jquery-ui-tabs' );
		wp_register_script(
			'apl-admin-ui-js',
			plugins_url() . '/advanced-post-list/admin/js/admin-ui.js',
			$script_deps,
			APL_VERSION,
			false
		);

		$script_deps = array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' );
		wp_register_script(
			'apl-jquery-ui-multiselect-js',
			plugins_url() . '/advanced-post-list/admin/js/jquery.multiselect.min.js',
			$script_deps,
			APL_VERSION,
			false
		);

		$script_deps = array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' );
		wp_register_script(
			'apl-jquery-ui-multiselect-filter-js',
			plugins_url() . '/advanced-post-list/admin/js/jquery.multiselect.filter.min.js',
			$script_deps,
			APL_VERSION,
			false
		);

		/*
		 * ************** REGISTER STYLES *****************************
		 */

		// Step 5.
		wp_enqueue_style(
			'apl-admin-css',
			plugins_url() . '/advanced-post-list/admin/css/admin.css',
			false,
			APL_VERSION,
			false
		);

		wp_enqueue_style(
			'apl-admin-ui-css',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/' . $otions['jquery_ui_theme'] . '/jquery-ui.css',
			false,
			APL_VERSION,
			false
		);

		wp_enqueue_style(
			'apl-jquery-ui-multiselect-css',
			plugins_url() . '/advanced-post-list/admin/css/jquery.multiselect.css',
			false,
			APL_VERSION,
			false
		);
		wp_enqueue_style(
			'apl-jquery-ui-multiselect-filter-css',
			plugins_url() . '/advanced-post-list/admin/css/jquery.multiselect.filter.css',
			false,
			APL_VERSION,
			false
		);
	}

	/**
	 * Summary.
	 *
	 * Handles the activation method when the plugin is first activated.
	 *
	 * STEP 1 - Load APLOptions.
	 * STEP 2 - If no options was loaded then install options to be loaded.
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function hook_activation() {
		// Step 1.
		$options = get_option( 'APL_Options' );
		// Step 2.
		if ( false === $options ) {
			$options = array();
		}
		if ( ! isset( $options['version'] ) ) {
			$options['version'] = APL_VERSION;
		}
		if ( ! isset( $options['preset_db_names'] ) ) {
			$options['preset_db_names'] = array(
				0 => 'default',
				);
		}
		if ( ! isset( $options['delete_core_db'] ) ) {
			$options['delete_core_db'] = false;
		}
		if ( ! isset( $options['jquery_ui_theme'] ) ) {
			$options['jquery_ui_theme'] = 'overcast';
		}
		if ( ! isset( $options['error'] ) ) {
			$options['error'] = '';
		}
		update_option( 'APL_Options', $options );
	}

	/**
	 * Summary.
	 *
	 * Handles the deactivation method when plugin is deactivated
	 *
	 * STEP 1 - Load Options from database.
	 * STEP 2 - If user has delete database set to true OR APLOption exists but
	 *          delete_core_db is not set, then delete all options.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Added delete_option('APL_preset_db-default') for deleting
	 *                preset database data.
	 * @access public
	 */
	public function hook_deactivation() {
		// STEP 1.
		$options = get_option( 'APL_Options' );
		// STEP 2.
		if ( true === $options['delete_core_db'] || ( false !== $options && ! isset( $options['delete_core_db'] ) ) ) {
			delete_option( 'APL_Options' );
			delete_option( 'APL_preset_db-default' );
		}
	}

	/**
	 * Summary.
	 *
	 * Handles the uninstall method when plugin is uninstalled.
	 *
	 * STEP 1 - Delete APLOptions/Core settings from WordPress.
	 * STEP 2 - Delete preset database options.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Changed to delete all plugin data, whether 'delete plugin
	 *                data upon deactivation' is set or not.
	 * @access public
	 */
	public function hook_uninstall() {
		// Step 1.
		delete_option( 'APL_Options' );
		// Step 2.
		// Alt uninstall that uses the 'delete upon deactivation' setting.
		delete_option( 'APL_preset_db-default' );
	}

	/**
	 * Summary.
	 *
	 * Gets APLOptions from WordPress database and send the option data back if any.
	 *
	 * STEP 1 - Get APLOptions from WordPress Database or get false if options
	 *          doesn't exist.
	 * STEP 2 - If Options exists, then return object. Otherwise return false.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 *
	 * @return object APL option settings.
	 */
	private function apl_options_load() {
		// New Name ( get_options ).
		// Step 1.
		$options = get_option( $this->_option_name );

		// Step 2.
		if ( false !== $options ) {
			return $options;
		} else {
			return $this->apl_options_default();
		}
	}

	/**
	 * Summary.
	 *
	 * Description.
	 *
	 * STEP 1 - If option data (param) exists, save option data to
	 *               WordPress database.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 *
	 * @param object $options Core option settings.
	 */
	private function apl_options_save( $options ) {
		// New name ( set_options ).
		// STEP 1.
		if ( isset( $options ) ) {
			update_option( $this->_option_name, $options );
		}
	}

	/**
	 * Summary.
	 *
	 * Sets options to default values.
	 *
	 * STEP 1 - Set options as an array.
	 * STEP 2 - Add default values to options.
	 * STEP 3 - Return Options.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @return object Core option settings
	 */
	private function apl_options_default() {
		// New name ( default_options ).
		// Step 1.
		$options = array();
		// Step 2.
		$options['version']          = APL_VERSION;
		$options['preset_db_names']  = array( 'default' );
		$options['delete_core_db']   = true;
		$options['jquery_ui_theme']  = 'overcast';
		$options['default_exit']     = false;
		$options['default_exit_msg'] = '<p>Sorry, but no content is available at this time.</p>';
		$options['error']            = '';

		// Step 3.
		return $options;
	}

	/**
	 * Summary.
	 *
	 * Adds the plugins widget to WordPress.
	 *
	 * STEP 1 - Register widget.
	 *
	 * @since  0.1.0
	 *
	 * @see APL_Widget class 'advanced-post-list/includes/class/class-apl-widget.php
	 * @link URL
	 */
	public function hook_action_widget_init() {
		register_widget( 'APL_Widget' );
	}

	/**
	 * Admin Menu.
	 *
	 * Adds the plugin's menu links to the WordPress.
	 *
	 * STEP 1 - Add a submenu to WordPress settings menu.
	 * STEP 2 - Add action scripts to that menu page.
	 *
	 * @todo create APL's own menu once addition pages are available
	 *
	 * @since 0.1.0
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 */
	public function hook_action_admin_menu() {
		// STEP 1.
		$apl_admin_page_hook = add_submenu_page(
			'options-general.php',
			'Advanced Post List',
			'Advanced Post List',
			'manage_options',
			'advanced-post-list',
			array( $this, 'admin_page' )
		);
		// STEP 2.
		add_action(
			'admin_print_styles-' . $apl_admin_page_hook,
			array( $this, 'admin_head' )
		);
		// add_filter( 'contextual_help', 'kalinsPost_contextual_help', 10, 3 );
	}

	/**
	 * Admin Header.
	 *
	 * Admin head section that is loaded before the body, and carries scripts
	 * and styles that are normally loaded before the body content.
	 *
	 * STEP 1 - Add JS files to WordPress script queue list.
	 * STEP 2 - Add CSS files to WordPress script queue list.
	 * STEP 3 - Get Preset Database data.
	 * STEP 4 - Get Post Type and Taxonomy structure.
	 * STEP 5 - Get Taxonomy and Terms structure.
	 * STEP 6 - Get Pages for selecting a parent page, and set them in a hierarchical fashion.
	 * STEP 7 - Store data in variables.
	 * STEP 8 - Send variables to script.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 - Added functions to queue scripts and styles on WordPress.
	 *
	 * @see APL_Preset_Db Class
	 * @see Function/method/class relied on
	 * @link URL
	 */
	public function admin_head() {
		// Step 1.
		/***** ADD SCRIPTS TO QUEUE LIST *****/
		wp_enqueue_script( 'apl-admin-js' );
		wp_enqueue_script( 'apl-admin-ui-js' );
		wp_enqueue_script( 'apl-jquery-ui-multiselect-js' );
		wp_enqueue_script( 'apl-jquery-ui-multiselect-filter-js' );

		// Step 2.
		/***** ADD STYLES TO QUEUE LIST *****/

		wp_enqueue_style( 'apl-admin-css' );
		wp_enqueue_style( 'apl-admin-ui-css' );
		wp_enqueue_style( 'apl-jquery-ui-multiselect-css' );
		wp_enqueue_style( 'apl-jquery-ui-multiselect-filter-css' );

		// Step 3.
		/***** GET AND STORE PLUGIN DATA *****/
		$preset_db = new APL_Preset_Db( 'default' );
		// Step 4.
		$post_type_taxonomies = $this->apl_get_post_types( array( 'taxonomies' ) );
		// Step 5.
		$tax_terms = $this->apl_get_taxonomies( '', array( 'terms' ) );
		// Step 6.
		$post_type_hierarchical = $this->apl_get_post_types( array( 'hierarchical' ) );

		$post_types = $this->apl_get_post_types();

		// Step 7.
		$apl_admin_settings = array(
			'plugin_url'         => APL_URL,
			'savePresetNonce'    => wp_create_nonce( 'APL_handler_save_preset' ),
			'deletePresetNonce'  => wp_create_nonce( 'APL_handler_delete_preset' ),
			'restorePresetNonce' => wp_create_nonce( 'APL_handler_restore_preset' ),
			'exportNonce'        => wp_create_nonce( 'APL_handler_export' ),
			'importNonce'        => wp_create_nonce( 'APL_handler_import' ),
			'saveSettingsNonce'  => wp_create_nonce( 'APL_handler_save_settings' ),
			'presetDb'           => json_encode( (array) $preset_db->_preset_db ),
			'postTax'            => $post_type_taxonomies,
			'taxTerms'           => $tax_terms,
		);
		$apl_admin_ui_settings = array(
			//'post_type_amount' => sizeof((array) $post_taxonomies),
			'post_types'              => $post_types,
			'postTax_parent_selector' => $post_type_hierarchical,
			'postTax'                 => $post_type_taxonomies,
			'taxTerms'                => $tax_terms,
		);

		// Step 8.
		/***** SEND PLUGIN DATA TO SCRIPTS *****/
		wp_localize_script( 'apl-admin-js', 'apl_admin_settings', $apl_admin_settings );
		wp_localize_script( 'apl-admin-ui-js', 'apl_admin_ui_settings', $apl_admin_ui_settings );
	}

	/*
	 *
	 * @param array $param1
	 * array(
	 * 0 => labels,
	 * 1 => labels->singular_name,
	 * 2 => hierarchical,
	 * 3 => taxonomies //may add extra support to get other varibles. For now
	 *                  // I'll just add the names as the default has it.
	stdClass Object
	(
		[labels] => stdClass Object(
			[name] => string
			[singular_name] => string
			[add_new] => string
			[add_new_item] => string
			[edit_item] => string
			[new_item] => string
			[view_item] => string
			[search_items] => string
			[not_found] => string
			[not_found_in_trash] => string
			[parent_item_colon] =>
			[all_items] => string
			[menu_name] => string
			[name_admin_bar] => string
		)
		[description] => string
		[publicly_queryable] => boolean
		[exclude_from_search] => boolean
		[capability_type] => string
		[map_meta_cap] => boolean
		[_builtin] => boolean
		[_edit_link] => string
		[hierarchical] => boolean
		[public] => boolean
		[rewrite] => boolean
		[has_archive] => boolean
		[query_var] => boolean
		[register_meta_box_cb] => Null
		[taxonomies] => Array(
			(CHANGED) - Gets taxonomy Attributes
		)
		[show_ui] => boolean
		[menu_position] => Null
		[menu_icon] => Null
		[permalink_epmask] => int
		[can_export] => boolean
		[show_in_nav_menus] => boolean
		[show_in_menu] => boolean
		[show_in_admin_bar] => boolean
		[name] => string
		[cap] => stdClass Object(
			[edit_post] => string
			[read_post] => string
			[delete_post] => string
			[edit_posts] => string
			[edit_others_posts] => string
			[publish_posts] => string
			[read_private_posts] => string
			[read] => string
			[delete_posts] => string
			[delete_private_posts] => string
			[delete_published_posts] => string
			[delete_others_posts] => string
			[edit_private_posts] => string
			[edit_published_posts] => string
		)
		[label] => string
	)
	 */
	/**
	 * Get Post Types.
	 *
	 * Get post type object variables by attr_names array.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see get_post_type_object() is located in wp-includes/post.php.
	 * @link https://codex.wordpress.org/Function_Reference/get_post_type_object
	 *
	 * @param array $attr_names Array keys to return.
	 * @return object Post Type objects.
	 */
	private function apl_get_post_types( $attr_names = array() ) {
		$rtn_obj = array();

		$post_type_names = get_post_types( '', 'names' );
		// Step 2.
		$skip_post_types = array( 'attachment', 'revision', 'nav_menu_item' );
		foreach ( $skip_post_types as $post_type_name ) {
			unset( $post_type_names[ $post_type_name ] );
		}
		unset( $post_type_name );
		unset( $skip_post_types );

		if ( empty( $attr_names ) ) {
			return $post_type_names;
		}

		foreach ( $post_type_names as $post_type_name ) {
			$rtn_obj[ $post_type_name ] = new stdClass();
			if ( ! empty( $attr_names ) ) {
				$post_type_object = get_post_type_object( $post_type_name );

				foreach ( $attr_names as $attr_name ) {
					$delimiter_pos = strpos( $attr_name, '->' );
					if ( false !== $delimiter_pos ) {
						$attr_name_dereference = substr( $attr_name, ( $delimiter_pos + 2 ) );
						$attr_name = substr( $attr_name, 0, $delimiter_pos );
					}
					unset( $delimiter_pos );

					switch ( $attr_name ) {
						case 'posts':
							break;
						case 'taxonomies':
							if ( ! empty( $attr_name_dereference ) ) {
								$terms_delimeter_pos_start = strpos( $attr_name_dereference, 'terms->[' );
								if ( false !== $terms_delimeter_pos_start ) {
									$terms_delimeter_pos_length = strpos( $attr_name_dereference, '"]"' );
									$terms_delimeter_pos_length += 2;
									$terms_delimeter_pos_length -= $terms_delimeter_pos_start;

									$terms_attr_names = substr(
										$attr_name_dereference,
										$terms_delimeter_pos_start,
										$terms_delimeter_pos_length
									);

									$attr_name_dereference = substr_replace(
										$attr_name_dereference,
										'terms',
										$terms_delimeter_pos_start,
										$terms_delimeter_pos_length
									);

									$taxonomies_attr = json_decode( $attr_name_dereference );
									foreach ( $taxonomies_attr as &$taxonomies_attr_name ) {
										if ( 'terms' === $taxonomies_attr_name ) {
											$taxonomies_attr_name = $terms_attr_names;
										}
									}
								} else {
									$taxonomies_attr = json_decode( $attr_name_dereference );
								}

								$rtn_obj[ $post_type_name ]->$attr_name = APL_Core::apl_get_taxonomies( $post_type_name, $taxonomies_attr );
								unset( $attr_name_dereference );
							} else {
								$rtn_obj[ $post_type_name ]->$attr_name = APL_Core::apl_get_taxonomies( $post_type_name, '' );
							}
							break;
						case 'labels':
							if ( ! empty( $attr_name_dereference ) ) {
								$rtn_obj[ $post_type_name ]->$attr_name->$attr_name_dereference = $post_type_object->$attr_name->$attr_name_dereference;
								unset( $attr_name_dereference );
							} else {
								$rtn_obj[ $post_type_name ]->$attr_name = $post_type_object->$attr_name;
							}
							break;
						case 'cap':
							if ( ! empty( $attr_name_dereference ) ) {
								$rtn_obj[ $post_type_name ]->$attr_name->$attr_name_dereference = $post_type_object->$attr_name->$attr_name_dereference;
								unset( $attr_name_dereference );
							} else {
								$rtn_obj[ $post_type_name ]->$attr_name = $post_type_object->$attr_name;
							}
							break;
						default:
							$rtn_obj[ $post_type_name ]->$attr_name = $post_type_object->$attr_name;
							break;
					}// End switch().
				}// End foreach().
				unset( $attr_name );
				unset( $post_type_object );
			}// End if().
		}// End foreach().
		unset( $post_type_name );

		return $rtn_obj;
	}

	/*
	stdClass Object
	(
		[hierarchical] => boolean
		[update_count_callback] => string
		[rewrite] => array()
		[query_var] => string
		[public] => boolean
		[show_ui] => boolean
		[show_tagcloud] => boolean
		[_builtin] => boolean
		[labels] => stdClass Object
		(
			[name] =>  string
			[singular_name] => string
			[search_items] => string
			[popular_items] =>
			[all_items] => string
			[parent_item] => string
			[parent_item_colon] => string
			[edit_item] => string
			[view_item] => string
			[update_item] => string
			[add_new_item] => string
			[new_item_name] => string
			[separate_items_with_commas] =>
			[add_or_remove_items] =>
			[choose_from_most_used] =>
			[menu_name] =>  string
			[name_admin_bar] => string
		)
		[show_in_nav_menus] => boolean
		[cap] => stdClass Object
		(
			[manage_terms] => string
			[edit_terms] => string
			[delete_terms] => string
			[assign_terms] => string
		)
		[name] => string
		[object_type] => Array()
		[label] => string
		[terms] => Array() (ADDED) - Can return terms plus attributes.
	)
	 */
	/**
	 * Get Taxonomies.
	 *
	 * Get taxonomy object variables by attr_names array.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see $this->apl_get_terms
	 * @link https://codex.wordpress.org/Function_Reference/get_taxonomy
	 *
	 * @param string $post_type_name Optional. Post type taxonomies belong to.
	 * @param array  $attr_names Optional. Array keys to return.
	 * @return object Taxonomies.
	 */
	private function apl_get_taxonomies( $post_type_name = '', $attr_names = array() ) {
		$rtn_arr = array();
		if ( ! empty( $post_type_name ) ) {
			$taxonomy_names = array();
			$taxonomy_names = get_object_taxonomies( $post_type_name );
			$rtn_arr = array();
			foreach ( $taxonomy_names as $taxonomy_name ) {
				$rtn_arr[ $taxonomy_name ] = $taxonomy_name;
			}
			$taxonomy_names = $rtn_arr;
			unset( $taxonomy_name );
			unset( $rtn_arr );
		} else {
			$taxonomy_names = get_taxonomies( '', 'names' );
		}
		$skip_taxonomies = array( 'post_format', 'nav_menu', 'link_category' );
		foreach ( $skip_taxonomies as $value ) {
			unset( $taxonomy_names[ $value ] );
		}
		unset( $skip_taxonomies );
		unset( $value );

		if ( empty( $attr_names ) ) {
			return $taxonomy_names;
		}
		foreach ( $taxonomy_names as $taxonomy_name ) {

			$rtn_arr[ $taxonomy_name ] = new stdClass();

			if ( ! empty( $attr_names ) ) {
				$taxonomy_object = get_taxonomy( $taxonomy_name );

				foreach ( $attr_names as $attr_name ) {
					$delimiter_pos = strpos( $attr_name, '->' );
					if ( false !== $delimiter_pos ) {
						$attr_name_dereference = substr( $attr_name, ( $delimiter_pos + 2 ) );
						$attr_name = substr( $attr_name, 0, $delimiter_pos );
					}
					unset( $delimiter_pos );

					switch ( $attr_name ) {
						case 'terms':
							if ( ! empty( $attr_name_dereference ) ) {
								$rtn_arr[ $taxonomy_name ]->$attr_name = $this->apl_get_terms( $taxonomy_name, json_decode( $attr_name_dereference ) );
							} else {
								$rtn_arr[ $taxonomy_name ]->$attr_name = $this->apl_get_terms( $taxonomy_name );
							}
							break;
						case 'labels':
							if ( ! empty( $attr_name_dereference ) ) {
								//$rtn_arr[ $taxonomy_name ]->$attr_name = new stdClass();
								$rtn_arr[ $taxonomy_name ]->$attr_name->$attr_name_dereference = $taxonomy_object->$attr_name->$attr_name_dereference;
								unset( $attr_name_dereference );
							} else {
								$rtn_arr[ $taxonomy_name ]->$attr_name = $taxonomy_object->$attr_name;
							}
							break;
						case 'cap':
							if ( ! empty( $attr_name_dereference ) ) {
								//$rtn_arr[ $taxonomy_name ]->$attr_name = new stdClass();
								$rtn_arr[ $taxonomy_name ]->$attr_name->$attr_name_dereference = $taxonomy_object->$attr_name->$attr_name_dereference;
								unset( $attr_name_dereference );
							} else {
								$rtn_arr[ $taxonomy_name ]->$attr_name = $taxonomy_object->$attr_name;
							}
							break;
						default:
							$rtn_arr[ $taxonomy_name ]->$attr_name = $taxonomy_object->$attr_name;
							break;
					}
				}// End foreach().
				unset( $attr_name );
			}// End if().
		}// End foreach().
		unset( $taxonomy_name );

		return $rtn_arr;
	}

	/*
	stdClass Object
	(
		[term_id] => string //TODO CHANGE TO INT
		[name] => string
		[slug] => string
		[term_group] => string //TODO CHANGE TO INT
		[term_taxonomy_id] => string
		[taxonomy] => string
		[description] => string
		[parent] => string //TODO CHANGE TO INT
		[count] => string //TODO CHANGE TO INT
	)
	 *
	 * Taxonomy_Name
	 *
	  $default_args = array(
	  'number'        => (int)        '',         //The maximum number of terms to return.
	  'offset'        => (int)        '',         //The number by which to offset the terms query.
	  'include'       => (array)      array(),    //An array, comma- or space-delimited string of term ids to include in the return array.
	  'exclude'       => (array)      array(),    //An array, comma- or space-delimited string of term ids to exclude in the return array.
	  'exclude_tree'  => (array)      array(),    //NO DOCUMENTATION
	  'orderby'       => (string)     'id',       //Which properties to order by
												  // 'id', 'count', 'name', 'slug', 'term_group', or 'none'
	  'order'         => (string)     'ASC',      //Which direction to orderby
												  // 'ASC' or 'DESC'
	  'hide_empty'    => (boolean)    false,      //Whether to return terms that haven't been used
	  'fields'        => (string)     'ids',      //Which properties to return
												  // 'all', 'ids', 'names', or 'count'
	  'slug'          => (string)     '',         //Returns terms whose "slug" matches this value.
	  'hierarchical'  => (boolean)    true,       //Whether to include terms that have non-empty descendants
	  'name__like'    => (string)     '',         //Returned terms' names will begin with the value of 'name__like', case-insensitive.
	  'pad_counts'    => (boolean)    false,      //If true, count all of the children along with the $terms.
	  'get'           => (string)     '',         //Allow for overwriting 'hide_empty' and 'child_of', which can be done by setting the value to 'all'.
	  'child_of'      => (int)        0,          //Get all descendents of this term.
	  'parent'        => (int)        '',         //Get direct children of this term (only terms whose explicit parent is this value). If 0 is passed, only top-level terms are returned.
	  'cache_domain'  => (string)     'core',     //The 'cache_domain' argument enables a unique cache key to be produced when the query produced by get_terms() is stored in object cache.
	  'search'        => (string)     ''          //Returned terms' names will contain the value of 'search' case-insensitive.
	  );

	 */

	/**
	 * Get Terms.
	 *
	 * Get taxonomy object variables by attr_names array.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see get_terms() in wp-includes/taxonomy.php
	 * @link https://developer.wordpress.org/reference/functions/get_terms/
	 *
	 * @param string $taxonomy_name Optional. Taxonomy terms belong to. Default.
	 * @param array  $attr_names    Optional. Variable keys to return. Default.
	 * @param array  $args          Optional. Simular to $attr_names. Default.
	 * @return object Terms.
	 */
	private function apl_get_terms( $taxonomy_name = '', $attr_names = array(), $args = array() ) {
		$default_args = array(
			'fields'     => 'ids',
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => false,
		);
		if ( empty( $taxonomy_name ) ) {
			$taxonomy_name = APL_Core::apl_get_taxonomies();
		}
		if ( ! empty( $args ) ) {
			foreach ( $args as $arg_name => $arg_value ) {

				if ( empty( $attr_names ) && 'fields' === $arg_name ) {
					$default_args[ $arg_name ] = $arg_value;
				} else {
					$default_args[ $arg_name ] = $arg_value;
				}
			}
			unset( $arg_name );
			unset( $arg_value );
		}
		$args = $default_args;
		unset( $default_args );

		$terms = get_terms( $taxonomy_name, $args );
		if ( 'ids' === $args['fields'] || 'count' === $args['fields'] ) {
			$tmp_terms = array();
			foreach ( $terms as $key => $value ) {
				$tmp_terms[ $key ] = intval( $value );
			}
			unset( $key );
			unset( $value );
			$terms = $tmp_terms;
			unset( $tmp_terms );
		}

		if ( empty( $attr_names ) || 'ids' !== $args['fields'] ) {
			return $terms;
		}

		$rtn_arr = array();

		foreach ( $terms as $key => $term ) {
			$term_object = get_term( $term, $taxonomy_name );

			$rtn_arr[ $key ] = new stdClass();

			if ( ! empty( $attr_names ) ) {
				foreach ( $attr_names as $attr_name ) {
					if ( ! empty( $attr_name ) && isset( $term_object->$attr_name ) ) {
						if ( 'term_id' === $attr_name ||
							 'term_group' === $attr_name ||
							 'parent' === $attr_name ||
							 'count' === $attr_name ) {
							$rtn_arr[ $key ]->$attr_name = intval( $term_object->$attr_name );
						} elseif ( isset( $term_object->$attr_name ) ) {
							$rtn_arr[ $key ]->$attr_name = $term_object->$attr_name;
						}
					}
				}
				unset( $attr_name );
			}
		}
		unset( $key );
		unset( $term );

		return $rtn_arr;
	}

	/*
	$arg_example = array(
		'orderby' => 'title',
		'order' => 'ASC',
		'p' => (int) 0,
		'post_parent' => (int) 0,
		'tax_query' => array(
			array(
				'taxonomy' => 'people',
				'field' => 'slug',
				'terms' => 'bob'
			)
		),
		'post_type' => array('post', 'page', 'custom_post_type_01'), //OR
		'post_type' => 'post',
		'nopaging' => true
	);
	*/

	/*
	array(
		0 => stdClass Object
		{
			[ID] => int 0
			[post_author] => string ''
			[post_date] => string ''
			[post_date_gmt] => string ''
			[post_content] => string ''
			[post_title] => string ''
			[post_excerpt] => string ''
			[post_status] => string ''
			[comment_status] => string ''
			[ping_status] => string ''
			[post_password] => string ''
			[post_name] => string ''
			[to_ping] => string ''
			[pinged] => string ''
			[post_modified] => string ''
			[post_modified_gmt] => string ''
			[post_content_filtered] => string ''
			[post_parent] => int 0
			[guid] => string ''
			[menu_order] => int 0
			[post_type] => string ''
			[post_mime_type] => string ''
			[comment_count] => string '0'
			[filter] => string 'raw'
		}
	)
	*/
	/**
	 * Get Posts.
	 *
	 * Get taxonomy object variables by attr_names array.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see WP_Query class
	 * @link URL
	 *
	 * @param array $attr_names Optional. Description. Default.
	 * @param array $args Optional. Description. Default.
	 * @return object Posts.
	 */
	private function apl_get_posts( $attr_names = array(), $args = array() ) {
		$default = array(
			'orderby'   => 'ID',
			'order'     => 'ASC',
			'post_type' => 'post',
			'nopaging'  => true,
		);

		if ( ! empty( $args ) ) {
			foreach ( $args as $key => $arg ) {
				if ( ! empty( $arg ) ) {
					$default[ $key ] = $arg;
				}
			}
			unset( $arg );
		}
		$args = $default;

		$apl_query = new WP_Query( $args );

		$posts = $apl_query->posts;
		unset( $apl_query );

		$rtn_posts = array();
		$attr_names_count = count( $attr_names );
		if ( empty( $attr_names ) ) {
			$rtn_posts = array();
			foreach ( $posts as $post ) {
				$rtn_posts[ $post->post_name ] = $post->ID;
			}
			unset( $post );
			unset( $posts );
			return $rtn_posts;
		} else {
			foreach ( $posts as $post ) {
				$rtn_posts[ $post->post_name ] = new stdClass();
				if ( ! empty( $attr_names ) ) {
					foreach ( $attr_names as $attr_name ) {
						if ( ! empty( $attr_name ) && isset( $post->$attr_name ) ) {
							$rtn_posts[ $post->post_name ]->$attr_name = $post->$attr_name;
						}
					}
					unset( $attr_name );
				}
				unset( $post );
			}

			unset( $posts );
			return $rtn_posts;
		}
	}

	/**
	 * Admin Page.
	 *
	 * Admin Page to display.
	 *
	 * @since 0.3.0
	 */
	public function admin_page() {
		// Step 1.
		require_once( APL_DIR . 'admin/admin.php' );
	}

	/**
	 * Summary.
	 *
	 * Export Hook function.
	 *
	 * STEP 1 - Check AJAX security value.
	 * STEP 2 - Store default data.
	 * STEP 3 - Store the filename and url export file location.
	 * STEP 4 - Echo that in json string.
	 *
	 * @since 0.2.0
	 *
	 * @return void JSON string to export.
	 */
	public function hook_action_ajax_export() {
		// Step 1.
		check_ajax_referer( 'APL_handler_export' );

		$rtn_data = new stdClass();
		// Step 2.
		$rtn_data->_status = 'success';
		$rtn_data->_error = '';

		// Step 3.
		$rtn_data->filename = $_POST['filename'];

		$preset_db = new APL_Preset_Db( 'default' );
		$temp_export_data = new stdClass();
		$temp_export_data->version = APL_VERSION;
		if ( 'database' === $_POST['export_type'] ) {
			$temp_export_data->presetDbObj = $preset_db;
		} elseif ( 'preset' === $_POST['export_type'] ) {
			$preset_name = $_POST['filename'];
			$rtn_data->filename = 'APL.' . $preset_name . '.' . date( 'Y-m-d' );

			$temp_export_data->preset_db = new stdClass();
			$temp_export_data->preset_db->_preset_db = new stdClass();

			$temp_export_data->preset_db->_preset_db->$preset_name = $preset_db->_preset_db->$preset_name;
		} else {
			$rtn_data->_status = 'failure';
			$rtn_data->_error = 'No \'Import Type\' selected - Unknown error';
		}

		update_option( 'APL_TMP_export_dataOutput', $temp_export_data );

		$rtn_data->action = 'APL_export';
		$rtn_data->_ajax_nonce = wp_create_nonce( 'APL_export' );

		// Step 4.
		echo json_encode( $rtn_data );
	}

	// TODO CREATE AN AJAX FUNCTION TO IMPORT DATA TO THE PLUGIN
	// COULDN'T FIND A WAY TO CARRY THE $_FILES GLOBAL VARIBLE
	// THROUGH .post TO TARGET PHP CODE.
	/**
	 * Summary.
	 *
	 * (Un-used) Handles the AJAX function for importing data. Method used when
	 * jQuery.post is called in javascript for $('#frmImport').submit().
	 *
	 * STEP 1 - Check wp_create_nonce value.
	 * STEP 2 - Return data (if any) as a JSON string.
	 *
	 * @since 0.2.0
	 * @since 0.3.0 - Fixed major bugs, added multi-file uploading, better error
	 *                handling.
	 *
	 * @see APL_Preset_Db class
	 * @see APLUpdater
	 *
	 * @return void JSON string to import.
	 */
	public function hook_action_ajax_import() {
		check_ajax_referer( 'APL_handler_import' );
		$rtn_data                      = new stdClass();
		$rtn_data->_msg                = 'success';
		$rtn_data->_error              = '';
		$rtn_data->_preset_db          = new stdClass();
		$rtn_data->overwrite_preset_db = new stdClass();

		$temp_preset_db = new APL_Preset_Db();

		if ( 'kalin' === $_POST['import_type'] ) {
			// GET KALIN'S POST LIST DATA.
			$kalin_preset_db = get_option( 'kalinsPost_admin_options' );
			if ( false === $kalin_preset_db ) {
				$rtn_data->_msg = 'failure';
				$rtn_data->_error .= 'Can\'t load Kalin\'s Post List data - Database may be missing or plugin is not installed.<br />';
			} else {
				// UPGRADE.
				$updater = new APLUpdater( 'kalin', $kalin_preset_db );
				if ( null === $updater->presetDbObj ) {
					$rtn_data->_msg = 'failure';
					$rtn_data->_error .= 'Can\'t upgrade Kalin\'s Post List - Unknown, may be a currupt data.<br />';
				} else {
					// MERGE TOGETHER.
					foreach ( $updater->presetDbObj->_preset_db as $preset_name => $preset_obj ) {
						if ( ! isset( $temp_preset_db->_preset_db->$preset_name ) ) {
							$temp_preset_db->_preset_db->$preset_name = $preset_obj;
						}
					}
				}
			}
		} elseif ( 'file' === $_POST['import_type'] ) {
			foreach ( $_FILES as $key => $value ) {
				// GET FILE CONTENT.
				$file_preset_db[ $key ] = json_decode( file_get_contents( $value['tmp_name'] ) );
				if ( is_null( $file_preset_db[ $key ] ) ) {
					$rtn_data->_msg = 'failure';
					$rtn_data->_error .= 'Can\'t load file ' . $value['name'] . ' - Syntax Error with JSON encoding inside file.<br />';
				} else {
					// UPGRADE.
					$updater = new APLUpdater( $file_preset_db[ $key ]->version, $file_preset_db[ $key ]->presetDbObj );
					if ( null === $updater->presetDbObj ) {
						$rtn_data->_msg = 'failure';
						$rtn_data->_error .= 'Can\'t upgrade file ' . $value['name'] . ' - Version number is missing, or no preset table was found; may be a currupted file.<br />';
					} else {
						// MERGE TOGETHER.
						foreach ( $updater->presetDbObj->_preset_db as $preset_name => $preset_obj ) {
							if ( ! isset( $temp_preset_db->_preset_db->$preset_name ) ) {
								$temp_preset_db->_preset_db->$preset_name = $preset_obj;
							}
						}
					}
				}
			}
		} else {
			$rtn_data->_msg = 'failure';
			$rtn_data->_error = 'No \'Imput Type\' selected. Choose between either Kalin\'s Post List or upload a file from Advanced Post List';
		}// End if().

		// LOAD PLUGIN PRESETS.
		$preset_db = new APL_Preset_Db( 'default' );
		$overwrite_preset_db = new stdClass();
		// COMPARE PLUGIN DB WITH UPLOAD DATA.
		foreach ( $temp_preset_db->_preset_db as $tmp_preset_name => $tmp_preset_value ) {
			// ADD MISSING.
			if ( ! isset( $preset_db->_preset_db->$tmp_preset_name ) ) {
				$preset_db->_preset_db->$tmp_preset_name = $tmp_preset_value;
			} else {
				// ADD TO CONFIRM OVERWRITE LIST {OBJECT}.
				$overwrite_preset_db->$tmp_preset_name = $tmp_preset_value;
			}
		}

		// SEND UPDATED AND POSSIBLE OVERWRITES TO UPDATE THE PRESET TABLE IN JS.
		$rtn_data->_preset_db = $preset_db->_preset_db;
		$rtn_data->overwrite_preset_db = $overwrite_preset_db;

		// STORE TEMP PRESET DATABASE OBJECT TO BE USED IN import.php.
		// DO NOT SAVE HERE - SAVE IN FINAL IMPORT @ import.php.
		// JUST A NOTE FOR FUTURE MODIFICATIONS.
		update_option( 'APL_TMP_import_presetDbObj', $temp_preset_db );

		// CREATE NEW AJAX NONCE VALUES.
		$rtn_data->action = 'APL_import';
		$rtn_data->_ajax_nonce = wp_create_nonce( 'APL_import' );

		echo json_encode( $rtn_data );
	}

	/**
	 * Summary.
	 *
	 * Method used for saving APL core 'General Settings' to the developer's
	 * WordPress database.
	 *
	 * STEP 1 - Check AJAX wp_create_nonce security value.
	 * STEP 2 - Load APL Options.
	 * STEP 3 - Store 'delete core db' value.
	 * STEP 4 - Store jQuery UI Theme value and queue the style.
	 * STEP 5 - Save APLOptions to database.
	 * STEP 6 - Set Theme value in return variable.
	 * STEP 7 - Echo JSON string to jQuery.post param function.
	 *
	 * @since 0.2.0
	 * @since 0.3.0 - Added JQuery UI Theme setting.
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 *
	 * @return void JSON string to return to AJAX.
	 */
	public function hook_action_ajax_save_settings() {
		// Step 1.
		$check_ajax_referer = check_ajax_referer( 'APL_handler_save_settings' );

		$rtn_data = new stdClass();
		$rtn_data->error = '';
		$rtn_data->theme = 'overcast';

		// Step 2.
		$options = $this->apl_options_load();

		// Step 3.
		//$options['delete_core_db'] = $_POST['deleteDb'];
		$options['delete_core_db'] = false;
		if ( 'true' === $_POST['deleteDb'] ) {
			$options['delete_core_db'] = true;
		}

		// Step 4.
		$options['jquery_ui_theme'] = $_POST['theme'];
		wp_enqueue_style(
			'apl-admin-ui-css',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/' . $options['jquery_ui_theme'] . '/jquery-ui.css',
			false,
			APL_VERSION,
			false
		);

		$options['default_exit'] = false;
		if ( 'true' === $_POST['defaultExit'] ) {
			$options['default_exit'] = true;
		}
		$options['default_exit_msg'] = stripslashes( $_POST['defaultExitMsg'] );

		// Step 5.
		$this->apl_options_save( $options );

		// Step 6.
		$rtn_data->theme = $options['jquery_ui_theme'];

		// Step 7.
		echo json_encode( $rtn_data );
	}

	/**
	 * Summary.
	 *
	 * Saves the created preset data from the APL Admin page.
	 *
	 * STEP 1 - Check javascript ajax wp_create_nonce reference.
	 * STEP 2 - Store preset name.
	 * STEP 3 - Store preset's post parents, if any.
	 * STEP 4 - Process and store the Post Type & Taxonomy structure.
	 * STEP 5 - Store the preset's number of posts.
	 * STEP 6 - Store the order values.
	 * STEP 7 - Store the post status.
	 * STEP 8 - Store the exclude current boolean.
	 * STEP 9 - Store the Before, Content, & After HTML/JavaScript/Shortcode content.
	 * STEP 10 - Overwrite or save the preset.
	 * STEP 11 - Create and store data for the varible returned to the AJAX function.
	 * STEP 12 - echo the data returned though a json_encode method.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 - Added custom post type & taxonomy support. Changed post
	 *                parent from one selection to multiple selections, and
	 *                get other pages from multiple hierarchical post types.
	 *                Along with the Post Status setting.
	 *
	 * @see APL_Preset_Db class
	 * @see APL_Preset class
	 * @link URL
	 *
	 * @return void JSON string to return to AJAX.
	 */
	public function hook_action_ajax_save_preset() {
		// TODO - Create a function to decode preset, and/or handle postTax structure.
		// TODO - Change to filter_input() instead of $_POST http://php.net/manual/en/function.filter-input.php .
		// Step 1.
		check_ajax_referer( 'APL_handler_save_preset' );

		// DEFAULT USE.
		$preset_db = new APL_Preset_Db( 'default' );
		// MULTI PRESET OPTIONS.
		/*
		  foreach ($options['preset_db_names'] as $key => $value)
		  {
		  $preset_db[$key] = $value;
		  }
		 */

		// Step 2.
		$preset_name = stripslashes( $_POST['presetName'] );

		$preset_obj = new APL_Preset();

		// Step 3.
		$preset_obj->_postParents = json_decode( stripslashes( $_POST['postParents'] ) );
		$preset_obj->_postParents = array_unique( $preset_obj->_postParents );

		// Step 4.
		$preset_obj->_postTax = json_decode( stripslashes( $_POST['postTax'] ) );
		$temp_post_tax = new stdClass();
		foreach ( $preset_obj->_postTax as $post_type_name => $post_type_value ) {
			foreach ( $post_type_value->taxonomies as $taxonomy_name => $taxonomy_value ) {
				if ( ! is_object( $temp_post_tax->$post_type_name ) ) {
					$temp_post_tax->$post_type_name = new stdClass();
				}
				if ( ! is_object( $temp_post_tax->$post_type_name->taxonomies ) ) {
					$temp_post_tax->$post_type_name->taxonomies = new stdClass();
				}
				$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name = new stdClass();

				$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name->require_taxonomy = $taxonomy_value->require_taxonomy;
				$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name->require_terms = $taxonomy_value->require_terms;
				$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name->include_terms = $taxonomy_value->include_terms;

				$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name->terms = array();
				foreach ( $taxonomy_value->terms as $term_index => $term_value ) {
					$temp_post_tax->$post_type_name->taxonomies->$taxonomy_name->terms[ $term_index ] = intval( $term_value );
				}
			}
		}
		$preset_obj->_postTax = $temp_post_tax;

		// Step 5.
		// (int) howmany to display.
		$preset_obj->_listCount = intval( $_POST['count'] );

		// Step 6.
		// (string)
		$preset_obj->_listOrder = $_POST['order'];
		// (string)
		$preset_obj->_listOrderBy = $_POST['orderBy'];

		// Step 7.
		// (array) => (string)
		$preset_obj->_postVisibility = json_decode( stripslashes( $_POST['postVisibility'] ) );
		// (array) => (string)
		$preset_obj->_postStatus = json_decode( stripslashes( $_POST['postStatus'] ) );
		// (string)
		$preset_obj->_userPerm = $_POST['userPerm'];

		// (string)
		$preset_obj->_postAuthorOperator = $_POST['authorOperator'];
		// (array) => (int)
		$preset_obj->_postAuthorIDs = json_decode( stripslashes( $_POST['authorIDs'] ) );

		// (boolean)
		$preset_obj->_listIgnoreSticky = true;
		if ( 'false' === $_POST['ignoreSticky'] ) {
			$preset_obj->_listIgnoreSticky = false;
		}

		// Step 8
		// (boolean)
		$preset_obj->_listExcludeCurrent = true;
		if ( $_POST['excludeCurrent'] === 'false' ) {
			$preset_obj->_listExcludeCurrent = false;
		}

		// (boolean)
		$preset_obj->_listExcludeDuplicates = true;
		if ( 'false' === $_POST['excludeDuplicates'] ) {
			$preset_obj->_listExcludeDuplicates = false;
		}

		$tmp_exclude_posts = array();
		$preset_obj->_listExcludePosts = array();
		// (array) => (int)
		$tmp_exclude_posts = json_decode( stripslashes( $_POST['excludePosts'] ) );
		foreach ( $tmp_exclude_posts as $post_id ) {
			if ( ! empty( $post_id ) ) {
				$preset_obj->_listExcludePosts[] = intval( $post_id );
			}
		}
		$preset_obj->_listExcludePosts = array_unique( $preset_obj->_listExcludePosts );

		// Step 9.
		// (string)
		$preset_obj->_exit = stripslashes( $_POST['exit'] );
		$preset_obj->_before = stripslashes( $_POST['before'] );
		$preset_obj->_content = stripslashes( $_POST['content'] );
		$preset_obj->_after = stripslashes( $_POST['after'] );

		// Step 10.
		$preset_db->_preset_db->$preset_name = $preset_obj;
		$preset_db->options_save_db();

		// Step 11.
		$rtn_data = new stdClass();
		$rtn_data->status = 'success';
		$rtn_data->preset_arr = $preset_db->_preset_db;
		$rtn_data->previewOutput = $this->display_post_list( $preset_name );

		// Step 12.
		echo json_encode( $rtn_data );
	}

	/**
	 * Summary.
	 *
	 * Method handler for deleting presets within the Preset DbOptions.
	 *
	 * STEP 1 - Check JavaScript AJAX wp_create_nonce reference.
	 * STEP 2 - Load APL Preset DbOptions.
	 * STEP 3 - Get postname from page
	 * STEP 4 - Delete (unset) preset from preset database variable.
	 * STEP 5 - Save preset database.
	 * STEP 6 - echo preset database.
	 *
	 * @since 0.1.0
	 *
	 * @see APL_Preset_Db class
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @return void JSON string to return to AJAX.
	 */
	public function hook_action_ajax_delete_preset() {
		// Step 1.
		check_ajax_referer( 'APL_handler_delete_preset' );

		// Step 2.
		$preset_db = new APL_Preset_Db( 'default' );

		// Step 3.
		$preset_name = stripslashes( $_POST['preset_name'] );

		// Step 4.
		unset( $preset_db->_preset_db->$preset_name );

		// Step 5.
		$preset_db->options_save_db();

		// Step 6.
		echo json_encode( $preset_db->_preset_db );
	}

	/**
	 * Summary.
	 *
	 * Method handler for restoring the original plugin preset defaults
	 *
	 * STEP 1 - Grab the JavaScript AJAX reference.
	 * STEP 2 - Get preset options for a temp and a current variable.
	 * STEP 3 - Set temp to default preset_database_object.
	 * STEP 4 - Add default presets to current preset_database_object.
	 * STEP 5 - Save current preset database variable.
	 * STEP 6 - Echo/Return preset values.
	 *
	 * @since 0.1.0
	 *
	 * @see APL_Preset_Db class
	 * @link URL
	 *
	 * @return void JSON string to return to AJAX.
	 */
	public function hook_action_ajax_restore_preset() {
		// STEP 1.
		check_ajax_referer( 'APL_handler_restore_preset' );

		// STEP 2.
		$preset_db = new APL_Preset_Db( 'default' );
		$temp_db = new APL_Preset_Db( 'default' );

		// STEP 3.
		$temp_db->set_to_defaults();

		// STEP 4.
		foreach ( $temp_db->_preset_db as $key => $value ) {
			$preset_db->_preset_db->$key = $value;
		}

		// STEP 5.
		$preset_db->options_save_db();

		// STEP 6.
		echo json_encode( $preset_db->_preset_db );
	}

	/**
	 * Summary.
	 *
	 * Method handler for 'post_list' shortcode and displaying the target post list.
	 *
	 * STEP 1 - If a value is not set, do STEP 3.
	 * STEP 2 - Return $this->display($preset_name).
	 * STEP 3 - Otherwise return an empty string.
	 *
	 * @since 0.1.0
	 *
	 * @param string $att Carries the preset name.
	 * @return string HTML content, if param is set. Otherwise return an empty string.
	 */
	public function hook_shortcode_post_list( $att ) {
		// STEP 1.
		if ( isset( $att['name'] ) ) {
			// STEP 2.
			return $this->display_post_list( $att['name'] );
		} else {
			// STEP 3.
			return '';
		}
	}

	/**
	 * Summary.
	 *
	 * Public function for post lists.
	 *
	 * @since 0.1.0
	 * @access (for functions: only use if private)
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param string $preset_name Preset slug/name.
	 * @return string HTML content
	 */
	public function display_post_list( $preset_name ) {
		require_once( APL_DIR . 'includes/class/class-apl-shortcodes.php' );

		return $this->apl_run( $preset_name );
	}

	/**
	 * Summary.
	 *
	 * Method used for executing the main purpose of the plugin. Creates an HTML
	 * post list string to be sent to the page that it was called from. What is
	 * displayed is determined by the 'post_list name' being used.
	 *
	 * STEP 1 - Get the preset object, and if empty, display a message to
	 * the admin.
	 * STEP 2 - If Exclude Duplicates (w/ multiple post lists) is checked,
	 * then add any post IDs collected to the preset post list object's exclude
	 * post array to be filter out.
	 * STEP 3 - Initialize the APLQuery object (sets the query strings).
	 * STEP 4 - Query the posts to retrieve the final WP_Query class.
	 * STEP 5 - If posts are present, the use the loop to display posts. Otherwise
	 * return an exit message if no posts are found.
	 * STEP 6 - Return output string.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Corrected a typo in the if statement for _postExcludeCurrent.
	 * @since 0.3.b8 - Complete overhaul. Moved dynamic settings to APLQuery,
	 *                implemented WP's loop.
	 * @access private
	 *
	 * @see APL_Preset_Db class
	 * @see APL_Query class
	 * @see APL_Internal_Shortcodes class
	 * @link URL
	 *
	 * @param string $preset_name Preset slug/name.
	 * @return string HTML string.
	 */
	private function apl_run( $preset_name ) {
		// What does this do???
		// This is something that Kalin was originally had in mind for
		// implementing page style and design with the global $post.
		/*
		if ($newVals->post_type == "none") {
			$output = APLInternalShortcodeReplace($newVals->content, $post, 0);
		}
		*/

		// STEP 1 - Get the preset object, and if empty, display a message
		// to the admin.
		$preset_db_obj = new APL_Preset_Db( 'default' );
		if ( isset( $preset_db_obj->_preset_db->$preset_name ) ) {
			$preset_obj = new APL_Preset();
			$preset_obj = $preset_db_obj->_preset_db->$preset_name;
		} elseif ( current_user_can( 'manage_options' ) ) {
			// Alert Message for admins in case an invalid preset was used.
			return '<p>Admin Alert - A problem has occured. A non-existent preset name has been passed use.</p>';
		} else {
			// Users/Visitors won't be able to see the post list if the
			// preset post list name isn't set right.
			return'';
		}

		// STEP 2 - If Exclude Duplicates (w/ multiple post lists) is checked,
		// then add any post IDs collected to the preset post list object's
		// exclude post array to be filter out.
		if ( isset( $preset_obj->_listExcludeDuplicates ) && true === $preset_obj->_listExcludeDuplicates ) {
			foreach ( $this->_remove_duplicates as $post_id ) {
				$preset_obj->_listExcludePosts[] = $post_id;
			}
		}

		// STEP 3 - Initialize the APLQuery object (sets the query strings).
		// The constructor will do most of the initial settings, like setting
		// multiple query strings according to APL. The class will still need to
		// use a public function to return a WP_Query class; until 'inheritance'
		// becomes more of a possibility.
		$apl_query = new APL_Query( $preset_obj );

		//STEP 4 - Query the posts to retrieve the final WP_Query class.
		$wp_query_class = $apl_query->query_wp( $apl_query->_query_str_array );

		/* ****************************************************************** */
		/* * The Loop (APL/WP Concept) ************************************** */
		/* ****************************************************************** */
		// STEP 5 - If posts are present, the use the loop to display posts.
		// Otherwise return an exit message if no posts are found.
		if ( $wp_query_class->have_posts() ) {
			$output = '';

			/* * Before ***************************************************** */
			$output .= $preset_obj->_before;

			/* * Content **************************************************** */
			$count = 0;

			$internal_shortcodes = new APL_Internal_Shortcodes();

			while ( $wp_query_class->have_posts() ) {
				$wp_query_class->the_post();
				// $APL_post->ID;.
				$this->_remove_duplicates[] = $wp_query_class->post->ID;

				$output .= $internal_shortcodes->replace( $preset_obj->_content, $wp_query_class->post );
				$count++;
			}

			if ( strrpos( $output, 'final_end' ) ) {
				$output = $internal_shortcodes->final_end( $output );
			}

			/* * After ****************************************************** */
			$output .= $preset_obj->_after;
		} else {
			// if (count($apl_query->_posts) === 0).
			$apl_options = $this->apl_options_load();
			if ( ! empty( $preset_obj->_exit ) ) {
				return $preset_obj->_exit;
			} elseif ( true === $apl_options['default_exit'] && ! empty( $apl_options['default_exit_msg'] ) ) {
				return $apl_options['default_exit_msg'];
			} else {
				return '';
			}
		}// End if().

		/* Restore Global Post Data */
		wp_reset_postdata();
		// Exit method for apl-shortcodes class.
		$internal_shortcodes->remove();

		// STEP 6 - Return output string.
		return $output;
	}

	/**
	 * Summary.
	 *
	 * Get the post values needed for the plugin's.
	 *
	 * STEP 1 - Get Global post, current post.
	 * STEP 2 - Store current post's ID.
	 * STEP 3 - Store current post's post_type.
	 * STEP 4 - Get Post Type's Taxonomies.
	 * STEP 5 - Get Taxonomy's Terms and store them accordingly.
	 * STEP 6 - Return the data stored.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @global object $post Description.
	 *
	 * @return object post_types & taxonomy.
	 */
	private function apl_get_post_attr() {
		$rtn_obj = new stdClass();
		// Step 1.
		global $post;

		// Step 2.
		$rtn_obj->ID = (int) 0;
		if ( isset( $post->ID ) ) {
			$rtn_obj->ID = $post->ID;
		}

		// Step 3.
		$rtn_obj->post_type = '';
		if ( isset( $post->post_type ) ) {
			$rtn_obj->post_type = $post->post_type;
		}

		// Step 4.
		$rtn_obj->taxonomies = new stdClass();
		$taxonomies = $this->apl_get_taxonomies( $rtn_obj->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			// Step 5.
			if ( isset( $post->ID ) ) {
				$terms = wp_get_post_terms( $post->ID, $taxonomy );
			}

			if ( ! empty( $terms ) ) {
				// $tmp_terms = array();.
				foreach ( $terms as $term_index => $term_object ) {
					$rtn_obj->taxonomies->$taxonomy->terms[ $term_index ] = $term_object->term_id;
					//$tmp_terms[$term_index] = $term_object->term_id;
				}
				//$rtn_obj->taxonomies->$taxonomy->terms = $tmp_terms;
			}
		}
		// Step 6.
		return $rtn_obj;
	}
}
