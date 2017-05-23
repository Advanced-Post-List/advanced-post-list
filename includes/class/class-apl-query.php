<?php
/**
 * APL Query Class
 *
 * Query API to WP_Query.
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.3.0
 */

/**
 * APL Query
 *
 * Query Object for extended functionality with WP_Query. Enables additional
 * concepts that WP doesn't provide.
 *
 * @since 0.3.0
 * @since 0.3.b8 Fixed nested code.
 */
class APL_Query {

	/**
	 * Stores error message when error occurs.
	 *
	 * @todo Possibly Remove, and just use WP_Query API.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $_posts;

	/**
	 * Query Args.
	 *
	 * @since 0.3.b7
	 * @var array
	 */
	public $_query_str_array;

	/**
	 * Constructor for APL Query.
	 *
	 * Constructor for APL's Query. Adds post/page dynamics, sets the multi-
	 * dimensional query array, and reduces the amount of query strings to be used.
	 *
	 * STEP 1: Add page dynamics to the presetObj.
	 * STEP 2: Set the query strings from the presetObj
	 * STEP 3: Merge any simular queries to lessen the amount of queries.
	 *
	 * @since 0.3.0
	 *
	 * @param object $preset APL Preset Post List Object.
	 * @return void
	 */
	public function __construct( $preset ) {
		// STEP 1.
		$preset                  = $this->set_presetObj_page_vals( $preset );
		// STEP 2.
		$_query_str_array        = $this->set_query( $preset );
		// STEP 3.
		$this->_query_str_array  = $this->query_str_consolidate( $_query_str_array );
	}

	/**
	 * Set Initial Query.
	 *
	 * Sets the initial values for query_str.
	 *
	 * STEP 1: Instead of using 'any', use all relevant post types except for
	 *         attachment, revision, and nav_menu_item.
	 * STEP 2: Set all other variables of query_str to a default value to use.
	 * STEP 3: Return query string argument array.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @return array Query Args.
	 */
	private function set_query_init() {
		// STEP 1.
		$post_type_list = get_post_types( '', 'names' );
		$skip_post_types = array( 'attachment', 'revision', 'nav_menu_item' );
		foreach ( $skip_post_types as $value ) {
			unset( $post_type_list[ $value ] );
		}
		unset( $value );
		unset( $skip_post_types );

		// STEP 2.
		$arg = array(
			'author'              => '',
			'tax_query'           => array(),
			//'post_parent'        => 0,
			'post__in'            => array(),
			// DO NOT USE IN WP_Query - there will be a manual function at the end.
			'post__not_in'        => array(),
			'post_type'           => $post_type_list,
			'post_status'         => array(
				'publish',
			),
			'nopaging'            => false,
			'order'               => 'DESC',
			'orderby'             => 'date',
			'ignore_sticky_posts' => false,
			'perm'                => 'readable',
		);

		// STEP 3.
		return $arg;
	}

