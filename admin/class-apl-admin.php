<?php
/**
 * APL Admin API: APL_Admin Class
 *
 * Admin core object to Advanced Post List
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage APL_Core
 * @since 0.4.0
 */

/**
 * APL Admin
 *
 * Admin core class.
 *
 * @since 0.4.0
 */
class APL_Admin {

	/**
	 * Singleton Instance.
	 *
	 * @since 0.4.0
	 * @access private
	 * @var null $instance Singleton Class Instance.
	 */
	protected static $instance = null;

	/**
	 * Get Singleton Instance.
	 *
	 * Singleton Get Instance.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Throws error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return void
	 */
	private function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'advanced-post-list' ), APL_VERSION );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 0.4.0
	 * @access protected
	 *
	 * @return void
	 */
	private function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'advanced-post-list' ), APL_VERSION );
	}

	/**
	 * Constructor.
	 *
	 * Private Singleton Constructor.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return void
	 */
	private function __construct() {
		// Exit if Non-Admins access this object. Also wrapped in APL_Core.
		if ( ! is_admin() ) {
			return new WP_Error( 'apl_admin', esc_html__( 'You do not have admin capabilities in APL_Admin.', 'advanced-post-list' ) );
		}
		$this->_requires();

		// Menu & Scripts.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		add_action( 'current_screen', array( $this, 'current_screen_hooks' ) );

		// Screen Options.
		add_action( 'admin_head', array( $this, 'disable_screen_boxes' ) );
		add_action( 'load-edit.php', array( $this, 'post_list_screen_all' ) );
		add_action( 'load-post-new.php', array( $this, 'post_list_screen_add_new' ) );

		add_action( 'manage_apl_post_list_posts_columns', array( $this, 'post_list_posts_columns' ) );
		add_action( 'manage_apl_post_list_posts_custom_column', array( $this, 'post_list_posts_custom_column' ), 10, 2 );
		add_action( 'manage_edit-apl_post_list_sortable_columns', array( $this, 'post_list_sortable_columns' ) );

		// Editor Meta Boxes.
		add_action( 'add_meta_boxes', array( $this, 'post_list_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'settings_meta_boxes' ) );

		
		
		// Settings Data
		add_action( 'admin_post_apl_save_general_settings', array( $this, 'save_general_settings' ) );
		// AJAX.
		add_action( 'admin_init', array( $this, 'add_settings_ajax_hooks' ) );

		// TESTING
		
		
		/*
		// Early Hook.
		add_action( 'plugins_loaded', array( $this, 'hook_action_plugins_loaded' ) );

		// Plugin Init Hook.
		add_action( 'init', array( $this, 'hook_action_init' ) );

		// After WordPress is fully loaded.
		add_action( 'wp_loaded', array( $this, 'hook_action_wp_loaded' ) );

		// WordPress Footer.
		add_action( 'wp_footer', array( $this, 'hook_action_wp_footer' ) );
		*/

	}
	
	/**
	 * 
	 * @param WP_Screen $current_screen Current WP_Screen object.
	 */
	public function current_screen_hooks( $current_screen ) {
		
		//var_dump( $current_screen );
		
		if ( 'edit-apl_post_list'  === $current_screen->id ) {
			// Add New.
			
			// Post Data
			add_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ), 10, 2 );

			add_action( 'private_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'publish_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'pending_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'future_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );

			//add_action( 'trash_apl_post_list', array( $this, 'trash_post_list' ), 10, 3 );
			add_action( 'wp_trash_post', array( $this, 'action_wp_trash_post_apl_post_list' ) );
			add_action( 'untrash_post', array( $this, 'action_untrash_post_apl_post_list' ) );
			add_action( 'before_delete_post', array( $this, 'action_before_delete_post_apl_post_list' ) );
			
			
		} elseif ( 'apl_post_list'  === $current_screen->id ) {
			// All Post Lists.
			
			// Post Data
			add_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ), 10, 2 );

			add_action( 'private_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'publish_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'pending_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'future_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );

			//add_action( 'trash_apl_post_list', array( $this, 'trash_post_list' ), 10, 3 );
			add_action( 'wp_trash_post', array( $this, 'action_wp_trash_post_apl_post_list' ) );
			add_action( 'untrash_post', array( $this, 'action_untrash_post_apl_post_list' ) );
			add_action( 'before_delete_post', array( $this, 'action_before_delete_post_apl_post_list' ) );
			
		} elseif ( 'apl_post_list_page_apl_settings'  === $current_screen->id ) {
			// Settings (Page)
			
			
		}
		
	}

	/**
	 * Requires Files.
	 *
	 * Files that this class object needs to load.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	private function _requires() {
		// Example.
		// 'require_once( APL_DIR . 'includes/example.php' )'.
		require_once( APL_DIR . 'admin/export.php' );
		require_once( APL_DIR . 'admin/import.php' );
	}

	/**
	 * APL Admin Menu.
	 *
	 * Adds the Admin Menu and Scripts for APL.
	 *
	 * @since 0.4.0
	 *
	 * @see 'admin_menu' hook
	 * @see wp-admin/admin-header.php
	 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 *
	 * @return void
	 */
	public function admin_menu() {
		// TODO - Add APL Dashboard.
		// TODO - Add APL Settings API.
		add_submenu_page(
			'edit.php?post_type=apl_post_list',
			__( 'APL Settings', 'advanced-post-list' ),
			__( 'Settings', 'advanced-post-list' ),
			'administrator',
			'apl_settings',
			array( $this, 'submenu_settings_page' )
		);
		add_action( 'admin_init', array( $this, 'settings_register_settings' ) );
		
		// TODO - Add Help API.
		
		// EXTENSIONS
		do_action( 'apl_admin_menu_ext' );

	}

	public function submenu_settings_page() {
		//echo 'FooBar';
		include( APL_DIR . 'admin/settings-page.php' );
	}

	/**
	 * Registers Input Settings for Settings Page.
	 *
	 * @since 0.4.0
	 *
	 * @return void 
	 */
	public function settings_register_settings() {
		register_setting( 'apl_settings_general', 'apl_delete_on_deactivation', 'strval' );
		register_setting( 'apl_settings_general', 'apl_default_empty_enable', 'strval' );
		register_setting( 'apl_settings_general', 'apl_default_empty_message', 'strval' );
		
		//register_setting( 'apl_settings_import_export', 'apl_export_file_name', 'strval' );
		//register_setting( 'apl_settings_import_export', 'apl_import_opt', 'strval' );
		//register_setting( 'apl_settings_import_export', 'apl_import_file', 'strval' );
		//register_setting( 'apl_settings_import_export', 'apl_restore_database', 'strval' );
	}

	/**
	 * APL Admin Enqueue Scripts & Styles.
	 *
	 * Loads APL scripts and styles. If not in APL Admin Pages, then remove.
	 *
	 * @since 0.4.0
	 *
	 * @see wp-admin/admin-header.php
	 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 *
	 * @param string $hook_suffix The suffix for the current Admin page.
	 * @return void.
	 */
	public function admin_enqueue( $hook_suffix ) {
		$screen = get_current_screen();

		/*
		 * ************** REMOVE SCRIPTS & STYLES *********************
		 */

		// STEP 1 - By default, remove any scripts & styles.
		wp_deregister_script( 'apl-admin-js' );
		wp_deregister_script( 'apl-admin-ui-js' );
		wp_deregister_script( 'apl-admin-ui-multiselect-js' );
		wp_deregister_script( 'apl-admin-ui-multiselect-filter-js' );

		wp_deregister_style( 'apl-admin-css' );
		wp_deregister_style( 'apl-admin-ui-multiselect-css' );
		wp_deregister_style( 'apl-admin-ui-multiselect-filter-css' );
		wp_deregister_style( 'apl-admin-settings-css' );

		// If we are not viewing APL Post List area, then return.
		if ( 'apl_post_list' !== $screen->post_type ) {
			return;
		} elseif ( 'apl_post_list_page_apl_settings' === $screen->id ) {
			// SETTINGS PAGE
			
			// SCRIPTS.
			wp_register_script(
				'apl-settings-js',
				APL_URL . 'admin/js/settings.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					//'jquery-ui-position',
					'jquery-ui-button',
					//'jquery-ui-draggable',
					//'jquery-ui-resizable',
					//'jquery-ui-effect',
					'jquery-ui-dialog',
				),
				APL_VERSION,
				false
			);
			wp_register_script(
				'apl-settings-ui-js',
				APL_URL . 'admin/js/settings-ui.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					'jquery-ui-dialog',
				),
				APL_VERSION,
				false
			);
			
			//wp_enqueue_script( 'common' );
			//wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
			wp_enqueue_script( 'apl-settings-js' );
			wp_enqueue_script( 'apl-settings-ui-js' );
			
			// STYLES.
			wp_enqueue_style(
				'apl-admin-settings-css',
				APL_URL . 'admin/css/settings.css',
				false,
				APL_VERSION,
				false
			);
			
			$wp_scripts = wp_scripts();
			wp_enqueue_style(
				'apl-admin-ui-css',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
				false,
				APL_VERSION,
				false
			);
			
			$trans_arr = array(
				'default_alert_title'            => __( 'Alert', 'advanced-post-list' ),
				'default_alert_message'          => __( 'No Message to Display.', 'advanced-post-list' ),
				'fileName_empty_alert_title'     => __( 'Filename Required', 'advanced-post-list' ),
				'fileName_empty_alert_message'   => __( 'A filename doesn\'t exist. \n Please enter a filename before exporting.', 'advanced-post-list' ),
				'import_no_file_message'         => __( 'No file(s) selected. Please choose a JSON file to upload.', 'advanced-post-list'),
				'import_no_file_title'           => __( 'No File', 'advanced-post-list'),
				'import_invalid_file_message'    => __( 'Invalid file type. Please choose a JSON file to upload.', 'advanced-post-list'),
				'import_invalid_file_title'      => __( 'Invalid File', 'advanced-post-list'),
				'import_success_message'         => __( 'Data successfully imported.', 'advanced-post-list' ),
				'import_success_title'           => __( 'Complete', 'advanced-post-list' ),
				'import_overwrite_dialog_title'  => __( 'Overwrite Presets', 'advanced-post-list' ),
				
			);
			$trans_ui_arr = array(
				'fileName_char_alert_title'    => __( 'Illegal Characters', 'advanced-post-list' ),
				'fileName_char_alert_message1' => __( 'Cannot use (< > : " / \\ | , ? *).', 'advanced-post-list' ),
				'fileName_char_alert_message2' => __( 'Please rename your filename.', 'advanced-post-list' ),
			);
			
			$settings_localize = array(
				'export_nonce'  => wp_create_nonce( 'apl_settings_export' ),
				'import_nonce'  => wp_create_nonce( 'apl_settings_import' ),
				'restore_nonce' => wp_create_nonce( 'apl_settings_restore' ),
				'trans'         => $trans_arr,
			);
			$settings_ui_localize = array(
				'trans'         => $trans_ui_arr,
			);
			
			wp_localize_script( 'apl-settings-js', 'apl_settings_local', $settings_localize );
			wp_localize_script( 'apl-settings-ui-js', 'apl_settings_ui_local', $settings_ui_localize );
			
			do_action( 'add_meta_boxes', $hook_suffix );
			add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );
			
		} else {
			
			/*
			 * ************** AJAX ACTION HOOKS ***************************
			 */

			// TODO - Add meta box to side to load different presets from 'edit.php'.
			add_action( 'wp_ajax_apl_load_preset', array( $this, 'hook_ajax_load_preset' ) );

			/*
			 * ************** REGISTER SCRIPTS ****************************
			 */

			// Step 2 - Register scripts to be enqueued.
			wp_register_script(
				'apl-admin-js',
				APL_URL . 'admin/js/admin.js',
				array(
					'jquery',
				),
				APL_VERSION,
				false
			);

			wp_register_script(
				'apl-admin-ui-js',
				APL_URL . 'admin/js/admin-ui.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					'jquery-ui-tabs',
					'jquery-ui-spinner',
					'jquery-ui-slider',
					'jquery-ui-button',
					'jquery-ui-dialog',
					'jquery-ui-selectmenu',
				),
				APL_VERSION,
				false
			);

			wp_register_script(
				'apl-admin-ui-multiselect-js',
				APL_URL . 'admin/js/jquery.multiselect.min.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					'jquery-ui-selectmenu',
				),
				APL_VERSION,
				false
			);

			wp_register_script(
				'apl-admin-ui-multiselect-filter-js',
				APL_URL . 'admin/js/jquery.multiselect.filter.min.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
				),
				APL_VERSION,
				false
			);

			// STEP 3 - Enqueue scripts.
			wp_enqueue_script( 'apl-admin-js' );
			wp_enqueue_script( 'apl-admin-ui-js' );
			wp_enqueue_script( 'apl-admin-ui-multiselect-js' );
			wp_enqueue_script( 'apl-admin-ui-multiselect-filter-js' );

			/*
			 * ************** REGISTER STYLES *****************************
			 */

			// Step 4 - (Register) Enqueue styles.
			wp_enqueue_style(
				'apl-admin-css',
				APL_URL . 'admin/css/admin.css',
				false,
				APL_VERSION,
				false
			);

			$wp_scripts = wp_scripts();
			wp_enqueue_style(
				'apl-admin-ui-css',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
				false,
				APL_VERSION,
				false
			);

			wp_enqueue_style(
				'apl-admin-ui-multiselect-css',
				APL_URL . 'admin/css/jquery.multiselect.css',
				false,
				APL_VERSION,
				false
			);

			wp_enqueue_style(
				'apl-admin-ui-multiselect-filter-css',
				APL_URL . 'admin/css/jquery.multiselect.filter.css',
				false,
				APL_VERSION,
				false
			);

