<?php

function apl_export() {
	check_ajax_referer( 'apl_export' );
	$export_data = get_option( 'apl_export_data' );
	delete_option( 'apl_export_data' );

	$output_data = array(
		'version'            => $export_data['version'],
		'apl_post_list_arr'  => array(),
		'apl_design_arr'     => array(),
	);

	foreach ( $export_data['apl_post_list_arr'] as $apl_post_list_slug ) {
		$post_list = new APL_Post_List( $apl_post_list_slug );

		if ( !empty( $post_list->id ) ) {
			$output_data['apl_post_list_arr'][] = $post_list;
		}
	}
	foreach ( $export_data['apl_design_arr'] as $apl_design_slug ) {
		$design = new APL_Design( $apl_design_slug );

		if ( !empty( $design->id ) ) {
			$output_data['apl_design_arr'][] = $design;
		}
	}

	header('Content-type: application/json');
	header('Content-Disposition: attachment; filename="' . $_GET['filename'] . '.json"');

	echo json_encode( $output_data );
	exit();
}

?>
