<?php
/**
 * Filter Meta Box Template.
 *
 * Filter Meta Box for making new Post Lists.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

/*
 * PRIOR VARIABLES
 */
//var_dump( $post );
//var_dump( $metabox );
//var_dump( $apl_post_tax );
//var_dump( $apl_tax_terms );
//var_dump( $apl_display_post_types );
$apl_help_text = array(
	'post_types'              => esc_html__( 'Each (jQuiry UI) accordion contains a separate individual post type. The default post types built into WordPress are Post and Page. Any additional post types are dynamically added in the manner WordPress does. Please Note: Each post/page can have only one post type, which may explain why it has been divided by post types.', 'advanced-post-list' ),
	'taxonomy_tab'            => esc_html__( 'Each taxonomy is generally spit up in two sections, and divided into separate tabs. Hierarchies (categories) are located on the left, and non-hierarchies (tags) are located on the right.', 'advanced-post-list' ),
	'parent_page_tab'         => esc_html__( 'Each hierarchical post type has a Parent selector for selecting which children pages to display. You can add multiple Post Parents of dynamically add children pages according to the Current Page.', 'advanced-post-list' ),
	'taxonomy_multiselect'    => esc_html__( 'MULTISELECT Each taxonomy is generally spit up in two sections, and divided into separate tabs. Hierarchies (categories) are located on the left, and non-hierarchies (tags) are located on the right.', 'advanced-post-list' ) .
		'<br /><br />' .
		esc_html( 'Req. Taxonomies: If more than one ‘Require Taxonomy’ is checked and terms (or include) are selected, or "any", then each taxonomy must be required within the post type.', 'advanced-post-list' ),
	'require_terms'           => esc_html__( 'If selected, and more than one term is checked, then each term must be required within the CPT/taxonomy in order to be displayed in the post list.', 'advanced-post-list' ),
	'dynamic_terms'           => esc_html__( 'If selected, the post list preset will include any terms the current page/post has within the CTP/taxonomy.', 'advanced-post-list' ),
	'any_terms'               => esc_html__( 'When checked, any terms will be included within that CPT/taxonomy.', 'advanced-post-list' ),
	'list_amount'             => esc_html__( 'The numeric value of how many posts you want the post list to display. Negative one (-1) will display all the posts that are available after filtering.', 'advanced-post-list' ),
	'order_by'                => esc_html__( 'Choose which page properties to sort from. All of which are built in params used in WP_Query.', 'advanced-post-list' ),
	'authors'                 => esc_html__( 'Show or remove posts that were created by a certain author, or authors. You can only choose between adding or removing, not both.', 'advanced-post-list' ) .
		'<br /><br />' .
		esc_html__( 'Operator - Determines whether you want to include or exclude authors.', 'advanced-post-list' ) .
		'<br /><br />' .
		esc_html__( 'Author Names/IDs - Displays a list of authors the site currently has and is divided/grouped into separate role groups.', 'advanced-post-list' ),
	'post_status'             => esc_html__( 'Holds the settings to show which posts to display based on the user visibility and/or the page states. To which is only visible to the users with the necessary capabilities to view them.', 'advanced-post-list' ) .
		'<br /><br />' .
		esc_html__( '<b>Visibility</b> - Display posts as either Public, Private, or Both', 'advanced-post-list' ) .
		'<br /><br />' .
		esc_html__( '<b>Status States</b>: Choose from Published, Future, Pending Review, Draft, Auto-save, Inherit, and/or Trash.', 'advanced-post-list' ),
	'user_perms'              => esc_html__( 'Uses the user permission via. user capabilities to determine what posts to display in the post list to the visitor/user.', 'advanced-post-list' ),
	'offset'                  => esc_html__( 'Number of posts to skip/displace.', 'advanced-post-list' ) . '<br />' .
		                         esc_html__( 'NOTE: Offset is ignored when List Amount is set to -1 (show all posts).', 'advanced-post-list' ),
	'exclude_posts_by_id'     => esc_html__( 'Add post/page IDs, seperated by a comma (,), will prevent those posts from being added to the post list.', 'advanced-post-list' ),
	'enable_sticky_posts'     => esc_html__( 'Meant for the built-in post type (Posts) function. When checked, this will prevent sticky posts from always displaying at the top of the post list.', 'advanced-post-list' ),
	'exclude_current_post'    => esc_html__( 'When checked, the current post being viewed will be excluded from the post list.', 'advanced-post-list' ),
	'exclude_duplicate_posts' => esc_html__( 'In the "order that it is received", each preset post list being viewed will add the post IDs to a global exclude list built into APL. When checked, the preset post list will add the post IDs (listed at the time) to the exclude filter settings in WP_Query. This will remove any posts that have already been displayed to the user by the APL plugin.', 'advanced-post-list' ),
);
?>
<?php

