<?php
/**
 * APL Updater Class
 *
 * Updater object to Advanced Post List
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.3.0
 */

/**
 * APL Updater
 *
 * Updates APL's database.
 *
 * @since 0.3.0
 */
class APL_Updater {

	/**
	 * Even Update Occurred.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var boolean 
	 */
	public $update_occurred = false;

	/**
	 * Stores multiple plugin settings within an array.
	 *
	 * @since 0.3.0
	 * @access public
	 * @var array
	 */
	public $options;

	/**
	 * Stores the preset post lists.
	 *
	 * @since 0.3.0
	 * @access private
	 * @var object
	 */
	public $preset_db;

	/**
	 * Stores the APL_Post_List Class Data Format.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array
	 */
	
	public $apl_post_list_arr = array();

	/**
	 * Stores the APL_Design Class Data Format.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array
	 */
	public $apl_design_arr = array();

	/**
	 * Updater Constructor.
	 *
	 * Constructor for the Updater Class.
	 *
	 * @since 0.3.0
	 *
	 * @param string $old_version Version number the plugin is currently operating at.
	 * @param object $preset_db Optional. Current/Old Preset Database Object. Default null.
	 * @param array  $options Optional. The plugin settings stored.
	 * @return void
	 */
	public function __construct( $old_version, $update_items ) {
		if ( empty( $old_version ) || empty ( $update_items ) ) {
			return new WP_Error( 'apl_updater', __( 'APL Updater Class Error: empty version and/or empty APL Options & APL Preset Db is being passed to the Updater Class.', 'advanced-post-list' ) );
			return;
		}

		// INIT - FILL IN VARIABLES.
		$this->options = array();
		if ( ! empty( $update_items['options'] ) ) {
			$this->options = $update_items['options'];
		}

		$this->preset_db = new stdClass();
		if ( ! empty( $update_items['preset_db'] ) ) {
			$this->preset_db = $update_items['preset_db'];
		}

		$this->apl_post_list_arr = array();
		if ( ! empty( $update_items['apl_post_list_arr'] ) ) {
			$this->apl_post_list_arr = $update_items['apl_post_list_arr'];
		}

		$this->apl_design_arr = array();
		if ( ! empty( $update_items['apl_design_arr'] ) ) {
			$this->apl_design_arr = $update_items['apl_design_arr'];
		}

		/* **** UPGRADES **** */
		// CONVERT FROM KALIN'S POST LIST TO BASE.
		if ( 'kalin' === $old_version ) {
			$this->APL_convert_kalin_to_base();
			$old_version = '0.1.0';
		}
		if ( version_compare( APL_VERSION, $old_version, '>' ) ) {
			// UPGRADE FROM BASE TO "0.3.X".
			if ( version_compare( '0.3.a1', $old_version, '>' ) ) {
				$this->APL_upgrade_to_03a1();
			}
			// VERSION 0.3.b5
			if ( version_compare( '0.3.b5', $old_version, '>' ) ) {
				$this->APL_upgrade_to_03b5();
			}
			// VERSION 0.4.0
			if ( version_compare( '0.4.b1', $old_version, '>' ) ) {
				if ( ! empty( $this->preset_db ) ) {
					$new_options = $this->upgrade_options_03b5_to_040( $this->options );
					$this->options = $new_options;

					$new_preset_arr = $this->upgrade_preset_db_03b5_to_040( $this->preset_db );
					$this->apl_post_list_arr  = $new_preset_arr['apl_post_list'];
					$this->apl_design_arr     = $new_preset_arr['apl_design'];
				}
			}

			$this->update_occurred = true;
		}

		// This is likely to never happen, but just in case...
		/* **** DOWNGRADES CONCEPT ( FUTURE REFERENCE ) **** */
		// DOWNGRADE FROM 0.3.X TO BASE.
		/*
		if ( version_compare( '0.3.b5', $oldversion, '<' ) ) {
			$this->APL_downgrade_from_03b5();
		}
		if ( version_compare( '0.3.a1', $oldversion, '<' ) ) {
			$this->APL_downgrade_from_03a();
		}
		*/

		// Finalize the APL_Post_List & APL_Design to the proper class format.
		// Does not save since it also handles imported data.
		if ( $this->update_occurred ) {
			$new_items_arr = $this->the_finisher( $this->apl_post_list_arr, $this->apl_design_arr );

			$this->apl_post_list_arr  = $new_items_arr['apl_post_list_arr'];
			$this->apl_design_arr     = $new_items_arr['apl_design_arr'];

			// Fallback option if things go wrong.
			update_option( 'apl_update_items_backup', $update_items );
		}
	}