	/**
	 * Set Query Args.
	 *
	 * Used as a repeating function to set multiple query strings.
	 *
	 * STEP 1: Clone the param object (REQUIRED).
	 * STEP 2: Set defaults for query_str.
	 * STEP 3: Set boolean to determine if the function is repeating.
	 * STEP 4: If a post types exists, then do **steps 5-7**.
	 * STEP 5: Go through post parents, if any, and if more than one exists in
	 *         the post type, then repeat this function with 1 less.
	 * STEP 6: Set query_str's tax_query array variables.
	 * STEP 7: If more post types exist, then repeat this function.
	 * STEP 8: If no post types exist, then cycle through any post parents, and
	 *         if more than one exists, repeat this function.
	 * STEP 9: Add the rest of the base query_str values.
	 * STEP 10: If more both public and private visibility setting is enabled,
	 *          then duplicate the variable and change one to private. Otherwise
	 *          only change it if private is enabled.
	 * STEP 11: Return all collected query strings.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see $query_args
	 * @link https://gist.github.com/EkoJr/7352549
	 *
	 * @param object $preset APL preset post list objects.
	 * @return array Multi-dimensional query_str array.
	 */
	private function set_query( $preset ) {
		/*
		 * Found out that relying on the param to be seperate from the call
		 * stack produces the param to be the same object; acing like a pointer?
		 */
		// STEP 1.
		$preset = clone $preset;
		$preset->_postTax = clone $preset->_postTax;

		// Used for collecting and returning an array of $query_str; Multi-Dimensional.
		$query_str_arrays = array();

		// STEP 2.
		$query_str = $this->set_query_init();

		/*
		 * This is used to prevent repeating when the scope of the presetObj has
		 * already been finished. This is caused by post_parents that have
		 * post_types/taxonomies and causes the possibility of using set_query
		 * twice in one instance.
		 */
		// STEP 3.
		$set_query_used = false;

		// STEP 4.
		// POST_TYPES & TAXONOMIES + POST_PARENTS.
		// DON'T USE A FOR LOOP for post_types.
		$post_type_key = key( (array) $preset->_postTax );
		if ( null !== $post_type_key ) {
			$query_str['post_type'] = array();

			// STEP 5.
			// Use this type of FOR loop in order to use the index as a counter.
			$parent_count = count( $preset->_postParents );
			for ( $i = 0; $i < $parent_count; $i++ ) {
				if ( get_post_type( $preset->_postParents[ $i ] ) === $post_type_key ) {
					$query_str['post_parent'] = array_shift( $preset->_postParents );

					// Cycle through rest of the array to check to see if there
					// is another match before deciding to repeat this function.
					// Index ($i) needs to cap inside to serve as a break.
					$parent_count2 = count( $preset->_postParents );
					for ( $i; $i < $parent_count2; $i++ ) {
						if ( get_post_type( $preset->_postParents[ $i ] ) === $post_type_key ) {
							$query_str_arrays = array_merge( $query_str_arrays, $this->set_query( $preset ) );
							$set_query_used = true;
							$i = count( $preset->_postParents );
						}
					}
				}
			}

			// STEP 6.
			$tax_operator = 'OR';
			foreach ( $preset->_postTax->$post_type_key->taxonomies as $taxonomy_slug => $taxonomy_value ) {
				if ( true === $taxonomy_value->require_taxonomy ) {
					$tax_operator = 'AND';
				}
				$term_operator = 'IN';
				if ( true === $taxonomy_value->require_terms ) {
					$term_operator = 'AND';
				}
				// For the Any/All setting.
				if ( in_array( 0, $taxonomy_value->terms ) ) {
					// Does this need all terms added or leave empty.
					// Note: An empty array with a -1 list amount returns zero posts.
					//$taxonomy_value->terms = array();
					$term_args = array(
						'taxonomy' => $taxonomy_slug,
						'fields' => 'ids',
					);
					$taxonomy_value->terms = get_terms( $term_args );

				}
				//Set query string's tax_query
				$query_str['tax_query'][] = array(
					'taxonomy'          => $taxonomy_slug,
					'field'             => 'id',
					'terms'             => $taxonomy_value->terms,
					'include_children'  => false,
					'operator'          => $term_operator,
				);
			}
			$query_str['tax_query']['relation'] = $tax_operator;

			$query_str['post_type'] = array( $post_type_key );
			unset( $preset->_postTax->$post_type_key );

			// STEP 7 - If more post types exist, then repeat this function.
			if ( false === count( (array) $preset->_postTax ) > 0 && $set_query_used ) {
				$query_str_arrays = array_merge( $query_str_arrays, $this->set_query( $preset ) );
			}
		} elseif ( count( $preset->_postParents ) > 0 ) {
			// STEP 8.
			// POST PARENTS (w/o post_type/Tax).

			// If a Post Parent arg is already set, then repeat this query. This
			// is just in case it happens to be set and to prevent overwriting.
			$parent_count = count( $preset->_postParents );
			if ( ! empty( $query_str['post_parent'] ) ) {
				$query_str_arrays = array_merge( $query_str_arrays, $this->set_query( $preset ) );
			} elseif ( 1 < $parent_count ) {
				// Set and continues adding the rest of the page parents, if any.
				$query_str['post_parent'] = intval( array_shift( $preset->_postParents ) );
				$query_str['post_type']   = get_post_type( $query_str['post_parent'] );
				$query_str_arrays = array_merge( $query_str_arrays, $this->set_query( $preset ) );
			} else {
				$query_str['post_parent'] = intval( array_shift( $preset->_postParents ) );
				$query_str['post_type']   = get_post_type( $query_str['post_parent'] );
			}
		}// End if().

		// STEP 9.
		$query_str = array_merge( $query_str, $this->set_query_base_val( $preset ) );

		// Step 10.
		// Otherwise leave alone.
		if ( count( (array) $preset->_postVisibility ) === 2 ) {
			$query_str_arrays[] = $query_str;
			$query_str['post_status'][] = 'private';
		} elseif ( 'private' === $preset->_postVisibility[0] ) {
			$query_str['post_status'][] = 'private';
		}

		// STEP 11.
		$query_str_arrays[] = $query_str;

		return $query_str_arrays;
	}