//			// POST LISTS DATA.
//			$data_post_lists = array();
//			$args = array(
//				//'post_status'      => 'publish',
//				'post_type'        => 'apl_post_list',
//			);
//			$post_post_list = get_posts( $args );
//			foreach ( $post_post_list as $key => $value ) {
//				$data_post_lists[ $value->post_name ] = new APL_Post_List( $value->post_name );
//			}
//
//			// DESIGNS DATA.
//			$data_designs = array();
//			$args = array(
//				'post_status'      => 'publish',
//				'post_type'        => 'apl_design',
//			);
//			$post_designs = get_posts( $args );
//			foreach ( $post_designs as $key => $value ) {
//				$data_designs[ $value->post_name ] = new APL_Design( $value->post_name );
//			}

			// Get values for variables to localize into JS files.
			// POST => TAXONOMIES.
			$data_post_tax = $this->get_post_tax();

			// TAXONOMIES => TERMS.
			$data_tax_terms = $this->get_tax_terms();

			$data_ui_trans = array(
				'tax_noneSelectedText'           => esc_html__( 'Select Taxonomy', 'advanced-post-list' ),
				'tax_selectedText'               => esc_html__( '# of # taxonomies selected', 'advanced-post-list' ),
				'author_ids_noneSelectedText'    => esc_html__( '- None -', 'advanced-post-list' ),
				'author_ids_selectedText'        => esc_html__( '# Selected', 'advanced-post-list' ),
				'post_status_1_noneSelectedText' => esc_html__( 'Select Status', 'advanced-post-list' ),
				'post_status_1_selectedText'     => esc_html__( 'Both', 'advanced-post-list' ),
				'post_status_2_noneSelectedText' => esc_html__( 'Published', 'advanced-post-list' ),
				'post_status_2_selectedText'     => esc_html__( '# Selected', 'advanced-post-list' ),
			);

			$admin_localize = array();
			$admin_ui_localize = array(
				'post_tax' => $data_post_tax,
				'tax_terms' => $data_tax_terms,
				'trans' => $data_ui_trans,
			);

			// Add variables to JS files.
			// '../admin/js/admin.js'.
			// '../admin/js/admin-ui.js'.
			wp_localize_script( 'apl-admin-js', 'apl_admin_local', $admin_localize );
			wp_localize_script( 'apl-admin-ui-js', 'apl_admin_ui_local', $admin_ui_localize );
		}// End if().
	}

	/**
	 * Disables/Hides Screen Option Settings.
	 *
	 * Disables / Hides the Screen Option's display Meta Boxes Settings. Basically
	 * prevents certain Meta Boxes from being hidden, and forces the box to display.
	 *
	 * @since 0.4.0
	 *
	 * @see 'admin_head' hook.
	 * @link https://wordpress.stackexchange.com/questions/149602/hiding-metabox-from-screen-options-pull-down
	 *
	 * @return void
	 */
	public function disable_screen_boxes() {
		echo '<style>label[for=apl-post-list-filter-hide] { display: none; }</style>';
		echo '<style>#apl-post-list-filter { display: block; }</style>';
	}

	// Screen Options tab at top.
	/**
	 * Screen Options for 'All Post List' page.
	 *
	 * Hook 'load-edit.php', sets additional Screen Options.
	 *
	 * @see 'load-edit.php' hook.
	 * @since 0.4.0
	 *
	 * @return void
	 */
	public function post_list_screen_all() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if ( ! is_object( $screen ) || 'edit-apl_post_list' !== $screen->id ) {
			return;
		}

		$options = $screen->get_options();
	}

	/**
	 * Screen Options for 'Add New' page.
	 *
	 * Hook 'load-post-new.php', sets additional Screen Options.
	 *
	 * @see 'load-post-new.php' hook.
	 * @since 0.4.0
	 *
	 * @return void
	 */
	public function post_list_screen_add_new() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if ( ! is_object( $screen ) || 'apl_post_list' !== $screen->id ) {
			return;
		}
		$options = $screen->get_options();
	}

	/**
	 * Post List All Posts Columns.
	 *
	 * Adds additional columns to All Post Lists page.
	 *
	 * @since 0.4.0
	 *
	 * @see 'manage_apl_post_list_posts_columns'
	 * @uses manage_${post_type}_posts_columns
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns
	 *
	 * @param type $columns Columns use in the 'All Post Lists' page.
	 * @return array
	 */
	public function post_list_posts_columns( $columns ) {
		$tmp_date = $columns['date'];
		unset( $columns['date'] );
		
		$columns['post_name']      = __( 'Slug', 'advanced-post-list' );
		$columns['apl_shortcode']  = __( 'Shortcode', 'advanced-post-list' );
		
		$columns['date'] = $tmp_date;
		
		return $columns;
	}

	/**
	 * Post List Custom Column.
	 *
	 * Adds content to custom column.
	 *
	 * @since 0.4.0
	 *
	 * @uses manage_${post_type}_posts_columns hook.
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
	 *
	 * @param type $column
	 * @param type $post_id
	 */
	public function post_list_posts_custom_column( $column, $post_id ) {
		$args = array(
			'post__in' => array( $post_id ),
			'post_type' => 'apl_post_list',
			'post_status'     => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
		);
		$post_lists = new WP_Query( $args );
		$post_list = $post_lists->post;
		
		switch ( $column ) {
			case 'post_name' :
				echo $post_list->post_name;
				break;
			case 'apl_shortcode' :
				echo '<input value="[post_list name=\'' . $post_list->post_name . '\']" type="text" size="32" onfocus="this.select();" onclick="this.select();" readonly="readonly" />';
				break;
		}
	}

	/**
	 * Post List Sortable Columns.
	 *
	 * Sets Custom Columns to be sortable.
	 *
	 * @param array $columns
	 * @return string
	 */
	public function post_list_sortable_columns( $columns ) {
		$columns['post_name'] = 'post_name';
		
		return $columns;
	}

	/**
	 * Post List Meta Boxes.
	 *
	 * Hook 'add_meta_boxes', adds meta boxes used in post lists.
	 *
	 * @since 0.4.0
	 *
	 * @see 'add_meta_boxes' hook.
	 * @see wp-admin/includes/template.php
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 *
	 * @return void
	 */
	public function post_list_meta_boxes() {
		add_meta_box(
			'apl-post-list-filter',
			__( 'Filter Settings', 'advanced-post-list' ),
			array( $this, 'post_list_meta_box_filter' ),
			'apl_post_list',
			'normal', // 'normal', 'advanced', 'side'.
			'high' // 'high', 'sorted', 'core', 'default', 'low'.
		);
		add_meta_box(
			'apl-post-list-display',
			__( 'Display Settings', 'advanced-post-list' ),
			array( $this, 'post_list_meta_box_display' ),
			'apl_post_list',
			'normal',
			'core'
		);
	}

	/**
	 * Add Settings Page Meta Boxes
	 *
	 * @uses add_meta_box();
	 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
	 *
	 * @return void
	 */
	public function settings_meta_boxes() {
		add_meta_box(
			'apl-info',
			__( 'Import / Export', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_info' ),
			'apl_post_list_page_apl_settings',
			'side',
			'core'
		);
		$title = '<a id="info16" class="info_a_link" style="float:right;">Export/Import Info<span class="ui-icon ui-icon-info info-icon" style="float:right"></span></a>';
		add_meta_box(
			'apl-general',
			$title . __( 'General Settings', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_general' ),
			'apl_post_list_page_apl_settings',
			'normal', // 'normal', 'advanced', 'side'.
			'high' // 'high', 'sorted', 'core', 'default', 'low'.
		);
		add_meta_box(
			'apl-import-export',
			__( 'Import / Export', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_import_export' ),
			'apl_post_list_page_apl_settings',
			'advanced',
			'core'
		);
	}

	/**
	 * Settings Info Meta Box
	 *
	 * @since 0.4.0
	 * 
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function settings_meta_box_info( $post, $metabox ) {
		include( APL_DIR . 'admin/settings-meta-box-info.php' );
	}

	/**
	 * Settings General Settings Meta Box
	 *
	 * @since 0.4.0
	 * 
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function settings_meta_box_general( $post, $metabox ) {
		include( APL_DIR . 'admin/settings-meta-box-general.php' );
	}

	/**
	 * Settings Import/Export Meta Box
	 *
	 * @since 0.4.0
	 * 
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function settings_meta_box_import_export( $post, $metabox ) {
		include( APL_DIR . 'admin/settings-meta-box-import-export.php' );
	}

	/**
	 * Post List Filter Meta box Template.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 * @return void
	 */
	public function post_list_meta_box_filter( $post, $metabox ) {
		$apl_post_tax           = $this->get_post_tax();
		$apl_tax_terms          = $this->get_tax_terms();
		$apl_display_post_types = apl_get_display_post_types();

		include( APL_DIR . 'admin/post-list-meta-box-filter.php' );
	}

	/**
	 * Post List Design Meta box Template.
	 *
	 * Hook '$this->post_list_meta_boxes()', renders the Design Meta Box Template.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 * @return void
	 */
	public function post_list_meta_box_display( $post, $metabox ) {
		include( APL_DIR . 'admin/post-list-meta-box-design.php' );
	}

	/**
	 * Draft Post List.
	 *
	 * Hook for draft Post Transitions with Post Lists.
	 *
	 * @since 0.4.0
	 *
	 * @param int      $post_id
	 * @param WP_Post  $post
	 * @return void
	 */
	public function draft_post_list( $post_id, $post ) {
		if ( isset( $_REQUEST['action'] ) ) {
			if ( 'untrash' === $_REQUEST['action'] ) {
				return;
			}
		}
		if ( empty( $post->post_name ) ) {
			if ( empty( $post->post_title ) ) {
				$post->post_title = 'APL-' . $post->ID;
			}
			
			remove_action('draft_apl_post_list', array( $this, 'draft_post_list' ) );
			$post->post_name = sanitize_title_with_dashes( $post->post_title );
			
			$postarr = array(
				'ID' => $post->ID,
				'post_title' => $post->post_title,
				'post_name' => $post->post_name,
				//'post_status' => $post->post_status,
			);
			wp_update_post( $postarr );
			
			add_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ), 10, 2 );
		}
		
		$this->post_list_process( $post_id, $post );
	}

	/**
	 * Save Post List.
	 *
	 * Hook for saving object during post transitions.
	 *
	 * @since 0.4.0
	 *
	 * @see Hook: {status}_{post_type}
	 * @link https://codex.wordpress.org/Post_Status_Transitions
	 *
	 * @param int     $post_id Old post ID.
	 * @param WP_Post $post    Current Post object.
	 * @return void
	 */
	public function save_post_list( $post_id, $post ) {
		// CHECK AJAX REFERENCE.
		
		// ACTION = editpost
		// Doesn't work if there is no action ( Add New )
		//check_admin_referer( 'update-post_' . $post_id );
		
		//add_action( 'private_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		//add_action( 'publish_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		//add_action( 'pending_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		//add_action( 'future_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		
		$this->post_list_process( $post_id, $post );
		
	}

	// MOVE TO APL_POST_LIST???
	/**
	 * WP_Trash_Post APL Post List
	 *
	 * Host for trash post transitions with Post Lists.
	 *
	 * @since 0.4.0
	 *
	 * @param type $post_id
	 * @return boolean
	 */
	public function action_wp_trash_post_apl_post_list( $post_id ) {
		$args = array(
			'post__in'    => array( $post_id ),
			'post_type'   => 'apl_post_list',
			//'post_status' => 'trash',
		);
		$post_lists = new WP_Query( $args );
		if ( 1 > $post_lists->post_count ) {
			return false;
		}
		$post_list = $post_lists->post;
		
		if ( 'apl_post_list' !== $post_list->post_type ) {
			return;
		}
		
		$apl_post_list = new APL_Post_List( $post_list->post_name );
		
		$apl_design = new APL_Design( $apl_post_list->pl_apl_design );
		
		$new_post_list_slug = $post_list->post_name . '__trashed';
		$new_design_slug = '';
		if ( !empty( $post_list->post_name ) ) {
			$slug_suffix = apply_filters( 'apl_design_slug_suffix', '-design' );
			$design_slug = apply_filters( 'apl_design_trash_slug', $new_post_list_slug );
			$new_design_slug = $design_slug . $slug_suffix;
		}
		$apl_post_list->pl_apl_design = $new_design_slug;
		$apl_design->slug = $new_design_slug;
		
		$apl_design->save_design();
	}

	/**
	 * Un-Trash for Post List
	 *
	 * Hook for untrash Post Transition with Post Lists.
	 *
	 * @since 0.4.0 
	 *
	 * @param int $post_id
	 * @return boolean
	 */
	public function action_untrash_post_apl_post_list( $post_id ) {
		$args = array(
			'post__in'    => array( $post_id ),
			'post_type'   => 'apl_post_list',
			'post_status' => 'trash',
		);
		$post_lists = new WP_Query( $args );
		if ( 1 > $post_lists->post_count ) {
			return false;
		}
		$post_list = $post_lists->post;
		
		if ( 'apl_post_list' !== $post_list->post_type ) {
			return;
		}
		
		$apl_post_list = new APL_Post_List( $post_list->post_name );
		
		$apl_design = new APL_Design( $apl_post_list->pl_apl_design );
		
		$new_post_list_slug = str_replace( '__trashed', '', $post_list->post_name );
		$new_design_slug = '';
		if ( !empty( $post_list->post_name ) ) {
			$slug_suffix = apply_filters( 'apl_design_slug_suffix', '-design' );
			$design_slug = apply_filters( 'apl_design_trash_slug', $new_post_list_slug );
			$new_design_slug = $design_slug . $slug_suffix;
		}
		$apl_post_list->pl_apl_design = $new_design_slug;
		$apl_design->slug = $new_design_slug;
		
		$apl_design->save_design();
	}

	// MOVE TO APL_POST_LIST???
	/**
	 * WP_Delete_Post APL Post List
	 *
	 * Host for delete post transitions with Post Lists.
	 *
	 * @since 0.4.0
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/before_delete_post
	 *
	 * @param int $post_id
	 * @return boolean
	 */
	public function action_before_delete_post_apl_post_list( $post_id ) {
		
		
		$args = array(
			'post__in'    => array( $post_id ),
			'post_type'   => 'apl_post_list',
			'post_status' => 'trash',
		);
		$post_lists = new WP_Query( $args );
		if ( 1 > $post_lists->post_count ) {
			return false;
		}
		$post_list = $post_lists->post;
		
		if ( 'apl_post_list' !== $post_list->post_type ) {
			return;
		}
		
		$apl_post_list = new APL_Post_List( $post_list->post_name );
		$apl_design = new APL_Design( $apl_post_list->pl_apl_design );
		
		$apl_design->delete_design();
	}

	/**
	 * Save General Settings
	 *
	 * Uses a lazy REST API built into WP.
	 *
	 * @uses Hook 'admin_post_{SCREEN ID}' ex. 'admin_post_apl_save_general_settings'.
	 * @link https://developer.wordpress.org/reference/hooks/admin_post_action/
	 *
	 * @return void
	 */
	public function save_general_settings() {
		$options = apl_options_load();
		
		if ( isset( $_POST['apl_delete_on_deactivate'] ) ) {
			$input = filter_input( INPUT_POST, 'apl_delete_on_deactivate', FILTER_SANITIZE_STRING );
			if ( 'yes' === $input ) {
				$options['delete_core_db'] = true;
			} elseif ( 'no' === $input ) {
				$options['delete_core_db'] = false;
			} else {
				$options['delete_core_db'] = false;
			}
		}
		
		if ( isset( $_POST['apl_default_empty_enable'] ) ) {
			$input = filter_input( INPUT_POST, 'apl_delete_on_deactivate', FILTER_SANITIZE_STRING );
			if ( 'yes' === $input ) {
				$options['default_empty_enable'] = true;
			} elseif ( 'no' === $input ) {
				$options['default_empty_enable'] = false;
			} else {
				$options['default_empty_enable'] = true;
			}
		}
		
		$options['default_empty_output'] = '';
		if ( isset( $_POST['apl_default_empty_message'] ) ) {
			// Sanatize with admins?
			$tmp_empty_messaage = filter_input( INPUT_POST, 'apl_default_empty_message', FILTER_UNSAFE_RAW );
			$options['default_empty_output'] = $tmp_empty_messaage;
		}
		
		apl_options_save( $options );
		
		wp_redirect( 'edit.php?post_type=apl_post_list&page=apl_settings' );
		exit;
	}

	/**
	 * Settings Page AJAX Hooks
	 *
	 * Add AJAX hooks for Settings Page.
	 *
	 * @uses 'wp_ajax_{name}'
	 *
	 * @return void
	 */
	public function add_settings_ajax_hooks() {
		add_action( 'wp_ajax_apl_settings_export', array( $this, 'ajax_settings_export' ) );
		add_action( 'wp_ajax_apl_export', 'apl_export' );
		
		add_action( 'wp_ajax_apl_settings_import', array( $this, 'ajax_settings_import' ) );
		add_action( 'wp_ajax_apl_import', 'apl_import' );
	}

	/**
	 * AJAX Settings Page Export
	 *
	 * Handles the AJAX call for exporting data.
	 *
	 * @return void
	 */
	public function ajax_settings_export() {
		check_ajax_referer( 'apl_settings_export' );

		$rtn_data = array(
			'action'             => 'apl_export',
			'_ajax_nonce'        => wp_create_nonce( 'apl_export' ),
		);

		$tmp_filename = 'file_export_name';
		if ( isset( $_POST['filename'] ) ) {
			$tmp_filename = filter_input( INPUT_POST, 'filename', FILTER_SANITIZE_STRING );
		}
		$rtn_data['filename'] = $tmp_filename;

		$export_data = array(
			'version'            => APL_VERSION,
			'apl_post_list_arr'  => array(),
			'apl_design_arr'     => array(),
		);

		$args = array(
			'post_type' => 'apl_post_list',
			'post_status' => 'publish',
			//'field' => 'slug',
			'post_per_page' => -1,
		);
		$apl_post_lists =  new WP_Query( $args );

		foreach ( $apl_post_lists->posts as $post_obj ) {
			$apl_post_list  = new APL_Post_List( $post_obj->post_name );
			$apl_design     = new APL_Design( $apl_post_list->pl_apl_design );

			$export_data['apl_post_list_arr'][] = $apl_post_list->slug;
			$export_data['apl_design_arr'][] = $apl_design->slug;
		}

		update_option( 'apl_export_data', $export_data );

		echo json_encode( $rtn_data );

		die();
	}

	/**
	 * AJAX Settings Import
	 *
	 * @since 0.4.0
	 *
	 * @uses add_settings_ajax_hooks().
	 * @uses wp_ajax_apl_settings_import.
	 *
	 * @return void
	 */
	public function ajax_settings_import() {
		check_ajax_referer( 'apl_settings_import' );

		$raw_content = array();
		$i = 0;
		while ( isset( $_FILES[ 'file_' . $i ] ) ) {
			$file_arr = $_FILES[ 'file_' . $i ];
			$file_content = file_get_contents( $file_arr['tmp_name'] );
			$raw_content[] = json_decode( $file_content );
			$i++;
		}

		// TODO UPGRADER
		$imported_content = array();
		foreach ( $raw_content as $v1_content ) {
			$update_items = array();
			if ( isset( $v1_content->version ) ) {
				$version = $v1_content->version;
			} else {
				return new WP_Error( 'apl_admin', __( 'Version number is not present in imported file.', 'advanced-post-list' ) );
				die();
			}
			if ( isset( $v1_content->presetDbObj ) ) {

				$update_items['preset_db'] = $v1_content->presetDbObj;

			}
			if ( isset( $v1_content->apl_post_list_arr ) ) {
				$update_items['apl_post_list_arr'] = $v1_content->apl_post_list_arr;
			}
			if ( isset( $v1_content->apl_design_arr ) ) {
				$update_items['apl_design_arr'] = $v1_content->apl_design_arr;
			}

			$updater = new APL_Updater( $version, $update_items );

			$imported_content[] = array(
				'apl_post_list_arr'  => $updater->apl_post_list_arr,
				'apl_design_arr'     => $updater->apl_design_arr,
			);

		}

		$overwrite_apl_post_list = array();
		$overwrite_apl_design = array();

		$data_overwrite_post_list = array();
		$data_overwrite_design = array();

		foreach ( $imported_content as $v1_content ) {
			// POST LISTS.
			foreach ( $v1_content['apl_post_list_arr'] as $v2_post_list ) {
				$db_post_list = new APL_Post_List( $v2_post_list->slug );
				if ( 0 !== $db_post_list->id ) {
					$overwrite_apl_post_list[] = $v2_post_list;
					$data_overwrite_post_list[] = $v2_post_list->slug;
				} else {
					// Add Variable to Database.
					$this->import_process_post_list( $v2_post_list );
				}
			}

			// DESIGNS.
			foreach ( $v1_content['apl_design_arr'] as $v2_design ) {
				$db_design = new APL_Design( $v2_design->slug );
				if ( 0 !== $db_design->id ) {
					$overwrite_apl_design[] = $v2_design;
					$data_overwrite_design[] = $v2_design->slug;
				} else {
					// Add Variable to Database.
					$this->import_process_design( $v2_design );
				}
			}
		}

		update_option( 'apl_import_overwrite_post_list', $overwrite_apl_post_list );
		update_option( 'apl_import_overwrite_design', $overwrite_apl_design );

		$rtn_data = array(
			'action'               => 'apl_import',
			'_ajax_nonce'          => wp_create_nonce( 'apl_import' ),
			'overwrite_post_list'  => $data_overwrite_post_list,
			'overwrite_design'     => $data_overwrite_design,
		);

		echo json_encode( $rtn_data );

		die();
	}

	/**
	 * Process Import for Post Lists
	 *
	 * @since 0.4.0
	 *
	 * @param APL_Post_List $apl_post_list
	 * @return void
	 */
	private function import_process_post_list( $apl_post_list ) {
		$tmp_apl_post_list = new APL_Post_List( $apl_post_list->slug );
		
		$tmp_apl_post_list->title                = $apl_post_list->title                ?: $tmp_apl_post_list->title;
		$tmp_apl_post_list->post_type            = $apl_post_list->post_type            ? json_decode( json_encode( $apl_post_list->post_type ), true ) : $tmp_apl_post_list->post_type ;
		$tmp_apl_post_list->tax_query            = $apl_post_list->tax_query            ? json_decode( json_encode( $apl_post_list->tax_query ), true ) : $tmp_apl_post_list->tax_query;
		$tmp_apl_post_list->post_parent__in      = $apl_post_list->post_parent__in      ? json_decode( json_encode( $apl_post_list->post_parent__in ), true ) : $tmp_apl_post_list->post_parent__in;
		$tmp_apl_post_list->post_parent_dynamic  = $apl_post_list->post_parent_dynamic  ? json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true ) : $tmp_apl_post_list->post_parent_dynamic;
		$tmp_apl_post_list->posts_per_page       = $apl_post_list->posts_per_page       ?: $tmp_apl_post_list->posts_per_page;
		$tmp_apl_post_list->order_by             = $apl_post_list->order_by             ?: $tmp_apl_post_list->order_by;
		$tmp_apl_post_list->order                = $apl_post_list->order                ?: $tmp_apl_post_list->order;
		$tmp_apl_post_list->post_status          = $apl_post_list->post_status          ? json_decode( json_encode( $apl_post_list->post_status ), true ) : $tmp_apl_post_list->post_status;
		$tmp_apl_post_list->perm                 = $apl_post_list->perm                 ?: $tmp_apl_post_list->perm;
		$tmp_apl_post_list->author__bool         = $apl_post_list->author__bool         ?: $tmp_apl_post_list->author__bool;
		$tmp_apl_post_list->author__in           = $apl_post_list->author__in           ?: $tmp_apl_post_list->author__in;
		$tmp_apl_post_list->ignore_sticky_posts  = $apl_post_list->ignore_sticky_posts  ?: $tmp_apl_post_list->ignore_sticky_posts;
		$tmp_apl_post_list->post__not_in         = $apl_post_list->post__not_in         ?: $tmp_apl_post_list->post__not_in;
		$tmp_apl_post_list->pl_exclude_current   = $apl_post_list->pl_exclude_current   ?: $tmp_apl_post_list->pl_exclude_current;
		$tmp_apl_post_list->pl_exclude_dupes     = $apl_post_list->pl_exclude_dupes     ?: $tmp_apl_post_list->pl_exclude_dupes;
		$tmp_apl_post_list->pl_apl_design        = $apl_post_list->pl_apl_design        ?: $tmp_apl_post_list->pl_apl_design;
		
		$tmp_apl_post_list->save_post_list();
	}

	/**
	 * Process Import for Designs
	 *
	 * @since 0.4.0
	 *
	 * @param APL_Design $apl_design
	 */
	private function import_process_design( $apl_design ) {
		$tmp_apl_design = new APL_Design( $apl_design->slug );

		$tmp_apl_design->title    = $apl_design->title    ?: $tmp_apl_design->title;
		$tmp_apl_design->before   = $apl_design->before   ?: $tmp_apl_design->before;
		$tmp_apl_design->content  = $apl_design->content  ?: $tmp_apl_design->content;
		$tmp_apl_design->after    = $apl_design->after    ?: $tmp_apl_design->after;
		$tmp_apl_design->empty    = $apl_design->empty    ?: $tmp_apl_design->empty;

		$tmp_apl_design->save_design();
	}
	/**
	 * Process Post List Form.
	 *
	 * Gathers data from the Post List edit page.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see $this->save_post_list()
	 *
	 * @param int     $post_id Old post ID.
	 * @param WP_Post $post    Current Post object.
	 * @return void
	 */
	private function post_list_process( $post_id, $post ) {
		$old_post = get_post( $post_id );
		$apl_post_list = new APL_Post_List( $old_post->post_name );

		// post_type[0,1,2]       = 'any' || 'none'    || array(); CANNOT USE 'any' IN ARRAY.
		// tax_query[pt1,pt2,pt3] = array( empty )     || array( query ).
		// post_parent__in.
		// post_parent_dynamic.
		$tmp_post_type = array();
		$tmp_tax_query = array();

		$tmp_post_parent__in = array();
		$tmp_post_parent_dynamic = array();

		$post_type_names = apl_get_display_post_types();
		$post_type_names = array_merge( array( 'any' => __( 'Any / All', 'advanced-post-list' ) ), $post_type_names );
		foreach ( $post_type_names as $k_pt_slug => $v_pt_title ) {
			// POST TYPES (ACTIVE).
			if ( isset( $_POST[ 'apl_toggle-' . $k_pt_slug ] ) ) {
				$tmp_tax_query[ $k_pt_slug ] = array();
				
				// If 'Any / All' is toggled, then treat 'any' differently and skip the rest.
				if ( 'any' === $k_pt_slug ) {
					// 'any' TAXONOMY.
					$tmp_post_type[] = 'any';

					if ( isset( $_POST['apl_multiselect_taxonomies-any'] ) ) {
						$tmp_tax_query[ $k_pt_slug ] = $this->post_list_process_tax_query( $k_pt_slug );
					}

					break;
				} else {
					// POST TYPE TAXONOMIES.
					$tmp_post_type[] = array( $k_pt_slug );

					if ( isset( $_POST['apl_multiselect_taxonomies-' . $k_pt_slug] ) ) {
						$tmp_tax_query[ $k_pt_slug ] = $this->post_list_process_tax_query( $k_pt_slug );
					}

					// PAGE PARENTS.
					if ( is_post_type_hierarchical( $k_pt_slug ) ) {
						
						$tmp_post_parent_dynamic[ $k_pt_slug ] = false;
						if ( isset( $_POST[ 'apl_page_parent_dynamic-' . $k_pt_slug ] ) ) {
							$tmp_post_parent_dynamic[ $k_pt_slug ] = true;
						}

						$page_args = array(
							'post_type' => $k_pt_slug,
							'posts_per_page'  => -1,
							'order'           => 'DESC',
							'orderby'         => 'id',
						);
						$page_query = new WP_Query( $page_args );
						while ( $page_query->have_posts() ) {
							$page_query->the_post();

							if ( isset( $_POST[ 'apl_page_parent-' . $k_pt_slug . '-' . $page_query->post->ID ] ) ) {
								if ( ! isset( $tmp_post_parent__in[ $k_pt_slug ] ) ) {
									$tmp_post_parent__in[ $k_pt_slug ] = array();
								}
								$tmp_post_parent__in[ $k_pt_slug ][] = $page_query->post->ID;
							}
						}
						wp_reset_postdata();
					}
				}
			}
		}// End Foreach Post_Types.
		$apl_post_list->post_type = $tmp_post_type;
		$apl_post_list->tax_query = $tmp_tax_query;

		$apl_post_list->post_parent__in = $tmp_post_parent__in;
		$apl_post_list->post_parent_dynamic = $tmp_post_parent_dynamic;

		// posts_per_page.
		$tmp_posts_per_page = 5;
		if ( isset( $_POST['apl_posts_per_page'] ) ) {
			$p_posts_per_page = filter_input( INPUT_POST, 'apl_posts_per_page', FILTER_SANITIZE_NUMBER_INT );
			$tmp_posts_per_page = intval( $p_posts_per_page );
		}
		$apl_post_list->posts_per_page = $tmp_posts_per_page;

		// order_by.
		// order.
		$tmp_order_by = 'none';
		$tmp_order    = 'DESC';
		if ( isset( $_POST['apl_order_by'] ) ) {
			$order_by = filter_input( INPUT_POST, 'apl_order_by', FILTER_SANITIZE_STRING );
			$tmp_order_by = $order_by;

			if ( 'none' !== $order_by && isset( $_POST['apl_order'] ) ) {
				$order = filter_input( INPUT_POST, 'apl_order', FILTER_SANITIZE_STRING );
				$tmp_order = $order;
			}
		}
		$apl_post_list->order_by = $tmp_order_by;
		$apl_post_list->order    = $tmp_order;

		// post_status = array ( 'public', 'publish' ).
		$tmp_post_status = 'any';
		if( isset( $_POST['apl_post_status_1'] ) ) {
			$p_post_status_1 = array_map( 'sanitize_key', $_POST['apl_post_status_1'] );

			$p_post_status_2 = array();
			if ( 'none' === $p_post_status_1[0] || 'any' === $p_post_status_1[0] ) {
				$tmp_post_status = $p_post_status_1[0];
			} else {
				// add 'public' &| 'private'
				if ( isset( $_POST['apl_post_status_2'] ) ) {
					$p_post_status_2 = array_map( 'sanitize_key', $_POST['apl_post_status_2'] );
				}
				$tmp_post_status = array_merge( $p_post_status_1, $p_post_status_2 );
			}
		}
		$apl_post_list->post_status = $tmp_post_status;

		// perm.
		$tmp_perm = 'none';
		if ( isset( $_POST['apl_perm'] ) ) {
			$tmp_perm = filter_input( INPUT_POST, 'apl_perm', FILTER_SANITIZE_STRING );
		}
		$apl_post_list->perm = $tmp_perm;

		// author_in = (boolean).
		// author = array( ).
		$tmp_author__bool = 'none';
		$tmp_author__in = array();
		if ( isset( $_POST['apl_author__bool']) ) {
			$tmp_author__bool = filter_input( INPUT_POST, 'apl_author__bool', FILTER_SANITIZE_STRING );

			if ( 'none' !== $tmp_author__bool && isset( $_POST['apl_author__in'] ) ) {
				$tmp_author__in = array_map( 'intval', $_POST['apl_author__in'] );
			}
		}
		$apl_post_list->author__bool = $tmp_author__bool;
		$apl_post_list->author__in = $tmp_author__in;

		// post__not_in.
		$tmp_post__not_in = array();
		if ( isset( $_POST['apl_post__not_in'] ) ) {
			$p_post__not_in = filter_input( INPUT_POST, 'apl_post__not_in', FILTER_SANITIZE_STRING );
			if ( ! empty( $p_post__not_in ) ) {
				$tmp_post__not_in = array_map( 'absint', explode( ',', $p_post__not_in ) );
			}
			
		}
		$apl_post_list->post__not_in = $tmp_post__not_in;

		// ignore_stick_posts.
		$tmp_ignore_sticky_posts = true;
		if ( isset( $_POST['apl_sticky_posts'] ) ) {
			$p_ignore_sticky_posts = filter_input( INPUT_POST, 'apl_sticky_posts', FILTER_SANITIZE_STRING );
			$tmp_ignore_sticky_posts = false;
		}
		$apl_post_list->ignore_sticky_posts = $tmp_ignore_sticky_posts;

		// pl_exclude_current.
		$tmp_pl_exclude_current = false;
		if ( isset( $_POST['apl_pl_exclude_current'] ) ) {
			$p_pl_exclude_current = filter_input( INPUT_POST, 'apl_pl_exclude_current', FILTER_SANITIZE_STRING );
			$tmp_pl_exclude_current = true;
		}
		$apl_post_list->pl_exclude_current = $tmp_pl_exclude_current;

		// pl_exclude_dupes.
		$tmp_pl_exclude_dupes = false;
		if ( isset( $_POST['apl_pl_exclude_dupes'] ) ) {
			$p_pl_exclude_dupes = filter_input( INPUT_POST, 'apl_pl_exclude_dupes', FILTER_SANITIZE_STRING );
			$tmp_pl_exclude_dupes = true;
		}
		$apl_post_list->pl_exclude_dupes = $tmp_pl_exclude_dupes;

		$new_design_slug = '';
		if ( !empty( $post->post_name ) ) {
			$slug_suffix = apply_filters( 'apl_design_slug_suffix', '-design' );
			$design_slug = apply_filters( 'apl_design_process_slug', $post->post_name, $this );
			$new_design_slug = $design_slug . $slug_suffix;
		}

		$apl_post_list->pl_apl_design = $this->post_list_process_apl_design( $apl_post_list->pl_apl_design, $new_design_slug );
	}
	
	/**
	 * Process Tax Query.
	 *
	 * Processes the taxonomies and returns 'multiple arrays' simular to $args['tax_query'].
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see WP_Query Args
	 * @link https://gist.github.com/luetkemj/2023628
	 *
	 * @param string $post_type Post Type slug.
	 * @return array Tax_Query used in WP_Query Args.
	 */
	private function post_list_process_tax_query( $post_type ) {
		// Get the list of active taxonomies.
		$p_taxonomies = array_map( 'sanitize_key', $_POST[ 'apl_multiselect_taxonomies-' . $post_type ] );
		$tmp_tax_query = array();
		$tmp_req_tax = 'OR';
		foreach ( $p_taxonomies as $v1_taxonomy ) {
			// Check 'require' as an active checkbox.
			// Else process other checkboxes.
			if ( 'require' === $v1_taxonomy ) {
				$tmp_req_tax = 'AND';
			} else {
				// Check Require Terms.
				$tmp_terms_req = 'IN';
				if ( isset( $_POST[ 'apl_terms_req-' . $post_type . '-' . $v1_taxonomy ] ) ) {
					$tmp_terms_req = 'AND';
				}

				// Check Dynamic Terms.
				$tmp_terms_dynamic = false;
				if ( isset( $_POST[ 'apl_terms_dynamic-' . $post_type . '-' . $v1_taxonomy ] ) ) {
					$tmp_terms_dynamic = true;
				}

				// TERM LOOP.
				$arg_terms = array(
					'taxonomy'   => $v1_taxonomy,
					'hide_empty' => false,
				);
				$terms = get_terms( $arg_terms );
				$tmp_terms = array();
				foreach ( $terms as $v2_term_obj ) {
					// Check 'any' term, and if set, skip other terms. break;
					if ( isset( $_POST[ 'apl_term-' . $post_type . '-' . $v1_taxonomy . '-any' ] ) ) {
						// No reason to have dynamic true with 'any'; fallback method.
						$tmp_terms[] = 0;
						$tmp_terms_dynamic = false;
						break;
					} elseif ( isset( $_POST[ 'apl_term-' . $post_type . '-' . $v1_taxonomy . '-' . $v2_term_obj->term_id ] ) ) {
						$tmp_terms[] = $v2_term_obj->term_id;
					}

				}

				$tmp_tax_query[] =  array(
					'taxonomy'          => $v1_taxonomy,
					'field'             => 'id', // Or 'slug'.
					'terms'             => $tmp_terms,
					'include_children'  => false,
					'operator'          => $tmp_terms_req, // 'IN' | 'AND' | --'NOT IN'--

					//'apl_terms_req'     = $tmp_terms_req;  
					'apl_terms_dynamic' => $tmp_terms_dynamic,
				);
			}
		} // End Foreach Taxonomy.
		$tmp_tax_query['relation'] = $tmp_req_tax;
		
		return $tmp_tax_query;
	}

	/**
	 * Process Design Meta Box.
	 *
	 * Description.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param string $apl_design_slug Current active slug.
	 * @param string $new_design_slug New slug relative to $this->pl_apl_design.
	 * @return string Slug used in $this->pl_apl_design.
	 */
	private function post_list_process_apl_design( $apl_design_slug, $new_design_slug ) {
		$apl_design = new APL_Design( $apl_design_slug );

		// SLUG / KEY.
		if ( $new_design_slug !== $apl_design_slug && '-design' !== $new_design_slug  ) {
			$apl_design->title = $new_design_slug;
			$apl_design->slug = sanitize_title_with_dashes( $new_design_slug );
		}

		// BEFORE.
		$tmp_apl_design_before = '';
		if ( isset( $_POST['apl_before'] ) ) {
			$tmp_apl_design_before = filter_input( INPUT_POST, 'apl_before', FILTER_UNSAFE_RAW );
		}
		$apl_design->before = $tmp_apl_design_before;

		// CONTENT.
		$tmp_apl_design_content = '';
		if ( isset( $_POST['apl_content'] ) ) {
			$tmp_apl_design_content = filter_input( INPUT_POST, 'apl_content', FILTER_UNSAFE_RAW );
		}
		$apl_design->content = $tmp_apl_design_content;

		// AFTER.
		$tmp_apl_design_after = '';
		if ( isset( $_POST['apl_after'] ) ) {
			$tmp_apl_design_after = filter_input( INPUT_POST, 'apl_after', FILTER_UNSAFE_RAW );
		}
		$apl_design->after = $tmp_apl_design_after;

		// EMPTY MESSAGE.
		$tmp_apl_design_empty = '';
		if ( isset( $_POST['apl_empty_enable'] ) && isset( $_POST['apl_empty_message'] ) ) {
			$tmp_apl_design_empty = filter_input( INPUT_POST, 'apl_empty_message', FILTER_UNSAFE_RAW );
		}
		$apl_design->empty = $tmp_apl_design_empty;

		// Save APL_Design.
		$apl_design->save_design();

		// SLUG/KEY.
		$rtn_apl_design_slug = '';
		$rtn_apl_design_slug = $apl_design->slug;

		return $rtn_apl_design_slug;
	}

	/*
	 * *************************************************************************
	 * **** PRIVATE FUNCTIONS **************************************************
	 * *************************************************************************
	 */

	/**
	 * Get Post Type & Taxonomies.
	 *
	 * Gets and returns an array of Post_Types => Taxonomies.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return array Post_Type = > Name, Taxonomy Array.
	 */
	private function get_post_tax() {
		$rtn_post_tax = array();

		$post_types = apl_get_display_post_types();
		
		// Add to rtn {post_type} => {array( taxonomies )}.
		$rtn_post_tax['any']['name'] = __( 'Any / All', 'advanced-post-list' );
		$taxonomy_names = get_taxonomies( '', 'names' );
		foreach ( $taxonomy_names as $name ) {
			$rtn_post_tax['any']['tax_arr'][] = $name;
		}

		foreach ( $post_types as $k_slug => $v_name ) {
			$rtn_post_tax[ $k_slug ]['name'] = $v_name;
			$rtn_post_tax[ $k_slug ]['tax_arr'] = get_object_taxonomies( $k_slug, 'names' );
		}

		// Return Post_Tax.
		return $rtn_post_tax;
	}

	/**
	 * Get Taxonomies & Terms.
	 *
	 * Gets and returns an array of Taxonomies => Terms.
	 *
	 * @see get_terms()
	 * @link https://developer.wordpress.org/reference/functions/get_terms/
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return array Taxonomy => Term.
	 */
	private function get_tax_terms() {
		$rtn_tax_terms = array();

		// Get Taxonomy Names.
		$taxonomy_names = get_taxonomies( '', 'names' );

		// Loop foreach taxonomy. Get terms, and foreach term add to taxonomy.
		foreach ( $taxonomy_names as $taxonomy ) {
			$args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			);
			$terms = get_terms( $args );

			// Set slug.
			$rtn_tax_terms[ $taxonomy ] = array();
			foreach ( $terms as $term ) {
				$rtn_tax_terms[ $taxonomy ][] = $term->term_id;
			}
		}

		// Return Tax_Terms.
		return $rtn_tax_terms;
	}

	/**
	 * Get Post Types to Display.
	 *
	 * Displays a *valid* list of post types that also aren't on the global ignore list.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see $this->_ignore_post_types.
	 *
	 * @return array List of Post Types.
	 */
	private function get_display_post_types() {
		$rtn_post_types = array();

		$post_type_objs = get_post_types( '', 'objects' );
		// Remove ignored Post Types.
		$ignore_post_types = apl_get_display_post_types();
		foreach ( $ignore_post_types as $value ) {
			unset( $post_type_objs[ $value ] );
		}

		foreach ( $post_type_objs as $key => $value ) {
			$rtn_post_types[ $key ] = $value->labels->singular_name;
		}

		return $rtn_post_types;
	}

}
