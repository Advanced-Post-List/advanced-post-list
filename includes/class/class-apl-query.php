<?php
/**
 * APL Query Class
 *
 * Query API to WP_Query.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.3.0
 */

/**
 * APL Query
 *
 * Query Object for extended functionality with WP_Query. Enables additional
 * concepts that WP doesn't provide.
 *
 * @since 0.3.0
 * @since 0.3.b8 - Fixed nested code.
 * @since 0.4.0  - Changed to use APL_Post_List.
 */
class APL_Query {

	/**
	 * Stores error message when error occurs
	 *
	 * @todo Possibly Remove, and just use WP_Query API.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $_posts;

	/**
	 * Query_Args Array
	 *
	 * @since 0.3.b7
	 * @since 0.4.0 - Changed to snake_case.
	 * @var array
	 */
	public $query_args_arr = array();

	/**
	 * Constructor for APL Query
	 *
	 * Constructor for APL's Query. Adds post/page dynamics, sets the multi-
	 * dimensional query array, and reduces the amount of query strings to be used.
	 *
	 * STEP 1: Add current post dynamics to the Post List.
	 * STEP 2: Set the query strings from the APL_Post_List.
	 * STEP 3: Merge any simular queries to lessen the amount of queries.
	 * STEP 4: Make any additions/duplications for certain enhancements.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Change to new object method structure.
	 *
	 * @param APL_Post_List $apl_post_list APL Preset Post List Object.
	 */
	public function __construct( $apl_post_list ) {
		$apl_post_list        = $this->add_list_dynamics( $apl_post_list );
		$this->query_args_arr = $this->set_query_args_arr( $apl_post_list );
		$this->query_args_arr = $this->query_arg_consolidate( $this->query_args_arr );
		$this->query_args_arr = $this->query_args_accommodate( $this->query_args_arr );
	}

	/**
	 * Add Dynamic values to Post List
	 *
	 * Adds the current global post values for any dynamic settings checked.
	 *
	 * @ignore
	 * @since 0.3.b8
	 * @since 0.4.0 - Changed to use APL_Post_List object.
	 * @access private
	 *
	 * @param APL_Post_List $apl_post_list APL Post List object.
	 * @return APL_Post_List Modified post list object.
	 */
	private function add_list_dynamics( $apl_post_list ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $apl_post_list;
		}

		// EXCLUDE CURRENT POST.
		if ( $apl_post_list->pl_exclude_current ) {
			$apl_post_list->post__not_in[] = $post_id;
		}

		$current_post_type  = get_post_type( $post_id );
		$current_taxonomies = get_post_taxonomies( $post_id );

		$terms_args = array(
			'orderby' => 'term_id',
			'order'   => 'ASC',
			'fields'  => 'ids',
		);