	/**
	 * Summary.
	 *
	 * Sets the base query_str values.
	 *
	 * STEP 1: Add author filter settings.
	 * STEP 2: Add post status filter settings.
	 * STEP 3: Add order by settings.
	 * STEP 4: Add user's read perm filter.
	 * STEP 5: Add or Remove Post IDs.
	 * STEP 6: Add whether to ignore sticky settings.
	 * STEP 7: Set List Amount.
	 * STEP 8: Return query_str's base variable values.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @param object $preset APL preset post list objects.
	 * @return array Base values for query_str.
	 */
	private function set_query_base_val( $preset ) {
		// INIT.
		$arg = array();

		/* **** AUTHOR FILTER **** */
		// STEP 1.
		if ( 'none' !== $preset->_postAuthorOperator && ! empty( $preset->_postAuthorIDs ) ) {
			$author_filter = '';
			$author_operator = '';
			if ( 'exclude' === $preset->_postAuthorOperator ) {
				$author_operator = '-';
			}
			foreach ( $preset->_postAuthorIDs as $i => $author_id ) {
				$author_filter .= $author_operator . $author_id;
				$author_count = count( $preset->_postAuthorIDs ) - 1;
				// Adds a comma if there is more IDs.
				if ( $i < $author_count ) {
					$author_filter .= ',';
				}
			}
			$arg['author'] = $author_filter;
		}

		/* **** POST STATUS **** */
		// STEP 2.
		if ( ! empty( $preset->_postStatus ) ) {
			$post_status_filter = array();
			foreach ( $preset->_postStatus as $value ) {
				$post_status_filter[] = $value;
			}
			$arg['post_status'] = $post_status_filter;
		}

		/* **** Order/Sort **** */
		// STEP 3.
		if ( ! empty( $preset->_listOrder ) ) {
			$arg['order'] = $preset->_listOrder;
		}
		if ( ! empty( $preset->_listOrderBy ) ) {
			$arg['orderby'] = $preset->_listOrderBy;
		}

		/* ****  Permissions **** */
		// STEP 4.
		if ( ! empty( $preset->_userPerm ) ) {
			$arg['perm'] = $preset->_userPerm;
		}

		/*
		 * When adding posts, check if there are any invalid post IDs, and then
		 * filter out any duplicates. This prevents any conflicts that may occur
		 * with some of the dynamic settings/input.
		 */
		// STEP 5.
		$arg['post__not_in'] = array();
		if ( ! empty( $preset->_listExcludePosts ) ) {
			foreach ( $preset->_listExcludePosts as $i => $post_id ) {
				if ( 0 !== $post_id && ! empty( $post_id ) ) {
					$arg['post__not_in'][] = $post_id;
				}
			}
			$arg['post__not_in'] = array_unique( $arg['post__not_in'] );
		}

		/* **** Ignore Stickies **** */
		// STEP 6.
		// Default WP: False.
		if ( ! empty( $preset->_listIgnoreSticky ) ) {
			$arg['ignore_sticky_posts'] = $preset->_listIgnoreSticky;
		}

		/* **** List Amount **** */
		// STEP 7.
		/*
		 * IF set to -1 (unlimited), preserve the setting.
		 *
		 * Otherwise, add List Count/Amount plus the amount of excluded posts
		 * in order to balance out the total number of posts remaining.
		 */
		if ( isset( $preset->_listCount ) ) {
			if ( -1 === $preset->_listCount ) {
				$arg['posts_per_page'] = $preset->_listCount;
			} else {
				$arg['posts_per_page'] = $preset->_listCount + count( $arg['post__not_in'] );
			}
		}

		// STEP 8.
		return $arg;
	}

