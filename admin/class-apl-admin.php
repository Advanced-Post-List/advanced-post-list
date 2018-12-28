<?php
/**
 * APL Admin API: APL_Admin Class
 *
 * Admin core object to Advanced Post List
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list\APL_Core
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
	 * Singleton Instance
	 *
	 * @since 0.4.0
	 * @access private
	 * @var null $instance Singleton Class Instance.
	 */
	protected static $instance = null;

	/**
	 * Get Singleton Instance
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
			// TODO - Catch WP Error.
		}
		return static::$instance;
	}

	/**
	 * Throws error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 */
	private function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'advanced-post-list' ), APL_VERSION );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access protected
	 */
	private function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'advanced-post-list' ), APL_VERSION );
	}

	/**
	 * Constructor
	 *
	 * Private Singleton Constructor.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @todo Complete Admin Encapsulation. Concept linked.
	 * @link https://florianbrinkmann.com/en/3815/wordpress-backend-request/
	 */
	private function __construct() {
		// Exit if Non-Admins access this object. Also wrapped in APL_Core.
		if ( ! is_admin() ) {
			return new WP_Error( 'apl_admin', esc_html__( 'You do not have admin capabilities in APL_Admin.', 'advanced-post-list' ) );
		}

		// Initialize Core Class functions.
		$this->_requires();
		apl_notice_set_activation_review_plugin( false, false );

		// Settings Data.
		add_action( 'admin_post_apl_save_general_settings', array( $this, 'save_general_settings' ) );
		// AJAX.
		add_action( 'admin_init', array( $this, 'add_settings_ajax_hooks' ) );

		// Check if wp-admin.php is loaded, and WP_Screen is defined.
		// is_admin_bar_showing().
		if ( defined( 'WP_ADMIN' ) && WP_ADMIN && is_blog_admin() ) {
			// Menu & Scripts.
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

			// Admin Screens for Modular Screen Execution.
			// NOTE: Settings Page needs to hook sooner (in constructor/here).
			add_action( 'current_screen', array( $this, 'current_screen_hooks' ) );

			// Hook into WP to customize Screens.
			add_action( 'admin_head', array( $this, 'disable_screen_boxes' ) );
			add_action( 'load-edit.php', array( $this, 'post_list_screen_options_all' ) );
			add_action( 'load-post-new.php', array( $this, 'post_list_screen_options_add_new' ) );

			add_action( 'manage_apl_post_list_posts_columns', array( $this, 'post_list_posts_columns' ) );
			add_action( 'manage_apl_post_list_posts_custom_column', array( $this, 'post_list_posts_custom_column' ), 10, 2 );
			add_action( 'manage_edit-apl_post_list_sortable_columns', array( $this, 'post_list_sortable_columns' ) );

			// Editor Meta Boxes.
			add_action( 'add_meta_boxes', array( $this, 'post_list_meta_boxes' ) );
			add_action( 'add_meta_boxes', array( $this, 'settings_meta_boxes' ) );
			if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'APLP_VERSION' ) ) {
				add_action( 'add_meta_boxes', array( $this, 'design_meta_boxes' ) );
			}

			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
			add_action( 'admin_head', array( $this, 'tinymce_extra_vars' ) );
		}
	}

	/**
	 * Requires Files
	 *
	 * Files that this class object needs to load.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 */
	private function _requires() {
		// Example.
		// 'require_once( APL_DIR . 'includes/example.php' )'.
		require_once APL_DIR . 'admin/functions-admin.php';
		require_once APL_DIR . 'admin/export.php';
		require_once APL_DIR . 'admin/import.php';
		require_once APL_DIR . 'admin/class-apl-notices.php';
	}

	/**
	 * Current Admin Screen Hooks
	 *
	 * Adds hooks according to the admin screen in use.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Screen $current_screen Current WP_Screen object.
	 */
	public function current_screen_hooks( $current_screen ) {
		if ( 'apl_post_list' === $current_screen->id || 'edit-apl_post_list' === $current_screen->id ) {
			// Post Data.
			add_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ), 10, 2 );

			add_action( 'private_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'publish_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'pending_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
			add_action( 'future_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );

			// //add_action( 'trash_apl_post_list', array( $this, 'trash_post_list' ), 10, 3 );
			add_action( 'wp_trash_post', array( $this, 'action_wp_trash_post_apl_post_list' ) );
			add_action( 'untrash_post', array( $this, 'action_untrash_post_apl_post_list' ) );
			add_action( 'before_delete_post', array( $this, 'action_before_delete_post_apl_post_list' ) );

			// //if ( 'apl_post_list' === $current_screen->id ) {
			// //	$current_screen->add_help_tab( array(
			// //		'id'      => 'apl_post_list_help', //unique id for the tab.
			// //		'title'   => 'Testing Help Tab',   //unique visible title for the tab.
			// //		'content' => 'Hello World',        //actual help text.
			// //		//'callback' => $callback            //optional function to callback.
			// //	) );
			// //}
		} elseif ( 'apl_design' === $current_screen->id || 'edit-apl_design' === $current_screen->id ) {
			// Post Data.
			add_action( 'draft_apl_design', array( $this, 'draft_design' ), 10, 2 );

			add_action( 'private_apl_design', array( $this, 'save_design' ), 10, 2 );
			add_action( 'publish_apl_design', array( $this, 'save_design' ), 10, 2 );
			add_action( 'pending_apl_design', array( $this, 'save_design' ), 10, 2 );
			add_action( 'future_apl_design', array( $this, 'save_design' ), 10, 2 );

			// //add_action( 'trash_apl_post_list', array( $this, 'trash_design' ), 10, 3 );
			// //add_action( 'wp_trash_post', array( $this, 'action_wp_trash_post_apl_design' ) );
			// //add_action( 'untrash_post', array( $this, 'action_untrash_post_apl_design' ) );
			// //add_action( 'before_delete_post', array( $this, 'action_before_delete_post_apl_design' ) );
		} elseif ( 'adv-post-list_page_apl_settings' === $current_screen->id ) {
			// SETTINGS (Page).
			// DOES NOT always work as intended. Use self::_constructor().
		} else {
			// //add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
			// //add_filter( 'mce_buttons', array( $this, 'mce_register_buttons' ) );
		}// End if().

	}

	/**
	 * APL Admin Menu
	 *
	 * Adds the Admin Menu and Scripts for APL.
	 *
	 * @since 0.4.0
	 *
	 * @see 'admin_menu' hook
	 * @see wp-admin/admin-header.php
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Advanced Post List', 'advanced-post-list' ),
			__( 'Adv. Post List', 'advanced-post-list' ),
			'administrator',
			'advanced_post_list',
			'edit.php?post_type=apl_post_list', // Callback function if dashboard is added.
			'dashicons-welcome-widgets-menus',
			58
		);

		// TODO Add APL Dashboard.
		// All Post Lists (Submenu) - Submenu setting is added during CPT registration.
		// Add New (Submenu).
		add_submenu_page(
			'advanced_post_list',
			__( 'Add New Post List', 'advanced-post-list' ),
			__( '- New Post List', 'advanced-post-list' ),
			'administrator',
			'post-new.php?post_type=apl_post_list'
		);

		// Settings (Submenu).
		add_submenu_page(
			'advanced_post_list',
			// // edit.php?post_type=apl_post_list',
			__( 'APL Settings', 'advanced-post-list' ),
			__( 'Settings', 'advanced-post-list' ),
			'administrator',
			'apl_settings',
			array( $this, 'submenu_settings_page' )
		);
		add_action( 'admin_init', array( $this, 'settings_register_settings' ) );

		// TODO - Add Help API.
		// EXTENSIONS.
		do_action( 'apl_admin_menu_ext' );
	}

	/**
	 * Submenu Callback for Settings Page
	 *
	 * @since 0.4.0
	 */
	public function submenu_settings_page() {
		apl_get_template( 'admin/settings-page.php' );
	}

	/**
	 * Registers Input Settings for Settings Page
	 *
	 * @since 0.4.0
	 */
	public function settings_register_settings() {
		register_setting( 'apl_settings_general', 'apl_delete_on_deactivation', 'strval' );
		register_setting( 'apl_settings_general', 'apl_default_empty_enable', 'strval' );
		register_setting( 'apl_settings_general', 'apl_default_empty_message', 'strval' );

		// //register_setting( 'apl_settings_import_export', 'apl_export_file_name', 'strval' );
		// //register_setting( 'apl_settings_import_export', 'apl_import_opt', 'strval' );
		// //register_setting( 'apl_settings_import_export', 'apl_import_file', 'strval' );
		// //register_setting( 'apl_settings_import_export', 'apl_restore_database', 'strval' );
	}

	/**
	 * APL Admin Enqueue Scripts & Styles
	 *
	 * Loads APL scripts and styles. If not in APL Admin Pages, then remove.
	 *
	 * @since 0.4.0
	 *
	 * @see wp-admin/admin-header.php
	 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 *
	 * @param string $hook_suffix The suffix for the current Admin page.
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

		if ( 'apl_post_list' === $screen->id || 'edit-apl_post_list' === $screen->id || 'apl_design' === $screen->id || 'edit-apl_design' === $screen->id ) {
			/*
			 * ************** AJAX ACTION HOOKS ***************************
			 */

			// TODO - Add meta box to side to load different presets from 'edit.php'.
			// //add_action( 'wp_ajax_apl_load_preset', array( $this, 'hook_ajax_load_preset' ) );

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
					'jquery-ui-position',
					'jquery-ui-tooltip',

				),
				APL_VERSION,
				true
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

			global $wp_version;
			if ( version_compare( $wp_version, '4.9', '>' ) && ( 'apl_post_list' === $screen->id || 'apl_design' === $screen->id ) ) {
				// Enqueue code editor and settings for manipulating HTML.
				// https://developer.wordpress.org/reference/functions/wp_enqueue_code_editor/
				$args = array( 'type' => 'application/x-httpd-php' );
				$settings = wp_enqueue_code_editor( $args );

				if ( false !== $settings ) {
					wp_add_inline_script(
						'code-editor',
						sprintf(
							'jQuery( function() { wp.codeEditor.initialize( "apl_textarea_before", %s ); } );',
							wp_json_encode( $settings )
						)
					);
					wp_add_inline_script(
						'code-editor',
						sprintf(
							'jQuery( function() { wp.codeEditor.initialize( "apl_textarea_content", %s ); } );',
							wp_json_encode( $settings )
						)
					);
					wp_add_inline_script(
						'code-editor',
						sprintf(
							'jQuery( function() { wp.codeEditor.initialize( "apl_textarea_after", %s ); } );',
							wp_json_encode( $settings )
						)
					);
					// Empty is disabled for now, but will need to be hidden when checkbox is unchecked.
					// Hold off until 5.0.
				}
			}

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

			// Get values for variables to localize into JS files.
			// POST => TAXONOMIES.
			$data_post_tax = apl_get_post_tax();

			// TAXONOMIES => TERMS.
			$data_tax_terms = apl_get_tax_terms();

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

			$admin_localize    = array();
			$admin_ui_localize = array(
				'post_tax'  => $data_post_tax,
				'tax_terms' => $data_tax_terms,
				'trans'     => $data_ui_trans,
			);

			// Add variables to JS files.
			// '../admin/js/admin.js'.
			// '../admin/js/admin-ui.js'.
			wp_localize_script( 'apl-admin-js', 'apl_admin_local', $admin_localize );
			wp_localize_script( 'apl-admin-ui-js', 'apl_admin_ui_local', $admin_ui_localize );
		// //} elseif ( 'apl_design' === $screen->id || 'edit-apl_design' === $screen->id ) {
			// TODO Add handling APL Designs without extra code from APL_Post_Lists..
		} elseif ( 'adv-post-list_page_apl_settings' === $screen->id ) {
			// If we are not viewing APL Post List area, then return.
			// SETTINGS PAGE.
			// SCRIPTS.
			wp_register_script(
				'apl-settings-js',
				APL_URL . 'admin/js/settings.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					'jquery-ui-button',
					'jquery-ui-dialog',
				),
				APL_VERSION,
				true
			);
			wp_register_script(
				'apl-settings-ui-js',
				APL_URL . 'admin/js/settings-ui.js',
				array(
					'jquery',
					'jquery-ui-core',
					'jquery-ui-widget',
					'jquery-ui-dialog',
					'jquery-ui-position',
					'jquery-ui-tooltip',
				),
				APL_VERSION,
				true
			);

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
				'default_alert_title'           => __( 'Alert', 'advanced-post-list' ),
				'default_alert_message'         => __( 'No Message to Display.', 'advanced-post-list' ),
				'fileName_empty_alert_title'    => __( 'Filename Required', 'advanced-post-list' ),
				'fileName_empty_alert_message'  => __( 'A filename doesn\'t exist. \n Please enter a filename before exporting.', 'advanced-post-list' ),
				'import_no_file_message'        => __( 'No file(s) selected. Please choose a JSON file to upload.', 'advanced-post-list' ),
				'import_no_file_title'          => __( 'No File', 'advanced-post-list' ),
				'import_invalid_file_message'   => __( 'Invalid file type. Please choose a JSON file to upload.', 'advanced-post-list' ),
				'import_invalid_file_title'     => __( 'Invalid File', 'advanced-post-list' ),
				'import_success_message'        => __( 'Data successfully imported.', 'advanced-post-list' ),
				'import_success_title'          => __( 'Complete', 'advanced-post-list' ),
				'import_overwrite_dialog_title' => __( 'Overwrite Presets', 'advanced-post-list' ),
				'fileName_char_alert_title'     => __( 'Illegal Characters', 'advanced-post-list' ),
				'fileName_char_alert_message1'  => __( 'Cannot use (< > : " / \\ | , ? *).', 'advanced-post-list' ),
				'fileName_char_alert_message2'  => __( 'Please rename your filename.', 'advanced-post-list' ),

			);
			$trans_ui_arr = array(
				'fileName_char_alert_title'    => __( 'Illegal Characters', 'advanced-post-list' ),
				'fileName_char_alert_message1' => __( 'Cannot use (< > : " / \\ | , ? *).', 'advanced-post-list' ),
				'fileName_char_alert_message2' => __( 'Please rename your filename.', 'advanced-post-list' ),
			);

			$settings_localize = array(
				'export_nonce'          => wp_create_nonce( 'apl_settings_export' ),
				'import_nonce'          => wp_create_nonce( 'apl_settings_import' ),
				'restoreDefaultsNonce'  => wp_create_nonce( 'apl_settings_restore_defaults' ),
				'trans'                 => $trans_arr,
			);

			$settings_ui_localize = array(
				'trans' => $trans_ui_arr,
			);

			wp_localize_script( 'apl-settings-js', 'apl_settings_local', $settings_localize );
			wp_localize_script( 'apl-settings-ui-js', 'apl_settings_ui_local', $settings_ui_localize );

			do_action( 'add_meta_boxes', $hook_suffix );
			$screen_args = array(
				'max'     => 2,
				'default' => 2,
			);
			add_screen_option( 'layout_columns', $screen_args );

		} else {
			// REGISTER.
			// LOCALIZE.
			// ENQUEUE.
			wp_enqueue_style(
				'apl-admin-wp-editor-css',
				APL_URL . 'admin/css/wp-editor.css',
				false,
				APL_VERSION,
				false
			);
		}// End if().
	}

	/**
	 * Disables/Hides Screen Option Settings
	 *
	 * Disables / Hides the Screen Option's display Meta Boxes Settings. Basically
	 * prevents certain Meta Boxes from being hidden, and forces the box to display.
	 *
	 * @since 0.4.0
	 *
	 * @see 'admin_head' hook.
	 * @link https://wordpress.stackexchange.com/questions/149602/hiding-metabox-from-screen-options-pull-down
	 */
	public function disable_screen_boxes() {
		echo '<style>label[for=apl-post-list-filter-hide] { display: none; }</style>';
		echo '<style>#apl-post-list-filter { display: block; }</style>';
	}

	/**
	 * Screen Options for 'All Post List' page
	 *
	 * Hook 'load-edit.php', sets additional Screen Options.
	 *
	 * @see 'load-edit.php' hook.
	 * @since 0.4.0
	 */
	public function post_list_screen_options_all() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if ( ! is_object( $screen ) || 'edit-apl_post_list' !== $screen->id ) {
			return;
		}

		$options = $screen->get_options();
	}

	/**
	 * Screen Options for 'Add New' page
	 *
	 * Hook 'load-post-new.php', sets additional Screen Options.
	 *
	 * @see 'load-post-new.php' hook.
	 * @since 0.4.0
	 */
	public function post_list_screen_options_add_new() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if ( ! is_object( $screen ) || 'apl_post_list' !== $screen->id ) {
			return;
		}
		$options = $screen->get_options();
	}

	/**
	 * Post List All Posts Columns
	 *
	 * Adds additional columns to All Post Lists page.
	 *
	 * @since 0.4.0
	 *
	 * @see 'manage_apl_post_list_posts_columns'
	 * @uses manage_${post_type}_posts_columns
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns
	 *
	 * @param array $columns Columns use in the 'All Post Lists' page.
	 * @return array
	 */
	public function post_list_posts_columns( $columns ) {
		$tmp_date = $columns['date'];
		unset( $columns['date'] );

		$columns['post_name']     = __( 'Slug', 'advanced-post-list' );
		$columns['apl_shortcode'] = __( 'Shortcode', 'advanced-post-list' );

		$columns['date'] = $tmp_date;

		return $columns;
	}

	/**
	 * Post List Custom Column
	 *
	 * Adds content to custom column.
	 *
	 * @since 0.4.0
	 *
	 * @uses manage_${post_type}_posts_columns hook.
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
	 *
	 * @param array $column  The name of the column to display. Default: None.
	 * @param int   $post_id The ID of the current post. Can also be taken from the global $post->ID. Default: None.
	 */
	public function post_list_posts_custom_column( $column, $post_id ) {
		$args       = array(
			'post__in'    => array( $post_id ),
			'post_type'   => 'apl_post_list',
			'post_status' => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
		);
		$post_lists = new WP_Query( $args );
		$post_list  = $post_lists->post;

		switch ( $column ) {
			case 'post_name':
				echo esc_attr( $post_list->post_name );
				break;
			case 'apl_shortcode':
				echo '<input value="[post_list name=\'' . esc_attr( $post_list->post_name ) . '\']" type="text" size="32" onfocus="this.select();" onclick="this.select();" readonly="readonly" />';
				break;
		}
	}

	/**
	 * Post List Sortable Columns
	 *
	 * Sets Custom Columns to be sortable.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/manage_this-screen-id_sortable_columns/
	 *
	 * @param array $columns An array of sortable columns.
	 * @return array
	 */
	public function post_list_sortable_columns( $columns ) {
		$columns['post_name'] = 'post_name';

		return $columns;
	}

	/**
	 * Post List Meta Boxes
	 *
	 * Hook 'add_meta_boxes', adds meta boxes used in post lists.
	 *
	 * @since 0.4.0
	 *
	 * @see $this->_construct Used by.
	 * @see 'add_meta_boxes' hook.
	 * @see wp-admin/includes/template.php
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function post_list_meta_boxes() {
		add_meta_box(
			'apl-post-list-filter',
			__( 'Filter Settings', 'advanced-post-list' ),
			array( $this, 'post_list_meta_box_filter' ),
			'apl_post_list',
			// 'normal', 'advanced', 'side'.
			'normal',
			// 'high', 'sorted', 'core', 'default', 'low'.
			'high'
		);
		add_meta_box(
			'apl-post-list-display',
			__( 'Display Settings', 'advanced-post-list' ),
			array( $this, 'post_list_meta_box_design' ),
			'apl_post_list',
			'normal',
			'core'
		);
	}

	/**
	 * Design Meta Boxes
	 *
	 * Hook 'add_meta_boxes', adds meta boxes used in designs.
	 *
	 * @since 0.4.0
	 *
	 * @see $this->_construct Used by.
	 * @see 'add_meta_boxes' hook.
	 * @see wp-admin/includes/template.php
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function design_meta_boxes() {
		add_meta_box(
			'apl-post-list-display',
			__( 'Display Settings', 'advanced-post-list' ),
			array( $this, 'design_meta_box_design' ),
			'apl_design',
			'normal',
			'core'
		);
	}

	/**
	 * Add Settings Page Meta Boxes
	 *
	 * @see $this->_construct Used by.
	 * @see 'add_meta_boxes' hook.
	 * @see add_meta_box();
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function settings_meta_boxes() {
		add_meta_box(
			'apl-info',
			__( 'About', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_info' ),
			'adv-post-list_page_apl_settings',
			// 'normal', 'advanced', 'side'.
			'side',
			// 'high', 'sorted', 'core', 'default', 'low'.
			'core'
		);
		// TODO - Add Documentation Link to Admin Page/Metabox documentation.
		// //$title = '<a id="info16" class="info_a_link" style="float:right;">Export/Import Info<span class="ui-icon ui-icon-info info-icon" style="float:right"></span></a>';
		add_meta_box(
			'apl-general',
			// //$title . __( 'General Settings', 'advanced-post-list' ),
			__( 'General Settings', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_general' ),
			'adv-post-list_page_apl_settings',
			'normal',
			'high'
		);
		add_meta_box(
			'apl-import-export',
			__( 'Import / Export', 'advanced-post-list' ),
			array( $this, 'settings_meta_box_import_export' ),
			'adv-post-list_page_apl_settings',
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
		$args = array(
			'post'    => $post,
			'metabox' => $metabox,
		);

		apl_get_template( 'admin/meta-box/settings-info.php', $args );
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
		$args = array(
			'post'    => $post,
			'metabox' => $metabox,
		);

		apl_get_template( 'admin/meta-box/settings-general.php', $args );
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
		$args = array(
			'post'    => $post,
			'metabox' => $metabox,
		);

		apl_get_template( 'admin/meta-box/settings-import-export.php', $args );
	}

	/**
	 * Post List Filter Meta box Template
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function post_list_meta_box_filter( $post, $metabox ) {
		$args = array(
			'post'                   => $post,
			'metabox'                => $metabox,
			'apl_post_tax'           => apl_get_post_tax(),
			'apl_tax_terms'          => apl_get_tax_terms(),
			'apl_display_post_types' => apl_get_display_post_types(),
		);

		apl_get_template( 'admin/meta-box/post-list-filter.php', $args );
	}

	/**
	 * Post List Design Meta box Template
	 *
	 * Hook '$this->post_list_meta_boxes()', renders the Design Meta Box Template.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function post_list_meta_box_design( $post, $metabox ) {
		$args = array(
			'post'    => $post,
			'metabox' => $metabox,
		);

		apl_get_template( 'admin/meta-box/post-list-design.php', $args );
	}

	/**
	 * Post List Design Meta box Template
	 *
	 * Renders the Design Meta Box Template.
	 *
	 * @see self::design_meta_boxes()
	 * @since 0.4.0
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 */
	public function design_meta_box_design( $post, $metabox ) {
		$args = array(
			'post'    => $post,
			'metabox' => $metabox,
		);

		apl_get_template( 'admin/meta-box/design-design.php', $args );
	}

	/**
	 * Draft Post List
	 *
	 * Hook for draft Post Transitions with Post Lists.
	 *
	 * @since 0.4.0
	 *
	 * @param int      $post_id
	 * @param WP_Post  $post
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

			remove_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ) );
			$post->post_name = sanitize_title_with_dashes( $post->post_title );

			$post_arr = array(
				'ID'         => $post->ID,
				'post_title' => $post->post_title,
				'post_name'  => $post->post_name,
				//'post_status' => $post->post_status,
			);
			wp_update_post( $post_arr );

			add_action( 'draft_apl_post_list', array( $this, 'draft_post_list' ), 10, 2 );
		}

		$this->post_list_process( $post_id, $post );
	}

	/**
	 * Save Post List
	 *
	 * Hook for saving object during post transitions.
	 *
	 * @since 0.4.0
	 *
	 * @see self::current_screen_hooks() Used by.
	 * @see private_apl_post_list hook.
	 * @see publish_apl_post_list hook.
	 * @see pending_apl_post_list hook.
	 * @see future_apl_post_list hook.
	 * @see {status}_{post_type} Hook Transitions.
	 * @link https://codex.wordpress.org/Post_Status_Transitions
	 *
	 * @param int     $post_id Old post ID.
	 * @param WP_Post $post    Current Post object.
	 */
	public function save_post_list( $post_id, $post ) {
		// CHECK AJAX REFERENCE.
		// ACTION = editpost
		// Doesn't work if there is no action ( Add New )
		// //check_admin_referer( 'update-post_' . $post_id );

		// //add_action( 'private_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		// //add_action( 'publish_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		// //add_action( 'pending_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );
		// //add_action( 'future_apl_post_list', array( $this, 'save_post_list' ), 10, 2 );

		$this->post_list_process( $post_id, $post );

	}

	/**
	 * Draft Design
	 *
	 * Hook for draft Post Transitions with Designs.
	 *
	 * @since 0.4.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    (New) Post Data content.
	 */
	public function draft_design( $post_id, $post ) {
		if ( isset( $_REQUEST['action'] ) ) {
			if ( 'untrash' === $_REQUEST['action'] ) {
				return;
			}
		}

		if ( empty( $post->post_name ) ) {
			if ( empty( $post->post_title ) ) {
				$post->post_title = 'APL-' . $post->ID;
			}

			remove_action( 'draft_apl_design', array( $this, 'draft_design' ) );
			$post->post_name = sanitize_title_with_dashes( $post->post_title );

			$postarr = array(
				'ID'         => $post->ID,
				'post_title' => $post->post_title,
				'post_name'  => $post->post_name,
			);
			wp_update_post( $postarr );

			add_action( 'draft_apl_design', array( $this, 'draft_design' ), 10, 2 );
		}

		$this->design_process( $post_id, $post );
	}

	/**
	 * Save Design
	 *
	 * Hook for saving object during post transitions.
	 *
	 * @since 0.4.0
	 *
	 * @see self::current_screen_hooks() Used by.
	 * @see private_apl_design hook.
	 * @see publish_apl_design hook.
	 * @see pending_apl_design hook.
	 * @see future_apl_design hook.
	 * @see {status}_{post_type} Hook Transitions.
	 * @link https://codex.wordpress.org/Post_Status_Transitions
	 *
	 * @param int     $post_id Old post ID.
	 * @param WP_Post $post    Current Post object.
	 */
	public function save_design( $post_id, $post ) {
		$this->design_process( $post_id, $post );
	}

	/**
	 * WP_Trash_Post APL Post List
	 *
	 * Host for trash post transitions with Post Lists.
	 *
	 * @since 0.4.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_trash_post/
	 *
	 * @param int $post_id Post ID. Default is ID of the global $post if EMPTY_TRASH_DAYS equals true.
	 * @return boolean
	 */
	public function action_wp_trash_post_apl_post_list( $post_id ) {
		$args = array(
			'post__in'  => array( $post_id ),
			'post_type' => 'apl_post_list',
			//'post_status' => 'trash',
		);
		$post_lists = new WP_Query( $args );
		if ( 1 > $post_lists->post_count ) {
			return false;
		}
		$post_list = $post_lists->post;

		if ( 'apl_post_list' !== $post_list->post_type ) {
			return false;
		}

		$apl_post_list = new APL_Post_List( $post_list->post_name );

		$apl_design = new APL_Design( $apl_post_list->pl_apl_design );

		$new_post_list_slug = $post_list->post_name . '__trashed';
		$new_design_slug    = '';
		if ( ! empty( $post_list->post_name ) ) {
			// //$slug_suffix = apply_filters( 'apl_design_slug_suffix', '-design' );
			$design_slug = apply_filters( 'apl_design_trash_slug', $new_post_list_slug );
			// //$new_design_slug = $design_slug . $slug_suffix;
			$new_design_slug = $design_slug;
		}
		$apl_post_list->pl_apl_design = $new_design_slug;
		$apl_design->slug             = $new_design_slug;

		$apl_design->save_design();
	}

	/**
	 * Un-Trash for Post List
	 *
	 * Hook for untrash Post Transition with Post Lists.
	 *
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter APL_Design object referencing.
	 *
	 * @hook `untrash_post`
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/untrash_post
	 *
	 * @param int $post_id ID of the post being untrashed.
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
			return false;
		}

		$apl_post_list = new APL_Post_List( $post_list->post_name );

		$apl_design = new APL_Design( $apl_post_list->pl_apl_design_id );

		$new_post_list_slug = str_replace( '__trashed', '', $post_list->post_name );
		$new_design_slug    = '';
		if ( ! empty( $post_list->post_name ) ) {
			// //$slug_suffix = apply_filters( 'apl_design_slug_suffix', '-design' );
			$design_slug = apply_filters( 'apl_design_trash_slug', $new_post_list_slug );
			// //$new_design_slug = $design_slug . $slug_suffix;
			$new_design_slug = $design_slug;
		}
		$apl_post_list->pl_apl_design = $new_design_slug;
		$apl_design->slug             = $new_design_slug;

		$apl_design->save_design();
	}

	/**
	 * WP_Delete_Post APL Post List
	 *
	 * Host for delete post transitions with Post Lists.
	 *
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter APL_Design object referencing.
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/before_delete_post
	 *
	 * @param int $post_id The post id that is being deleted.
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
			return false;
		}

		$apl_post_list = new APL_Post_List( $post_list->post_name );
		$apl_design    = new APL_Design( $apl_post_list->pl_apl_design_id );

		$apl_design->delete_design();
	}

	/**
	 * Save General Settings
	 *
	 * Uses a lazy REST API built into WP.
	 *
	 * @uses Hook 'admin_post_{SCREEN ID}' ex. 'admin_post_apl_save_general_settings'.
	 * @link https://developer.wordpress.org/reference/hooks/admin_post_action/
	 */
	public function save_general_settings() {
		if ( ! is_admin() ) {
			wp_die();
		}
		$options = apl_options_load();

		$tmp_ignore_pt = array();
		$post_types    = get_post_types( '', 'names' );
		foreach ( $post_types as $post_type ) {
			if ( isset( $_POST[ 'apl_ignore_pt_' . $post_type ] ) ) {
				$input = filter_input( INPUT_POST, 'apl_ignore_pt_' . $post_type, FILTER_SANITIZE_STRING );

				$tmp_ignore_pt[ $post_type ] = sanitize_key( $input );
			}
		}
		$options['ignore_post_types'] = $tmp_ignore_pt;

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

		wp_redirect( 'admin.php?page=apl_settings' );
		//wp_die();
		exit();
	}

	/**
	 * Settings Page AJAX Hooks
	 *
	 * Add AJAX hooks for Settings Page.
	 *
	 * @uses 'wp_ajax_{name}'
	 */
	public function add_settings_ajax_hooks() {
		add_action( 'wp_ajax_apl_settings_export', array( $this, 'ajax_settings_export' ) );
		add_action( 'wp_ajax_apl_export', 'apl_export' );

		add_action( 'wp_ajax_apl_settings_import', array( $this, 'ajax_settings_import' ) );
		add_action( 'wp_ajax_apl_import', 'apl_import' );

		add_action( 'wp_ajax_apl_settings_restore_defaults', array( $this, 'ajax__restore_defaults' ) );
	}

	/**
	 * MCE External Plugins
	 *
	 * @since 0.4.2
	 *
	 * @see 'mce_external_plugins' filter hook
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_external_plugins
	 *
	 * @param array $plugin_array Other plugin's mce buttons.
	 * @return mixed
	 */
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array['advanced_post_list'] = APL_URL . 'admin/js/wp-editor-mce.js';

		return $plugin_array;

	}

	/**
	 * MCE Buttons
	 *
	 * @since 0.4.2
	 *
	 * @uses 'mce_buttons' filter hook
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4
	 *
	 * @param array $buttons Buttons for MCE Editor.
	 * @return mixed
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'apl_post_list' );
		array_push( $buttons, 'dropcap', 'showrecent' );

		return $buttons;
	}

	/**
	 * TinyMCE Extra Vars
	 *
	 * @since 0.4.0
	 */
	public function tinymce_extra_vars() {
		$args = array(
			'post_type'   => 'apl_post_list',
			'post_status' => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
		);

		$pl_query   = new WP_Query( $args );
		$post_lists = array();
		foreach ( $pl_query->posts as $apl_post ) {
			$post_lists[ $apl_post->post_name ] = $apl_post->post_title;
		}

		$trans = array(
			'button_title'          => __( 'APL Post List', 'advanced-post-list' ),
			'button_tooltip'        => __( 'Insert APL Shortcode', 'advanced-post-list' ),
			'window_title'          => __( 'APL Shortcode', 'advanced-post-list' ),
			'window_body_1_label'   => __( 'Post List', 'advanced-post-list' ),
			'window_body_1_tooltip' => __( 'Select the Post List you want.', 'advanced-post-list' ),

		);

		$apl_tinymce_json = wp_json_encode(
			array(
				'post_lists' => $post_lists,
				'trans'      => $trans,
			)
		);
		?>
		<script type="text/javascript">
			var apl_tinyMCE = <?php echo $apl_tinymce_json; ?>;
		</script>
		<?php
	}

	/**
	 * AJAX Settings Page Export
	 *
	 * Handles the AJAX call for exporting data.
	 *
	 * @since 0.3
	 * @since 0.4.4 Added stricter APL_Design object referencing.
	 */
	public function ajax_settings_export() {
		check_ajax_referer( 'apl_settings_export' );

		$rtn_data = array(
			'action'      => 'apl_export',
			'_ajax_nonce' => wp_create_nonce( 'apl_export' ),
		);

		$tmp_filename = 'file_export_name';
		if ( isset( $_POST['filename'] ) ) {
			$tmp_filename = filter_input( INPUT_POST, 'filename', FILTER_SANITIZE_STRING );
		}
		$rtn_data['filename'] = $tmp_filename;

		$export_data = array(
			'version'           => APL_VERSION,
			'apl_post_list_arr' => array(),
			'apl_design_arr'    => array(),
		);

		$args = array(
			'post_type'      => 'apl_post_list',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$apl_post_lists = new WP_Query( $args );

		foreach ( $apl_post_lists->posts as $post_obj ) {
			$apl_post_list  = new APL_Post_List( $post_obj->post_name );
			$apl_design     = new APL_Design( $apl_post_list->pl_apl_design_id );

			$export_data['apl_post_list_arr'][] = $apl_post_list->slug;
			$export_data['apl_design_arr'][] = $apl_design->slug;
		}

		update_option( 'apl_export_data', $export_data );

		echo json_encode( $rtn_data );

		wp_die();
	}

	/**
	 * AJAX Settings Import
	 *
	 * @since 0.4.0
	 * @since 0.4.4 Handle 0.3 and 0.4 database types separately, and stricter handling APL_Design with APL_Post_List.
	 *
	 * @uses add_settings_ajax_hooks().
	 * @uses wp_ajax_apl_settings_import.
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

		$imported_content = array();
		foreach ( $raw_content as $v1_content ) {
			$update_items = array();
			if ( isset( $v1_content->version ) ) {
				$version = $v1_content->version;
			} else {
				return new WP_Error( 'apl_admin', __( 'Version number is not present in imported file.', 'advanced-post-list' ) );
			}

			// 0.3 Database.
			if ( version_compare( '0.3.0', $version, '<' ) && version_compare( '0.4.0', $version, '>' ) ) {
				if ( isset( $v1_content->presetDbObj ) ) {
					$update_items['preset_db'] = $v1_content->presetDbObj;
				}
			}
			// 0.4+ Database.
			elseif( version_compare( '0.4.0', $version, '<' ) ) {
				if ( isset( $v1_content->apl_post_list_arr ) ) {
					$update_items['apl_post_list_arr'] = $v1_content->apl_post_list_arr;
				}
				if ( isset( $v1_content->apl_design_arr ) ) {
					$update_items['apl_design_arr'] = $v1_content->apl_design_arr;
				}
			}

			$updater = new APL_Updater( $version, $update_items, 'OBJECT' );

			$imported_content[] = array(
				'apl_post_list_arr' => $updater->apl_post_list_arr,
				'apl_design_arr'    => $updater->apl_design_arr,
			);

		}

		$overwrite_apl_post_list = array();
		$overwrite_apl_design    = array();

		$data_overwrite_post_list = array();
		$data_overwrite_design    = array();

		foreach ( $imported_content as $v1_content ) {
			// POST LISTS.
			foreach ( $v1_content['apl_post_list_arr'] as $v2_post_list ) {
				$db_post_list = new APL_Post_List( $v2_post_list->slug );
				// Check if Post List (ID) already exists.
				if ( 0 !== $db_post_list->id ) {
					$overwrite_apl_post_list[]  = $v2_post_list;
					$data_overwrite_post_list[] = $v2_post_list->slug;
					// DESIGNS.
					foreach ( $v1_content['apl_design_arr'] as $k3_design => $v3_design ) {
						if ( $v3_design->slug === $v2_post_list->pl_apl_design ) {
							$overwrite_apl_design[]  = $v3_design;
							$data_overwrite_design[] = $v3_design->slug;

							unset( $v1_content['apl_design_arr'][ $k3_design ] );
							break;
						}
					}
				} else {

					// DESIGNS.
					foreach ( $v1_content['apl_design_arr'] as $k3_design => $v3_design ) {
						if ( $v3_design->slug === $v2_post_list->pl_apl_design ) {
							// Uses slug instead of ID.
							$db_design = new APL_Design( $v3_design->slug );
							if ( 0 !== $db_design->id ) {
								// Add Variable to Database.
								//$v2_post_list->pl_apl_design_id   = $v3_design->id;
								//$v2_post_list->pl_apl_design_slug = $v3_design->slug;
								//$this->import_process_post_list( $v2_post_list );
								$overwrite_apl_post_list[]  = $v2_post_list;
								$data_overwrite_post_list[] = $v2_post_list->slug;

								$overwrite_apl_design[]  = $v3_design;
								$data_overwrite_design[] = $v3_design->slug;
							} else {
								// Add Variable to Database.
								//$db_design = $this->import_process_design( $v3_design );
								$db_design = $v3_design;
								$this->import_process_post_list_design( $v2_post_list, $db_design );
							}


							unset( $v1_content['apl_design_arr'][ $k3_design ] );
							break;
						}
					}
					// Add Variable to Database.
					//$this->import_process_post_list( $v2_post_list, $db_design );

				}
			}

			// DESIGNS.
			// Catch any remaining designs that may be left.
			foreach ( $v1_content['apl_design_arr'] as $v2_design ) {
				// Uses slug instead of ID.
				$db_design = new APL_Design( $v2_design->slug );
				if ( 0 !== $db_design->id ) {
					$overwrite_apl_design[]  = $v2_design;
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
			'action'              => 'apl_import',
			'_ajax_nonce'         => wp_create_nonce( 'apl_import' ),
			'overwrite_post_list' => $data_overwrite_post_list,
			'overwrite_design'    => $data_overwrite_design,
		);

		echo json_encode( $rtn_data );

		die();
	}

	/**
	 * AJAX - Restore Defaults
	 *
	 * @since 0.5
	 *
	 * @return void
	 */
	public function ajax__restore_defaults() {
		check_ajax_referer( 'apl_settings_restore_defaults' );

		$this->restore_default_presets();

		wp_send_json_success( 'Success' );
	}

	/**
	 * Restore Default Presets
	 *
	 * @since 0.5
	 */
	public function restore_default_presets() {
		include_once APL_DIR . 'admin/includes/default-presets.php';

		$apl_post_list_default = apl_restore_post_list_default();

		$apl_design_excerpt_divided      = apl_restore_design_excerpt_divided();
		$apl_design_page_content_divided = apl_restore_design_page_content_divided();
		$apl_design_footer_list          = apl_restore_design_footer_list();

		$apl_post_list_default->title         = 'Excerpt Divided';
		$apl_post_list_default->slug          = 'excerpt-divided';
		$apl_post_list_default->pl_apl_design = 'excerpt-divided';
		$this->import_process_post_list_design( $apl_post_list_default, $apl_design_excerpt_divided );

		$apl_post_list_default->title         = 'Page Content Divided';
		$apl_post_list_default->slug          = 'page-content-divided';
		$apl_post_list_default->pl_apl_design = 'page-content-divided';
		$this->import_process_post_list_design( $apl_post_list_default, $apl_design_page_content_divided );

		$apl_post_list_default->title         = 'Footer List';
		$apl_post_list_default->slug          = 'footer-list';
		$apl_post_list_default->pl_apl_design = 'footer-list';
		$this->import_process_post_list_design( $apl_post_list_default, $apl_design_footer_list );
	}

	/**
	 * Process Import for Post Lists
	 *
	 * @ignore
	 * @since 0.4.0
	 * @since 0.4.4 Added APL_Design ID, but disabled for future use.
	 *
	 * @param APL_Post_List $apl_post_list Current post list to import.
	 */
	private function import_process_post_list( $apl_post_list ) {
		$tmp_apl_post_list = new APL_Post_List( $apl_post_list->slug );

		$tmp_apl_post_list->title               = $apl_post_list->title               ?: $tmp_apl_post_list->title;
		$tmp_apl_post_list->post_type           = $apl_post_list->post_type           ? json_decode( json_encode( $apl_post_list->post_type ), true ) : $tmp_apl_post_list->post_type ;
		$tmp_apl_post_list->tax_query           = $apl_post_list->tax_query           ? json_decode( json_encode( $apl_post_list->tax_query ), true ) : $tmp_apl_post_list->tax_query;
		$tmp_apl_post_list->post_parent__in     = $apl_post_list->post_parent__in     ? json_decode( json_encode( $apl_post_list->post_parent__in ), true ) : $tmp_apl_post_list->post_parent__in;
		$tmp_apl_post_list->post_parent_dynamic = $apl_post_list->post_parent_dynamic ? json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true ) : $tmp_apl_post_list->post_parent_dynamic;
		$tmp_apl_post_list->posts_per_page      = $apl_post_list->posts_per_page      ?: $tmp_apl_post_list->posts_per_page;
		$tmp_apl_post_list->offset              = $apl_post_list->offset              ?: $tmp_apl_post_list->offset;
		$tmp_apl_post_list->order_by            = $apl_post_list->order_by            ?: $tmp_apl_post_list->order_by;
		$tmp_apl_post_list->order               = $apl_post_list->order               ?: $tmp_apl_post_list->order;
		$tmp_apl_post_list->post_status         = $apl_post_list->post_status         ? json_decode( json_encode( $apl_post_list->post_status ), true ) : $tmp_apl_post_list->post_status;
		$tmp_apl_post_list->perm                = $apl_post_list->perm                ?: $tmp_apl_post_list->perm;
		$tmp_apl_post_list->author__bool        = $apl_post_list->author__bool        ?: $tmp_apl_post_list->author__bool;
		$tmp_apl_post_list->author__in          = $apl_post_list->author__in          ?: $tmp_apl_post_list->author__in;
		$tmp_apl_post_list->ignore_sticky_posts = $apl_post_list->ignore_sticky_posts ?: $tmp_apl_post_list->ignore_sticky_posts;
		$tmp_apl_post_list->post__not_in        = $apl_post_list->post__not_in        ?: $tmp_apl_post_list->post__not_in;
		$tmp_apl_post_list->pl_exclude_current  = $apl_post_list->pl_exclude_current  ?: $tmp_apl_post_list->pl_exclude_current;
		$tmp_apl_post_list->pl_exclude_dupes    = $apl_post_list->pl_exclude_dupes    ?: $tmp_apl_post_list->pl_exclude_dupes;
		$tmp_apl_post_list->pl_apl_design       = $apl_post_list->pl_apl_design       ?: $tmp_apl_post_list->pl_apl_design;
		$tmp_apl_post_list->pl_apl_design_id    = $apl_post_list->pl_apl_design_id    ?: $tmp_apl_post_list->pl_apl_design_id;
		$tmp_apl_post_list->pl_apl_design_slug  = $apl_post_list->pl_apl_design_slug  ?: $tmp_apl_post_list->pl_apl_design_slug;

		$tmp_apl_post_list->save_post_list();
	}

	/**
	 * Process Import for Post List with Design
	 *
	 * @ignore
	 * @since 0.4.0
	 *
	 * @param APL_Post_List $apl_post_list
	 * @param APL_Design    $apl_design
	 */
	private function import_process_post_list_design( $apl_post_list, $apl_design ) {
		$tmp_apl_post_list = new APL_Post_List( $apl_post_list->slug );

		$tmp_apl_post_list->title               = $apl_post_list->title               ?: $tmp_apl_post_list->title;
		$tmp_apl_post_list->post_type           = $apl_post_list->post_type           ? json_decode( json_encode( $apl_post_list->post_type ), true ) : $tmp_apl_post_list->post_type ;
		$tmp_apl_post_list->tax_query           = $apl_post_list->tax_query           ? json_decode( json_encode( $apl_post_list->tax_query ), true ) : $tmp_apl_post_list->tax_query;
		$tmp_apl_post_list->post_parent__in     = $apl_post_list->post_parent__in     ? json_decode( json_encode( $apl_post_list->post_parent__in ), true ) : $tmp_apl_post_list->post_parent__in;
		$tmp_apl_post_list->post_parent_dynamic = $apl_post_list->post_parent_dynamic ? json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true ) : $tmp_apl_post_list->post_parent_dynamic;
		$tmp_apl_post_list->posts_per_page      = $apl_post_list->posts_per_page      ?: $tmp_apl_post_list->posts_per_page;
		$tmp_apl_post_list->offset              = $apl_post_list->offset              ?: $tmp_apl_post_list->offset;
		$tmp_apl_post_list->order_by            = $apl_post_list->order_by            ?: $tmp_apl_post_list->order_by;
		$tmp_apl_post_list->order               = $apl_post_list->order               ?: $tmp_apl_post_list->order;
		$tmp_apl_post_list->post_status         = $apl_post_list->post_status         ? json_decode( json_encode( $apl_post_list->post_status ), true ) : $tmp_apl_post_list->post_status;
		$tmp_apl_post_list->perm                = $apl_post_list->perm                ?: $tmp_apl_post_list->perm;
		$tmp_apl_post_list->author__bool        = $apl_post_list->author__bool        ?: $tmp_apl_post_list->author__bool;
		$tmp_apl_post_list->author__in          = $apl_post_list->author__in          ?: $tmp_apl_post_list->author__in;
		$tmp_apl_post_list->ignore_sticky_posts = $apl_post_list->ignore_sticky_posts ?: $tmp_apl_post_list->ignore_sticky_posts;
		$tmp_apl_post_list->post__not_in        = $apl_post_list->post__not_in        ?: $tmp_apl_post_list->post__not_in;
		$tmp_apl_post_list->pl_exclude_current  = $apl_post_list->pl_exclude_current  ?: $tmp_apl_post_list->pl_exclude_current;
		$tmp_apl_post_list->pl_exclude_dupes    = $apl_post_list->pl_exclude_dupes    ?: $tmp_apl_post_list->pl_exclude_dupes;

		$apl_design = $this->import_process_design( $apl_design );
		$tmp_apl_post_list->pl_apl_design      = $apl_post_list->pl_apl_design ?: $tmp_apl_post_list->pl_apl_design;
		$tmp_apl_post_list->pl_apl_design_id   = $apl_design->id               ?: $tmp_apl_post_list->pl_apl_design_id;
		$tmp_apl_post_list->pl_apl_design_slug = $apl_design->slug             ?: $tmp_apl_post_list->pl_apl_design_slug;

		$tmp_apl_post_list->save_post_list();
	}

	/**
	 * Process Import for Designs
	 *
	 * @ignore
	 * @since 0.4.0
	 *
	 * @param APL_Design $apl_design
	 * @return APL_Design
	 */
	private function import_process_design( $apl_design ) {
		$new_apl_design = new APL_Design( $apl_design->slug );

		$new_apl_design->title   = $apl_design->title   ?: $new_apl_design->title;
		$new_apl_design->before  = $apl_design->before  ?: $new_apl_design->before;
		$new_apl_design->content = $apl_design->content ?: $new_apl_design->content;
		$new_apl_design->after   = $apl_design->after   ?: $new_apl_design->after;
		$new_apl_design->empty   = $apl_design->empty   ?: $new_apl_design->empty;

		$new_apl_design->save_design();

		return $new_apl_design;
	}

	/**
	 * Process Post List Form
	 *
	 * Gathers data from the Post List edit page.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter APL_Design object referencing.
	 * @access private
	 *
	 * @see $this->save_post_list()
	 *
	 * @param int     $post_id Old post ID.
	 * @param WP_Post $post    Current Post object.
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

					if ( isset( $_POST[ 'apl_multiselect_taxonomies-' . $k_pt_slug ] ) ) {
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
				}// End if().
			}// End if().
		}// End foreach().
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

		// offset.
		$tmp_offset = 5;
		if ( isset( $_POST['apl_offset'] ) ) {
			$p_offset = filter_input( INPUT_POST, 'apl_offset', FILTER_SANITIZE_NUMBER_INT );
			$tmp_offset = intval( $p_offset );
		}
		$apl_post_list->offset = $tmp_offset;

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
		if ( isset( $_POST['apl_post_status_1'] ) ) {
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
		if ( isset( $_POST['apl_author__bool'] ) ) {
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
		if ( ! empty( $post->post_name ) ) {
			$new_design_slug = $post->post_name;
		}

		$tmp_apl_design = $this->post_list_process_apl_design( $apl_post_list->pl_apl_design, $new_design_slug );

		$apl_post_list->pl_apl_design      = $tmp_apl_design->slug;
		$apl_post_list->pl_apl_design_id   = $tmp_apl_design->id;
		$apl_post_list->pl_apl_design_slug = $tmp_apl_design->slug;
	}

	/**
	 * Process Tax Query
	 *
	 * Processes the taxonomies and returns 'multiple arrays' simular to $args['tax_query'].
	 *
	 * @ignore
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
				$tmp_terms_slug = array();
				foreach ( $terms as $v2_term_obj ) {
					// Check 'any' term, and if set, skip other terms. break;
					if ( isset( $_POST[ 'apl_term-' . $post_type . '-' . $v1_taxonomy . '-any' ] ) ) {
						// No reason to have dynamic true with 'any'; fallback method.
						$tmp_terms[] = 0;
						$tmp_terms_dynamic = false;
						break;
					} elseif ( isset( $_POST[ 'apl_term-' . $post_type . '-' . $v1_taxonomy . '-' . $v2_term_obj->term_id ] ) ) {
						$tmp_terms[] = $v2_term_obj->term_id;
						$tmp_terms_slug[ $v2_term_obj->term_id ] = $v2_term_obj->slug;
					}
				}

				$tmp_tax_query[] = array(
					'taxonomy'          => $v1_taxonomy,
					'field'             => 'id', // Or 'slug'.
					'terms'             => $tmp_terms,
					'include_children'  => false,
					'operator'          => $tmp_terms_req, // 'IN' | 'AND' | --'NOT IN'--

					//'apl_terms_req'     = $tmp_terms_req;
					'apl_terms_slug'    => $tmp_terms_slug,
					'apl_terms_dynamic' => $tmp_terms_dynamic,
				);
			}// End if().
		} // End foreach().
		$tmp_tax_query['relation'] = $tmp_req_tax;

		return $tmp_tax_query;
	}

	/**
	 * Process Design Meta Box
	 *
	 * Processes the incoming data to APL Designs.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter APL_Design object referencing; Changed to return APL_Design.
	 * @access private
	 *
	 * @param string $apl_design_slug Current active slug.
	 * @param string $new_design_slug New slug relative to $this->pl_apl_design.
	 * @return APL_Design Slug used in $this->pl_apl_design.
	 */
	private function post_list_process_apl_design( $apl_design_slug, $new_design_slug ) {
		$apl_design = new APL_Design( $apl_design_slug );

		// SLUG / KEY.
		//if ( $new_design_slug !== $apl_design_slug && '-design' !== $new_design_slug  ) {
		if ( $new_design_slug !== $apl_design_slug ) {
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

		return $apl_design;
	}

	/**
	 * Process APL Design Class
	 *
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter APL_Design object referencing.
	 * @access private
	 *
	 * @param int      $post_id  Contains the ID of the post type.
	 * @param WP_Post  $post     New Post Data content to save/update.
	 */
	private function design_process( $post_id, $post ) {
		$apl_design = new APL_Design( $post_id );

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
	}

	/*
	 * *************************************************************************
	 * **** PRIVATE FUNCTIONS **************************************************
	 * *************************************************************************
	 */

	/**
	 * Get Post Type & Taxonomies
	 *
	 * Gets and returns an array of Post_Types => Taxonomies.
	 *
	 * @deprecated 0.4.4.1 Use apl_get_post_tax()
	 *
	 * @ignore
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
	 * Get Taxonomies & Terms
	 *
	 * Gets and returns an array of Taxonomies => Terms.
	 *
	 * @deprecated 0.4.4.1 Use apl_get_tax_terms().
	 *
	 * @see get_terms()
	 * @link https://developer.wordpress.org/reference/functions/get_terms/
	 *
	 * @ignore
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
	 * Get Post Types to Display
	 *
	 * Displays a *valid* list of post types that also aren't on the global ignore list.
	 *
	 * @ignore
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
