<?php
/**
 * Filter Meta Box Template.
 *
 * Filter Meta Box for making new Post Lists.
 *
 * @package WordPress
 * @subpackage APL_Admin
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

?>
<?php

/*
 * **** FUNCTIONS **************************************************************
 */

/**
 * Short Description.
 *
 * Full Description.
 *
 * @since 0.4.0
 *
 * @param string $post_type   Post Type slug.
 * @param string $taxonomy    Taxonomy slug.
 * @param int    $term_parent Parent Term ID.
 * @param int    $indent      Number of indents.
 * @return void
 */
function apl_render_categories( $post_type, $taxonomy, $term_parent = 0, $indent = 0 ) {
	$args = array(
		'taxonomy' => $taxonomy,
		'parent' => $term_parent,
		'hide_empty' => false,
	);
	$terms = get_terms( $args );
	?>
	<?php if ( ! empty( $terms ) ) : ?>
		<?php $e_indent = 18 * $indent; ?>
		<ul style="margin-left: <?php echo absint( $e_indent ); ?>px;">
			<?php if ( 0 === $term_parent ) : ?>
				<?php $ele_tag = $post_type . '-' . $taxonomy . '-any'?>
				<li>
					<label for="term-<?php echo esc_attr( $ele_tag ); ?>">
						<input type="checkbox" id="term-<?php echo esc_attr( $ele_tag ); ?>" name="term-<?php echo esc_attr( $ele_tag ); ?>" >
						<?php esc_html_e( 'Any / All Terms', 'advanced-post-list' ); ?>
					</label>
				</li>
			<?php endif; ?>
			<?php foreach ( $terms as $k_index => $v_term_obj ) : ?>
				<?php $ele_tag = $post_type . '-' . $taxonomy . '-' . $v_term_obj->slug; ?>
				<li>
					<label for="term-<?php echo esc_attr( $ele_tag ); ?>">
						<input type="checkbox" id="term-<?php echo esc_attr( $ele_tag ); ?>" name="term-<?php echo esc_attr( $ele_tag ); ?>" >
						<?php echo esc_html( $v_term_obj->name ); ?>
					</label>
				</li>
				<?php apl_render_categories( $post_type, $taxonomy, $v_term_obj->term_id, ( $indent + 1 ) ); ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php
}

/**
 * Short Desc.
 *
 * Description.
 *
 * @param string $post_type    Post Type slug.
 * @param int    $page_parent  Parent Page(Post) ID.
 * @param int    $indent       Amount to Indent (Add dashes).
 */
function apl_render_page_parents( $post_type, $page_parent = 0, $indent = 0 ) {
	$args = array(
		'post_type'       => $post_type,
		'post_parent'     => $page_parent,
		'posts_per_page'  => -1,
		'order'           => 'DESC',
		'orderby'         => 'name',
	);
	$query_pages = new WP_Query( $args );

	$indent_str = '';
	for ( $i = 0; $i < $indent; $i++ ) {
		$indent_str .= '—';
	}

	?>
	
	<?php if ( 0 === $page_parent ) : ?>
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
		$ele_tag = $post_type . '-' . $query_pages->post->post_name;
		?>
				<tr class="alternate">
					<td class="apl-chk-td-post_type-parent">
						<input type="checkbox" class="apl-page-post_type-pages" id="apl-page-<?php echo esc_attr( $ele_tag ); ?>" name="apl-page-<?php echo esc_attr( $ele_tag ); ?>" />
					</td>
					<td>
						<?php echo esc_html( $query_pages->post->ID ); ?>
					</td>
					<td class="row-title">
						<label for="apl-page-<?php echo esc_attr( $ele_tag ); ?>"><?php echo esc_html( $indent_str . ' ' . $query_pages->post->post_title ); ?></label>
					</td>
				</tr>
				<?php
				apl_render_page_parents( $post_type, $query_pages->post->ID, ( $indent + 1 ) );
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
		'taxonomy' => $key,
		'hide_empty' => false,
	);
	$apl_term_objs_arr[ $key ] = get_terms( $args );
}