	/**
	 * Summary.
	 *
	 * Merges any simular query strings.
	 *
	 * STEP 1: Go through string and match post parents with the same post status
	 *         or tax query.
	 * STEP 2: Return (modified) query_str_array.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param array $query_str_array Multi-dimensional query_str array.
	 * @return $query_str_array Multi-dimensional query_str array.
	 */
	private function query_str_consolidate( $query_str_array ) {
		// STEP 1.
		$query_count = count( $query_str_array );
		for ( $i = 0; $i < $query_count; $i++ ) {
			if ( empty( $query_str_array[ $i ]['post_parent'] ) ) {
				$query_count = count( $query_str_array );
				for ( $j = $i + 1; $j < $query_count; $j++ ) {
					// IF there isn't a post_parent that would void a merge and
					// IF both query_str does have or not have private post_status.
					if ( empty( $query_str_array[ $j ]['post_parent'] ) &&
						 in_array( 'private', $query_str_array[ $i ]['post_status'] ) === in_array( 'private', $query_str_array[ $j ]['post_status'] ) &&
						 $this->tax_query_match( $query_str_array[ $i ]['tax_query'], $query_str_array[ $j ]['tax_query'] ) ) {

						$query_str_array[ $i ] = $this->query_str_merge( $query_str_array[ $i ], $query_str_array[ $j ] );
						unset( $query_str_array[ $j ] );
						$query_str_array = array_values( $query_str_array );
						$i--;
					}
				}
			}
		}

		// STEP.
		return $query_str_array;
	}

	/**
	 * Merge Query Strings.
	 *
	 * Merges two query_str arrays.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @param array $query_str1 Query string values.
	 * @param array $query_str2 Query string values.
	 * @return array Query string values.
	 */
	private function query_str_merge( $query_str1, $query_str2 ) {
		$query_str1['post_type'] = array_merge( $query_str1['post_type'], $query_str2['post_type'] );
		return $query_str1;
	}

	/**
	 * Match Tax Query Args.
	 *
	 * Checks to see if there is a 100% relation.
	 *
	 * STEP 1: Check and return false if taxomonies do not have 100% relation.
	 * STEP 2: Return true if there is a 100% relation.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param array $tax_query1 The query string's tax query.
	 * @param array $tax_query2 The query string's tax query.
	 * @return boolean Similar Tax_Querys.
	 */
	private function tax_query_match( $tax_query1, $tax_query2 ) {
		// STEP 1.
		if ( $tax_query1['relation'] === $tax_query2['relation'] ) {
			$tax_query_count1 = count( $tax_query1 ) - 1 ;
			for ( $i = 0; $i < $tax_query_count; $i++ ) {
				$tax_match_found = false;
				$tax_query_count2 = count( $tax_query2 ) - 1 ;
				for ( $j = 0; $j < $tax_query_count2; $j++ ) {
					// Would have included the next IF statement if the 2 weren't
					// required to have and not have an else return false.
					if ( $tax_query1[ $i ]['taxonomy'] === $tax_query2[ $j ]['taxonomy'] ) {
						$tax_match_found = true;
						if ( $tax_query1[ $i ]['operator'] === $tax_query2[ $j ]['operator'] ) {
							foreach ( $tax_query1[ $i ]['terms'] as $key => $value ) {
								if ( ! in_array( $value, $tax_query2[ $j ]['terms'] ) ) {
									return false;
								}
							}
						} else {
							return false;
						}
					}
				}
				if ( ! $tax_match_found ) {
					return false;
				}
			}
		} else {
			return false;
		}
		// STEP 2.
		return true;
	}

