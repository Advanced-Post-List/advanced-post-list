<?php
/**
 * APL Import
 *
 * Final step to import data.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @package advanced-post-list\APL_Admin
 * @since 0.4.0
 */

/**
 * APL Import from File(s)
 *
 * Used to catch any files that need to be overwritten.
 *
 * @since 0.3.0
 * @since 0.4.0 Changed to Post Data.
 */
function apl_import() {
	check_ajax_referer( 'apl_import' );

	$tmp_post_list  = get_option( 'apl_import_overwrite_post_list' );
	$tmp_design     = get_option( 'apl_import_overwrite_design' );

	if ( isset( $_GET['post_list_overwrite'] ) ) {
		$tmp_post_list_slugs = explode( ',', $_GET['post_list_overwrite'] );

		foreach ( $tmp_post_list_slugs as  $v1_pt_slug ) {
			foreach ( $tmp_post_list as $k2_index => $v2_post_list_import ) {
				if ( $v1_pt_slug === $v2_post_list_import->slug ) {
					$apl_post_list = new APL_Post_List( $v2_post_list_import->slug );

					$apl_post_list->title               = $v2_post_list_import->title               ?: $apl_post_list->title;
					$apl_post_list->post_type           = $v2_post_list_import->post_type           ? json_decode( json_encode( $apl_post_list->post_type ), true ) : $apl_post_list->post_type ;
					$apl_post_list->tax_query           = $v2_post_list_import->tax_query           ? json_decode( json_encode( $apl_post_list->tax_query ), true ) : $apl_post_list->tax_query;
					$apl_post_list->post_parent__in     = $v2_post_list_import->post_parent__in     ? json_decode( json_encode( $apl_post_list->post_parent__in ), true ) : $apl_post_list->post_parent__in;
					$apl_post_list->post_parent_dynamic = $v2_post_list_import->post_parent_dynamic ? json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true ) : $apl_post_list->post_parent_dynamic;
					$apl_post_list->posts_per_page      = $v2_post_list_import->posts_per_page      ?: $apl_post_list->posts_per_page;
					$apl_post_list->offset              = $v2_post_list_import->offset              ?: $apl_post_list->offset;
					$apl_post_list->order_by            = $v2_post_list_import->order_by            ?: $apl_post_list->order_by;
					$apl_post_list->order               = $v2_post_list_import->order               ?: $apl_post_list->order;
					$apl_post_list->post_status         = $v2_post_list_import->post_status         ? json_decode( json_encode( $apl_post_list->post_status ), true ): $apl_post_list->post_status;
					$apl_post_list->perm                = $v2_post_list_import->perm                ?: $apl_post_list->perm;
					$apl_post_list->author__bool        = $v2_post_list_import->author__bool        ?: $apl_post_list->author__bool;
					$apl_post_list->author__in          = $v2_post_list_import->author__in          ?: $apl_post_list->author__in;
					$apl_post_list->ignore_sticky_posts = $v2_post_list_import->ignore_sticky_posts ?: $apl_post_list->ignore_sticky_posts;
					$apl_post_list->post__not_in        = $v2_post_list_import->post__not_in        ?: $apl_post_list->post__not_in;
					$apl_post_list->pl_exclude_current  = $v2_post_list_import->pl_exclude_current  ?: $apl_post_list->pl_exclude_current;
					$apl_post_list->pl_exclude_dupes    = $v2_post_list_import->pl_exclude_dupes    ?: $apl_post_list->pl_exclude_dupes;

					foreach ( $tmp_design as $v2_design_import ) {
						if ( $apl_post_list->slug === $v2_design_import->slug ) {
							// TODO Change to Unique ID.
							$apl_design = new APL_Design( $v2_design_import->slug );

							$apl_design->title   = $v2_design_import->title   ?: $apl_design->title;
							$apl_design->before  = $v2_design_import->before  ?: $apl_design->before;
							$apl_design->content = $v2_design_import->content ?: $apl_design->content;
							$apl_design->after   = $v2_design_import->after   ?: $apl_design->after;
							$apl_design->empty   = $v2_design_import->empty   ?: $apl_design->empty;

							$apl_design->save_design();
						}
					}

					$apl_post_list->pl_apl_design      = $v2_post_list_import->pl_apl_design ?: $apl_post_list->pl_apl_design;
					$apl_post_list->pl_apl_design_id   = $apl_design->id                     ?: $apl_design->id ;
					$apl_post_list->pl_apl_design_slug = $apl_design->slug                   ?: $apl_design->slug;

					$apl_post_list->save_post_list();
					unset( $tmp_post_list[ $k2_index ] );
				}
			}
		}
	}

//	if ( isset( $_GET['post_list_overwrite'] ) ) {
//		$tmp_post_list_slugs = explode( ',', $_GET['post_list_overwrite'] );
//
//		foreach ( $tmp_post_list_slugs as  $v1_d_slug ) {
//			foreach ( $tmp_design as $v2_design_import ) {
//				if ( $v1_d_slug === $v2_design_import->slug ) {
//
//					// TODO Change to Unique ID.
//					$apl_design = new APL_Design( $v2_design_import->slug );
//
//					$apl_design->title    = $v2_design_import->title    ?: $apl_design->title;
//					$apl_design->before   = $v2_design_import->before   ?: $apl_design->before;
//					$apl_design->content  = $v2_design_import->content  ?: $apl_design->content;
//					$apl_design->after    = $v2_design_import->after    ?: $apl_design->after;
//					$apl_design->empty    = $v2_design_import->empty    ?: $apl_design->empty;
//
//					$apl_design->save_design();
//				}
//			}
//		}
//	}

	/* POSSIBLE CONCEPT FOR SELECTING DESIGNS */
	//if ( isset( $_GET['design_overwrite'] ) ) {
	//	$tmp_design_slugs = explode( ',', $_GET['design_overwrite'] );
	//
	//	foreach ( $tmp_design_slugs as $v1_d_slug ) {
	//		foreach ( $tmp_design as $v2_design_import ) {
	//			if ( $v1_d_slug === $v2_design_import->slug ) {
	//
	//				$apl_design = new APL_Design( $v2_design_import->slug );
	//
	//				$apl_design->title    = $v2_design_import->title    ?: $apl_design->title;
	//				$apl_design->before   = $v2_design_import->before   ?: $apl_design->before;
	//				$apl_design->content  = $v2_design_import->content  ?: $apl_design->content;
	//				$apl_design->after    = $v2_design_import->after    ?: $apl_design->after;
	//				$apl_design->empty    = $v2_design_import->empty    ?: $apl_design->empty;
	//
	//				$apl_design->save_design();
	//			}
	//		}
	//	}
	//}

	delete_option( 'apl_import_overwrite_post_list' );
	delete_option( 'apl_import_overwrite_design' );
}
