<?php
/**
 * APL Custom Hooks
 *
 * Preset Post List Object used by Preset Db.
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.1.0
 */

/**
 * 
 * @param type $slug
 * @param type $preset_obj
 * @return type
 */
function hook_filter_apl_design_slug( $slug, $preset_obj ) {
	$return_slug = $slug;
	$val_1       = $preset_obj;

	return $return_slug;
}
//add_filter( 'apl_design_slug', 'hook_filter_apl_design_slug', 10, 2 );

/**
 * Args Array for Registering Post List CPT.
 *
 * @param type $args
 * @return type
 */
function hook_filter_apl_register_post_type_post_list( $args ) {
	$return_args = $args;
	
	return $return_args;
}
//add_filter( 'apl_register_post_type_post_list', 'hook_filter_apl_register_post_type_post_list' );

/**
 * Args Array for Registering Design CPT.
 *
 * @param type $args
 * @return type
 */
function hook_filter_apl_register_design( $args ) {
	$return_args = $args;

	return $return_args;
}
//add_filter( 'apl_register_post_type_design', 'hook_filter_apl_register_design' );


/**
 * Used to set the APL_Design slug in 'APL_Post_List->apl_design'.
 *
 * @param type $post_list_slug
 * @return string Design slug.
 */
function hook_filter_design_slug_for_get_apl_post_list( $post_list_slug ) {
	$return_slug = $post_list_slug;
	
	return $return_slug;
}
//add_filter( 'apl_post_list_get_data_apl_design_slug', 'hook_filter_design_slug_for_get_apl_post_list' );
//( 'apl_post_list_apl_design_slug', $this->slug )

/**
 * Used to set the APL_Design slug in 'APL_Post_List->apl_design'.
 *
 * @param type $post_list_slug
 * @return string Design slug.
 */
function hook_filter_design_slug_for_process_apl_post_list( $post_list_slug ) {
	$return_slug = $post_list_slug;
	
	return $return_slug;
}
add_filter( 'apl_post_list_process_apl_design_slug', 'hook_filter_design_slug_for_process_apl_post_list' );

/**
 * Manually prevents Custom Post Types from being displayed on Add New.
 * @param array $ignore_post_types
 * @return string
 */
function hook_filter_apl_display_post_types_ignore( $ignore_post_types ) {
	$ignore_post_types[] = 'et_pb_layout';
	return $ignore_post_types;
}
add_filter( 'apl_display_post_types_ignore', 'hook_filter_apl_display_post_types_ignore' );