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
	 * Summary.
	 *
	 * @since 0.4.0
	 * @access private
	 * @var array( string )
	 */
	private $_ignore_post_types = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'apl_post_list',
		'apl_design',
	);

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
			return new WP_Error( 'apl_admin', esc_html__( 'You do not have admin capabilities.', 'advanced-post-list' ) );
		}
		$this->_requires();

		// Menu & Scripts.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Screen Options.
		add_action( 'admin_head', array( $this, 'disable_screen_boxes' ) );
		add_action( 'load-edit.php', array( $this, 'post_list_screen_all' ) );
		add_action( 'load-post-new.php', array( $this, 'post_list_screen_add_new' ) );

		// Editor Meta Boxes.
		add_action( 'add_meta_boxes', array( $this, 'post_list_meta_boxes' ) );

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
	}

	/**
	 * APL Admin Menu.
	 *
	 * Adds the Admin Menu and Scripts for APL.
	 *
	 * @since 0.4.0
	 *
	 * @see wp-admin/admin-header.php
	 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 *
	 * @return void
	 */
	public function admin_menu() {
		// TODO - Add APL Dashboard.
		// TODO - Add APL Settings API.
		// TODO - Add Help API.

		// Enqueue Scripts & Styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
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

		// If we are not viewing APL Post List area, then return.
		if ( 'apl_post_list' !== $screen->post_type ) {
			return;
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
	 * Post List Meta Boxes.
	 *
	 * Hook 'add_meta_boxes', adds meta boxes used in post lists.
	 *
	 * @since 0.4.0
	 *
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
			'advanced',
			'sorted'
		);
		add_meta_box(
			'apl-post-list-display',
			__( 'Display Settings', 'advanced-post-list' ),
			array( $this, 'post_list_meta_box_display' ),
			'apl_post_list',
			'advanced',
			'core'
		);
	}

	/**
	 * Post List Filter Meta box Template.
	 *
	 * Hook '$this->post_list_meta_boxes()', renders the Filter Meta Box Template.
	 *
	 * @since 0.4.0
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
	 *
	 * @param WP_Post $post Current WP_Post object.
	 * @param array   $metabox With Meta Box id, title, callback, and args elements.
	 * @return void
	 */
	public function post_list_meta_box_filter( $post, $metabox ) {
		$apl_post_tax           = $this->get_post_tax();
		$apl_tax_terms          = $this->get_tax_terms();
		$apl_display_post_types = $this->get_display_post_types();

		include( APL_DIR . 'admin/meta-box-filter.php' );
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
		include( APL_DIR . 'admin/meta-box-design.php' );
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

		// Get Post Type names.
		$post_type_obj = get_post_types( '', 'objects' );

		// Remove ignored Post Types.
		foreach ( $this->_ignore_post_types as $value ) {
			unset( $post_type_obj[ $value ] );
		}

		// Add to rtn {post_type} => {array( taxonomies )}.
		$rtn_post_tax['any']['name'] = __( 'Any / All', 'advanced-post-list' );
		$taxonomy_names = get_taxonomies( '', 'names' );
		foreach ( $taxonomy_names as $name ) {
			$rtn_post_tax['any']['tax_arr'][] = $name;
		}

		foreach ( $post_type_obj as $key => $value ) {
			$rtn_post_tax[ $key ]['name'] = $value->labels->singular_name;
			$rtn_post_tax[ $key ]['tax_arr'] = get_object_taxonomies( $key, 'names' );
		}

		// Return Post_Tax.
		return $rtn_post_tax;
	}

	/**
	 * Get Taxonomies & Terms.
	 *
	 * Gets and returns an array of Taxonomies => Terms.
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
				$rtn_tax_terms[ $taxonomy ][] = $term->slug;
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
		foreach ( $this->_ignore_post_types as $value ) {
			unset( $post_type_objs[ $value ] );
		}

		foreach ( $post_type_objs as $key => $value ) {
			$rtn_post_types[ $key ] = $value->labels->singular_name;
		}

		return $rtn_post_types;
	}

}