		foreach ( $apl_post_list->post_type as $k1_index => $v1_pt_arr ) {
			if ( is_array( $v1_pt_arr ) ) {
				// POST TYPES.
				foreach ( $v1_pt_arr as $k2_index => $v2_pt_slug ) {
					// Current Post Type.
					// PAGE PARENT.
					if ( $current_post_type === $v2_pt_slug ) {
						if ( isset( $apl_post_list->post_parent_dynamic[ $v2_pt_slug ] ) ) {
							if ( $apl_post_list->post_parent_dynamic[ $v2_pt_slug ] ) {
								// Add current Page as Parent and prevent doubles.
								$apl_post_list->post_parent__in[ $v2_pt_slug ][] = $post_id;
								$apl_post_list->post_parent__in[ $v2_pt_slug ]   = array_values( array_unique( $apl_post_list->post_parent__in[ $v2_pt_slug ] ) );
							}
						}
					}
					// TAXONOMIES.
					if ( isset( $apl_post_list->tax_query[ $v2_pt_slug ] ) ) {
						foreach ( $apl_post_list->tax_query[ $v2_pt_slug ] as $k3_tax_key => &$v3_tax_arr ) {

							if ( 'relation' !== $k3_tax_key ) {
								if ( in_array( $v3_tax_arr['taxonomy'], $current_taxonomies, true ) ) {
									if ( $v3_tax_arr['apl_terms_dynamic'] ) {
										// Add Terms.
										$current_taxonomy_terms = wp_get_post_terms(
											$post_id,
											$v3_tax_arr['taxonomy'],
											array(
												'fields' => 'ids',
											)
										);
										if ( is_wp_error( $current_taxonomy_terms ) ) {
											echo '<div id="message" class="error"><p>' . esc_html( $current_taxonomy_terms->get_error_message() ) . '</p></div>';
											return $apl_post_list;
										}

										foreach ( $current_taxonomy_terms as $current_term_id ) {
											//$apl_post_list->tax_query[ $v2_pt_slug ]['terms'][] = $current_term_id;
											$v3_tax_arr['terms'][] = $current_term_id;
										}

										$apl_post_list->tax_query[ $v2_pt_slug ][ $k3_tax_key ]['terms'] = array_values( array_unique( $v3_tax_arr['terms'] ) );
									}
								}
							}
						}
					}
				}// End Foreach( POST TYPE).
			} else { // ANY / ALL.
				// TAXONOMIES.
				if ( isset( $apl_post_list->tax_query[ $v1_pt_arr ] ) ) {
					foreach ( $apl_post_list->tax_query[ $v1_pt_arr ] as $k2_tax_key => &$v2_tax_arr ) {

						if ( 'relation' !== $k2_tax_key ) {
							if ( in_array( $v2_tax_arr['taxonomy'], $current_taxonomies, true ) ) {
								if ( $v2_tax_arr['apl_terms_dynamic'] ) {
									// Add Terms.
									$current_taxonomy_terms = wp_get_post_terms(
										$post_id,
										$v2_tax_arr['taxonomy'],
										array(
											'fields' => 'ids',
										)
									);
									if ( is_wp_error( $current_taxonomy_terms ) ) {
										echo '<div id="message" class="error"><p>' . esc_html( $current_taxonomy_terms->get_error_message() ) . '</p></div>';
									}

									foreach ( $current_taxonomy_terms as $current_term_id ) {
										$v2_tax_arr['terms'][] = $current_term_id;
									}

									$apl_post_list->tax_query[ $v1_pt_arr ][ $k2_tax_key ]['terms'] = array_values( array_unique( $v2_tax_arr['terms'] ) );
								}
							}
						}
					}
				}
				// Break Post Type Loop (Just in case).
				break;
			}// End if().
		}// End foreach().