	/**
	 * Convert Kalin Plugin to APL.
	 *
	 * Converts data from the previous plugin to APL configured data.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @return void.
	 */
	private function APL_convert_kalin_to_base() {
		$tmp_preset_array = json_decode( $this->preset_db['preset_arr'] );

		$this->preset_db = new APL_Preset_Db();

		foreach ( $tmp_preset_array as $key => $value ) {
			$this->preset_db->_preset_db->$key = new stdClass();
			$this->preset_db->_preset_db->$key->reset_to_version( '0.1.0' );

			$this->preset_db->_preset_db->$key = $this->APL_convert_preset_kalin_to_base( $value );
		}
	}

	/**
	 * Convert Kalin's preset to APL's preset.
	 *
	 * Converts individual data from the previous plugin to APL Preset data.
	 *
	 * @since 0.3.0
	 * @access private
	 *
	 * @param object $old_preset Old variable used in Kalin's Post List.
	 * @return object Base Preset structure to APL.
	 */
	private function APL_convert_preset_kalin_to_base( $old_preset ) {
		//$return_preset = new APL_Preset();
		$return_preset = new stdClass();
		
		$return_preset->reset_to_version( '0.1.0' );

		$return_preset->_catsSelected        = $old_preset->categories;
		$return_preset->_tagsSelected        = $old_preset->tags;
		$return_preset->_postType            = $old_preset->post_type;
		$return_preset->_listOrderBy         = $old_preset->orderby;
		$return_preset->_listOrder           = $old_preset->order;
		$return_preset->_listAmount          = $old_preset->numberposts;
		$return_preset->_before              = $old_preset->before;
		$return_preset->_content             = $old_preset->content;
		$return_preset->_after               = $old_preset->after;
		$return_preset->_postExcludeCurrent  = $old_preset->excludeCurrent;
		$return_preset->_catsInclude         = $old_preset->includeCats;
		$return_preset->_tagsInclude         = $old_preset->includeTags;

		if ( isset( $old_preset->post_parent ) ) {
			$return_preset->_postParent      = $old_preset->post_parent;
		}

		if ( isset( $old_preset->requireAllCats ) ) {
			$return_preset->_catsRequired    = $old_preset->requireAllCats;
		}
		if ( isset( $old_preset->requireAllTags ) ) {
			$return_preset->_tagsRequired    = $old_preset->requireAllTags;
		}

		return $return_preset;
	}

	/**
	 * Upgrade to "0.3.a1".
	 *
	 * Upgrade APL to 0.3.a1.
	 *
	 * @since 0.3.a1
	 * @access private
	 *
	 * @return void
	 */
	private function APL_upgrade_to_03a1() {
		if ( ! empty( $this->options ) ) {
			$this->options = $this->APL_upgrade_options_base_to_03a1( $this->options );
		}
		if ( ! empty( $this->preset_db ) ) {
			$this->preset_db = $this->APL_upgrade_presetDbObj_base_to_03a1( $this->preset_db );
		}
	}

	/**
	 * Upgrade Options to 0.3.a1.
	 *
	 * Upgrades Options from Base to "0.3.a1".
	 *
	 * @since 0.3.a1
	 * @access private
	 *
	 * @param type $old_options Old/base variable structure.
	 * @return array New option structure.
	 */
	private function APL_upgrade_options_base_to_03a1( $old_options ) {
		// INIT/DEFAULTS.
		$return_options = array();
		$return_options['version']          = '0.3.a1';
		$return_options['preset_db_names']  = array( 'default' );
		$return_options['delete_core_db']   = true;
		$return_options['error']            = '';
		//////// UPDATE/ADD ADMIN OPTIONS ////////
		$return_options['jquery_ui_theme']  = 'overcast';

		foreach ( $return_options as $key => &$value ) {
			if ( ! empty( $old_options[ $key ] ) ) {
				$value = $old_options[ $key ];
			}
		}

		return $return_options;
	}

