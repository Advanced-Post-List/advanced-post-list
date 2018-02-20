<?php
/**
 * APL Notice API: APL Notice Class
 *
 * Handles adding, updating, and removing notices. Then handles activating or
 * deactivating those notices site-wide or user based.
 *
 * @link https://wordpress.org/plugins/advanced-post-list/
 *
 * @package All-in-One-SEO-Pack
 * @since 0.4.2
 */

if ( ! class_exists( 'APL_Notices' ) ) {
	/**
	 * APL Notice.
	 *
	 * Admin notices for APL.
	 *
	 * @todo Possibly add abstract class. Especially if there are 3+ ( Notices, Footer, Alerts, Messages, Logs, etc. ).
	 *
	 * @since 0.4.2
	 */
	class APL_Notices {
		/**
		 * Collection of notices to display.
		 *
		 * @since 0.4.2
		 * @access public
		 *
		 * @todo add callback functionality.
		 *
		 * @var array $notices {
		 *     @type array $slug {
		 *         @type string $slug        Required. Notice unique ID.
		 *         @type int    $delay_time  Amount of time to begin showing message.
		 *         @type string $message     Content message to display in the container.
		 *         @type array  $action_options {
		 *         Show options for users to click on. Default: See self::action_options_defaults().
		 *             @type array {
		 *                 @type int     $time    Optional. The amount of time to delay. Zero immediately displays Default: 0.
		 *                 @type string  $text    Optional. Button/Link HTML text to display. Default: ''.
		 *                 @type string  $class   Optional. Class names to add to the link/button for styling. Default: ''.
		 *                 @type string  $link    Optional. The elements href source/link. Default: '#'.
		 *                 @type boolean $dismiss Optional. Variable for AJAX to dismiss showing a notice.
		 *             }
		 *         }
		 *         @type string $class       The class notice used by WP, or a custom CSS class.
		 *                                   Ex. notice-error, notice-warning, notice-success, notice-info.
		 *         @type string $target      Shows based on site-wide or user notice data.
		 *         @type array  $screens     Which screens to exclusively display the notice on. Default: array().
		 *                                   array()          = all,
		 *                                   array('apl') = $this->apl_screens,
		 *                                   array('CUSTOM')  = specific screen(s).
		 *         @type int    $time_start  The time the notice was added to the object.
		 *     }
		 * }
		 */
		public $notices = array();

		/**
		 * List of notice slugs that are currently active.
		 *
		 * @since 0.4.2
		 * @access public
		 *
		 * @var array $active_notices {
		 *     @type string/int $slug => $display_time Contains the current active notices
		 *                                             that are scheduled to be displayed.
		 * }
		 */
		public $active_notices = array();

		/**
		 * The default dismiss time. An anti-nag setting. 1800 seconds = 30 minutes.
		 *
		 * @var int $default_dismiss_delay
		 */
		private $default_dismiss_delay = 1800;

		/**
		 * List of Screens used in APL.
		 *
		 * @since 0.4.2
		 *
		 * @var array $apl_screens {
		 *     @type string Screen ID.
		 * }
		 */
		private $apl_screens = array(
			'apl_post_list',
			'edit-apl_post_list',
			'apl_design',
			'edit-apl_design',
			'adv-post-list_page_apl_settings',
		);

		/**
		 * __constructor.
		 *
		 * @since 0.4.2
		 */
		public function __construct() {
			$this->_requires();
			if ( is_admin() ) {
				$this->obj_load_options();

				add_action( 'admin_init', array( $this, 'init' ) );
				add_action( 'current_screen', array( $this, 'admin_screen' ) );
			}
		}

		/**
		 * _Requires
		 *
		 * Additional files required.
		 *
		 * @since 0.4.2
		 */
		private function _requires() {
			require_once APL_DIR . 'admin/functions-notice.php';
		}

		/**
		 * Early operations required by the plugin.
		 *
		 * AJAX requires being added early before screens have been loaded.
		 *
		 * @since 0.4.2
		 */
		public function init() {
			add_action( 'wp_ajax_apl_notice', array( $this, 'ajax_notice_action' ) );
		}

		/**
		 * Setup/Init Admin Screen
		 *
		 * Adds the initial actions to WP based on the Admin Screen being loaded.
		 * The APL and Other Screens have separate methods that are used, and
		 * additional screens can be made exclusive/unique.
		 *
		 * @since 0.4.2
		 *
		 * @param WP_Screen $current_screen The current screen object being loaded.
		 */
		public function admin_screen( $current_screen ) {
			$this->deregister_scripts();
			if ( in_array( $current_screen->id, $this->apl_screens, true ) && isset( $current_screen->id ) ) {
				// APL Notice Content.
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'all_admin_notices', array( $this, 'display_notice_apl' ) );
			} elseif ( isset( $current_screen->id ) ) {
				// Default WP Notice.
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'all_admin_notices', array( $this, 'display_notice_default' ) );
			}
		}

		/**
		 * Load APL_Notice Options
		 *
		 * Gets the options for APL_Notice to set its variables to.
		 *
		 * @since 0.4.2
		 * @access private
		 *
		 * @see self::notices
		 * @see self::active_notices
		 */
		private function obj_load_options() {
			$notices_options = $this->obj_get_options();

			$this->notices        = $notices_options['notices'];
			$this->active_notices = $notices_options['active_notices'];
		}

		/**
		 * Get APL_Notice Options
		 *
		 * @since 0.4.2
		 * @access private
		 *
		 * @return array
		 */
		private function obj_get_options() {
			$defaults = array(
				'notices'        => array(),
				'active_notices' => array(),
			);

			$notices_options = get_option( 'apl_notices' );
			if ( false === $notices_options ) {
				return $defaults;
			}

			return wp_parse_args( $notices_options, $defaults );
		}

		/**
		 * Update Notice Options
		 *
		 * @since 0.4.2
		 * @access private
		 *
		 * @return boolean True if successful, using update_option() return value.
		 */
		private function obj_update_options() {
			$notices_options     = array(
				'notices'        => $this->notices,
				'active_notices' => $this->active_notices,
			);
			$old_notices_options = $this->obj_get_options();
			$notices_options     = wp_parse_args( $notices_options, $old_notices_options );

			return update_option( 'apl_notices', $notices_options );
		}

		/**
		 * Notice Default Values
		 *
		 * Returns the default value for a variable to be used in self::notices[].
		 *
		 * @since 0.4.2
		 *
		 * @see self::notices Array variable that stores the collection of notices.
		 *
		 * @return array Notice variable in self::notices.
		 */
		public function notice_defaults() {
			return array(
				'slug'           => '',
				'delay_time'     => 0,
				'message'        => '',
				'action_options' => array(),
				'class'          => 'notice-info',
				'target'         => 'site',
				'screens'        => array(),
				'time_start'     => time(),
			);
		}

		/**
		 * Delay Options Default Values
		 *
		 * Returns the default value for action_options in self::notices[$slug]['action_options'].
		 *
		 * @since 0.4.2
		 *
		 * @return array Delay_Options variable in self::notices[$slug]['action_options'].
		 */
		public function action_options_defaults() {
			return array(
				'time'    => 0,
				'text'    => __( 'Dismiss', 'advanced-post-list' ),
				'link'    => '#',
				'dismiss' => true,
				'class'   => '',
			);
		}

		/**
		 * Set Notice Delay Options
		 *
		 * Sets the Delay Options in a Notice.
		 *
		 * @since 0.4.2
		 * @access private
		 *
		 * @see self::insert_notice()
		 * @see self::update_notice()
		 *
		 * @param array $action_options New delay options to be added/updated.
		 * @return array Delay Options with new values added to old.
		 */
		private function set_action_options( $action_options ) {
			$rtn_action_options = array();
			if ( empty( $action_options ) && ! is_array( $action_options ) ) {
				$rtn_action_options[] = $this->action_options_defaults();
				return $rtn_action_options;
			}

			foreach ( $action_options as $action_option ) {
				$tmp_delay_o = $this->action_options_defaults();

				// Button Delay Time.
				$tmp_delay_o['time'] = $this->default_dismiss_delay;
				if ( isset( $action_option['time'] ) ) {
					$tmp_delay_o['time'] = $action_option['time'];
				}

				// Button Text.
				if ( isset( $action_option['text'] ) && ! empty( $action_option['text'] ) ) {
					$tmp_delay_o['text'] = $action_option['text'];
				}

				// Link.
				if ( isset( $action_option['link'] ) && ! empty( $action_option['link'] ) ) {
					$tmp_delay_o['link'] = $action_option['link'];
				}

				// Dismiss.
				if ( isset( $action_option['dismiss'] ) ) {
					$tmp_delay_o['dismiss'] = $action_option['dismiss'];
				}

				// Class.
				if ( isset( $action_option['class'] ) && ! empty( $action_option['class'] ) ) {
					$tmp_delay_o['class'] = $action_option['class'];
				}

				$rtn_action_options[] = $tmp_delay_o;
			}

			return $rtn_action_options;
		}

		/**
		 * Insert Notice
		 *
		 * Initial insert for a Notice and Activates it. Used strictly for adding notices
		 * when no updating or modifications is intended.
		 *
		 * @since 0.4.2
		 *
		 * @uses self::activate_notice() Used to initialize a notice.
		 *
		 * @param array $notice See self::notices for more info.
		 * @return boolean True on success.
		 */
		public function insert_notice( $notice = array() ) {
			if ( empty( $notice['slug'] ) ) {
				return false;
			} elseif ( isset( $this->notices[ $notice['slug'] ] ) ) {
				return false;
			}

			$notice_default = $this->notice_defaults();
			$new_notice     = wp_parse_args( $notice, $notice_default );

			$new_notice['action_options'] = $this->set_action_options( $new_notice['action_options'] );

			$this->notices[ $notice['slug'] ] = $new_notice;
			$this->obj_update_options();
			$this->activate_notice( $notice['slug'] );

			return true;
		}

		/**
		 * Update Notice
		 *
		 * Updates an existing Notice without resetting it. Used when modifying
		 * any existing notices without disturbing its set environment/timeline.
		 *
		 * @since 0.4.2
		 *
		 * @param array $notice See self::notices for more info.
		 * @return boolean True on success.
		 */
		public function update_notice( $notice = array() ) {
			if ( empty( $notice['slug'] ) ) {
				return false;
			} elseif ( ! isset( $this->notices[ $notice['slug'] ] ) ) {
				return false;
			}

			$notice_default = $this->notice_defaults();
			$new_notice     = wp_parse_args( $notice, $notice_default );

			$new_notice['action_options'] = $this->set_action_options( $new_notice['action_options'] );

			$this->notices[ $notice['slug'] ] = $new_notice;
			$this->obj_update_options();
			// DO NOT use activate. This is intended to update pre-existing data.
			// //$this->activate_notice( $slug );

			return true;
		}

		/**
		 * Used strictly for any notices that are deprecated/obsolete. To stop notices,
		 * use notice_deactivate().
		 *
		 * @since 0.4.2
		 *
		 * @param string $slug Unique notice slug.
		 * @return boolean True if successfully removed.
		 */
		public function remove_notice( $slug ) {
			if ( isset( $this->notices[ $slug ] ) ) {
				unset( $this->notices[ $slug ] );
				$this->obj_update_options();
				return true;
			}

			return false;
		}

		/**
		 * Activate Notice
		 *
		 * Activates a notice, or Re-activates with a new display time. Used after
		 * updating a notice that requires a hard reset.
		 *
		 * @since 0.4.2
		 *
		 * @param string $slug Notice slug.
		 */
		public function activate_notice( $slug ) {
			$display_time = time() + $this->notices[ $slug ]['delay_time'];
			$display_time--;

			if ( 'user' === $this->notices[ $slug ]['target'] ) {
				$current_user_id = get_current_user_id();
				update_user_meta( $current_user_id, 'apl_notice_display_time_' . $slug, $display_time );
			}

			$this->active_notices[ $slug ] = $display_time;
			$this->obj_update_options();
		}

		/**
		 * Deactivate Notice
		 *
		 * Deactivates a notice set as active and completely removes it from the
		 * list of active notices. Used to prevent conflicting notices that may be
		 * active at any given point in time.
		 *
		 * @since 0.4.2
		 *
		 * @param string $slug Notice slug.
		 * @return boolean
		 */
		public function deactivate_notice( $slug ) {
			if ( ! isset( $this->active_notices[ $slug ] ) ) {
				return false;
			} elseif ( ! isset( $this->notices[ $slug ] ) ) {
				return false;
			}

			$this->notices[ $slug ]['active'] = false;
			unset( $this->active_notices[ $slug ] );
			$this->obj_update_options();

			return true;
		}

		/*** DISPLAY Methods **************************************************/
		/**
		 * Deregister Scripts
		 *
		 * Initial Admin Screen action to remove apl script(s) from all screens;
		 * which will be registered if executed on screen.
		 * NOTE: As of 0.4.2, most of it is default layout, styling, & scripting
		 * that is loaded on all pages. Which can later be different.
		 *
		 * @since 0.4.2
		 * @access private
		 *
		 * @see self::admin_screen()
		 */
		private function deregister_scripts() {
			wp_deregister_script( 'apl-notices-js' );
			wp_deregister_style( 'apl-notices-css' );
		}

		/**
		 * (Register) Enqueue Scripts
		 *
		 * Used to register, enqueue, and localize any JS data. Styles can later be added.
		 *
		 * @since 0.4.2
		 */
		public function admin_enqueue_scripts() {
			// Register.
			wp_register_script(
				'apl-notice-js',
				APL_URL . 'admin/js/apl-notices.js',
				array(),
				APL_VERSION,
				true
			);


			// Localization.
			$notice_delays = array();
			foreach ( $this->active_notices as $notice_slug => $notice_display_time ) {
				foreach ( $this->notices[ $notice_slug ]['action_options'] as $delay_index => $delay_arr ) {
					$notice_delays[ $notice_slug ][] = $delay_index;
				}
			}

			$admin_notice_localize = array(
				'notice_nonce'  => wp_create_nonce( 'apl_ajax_notice' ),
				'notice_delays' => $notice_delays,
			);
			wp_localize_script( 'apl-notice-js', 'apl_notice_data', $admin_notice_localize );

			// Enqueue.
			wp_enqueue_script( 'apl-notice-js' );

			wp_enqueue_style(
				'apl-notices-css',
				APL_URL . 'admin/css/apl-notices.css',
				false,
				APL_VERSION,
				false
			);
		}

		/**
		 * Display Notice as Default
		 *
		 * Method for default WP Admin notices.
		 * NOTE: As of 0.4.2, display_notice_default() & display_notice_apl()
		 * have the same functionality, but serves as a future development concept.
		 *
		 * @since 0.4.2
		 * @since 0.4.4 Fixed displaying content when JS hasn't loaded.
		 *
		 * @uses APL_DIR . 'admin/display/notice-default.php' Template for default notices.
		 *
		 * @return void
		 */
		public function display_notice_default() {
			if ( ! wp_script_is( 'apl-notice-js', 'enqueued' ) ) {
				return;
			}

			$current_screen  = get_current_screen();
			$current_user_id = get_current_user_id();
			foreach ( $this->active_notices as $a_notice_slug => $a_notice_time_display ) {
				$notice_show = true;

				// Screen Restriction.
				if ( ! empty( $this->notices[ $a_notice_slug ]['screens'] ) ) {
					if ( ! in_array( 'apl', $this->notices[ $a_notice_slug ]['screens'], true ) ) {
						if ( ! in_array( $current_screen->id, $this->notices[ $a_notice_slug ]['screens'], true ) ) {
							continue;
						}
					}
				}

				// User Settings.
				if ( 'user' === $this->notices[ $a_notice_slug ]['target'] ) {
					$user_dismissed = get_user_meta( $current_user_id, 'apl_notice_dismissed_' . $a_notice_slug, true );
					if ( ! $user_dismissed ) {
						$user_notice_time_display = get_user_meta( $current_user_id, 'apl_notice_display_time_' . $a_notice_slug, true );
						if ( ! empty( $user_notice_time_display ) ) {
							$a_notice_time_display = intval( $user_notice_time_display );
						}
					} else {
						$notice_show = false;
					}
				}

				// Display/Render.
				if ( time() > $a_notice_time_display && $notice_show ) {
					include APL_DIR . 'admin/view/display/notice-default.php';
				}
			}
		}

		/**
		 * Display Notice as APL Screens
		 *
		 * Method for Admin notices exclusive to APL screens.
		 * NOTE: As of 0.4.2, display_notice_default() & display_notice_apl()
		 * have the same functionality, but serves as a future development concept.
		 *
		 * @since 0.4.2
		 *
		 * @uses APL_DIR . 'admin/display/notice-apl.php' Template for notices.
		 *
		 * @return void
		 */
		public function display_notice_apl() {
			if ( ! wp_script_is( 'apl-notice-js', 'enqueued' ) ) {
				return;
			}

			$current_screen  = get_current_screen();
			$current_user_id = get_current_user_id();
			foreach ( $this->active_notices as $a_notice_slug => $a_notice_time_display ) {
				$notice_show = true;

				// Screen Restriction.
				if ( ! empty( $this->notices[ $a_notice_slug ]['screens'] ) ) {
					if ( ! in_array( 'apl', $this->notices[ $a_notice_slug ]['screens'], true ) ) {
						if ( ! in_array( $current_screen->id, $this->notices[ $a_notice_slug ]['screens'], true ) ) {
							continue;
						}
					}
				}

				// User Settings.
				if ( 'user' === $this->notices[ $a_notice_slug ]['target'] ) {
					$user_dismissed = get_user_meta( $current_user_id, 'apl_notice_dismissed_' . $a_notice_slug, true );
					if ( ! $user_dismissed ) {
						$user_notice_time_display = get_user_meta( $current_user_id, 'apl_notice_display_time_' . $a_notice_slug, true );
						if ( ! empty( $user_notice_time_display ) ) {
							$a_notice_time_display = intval( $user_notice_time_display );
						}
					} else {
						$notice_show = false;
					}
				}

				// Display/Render.
				if ( time() > $a_notice_time_display && $notice_show ) {
					include APL_DIR . 'admin/view/display/notice-apl.php';
				}
			}
		}

		/**
		 * AJAX Notice Action
		 *
		 * Fires when a Delay_Option is clicked and sent via AJAX. Also includes
		 * WP Default Dismiss (rendered as a clickable button on upper-right).
		 *
		 * @since 0.4.2
		 *
		 * @see APL_DIR . 'js/admin-notice.js'
		 */
		public function ajax_notice_action() {
			check_ajax_referer( 'apl_ajax_notice' );
			// Notice (Slug) => (Delay_Options) Index.
			$notice_slug = null;
			$delay_index = null;
			if ( isset( $_POST['notice_slug'] ) ) {
				$notice_slug = filter_input( INPUT_POST, 'notice_slug', FILTER_SANITIZE_STRING );
			}
			if ( isset( $_POST['delay_index'] ) ) {
				$delay_index = filter_input( INPUT_POST, 'delay_index', FILTER_SANITIZE_STRING );
			}
			if ( empty( $notice_slug ) || empty( $delay_index ) ) {
				wp_die();
			}

			$action_options            = $this->action_options_defaults();
			$action_options['time']    = $this->default_dismiss_delay;
			$action_options['dismiss'] = false;

			if ( isset( $this->notices[ $notice_slug ]['action_options'][ $delay_index ] ) ) {
				$action_options = $this->notices[ $notice_slug ]['action_options'][ $delay_index ];
			}

			// User Notices or Sitewide.
			if ( 'user' === $this->notices[ $notice_slug ]['target'] ) {
				// Always sets the delay time, even if dismissed, so last timestamp is recorded.
				$current_user_id = get_current_user_id();
				if ( $action_options['time'] ) {
					$metadata = time() + $action_options['time'];
					update_user_meta( $current_user_id, 'apl_notice_display_time_' . $notice_slug, $metadata );
				}
				if ( $action_options['dismiss'] ) {
					update_user_meta( $current_user_id, 'apl_notice_dismissed_' . $notice_slug, $action_options['dismiss'] );
				}
			} else {
				if ( $action_options['time'] ) {
					$this->active_notices[ $notice_slug ] = time() + $action_options['time'];
				}

				if ( $action_options['dismiss'] ) {
					$this->deactivate_notice( $notice_slug );
				}
			}

			$this->obj_update_options();
			wp_die();
		}

	}
	// CLASS INITIALIZATION.
	// Should this be a singleton class instead of a global?
	global $apl_notices;
	$apl_notices = new APL_Notices();
}
