<?php

/**
 * APL Post List Class
 *
 * Preset Post List Object used by Preset Db.
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.1.0
 */

/**
 * APL Post List
 *
 * Preset Post List Object that is used by Preset Db to store within the database.
 *
 * @since 0.1.0
 * @since 0.4.0 - Changed class name.
 */
class APL_Post_List {
	
	public $id = 0;

	public $title = '';

	public $slug = '';
	
	/**
	 * Filter by Page Parents.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string
	 */
	public $post_parents = '';

	/**
	 * Filter by Post Type and Taxonomy structure.
	 * @todo Change to an array.
	 *
	 * @since 0.3.0
	 * @var object
	 */
	public $post_tax;

	/**
	 * Post List Amount.
	 *
	 * @since 0.1.0
	 * @version 0.3.0  - Changed (string) to (int).
	 * @var int
	 */
	public $list_count = 0;

	/**
	 * Order Filter By.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $list_order_by = '';

	/**
	 * Order Filter Ascending or Descending.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $list_order = '';

	/**
	 * Filter by Post Visibility.
	 *
	 * @since 0.1.0
	 * @var array[]string
	 */
	public $post_visibility = array( 'public' );

	/**
	 * Filter by Post Status.
	 *
	 * @since 0.3.0
	 * @version 0.3.b5 - Change from (string) to (array) => (string).
	 * @var array[]string
	 */
	public $post_status = array( 'publish' );

	/**
	 * Filter by User Permissions.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	public $user_perm = 'readable';

	/**
	 * Operator for Author ID filter.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	public $post_author_operator = 'none';

	/**
	 * Filter by Author ID.
	 *
	 * @since 0.3.0
	 * @var array=>int
	 */
	public $post_author_ids = array();

	/**
	 * Filter Stickies.
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $list_ignore_sticky = false;

	/**
	 * Filter Current Post/Page.
	 *
	 * @since 0.1.0
	 * @version 0.3.0 - changed (string) to (boolean).
	 * @var boolean
	 */
	public $list_exclude_current = true;

	/**
	 * Filter Duplicates.
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $list_exclude_duplicates = false;

	/**
	 * Filter Posts.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $list_exclude_posts = array();

	/**
	 * Design for APL Preset Loop.
	 *
	 * @since 0.4.0
	 * @var string
	 */
	public $apl_design = '';

	public function __construct( $post_list_name ) {
		$this->title = $post_list_name;
		$this->slug  = (string) sanitize_title_with_dashes( $post_list_name );
		$args = array(
			'name' => $this->slug,
		);
		$this->get_data( $args );

		if ( is_admin() ) {
			// Save Design Meta Data Hook.
			add_action( 'save_post_apl_post_list', array( &$this, 'hook_action_save_post_apl_post_list' ) );
			// Delete Design Hook.
			// Import / Export Hook.
		}
		
	}
	
