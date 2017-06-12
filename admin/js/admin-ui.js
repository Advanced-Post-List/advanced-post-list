/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery( document ).ready( function($) {
	var post_tax  = apl_admin_ui_local.post_tax;
	var tax_terms = apl_admin_ui_local.tax_terms;
	var trans     = apl_admin_ui_local.trans;

	init_chkbox_post_type_toggle_tabs();

	init_tabs_post_type_taxonomies();

	init_multiselect_post_type_taxonomies();
	
	init_spinner_amount();
	
	init_slider_amount();
	
	init_selectmenu_order_by();
	
	init_selectmenu_order();
	
	init_selectmenu_author_operator();
	
	init_multiselect_author_ids();
	
	init_multiselect_post_status_1();
	
	init_multiselect_post_status_2();
	
	init_selectmenu_perm();

	function init_chkbox_post_type_toggle_tabs() {
		$( '#apl-toggle-any' ).prop( 'checked', true );
		$.each( post_tax, function( k_post_type_slug, v_taxonomy_arr ) {
			if ( 'any' !== k_post_type_slug ) {
				$( '#apl-filter-' + k_post_type_slug ).hide();
			}

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
				} else {
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
				$( '#apl-tabs-' + k_post_type_slug + '-type' ).tabs( 'refresh' );
				$( '#apl-tabs-' + k_post_type_slug + '-taxonomies' ).tabs( 'refresh' );
			});
			
		});
	}

	function init_tabs_post_type_taxonomies () {
		$.each( post_tax, function( k_post_type_slug, v_taxonomy_arr ) {
			

			var first_tax = true;
			$.each( v_taxonomy_arr.tax_arr, function( k_index, v_tax_slug ) {
				var target_tab = '#apl-t-li-' + k_post_type_slug + '-' + v_tax_slug;
				var target_div = '#apl-t-div-' + k_post_type_slug + '-' + v_tax_slug;

				if ( ! first_tax ) {
					$( target_tab ).hide();
					$( target_div ).hide();
				}

				first_tax = false;
			});
			$( '#apl-tabs-' + k_post_type_slug +'-taxonomies' ).tabs({
				heightStyle: "fill"
			});
			$( '#apl-tabs-' + k_post_type_slug +'-type' ).tabs({
				heightStyle: "fill"
			});
			
		});
	}

	function init_multiselect_post_type_taxonomies() {
		$.each( post_tax, function( k_pt_slug, v_tax_arr ) {
			$( '#apl-multiselect-' + k_pt_slug ).multiselect({
				header: false,
				noneSelectedText: trans.tax_noneSelectedText,
				selectedText: trans.tax_selectedText,
				selectedList: 2,
				height: 150,
				minWidth:333,
				menuWidth:333,

				click: function( event, ui ) {
					var target_tab = '#apl-t-li-' + ui.value;
					var target_div = '#apl-t-div-' + ui.value;

					if ( true == ui.checked ) {
						$( target_tab ).show();
						$( target_div ).show();

						$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'refresh' );
					} else {
						$( target_tab ).hide();
						$( target_div ).hide();

						$( '#apl-tabs-' + k_pt_slug + '-taxonomies' ).tabs( 'refresh' );
						$( target_div ).hide();
					}
				},
			});

			$( '#apl-multiselect-' + k_pt_slug ).multiselect( 'widget' ).find(':checkbox[value="' + k_pt_slug + '-' + v_tax_arr.tax_arr[ 0 ] + '"]').prop('checked', true);

		});
	}
	
	function init_spinner_amount() {
		$( '.apl-spinner-amount' ).spinner({
			min: -1,
			change: function( event, ui ) {},
			spin: function( event, ui ) {}
		});
	}
	
	function init_slider_amount() {
		$( '.apl-slider-amount' ).slider({
			value: $( '.apl-spinner-amount' ).spinner( 'value' ),
			min: -1,
			max: 101,
			create: function() {
				//$( '#apl_list_amount_slider' ).text( $( '#apl_list_amount_spinner' ).spinner( "value" ) );
				$( '.apl-slider-handle-amount' ).text( $( this ).slider( 'value' ) );
			},
			slide: function( event, ui ) {
				$( '.apl-slider-handle-amount' ).text( ui.value );
			}
		});
	}
	
	function init_selectmenu_order_by() {
		$( '.apl-selectmenu-order-by' ).selectmenu({
			
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			}
//			change: function( event, ui ) {
//				
//			}
		});
	}

	function init_selectmenu_order() {
		$( '.apl-selectmenu-order' ).selectmenu({
			disabled: true,
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			}
		});
	}
	
	function init_selectmenu_author_operator() {
		$( '.apl-selectmenu-author-operator' ).selectmenu({
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			}
			
		});
	}
	
	function init_multiselect_author_ids() {
		$( '.apl-multiselect-author-ids' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			header: false,
			noneSelectedText: 'string',
			selectedText: 'string',
			selectedList: 1,
			height: 200,
			minWidth:100,
			menuWidth:100,
			click: function( event, ui ) {
				
			}
		});
	}
	
	function init_multiselect_post_status_1() {
		$( '.apl-multiselect-post-status-1' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			header: false,
			noneSelectedText: 'string',
			selectedText: 'string',
			selectedList: 1,
			height: 150,
			/* minWidth:'45%', // admin.css button.apl-ui-ms-filter-2-row */
			menuWidth:150,
			click: function( event, ui ) {
				
			}
		})
	}
	
	function init_multiselect_post_status_2() {
		$( '.apl-multiselect-post-status-2' ).multiselect({
			classes: 'apl-ui-ms apl-ui-ms-filter-2-row',
			header: false,
			noneSelectedText: 'string',
			selectedText: 'string',
			selectedList: 1,
			height: 300,
			minWidth:'45%',
			menuWidth:150,
			click: function( event, ui ) {
				
			}
		})
	}
	
	function init_selectmenu_perm() {
		$( '.apl-selectmenu-perm' ).selectmenu({
			create: function( event, ui ) {
				$( '#' + this.id + '-button' ).addClass( 'apl-ui-ms-filter-2-row' );
			},
			change: function( event, ui ) {
				
			}
		});
	}
	
	
});










