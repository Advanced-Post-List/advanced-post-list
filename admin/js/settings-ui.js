/**
 * Settings UI JavaScript
 *
 * @summary     Settings JavaScript / jQuery for UI design and conditional logic.
 *
 * @since       0.4.0
 * @package     APL_Admin
 * @requires    jQuery
 * @requires    jQuery UI
 * @requires    jQuery UI Multiselect
 * 
 */

( function($) {
	var trans = apl_settings_ui_local.trans;

	apl_init_tooltips();

	/**
	 * Initialize Tooltips
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function apl_init_tooltips() {
		$( '.apl-tooltip' ).tooltip({
//			content: function( callback ) {
//				//callback( $( this ).prop('title').replace( '(<br([\s]?[\/]?)?>)', '<br />' ) );
//			}
			content: function( callback ) {
				callback( $( this ).prop( 'title' ) );
			}
		});
	}

	/**
	 * WordPress MetaBox Workaround (toggle)
	 */
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	postboxes.add_postbox_toggles( 'apl_post_list_page_apl_settings' );

	/**
	 * Event lister for Invalid Characters
	 *
	 * @since 0.4.0
	 */
	$( '#apl_export' ).change( function( event ) {
		console.log( 'Filename change().' );
		
		var fileName = $( this ).val();
		var iChars = "<>:\"/\\|,?*";

		for ( var i = 0; i < fileName.length; i++ ) {
			if ( iChars.indexOf( fileName.charAt(i) ) != -1) {
				apl_alert( '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 75px 0;"></span>' + trans.fileName_char_alert_message1 + '<br/>' + trans.fileName_char_alert_message2 + '</p>', trans.fileName_char_alert_title );
			}
		}
	});

	/**
	 * APL Alert Dialog
	 *
	 * @since 0.4.0
	 *
	 * @param {type} output_msg
	 * @param {type} title_msg
	 * @returns {undefined}
	 */
	function apl_alert( output_msg, title_msg ) {
		if ( !title_msg )
			title_msg = trans.default_alert_title;

		if ( !output_msg )
			output_msg = trans.default_alert_message;

		$('<div></div>').html( output_msg ).dialog({
			title: title_msg,
			//resizable: true,
			modal: true,
			buttons: {
				"Ok": function() {
					$( this ).dialog( 'close' );
				}
			}
		});
	}
})(jQuery);