	/**
	 * Upgrade Preset Database to 0.3.a1.
	 *
	 * Upgrade Preset Database Object to "0.3.a1".
	 *
	 * @since 0.3.a1
	 * @access private
	 *
	 * @param object $old_preset old Preset Data.
	 * @return object New Preset Database.
	 */
	private function APL_upgrade_presetDbObj_base_to_03a1( $old_preset ) {
		$return_preset_db = new APL_Preset_Db();
		$return_preset_db->reset_to_version( '0.3.a1' );

		foreach ( $return_preset_db as $key1 => $value1 ) {
			if ( '_preset_db' === $key1 && ! empty( $old_preset->$key1 ) ) {
				foreach ( $old_preset->_preset_db as $key2 => $value2 ) {
					$return_preset_db->_preset_db->$key2 = $this->APL_upgrade_preset_base_to_03a1( $value2 );
				}
			} elseif ( ! empty( $old_preset->$key ) ) {
				$return_preset_db->$key1 = $old_preset->$key1;
			}
		}
		return $return_preset_db;
	}

	/**
	 * Upgrade preset to 0.3.a1.
	 *
	 * Upgrades Preset object to "0.3.a1".
	 *
	 * @since 0.3.a1
	 * @access private
	 *
	 * @param object $old_preset Old Preset data structure.
	 * @return object New Preset object.
	 */
	private function APL_upgrade_preset_base_to_03a1( $old_preset ) {
		$return_preset = new APL_Preset();
		$return_preset->reset_to_version( '0.3.a1' );

		// Step 4.
		//// SET PARENT SETTING ////
		if ( 'current' === $old_preset->_postParent ) {
			$return_preset->_postParent[0] = '-1';
		} elseif ( 'None' !== $old_preset->_postParent && '' !== $old_preset->_postParent ) {
			$return_preset->_postParent[0] = $old_preset->_postParent;
		}

		// Step 5.
		//// SET POST TYPES & TAXONOMIES SETTINGS ////
		if ( '' !== $old_preset->_catsSelected ) {
			$return_preset->_postTax->post->taxonomies->category->require_taxonomy = false;
			$return_preset->_postTax->post->taxonomies->category->require_terms = true;
			if ( 'false' === $old_preset->_catsRequired ) {
				$return_preset->_postTax->post->taxonomies->category->require_terms = false;
			}
			$return_preset->_postTax->post->taxonomies->category->include_terms = true;
			if ( 'false' === $old_preset->_catsInclude ) {
				$return_preset->_postTax->post->taxonomies->category->include_terms = false;
			}
			$terms = explode( ',', $old_preset->_catsSelected );
			$i = 0;
			foreach ( $terms as $term ) {
				$return_preset->_postTax->post->taxonomies->category->terms[ $i ] = intval( $term );
				$i++;
			}
			unset( $return_preset->_postTax->post->taxonomies->category->terms[ ( $i - 1 ) ] );
		}

		if ( '' !== $old_preset->_tagsSelected ) {
			$return_preset->_postTax->post->taxonomies->post_tag->require_taxonomy = false;
			$return_preset->_postTax->post->taxonomies->post_tag->require_terms = true;
			if ( 'false' === $old_preset->_tagsRequired ) {
				$return_preset->_postTax->post->taxonomies->post_tag->require_terms = false;
			}
			$return_preset->_postTax->post->taxonomies->post_tag->include_terms = true;
			if ( 'false' === $old_preset->_tagsInclude ) {
				$return_preset->_postTax->post->taxonomies->post_tag->include_terms = false;
			}
			$terms = explode( ',', $old_preset->_tagsSelected );
			$i = 0;
			foreach ( $terms as $term ) {
				$return_preset->_postTax->post->taxonomies->post_tag->terms[ $i ] = intval( $term );
				$i++;
			}
			unset( $return_preset->_postTax->post->taxonomies->post_tag->terms[ ( $i - 1 ) ] );
		}

		// Step 6.
		//// SET THE LIST AMOUNT ////
		$return_preset->_listAmount  = intval( $old_preset->_listAmount );

		//// SET THE ORDER AND ORDERBY SETTINGS ////
		$return_preset->_listOrder   = $old_preset->_listOrder;
		$return_preset->_listOrderBy = $old_preset->_listOrderBy;

		//// SET THE POST STATUS AS THE DEFAULT ////
		////  SETTING                           ////
		$return_preset->_postStatus  = 'publish';

		//// SET THE EXCLUDE CURRENT POST SETTING ////
		$return_preset->_postExcludeCurrent = true;
		if ( 'false' === $old_preset->_postExcludeCurrent ) {
			$return_preset->_postExcludeCurrent = false;
		}

		//// SET THE STYLE (BEFORE/CONTENT/AFTER) ////
		////  CONTENT SETTINGS                    ////
		$return_preset->_before   = $old_preset->_before;
		$return_preset->_content  = $old_preset->_content;
		$return_preset->_after    = $old_preset->_after;

		return $return_preset;
	}