		return $apl_post_list;
	}

	/**
	 * Set Query Args
	 *
	 * Sets multiple query_args in query_args_arr.
	 *
	 * @ignore
	 * @since 0.3.b8
	 * @since 0.4.0 - Changed to use APL_Post_List structure.
	 * @access private
	 *
	 * @see $query_args for WP_Query.
	 * @link https://gist.github.com/luetkemj/2023628
	 *
	 * @param APL_Post_List $apl_post_list APL Post List object.
	 * @return array Multiple query_args
	 */
	private function set_query_args_arr( $apl_post_list ) {
		$rtn_query_args_arr = array();

		foreach ( $apl_post_list->post_type as $v1_pt_arr ) {
			$tmp_query_args = array();

			// If there is an array of post types, then loop through and add.
			if ( is_array( $v1_pt_arr ) ) {
				foreach ( $v1_pt_arr as $v2_pt_slug ) {
					$tmp_query_args['post_type'] = array( $v2_pt_slug );

					//$tmp_query_args['tax_query'] = $apl_post_list->tax_query[ $v2_pt_slug ];
					$tmp_tax_query = $apl_post_list->tax_query[ $v2_pt_slug ];

					if ( ! empty( $tmp_tax_query ) ) {
						$tmp_query_args['tax_query']['relation'] = $tmp_tax_query['relation'];
						unset( $tmp_tax_query['relation'] );

						foreach ( $tmp_tax_query as $tax_arr ) {
							// Any / All.
							if ( isset( $tax_arr['terms'][0] ) && 0 === $tax_arr['terms'][0] ) {
								$terms_args = array(
									'taxonomy' => $tax_arr['taxonomy'],
									'fields'   => 'ids',
								);

								$tax_arr['terms'] = get_terms( $terms_args );

								$tmp_query_args['tax_query'][] = $tax_arr;
							} else {
								$tmp_query_args['tax_query'][] = $tax_arr;
							}
						}
					}

					// Page Parents.
					if ( isset( $apl_post_list->post_parent__in[ $v2_pt_slug ] ) ) {
						$tmp_query_args['post_parent__in'] = $apl_post_list->post_parent__in[ $v2_pt_slug ];
					}
				}
			} else { // ANY.
				$tmp_query_args['post_type'] = 'any';
				//$tmp_query_args['tax_query'] = $apl_post_list->tax_query['any'];

				if ( ! empty( $apl_post_list->tax_query[ $v1_pt_arr ] ) ) {
					$tmp_tax_query                           = $apl_post_list->tax_query[ $v1_pt_arr ];
					$tmp_query_args['tax_query']['relation'] = $tmp_tax_query['relation'];
					unset( $tmp_tax_query['relation'] );

					foreach ( $tmp_tax_query as $tax_arr ) {
						// Any / All.
						if ( 0 === $tax_arr['terms'][0] ) {
							$terms_args = array(
								'taxonomy' => $tax_arr['taxonomy'],
								'fields'   => 'ids',
							);

							$tax_arr['terms'] = get_terms( $terms_args );

							$tmp_query_args['tax_query'][] = $tax_arr;
						} else {
							$tmp_query_args['tax_query'][] = $tax_arr;
						}
					}
				}

				// Post Parents is empty in 'Any'
				//$tmp_query_args['post_parent__in'] = $apl_post_list->post_parent__in['any'];
			}// End if().

			// General Filter.
			$tmp_query_args['posts_per_page'] = $apl_post_list->posts_per_page;
			$tmp_query_args['offset']         = $apl_post_list->offset;
			$tmp_query_args['orderby']        = $apl_post_list->order_by;
			$tmp_query_args['order']          = $apl_post_list->order;
			// Handle post_status in accommodate method.
			$tmp_query_args['post_status'] = $apl_post_list->post_status;
			if ( 'none' !== $apl_post_list->perm ) {
				$tmp_query_args['perm'] = $apl_post_list->perm;
			}
			if ( 'none' !== $apl_post_list->author__bool ) {
				if ( 'in' === $apl_post_list->author__in ) {
					$tmp_query_args['author__in'] = $apl_post_list->author__in;
				} elseif ( 'not_in' === $apl_post_list->author__in ) {
					$tmp_query_args['author__not_in'] = $apl_post_list->author__in;
				}
			}
			$tmp_query_args['ignore_sticky_posts'] = $apl_post_list->ignore_sticky_posts;
			$tmp_query_args['post__not_in']        = $apl_post_list->post__not_in;

			$rtn_query_args_arr[] = $tmp_query_args;
		}// End foreach().

		return $rtn_query_args_arr;
	}

	/**
	 * Query Args Consolidation
	 *
	 * Merges any simular query strings.
	 *
	 * STEP 1: Go through string and match post parents with the same post status
	 *         or tax query.
	 * STEP 2: Return (modified) query_str_array.
	 *
	 * @ignore
	 * @since 0.3.b8
	 * @access private
	 *
	 * @param array $arg_arr Multi-dimensional query_str array.
	 * @return array Multi-dimensional query_str array.
	 */
	private function query_arg_consolidate( $arg_arr ) {
		$query_count = count( $arg_arr );
		for ( $i = 0; $i < $query_count; $i++ ) {
			if ( empty( $arg_arr[ $i ]['post_parent'] ) ) {
				$query_count = count( $arg_arr );
				for ( $j = $i + 1; $j < $query_count; $j++ ) {
					// IF there is a post_parents; which would conflict.
					// IF both query_arg's tax_query match.
					if ( isset( $arg_arr[ $i ]['post_parent'] ) && empty( $arg_arr[ $i ]['post_parent'] ) &&
					$this->tax_query_match( $arg_arr[ $i ]['tax_query'], $arg_arr[ $j ]['tax_query'] ) ) {

						$arg_arr[ $i ]['post_type'][] = $arg_arr['post_type'][0];

						unset( $arg_arr[ $j ] );
						$arg_arr = array_values( $arg_arr );

						// Set $i back 1 to do next $j properly.
						$i--;
					}
				}
			}
		}

		return $arg_arr;
	}

	/**
	 * Match Tax Query Args
	 *
	 * Checks to see if there is a 100% relation.
	 *
	 * STEP 1: Check and return false if taxomonies do not have 100% relation.
	 * STEP 2: Return true if there is a 100% relation.
	 *
	 * @ignore
	 * @since 0.3.b8
	 * @access private
	 *
	 * @param array $tax_query1 The query string's tax query.
	 * @param array $tax_query2 The query string's tax query.
	 * @return boolean Similar Tax_Querys.
	 */
	private function tax_query_match( $tax_query1, $tax_query2 ) {
		// STEP 1.
		if ( $tax_query1['relation'] === $tax_query2['relation'] ) {
			$tax_query_count1 = count( $tax_query1 ) - 1;
			for ( $i = 0; $i < $tax_query_count1; $i++ ) {
				$tax_match_found  = false;
				$tax_query_count2 = count( $tax_query2 ) - 1;
				for ( $j = 0; $j < $tax_query_count2; $j++ ) {
					// Would have included the next IF statement if the 2 weren't
					// required to have and not have an else return false.
					if ( $tax_query1[ $i ]['taxonomy'] === $tax_query2[ $j ]['taxonomy'] ) {
						$tax_match_found = true;
						if ( $tax_query1[ $i ]['operator'] === $tax_query2[ $j ]['operator'] ) {
							foreach ( $tax_query1[ $i ]['terms'] as $key => $value ) {
								if ( ! in_array( $value, $tax_query2[ $j ]['terms'], true ) ) {
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
	 * Add/Change Query Args
	 *
	 * Modifications for certain enhancements after query array has been consolidated.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @private
	 *
	 * @param array $query_args_arr Old query argument array.
	 * @return array
	 */
	private function query_args_accommodate( $query_args_arr ) {
		$rtn_query_args_arr = array();
		foreach ( $query_args_arr as $v1_query_args ) {
			if ( is_array( $v1_query_args['post_status'] ) ) {
				$public  = array_search( 'public', $v1_query_args['post_status'], true );
				$private = array_search( 'private', $v1_query_args['post_status'], true );

				if ( $public && $private ) {
					unset( $v1_query_args['post_status'][ $public ] );
					$rtn_query_args_arr[] = $v1_query_args;
					unset( $v1_query_args['post_status'][ $private ] );
					$rtn_query_args_arr[] = $v1_query_args;
				} elseif ( $public ) {
					unset( $v1_query_args['post_status'][ $public ] );
					$rtn_query_args_arr[] = $v1_query_args;
				} else {
					$rtn_query_args_arr[] = $v1_query_args;
				}
			} elseif ( 'none' === $v1_query_args['post_status'] ) {
				// WP Default.
				$v1_query_args['post_status'] = array( 'publish' );
				$rtn_query_args_arr[]         = $v1_query_args;
			} elseif ( 'any' === $v1_query_args['post_status'] ) {
				$v1_query_args['post_status'] = 'any';
				$rtn_query_args_arr[]         = $v1_query_args;
			}
		}

		return $rtn_query_args_arr;
	}

	/**
	 * APL Query WP
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
		$post_in_IDs     = array();
		$post_not_in_IDs = array();

		/*
		 * Normally a recursive function will have an exit statement setup first,
		 * however, it needs to check if it is a repeated instance first before
		 * moving on to the first/last instance.
		 */
		// TODO Create function for the repeated query_wp function.
		// STEP 1.
		if ( true === $repeated ) {
			// Shift the args when repeating this method / function.
			// If more args exist, then repeat this function.
			// Merge returned post ids for pre-final query.
			$query_str = array_shift( $query_str_array );
			if ( ! empty( $query_str_array ) ) {
				$post_in_IDs = array_merge( $this->query_wp( $query_str_array, true ), $post_in_IDs );
			}

			// Since post__in and post__not_in don't mix at all. The 2 variables
			// are stored seperately.
			// TODO Create function for Post Include/Exclude.
			if ( ! empty( $query_str['post__not_in'] ) && 0 < $query_str['posts_per_page'] ) {
				// Removed at final.
				//$post_not_in_IDs = $query_str['post__not_in'];
				$query_str['posts_per_page'] += count( $query_str['post__not_in'] );
			}
			unset( $query_str['post__not_in'] );

			if ( ! empty( $query_str['post__in'] ) ) {
				$post_in_IDs = array_merge( $post_in_IDs, $query_str['post__in'] );
			}
			unset( $query_str['post__in'] );

			// If Posts Per Page is set to -1/Unlimited, then set nopaging to true.
			if ( -1 === $query_str['posts_per_page'] ) {
				$query_str['nopaging'] = true;
				$query_str['offset']   = 0;
			}

			// Sets the query string to just query IDs.
			$query_str['fields'] = 'ids';
			$query_obj = new WP_Query( $query_str );

			// Collect an array of Post IDs.
			$post_IDs = array();
			if ( ! empty( $query_obj->posts ) ) {
				foreach ( $query_obj->posts as $i => $post_ID ) {
					$post_IDs[] = intval( $post_ID );
				}
			}

			$post_IDs = array_merge( $post_IDs, $post_in_IDs );

			wp_reset_postdata();
			return $post_IDs;

		} else {
			// STEP 2.

			/*
			 * This is the Initial and Final Query. This is used to collect IDs first
			 * with 1 or more query_str (that couldn't be consolidated/merged), and
			 * then do one last query here to return. This allows sorting with other
			 * custom post types that couldn't be queried together, and allows
			 * compatability with posts_in & posts_not_in.
			 */

			$post_in_IDs = array_merge( $this->query_wp( $query_str_array, true ) );
			$query_str   = array_shift( $query_str_array );

			// Filter out excluded posts.
			foreach ( $query_str['post__not_in'] as $post_not_value ) {
				foreach ( $post_in_IDs as $key => $post_in_value ) {
					if ( $post_in_value === $post_not_value ) {
						unset( $post_in_IDs[ $key ] );
					}
				}
			}
			$post_in_IDs = array_merge( $post_in_IDs );

			// Prevent defaulting when there's no posts.
			if ( empty( $post_in_IDs ) ) {
				$post_in_IDs[] = 0;
			}

			// STEP.
			// Set FINAL query_str with post IDs.
			$final_query_str = array(
				'post__in'            => $post_in_IDs,
				'post_type'           => 'any',
				'posts_per_page'      => $query_str['posts_per_page'],
				'offset'              => $query_str['offset'],
				'nopaging'            => ( -1 === $query_str['posts_per_page'] ) ? true : false,
				'order'               => $query_str['order'],
				'orderby'             => $query_str['orderby'],
				'ignore_sticky_posts' => $query_str['ignore_sticky_posts'],
			);

			// Get FINAL Query Object.
			$final_query_obj = new WP_Query( $final_query_str );

			return $final_query_obj;
		}// End if().
	}
}