	/**
	 * Set Preset Page Values.
	 *
	 * Adds the current global post's values if dynamic settings are checked.
	 *
	 * STEP 1: Get the current $post ID.
	 * STEP 2: If excluding current post/page is checked, then add post_ID.
	 * STEP 3: If the post parent dynamic/'current page' option is checked.
	 *         Then see if the post is hierarchical and add it to post parents
	 *         array.
	 * STEP 4: If any include/dynamic taxonomy terms are selected, then add
	 *         the (global) post's values.
	 * STEP 5: Return (modified) presetObj.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param object $preset APL preset post list object.
	 * @return object APL's (modified) preset post list object.
	 */
	private function set_presetObj_page_vals( $preset ) {
		// STEP 1.
		$post_ID = get_the_ID();

		// STEP 2.
		if ( true === $preset->_listExcludeCurrent && ! empty( $post_ID ) ) {
			$preset->_listExcludePosts[] = $post_ID;
		}

		/* ****************************************************************** */
		/* * PAGE PARENTS *************************************************** */
		/* ****************************************************************** */
		// STEP 3.
		$post_post_type = get_post_type( $post_ID );
		$post_hierarchical = is_post_type_hierarchical( $post_post_type );
		foreach ( $preset->_postParents as $key => $value ) {
			// If dynamic/current post is enabled, zero (0).
			if ( 0 === intval( $value ) ) {
				// If the post is a valid page parent (hierarchical), then
				// replace 0 with page ID.
				if ( $post_hierarchical && ! empty( $post_ID ) ) {
					// Replace Current Page Parent indicator with the (real)
					// page ID.
					$preset->_postParents[ $key ] = $post_ID;
				} else {
					// Otherwise remove the invalid entry (value 0).
					unset( $preset->_postParents[ $key ] );
					$preset->_postParents = array_values( $preset->_postParents );
				}
			}
		}
		$preset->_postParents = array_values( array_unique( $preset->_postParents ) );

		/* ****************************************************************** */
		/* * POST TYPE & TAXONOMIES -> TERMS ******************************** */
		/* ****************************************************************** */
		// STEP 4.
		$post_taxonomies = get_post_taxonomies( $post_ID );
		$args_post_terms = array(
			'orderby'  => 'term_id',
			'order'    => 'ASC',
			'fields'   => 'ids',
		);

		foreach ( $preset->_postTax as $preset_post_type => $preset_pt_value ) {
			if ( $post_post_type === $preset_post_type ) {
				foreach ( $preset_pt_value->taxonomies as $preset_taxonomy => $preset_tax_value ) {
					if ( true === $preset_tax_value->include_terms ) {
						foreach ( $post_taxonomies as $post_taxonomy_value ) {
							if ( $preset_taxonomy === $post_taxonomy_value && ! empty( $post_ID ) ) {
								$post_taxonomy_terms = wp_get_object_terms(
									$post_ID,
									$post_taxonomy_value,
									$args_post_terms
								);
								$preset_tax_value->terms = array_merge( $preset_tax_value->terms, (array) $post_taxonomy_terms );
								$preset_tax_value->terms = array_unique( $preset_tax_value->terms );
							}
						}
					}
				}
			}
		}

		// STEP 5.
		return $preset;
	}