	/**
	 * Upgrade APL to 0.3.b5.
	 *
	 * Upgrades Plugin to "0.3.b5".
	 *
	 * @since 0.3.b5
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @return void
	 */
	private function APL_upgrade_to_03b5() {
		if ( ! empty( $this->options ) ) {
			$this->options = $this->APL_upgrade_options_03a1_to_03b5( $this->options );
		}
		if ( ! empty( $this->preset_db ) ) {
			$this->preset_db = $this->APL_upgrade_return_preset_db_03a1_to_03b5( $this->preset_db );
		}
	}

	/**
	 * Upgrade Options from 0.3.a1 to 0.3.b5.
	 *
	 * Upgrades APL's options "0.3.a1" to "0.3.b5".
	 *
	 * @since 0.3.b5
	 * @access private
	 *
	 * @param array $old_options Old Option data.
	 * @return array New Option structure.
	 */
	private function APL_upgrade_options_03a1_to_03b5( $old_options ) {
		// INIT/DEFAULTS.
		$return_options = array();
		$return_options['version']           = '0.3.b5';
		$return_options['preset_db_names']   = array( 'default' );
		$return_options['delete_core_db']    = true;
		$return_options['error']             = '';
		$return_options['jquery_ui_theme']   = 'overcast';
		//////// UPDATE/ADD ADMIN OPTIONS ////////
		$return_options['default_exit']      = false;
		$return_options['default_exit_msg']  = '<p>Sorry, but no content is available at this time.</p>';

		// OVERWRITE DATA FROM THE ORIGINAL.
		foreach ( $return_options as $key => &$value ) {
			if ( ! empty( $old_options[ $key ] ) ) {
				$value = $old_options[ $key ];
			}
		}

		return $return_options;
	}

	/**
	 * Upgrade Preset Database from 0.3.a1 to 0.3.b5.
	 *
	 * Upgrades the Preset Database from "0.3.a1" to "0.3.b5".
	 *
	 * @since 0.3.b5
	 * @access private
	 *
	 * @param object $old_preset Old Preset Database.
	 * @return object New Preset structure.
	 */
	private function APL_upgrade_return_preset_db_03a1_to_03b5( $old_preset ) {
		$return_preset_db = new APL_Preset_Db();
		$return_preset_db->reset_to_version( '0.3.b5' );

		foreach ( $return_preset_db as $key1 => $value1 ) {
			if ( '_preset_db' === $key1 && ! empty( $old_preset->$key1 ) ) {
				foreach ( $old_preset->_preset_db as $key2 => $value2 ) {
					$return_preset_db->_preset_db->$key2 = $this->APL_upgrade_preset_03a1_to_03b5( $value2 );
				}
			} elseif ( ! empty( $old_preset->$key1 ) ) {
				$return_preset_db->$key1 = $old_preset->$key1;
			}
		}

		return $return_preset_db;
	}

