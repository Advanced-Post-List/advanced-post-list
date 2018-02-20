/**
 * Settings UI JavaScript
 *
 * @summary     Settings JavaScript / jQuery.
 *
 * @since       0.4.0
 * @package     APL_Admin
 * @requires    jQuery
 * @requires    jQuery UI
 */

( function($) {
	var exportNonce  = apl_settings_local.export_nonce;
	var importNonce  = apl_settings_local.import_nonce;
	var restoreNonce = apl_settings_local.restore_nonce;
	var trans        = apl_settings_local.trans;

	/**
	 * Save Settings Event
	 *
	 * @since 0.4.0
	 */
	$( '#apl_save_settings' ).click( function( event ) {
		console.log('Saving Settings');
	});

	/**
	 * Export AJAX Event
	 *
	 * @since 0.4.0
	 */
	$('#apl_export').click( function( event ) {
		console.log( 'Initializing Export...' );

		var fileName = $( '#apl_export_filename' ).val();

		// Check Filename for errors
		if ( '' === fileName ) {
			apl_alert( trans.fileName_empty_alert_message, trans.fileName_empty_alert_title );
			return false;
		} else {
			var iChars = "<>:\"/\\|,?*";

			for ( var i = 0; i < fileName.length; i++ ) {
				if ( iChars.indexOf( fileName.charAt(i) ) != -1 ) {
					// Cannot use (< > : " / \\ | , ? *).
					apl_alert('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 75px 0;"></span>' + trans.fileName_char_alert_message1 + '<br/>' + trans.fileName_char_alert_message2 + '</p>', trans.fileName_char_alert_title );
					return false;
				}
			}
		}

		var formData = new FormData();
		formData.append( 'action', 'apl_settings_export' );
		formData.append( '_ajax_nonce', exportNonce );
		formData.append( 'export_type', 'database' );
		formData.append( 'filename', $.trim( $('#apl_export_filename').val() ) );
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			cache: false,
			contentType: false,
			processData: false,

			data: formData,
			dataFilter: function( data, type ){
				return JSON.parse( data );
			},
			success: function( data, textStatus, jqXHR ){
				var paramStr = '';
				paramStr += '?_ajax_nonce=' + data._ajax_nonce;
				paramStr += '&action='      + data.action;
				paramStr += '&filename='    + data.filename;


				var elemIF = document.createElement("iframe");
				elemIF.id = 'apl_exportIF'
				elemIF.style.display = "none";
				elemIF.src = ajaxurl + paramStr;

				document.body.appendChild(elemIF);
			}
		});
	});

	/**
	 * Import Event (AJAX)
	 *
	 * @since 0.4.0
	 */
	$('#form_settings_import').submit( function( event ) {
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening

		var importFiles = $('#apl_file_import')[0].files;
		var formData = new FormData();

		if ( 1 > importFiles.length ) {
			apl_alert( trans.import_no_file_message, trans.import_no_file_title );
			return false;
		}

		var invalid = false;
		jQuery.each( importFiles, function( index, value ) {
			if ( '' === value.name ) {
				apl_alert( trans.import_no_file_message, trans.import_no_file_title );

				invalid = true;
				return false;
			} else {
				var ext = value.name.split('.').pop().toLowerCase();
				if($.inArray(ext, ['json']) === -1) {
					apl_alert( trans.import_invalid_file_message, trans.import_invalid_file_title );

					invalid = true;
					return false;
				}
			}

			var name = 'file_' + index;
			formData.append( name, value )
		});

		if ( invalid ) {
			return false;
		}

		formData.append('action', 'apl_settings_import');
		formData.append('_ajax_nonce', importNonce);

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request


			beforeSend: function( jqXHR, settings ){
				var element = document.getElementById('apl_export_if');
				if ( element !== null ){
					element.parentNode.removeChild( element );
				}
			},
			success: function( data, textStatus, jqXHR ) {
				console.log( 'Return from Server-side.' );

				if ( $.isEmptyObject( data.overwrite_post_list ) && $.isEmptyObject( data.overwrite_design ) ) {
					// No Data to confirm overwrite.
					apl_alert( trans.import_success_message, trans.import_success_title );
				} else {
					var output = '';

					//output += '<h3 id="overwrite_select_group" style="margin: 5px 0px;" >';
					//output += '(<a id="overwrite_select_group_all" >All</a> / ';
					//output += '<a id="overwrite_select_group_none">None</a>) Presets</h3>';

					output += '<h3>Post Lists</h3>';
					output += '<div>';
					for ( var pl_i in data.overwrite_post_list ) {
						output += '<input type="checkbox" name="' + data.overwrite_post_list[ pl_i ] + '" value="' + data.overwrite_post_list[ pl_i ] + '" id="chkGroup_overwrite_preset_' + data.overwrite_post_list[ pl_i ] + '" />';
						output += '<label for="">' + data.overwrite_post_list[ pl_i ] + '</label>';
						output += '<br />';
					}
					output += '</div>';

//					output += '<h3>Designs</h3>';
//					output += '<div>';
//					for ( var d_i in data.overwrite_design ) {
//						output += '<input type="checkbox" name="' + data.overwrite_design[ d_i ] + '" value="' + data.overwrite_design[ d_i ] + '" id="chkGroup_overwrite_preset_' + data.overwrite_design[ d_i ] + '" />';
//						output += '<label for="">' + data.overwrite_design[ d_i ] + '</label>';
//						output += '<br />';
//					}
//					output += '</div>';

					$('<div id="apl_confirm_overwrite"></div>').html( output ).dialog({
						stack: false,
						title: trans.import_overwrite_dialog_title,
						resizable: true,
						height: 270,
						minWidth: 360,
						maxWidth: 540,
						maxHeight: 639,
						modal: true,
						buttons: {
							Next: function() {
								var post_list_overwrite = [];
								var design_overwrite = [];

								for ( var pl_i in data.overwrite_post_list ) {
									if ( $( '#chkGroup_overwrite_preset_' + data.overwrite_post_list[ pl_i ] ).is( ':checked' ) ) {
										post_list_overwrite.push( data.overwrite_post_list[ pl_i ] );
									}
								}
								for ( var d_i in data.overwrite_post_list ) {
									if ( $( '#chkGroup_overwrite_preset_' + data.overwrite_design[ d_i ] ).is( ':checked' ) ) {
										design_overwrite.push( data.overwrite_design[ d_i ] );
									}
								}

								var paramStr = '';
								paramStr += '?_ajax_nonce='          + data._ajax_nonce;
								paramStr += '&action='               + data.action;
								paramStr += '&post_list_overwrite='  + post_list_overwrite;
								paramStr += '&design_overwrite='     + design_overwrite;

								var elemIF = document.createElement("iframe");
								elemIF.id = 'apl_exportIF';
								elemIF.style.display = "none";
								elemIF.src = ajaxurl + paramStr;

								document.body.appendChild(elemIF);

								apl_alert( trans.import_success_message, trans.import_success_title );

								$( this ).dialog( "close" );
								var element = document.getElementById( 'apl_confirm_overwrite' );
								element.parentNode.removeChild(element);
							},
							Cancel: function() {
								$( this ).dialog( "close" );
								var element = document.getElementById( 'apl_confirm_overwrite' );
								element.parentNode.removeChild(element);
							}
						},
						open: function(){
							$('#overwrite_select_group_all').click( function( event ){
								for ( var preset_key in data.overwrite_preset_db ) {
									$('#chkGroup_overwrite_preset_' + preset_key).attr( 'checked', true );
								}
							});
							$('#overwrite_select_group_none').click( function( event ){
								for (var preset_key in data.overwrite_preset_db) {
									$('#chkGroup_overwrite_preset_' + preset_key).attr( 'checked', false );
								}
							});
						}
					});// End .dialog().
				}
			}
		});// End AJAX.
	});// End .submit().

	/**
	 * APL Alert Dialog
	 *
	 * @since 0.4.0
	 *
	 * @param {type} output_msg
	 * @param {type} title_msg
	 * @param {type} id
	 * @returns {undefined}
	 */
	function apl_alert( output_msg, title_msg, id ) {
		if ( !id ) 
			id = '';
		else
			id = ' id="apl-dialog-' + id + '" ';
		if ( !title_msg )
			title_msg = trans.default_alert_title;

		if ( !output_msg )
			output_msg = trans.default_alert_message;

		var elem = '<div' + id + '></div>';
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