	/**
	 * APL Query WP.
	 *
	 * Queries multiple instances of this function if there is more than one
	 * query_str.
	 *
	 * STEP 1: If this is NOT the first and last instance of this function.
	 *         Then repeat this function if more queries are present, and
	 *         query/collect the posts IDs.
	 * STEP 2: FINAL Query and order the post IDs collected. Return results.
	 *
	 * @since 0.3.b8
	 *
	 * @see Function/method/class relied on
	 * @link https://gist.github.com/EkoJr/7352549
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param array   $query_str_array Multi-dimensional query_str array.
	 * @param boolean $repeated This function repeated.
	 * @return mixed WP_Query class if unrepeated, otherwise array of post_IDs.
	 */
	public function query_wp( $query_str_array, $repeated = false ) {
		$post_in_IDs = array();
		$post_not_in_IDs = array();
		$final_query_str = array();

		/*
		 * Normally a recursive function will have an exit statement setup first,
		 * however, it needs to check if it is a repeated instance first before
		 * moving on to the first/last instance.
		 */
		// TODO Create function for the repeated query_wp function.
		// STEP 1.
		if ( true === $repeated ) {
			// STEP.
			// Copy the first Query String Array, and delete it, then shift
			// any additional arrays.
			$query_str = array_shift( $query_str_array );

			// If more Query Strings Arrays exist, then repeat this function.
			// When returned, merge post ids for pre-final query.
			if ( ! empty( $query_str_array ) ) {
				$post_in_IDs = array_merge( $this->query_wp( $query_str_array, true ), $post_in_IDs );
			}

			// STEP.
			// Since post__in and post__not_in don't mix at all. The 2 variables
			// are stored seperately.
			// TODO Create function for Post Include/Exclude.
			if ( ! empty( $query_str['post__not_in'] ) ) {
				$post_not_in_IDs = $query_str['post__not_in'];

			}
			unset( $query_str['post__not_in'] );
			if ( ! empty( $query_str['post__in'] ) ) {
				$post_in_IDs = array_merge( $post_in_IDs, $query_str['post__in'] );
			}
			unset( $query_str['post__in'] );

			// STEP.
			// If Posts Per Page is set to -1/Unlimited, then set nopaging to true.
			if ( -1 === $query_str['posts_per_page'] ) {
				$query_str['nopaging'] = true;
			}
			// STEP.
			// Sets the query string to just query IDs.
			$query_str['fields'] = 'ids';
			$Query_Obj = new WP_Query( $query_str );

			// STEP.
			// Collect an array of Post IDs.
			$post_IDs = array();
			foreach ( $Query_Obj->posts as $i => $post_ID ) {
				$post_IDs[] = intval( $post_ID );
			}
			$post_IDs = array_merge( $post_IDs, $post_in_IDs );

			// STEP.
			wp_reset_postdata();
			// STEP.
			return $post_IDs;

		} else {
			// STEP 2.
			// Repeated === false
			/*
			 * This is the Initial and Final Query. This is used to collect IDs first
			 * with 1 or more query_str (that couldn't be consolidated/merged), and
			 * then do one last query here to return. This allows sorting with other
			 * custom post types that couldn't be queried together, and allows
			 * compatability with posts_in & posts_not_in.
			 */
			// STEP.
			$post_in_IDs = array_merge( $this->query_wp( $query_str_array, true ) );
			// STEP.
			$query_str = array_shift( $query_str_array );

			// STEP.
			// Filter out excluded posts.
			foreach ( $query_str['post__not_in'] as $post_not_value ) {
				foreach ( $post_in_IDs as $key => $post_in_value ) {
					if ( $post_in_value === $post_not_value ) {
						unset( $post_in_IDs[ $key ] );
					}
				}
			}
			$post_in_IDs = array_merge( $post_in_IDs );

			// STEP.
			if ( empty( $post_in_IDs ) ) {
				$post_in_IDs[] = 0;
			}

			// STEP.
			// Set FINAL query_str with post IDs.
			$final_query_str['post__in']             = $post_in_IDs;
			$final_query_str['post_type']            = 'any';
			$final_query_str['nopaging']             = false;
			$final_query_str['order']                = $query_str['order'];
			$final_query_str['orderby']              = $query_str['orderby'];
			$final_query_str['ignore_sticky_posts']  = $query_str['ignore_sticky_posts'];

			if ( -1 === $query_str['posts_per_page'] ) {
				$final_query_str['nopaging'] = true;
			}
			$final_query_str['posts_per_page'] = $query_str['posts_per_page'] - count( $query_str['post__not_in'] );

			// Get FINAL Query Object.
			$final_Query_Obj = new WP_Query( $final_query_str );

			return $final_Query_Obj;
		}// End if().
	}

	/**
	 * Post Not in List.
	 *
	 * Removes any posts excluded from the list.
	 *
	 * STEP 1: Go though posts.
	 * STEP 2: If a post matches one of the excluded IDs, then remove the post
	 *         from both post, posts, and post counts in WP_Query class.
	 * STEP 3: Return WP_Query class.
	 *
	 * @since 0.3.b8
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @param object $Query_Obj WP_Query class.
	 * @param array  $post_not_in_IDs Posts to exclude/remove.
	 * @return object WP_Query class (modified) object.
	 */
	private function post__not_in( $Query_Obj, $post_not_in_IDs ) {
		// STEP 1.
		foreach ( $Query_Obj->posts as $i => $post ) {
			foreach ( $post_not_in_IDs as $post_not_ID ) {
				// STEP 2.
				if ( $post->ID === $post_not_ID ) {
					unset( $Query_Obj->posts[ $i ] );
					$Query_Obj->post_count -= 1;
					$Query_Obj->found_posts -= 1;
					if ( $Query_Obj->post->ID === $post_not_ID ) {
						$Query_Obj->post = $Query_Obj->posts[ $i + 1 ];
					}
				}
			}
		}
		$Query_Obj->posts = array_values( $Query_Obj->posts );

		// STEP 3.
		return $Query_Obj;
	}
}