	/**
	 * Upgrade Preset from 0.3.a1 to 0.3.b5.
	 *
	 * Upgrades Preset from "0.3.a1" to "0.3.b5".
	 *
	 * @since 0.3.b5
	 * @access private
	 *
	 * @param object $old_preset Old Preset Data.
	 * @return object New Preset structure.
	 */
	private function APL_upgrade_preset_03a1_to_03b5( $old_preset ) {
		$return_preset = new APL_Preset();
		$return_preset->reset_to_version( '0.3.b5' );

		// Step 4
		//// SET PARENT SETTING ////
		if ( isset( $old_preset->_postParent ) ) {
			$return_preset->_postParents = $old_preset->_postParent;
		}
		if ( isset( $old_preset->_postTax ) ) {
			$return_preset->_postTax = $old_preset->_postTax;
		}
		if ( isset( $old_preset->_listAmount ) ) {
			$return_preset->_listCount = $old_preset->_listAmount;
		}
		if ( isset( $old_preset->_listOrderBy ) ) {
			$return_preset->_listOrderBy = $old_preset->_listOrderBy;
		}
		if ( isset( $old_preset->_listOrder ) ) {
			$return_preset->_listOrder = $old_preset->_listOrder;
		}
		if ( 'private' === $old_preset->_postStatus ) {
			$return_preset->_postVisibility = array( 'private' );
		}
		if ( isset( $old_preset->_postStatus ) && 'private' !== $old_preset->_postStatus ) {
			$return_preset->_postStatus = array( $old_preset->_postStatus );
		}

		$return_preset->_userPerm               = (string) 'readable';
		$return_preset->_postAuthorOperator     = (string) 'none';
		$return_preset->_postAuthorIDs          = (array) array();
		$return_preset->_listIgnoreSticky       = (bool) false;

		$return_preset->_listExcludeCurrent     = (bool) true;
		if ( isset( $old_preset->_listExcludeCurrent ) ) {
			$return_preset->_listExcludeCurrent = array( $old_preset->_postExcludeCurrent );
		}

		$return_preset->_listExcludeDuplicates  = (bool) false;
		$return_preset->_listExcludePosts       = array();
		$return_preset->_exit                   = (string) '';

		$return_preset->_before                 = (string) '';
		if ( isset( $old_preset->_before ) ) {
			$return_preset->_before             = $old_preset->_before;
		}

		$return_preset->_content                = (string) '';
		if ( isset( $old_preset->_content ) ) {
			$return_preset->_content            = $old_preset->_content;
		}

		$return_preset->_after                  = (string) '';
		if ( isset( $old_preset->_after ) ) {
			$return_preset->_after              = $old_preset->_after;
		}

		return $return_preset;
	}

	/**
	 * Upgrade APL_Options to 0.4.0
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $options
	 * @return array New APL_Options
	 */
	private function upgrade_options_03b5_to_040( $options ) {
		// SET DEFAULTS MANUALLY.
		$rtn_options = array(
			'version'               => '',
			'delete_core_db'        => false,
			'default_empty_enable'  => false,
			'default_empty_output'  => '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>',
		);

		$rtn_options['version'] = APL_VERSION;

		$rtn_options['delete_core_db']        = $options['delete_core_db']   ?: $rtn_options['delete_core_db'];
		$rtn_options['default_empty_enable']  = $options['default_exit']     ?: $rtn_options['default_empty_enable'];
		$rtn_options['default_empty_output']  = $options['default_exit_msg'] ?: $rtn_options['default_empty_output'];

		return $rtn_options;
	}
	