/*
 * **** FUNCTIONS **************************************************************
 */

/**
 * Render Taxonomies.
 *
 * @since 0.4.0
 *
 * @param string        $post_type     Post Type slug.
 * @param string        $taxonomy      Taxonomy slug.
 * @param APL_Post_List $apl_post_list Current Post List object.
 * @param int           $term_parent   Parent Term ID.
 * @param int           $indent        Number of indents.
 * @return void
 */
function apl_render_categories( $post_type, $taxonomy, $apl_post_list, $term_parent = 0, $indent = 0 ) {
	$args = array(
		'taxonomy'   => $taxonomy,
		'parent'     => $term_parent,
		'hide_empty' => false,
	);
	$terms = get_terms( $args );

	$first_term = true;

	?>
	<?php $e_indent = 18 * $indent; ?>
	<ul style="margin-left: <?php echo absint( $e_indent ); ?>px;">
		<?php if ( 0 === $term_parent ) : ?>
			<?php
			$ele_tag = $post_type . '-' . $taxonomy . '-any';

			$term_checked = '';
			if ( ! empty( $apl_post_list->tax_query[ $post_type ] ) ) {
				foreach ( $apl_post_list->tax_query[ $post_type ] as $k1_pl_index => $v1_pl_tax_query ) {
					if ( 'relation' !== $k1_pl_index ) {
						if ( $taxonomy === $v1_pl_tax_query['taxonomy'] && empty( $v1_pl_tax_query['terms'] ) ) {
							$term_checked = 'checked="checked"';
						}
					}
				}
			}

			?>
			<li>
				<label for="term-<?php echo esc_attr( $ele_tag ); ?>">
					<input type="checkbox" id="term-<?php echo esc_attr( $ele_tag ); ?>" name="apl_term-<?php echo esc_attr( $ele_tag ); ?>" <?php echo esc_attr( $term_checked ); ?> >
					<?php esc_html_e( 'Any / All Terms', 'advanced-post-list' ); ?>
				</label>
			</li>
		<?php endif; ?>
		<?php if ( ! empty( $terms ) ) : ?>
			<?php foreach ( $terms as $k_index => $v_term_obj ) : ?>
				<?php
				$ele_tag = $post_type . '-' . $taxonomy . '-' . $v_term_obj->term_id;

				$term_checked = '';
				if ( isset( $apl_post_list->tax_query[ $post_type ] ) ) {
					foreach ( $apl_post_list->tax_query[ $post_type ] as $k2_pl_index => $v2_pl_tax_query ) {
						if ( 'relation' !== $k2_pl_index ) {
							if ( $taxonomy === $v2_pl_tax_query['taxonomy'] && in_array( $v_term_obj->term_id, $v2_pl_tax_query['terms'], true ) ) {
								$term_checked = 'checked="checked"';
							}
						}
					}
				}
				?>
				<li>
					<label for="term-<?php echo esc_attr( $ele_tag ); ?>">
						<input type="checkbox" id="term-<?php echo esc_attr( $ele_tag ); ?>" name="apl_term-<?php echo esc_attr( $ele_tag ); ?>" <?php echo esc_attr( $term_checked ); ?> >
						<?php echo esc_html( $v_term_obj->name ); ?>
					</label>
				</li>
				<?php apl_render_categories( $post_type, $taxonomy, $apl_post_list, $v_term_obj->term_id, ( $indent + 1 ) ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<?php
}

/**
 * Render Page Parent container.
 *
 * Description.
 *
 * @param string         $post_type      Post Type slug.
 * @param APL_Post_List  $apl_post_list  APL Post List object.
 * @param int            $page_parent    Parent Page(Post) ID.
 * @param int            $indent         Amount to Indent (Add dashes).
 */
function apl_render_page_parents( $post_type, $apl_post_list, $page_parent = 0, $indent = 0 ) {
	$args = array(
		'post_type'      => $post_type,
		'post_parent'    => $page_parent,
		'posts_per_page' => -1,
		'order'          => 'DESC',
		'orderby'        => 'name',
	);
	$query_pages = new WP_Query( $args );

	$indent_str = '';
	for ( $i = 0; $i < $indent; $i++ ) {
		$indent_str .= '—';
	}

	$page_dynamic_checked = '';
	if ( isset( $apl_post_list->post_parent_dynamic[ $post_type ] ) ) {
		if ( true === $apl_post_list->post_parent_dynamic[ $post_type ] ) {
			$page_dynamic_checked = 'checked="checked"';
		}
	}

	$apl_help_text_parent = esc_html__(
		'Adds the Current Page being displayed as a Parent Page, which will then add those child pages.',
		'advanced-post-list'
	);
	?>
	<?php if ( 0 === $page_parent ) : ?>
		<input type="checkbox" id="apl_parent_page_dynamic-<?php echo esc_attr( $post_type ); ?>" class="apl_parent_page_dynamic" name="apl_page_parent_dynamic-<?php echo esc_attr( $post_type ); ?>" <?php echo esc_attr( $page_dynamic_checked ); ?> >
		<label for="apl_parent_page_dynamic-<?php echo esc_attr( $post_type ); ?>"><b><?php esc_html_e( 'Dynamic Parent Page', 'advanced-post-list' ); ?></b></label>
		<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text_parent; ?>"></span>
		<br />
		<br />
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Add', 'advanced-post-list' ); ?></th>
					<th><?php esc_html_e( 'ID', 'advanced-post-list' ); ?></th>
					<th><?php esc_html_e( 'Page Title', 'advanced-post-list' ); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php endif; ?>
	<?php while ( $query_pages->have_posts() ) : ?>
		<?php
		$query_pages->the_post();
		$ele_tag = $post_type . '-' . $query_pages->post->ID;

		$page_checked = '';
		if ( isset( $apl_post_list->post_parent__in[ $post_type ] ) ) {
			if ( in_array( $query_pages->post->ID, $apl_post_list->post_parent__in[ $post_type ], true ) ) {
				$page_checked = 'checked="checked"';
			}
		}
		?>
				<tr class="alternate">
					<td class="apl-chk-td-post_type-parent">
						<input type="checkbox" class="apl-page-post_type-pages" id="apl-page-<?php echo esc_attr( $ele_tag ); ?>" name="apl_page_parent-<?php echo esc_attr( $ele_tag ); ?>" <?php echo esc_attr( $page_checked ); ?> />
					</td>
					<td>
						<?php echo esc_html( $query_pages->post->ID ); ?>
					</td>
					<td class="row-title">
						<label for="apl-page-<?php echo esc_attr( $ele_tag ); ?>"><?php echo esc_html( $indent_str . ' ' . $query_pages->post->post_title ); ?></label>
					</td>
				</tr>
				<?php
				apl_render_page_parents( $post_type, $apl_post_list, $query_pages->post->ID, ( $indent + 1 ) );
				?>
	<?php endwhile; ?>
	<?php if ( 0 === $page_parent ) : ?>
				<tfoot>
					<tr>
						<th><?php esc_html_e( 'Add', 'advanced-post-list' ); ?></th>
						<th><?php esc_html_e( 'ID', 'advanced-post-list' ); ?></th>
						<th><?php esc_html_e( 'Page Title', 'advanced-post-list' ); ?></th>
					</tr>
				</tfoot>
			</tbody>
		</table>
	<?php endif; ?>
	<?php
	wp_reset_postdata();
}

/**
 * APL Render checked post types.
 *
 * @param string $apl_post_types Post Types selected.
 * @param string $post_type Current post type.
 * @return string
 */
function apl_checked_post_type( $apl_post_types, $post_type ) {
	$checked = '';
	if ( ! empty( $apl_post_types ) ) {
		foreach ( $apl_post_types as $post_type_arr ) {
			if ( 'any' !== $post_type_arr ) {
				if ( in_array( $post_type, $post_type_arr, true ) ) {
					$checked = 'checked="checked"';
				}
			} elseif ( $post_type === $post_type_arr ) {
				$checked = 'checked="checked"';
			}
		}
	}

	return $checked;
}

/**
 * Diplay ( Hide or Show ) post type containers.
 *
 * @param array $apl_post_types Post Types selected.
 * @param string $post_type Current post type.
 * @return string
 */
function apl_display_post_type( $apl_post_types, $post_type ) {
	$hidden = 'display: none;';
	if ( ! empty( $apl_post_types ) ) {
		foreach ( $apl_post_types as $post_type_arr ) {
			if ( 'any' !== $post_type_arr ) {
				if ( in_array( $post_type, $post_type_arr, true ) ) {
					$hidden = 'display: block;';
				}
			} elseif ( $post_type === $post_type_arr ) {
				$hidden = 'display: block;';
			}
		}
	}

	return $hidden;
}

/**
 * APL Render selected Post Status
 *
 * @param string         $post_status Current post type to look for.
 * @param string | array $apl_post_status APL post status array.
 * @return string Selected attribute or nothing.
 */
function apl_selected_post_status( $post_status, $apl_post_status ) {
	$rtn_selected = '';
	if ( 'none' === $post_status || 'any' === $post_status ) {
		if ( $post_status === $apl_post_status ) {
			$rtn_selected = 'selected="selected"';
		}
	} elseif ( is_array( $apl_post_status ) ) {
		if ( in_array( $post_status, $apl_post_status, true ) ) {
			$rtn_selected = 'selected="selected"';
		}
	}

	return $rtn_selected;
}

?>
<?php

/*
 * **** VARIABLES **************************************************************
 */
$apl_post_list = new APL_Post_List( $post->post_name );

$apl_post_type_objs = get_post_types( '', 'objects' );

$apl_taxonomy_objs = get_taxonomies( '', 'objects' );

$apl_term_objs_arr = array();
foreach ( $apl_tax_terms as $key => $value ) {
	$args = array(
		'taxonomy'   => $key,
		'hide_empty' => false,
	);

	$apl_term_objs_arr[ $key ] = get_terms( $args );
}

?>
<div class="apl-filter-box-1">
	<div class="apl-left-minibar">
		<h3>
			<?php esc_html_e( 'Post Types', 'advanced-post-list' ); ?>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['post_types']; ?>"></span>
		</h3>
		<div class="apl_post_types">
			<?php $first_pt = false; ?>
			<?php foreach ( $apl_post_tax as $k_pt_slug => $v_arr ) : ?>
				<?php
				$checked = apl_checked_post_type( $apl_post_list->post_type, $k_pt_slug );
				if ( $first_pt && empty( $apl_post_list->post_type ) ) {
					$checked = 'checked="checked"';
				}
				$first_pt = false;
				?>
				<input type="checkbox" class="apl-toggle-post_type" id="apl-toggle-<?php echo esc_attr( $k_pt_slug ); ?>" name="apl_toggle-<?php echo esc_attr( $k_pt_slug ); ?>"  <?php echo esc_attr( $checked ); ?>>
				<label for="apl-toggle-<?php echo esc_attr( $k_pt_slug ); ?>"><?php echo esc_html( $v_arr['name'] ); ?></label>
				<br>
			<?php endforeach; ?>
		</div>
	</div>
	<div id="apl-filter-tax-query" class="apl-filter-post-tax-query">
		<!-- POST TYPES -->
		<?php $first_pt = true; ?>
		<?php foreach ( $apl_post_tax as $k_pt_slug => $v_pt_arr ) : ?>
			<?php
			$display = apl_display_post_type( $apl_post_list->post_type, $k_pt_slug );
			if ( $first_pt && empty( $apl_post_list->post_type ) ) {
				$display = 'display: block;';
			}
			$first_pt = false;
			?>
			<div id="apl-filter-<?php echo esc_attr( $k_pt_slug ); ?>" class="apl-filter-post-type" style="<?php echo esc_attr( $display ); ?>">
				<h4><span><?php echo esc_html( $v_pt_arr['name'] ); ?></span></h4>
				<div id="apl-tabs-<?php echo esc_attr( $k_pt_slug ); ?>-type" class="apl-tabs-post_type-type">
					<ul>
						<?php if ( ! empty( $v_pt_arr['tax_arr'] ) ) : ?>
							<li class="apl-t-li-post-type">
								<a href="#apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-taxonomies">
									<h5><?php esc_html_e( 'Taxonomies', 'advanced-post-list' ); ?><span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['taxonomy_tab']; ?>"></span></h5>
								</a>
							</li>
						<?php endif; ?>
						<?php if ( isset( $apl_post_type_objs[ $k_pt_slug ] ) ) : ?>
							<?php if ( $apl_post_type_objs[ $k_pt_slug ]->hierarchical ) : ?>
								<li class="apl-t-li-post-type">
									<a href="#apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-pages">
										<h5><?php esc_html_e( 'Parent Pages', 'advanced-post-list' ); ?><span class="apl-help apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['parent_page_tab']; ?>"></span></h5>
									</a>
								</li>
							<?php endif; ?>
						<?php endif; ?>
					</ul>
					<!-- TAXONOMIES -->
					<?php if ( ! empty( $v_pt_arr['tax_arr'] ) ) : ?>
						<div id="apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-taxonomies">
							<div class="apl-pt-taxonomy-selector">
								<select id="apl-multiselect-<?php echo esc_attr( $k_pt_slug ); ?>" class="apl-multiselect-<?php echo esc_attr( $k_pt_slug ); ?>" name="apl_multiselect_taxonomies-<?php echo esc_attr( $k_pt_slug ); ?>[]" multiple="multiple">
									<?php
									$tax_req_selected = '';
									if ( isset( $apl_post_list->tax_query[ $k_pt_slug ]['relation'] ) ) {
										if ( 'AND' === $apl_post_list->tax_query[ $k_pt_slug ]['relation'] ) {
											$tax_req_selected = 'selected="selected"';
										}
									}
									$first_tax = true;
									?>
									<option id="apl_chk_req_taxonomies-<?php echo esc_attr( $k_pt_slug ); ?>" class="apl-chk-req-taxonomies apl-tooltip" value="require" <?php echo esc_attr( $tax_req_selected ); ?>><b><?php esc_html_e( 'Req. Taxonomies', 'advanced-post-list' ); ?></b></option>
									<hr />
									<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
										<?php
										$opt_value    = $k_pt_slug . '-' . $tax_slug;
										$tax_selected = '';
										if ( isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
											foreach ( $apl_post_list->tax_query[ $k_pt_slug ] as $k3_pl_tax_query => $v3_pl_tax_query ) {
												if ( 'relation' !== $k3_pl_tax_query ) {
													if ( $tax_slug === $v3_pl_tax_query['taxonomy'] ) {
														$tax_selected = 'selected="selected"';
													}
												}
											}
										}

										if ( true === $first_tax && ! isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
											$tax_selected = 'selected="selected"';
										}
										$first_tax = false;
										?>
										<option value="<?php echo esc_attr( $tax_slug ); ?>" <?php echo esc_attr( $tax_selected ); ?>><?php echo esc_html( $apl_taxonomy_objs[ $tax_slug ]->labels->singular_name ); ?></option>
									<?php endforeach; ?>
								</select>
								<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['taxonomy_multiselect']; ?>"></span>
							</div>
							<div class="apl-pt-taxonomy-tabs">
								<div id="apl-tabs-<?php echo esc_attr( $k_pt_slug ); ?>-taxonomies" class="apl-tabs-post-type-taxonomies">
									<ul>
										<?php $first_tax = true; ?>
										<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
											<?php
											$tax_display = 'display: none;';
											if ( isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
												foreach ( $apl_post_list->tax_query[ $k_pt_slug ] as $k3_pl_tax_query => $v3_pl_tax_query ) {
													if ( 'relation' !== $k3_pl_tax_query ) {
														if ( $tax_slug === $v3_pl_tax_query['taxonomy'] ) {
															$tax_display = 'display: block;';
														}
													}
												}
											}
											if ( $first_tax && ! isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
												$tax_display = 'display: block;';
											}
											$first_tax = false;
											?>
											<li id="apl-t-li-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>" style="<?php echo esc_attr( $tax_display ); ?>"><a href="#apl-t-div-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>"><?php echo esc_html( $apl_taxonomy_objs[ $tax_slug ]->labels->singular_name ); ?></a></li>
										<?php endforeach; ?>
									</ul>
									<?php $first_tax = true; ?>
									<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
										<?php
										$tax_display      = 'display: none;';
										$tax_chk_required = '';
										$tax_chk_dynamic  = '';
										if ( isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
											foreach ( $apl_post_list->tax_query[ $k_pt_slug ] as $k3_pl_tax_query => $v3_pl_tax_query ) {
												if ( 'relation' !== $k3_pl_tax_query ) {
													if ( $tax_slug === $v3_pl_tax_query['taxonomy'] ) {
														$tax_display = 'display: block;';
														// Require Terms Checkbox.
														if ( 'AND' === $v3_pl_tax_query['operator'] ) {
															$tax_chk_required = 'checked="checked"';
														}
														if ( true === $v3_pl_tax_query['apl_terms_dynamic'] ) {
															$tax_chk_dynamic = 'checked="checked"';
														}
													}
												}
											}

											if ( $first_tax && ! isset( $apl_post_list->tax_query[ $k_pt_slug ] ) ) {
												$tax_display = 'display: block;';
												$first_tax   = false;
											}
										}
										?>
										<div id="apl-t-div-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>" class="apl-t-terms" style="<?php echo esc_attr( $tax_display ); ?>">
											<!-- WP_Terms -->
											<?php $ele_tag = $k_pt_slug . '-' . $tax_slug?>
											<ul>
												<li>
													<label for="apl_chk_req_terms-<?php echo esc_attr( $ele_tag ); ?>">
														<input type="checkbox" id="apl_chk_terms_req-<?php echo esc_attr( $ele_tag ); ?>" name="apl_terms_req-<?php echo esc_attr( $ele_tag ); ?>" <?php echo esc_attr( $tax_chk_required ); ?> >
														<?php esc_html_e( 'Require Terms', 'advanced-post-list' ); ?>
													</label>
													<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['require_terms']; ?>"></span>
												</li>
												<li>
													<label for="apl_chk_dynamic-<?php echo esc_attr( $ele_tag ); ?>">
														<input type="checkbox" id="apl_chk_terms_dynamic-<?php echo esc_attr( $ele_tag ); ?>" name="apl_terms_dynamic-<?php echo esc_attr( $ele_tag ); ?>" <?php echo esc_attr( $tax_chk_dynamic ); ?> >
														<?php esc_html_e( 'Dynamic Terms', 'advanced-post-list' ); ?>
													</label>
													<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['dynamic_terms']; ?>"></span>
												</li>
											</ul>
											<?php do_action( 'apl_metabox_filter_term_settings', $k_pt_slug, $tax_slug ); ?>
											<hr />
											<?php
											apl_render_categories( $k_pt_slug, $tax_slug, $apl_post_list );
											?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- PAGES -->
					<?php if ( isset( $apl_post_type_objs[ $k_pt_slug ] ) ) : ?>
						<?php if ( $apl_post_type_objs[ $k_pt_slug ]->hierarchical ) : ?>
							<div id="apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-pages">
								<?php apl_render_page_parents( $k_pt_slug , $apl_post_list ); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<div class="apl-filter-box-2">
	<!-- LEFT SIDE -->
	<div class="apl-filter-box-2-left">
		<!-- POSTS PER PAGE -->
		<div class="apl-filter-field-left-row apl-list-amount-row">
			<div>
				<label for="apl_spinner_posts_per_page"><?php esc_html_e( 'List Amount:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['list_amount']; ?>"></span>
			</div>
			<div>
				<div class="apl-spinner-posts_per_page-wrap">
					<input id="apl_spinner_posts_per_page" class="apl-spinner-posts_per_page small-text" name="apl_posts_per_page" value="<?php echo intval( $apl_post_list->posts_per_page ); ?>" />
				</div>
				<div class="apl-slider-posts_per_page-wrap">
					<div id="apl_slider_posts_per_page" class="apl-slider-posts_per_page">
						<div id="apl_slider_handle_posts_per_page" class="apl-slider-handle-posts_per_page ui-slider-handle"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- OFFSET -->
		<div class="apl-filter-field-left-row apl-list-offset-row">
			<div>
				<label for="apl_spinner_offset"><?php esc_html_e( 'Offset:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['offset']; ?>"></span>
			</div>
			<div>
				<div class="apl-spinner-offset-wrap">
					<input id="apl_spinner_offset" class="apl-spinner-offset small-text" name="apl_offset" value="<?php echo intval( $apl_post_list->offset ); ?>" />

				</div>
				<div class="apl-slider-offset-wrap">
					<div id="apl_slider_offset" class="apl-slider-offset">
						<div id="apl_slider_handle_offset" class="apl-slider-handle-offset ui-slider-handle"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- ORDER BY -->
		<div class="apl-filter-field-left-row apl-order-by">
			<div>
				<label for="apl_selectmenu_order_by"><?php esc_html_e( 'Order By:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['order_by']; ?>"></span>
			</div>
			<div>
				<select id="apl_selectmenu_order_by" class="apl-selectmenu-order-by" name="apl_order_by">
					<option <?php echo ( 'none' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="none"><?php esc_html_e( '- None -', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'ID' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e( 'ID', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'title' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e( 'Title', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'name' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="name"><?php esc_html_e( 'Slug', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'date' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e( 'Date', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'modified' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="modified"><?php esc_html_e( 'Modified Date', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'comment_count' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e( 'Comments', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'author' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="author"><?php esc_html_e( 'Author', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'parent' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="parent"><?php esc_html_e( 'Parent', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'menu_order' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="menu_order"><?php esc_html_e( 'Menu Order', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'rand' === $apl_post_list->order_by ) ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e( 'Random', 'advanced-post-list' ); ?></option>
				</select>
				<select id="apl_selectmenu_order" class="apl-selectmenu-order" name="apl_order">
					<option <?php echo ( 'DESC' === $apl_post_list->order ) ? 'selected="selected"' : ''; ?> value="DESC"><?php esc_html_e( 'Descending', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'ASC' === $apl_post_list->order ) ? 'selected="selected"' : ''; ?> value="ASC"><?php esc_html_e( 'Ascending', 'advanced-post-list' ); ?></option>
				</select>
			</div>
		</div>
		<!-- AUTHORS -->
		<div class="apl-filter-field-left-row apl-authors">
			<div>
				<label for="apl_selectmenu_author__bool"><?php esc_html_e( 'Authors:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['authors']; ?>"></span>
			</div>
			<div>
				<div>
					<!-- INPUT -->
					<select id="apl_selectmenu_author__bool" class="apl-selectmenu-author-operator" name="apl_author__bool">
						<option <?php echo ( 'none' === $apl_post_list->author__bool ) ? 'selected="selected"' : ''; ?> value="none"><?php esc_html_e( '- None -', 'advanced-post-list' ); ?></option>
						<option <?php echo ( 'in' === $apl_post_list->author__bool ) ? 'selected="selected"' : ''; ?> value="in"><?php esc_html_e( 'From', 'advanced-post-list' ); ?></option>
						<option <?php echo ( 'not_in' === $apl_post_list->author__bool ) ? 'selected="selected"' : ''; ?> value="not_in"><?php esc_html_e( 'Not From', 'advanced-post-list' ); ?></option>
					</select>
					<select id="apl_multiselect_author__in" class="apl-multiselect-author--in" name="apl_author__in[]" multiple="multiple">
						<?php global $wp_roles; ?>
						<?php foreach ( $wp_roles->role_names as $k_role_slug => $v_role_name ) : ?>
							<?php
							$args = array(
								'orderby' => 'display_name',
								'order'   => 'DESC',
								'role'    => $k_role_slug,
							);

							$apl_authors = get_users( $args );
							?>
							<?php if ( $apl_authors ) : ?>
								<optgroup label="<?php echo esc_attr( $v_role_name ); ?>">
									<?php foreach ( $apl_authors as $k2_index => $v2_user ) : ?>
										<option <?php echo ( in_array( $v2_user->ID , $apl_post_list->author__in, true ) ) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $v2_user->data->ID ); ?>" ><?php echo esc_html( $v2_user->data->display_name ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<!-- AUTHORS -->
		<div class="apl-filter-field-left-row">
			<div>
				<label for="apl_multiselect_post_status_1"><?php esc_html_e( 'Post Status:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['post_status']; ?>"></span>
			</div>
			<div>
				<select id="apl_multiselect_post_status_1" class="apl-multiselect-post-status-1" name="apl_post_status_1[]" multiple="multiple" style="width:96px;">
					<option <?php echo apl_selected_post_status( 'none', $apl_post_list->post_status ); ?> value="none"><?php esc_html_e( '- None -', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'any', $apl_post_list->post_status ); ?> value="any"><?php esc_html_e( 'Any', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'public', $apl_post_list->post_status ); ?> value="public"><?php esc_html_e( 'Public', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'private', $apl_post_list->post_status ); ?> value="private"><?php esc_html_e( 'Private (BETA)', 'advanced-post-list' ); ?></option>
				</select>
				<select id="apl_multiselect_post_status_2" class="apl-multiselect-post-status-2" name="apl_post_status_2[]" multiple="multiple">
					<option <?php echo apl_selected_post_status( 'publish', $apl_post_list->post_status ); ?> value="publish"><?php esc_html_e( 'Published', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'pending', $apl_post_list->post_status ); ?> value="pending"><?php esc_html_e( 'Pending Review', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'draft', $apl_post_list->post_status ); ?> value="draft"><?php esc_html_e( 'Draft', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'auto-draft', $apl_post_list->post_status ); ?> value="auto-draft"><?php esc_html_e( 'Auto-Draft', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'future', $apl_post_list->post_status ); ?> value="future"><?php esc_html_e( 'Scheduled', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'inherit', $apl_post_list->post_status ); ?> value="inherit"><?php esc_html_e( 'Revisions', 'advanced-post-list' ); ?></option>
					<option <?php echo apl_selected_post_status( 'trash', $apl_post_list->post_status ); ?> value="trash"><?php esc_html_e( 'Trash', 'advanced-post-list' ); ?></option>
				</select>
			</div>
		</div>
		<div class="apl-filter-field-left-row">
			<div>
				<label for="apl_selectmenu_perm"><?php esc_html_e( 'User Perms:', 'advanced-post-list' ); ?></label>
				<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['user_perms']; ?>"></span>
			</div>
			<div>
				<select id="apl_selectmenu_perm" class="apl-selectmenu-perm" name="apl_perm">
					<option <?php echo ( 'none' === $apl_post_list->perm ) ? 'selected="selected"' : ''; ?> selected="selected" value="none"><?php esc_html_e( '- None -', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'readable' === $apl_post_list->perm ) ? 'selected="selected"' : ''; ?> value="readable"><?php esc_html_e( 'Readable', 'advanced-post-list' ); ?></option>
					<option <?php echo ( 'editable' === $apl_post_list->perm ) ? 'selected="selected"' : ''; ?> value="editable"><?php esc_html_e( 'Editable', 'advanced-post-list' ); ?></option>
				</select>
			</div>
		</div>
	</div>
	<!-- RIGHT SIDE -->
	<div class="apl-filter-box-2-right">
		<!-- EXCLUDE POST BY ID -->
		<div class="apl-filter-field-row-full">
			<label for="apl_exclude_posts"><?php esc_html_e( 'Exclude Post by ID:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['exclude_posts_by_id']; ?>"></span>
		</div>
		<div class="apl-filter-field-row-full">
			<input type="text" id="apl_exclude_posts" class="apl-text-exclude-posts" name="apl_post__not_in" value="<?php echo implode( ',', $apl_post_list->post__not_in ); ?>" />
		</div>
		<!-- STICKY POSTS -->
		<div class="apl-filter-field-right-row">
			<label for="apl_sticky_posts"><?php esc_html_e( 'Enable Sticky Posts:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['enable_sticky_posts']; ?>"></span>
			<input type="checkbox" id="apl_sticky_posts" class="apl-chkbox-sticky-posts apl-chkbox-input" name="apl_sticky_posts" <?php echo ( false === $apl_post_list->ignore_sticky_posts ) ? 'checked="checked"' : ''; ?> />
		</div>
		<!-- EXCLUDE CURRENT POST -->
		<div class="apl-filter-field-right-row">
			<label for="apl_exclude_current"><?php esc_html_e( 'Exclude Current Post:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['exclude_current_post']; ?>"></span>
			<input type="checkbox" id="apl_exclude_current" class="apl-chkbox-exclude-current apl-chkbox-input" name="apl_pl_exclude_current" <?php echo ( $apl_post_list->pl_exclude_current ) ? 'checked="checked"' : ''; ?> />
		</div>
		<!-- EXCLUDE DUPLICATE POSTS -->
		<div class="apl-filter-field-right-row">
			<label for="apl_exclude_dupe" ><?php esc_html_e( 'Exclude Duplicate Posts:', 'advanced-post-list' ); ?></label>
			<span class="apl-tooltip apl-help apl-help-icon dashicons dashicons-editor-help" title="<?php echo $apl_help_text['exclude_duplicate_posts']; ?>"></span>
			<?php $p_pl_exclude_dupes = ( true === $apl_post_list->pl_exclude_dupes ) ? 'checked="checked"' : ''; ?>
			<input type="checkbox" id="apl_exclude_dupes" class="apl-chkbox-exclude-dupes apl-chkbox-input" name="apl_pl_exclude_dupes" <?php echo ( true === $apl_post_list->pl_exclude_dupes ) ? 'checked="checked"' : ''; ?> />
		</div>
	</div>
</div>
