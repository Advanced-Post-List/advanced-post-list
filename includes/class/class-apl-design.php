<?php
/**
 * APL Deign Class
 *
 * APL Preset Design for handling the object and post type data.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.4.0
 */

/**
 * APL Design
 *
 * Used to handle Preset Designs
 *
 * @since 0.4.0
 */
class APL_Design {

	/**
	 * ID to Database Design
	 *
	 * @since 0.4.0
	 * @access private
	 * @var int
	 */
	public $id = 0;

	/**
	 * Title
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Slug
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $slug = '';

	/**
	 * Before List
	 *
	 * HTML content for Before.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $before = '';

	/**
	 * List Content
	 *
	 * HTML & Shortcode content for list Content.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $content = '';

	/**
	 * After List
	 *
	 * HTML content for After.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $after = '';

	/**
	 * Empty Message
	 *
	 * HTML content for an empty list.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $empty = '';

	/**
	 * Constructor for APL_Design Class
	 *
	 * Creates or loads an APL Design Object.
	 *
	 * @since 0.4.0
	 *
	 * @param string $design_name Saved as title, but is converted to a slug.
	 */
	public function __construct( $design_name ) {
		// Add Hooks.
		$this->slug = sanitize_title_with_dashes( $design_name );
		$this->title = (string) $design_name;

		$args = array(
			'name' => $this->slug,
		);
		$this->get_data( $args );

		if ( is_admin() ) {
			// Draft/Init Hook.

			// Save Design Meta Data Hook.
			add_action( 'save_post_apl_design', array( &$this, 'hook_action_save_post_apl_design' ), 10, 3 );
			// Delete Design Hook.
			// Import / Export Hook.
		}
	}

	/**
	 * Get Design data
	 *
	 * Gets the design data from the database.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $args Query args for get_posts ( Same as WP_Query ).
	 * @return boolean Will return false on failure.
	 */
	private function get_data( $args = array() ) {
		$defaults = array(
			'post_type'       => 'apl_design',
			//'name'            => '',
			'p' => 0,
			'post_status'     => array(
				'draft',
				'pending',
				'publish',
				'future',
				'private',
				'trash',
			),
			'posts_per_page'  => 1,
			//'suppress_filters' => true,
		);
		$args = wp_parse_args( $args, $defaults );

		// If there is a design, set this variable to the meta data it has.
		// Else no designs stored, return false.
		add_action( 'parse_query', array( $this, 'parse_query' ), 99, 1 );
		$d_query = new WP_Query( $args );
		remove_action( 'parse_query', array( $this, 'parse_query' ), 99 );

		if ( 1 > $d_query->post_count ) {
			return false;
		}
		$design = $d_query->post;

		if ( $design->post_name === $args['name'] && ! empty( $args['name'] ) ) {
			$this->id      = absint( $design->ID );
			$this->title   = esc_html( $design->post_title );
			$this->slug    = $design->post_name;

			$this->before  = get_post_meta( $this->id, 'apl_before', true )   ?: '';
			$this->content = get_post_meta( $this->id, 'apl_content', true )  ?: '';
			$this->after   = get_post_meta( $this->id, 'apl_after', true )    ?: '';
			$this->empty   = get_post_meta( $this->id, 'apl_empty', true )    ?: '';
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Parse Query
	 *
	 * @see APL_Design::get_date() action hook 'parse_query'
	 * @param $d_query
	 * @return mixed
	 */
	public function parse_query( $d_query ) {
		if ( 'apl_design' === $d_query->query['post_type'] || in_array( 'apl_design', $d_query->query['post_type'] ) ) {
			$d_query->query['post_type'] = 'apl_design';
			$d_query->query_vars['post_type'] = 'apl_design';
			$d_query->query['p'] = 0;
			$d_query->query_vars['p'] = 0;
		}

		return $d_query;
	}

	/**
	 * Save APL Design Object
	 *
	 * Inserts or updates the Design post data with $this object.
	 *
	 * @since 0.4.0
	 * @access public
	 */
	public function save_design() {
		if ( empty( $this->slug ) ) {
			return;
		}
		$get_args = array(
			'post__in'   => array( $this->id ),
			//'name'       => $this->slug,
			'post_type'  => 'apl_design',
		);
		$designs = new WP_Query( $get_args );

		$save_postarr = array(
			'ID'               => $this->id,
			'post_title'       => $this->title,
			'post_name'        => $this->slug,
			'post_status'      => 'publish',
			'post_type'        => 'apl_design',
		);

		if ( 1 > $designs->post_count || 0 === $this->id ) {
			$this->insert_design_post( $save_postarr );
		} else {
			$this->update_design_post( $save_postarr );
		}
	}

	/**
	 * Deletes the Design
	 *
	 * @since 0.4.0
	 *
	 * @link https://codex.wordpress.org/Function_Reference/wp_delete_post
	 */
	public function delete_design() {

		wp_delete_post( $this->id, true );

	}

	/**
	 * Insert Design post data
	 *
	 * Inserts APL Design's post args to the database to create a new WP_Post.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $args Post arg array for creating Post objects.
	 */
	private function insert_design_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		global $wp_rewrite;
		if ( is_null( $wp_rewrite ) ) {
			$wp_rewrite = new WP_Rewrite();
		}

		remove_all_actions( 'save_post_apl_design', 10 );
		add_action( 'save_post_apl_design', array( &$this, 'hook_action_save_post_apl_design' ), 10, 3 );
		$old_id = $this->id;
		$rtn_post_id = wp_insert_post( $args );

		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			if ( 0 === $old_id ) {
				$sitepress->set_element_language_details( $rtn_post_id, 'post_apl_design', null, ICL_LANGUAGE_CODE );
			}
		}

		// ERROR.
		if ( is_wp_error( $rtn_post_id ) ) {
			$errors = $rtn_post_id->get_error_messages();
			foreach ( $errors as $error ) {
				echo $error;
			}
		}
	}