	/**
	 * Upgrade Preset Database from 0.3.b5 to 0.4.0.
	 *
	 * Upgrades the Preset Database from "0.3.b5" to "0.4.0".
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @param object $old_preset Old Preset Database.
	 * @return object New Preset structure.
	 */
	private function upgrade_preset_db_03b5_to_040( $old_preset_db ) {
		$rtn_new_preset_arr = array(
			'apl_post_list'  => array(),
			'apl_design'     => array(),
		);

		foreach ( $old_preset_db as $k1_db_index => $v1_preset_db_var ) {
			if ( '_preset_db' === $k1_db_index && ! empty( $v1_preset_db_var ) ) {
				foreach ( $v1_preset_db_var as $k2_preset_slug => $old_value ) {
					// Replace with stdClass
					//$new_post_list = new APL_Post_List( $preset_key );
					$new_post_list = new stdClass();

					// SET DEFAULTS MANUALLY
					$new_post_list->id                   = 0;
					$new_post_list->title                = '';
					//$new_post_list->slug               = '';
					$new_post_list->post_type            = array( 'any' );
					$new_post_list->tax_query            = array();
					$new_post_list->post_parent__in      = array();
					$new_post_list->post_parent_dynamic  = array();
					$new_post_list->posts_per_page       = 5;
					$new_post_list->order_by             = 'none';
					$new_post_list->order                = 'DESC';
					$new_post_list->post_status          = 'none';
					$new_post_list->perm                 = 'none';
					$new_post_list->author__bool         = 'none';
					$new_post_list->author__in           = array();
					$new_post_list->ignore_sticky_posts  = true;
					$new_post_list->post__not_in         = array();
					$new_post_list->pl_exclude_current   = true;
					$new_post_list->pl_exclude_dupes     = false;
					$new_post_list->pl_apl_design        = '';

					// ADD VALUES.
					$new_post_list->title  = sanitize_key( $k2_preset_slug )  ?: $new_post_list->title;
					$new_post_list->slug   = $new_post_list->title            ?: $k2_preset_slug;

					// POST TYPES & TAX_QUERY
					// array <= (object).
					// array <= (object).
					$tmp_post_type_arr = array();
					$tmp_tax_query_arr = array();
					if ( ! empty( $old_value->_postTax ) ) {
						// FOR POST TYPES SELECTED
						foreach( $old_value->_postTax as $k3_pt_slug => $v3_taxonomies_obj ) {
							// DEFAULTS.
							$tmp_tax_query = array();
							$tmp_tax_query['relation'] = 'OR';
							foreach ( $v3_taxonomies_obj->taxonomies as $k4_tax_slug => $v4_tax_obj ) {
								// DEFAULTS.
								$tmp_tq_item = array(
									'taxonomy'           => '',
									'field'              => 'id', // Unmodified.
									'terms'              => array(),
									'include_children'   => false, // Unmodified.
									'operator'           => 'IN',

									'apl_dynamic_terms'  => false,
								);

								// TAXONOMY SLUG.
								$tmp_tq_item['taxonomy'] = $k4_tax_slug;

								// REQUIRED TAXONOMY(IES)
								if ( $v4_tax_obj->require_taxonomy ) {
									$tmp_tax_query['relation'] = 'AND';
								}

								// REQUIRED TERMS.
								if ( $v4_tax_obj->require_terms ) {
									$tmp_tq_item['operator'] = 'AND';
								}

								// DYNAMIC TERMS [APL]
								if ( $v4_tax_obj->include_terms ) {
									$tmp_tq_item['apl_dynamic_terms'] = true;
								}

								// TERMS.
								$tmp_tq_item['terms'] = $v4_tax_obj->terms;

								$tmp_tax_query[] = $tmp_tq_item;
							}

							$tmp_post_type_arr[] = array( $k3_pt_slug );

							// ADD TO FINAL TEMP.
							$tmp_tax_query_arr[ $k3_pt_slug ] = array();
							$tmp_tax_query_arr[ $k3_pt_slug ] = $tmp_tax_query;
						}
					} elseif ( empty( $old_value->_postParents ) ) {
						// FOR EMPTY TO Post_Type 'Any'
						// An empty tax_query is ok. ( ['tax_query']['any'] = array(); )
						$tmp_post_type_arr[] = 'any';
						$tmp_tax_query_arr['any'] = array();
					}
					$new_post_list->post_type = $tmp_post_type_arr;
					$new_post_list->tax_query = $tmp_tax_query_arr;

					// POST PARENTS & POST DYNAMICS (MAYBE POST TYPE)
					// (array)=>(string)
					$tmp_post_parent__in_arr = array();
					$tmp_post_parent_dynamic = array();

					if ( ! empty( $old_value->_postParents ) ) {
						$is_dynamic = false;
						if ( in_array( '0', $old_value->_postParents ) ) {
							$is_dynamic = true;
							$dynamic_key = array_search( '0', $old_value->_postParents  );
							unset( $old_value->_postParents[ $dynamic_key ] );

							if ( empty( $old_value->_postParents ) ) {

								$args = array(
									'public' => true,
									'hierarchical' => true
								);

								$post_types = get_post_types( $args, 'names' );

								foreach ( $post_types as $v3_post_type_slug ) {
									if ( 'any' === $tmp_post_type_arr[0] ) {
										// Add All Post Types to post_type.
										$tmp_post_type_arr[] = $post_types;

										// Set All Page Dynamics to true.
										foreach ( $post_types as $v4_post_type_slug ) {
											$tmp_post_parent_dynamic[ $v4_post_type_slug ] = true;
										}
										break;
									}
									foreach ( $tmp_post_type_arr as $v4_pt_arr ) {
										// Add Page Dynamics to already set Post Types
										if ( is_array( $v4_pt_arr ) ) {
											foreach ( $v4_pt_arr as $v5_pt_slug ) {
												$tmp_post_parent_dynamic[ $v5_pt_slug ] = true;
											}
										}
									}
								}
							}
						} else {
							// With IDs
							foreach ( $old_value->_postParents as $k3_index => $v3_page_id ) {
								if ( 0 === intval( $v3_page_id ) ) {
									continue;
								}
								$tmp_page = get_post( $v3_page_id );
								if ( ! empty( $tmp_page ) ) {
									// Process Post Types and Page Parent corralations.
									foreach ( $tmp_post_type_arr as $post_type_arr ) {
										if ( is_array( $post_type_arr ) ) {
											if ( in_array( $tmp_page->post_type, $post_type_arr ) ) {
												// If post_type already exists.
												$tmp_post_parent__in_arr[ $tmp_page->post_type ] = $tmp_page->ID;
											} else {
												// IF no post_type set
												$tmp_post_type_arr[] = array( $tmp_page->post_type );
												$tmp_post_parent__in_arr[ $tmp_page->post_type ] = $tmp_page->ID;
											}
										} else {
											// ANY.
											unset( $tmp_post_type_arr );
											$tmp_post_type_arr = array( $tmp_page->post_type );
											$tmp_post_parent__in_arr[ $tmp_page->post_type ] = $tmp_page->ID;
										}
									}

									// PAGE DYNAMICS.
									$tmp_post_parent_dynamic[ $tmp_page->post_type ] = $is_dynamic;
								}
							}
						}
					}
					$new_post_list->post_type = $tmp_post_type_arr;
					$new_post_list->post_parent__in = $tmp_post_parent__in_arr;
					$new_post_list->post_parent_dynamic = $tmp_post_parent_dynamic;

					// (int).
					$new_post_list->posts_per_page       = $old_value->_listCount              ?: $new_post_list->posts_per_page;

					// (string)
					$new_post_list->order_by             = $old_value->_listOrderBy            ?: $new_post_list->order_by;
					// (string)
					$new_post_list->order                = $old_value->_listOrder              ?: $new_post_list->order;

					// (array)=>(string)
					// (array)=>(string)
					$new_post_list->post_status          = $old_value->_postVisibility         ?: $new_post_list->post_status;
					$new_post_list->post_status          = $old_value->_postStatus             ? array_merge( $old_value->_postStatus, $new_post_list->post_status ) : $new_post_list->post_status;

					// (string)
					$new_post_list->perm                 = $old_value->_userPerm               ?: $new_post_list->perm;

					// (string)
					// (array)=>(int)
					//$new_post_list->author__bool         = $old_value->_postAuthorOperator     ?: $new_post_list->author__bool;
					if ( isset( $old_value->_postAuthorOperator ) ) {
						if ( 'include' === $old_value->_postAuthorOperator ) {
							$new_post_list->author__bool = 'in';
						} elseif( 'exclude' === $old_value->_postAuthorOperator ) {
							$new_post_list->author__bool = 'not_in';
						}
					}

					$new_post_list->author__in           = $old_value->_postAuthorIDs          ?: $new_post_list->author__in;

					// (boolean)
					$new_post_list->ignore_sticky_posts  = $old_value->_listIgnoreSticky       ?: $new_post_list->ignore_sticky_posts;

					// (array)=>(int)
					// String or Array => Int
					$new_post_list->post__not_in         = $old_value->_listExcludePosts       ?: $new_post_list->post__not_in; 

					// (boolean)
					if ( isset( $old_value->_listExcludeCurrent ) ) {
						$new_post_list->pl_exclude_current = $old_value->_listExcludeCurrent;
					} elseif ( isset( $old_value->_postExcludeCurrent ) ) {
						$new_post_list->pl_exclude_current = $old_value->_postExcludeCurrent;
					}

					// (boolean)
					$new_post_list->pl_exclude_dupes     = $old_value->_listExcludeDuplicates  ?: $new_post_list->pl_exclude_dupes;

					/*
					 * DESIGN OBJECT ( APL_Design() )
					 */
					// SET DEFAULTS MANUALLY.
					$new_design = new stdClass();

					$new_design->id       = 0;
					$new_design->title    = '';
					$new_design->slug     = '';
					$new_design->before   = '';
					$new_design->content  = '';
					$new_design->after    = '';
					$new_design->empty    = '';

					$new_design->title             = sanitize_key( $k2_preset_slug ) ?: $new_design->title;
					$new_design->title .= '-design';
					$new_design->slug              = $new_design->title;
					// Add the design slug to post_list it belongs to.
					$new_post_list->pl_apl_design  = $new_design->slug;

					$new_design->before   = $old_value->_before   ?: $new_design->before;
					$new_design->content  = $old_value->_content  ?: $new_design->content;
					$new_design->after    = $old_value->_after    ?: $new_design->after;
					$new_design->empty    = $old_value->_exit     ?: $new_design->empty;

					// Add to Final Temp.
					$rtn_new_preset_arr['apl_post_list'][]  = $new_post_list;
					$rtn_new_preset_arr['apl_design'][]     = $new_design;
				}
			} elseif ( ! empty( $old_preset_db->$k1_db_index ) ) {
				// ARE THESE NEEDED ANYMORE?
				// Other unmodified APL_Options ( _preset_db_name & _delete )
				//$return_preset_db->$k1_db_index = $old_preset_db->$k1_db_index;
			}
		}

		return $rtn_new_preset_arr;
	}

