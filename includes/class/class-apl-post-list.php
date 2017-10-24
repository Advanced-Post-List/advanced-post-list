<?php

/**
 * APL Post List Class
 *
 * Preset Post List Object used by Preset Db.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
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

	/**
	 * Post Data ID
	 *
	 * @since 0.4.0
	 * @access public
	 * @var int
	 */
	public $id = 0;

	/**
	 * Post Data Title
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Post Data slug
	 *
	 * @since 0.4.0
	 * @access public
	 * @var string $slug
	 */
	public $slug = '';

	/**
	 * Post Types filter
	 *
	 * Also used to determine which Tax_Queries are active and to pull data from.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed from $post_tax to $post_type, and works in
	 *                conjunction with tax_query.
	 *                Fixed/reduced nested multi-dimensional arrays.
	 * @access public
	 * @var array $post_type {
	 *     @type string X => 'any'
	 *     --- OR ---
	 *     @type array  X => array( 'post_type_1' ),
	 *     // Consolidate in WP_Query with Tax_Query & Dynamic features.
	 *     // Use first post type for Tax_Query
	 *     @type array  X => array( 'post_type_2', 'post_type_3' )
	 * }
	 */
	public $post_type = array( 'any' );

	/**
	 * Tax Query filter ( Modified )
	 *
	 * Follows the same general Query_Args structure, but carries additional
	 * variables used with APL.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array $tax_query {
	 *     $post_type = array(
	 *         'relation'  => 'AND' || 'OR',
	 *         [Y1]        => array(
	 *             'taxonomy'           => 'tax_1',
	 *             'field'              => 'id',
	 *             'terms'              => array( 103, 115, 206 ),
	 *             'include_children'   => false,
	 *             'operator'           => 'IN' || 'AND' || -'NOT_IN'-,
	 *
	 *             'apl_terms_dynamic'  => false,
	 *             'apl_terms_slug      => array( 'alpha', 'beta', 'gamma' ),
	 *         ),
	 *         [Y2]        => array(
	 *             'taxonomy'           => 'tax_2',
	 *             'field'              => 'id',
	 *             'terms'              => array( 456, 189, 752 ),
	 *             'include_children'   => false,
	 *             'operator'           => 'IN' || 'AND' || -'NOT_IN'-,
	 *
	 *             'apl_terms_dynamic'  => false,
	 *             'apl_terms_slug      => array( 'alfa', 'bravo', 'charlie' ),
	 *         ),
	 *     )
	 *     --- OR ---
	 *     $post_type => {}, //ANY
	 *     --- OR ---
	 *     'any' => array(), //ANY
	 * }
	 */
	public $tax_query = array();

	/**
	 * Filter by Page Parents
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to an array.
	 * @access public
	 * @var array $post_parent__in {
	 *     @type array $post_type => array( 1, 23 ) Contain Post/Page IDs.
	 * }
	 */
	public $post_parent__in = array();

	/**
	 * Dynamic Page Parents
	 *
	 * May want to change to apl_ prefix.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array $post_parent_dynamic {
	 *     Desc
	 *
	 *     @type boolean $post_type => (bool) Active Post Types with dynamic
	 *                                        Page Parent settings.
	 * }
	 */
	public $post_parent_dynamic = array();

	/**
	 * Post List Amount
	 *
	 * @since 0.1.0
	 * @version 0.3.0  - Changed (string) to (int).
	 * @var int
	 */
	public $posts_per_page = 5;

	/**
	 * Offset
	 *
	 * @since 0.4.0
	 * @var int
	 */
	public $offset = 0;

	/**
	 * Order Filter By
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Changed from $_list_order_by to $order_by.
	 *              Added 'none', and value as default.
	 * @var string
	 */
	public $order_by = 'none';

	/**
	 * Order Filter Ascending or Descending
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Changed from $_list_order to $order
	 *              Changed value to 'DESC' as default.
	 * @var string
	 */
	public $order = 'DESC';

	/**
	 * Filter by Post Status
	 *
	 * Note: Empty default as array( 'public', 'publish' )
	 *
	 * @since 0.3.0
	 * @version 0.3.b5 - Change from (string) to (array) => (string).
	 * @var  string | array $post_status {
	 *     $type string 'public', 'publish' || 'private', 'pending'
	 * }
	 */
	public $post_status = 'none';

	/**
	 * Filter by User Permissions
	 *
	 * @since 0.3.0
	 * @var string 'none' || 'readable' || 'editable'
	 */
	public $perm = 'none';

	/**
	 * Operator for Author ID filter
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed from $post_author_operator to $author__bool.
	 * @var string 'none' || 'in' || 'not_in'.
	 */
	public $author__bool = 'none';

	/**
	 * Filter by Author ID
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Change from $post_author_ids to $author__in
	 * @var array $author__in {
	 *     @type int $index => Author_ID
	 * }
	 */
	public $author__in = array();

	/**
	 * Filter Stickies
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $ignore_sticky_posts = true;

	/**
	 * Filter Posts
	 *
	 * @since 0.3.0
	 * @var array => int
	 */
	public $post__not_in = array();

	/**
	 * Filter Current Post/Page
	 *
	 * @since 0.1.0
	 * @version 0.3.0 - changed (string) to (boolean).
	 * @var boolean
	 */
	public $pl_exclude_current = true;

	/**
	 * Filter Duplicates
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $pl_exclude_dupes = false;

	/**
	 * Design for APL Preset Loop
	 *
	 * @since 0.4.0
	 * @var string
	 */
	public $pl_apl_design = '';

	/**
	 * APL Post List Constructor
	 *
	 * Class Constructor.
	 *
	 * @since 0.4.0
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param string $post_list_name Post List slug.
	 */
	public function __construct( $post_list_name ) {
		//$this->title = $post_list_name;
		$this->slug  = (string) sanitize_title_with_dashes( $post_list_name );
		$args = array(
			'name' => $this->slug,
		);
		$this->get_data( $args );

		if ( is_admin() ) {
			// Save Design Meta Data Hook.
			// Only use 'save_post_{post_type}' for saving object as meta data
			// after post has been saved, and data has been process via {status}_{post_type}.
			add_action( 'save_post_apl_post_list', array( &$this, 'hook_action_save_post_apl_post_list' ), 10, 3 );

			// Delete Design Hook.
			// Import / Export Hook.
		}
	}

	/**
	 * Get APL Post List Data
	 *
	 * Get Post Data and set as Post List class.
	 *
	 * @since 0.4.0
	 *
	 * @see WP_Query Args.
	 * @link https://gist.github.com/luetkemj/2023628
	 * @see get_post_meta().
	 * @link https://developer.wordpress.org/reference/functions/get_post_meta/
	 *
	 * @param array $args WP_Query args.
	 * @return boolean True if data exists.
	 */
	private function get_data( $args = array() ) {
		$defaults = array(
			'post_type'       => 'apl_post_list',
			//'post__in'        => array(), // Need this?
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

		// If there is a design, set object variables to the meta data.
		// Else no designs, return false.
		$pl_query = new WP_Query( $args );
		if ( 1 > $pl_query->post_count ) {
			return false;
		}
		$post_list = $pl_query->post;

		if ( $post_list->post_name === $args['name'] && ! empty( $args['name'] ) ) {
			$this->id      = absint( $post_list->ID );
			$this->title   = esc_html( $post_list->post_title );
			$this->slug    = sanitize_title_with_dashes( $post_list->post_name );

			$this->post_type            = get_post_meta( $this->id, 'apl_post_type', true )            ?: array( 'any' );
			$this->tax_query            = get_post_meta( $this->id, 'apl_tax_query', true )            ?: array();
			$this->post_parent__in      = get_post_meta( $this->id, 'apl_post_parent__in', true )      ?: array();
			$this->post_parent_dynamic  = get_post_meta( $this->id, 'apl_post_parent_dynamic', true )  ?: array();

			$this->posts_per_page       = get_post_meta( $this->id, 'apl_posts_per_page', true )       ?: 5;
			$this->offset               = get_post_meta( $this->id, 'apl_offset', true )               ?: 0;
			$this->order_by             = get_post_meta( $this->id, 'apl_order_by', true )             ?: 'none';
			$this->order                = get_post_meta( $this->id, 'apl_order', true )                ?: 'DESC';
			$this->author__bool         = get_post_meta( $this->id, 'apl_author__bool', true )         ?: 'none';
			$this->author__in           = get_post_meta( $this->id, 'apl_author__in', true )           ?: array();
			$this->post_status          = get_post_meta( $this->id, 'apl_post_status', true )          ?: 'none';
			$this->perm                 = get_post_meta( $this->id, 'apl_perm', true )                 ?: 'none';
			$this->post__not_in         = get_post_meta( $this->id, 'apl_post__not_in', true )         ?: array();

			$tmp_ignore_sticky_posts  = get_post_meta( $this->id, 'apl_ignore_sticky_posts', true );
			$this->ignore_sticky_posts  = ( false !== $tmp_ignore_sticky_posts )  ? (bool) $tmp_ignore_sticky_posts : true;

			$tmp_pl_exclude_current   = get_post_meta( $this->id, 'apl_pl_exclude_current', true );
			$this->pl_exclude_current   = ( false !== $tmp_pl_exclude_current )   ? (bool) $tmp_pl_exclude_current  : true;

			$tmp_pl_exclude_dupes     = get_post_meta( $this->id, 'apl_pl_exclude_dupes', true );
			$this->pl_exclude_dupes     = ( false !== $tmp_pl_exclude_dupes )     ? (bool) $tmp_pl_exclude_dupes    : false;

			//$apl_design_slug            = apply_filters( 'apl_post_list_get_data_apl_design_slug', $this->slug );
			$this->pl_apl_design        = get_post_meta( $this->id, 'apl_pl_apl_design', true )        ?: '';

			return true;
		} else {
			return false;
		}// End if().
	}

	/**
	 * Save APL Post List Object
	 *
	 * Inserts or updates the Post List post data with $this object.
	 *
	 * @since 0.4.0
	 * @access public
	 */
	public function save_post_list() {
		if ( empty( $this->slug ) ) {
			return;
		}
		$get_args = array(
			'post__in'  => array( $this->id ),
			'post_type' => 'apl_post_list',
		);
		$post_lists = new WP_Query( $get_args );

		$save_postarr = array(
			'ID'          => $this->id,
			'post_title'  => $this->title,
			'post_name'   => $this->slug,
			'post_status' => 'publish',
			'post_type'   => 'apl_post_list',
		);
		if ( 1 > $post_lists->post_count || 0 === $this->id ) {
			$this->insert_post_list_post( $save_postarr );
		} else {
			$this->update_post_list_post( $save_postarr );
		}
	}

	/**
	 * Deletes the Post List
	 *
	 * @since 0.4.0
	 *
	 * @link https://codex.wordpress.org/Function_Reference/wp_delete_post
	 */
	public function delete_post_list() {
		wp_delete_post( $this->id, true );
	}

	/**
	 * Insert Post List post data
	 *
	 * Inserts APL Post List's post args to the database to create a new WP_Post.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $args Post arg array for creating Post objects.
	 */
	private function insert_post_list_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		remove_all_actions( 'save_post_apl_post_list', 10 );
		add_action( 'save_post_apl_post_list', array( &$this, 'hook_action_save_post_apl_post_list' ), 10, 3 );
		wp_insert_post( $args );
	}

	/**
	 * Update Post List post data
	 *
	 * Inserts APL Post List's post args to the database to update a WP_Post.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param type $args Post arg array for creating Post objects.
	 */
	private function update_post_list_post( $args = array() ) {
		$defaults = $this->default_postarr();
		$args = wp_parse_args( $args, $defaults );

		global $wp_rewrite;
		$wp_rewrite = new WP_Rewrite();

		remove_all_actions( 'save_post_apl_post_list', 10 );
		add_action( 'save_post_apl_post_list', array( &$this, 'hook_action_save_post_apl_post_list' ), 10, 3 );
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

	/**
	 * Meta Data Save Post APL Post List
	 *
	 * Description.
	 *
	 * @since 0.4.0
	 *
	 * @see Function/method/class relied on
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/wp_insert_post
	 * @global global $_POST Description.
	 *
	 * @param int     $post_id   Post ID.
	 * @param WP_Post $post_obj  Post object.
	 * @param boolean $update    Whether this is an existing post being updated or not.
	 */
	public function hook_action_save_post_apl_post_list( $post_id, $post_obj, $update ) {
		$this->id     = $post_id;
		$this->title  = $post_obj->post_title;
		$this->slug   = $post_obj->post_name;

		$old_post_type            = get_post_meta( $this->id, 'apl_post_type', true );
		$old_tax_query            = get_post_meta( $this->id, 'apl_tax_query', true );
		$old_post_parent__in      = get_post_meta( $this->id, 'apl_post_parent__in', true );
		$old_post_parent_dynamic  = get_post_meta( $this->id, 'apl_post_parent_dynamic', true );

		$old_posts_per_page       = get_post_meta( $this->id, 'apl_posts_per_page', true );
		$old_offset               = get_post_meta( $this->id, 'apl_offset', true );
		$old_order_by             = get_post_meta( $this->id, 'apl_order_by', true );
		$old_order                = get_post_meta( $this->id, 'apl_order', true );
		$old_author__bool         = get_post_meta( $this->id, 'apl_author__bool', true );
		$old_author__in           = get_post_meta( $this->id, 'apl_author__in', true );
		$old_post_status          = get_post_meta( $this->id, 'apl_post_status', true );
		$old_perm                 = get_post_meta( $this->id, 'apl_perm', true );
		$old_post__not_in         = get_post_meta( $this->id, 'apl_post__not_in', true );

		$old_ignore_sticky_posts  = get_post_meta( $this->id, 'apl_ignore_sticky_posts', true );
		$old_ignore_sticky_posts  = ( false !== $old_ignore_sticky_posts )  ? (bool) $old_ignore_sticky_posts : null;

		$old_pl_exclude_current   = get_post_meta( $this->id, 'apl_pl_exclude_current', true );
		$old_pl_exclude_current   = ( false !== $old_pl_exclude_current )   ? (bool) $old_pl_exclude_current  : null;

		$old_pl_exclude_dupes     = get_post_meta( $this->id, 'apl_pl_exclude_dupes', true );
		$old_pl_exclude_dupes     = ( false !== $old_pl_exclude_dupes )     ? (bool) $old_pl_exclude_dupes    : null;

		$old_pl_apl_design        = get_post_meta( $this->id, 'apl_pl_apl_design', true );

		// Compare and update if modified.
		if ( $this->post_type !== $old_post_type ) {
			update_post_meta( $this->id, 'apl_post_type', $this->post_type );
		}
		if ( $this->tax_query !== $old_tax_query ) {
			update_post_meta( $this->id, 'apl_tax_query', $this->tax_query );
		}
		if ( $this->post_parent__in !== $old_post_parent__in ) {
			update_post_meta( $this->id, 'apl_post_parent__in', $this->post_parent__in );
		}
		if ( $this->post_parent_dynamic !== $old_post_parent_dynamic ) {
			update_post_meta( $this->id, 'apl_post_parent_dynamic', $this->post_parent_dynamic );
		}
		if ( $this->posts_per_page !== $old_posts_per_page ) {
			update_post_meta( $this->id, 'apl_posts_per_page', $this->posts_per_page );
		}
		if ( $this->offset !== $old_offset ) {
			update_post_meta( $this->id, 'apl_offset', $this->offset );
		}
		if ( $this->order_by !== $old_order_by ) {
			update_post_meta( $this->id, 'apl_order_by', $this->order_by );
		}
		if ( $this->order !== $old_order ) {
			update_post_meta( $this->id, 'apl_order', $this->order );
		}
		if ( $this->author__bool !== $old_author__bool ) {
			update_post_meta( $this->id, 'apl_author__bool', $this->author__bool );
		}
		if ( $this->author__in !== $old_author__in ) {
			update_post_meta( $this->id, 'apl_author__in', $this->author__in );
		}
		if ( $this->post_status !== $old_post_status ) {
			update_post_meta( $this->id, 'apl_post_status', $this->post_status );
		}
		if ( $this->perm !== $old_perm ) {
			update_post_meta( $this->id, 'apl_perm', $this->perm );
		}
		if ( $this->post__not_in !== $old_post__not_in ) {
			update_post_meta( $this->id, 'apl_post__not_in', $this->post__not_in );
		}
		if ( $this->ignore_sticky_posts !== $old_ignore_sticky_posts ) {
			update_post_meta( $this->id, 'apl_ignore_sticky_posts', $this->ignore_sticky_posts );
		}
		if ( $this->pl_exclude_current !== $old_pl_exclude_current ) {
			update_post_meta( $this->id, 'apl_pl_exclude_current', $this->pl_exclude_current );
		}
		if ( $this->pl_exclude_dupes !== $old_pl_exclude_dupes ) {
			update_post_meta( $this->id, 'apl_pl_exclude_dupes', $this->pl_exclude_dupes );
		}
		if ( $this->pl_apl_design !== $old_pl_apl_design ) {
			update_post_meta( $this->id, 'apl_pl_apl_design', $this->pl_apl_design );
		}

		remove_action( 'save_post_apl_post_list', array( &$this, 'hook_action_save_post_apl_post_list' ) );
	}
}
