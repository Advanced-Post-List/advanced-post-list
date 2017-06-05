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
	private static $instance = null;

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
		if ( null === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
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
		
		if ( ! is_admin() ) {
			return new WP_Error( 'apl_admin_construct', esc_html__( 'You do not have admin capabilities.', 'advanced-post-list' ) );
		}
		$this->_requires();

		/*
		// Early Hook
		add_action( 'plugins_loaded', array( $this, 'hook_action_plugins_loaded' ) );

		// Multilingual Support
		add_action( 'load_textdomain', array( $this, 'hook_action_load_textdomain' ) );

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
}