	/**
	 * Finalize the Database Items.
	 *
	 * Used as a final function for class variables. Useful for later when
	 * using the class save method.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @uses $this->update_occurred
	 *
	 * @param array $apl_post_list_arr  An array of APL_Post_List objects.
	 * @param array $apl_design_arr     An array of APL_Design objects.
	 * @return array {
	 *     @type array 'apl_post_list_arr'  => array( APL_Post_List )
	 *     @type array 'apl_design_arr'     => array( APL_Design )
	 * }
	 */
	private function the_finisher( $apl_post_list_arr, $apl_design_arr ) {
		$rtn_new_db_arr = array(
			'apl_post_list_arr'  => array(),
			'apl_design_arr'     => array(),
		);
		
		foreach ( $apl_post_list_arr as $v1_post_list ) {
			$tmp_post_list = new APL_Post_List( $v1_post_list->slug );
			
			$tmp_post_list->title                = $v1_post_list->title                ?: $tmp_post_list->title;
			$tmp_post_list->post_type            = $v1_post_list->post_type            ?: $tmp_post_list->post_type;
			$tmp_post_list->tax_query            = $v1_post_list->tax_query            ?: $tmp_post_list->tax_query;
			$tmp_post_list->post_parent__in      = $v1_post_list->post_parent__in      ?: $tmp_post_list->post_parent__in;
			$tmp_post_list->post_parent_dynamic  = $v1_post_list->post_parent_dynamic  ?: $tmp_post_list->post_parent_dynamic;
			$tmp_post_list->posts_per_page       = $v1_post_list->posts_per_page       ?: $tmp_post_list->posts_per_page;
			$tmp_post_list->order_by             = $v1_post_list->order_by             ?: $tmp_post_list->order_by;
			$tmp_post_list->order                = $v1_post_list->order                ?: $tmp_post_list->order;
			$tmp_post_list->post_status          = $v1_post_list->post_status          ?: $tmp_post_list->post_status;
			$tmp_post_list->perm                 = $v1_post_list->perm                 ?: $tmp_post_list->perm;
			$tmp_post_list->author__bool         = $v1_post_list->author__bool         ?: $tmp_post_list->author__bool;
			$tmp_post_list->author__in           = $v1_post_list->author__in           ?: $tmp_post_list->author__in;
			$tmp_post_list->ignore_sticky_posts  = $v1_post_list->ignore_sticky_posts  ?: $tmp_post_list->ignore_sticky_posts;
			$tmp_post_list->post__not_in         = $v1_post_list->post__not_in         ?: $tmp_post_list->post__not_in;
			$tmp_post_list->pl_exclude_current   = $v1_post_list->pl_exclude_current   ?: $tmp_post_list->pl_exclude_current;
			$tmp_post_list->pl_exclude_dupes     = $v1_post_list->pl_exclude_dupes     ?: $tmp_post_list->pl_exclude_dupes;
			$tmp_post_list->pl_apl_design        = $v1_post_list->pl_apl_design        ?: $tmp_post_list->pl_apl_design;
			
			$rtn_new_db_arr['apl_post_list_arr'][] = $tmp_post_list;
		}
		
		foreach ( $apl_design_arr as $v1_design ) {
			$tmp_design = new APL_Design( $v1_design->slug );
			
			$tmp_design->title    = $v1_design->title    ?: $tmp_design->title;
			$tmp_design->before   = $v1_design->before   ?: $tmp_design->before;
			$tmp_design->content  = $v1_design->content  ?: $tmp_design->content;
			$tmp_design->after    = $v1_design->after    ?: $tmp_design->after;
			$tmp_design->empty    = $v1_design->empty    ?: $tmp_design->empty;
			
			$rtn_new_db_arr['apl_design_arr'][] = $tmp_design;
		}
		
		return $rtn_new_db_arr;
	}
	
}