?>
<div class="apl-filter-box-1">
	<div class="apl-left-minibar">
		<h3><span><?php esc_html_e( 'Post Types', 'advanced-post-list' ); ?></span></h3>
		<div class="apl_post_types">
			<?php foreach ( $apl_post_tax as $k_pt_slug => $v_arr ) : ?>
				<input type="checkbox" class="apl-toggle-post_type" id="apl-toggle-<?php echo esc_attr( $k_pt_slug ); ?>" name="apl-toggle-<?php echo esc_attr( $k_pt_slug ); ?>" >
				<label for="apl-toggle-<?php echo esc_attr( $k_pt_slug ); ?>"><?php echo esc_html( $v_arr['name'] ); ?></label>
				<br>
			<?php endforeach; ?>
		</div>
	</div>
	<div id="apl-filter-tax-query" class="apl-filter-post-tax-query">
		<!-- POST TYPES -->
		<?php foreach ( $apl_post_tax as $k_pt_slug => $v_pt_arr ) : ?>
			<div id="apl-filter-<?php echo esc_attr( $k_pt_slug ); ?>" class="apl-filter-post-type">
				<h4><span><?php echo esc_html( $v_pt_arr['name'] ); ?></span></h4>
				<div id="apl-tabs-<?php echo esc_attr( $k_pt_slug ); ?>-type" class="apl-tabs-post_type-type">
					<ul>
						<?php if ( ! empty( $v_pt_arr['tax_arr'] ) ) : ?>
							<li class="apl-t-li-post-type"><a href="#apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-taxonomies"><h5><?php esc_html_e( 'Taxonomies', 'advanced-post-list' ); ?></h5></a></li>
						<?php endif; ?>
						<?php if ( isset( $apl_post_type_objs[ $k_pt_slug ] ) ) : ?>
							<?php if ( $apl_post_type_objs[ $k_pt_slug ]->hierarchical ) : ?>
								<li class="apl-t-li-post-type"><a href="#apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-pages"><h5><?php esc_html_e( 'Parent Pages', 'advanced-post-list' ); ?></h5></a></li>
							<?php endif; ?>
						<?php endif; ?>
					</ul>
					<!-- TAXONOMIES -->
					<?php if ( ! empty( $v_pt_arr['tax_arr'] ) ) : ?>
						<div id="apl-t-<?php echo esc_attr( $k_pt_slug ); ?>-type-taxonomies">
							<div class="apl-pt-taxonomy-selector">
								<select id="apl-multiselect-<?php echo esc_attr( $k_pt_slug ); ?>" name="apl-multiselect-<?php echo esc_attr( $k_pt_slug ); ?>" value="<?php echo esc_attr( $k_pt_slug ); ?>" multiple="multiple">
									<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
										<?php $opt_value = $k_pt_slug . '-' . $tax_slug; ?>
										<option value="<?php echo esc_attr( $opt_value ); ?>"><?php echo esc_html( $apl_taxonomy_objs[ $tax_slug ]->labels->singular_name ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="apl-pt-taxonomy-tabs">
								<div id="apl-tabs-<?php echo esc_attr( $k_pt_slug ); ?>-taxonomies" class="apl-tabs-post-type-taxonomies">
									<ul>
										<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
											<li id="apl-t-li-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>"><a href="#apl-t-div-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>"><?php echo esc_html( $apl_taxonomy_objs[ $tax_slug ]->labels->singular_name ); ?></a></li>
										<?php endforeach; ?>
									</ul>
									<?php foreach ( $v_pt_arr['tax_arr'] as $index => $tax_slug ) : ?>
										<div id="apl-t-div-<?php echo esc_attr( $k_pt_slug ); ?>-<?php echo esc_attr( $tax_slug ); ?>" class="apl-t-terms">
											<!-- WP_Terms -->
											<?php $ele_tag = $k_pt_slug . '-' . $tax_slug?>
											<ul>
												<li>
													<label for="apl_chk_req_tax_<?php echo esc_attr( $ele_tag ); ?>">
														<input type="checkbox" id="apl_chk_req_tax_<?php echo esc_attr( $ele_tag ); ?>" name="check_required_taxonomy_<?php echo esc_attr( $ele_tag ); ?>" >
														<?php esc_html_e( 'Require this Taxonomy.', 'advanced-post-list' ); ?>
													</label>
												</li>
												<li>
													<label for="apl_chk_req_terms_<?php echo esc_attr( $ele_tag ); ?>">
														<input type="checkbox" id="apl_chk_req_terms_<?php echo esc_attr( $ele_tag ); ?>" name="apl_chk_req_terms_<?php echo esc_attr( $ele_tag ); ?>" >
														<?php esc_html_e( 'Require added Terms', 'advanced-post-list' ); ?>
													</label>
												</li>
												<li>
													<label for="apl_chk_dynamic_<?php echo esc_attr( $ele_tag ); ?>">
														<input type="checkbox" id="apl_chk_dynamic_<?php echo esc_attr( $ele_tag ); ?>" name="check_dynamic_<?php echo esc_attr( $ele_tag ); ?>" >
														<?php esc_html_e( 'Dynamic Terms', 'advanced-post-list' ); ?>
													</label>
												</li>
											</ul>
											<?php do_action( 'apl_metabox_filter_term_settings', $k_pt_slug, $tax_slug ); ?>
											<hr />
											<?php
											apl_render_categories( $k_pt_slug, $tax_slug );
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
								<?php apl_render_page_parents( $k_pt_slug ); ?>
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
		<div class="apl-filter-field-left-row" style="height: 84px;">
			<div>
				<label for="apl_list_amount">List Amount:</label>
			</div>
			<div>
				<input id="apl_amount" class="apl-spinner-amount small-text" name="list_amount" value="5" />
				<div class="apl-amount-alt">
					<div id="apl_amount_slider" class="apl-slider-amount"><div id="apl_slider_handle" class="apl-slider-handle-amount ui-slider-handle"></div></div>
				</div>
			</div>
		</div>
		<div class="apl-filter-field-left-row apl-order-by">
			<div>
				<label for="apl_order_by">Order By:</label>
			</div>
			<div>
				<!-- INPUT -->
				<select id="apl_order_by" class="apl-selectmenu-order-by">
					<option selected="selected" value="none">- None -</option>
					<option value="ID">ID</option>
					<option value="title">Title</option>
					<option value="name">Slug</option>
					<option value="date">Date</option>
					<option value="modified">Modified Date</option>
					<option value="comment_count">Comments</option>
					<option value="author">Author</option>
					<option value="parent">Parent</option>
					<option value="menu_order">Menu Order</option>
					<option value="rand">Random</option>
				</select>
				<select id="apl_order" class="apl-selectmenu-order">
					<option selected="selected" value="desc">Descending</option>
					<option value="asc">Ascending</option>
				</select>
			</div>
		</div>
		<div class="apl-filter-field-left-row apl-authors">
			<div>
				<label for="apl_author_operator">Authors:</label>
			</div>
			<div>
				<div>
					<!-- INPUT -->
					<select id="apl_author_operator" class="apl-selectmenu-author-operator">
						<option selected="selected" value="none">- None -</option>
						<option value="in">From</option>
						<option value="not_in">Not From</option>
					</select>
					<select id="apl_author_ids" class="apl-multiselect-author-ids">
						<?php global $wp_roles; ?>
						<?php foreach ( $wp_roles->role_names as $k_role_slug => $v_role_name ) : ?>
							<?php
							$args = array(
								'orderby' => 'display_name',
								'order' => 'DESC',
								'role' => $k_role_slug,
							);
							$apl_authors = get_users( $args );
							?>
							<?php if ( $apl_authors ) : ?>
								<optgroup label="<?php echo esc_attr( $v_role_name ); ?>">
									<?php foreach ( $apl_authors as $k2_index => $v2_user ) : ?>
										<option value="<?php echo esc_attr( $v2_user->data->ID ); ?>" ><?php echo esc_html( $v2_user->data->display_name ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="apl-filter-field-left-row">
			<div>
				<label for="apl_post_status_1">Post Status:</label>
			</div>
			<div>
				<!-- INPUT -->
				<select id="apl_post_status_1" class="apl-multiselect-post-status-1" multiple="multiple" style="width:96px;">
					<!-- ADDED ANY -->
					<option selected="selected" value="any">Any</option>
					<option value="public">Public</option>
					<option value="private">Private (BETA)</option>
				</select>
				<select id="apl_post_status_2" class="apl-multiselect-post-status-2" multiple="multiple">
					<option value="publish">Published</option>
					<option value="pending">Pending Review</option>
					<option value="draft">Draft</option>
					<option value="auto-draft">Auto-Draft</option>
					<option value="future">Scheduled</option>
					<option value="inherit">Revisions</option>
					<option value="trash">Trash</option>
				</select>
			</div>
		</div>
		<div class="apl-filter-field-left-row">
			<div>
				<label for="apl_perm">User Perms:</label>
			</div>
			<div>
				<!-- INPUT -->
				<select id="apl_perm" class="apl-selectmenu-perm">
					<option selected="selected" value="none">- None -</option>
					<option value="readable">Readable</option>
					<option value="editable">Editable</option>
				</select>
			</div>
		</div>
	</div>
	<!-- RIGHT SIDE -->
	<div class="apl-filter-box-2-right">
		<div class="apl-filter-field-row-full">
			<label for="apl_exclude_posts">Exclude Post by ID:</label>
		</div>
		<div class="apl-filter-field-row-full">
			<input type="text" id="apl_exclude_posts" class="apl-text-exclude-posts"/>
		</div>
		<div class="apl-filter-field-right-row">
			<label for="apl_sticky_posts">Enable Sticky Posts:</label>
			<input type="checkbox" id="apl_sticky_posts" class="apl-chkbox-sticky-posts apl-chkbox-input" />
		</div>
		<div class="apl-filter-field-right-row">
			<label for="apl_exclude_current">Exclude Current Post:</label>
			<input type="checkbox" id="apl_exclude_current" class="apl-chkbox-exclude-current apl-chkbox-input" checked="checked" />
		</div>
		<div class="apl-filter-field-right-row">
			<label for="apl_exclude_dupe" >Exclude Duplicate Posts:</label>
			<input type="checkbox" id="apl_exclude_dupe" class="apl-chkbox-exclude-dupe apl-chkbox-input" />
		</div>
	</div>
</div>