	private function get_data( $args = array() ) {
		$defaults = array(
			'name'   => '',
			'post_type'   => 'apl_post_list',
			//'post_status' => 'publish',
			'numberposts' => 1,
		);
		$args = wp_parse_args( $args, $defaults );

		// If there is a design, set this variable to the meta data it has.
		// Else no designs, return false.
		$post_lists = get_posts( $args );
		if ( $post_lists ) {
			$this->id      = $post_lists[0]->ID;
			$this->title   = esc_html( $post_lists[0]->post_title );
			$this->slug    = $post_lists[0]->post_name;
			
			$this->post_parents            = get_post_meta( $this->id, 'apl_post_parents', true ) ?: '';
			$this->post_tax                = get_post_meta( $this->id, 'apl_post_tax', true ) ?: '';
			$this->list_count              = get_post_meta( $this->id, 'apl_list_count ', true ) ?: '';
			$this->list_order_by           = get_post_meta( $this->id, 'apl_list_order_by ', true ) ?: '';
			$this->list_order              = get_post_meta( $this->id, 'apl_list_order', true ) ?: '';
			$this->post_visibility         = get_post_meta( $this->id, 'apl_post_visibility ', true ) ?: '';
			$this->post_status             = get_post_meta( $this->id, 'apl_post_status', true ) ?: '';
			$this->user_perm               = get_post_meta( $this->id, 'apl_user_perm', true ) ?: '';
			$this->post_author_operator    = get_post_meta( $this->id, 'apl_post_author_operator', true ) ?: '';
			$this->post_author_ids         = get_post_meta( $this->id, 'apl_list_ignore_sticky', true ) ?: '';
			$this->list_ignore_sticky      = get_post_meta( $this->id, 'apl_list_exclude_posts', true ) ?: '';
			$this->list_exclude_posts      = get_post_meta( $this->id, 'apl_list_exclude_posts', true ) ?: '';
			$this->list_exclude_duplicates = get_post_meta( $this->id, 'apl_list_exclude_duplicates', true ) ?: '';
			$this->list_exclude_current    = get_post_meta( $this->id, 'apl_list_exclude_current', true ) ?: '';
			$this->apl_design              = get_post_meta( $this->id, 'apl_design', true ) ?: '';;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns the APL Design slug.
	 *
	 * Gets and returns $this->apl_design.
	 *
	 * @since 0.4.0
	 *
	 * @return string Returns the slug from variable APL Design.
	 */
	public function get_apl_design() {
		$apl_design = apply_filters( 'apl_design_slug', $this->apl_design, $this );
		return $apl_design;
	}
	/**
	 * Save APL Design Object.
	 *
	 * Inserts or updates the Design post data with $this object.
	 *
	 * @since 0.4.0
	 * @access public
	 *
	 * @return void
	 */
	public function save_post_list() {
		$get_args = array(
			'name'      => $this->slug,
			'post_type' => 'apl_post_list',
			//'post_title'          => $this->title,
			//'post_status'         => 'publish',
			//'post__in'            => array( $this->id ),
			//'ignore_sticky_posts' => true,
		);
		$post_list = get_posts( $get_args );

		$save_args = array(
			'ID'          => $this->id,
			'post_title'  => $this->title,
			'post_name'   => $this->slug,
			'post_status' => 'publish',
			'post_type'   => 'apl_post_list',
		);
		if ( empty( $post_list ) ) {
			$this->insert_post_list_post( $save_args );
		} else {
			$this->update_post_list_post( $save_args );
		}
	}

	/**
	 * Insert Design post data.
	 *
	 * Inserts APL Design's post args to the database to create a new WP_Post.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $args Post arg array for creating Post objects.
	 * @return void
	 */
	private function insert_post_list_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		wp_insert_post( $args );
	}

	/**
	 * Update Design post data.
	 *
	 * Inserts APL Design's post args to the database to update a WP_Post.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param type $args Post arg array for creating Post objects.
	 * @return void
	 */
	private function update_post_list_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		wp_update_post( $args );
	}

