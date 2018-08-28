<?php

/**
 * APL Restore Post Lst - Default
 *
 * @since 0.5
 *
 * @return object
 */
function apl_restore_post_list_default() {
	return (object) array(
//		'id'                  => 123,
//		'title'               => 'Footer List',
//		'slug'                => 'footer-list',
		'post_type'           => array( 'any' ),
		'tax_query'           => array(
			'any' => array(
				'0'        => array(
					'field'             => 'id',
					'terms'             => array( 0 ),
					'include_children'  => false,
					'operator'          => 'IN',
					'apl_terms_slug'    => array(),
					'apl_terms_dynamic' => false,
				),
				'relation' => 'OR',
			),
		),
		'post_parent__in'     => array(),
		'post_parent_dynamic' => array(),
		'posts_per_page'      => '5',
		'offset'              => 0,
		'order_by'            => 'none',
		'order'               => 'DESC',
		'post_status'         => 'none',
		'perm'                => 'none',
		'author__bool'        => 'none',
		'author__in'          => array(),
		'ignore_sticky_posts' => true,
		'post__not_in'        => array(),
		'pl_exclude_current'  => true,
		'pl_exclude_dupes'    => false,
//		'pl_apl_design'       => 'footer-list',
//		'pl_apl_design_id'    => 123,
//		'pl_apl_design_slug'  => 'footer-list',
	);
}

/**
 * APL Restore Design - Excerpt Divided
 *
 * @since 0.5
 *
 * @return object
 */
function apl_restore_design_excerpt_divided() {
	return (object) array(
//		'id'      => 123,
		'slug'    => 'excerpt-divided',
		'title'   => 'excerpt-divided',
		'before'  => '<p><hr />',
		'content' => '<a href="[post_permalink]">[post_title]</a> by [post_author] - [post_date]<br />[post_excerpt]<hr />',
		'after'   => '</p>',
		'empty'   => '',
	);
}

/**
 * APL Restore Design - Content Divided
 *
 * @since 0.5
 *
 * @return object
 */
function apl_restore_design_page_content_divided() {
	return (object) array(
//		'id'      => 123,
		'slug'    => 'page-content-divided',
		'title'   => 'page-content-divided',
		'before'  => '<p><hr />',
		'content' => '<a href="[post_permalink]">[post_title]</a> by [post_author] - [post_date]<br />[post_content]<hr />',
		'after'   => '</p>',
		'empty'   => '',
	);
}

/**
 * APL Restore Design - Footer List
 *
 * @since 0.5
 *
 * @return object
 */
function apl_restore_design_footer_list() {
	return (object) array(
//		'id'      => 123,
		'slug'    => 'footer-list',
		'title'   => 'footer-list',
		'before'  => '<p align="center">',
		'content' => '<a href="[post_permalink]">[post_title]</a>[final_end] | ',
		'after'   => '</p>',
		'empty'   => '',
	);
}
