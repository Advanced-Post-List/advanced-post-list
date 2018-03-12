/**
 * Admin UI JavaScript
 *
 * @summary     Admin JavaScript / jQuery for UI design and conditional logic.
 *
 * @since       0.4.0
 * @package     APL_Admin
 * @requires    jQuery
 * @requires    jQuery UI
 * @requires    jQuery UI Multiselect
 */

( function($) {
	var post_tax  = apl_admin_ui_local.post_tax;
	var tax_terms = apl_admin_ui_local.tax_terms;
	var trans     = apl_admin_ui_local.trans;

	init_chkbox_post_type_toggle_tabs();
	init_tabs_post_type_taxonomies();
	init_multiselect_post_type_taxonomies();
	init_chkbox_terms();
	init_spinner_amount();
	init_slider_amount();
	init_selectmenu_order_by();
	init_selectmenu_order();
	init_selectmenu_author_operator();
	init_multiselect_author_ids();
	init_multiselect_post_status_1();
	init_multiselect_post_status_2();
	init_selectmenu_perm();

	init_spinner_offset();
	init_slider_offset();

	init_textarea_empty_message();

	apl_init_tooltips();
	apl_init_dialogs();

	/**
	 * Initialize Toggling Tabs with Checkbox
	 *
	 * @since 0.4.0
	 * @returns {undefined}
	 */
	function init_chkbox_post_type_toggle_tabs() {
		$.each( post_tax, function( k_post_type_slug, v_taxonomy_arr ) {
			// On Change/Click Hide or unhide sections
			$( '#apl-toggle-' + k_post_type_slug ).change( function() {
				if ( $( this ).is( ':checked' ) ) {
					if ( 'any' !== k_post_type_slug ) {
						$( '#apl-filter-' + k_post_type_slug ).show();

						$.each( v_taxonomy_arr.tax_arr, function( k2_tax_index, v2_tax_slug ) {
							var target_div = '#apl-t-div-' + k_post_type_slug + '-' + v2_tax_slug;
							$( target_div ).show();
							return false;
						});

						if ( $( '#apl-toggle-any' ).is( ':checked' ) ) {
							$( '#apl-toggle-any' ).prop( 'checked', false );
							$( '#apl-filter-any' ).hide();
						}
					} else {
						$( this ).prop( 'checked', false );
					}
				} else { // Unchecked
					$( '#apl-filter-' + k_post_type_slug ).hide();

					$( '#apl-toggle-any' ).prop( 'checked', true );
					$( '#apl-filter-any' ).show();
					$.each( post_tax, function( k2_post_type_slug, v2_taxonomy_arr ) {
						if ( 'any' !== k2_post_type_slug && $( '#apl-toggle-' + k2_post_type_slug ).is( ':checked' ) ) {
							$( '#apl-toggle-any' ).prop( 'checked', false );
							$( '#apl-filter-any' ).hide();
						}
					});
				}

				$( '#apl-tabs-any-type' ).tabs( 'refresh' );
				$( '#apl-tabs-any-taxonomies' ).tabs( 'refresh' );
				$( '#apl-tabs-' + k_post_type_slug + '-type' ).tabs( 'refresh' );
				$( '#apl-tabs-' + k_post_type_slug + '-taxonomies' ).tabs( 'refresh' );
			});// End of .change().
		});// End of .each().
	}

	/**
	 * Initialize Post Type Taxonomy Tabs
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_tabs_post_type_taxonomies () {
		$.each( post_tax, function( k_post_type_slug, v_taxonomy_arr ) {
			$( '#apl-tabs-' + k_post_type_slug + '-taxonomies' ).tabs({
				heightStyle: "fill",
				create: function(event, ui) {
					// MANUALLY HIDE ALL AND SHOW SELECTED.
					var opts = $( '#apl-multiselect-' + k_post_type_slug ).children();
					$.each( opts, function( k_tax_index, v_tax_ele ) {
						// Hide panel.
						var opt_tax = $( v_tax_ele ).val();
						if ( 'require' !== opt_tax ) {
							$( '#apl-t-div-' + k_post_type_slug + '-' + opt_tax ).hide();

							// If selected, unhide.
							var selected = $( v_tax_ele ).prop( 'selected' );
							if ( selected ) {
								$( '#apl-t-div-' + k_post_type_slug + '-' + opt_tax ).show();
							}
						}
					});
					var option_index = $( '#apl-multiselect-' + k_post_type_slug + ' option:selected' ).index();
					if ( 0 === option_index ) {
						option_index = $( '#apl-multiselect-' + k_post_type_slug + ' option' ).next( ':selected' ).index();
					}
					if ( -1 !== option_index ) {
						// -1 since req checkbox isn't a tab.
						$( '#apl-tabs-' + k_post_type_slug + '-taxonomies' ).tabs( 'option', 'active', ( option_index - 1 ) );
					}
					$( '#apl-tabs-' + k_post_type_slug + '-taxonomies' ).tabs( 'refresh' );
				}
			});

			$( '#apl-tabs-' + k_post_type_slug + '-type' ).tabs({
				heightStyle: "fill",
				create: function(event, ui) {
					//console.log('foobar');
				}
			});
		});// End of .each().
	}

	/**
	 * Initialize Multiselect with Post Type Taxonomy Selector
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_multiselect_post_type_taxonomies() {
		$.each( post_tax, function( k_pt_slug, v_tax_arr ) {
			//var elem = '#apl_chk_req_taxonomies-' + k_pt_slug;
			//var help_attr_title1 = $( elem ).attr('title');
			//elem = 'option.apl-chk-req-taxonomies';
			//var help_attr_title2 = $( elem ).attr( 'title' );

			$( '#apl-multiselect-' + k_pt_slug ).multiselect({
				header: false,
				noneSelectedText: trans.tax_noneSelectedText,
				selectedText: trans.tax_selectedText,
				selectedList: 2,
				height: 150,
				minWidth:333,
				menuWidth:333,

				click: function( event, ui ) {
					if ( 'require' === ui.value ) {
						return;
					}
					var target_tab = '#apl-t-li-' + k_pt_slug + '-' + ui.value;
					var target_div = '#apl-t-div-' + k_pt_slug + '-' + ui.value;

					var option_index = $( '#apl-multiselect-' + k_pt_slug + ' > option[value="' + ui.value + '"]' ).index();

					if ( true == ui.checked ) {
						$( '#apl-multiselect-' + k_pt_slug + ' option[value="' + ui.value + '"]' ).prop( 'selected', true );
						$( target_tab ).show();
						$( target_div ).show();

						// TODO - ADD FOCUS / ACTIVE.
						// -1 since req checkbox isn't a tab.
						$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'option', 'active', ( option_index - 1 ) );

						$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'refresh' );
					} else {
						$( '#apl-multiselect-' + k_pt_slug + ' option[value="' + ui.value + '"]' ).prop( 'selected', false );
						$( target_tab ).hide();
						$( target_div ).hide();

						// TODO - CHANGE FOCUS / ACTIVE.
						option_index = $( '#apl-multiselect-' + k_pt_slug + ' option:selected' ).index();
						if ( 0 === option_index ) {
							option_index = $( '#apl-multiselect-' + k_pt_slug + ' option' ).next( ':selected' ).index();
						}
						if ( -1 !== option_index ) {
							// -1 since req checkbox isn't a tab.
							$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'option', 'active', ( option_index - 1 ) );
						}

						$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'refresh' );
						$( target_div ).hide();
					}
				}
			});

			//$( 'li.apl-chk-req-taxonomies' ).attr( 'title', help_attr_title2  );

			$( '#apl-multiselect-' + k_pt_slug ).multiselect( 'refresh' );
		});
	}

	/**
	 * Initialized Checkbox Term Events
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_chkbox_terms() {
		$.each( post_tax, function( k1_pt_slug, v1_pt_value ) {
			$.each( v1_pt_value.tax_arr, function( k2_index, v2_tax_slug ) {
				//apl_chk_terms_req-XXX-YYY
				$( '#apl_chk_terms_req-' + k1_pt_slug + '-' + v2_tax_slug ).change( function( event ) {
					// There may be no need.
				});

				//apl_chk_terms_dynamic-XXX-YYY
				$( '#apl_chk_terms_dynamic-' + k1_pt_slug + '-' + v2_tax_slug ).change( function( event ) {
					if ( this.checked ) {
						if ( $( '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-any' ).prop( 'checked' ) ) {
							$( this ).prop( 'checked', false );
						}
					} else {
						// Nothing
					}
				});

				//term-any-category-any
				// 'Any' TERM CHANGE.
				$( '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-any' ).change( function( event ) {
					if ( this.checked ) {
						$( '#apl_chk_terms_dynamic-' + k1_pt_slug + '-' + v2_tax_slug ).prop( 'checked', false );
						$.each( tax_terms[ v2_tax_slug ], function( k3_index, v3_term_id ) {
							var t1_input = '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-' + v3_term_id;
							$( t1_input ).prop( 'checked', false );
						});
					} else {
						$( this ).prop( 'checked', true );
					}
				});

				var init_term_checked = false;

				$.each( tax_terms[ v2_tax_slug ], function( k3_index, v3_term_id ) {
					//term-XX-YY-ZZ
					var t1_input = '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-' + v3_term_id;

					if ( $( t1_input ).prop( 'checked' ) ) {
						init_term_checked = true;
					}

					// TERMS CHANGE.
					// term-any-category-TERM_ID.
					$( t1_input ).change( function( event ) {
						if ( this.checked ) {
							$( '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-any' ).prop( 'checked', false );
						} else { // unchecked
							var other_checked = false;
							$.each( tax_terms[ v2_tax_slug ], function( k4_index, v4_term_id ) {
								var t2_input = '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-' + v4_term_id;
								if ( $( t2_input ).prop( 'checked' ) && t1_input !== t2_input ) {
									other_checked = true;
								}
							});

							// Add checked to 'any'
							if ( ! other_checked ) {
								var t2_input_any = '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-any';
								$( t2_input_any ).prop( 'checked', true );
							}
						}
					});// End terms .change().
				});// End tax_terms[post_type].

				if ( ! init_term_checked ) {
					$( '#term-' + k1_pt_slug + '-' + v2_tax_slug + '-any' ).prop( 'checked', true );
				}
			});
		});
	}

	/**
	 * Initialize List Amount Spinner
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_spinner_amount() {
		$( '#apl_spinner_posts_per_page' ).spinner({
			disabled: false,
			min: -1,
			max:9999,

			change: function( event, ui ) {
				$( '#apl_slider_handle_posts_per_page' ).text( ui.value );
				$( '#apl_slider_posts_per_page' ).val( ui.value );
			},
			spin: function( event, ui ) {
				$( '#apl_slider_handle_posts_per_page' ).text( ui.value );
				$( '#apl_slider_posts_per_page' ).slider( 'value', ui.value );
			}
		});
	}

	/**
	 * Initialize List Amount Slider
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_slider_amount() {
		$( '#apl_slider_posts_per_page' ).slider({
			value: $( '#apl_spinner_posts_per_page' ).spinner( 'value' ),
			min: -1,
			max: 100,

			create: function() {
				$( '#apl_slider_handle_posts_per_page' ).text( $( this ).slider( 'value' ) );
			},
			slide: function( event, ui ) {
				$( '#apl_slider_handle_posts_per_page' ).text( ui.value );
				$( '#apl_spinner_posts_per_page' ).val( ui.value );
			}
		});
	}

	/**
	 * Initialize Order By Selectmenu
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_selectmenu_order_by() {
		$( '#apl_selectmenu_order_by' ).selectmenu({
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			},
			change: function( event, ui ) {
				if ( 'none' === ui.item.value ) {
					$( '#apl_selectmenu_order' ).selectmenu( 'option', 'disabled', true );
				} else {
					$( '#apl_selectmenu_order' ).selectmenu( 'option', 'disabled', false );
				}
			}
		});
	}

	/**
	 * Initialize Order Selectmenu
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_selectmenu_order() {
		$( '#apl_selectmenu_order' ).selectmenu({

			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
				if ( 'none' === $( '#apl_selectmenu_order_by' ).find( ':selected' ).val() ) {
					$( this ).selectmenu( 'disable' );
				}
			}
		});
	}

	/**
	 * Initialize Author Operators Selectmenu
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_selectmenu_author_operator() {
		$( '#apl_selectmenu_author__bool' ).selectmenu({
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			},
			change: function( event, ui ) {
				if ( 'none' === ui.item.value ) {
					$( '#apl_multiselect_author__in' ).multiselect( 'uncheckAll' );
					$( '#apl_multiselect_author__in' ).multiselect( 'disable' );
				} else {
					$( '#apl_multiselect_author__in' ).multiselect( 'enable' );
				}
			}
		});
	}

	/**
	 * Initialize Author IDs Multiselect
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_multiselect_author_ids() {
		$( '#apl_multiselect_author__in' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			disable: true,
			header: false,
			noneSelectedText: trans.author_ids_noneSelectedText,
			selectedText: trans.author_ids_selectedText,
			selectedList: 1,
			height: 300,
			//minWidth:100,
			menuWidth:'auto',

			create: function( event, ui ) {
				if ( 'none' === $( '#apl_selectmenu_author__bool' ).find( ':selected' ).val() ) {
					$( this ).multiselect( 'disable' );
				}
			},
			click: function( event, ui ) {}
		});
	}

	/**
	 * Initialize Post Status 1 Multiselect
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_multiselect_post_status_1() {
		$( '#apl_multiselect_post_status_1' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			header: false,
			noneSelectedText: trans.post_status_1_noneSelectedText,
			selectedText: trans.post_status_1_selectedText,
			selectedList: 1,
			height: 300,
			/* minWidth:'45%', // admin.css button.apl-ui-ms-filter-2-row */
			menuWidth:150,

			click: function( event, ui ) {
				// Manually update element for logical operations.
				$( '#apl_multiselect_post_status_1 option[value="' + ui.value + '"]' ).prop( 'selected', ui.checked );

				var opts = $( this ).children();

				// If clicked is 'Any'.
				if ( 'none' === ui.value ) {
					if ( true === ui.checked ) {
						$( opts[ '1' ] ).prop( 'selected', false );
						$( opts[ '2' ] ).prop( 'selected', false );
						$( opts[ '3' ] ).prop( 'selected', false );

						$( '#apl_multiselect_post_status_2' ).multiselect( 'uncheckAll' );
						$( '#apl_multiselect_post_status_2' ).multiselect( 'disable' );
					} else {
						$( opts[ '0' ] ).prop( 'selected', true );
					}
				} else if ( 'any' === ui.value ) {
					if ( true === ui.checked ) {
						$( opts[ '0' ] ).prop( 'selected', false );
						$( opts[ '2' ] ).prop( 'selected', false );
						$( opts[ '3' ] ).prop( 'selected', false );

						$( '#apl_multiselect_post_status_2' ).multiselect( 'uncheckAll' );
						$( '#apl_multiselect_post_status_2' ).multiselect( 'disable' );
					} else {
						$( opts[ '0' ] ).prop( 'selected', true );
					}
				} else {
					// If others are checked, then uncheck 'Any' and enable Post_Status_2.
					if ( true === ui.checked ) {
						var opt2_val = $( opts[ '2' ] ).prop( 'selected' );
						var opt3_val = $( opts[ '3' ] ).prop( 'selected' );

						if ( ! opt2_val || ! opt3_val ) {
							$( '#apl_multiselect_post_status_2' ).find( 'option[value="publish"]' ).prop( 'selected', true );
							$( '#apl_multiselect_post_status_2' ).multiselect( 'enable' );
						}

						$( opts[ '0' ] ).prop( 'selected', false );
						$( opts[ '1' ] ).prop( 'selected', false );
					} else {
						// If both are unchecked, then check 'Any' and disable Post_Status_2.
						var opt2_val = $( opts[ '2' ] ).prop( 'selected' );
						var opt3_val = $( opts[ '3' ] ).prop( 'selected' );

						if ( ! opt2_val && ! opt3_val ) {
							$( opts[ '0' ] ).prop( 'selected', true );

							$( '#apl_multiselect_post_status_2' ).multiselect( 'uncheckAll' );
							$( '#apl_multiselect_post_status_2' ).multiselect( 'disable' );
						}
					}
				}// End if().

				$( '#apl_multiselect_post_status_1' ).multiselect( 'refresh' );
				$( '#apl_multiselect_post_status_2' ).multiselect( 'refresh' );
			}
		});
	}

	/**
	 * Initialize Post Status 2 Multiselect
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_multiselect_post_status_2() {
		$( '#apl_multiselect_post_status_2' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			disabled: true,
			header: false,
			noneSelectedText: trans.post_status_2_noneSelectedText,
			selectedText: trans.post_status_2_selectedText,
			selectedList: 1,
			height: 300,
			minWidth:'45%',
			menuWidth:150,

			create: function ( event, ui ) {
				$( '#apl_multiselect_post_status_1' ).find( ':selected' ).each( function( index, element ) {
					if ( 'none' === $( element ).val() || 'any' === $( element ).val() ) {
						$( '#apl_multiselect_post_status_2' ).multiselect( 'disable' );
						$( '#apl_multiselect_post_status_2' ).multiselect( 'refresh' );
					}
				});
			},
			click: function( event, ui ) {}
		});
	}

	/**
	 * Initialize Perms Selectmenu
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_selectmenu_perm() {
		$( '#apl_selectmenu_perm' ).selectmenu({
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			}
		});
	}

	/**
	 * Initialize Offset Spinner
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_spinner_offset() {
		$( '#apl_spinner_offset' ).spinner({
			disabled: false,
			min: 0,
			max:9999,

			change: function( event, ui ) {
				$( '#apl_slider_handle_offset' ).text( ui.value );
				$( '#apl_slider_offset' ).val( ui.value );
			},
			spin: function( event, ui ) {
				$( '#apl_slider_handle_offset' ).text( ui.value );
				$( '#apl_slider_offset' ).slider( 'value', ui.value );
			}
		});
	}

	/**
	 * Initialize Offset Slider
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_slider_offset() {
		$( '#apl_slider_offset' ).slider({
			value: $( '#apl_spinner_offset' ).spinner( 'value' ),
			min: 0,
			max: 100,

			create: function() {
				$( '#apl_slider_handle_offset' ).text( $( this ).slider( 'value' ) );
			},
			slide: function( event, ui ) {
				$( '#apl_slider_handle_offset' ).text( ui.value );
				$( '#apl_spinner_offset' ).val( ui.value );
			}
		});
	}

	/**
	 * Initialize Empty Message Textacrea
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function init_textarea_empty_message() {
		$( '#apl_empty_message_enable' ).change( function( event, ui ) {
			if ( this.checked ) {
				$( '#apl_textarea_empty_message' ).show();
			} else {
				$( '#apl_textarea_empty_message' ).hide();
			}
		});
	}

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
	 * Initialize Dialogs
	 *
	 * @since 0.4.0
	 *
	 * @returns {undefined}
	 */
	function apl_init_dialogs() {
		$( '#info-shortcodes' ).click( function() {
			$( '#d-shortcodes' ).dialog({
				width: '32%',
				height: 450,
				open: function () {
					$( this ).scrollTop(0);
				}
			});
		});
	}

})(jQuery);
