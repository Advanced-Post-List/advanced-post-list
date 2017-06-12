<?php

/**
 * APL Admin Class
 *
 * Admin core object to Advanced Post List
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.1.0
 */

/**
 * APL Admin
 *
 * Admin core class.
 *
 * @since 0.1.0
 * @since 0.2.0
 * @since 0.3.0
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
	 * Summary.
	 *
	 * Description.
	 *
	 * @since 0.4.0
	 * @access private
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
	 * Get Singleton Instance.
	 *
	 * Singleton Get Instance.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return void
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'advanced-post-list' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'advanced-post-list' ), '1.0' );
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

		// Menu & Scripts
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Screen Options
		add_action( 'admin_head', array( $this, 'disable_screen_boxes' ) );
		add_action( 'load-edit.php', array( $this, 'post_list_screen_all' ) );
		add_action( 'load-post-new.php', array( $this, 'post_list_screen_add_new' ) );

		// Editor Meta Boxes
		add_action( 'add_meta_boxes', array( $this, 'post_list_meta_boxes' ) );

		/*
		// Early Hook
		add_action( 'plugins_loaded', array( $this, 'hook_action_plugins_loaded' ) );

		// Plugin Init Hook
		add_action( 'init', array( $this, 'hook_action_init' ) );

		// After WordPress is fully loaded
		add_action( 'wp_loaded', array( $this, 'hook_action_wp_loaded' ) );

		// WordPress Footer
		add_action( 'wp_footer', array( $this, 'hook_action_wp_footer' ) );
		*/

	}

	private function _requires() {
		//require_once( APL_DIR . 'includes/example.php' );
	}
	
	public function admin_menu() {
		// Add APL Dashboard
		// Add APL Settings API
		// Add Help API
		
		// Enqueue Scripts & Styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}
	
	public function admin_enqueue( $hook ) {
		$screen = get_current_screen();
		
		/*
		 * ************** REMOVE SCRIPTS & STYLES *********************
		 */
		wp_deregister_script( 'apl-admin-js' );
		wp_deregister_script( 'apl-admin-ui-js' );
		wp_deregister_script( 'apl-admin-ui-multiselect-js' );
		wp_deregister_script( 'apl-admin-ui-multiselect-filter-js' );

		wp_deregister_style( 'apl-admin-css' );
		wp_deregister_style( 'apl-admin-ui-multiselect-css' );
		wp_deregister_style( 'apl-admin-ui-multiselect-filter-css' );

		if ( 'apl_post_list' !== $screen->post_type ) {
			return;
		} else {

			/*
			 * ************** AJAX ACTION HOOKS ***************************
			 */
			add_action( 'wp_ajax_apl_load_preset', array( $this, 'hook_ajax_load_preset' ) );

			/*
			 * ************** REGISTER SCRIPTS ****************************
			 */

			// Step 4.
			wp_register_script(
				'apl-admin-js',
				APL_URL . 'admin/js/admin.js',
				array(
					'jquery',
					//'jquery-ui-core',
					//'jquery-ui-widget',
					//'jquery-ui-dialog',
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
					//'jquery-ui-checkboxradio',
					//'jquery-ui-accordion',
					
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
			
			wp_enqueue_script( 'apl-admin-js' );
			wp_enqueue_script( 'apl-admin-ui-js' );
			wp_enqueue_script( 'apl-admin-ui-multiselect-js' );
			wp_enqueue_script( 'apl-admin-ui-multiselect-filter-js' );
			
			/*
			 * ************** REGISTER STYLES *****************************
			 */

			// Step 5.
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

			// POST LISTS DATA.
			$data_post_lists = array();
			$args = array(
				//'post_status'      => 'publish',
				'post_type'        => 'apl_post_list',
			);
			$post_post_list = get_posts( $args );
			foreach ( $post_post_list as $key => $value ) {
				$data_post_lists[ $value->post_name ] = new APL_Post_List( $value->post_name );
			}

			// DESIGNS DATA.
			$data_designs = array();
			$args = array(
				'post_status'      => 'publish',
				'post_type'        => 'apl_design',
			);
			$post_designs = get_posts( $args );
			foreach ( $post_designs as $key => $value ) {
				$data_designs[ $value->post_name ] = new APL_Design( $value->post_name );
			}

			// POST => TAXONOMIES.
			$data_post_tax = $this->get_post_tax();

			// TAXONOMIES => TERMS
			$data_tax_terms = $this->get_tax_terms();

			$data_ui_trans = array(
				'tax_noneSelectedText' => esc_html__( 'Select Taxonomy', 'advanced-post-list' ),
				'tax_selectedText'     => esc_html__( '# of # taxonomies selected', 'advanced-post-list' ),
			);

			$admin_localize = array();
			$admin_ui_localize = array(
				//'post_type_objs'
				//'taxonomy_objs'
				//'term_objs'
				'post_tax' => $data_post_tax,
				'tax_terms' => $data_tax_terms,
				'trans' => $data_ui_trans,
			);

			wp_localize_script( 'apl-admin-js', 'apl_admin_local', $admin_localize );
			wp_localize_script( 'apl-admin-ui-js', 'apl_admin_ui_local', $admin_ui_localize );
		}
		
		
	}

	public function disable_screen_boxes() {
		echo '<style>label[for=apl-post-list-filter-hide] { display: none; }</style>';
		echo '<style>#apl-post-list-filter { display: block; }</style>';
	}

	// Screen Options tab at top.
	public function post_list_screen_all() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if( ! is_object( $screen ) || $screen->id !== 'edit-apl_post_list' )
			return;
		$options = $screen->get_options();
	}

	public function post_list_screen_add_new() {
		$screen = get_current_screen();
		// Get out of here if we are not on our settings page.
		if( ! is_object( $screen ) || $screen->id !== 'apl_post_list' ) {
			return;
		}
		$options = $screen->get_options();

		/*
		$args = array(
			'label' => __('Members per page', 'pippin'),
			'default' => 10,
			'option' => 'pippin_per_page'
		);
		add_screen_option( 'per_page', $args );
		*/
	}
	
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
	public function post_list_meta_box_filter( $post, $metabox ) {
		// ob_start.

		$apl_post_tax           = $this->get_post_tax();
		$apl_tax_terms          = $this->get_tax_terms();
		$apl_display_post_types = $this->get_display_post_types();

		include( APL_DIR . 'admin/meta-box-filter.php' );
		
	}
	public function post_list_meta_box_display( $post, $metabox ) {
		echo '<p>Hello2</p>';
		var_dump( $metabox );
	}
	
	/*
	 * *************************************************************************
	 * **** PRIVATE FUNCTIONS **************************************************
	 * *************************************************************************
	 */
	private function get_post_tax() {
		$rtn_post_tax = array();

		// Get Post Type names.
		$post_type_obj = get_post_types( '', 'objects' );

		// Remove ignored Post Types.
		foreach ( $this->_ignore_post_types as $value )
		{
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

	private function get_tax_terms() {
		$rtn_tax_terms = array();

		// Get Taxonomy Names
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

		// Return Tax_Terms
		return $rtn_tax_terms;
	}

	// Get post types that aren't ignored ( without 'any' as used in post_tax ).
	private function get_display_post_types() {
		$rtn_post_types = array();

		$post_type_objs = get_post_types( '', 'objects' );
		// Remove ignored Post Types.
		foreach ( $this->_ignore_post_types as $value )
		{
			unset( $post_type_objs[ $value ] );
		}

		foreach ( $post_type_objs as $key => $value ) {
			$rtn_post_types[ $key ] = $value->labels->singular_name;
		}

		return $rtn_post_types;
	}
	
}
