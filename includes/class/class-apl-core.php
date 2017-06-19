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
 * @since 0.4.0 - Removed Admin functions and added into APL_Admin class.
 */
class APL_Core {

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
	 * STEP 2: Add Hooks.
	 * STEP 3: Add Admin Hooks.
	 *
	 * @since 0.1.0
	 * @since 0.2.0 - Refined how version checking was performed.
	 * @since 0.4.0 - Added hook for loading the textdomain to enable
	 *                internalization support.
	 *                Changed check version to hook method.
	 * @access public
	 *
	 * @param string $file Main plugin file.
	 * @return void
	 */
	public function __construct( $file ) {
		// STEP 1.
		$this->_define_constants( $file );
		$this->_requires();

		// STEP 2.
		/* **** ACTION & FILTERS HOOKS **** */
		add_action( 'plugins_loaded', array( $this, 'hook_action_check_version' ) );
		add_action( 'init', array( $this, 'hook_action_register_post_type_post_list' ) );
		add_action( 'init', array( $this, 'hook_action_register_post_type_design' ) );
		add_action( 'plugins_loaded', array( $this, 'hook_action_load_plugin_textdomain' ) );
		add_shortcode( 'post_list', array( $this, 'hook_shortcode_post_list' ) );
		add_action( 'widgets_init', array( $this, 'hook_action_widget_init' ) );
		// STEP 3.
		if ( is_admin() ) {
			// Admin Class
			add_action( 'init', array( 'APL_Admin', 'get_instance' ) );
			
			/* **** ACTIVATE/DE-ACTIVATE/UNINSTALL HOOKS **** */
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
	 * @since 0.3.2
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
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string APL_NAME 'Advanced Post List'.
		 */
		define( 'APL_NAME', $plugin_data['Name'] );

		/**
		 * APL Slug.
		 *
		 * @since 0.3.2
		 * @var string APL_SLUG 'advanced-post-list'.
		 */
		define( 'APL_SLUG', $plugin_data['Slug'] );

		/**
		 * Version Number.
		 *
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string APL_VERSION '1.2.3'.
		 */
		define( 'APL_VERSION', $plugin_data['Version'] );

		/**
		 * URL Location.
		 *
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string APL_URL 'http://localhost/wordpress/wp-content/plugins/advanced-post-list/'.
		 */
		define( 'APL_URL', plugin_dir_url( $plugin_file ) );

		/**
		 * Directory Path.
		 *
		 * @since 0.1.0
		 * @since 0.3.2 - Moved from advanced-post-list.php to class-apl-core
		 *                APL_Core::_define_constants().
		 * @var string APL_DIR 'C:\xampp\htdocs\wordpress\wp-content\plugins\advanced-post-list/'.
		 */
		define( 'APL_DIR', plugin_dir_path( $plugin_file ) );
	}

	/**
	 * Add Required Files.
	 *
	 * Adds the required files to include.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @global string APL_DIR APL file path.
	 */
	private function _requires() {
		// PUBLIC.
		// Class Objects.
		require_once( APL_DIR . 'includes/class/class-apl-preset-db.php' );
		require_once( APL_DIR . 'includes/class/class-apl-preset.php' );
		require_once( APL_DIR . 'includes/class/class-apl-post-list.php' );
		require_once( APL_DIR . 'includes/class/class-apl-design.php' );
		require_once( APL_DIR . 'includes/class/class-apl-widget.php' );
		require_once( APL_DIR . 'includes/class/class-apl-query.php' );
		require_once( APL_DIR . 'includes/class/class-apl-updater.php' );
		// OLD - Remove between 0.4 - 0.6.
		require_once( APL_DIR . 'includes/class/old-APLPresetDbObj.php' );
		require_once( APL_DIR . 'includes/class/old-APLPresetObj.php' );
		
		// Functions.
		require_once( APL_DIR . 'includes/functions.php');

		// ADMIN
		require_once( APL_DIR . 'admin/class-apl-admin.php' );
	}

	/**
	 * Register the Design Post Type.
	 *
	 * Hook for loading the textdomain location.
	 *
	 * @since 0.4.0
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_post_type WP Codex.
	 *
	 * @return void
	 */
	public function hook_action_register_post_type_post_list() {
		
		$args = array(
			'labels' => array(
				'name'                  => __( 'Post Lists', 'advanced-post-list' ),
				'singular_name'         => __( 'Post List', 'advanced-post-list' ),
				'add_new'               => _x( 'Add New', 'List', 'advanced-post-list' ),
				'add_new_item'          => __( 'Add New Post List', 'advanced-post-list' ),
				'edit_item'             => __( 'Edit Post List', 'advanced-post-list' ),
				'new_item'              => __( 'New Post List', 'advanced-post-list' ),
				'view_item'             => __( 'View Post List', 'advanced-post-list' ),
				'view_items'            => __( 'View Post Lists', 'advanced-post-list' ),
				'search_items'          => __( 'Search Post Lists', 'advanced-post-list' ),
				'not_found'             => __( 'No Post Lists found', 'advanced-post-list' ),
				'not_found_in_trash'    => __( 'No Post Lists found in Trash', 'advanced-post-list' ),
				'parent_item_colon'     => __( ':', 'advanced-post-list' ),
				'all_items'             => __( 'All Post Lists', 'advanced-post-list' ),
				'archives'              => __( 'Post List Archives', 'advanced-post-list' ),
				'attributes'            => __( 'Post List Attributes', 'advanced-post-list' ),
				'insert_into_item'      => __( 'Insert into Post List', 'advanced-post-list' ),
				'uploaded_to_this_item' => __( 'Upload to this Post List', 'advanced-post-list' ),
				'menu_name'				=> __( 'APL Post List', 'advanced-post-list' ),
			),
			'description'           => __( 'APL Preset Post Lists.', 'advanced-post-list' ),
			'public'                => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'show_ui'               => true, // Shows up in admin menu bar.
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'		=> true,
			'menu_position'         => 58,
			'menu_icon'				=> 'dashicons-welcome-widgets-menus',
			'hierarchical'          => true,
			'supports' 				=> array(
				'title',
				'author',
				'thumbnail',
				'revisions',
			),
			'has_archive'			=> false,
			'rewrite'               => array(
				'slug' => 'apl-post-list',
			),
			// Disables the URL query /?{query_var}={single_post_slug}.
			'query_var'             => false,
			//'can_export'            => true, // Default true.
			'delete_with_user'		=> false,
		);

		$args = apply_filters( 'apl_register_post_type_post_list', $args );
		register_post_type( 'apl_post_list', $args );
	}

	/**
	 * Register the Design Post Type.
	 *
	 * Hook for loading the textdomain location.
	 *
	 * @since 0.4.0
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_post_type WP Codex.
	 *
	 * @return void
	 */
	public function hook_action_register_post_type_design() {
		$args = array(
			'labels' => array(
				'name'                  => __( 'Designs', 'advanced-post-list' ),
				'singular_name'         => __( 'Design', 'advanced-post-list' ),
				'add_new'               => _x( 'Add New', 'design', 'advanced-post-list' ),
				'add_new_item'          => __( 'Add New Design', 'advanced-post-list' ),
				'edit_item'             => __( 'Edit Design', 'advanced-post-list' ),
				'new_item'              => __( 'New Design', 'advanced-post-list' ),
				'view_item'             => __( 'View Design', 'advanced-post-list' ),
				'view_items'            => __( 'View Designs', 'advanced-post-list' ),
				'search_items'          => __( 'Search Designs', 'advanced-post-list' ),
				'not_found'             => __( 'No Design found', 'advanced-post-list' ),
				'not_found_in_trash'    => __( 'No Design found in Trash', 'advanced-post-list' ),
				'parent_item_colon'     => __( ':', 'advanced-post-list' ),
				'all_items'             => __( 'All Designs', 'advanced-post-list' ),
				'archives'              => __( 'Design Archives', 'advanced-post-list' ),
				'attributes'            => __( 'Design Attributes', 'advanced-post-list' ),
				'insert_into_item'      => __( 'Insert into Design', 'advanced-post-list' ),
				'uploaded_to_this_item' => __( 'Upload to this Design', 'advanced-post-list' ),
				'menu_name'				=> __( 'APL Designs', 'advanced-post-list' ),
			),
			'description'           => __( 'APL Designs for Preset Post Lists.', 'advanced-post-list' ),
			'public'                => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			// Shows up in admin menu bar.
			'show_ui'               => false,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'		=> false,
			'menu_icon'				=> 'dashicons-admin-generic',
			'hierarchical'          => true,
			'supports' 				=> array(
				'title',
				'thumbnail',
				'excerpt',
				'revisions',
			),
			'has_archive'			=> false,
			'rewrite'               => array(
				'slug' => 'apl-design',
			),
			// Disables the URL query /?{query_var}={single_post_slug}.
			'query_var'             => false,
			//'can_export'            => true, // Default true.
			'delete_with_user'		=> false,
		);

		$args = apply_filters( 'apl_register_post_type_design', $args );
		register_post_type( 'apl_design', $args );
	}

	/**
	 * Hook for APL check version.
	 *
	 * Update method for handling the Upgrader Class.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to action hook.
	 * @access public
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 *
	 * @return void
	 */
	public function hook_action_check_version() {
		$options = $this->apl_options_load();
		if ( isset( $options['version'] ) ) {
			/* **** UPGRADES **** *
			 * Put upgrade database functions in here. Not before.
			 *     Ex. APL_upgrade_to_XXX().
			 */
			if ( version_compare( $options['version'], APL_VERSION, '<' ) ) {
				$preset_db = new APL_Preset_Db( 'default' );
				$updater = new APL_Updater( $options['version'], $preset_db, $options );
				// IN THIS CASE, BOTH MUST HAVE VALUES FILLED.
				if ( null !== $updater->options || null !== $updater->preset_db ) {
					$preset_db = $updater->preset_db;
					$preset_db->options_save_db();
					$options = $updater->options;
					$this->apl_options_save( $options );
				}
			}
		}
	}

	/**
	 * Load APL's Textdomain.
	 *
	 * Hook for loading the textdomain location.
	 *
	 * @since 0.4.0
	 *
	 * @link https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/
	 *
	 * @return void
	 */
	public function hook_action_load_plugin_textdomain() {
		$lang_dir = APL_DIR . '/languages/';
		load_plugin_textdomain( APL_SLUG, false, $lang_dir );
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
		// TODO Change to Post Data
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
		// TODO Change to Post Data
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
		// TODO Change to Post Data
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
		//$options['jquery_ui_theme']  = 'overcast';
		$options['default_exit']     = false;
		$options['default_exit_msg'] = __( '<p>Sorry, but no content is available at this time.</p>', 'advanced-post-list' );
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
			return esc_html__( '<p>Admin Alert - A problem has occured. A non-existent preset name has been passed use.</p>', 'advanced-post-list' ) ;
		} else {
			// Users/Visitors won't be able to see the post list if the
			// preset post list name isn't set right.
			return '';
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
		$apl_design = new APL_Design( $preset_obj->get_apl_design() );

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
			$output .= $apl_design->before;

			/* * Content **************************************************** */
			$count = 0;

			$internal_shortcodes = new APL_Internal_Shortcodes();

			while ( $wp_query_class->have_posts() ) {
				$wp_query_class->the_post();
				// $APL_post->ID;.
				$this->_remove_duplicates[] = $wp_query_class->post->ID;

				$output .= $internal_shortcodes->replace( $apl_design->content, $wp_query_class->post );
				$count++;
			}

			if ( strrpos( $output, 'final_end' ) ) {
				$output = $internal_shortcodes->final_end( $output );
			}

			/* * After ****************************************************** */
			$output .= $apl_design->after;
		} else {
			// if (count($apl_query->_posts) === 0).
			$apl_options = $this->apl_options_load();
			if ( ! empty( $apl_design->empty ) ) {
				return $apl_design->empty;
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
}
