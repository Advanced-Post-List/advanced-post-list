/**
 * Basic essential functions usually unrelated to UI/UX.
 *
 * @summary     jQuery Functions.
 *
 * @since       0.4.0
 * @package     APL_Admin
 * @requires    jQuery
 * @requires    jQuery UI
 * @requires    jQuery UI Multiselect
 */

// TODO - Add Load Preset AJAX
// TODO - Add Pagination for list of Page Parents AJAX
jQuery(document).ready( function($) {
	/**
	 * OLD Sets the correct Output
	 *
	 * @ignore
	 * @param {type} preset_name
	 * @returns {undefined}
	 */
	function setPHPOutput( preset_name ) {
        //$('#presetPHP').html('PHP code: <code><<b>?php</b> if (method_exists($advanced_post_list, "display_post_list")){echo $advanced_post_list->display_post_list("' + preset_name + '");} <b>?</b>></code>');
    }
});
	