	/**
	 * Default Post Arg Array.
	 *
	 * Sets the default Post Argument Array for WP_Post objects.
	 *
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
			'ID'               => 0,
			//'post_author'      => $user_id,
			//'post_date'        => '', Default: is current time.
			//'post_date_gmt'    => '', Default: is $post_date.
			//'post_content'     => '',
			//'post_content_filtered' => '',
			'post_title'       => '',
			'post_name'        => '',
			//'post_excerpt'     => '',
			'post_status'      => 'draft',
			'post_type'        => 'apl_post_list',
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

	// TODO Simplify method with a For Loop? Or keep the same for readability?
	public function hook_action_save_post_apl_post_list( $post_id ) {
		$this->id = $post_id;
		$post_list_post = get_post( $this->id );

		$old_post_parents             = get_post_meta( $post_list_post, 'apl_post_parents', true ) ?: '';
		$old_post_tax                 = get_post_meta( $post_list_post, 'apl_post_tax', true ) ?: '';
		$old_list_count               = get_post_meta( $post_list_post, 'apl_list_count', true ) ?: '';
		$old_list_order_by            = get_post_meta( $post_list_post, 'apl_list_order_by ', true ) ?: '';
		$old_list_order               = get_post_meta( $post_list_post, 'apl_list_order', true ) ?: '';
		$old_post_visibility          = get_post_meta( $post_list_post, 'apl_post_visibility', true ) ?: '';
		$old_post_status              = get_post_meta( $post_list_post, 'apl_post_status', true ) ?: '';
		$old_user_perm                = get_post_meta( $post_list_post, 'apl_user_perm', true ) ?: '';
		$old_post_author_operator     = get_post_meta( $post_list_post, 'apl_post_author_operator', true ) ?: '';
		$old_post_author_ids          = get_post_meta( $post_list_post, 'apl_post_author_ids', true ) ?: '';
		$old_list_ignore_sticky       = get_post_meta( $post_list_post, 'apl_list_ignore_sticky', true ) ?: '';
		$old_list_exclude_posts       = get_post_meta( $post_list_post, 'apl_list_exclude_posts', true ) ?: '';
		$old_list_exclude_duplicates  = get_post_meta( $post_list_post, 'apl_list_exclude_duplicates', true ) ?: '';
		$old_list_exclude_current     = get_post_meta( $post_list_post, 'apl_list_exclude_current', true ) ?: '';
		$old_apl_design               = get_post_meta( $post_list_post, 'apl_design', true ) ?: '';

		if ( $old_post_parents !== $this->post_parents ) {
			update_post_meta( $this->id, 'apl_post_parents', $this->post_parents );
		}
		if ( $old_post_tax !== $this->post_tax ) {
			update_post_meta( $this->id, 'apl_post_tax', $this->post_tax );
		}
		if ( $old_list_count !== $this->list_count ) {
			update_post_meta( $this->id, 'apl_list_count', $this->list_count );
		}
		if ( $old_list_order_by !== $this->list_order_by ) {
			update_post_meta( $this->id, 'apl_list_order_by', $this->list_order_by );
		}
		if ( $old_list_order !== $this->list_order ) {
			update_post_meta( $this->id, 'apl_list_order', $this->list_order );
		}
		if ( $old_post_visibility !== $this->post_visibility ) {
			update_post_meta( $this->id, 'apl_post_visibility', $this->post_visibility );
		}
		if ( $old_post_status !== $this->post_status ) {
			update_post_meta( $this->id, 'apl_post_status', $this->post_status );
		}
		if ( $old_user_perm !== $this->user_perm ) {
			update_post_meta( $this->id, 'apl_user_perm', $this->user_perm );
		}
		if ( $old_post_author_operator !== $this->post_author_operator ) {
			update_post_meta( $this->id, 'apl_post_author_operator', $this->post_author_operator );
		}
		if ( $old_post_author_ids !== $this->post_author_ids ) {
			update_post_meta( $this->id, 'apl_post_author_ids', $this->post_author_ids );
		}
		if ( $old_list_ignore_sticky !== $this->list_ignore_sticky ) {
			update_post_meta( $this->id, 'apl_list_ignore_sticky', $this->list_ignore_sticky );
		}
		if ( $old_list_exclude_posts !== $this->list_exclude_posts ) {
			update_post_meta( $this->id, 'apl_list_exclude_posts', $this->list_exclude_posts );
		}
		if ( $old_list_exclude_duplicates !== $this->list_exclude_duplicates ) {
			update_post_meta( $this->id, 'apl_list_exclude_duplicates', $this->list_exclude_duplicates );
		}
		if ( $old_list_exclude_current !== $this->list_exclude_current ) {
			update_post_meta( $this->id, 'apl_list_exclude_current', $this->list_exclude_current );
		}
		if ( $old_apl_design !== $this->apl_design ) {
			update_post_meta( $this->id, 'apl_design', $this->apl_design );
		}
	}
	
}