	/**
	 * Update Design post data
	 *
	 * Inserts APL Design's post args to the database to update a WP_Post.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param type $args Post arg array for creating Post objects.
	 */
	private function update_design_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		global $wp_rewrite;
		if ( is_null( $wp_rewrite ) ) {
			$wp_rewrite = new WP_Rewrite();
		}

		remove_all_actions( 'save_post_apl_design', 10 );
		add_action( 'save_post_apl_design', array( &$this, 'hook_action_save_post_apl_design' ), 10, 3 );
		$rtn_post_id = wp_update_post( $args );

		// ERROR.
		if ( is_wp_error( $rtn_post_id ) ) {
			$errors = $rtn_post_id->get_error_messages();
			foreach ( $errors as $error ) {
				echo $error;
			}
		}
	}

	/**
	 * Default Post Arg Array
	 *
	 * Sets the default Post Argument Array for WP_Post objects.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @see wp_insert_post & wp_update_post for param $postarr.
	 * @link https://developer.wordpress.org/reference/functions/wp_insert_post/
	 * @link https://developer.wordpress.org/reference/functions/wp_update_post/
	 *
	 * @return array Variable for postarr param.
	 */
	private function default_postarr() {
		return array(
			//'ID'               => 0,
			//'post_author'      => $user_id,
			//'post_date'        => '', Default: is current time.
			//'post_date_gmt'    => '', Default: is $post_date.
			//'post_content'     => '',
			//'post_content_filtered' => '',
			'post_title'       => '',
			'post_name'        => '',
			//'post_excerpt'     => '',
			//'post_status'      => 'draft',
			'post_type'        => 'apl_design',
			//'comment_status'   => '',
			//'ping_status'      => '',
			//'post_password'    => '',
			//'to_ping' =>  '',
			//'pinged' => '',
			//'post_parent'      => 0,
			//'menu_order'       => 0,
			//'guid' => '',
			//'import_id'        => 0,
			//'context'          => '',
		);
	}

	/**
	 * Hook for Saving Design Post Meta
	 *
	 * WP hook for saving meta data in APL Design post type. This fires when
	 * either insert or update post has been used.
	 *
	 * @since 0.4.0
	 *
	 * @param int $post_id Post ID that is past by WP when saving post.
	 */
	public function hook_action_save_post_apl_design( $post_id, $post_obj, $update ) {
		$this->id     = $post_id;
		$this->title  = $post_obj->post_title;
		$this->slug   = $post_obj->post_name;

		$old_before  = get_post_meta( $this->id, 'apl_before', true );
		$old_content = get_post_meta( $this->id, 'apl_content', true );
		$old_after   = get_post_meta( $this->id, 'apl_after', true );
		$old_empty   = get_post_meta( $this->id, 'apl_empty', true );

		if ( $old_before !== $this->before ) {
			update_post_meta( $this->id, 'apl_before', $this->before );
		}
		if ( $old_content !== $this->content ) {
			update_post_meta( $this->id, 'apl_content', $this->content );
		}
		if ( $old_after !== $this->after ) {
			update_post_meta( $this->id, 'apl_after', $this->after );
		}
		if ( $old_empty !== $this->empty ) {
			update_post_meta( $this->id, 'apl_empty', $this->empty );
		}

		remove_action( 'save_post_apl_design', array( $this, 'hook_action_save_post_apl_design' ) );
	}
}



