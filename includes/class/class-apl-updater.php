<?php
/**
 * APL Updater Class
 *
 * Updater object to Advanced Post List
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
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
	 * Even Update Occurred
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
	 * Stores the preset post lists
	 *
	 * @since 0.3.0
	 * @access private
	 * @var object
	 */
	public $preset_db;

	/**
	 * Stores the APL_Post_List Class Data Format
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array
	 */
	
	public $apl_post_list_arr = array();

	/**
	 * Stores the APL_Design Class Data Format
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array
	 */
	public $apl_design_arr = array();

	/**
	 * Updater Constructor
	 *
	 * Constructor for the Updater Class.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed diverse upgrade params to upgrade_items array.
	 *
	 * @param string $old_version  Version number the plugin is currently operating at.
	 * @param array  $update_items Items needed to be upgraded from the old_version.
	 * @param string $return_type  (Optional) Type of values to return. Default: 'APL' ('APL' || 'OBJECT')
	 *                             'APL'    - Returns the APL Classes/Method objects.
	 *                             'OBJECT' - Returns a Standard Class object.
	 *                                        NOTE: Used for importing, since objects would
	 *                                        lose the pointers in that given instance.
	 */
	public function __construct( $old_version, $update_items, $return_type = 'OBJECT' ) {
		if ( empty( $old_version ) || empty( $update_items ) ) {
			return new WP_Error( 'apl_updater', __( 'APL Updater Class Error: empty version and/or empty APL Options & APL Preset Db is being passed to the Updater Class.', 'advanced-post-list' ) );
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
			// VERSION 0.3.b5.
			if ( version_compare( '0.3.b5', $old_version, '>' ) ) {
				$this->APL_upgrade_to_03b5();
			}
			// VERSION 0.4.0.
			if ( version_compare( '0.4.0', $old_version, '>' ) ) {
				if ( ! empty( $this->options ) ) {
					$new_options   = $this->upgrade_options_03b5_to_040( $this->options );
					$this->options = $new_options;
				}
				if ( ! empty( $this->preset_db ) ) {
					$new_preset_arr          = $this->upgrade_preset_db_03b5_to_040( $this->preset_db );
					$this->apl_post_list_arr = $new_preset_arr['apl_post_list'];
					$this->apl_design_arr    = $new_preset_arr['apl_design'];

					// Don't delete incase there is a revert.
					// //delete_option( 'APL_preset_db-default' );
				}
			}
			if ( version_compare( '0.4.4', $old_version, '>' ) ) {
				if ( ! empty( $this->apl_post_list_arr ) ) {
					$new_post_list_arr       = $this->upgrade_apl_post_list_db_040_to_044( $this->apl_post_list_arr );
					$this->apl_post_list_arr = $new_post_list_arr;
				}
			}

			$this->options['version'] = APL_VERSION;
			$this->update_occurred    = true;
		}

		// This is likely to never happen, but just in case...fundamental example.
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

		if ( $this->update_occurred ) {
			// Fallback option if things go wrong.
			update_option( 'apl_backup_update_dev_' . date( 'YmdHi' ), $update_items );
			foreach ( $update_items as $k1_index => $u_item ) {
				$option_name1 = 'apl_backup_update_' . sanitize_key( $old_version ) . '_apl_' . $k1_index;
				$option_name2 = 'apl_backup_update_' . sanitize_key( $old_version ) . '_' . date( 'YmdHi' ) . '_apl_' . $k1_index;
				update_option( $option_name1, $u_item );
				update_option( $option_name2, $u_item );
			}
		}

		// Reform Preset Filters to work according to website. Like when importing
		// to a different website with the same category names that differ in IDs.
		// This corrects those various diviations.
		$new_post_list_arr = array();
		foreach ( $this->apl_post_list_arr as $v1_apl_post_list ) {
			$new_post_list_arr[] = $this->reform_post_list( $v1_apl_post_list );
		}
		$this->apl_post_list_arr = $new_post_list_arr;

		// Finalize the APL_Post_List & APL_Design to the proper type/format.
		// During Importing, class instances are lost when a static method or
		// seperate instance is used.
		switch ( $return_type ) {
			case 'APL':
				$this->apl_post_list_arr = $this->return_type_apl_post_lists( $this->apl_post_list_arr );
				$this->apl_design_arr    = $this->return_type_apl_designs( $this->apl_design_arr );
				break;
			case 'OBJECT':
			default:
				// Do nothing since everything is already a StdObject.
				break;
		}

	}

	/**
	 * Convert Kalin Plugin to APL
	 *
	 * Converts data from the previous plugin to APL configured data.
	 *
	 * @ignore
	 * @since 0.3.0
	 * @access private
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
	 * Convert Kalin's preset to APL's preset
	 *
	 * Converts individual data from the previous plugin to APL Preset data.
	 *
	 * @ignore
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
	 * Upgrade to "0.3.a1"
	 *
	 * Upgrade APL to 0.3.a1.
	 *
	 * @ignore
	 * @since 0.3.a1
	 * @access private
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
	 * Upgrade Options to 0.3.a1
	 *
	 * Upgrades Options from Base to "0.3.a1".
	 *
	 * @ignore
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
	 * Upgrade Preset Database to 0.3.a1
	 *
	 * Upgrade Preset Database Object to "0.3.a1".
	 *
	 * @ignore
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
			} elseif ( ! empty( $old_preset->$key1 ) ) {
				$return_preset_db->$key1 = $old_preset->$key1;
			}
		}
		return $return_preset_db;
	}

	/**
	 * Upgrade preset to 0.3.a1
	 *
	 * Upgrades Preset object to "0.3.a1".
	 *
	 * @ignore
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
	 * Upgrade APL to 0.3.b5
	 *
	 * Upgrades Plugin to "0.3.b5".
	 *
	 * @ignore
	 * @since 0.3.b5
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
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
	 * Upgrade Options from 0.3.a1 to 0.3.b5
	 *
	 * Upgrades APL's options "0.3.a1" to "0.3.b5".
	 *
	 * @ignore
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
	 * Upgrade Preset Database from 0.3.a1 to 0.3.b5
	 *
	 * Upgrades the Preset Database from "0.3.a1" to "0.3.b5".
	 *
	 * @ignore
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
	 * Upgrade Preset from 0.3.a1 to 0.3.b5
	 *
	 * Upgrades Preset from "0.3.a1" to "0.3.b5".
	 *
	 * @ignore
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
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $options
	 * @return array New APL_Options
	 */
	private function upgrade_options_03b5_to_040( $options ) {
		// SET DEFAULTS MANUALLY.
		$rtn_options = $this->options_array();

		$rtn_options['version'] = APL_VERSION;

		$rtn_options['ignore_post_types']     = isset( $options['ignore_post_types'] ) ? $options['ignore_post_types'] : $rtn_options['ignore_post_types'];
		$rtn_options['delete_core_db']        = isset( $options['delete_core_db'] )    ? $options['delete_core_db']    : $rtn_options['delete_core_db'];
		$rtn_options['default_empty_enable']  = isset( $options['default_exit'] )      ? $options['default_exit']      : $rtn_options['default_empty_enable'];
		$rtn_options['default_empty_output']  = isset( $options['default_exit_msg'] )  ? $options['default_exit_msg']  : $rtn_options['default_empty_output'];

		return $rtn_options;
	}

	/**
	 * Upgrade Preset Database from 0.3.b5 to 0.4.0
	 *
	 * Upgrades the Preset Database from "0.3.b5" to "0.4.0".
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param object $old_preset_db Old Preset Database.
	 * @return array New Preset structure.
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
					$new_post_list = $this->post_list_stdObj( '0.4.0' );

					// ADD VALUES.
					$new_post_list->slug   = sanitize_key( $k2_preset_slug )  ?: $new_post_list->slug;
					$new_post_list->title  = $new_post_list->slug             ?: $new_post_list->title;

					// POST TYPES & TAX_QUERY
					// array <= (object).
					// array <= (object).
					$tmp_post_type_arr = array();
					$tmp_tax_query_arr = array();
					if ( ! empty( $old_value->_postTax ) ) {
						// FOR POST TYPES SELECTED
						foreach( $old_value->_postTax as $k3_pt_slug => $v3_taxonomies_obj ) {
							if ( ! post_type_exists( $k3_pt_slug ) ) {
								continue;
							}

							// DEFAULTS.
							$tmp_tax_query = array();
							$p_relation = 'OR';
							foreach ( $v3_taxonomies_obj->taxonomies as $k4_tax_slug => $v4_tax_obj ) {
								if ( ! taxonomy_exists( $k4_tax_slug ) ) {
									continue;
								}

								// DEFAULTS.
								$tmp_tq_item = array(
									'taxonomy'           => '',
									'field'              => 'id', // Unmodified.
									'terms'              => array(),
									'include_children'   => false, // Unmodified.
									'operator'           => 'IN',

									'apl_terms_slug'     => array(),
									'apl_terms_dynamic'  => false,
								);

								// TAXONOMY SLUG.
								$tmp_tq_item['taxonomy'] = $k4_tax_slug;

								// REQUIRED TAXONOMY(IES)
								if ( $v4_tax_obj->require_taxonomy ) {
									$p_relation = 'AND';
								}

								// REQUIRED TERMS.
								if ( $v4_tax_obj->require_terms ) {
									$tmp_tq_item['operator'] = 'AND';
								}

								// DYNAMIC TERMS [APL]
								if ( $v4_tax_obj->include_terms ) {
									$tmp_tq_item['apl_terms_dynamic'] = true;
								}

								// TERMS.
								foreach ( $v4_tax_obj->terms as $k5_ => $v5_term_id ) {
									if ( term_exists( $v5_term_id, $k4_tax_slug ) ) {
										$p_term_obj = get_term( $v5_term_id, $k4_tax_slug );
										
										$tmp_tq_item['terms'][] = $v5_term_id;
										$tmp_tq_item['apl_terms_slug'][ $v5_term_id ] = $p_term_obj->slug;
									} elseif( 0 === $v5_term_id ) {
										$tmp_tq_item['terms'][] = 0;
									}
								}

								$tmp_tax_query['relation'] = $p_relation;
								$tmp_tax_query[] = $tmp_tq_item;
							}

							$tmp_post_type_arr[] = array( $k3_pt_slug );

							// ADD TO FINAL TEMP.
							$tmp_tax_query_arr[ $k3_pt_slug ] = array();
							$tmp_tax_query_arr[ $k3_pt_slug ] = $tmp_tax_query;
						}
					}

					if ( empty( $old_value->_postParents ) && empty( $tmp_post_type_arr ) && empty( $tmp_tax_query_arr ) ) {
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
									if ( empty( $tmp_post_type_arr ) ) {
										// IF no post_type set
										$tmp_post_type_arr[] = array( $tmp_page->post_type );
										$tmp_post_parent__in_arr[ $tmp_page->post_type ][] = $tmp_page->ID;
									} else {
										// Process Post Types and Page Parent corralations.
										foreach ( $tmp_post_type_arr as $post_type_arr ) {
											if ( is_array( $post_type_arr ) ) {
												if ( in_array( $tmp_page->post_type, $post_type_arr ) ) {
													// If post_type already exists.
													$tmp_post_parent__in_arr[ $tmp_page->post_type ][] = $tmp_page->ID;
												} else {
													// IF no post_type set
													$tmp_post_type_arr[] = array( $tmp_page->post_type );
													$tmp_post_parent__in_arr[ $tmp_page->post_type ][] = $tmp_page->ID;
												}
											} else {
												// ANY.
												unset( $tmp_post_type_arr );
												$tmp_post_type_arr = array( $tmp_page->post_type );
												$tmp_post_parent__in_arr[ $tmp_page->post_type ][] = $tmp_page->ID;
											}
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

					// New Option $new_post_list->offset;.
					
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

					// Default already set.
					// (string)
					//$new_post_list->author__bool         = 'none';
					if ( isset( $old_value->_postAuthorOperator ) ) {
						if ( 'include' === $old_value->_postAuthorOperator ) {
							$new_post_list->author__bool = 'in';
						} elseif( 'exclude' === $old_value->_postAuthorOperator ) {
							$new_post_list->author__bool = 'not_in';
						}
					}
					// (array)=>(int)
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
					$new_design = $this->design_stdObj();

					$new_design->title             = sanitize_key( $k2_preset_slug ) ?: $new_design->title;
					//$new_design->title .= '-design';
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
	 * Upgrade Preset Database from 0.4.0 to 0.4.4
	 *
	 * Upgrades the Preset Database from "0.4.0" to "0.4.4".
	 *
	 * @ignore
	 * @since 0.4.4
	 * @access private
	 *
	 * @param object $old_post_list_arr Old Post List database.
	 * @return array New Post List structure.
	 */
	private function upgrade_apl_post_list_db_040_to_044( $old_post_list_arr ) {
		$rtn_new_post_list_arr = array();

		foreach ( $old_post_list_arr as $old_post_list ) {
			$new_post_list = $this->post_list_stdObj( '0.4.4' );

			$new_post_list->id                  = isset( $old_post_list->id )                  ? $old_post_list->id                    : $new_post_list->id;
			$new_post_list->slug                = sanitize_key( $old_post_list->slug )         ?: $new_post_list->slug;
			$new_post_list->title               = $old_post_list->title                        ?: $new_post_list->title;
			$new_post_list->post_type           = $old_post_list->post_type                    ?: $new_post_list->post_type;
			$new_post_list->tax_query           = $old_post_list->tax_query                    ?: $new_post_list->tax_query;
			$new_post_list->post_parent__in     = $old_post_list->post_parent__in              ?: $new_post_list->post_parent__in;
			$new_post_list->post_parent_dynamic = $old_post_list->post_parent_dynamic          ?: $new_post_list->post_parent_dynamic;
			$new_post_list->posts_per_page      = isset( $old_post_list->posts_per_page  )     ? $old_post_list->posts_per_page        : $new_post_list->posts_per_page;
			$new_post_list->offset              = isset( $old_post_list->offset  )             ? $old_post_list->offset                : $new_post_list->offset;
			$new_post_list->order_by            = $old_post_list->order_by                     ?: $new_post_list->order_by;
			$new_post_list->order               = $old_post_list->order                        ?: $new_post_list->order;
			$new_post_list->post_status         = $old_post_list->post_status                  ?: $new_post_list->post_status;
			$new_post_list->perm                = $old_post_list->perm                         ?: $new_post_list->perm;
			$new_post_list->author__bool        = $old_post_list->author__bool                 ?: $new_post_list->author__bool;
			$new_post_list->author__in          = $old_post_list->author__in                   ?: $new_post_list->author__in;
			$new_post_list->ignore_sticky_posts = isset( $old_post_list->ignore_sticky_posts ) ? $old_post_list->ignore_sticky_posts   : $new_post_list->ignore_sticky_posts;
			$new_post_list->post__not_in        = $old_post_list->post__not_in                 ?: $new_post_list->post__not_in;
			$new_post_list->pl_exclude_current  = isset( $old_post_list->pl_exclude_current )  ? $old_post_list->pl_exclude_current    : $new_post_list->pl_exclude_current;
			$new_post_list->pl_exclude_dupes    = isset( $old_post_list->pl_exclude_dupes )    ? $old_post_list->pl_exclude_dupes      : $new_post_list->pl_exclude_dupes;
			$new_post_list->pl_apl_design       = $old_post_list->pl_apl_design                ?: $new_post_list->pl_apl_design;

			foreach ( $this->apl_design_arr as $v1_apl_design ) {
				if ( $v1_apl_design->slug === $new_post_list->pl_apl_design ) {
					$new_post_list->pl_apl_design_id   = isset( $v1_apl_design->id ) ? intval( $v1_apl_design->id )         : $new_post_list->pl_apl_design_id;
					$new_post_list->pl_apl_design_slug = $v1_apl_design->slug        ?: $new_post_list->pl_apl_design_slug;

					break;
				}
			}

			$rtn_new_post_list_arr[] = $new_post_list;
		}

		return $rtn_new_post_list_arr;
	}

	/**
	 * (Default) Options Array
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function options_array() {
		return array(
			'version'               => APL_VERSION,
			'ignore_post_types'     => array(),
			'delete_core_db'        => false,
			'default_empty_enable'  => false,
			'default_empty_output'  => '<p>' . __( 'Sorry, but no content is available at this time.', 'advanced-post-list' ) . '</p>',
		);
	}

	/**
	 * Post List Object
	 *
	 * Sets the object as a stdClass for better handling with various class conflicts.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @since 0.4.4 Set mock object based on version param.
	 * @access private
	 *
	 * @param string $version
	 * @return object
	 */
	private function post_list_stdObj( $version = '' ) {
		// SET DEFAULTS MANUALLY.
		switch( $version ) {
			case '0.4.0' :
				$rtn_post_list = $this->post_list_stdObj_0_4_0();
				break;
			case '0.4.4' :
			default :
				$rtn_post_list = $this->post_list_stdObj_0_4_4();
				break;
		}

		return $rtn_post_list;
	}

	/**
	 * Post List Object
	 *
	 * Sets the object as a stdClass to mock version 0.4.0 APL_Post_List.
	 *
	 * @ignore
	 * @since 0.4.4
	 * @access private
	 *
	 * @return object
	 */
	private function post_list_stdObj_0_4_0() {
		$rtn_post_list = new stdClass();

		$rtn_post_list->id                   = 0;
		$rtn_post_list->slug                 = '';
		$rtn_post_list->title                = '';
		$rtn_post_list->post_type            = array( 'any' );
		$rtn_post_list->tax_query            = array();
		$rtn_post_list->post_parent__in      = array();
		$rtn_post_list->post_parent_dynamic  = array();
		$rtn_post_list->posts_per_page       = 5;
		$rtn_post_list->offset               = 0;
		$rtn_post_list->order_by             = 'none';
		$rtn_post_list->order                = 'DESC';
		$rtn_post_list->post_status          = 'none';
		$rtn_post_list->perm                 = 'none';
		$rtn_post_list->author__bool         = 'none';
		$rtn_post_list->author__in           = array();
		$rtn_post_list->ignore_sticky_posts  = true;
		$rtn_post_list->post__not_in         = array();
		$rtn_post_list->pl_exclude_current   = true;
		$rtn_post_list->pl_exclude_dupes     = false;
		$rtn_post_list->pl_apl_design        = '';

		return $rtn_post_list;
	}

	/**
	 * Post List Object
	 *
	 * Sets the object as a stdClass to mock version 0.4.0 APL_Post_List.
	 *
	 * @ignore
	 * @since 0.4.4
	 * @access private
	 *
	 * @return object
	 */
	private function post_list_stdObj_0_4_4() {
		$rtn_post_list = new stdClass();

		$rtn_post_list->id                   = 0;
		$rtn_post_list->slug                 = '';
		$rtn_post_list->title                = '';
		$rtn_post_list->post_type            = array( 'any' );
		$rtn_post_list->tax_query            = array();
		$rtn_post_list->post_parent__in      = array();
		$rtn_post_list->post_parent_dynamic  = array();
		$rtn_post_list->posts_per_page       = 5;
		$rtn_post_list->offset               = 0;
		$rtn_post_list->order_by             = 'none';
		$rtn_post_list->order                = 'DESC';
		$rtn_post_list->post_status          = 'none';
		$rtn_post_list->perm                 = 'none';
		$rtn_post_list->author__bool         = 'none';
		$rtn_post_list->author__in           = array();
		$rtn_post_list->ignore_sticky_posts  = true;
		$rtn_post_list->post__not_in         = array();
		$rtn_post_list->pl_exclude_current   = true;
		$rtn_post_list->pl_exclude_dupes     = false;
		$rtn_post_list->pl_apl_design        = '';
		$rtn_post_list->pl_apl_design_id     = 0;
		$rtn_post_list->pl_apl_design_slug   = '';

		return $rtn_post_list;
	}

	/**
	 * Design Object
	 *
	 * Sets the object as a stdClass for better handling with various class conflicts.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @return object
	 */
	private function design_stdObj() {
		$rtn_design = new stdClass();

		$rtn_design->id       = 0;
		$rtn_design->title    = '';
		$rtn_design->slug     = '';
		$rtn_design->before   = '';
		$rtn_design->content  = '';
		$rtn_design->after    = '';
		$rtn_design->empty    = '';
		
		return $rtn_design;
	}

	/**
	 * Reform to Post List
	 *
	 * Used to set the post list to the correct configuration in a given
	 * website environment.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @since 0.4.4 Added stricter object referrencing with APL_Designs.
	 * @access private
	 *
	 * @param APL_Post_list $apl_post_list
	 * @return object
	 */
	private function reform_post_list( $apl_post_list ) {
		$new_post_list = $this->post_list_stdObj();

		$new_post_list->id     = $apl_post_list->id     ?: $new_post_list->id;
		$new_post_list->slug   = $apl_post_list->slug   ?: $new_post_list->slug;
		$new_post_list->title  = $apl_post_list->title  ?: $new_post_list->title;

		$tmp_post_type = array();
		$tmp_tax_query = array();
		$tmp_post_parent__in = array();
		$tmp_post_parent_dynamic = array();
		foreach ( $apl_post_list->post_type as $k1_ => $v1_post_types ) {
			if ( is_array( $v1_post_types ) ) {

				$tmp2_post_type = array();
				foreach ( $v1_post_types as $k2_ => $v2_pt_slug ) {
					$args = array(
						'post_type'       => $v2_pt_slug,
						'post_status'     => array(
							'draft',
							'pending',
							'publish',
							'future',
							'private',
							'trash',
						),
						'posts_per_page'  => 1,
					);
					$pl_query = new WP_Query( $args );

					if ( 1 > $pl_query->post_count ) {
						continue;
					}

					$tmp2_post_type[] = $v2_pt_slug;

					$p_tax_query = json_decode( json_encode( $apl_post_list->tax_query ), true );
					$tmp_tax_query[ $v2_pt_slug ] = array();
					$tmp_tax_query[ $v2_pt_slug ] = $this->reform_post_list_tax_query( $p_tax_query[ $v2_pt_slug ] );

					////////////////////////////////////////////////////////////

					$p_post_parent__in = json_decode( json_encode( $apl_post_list->post_parent__in ), true );
					if ( isset( $p_post_parent__in[ $v2_pt_slug ] ) && ! empty( $p_post_parent__in[ $v2_pt_slug ] ) ) {
						$tmp_post_parents = array();
						foreach ( $p_post_parent__in[ $v2_pt_slug ] as $k3_ => $v3_post_id ) {
							$args = array(
								'post__in'        => array( $v3_post_id ),
								// TODO Add Post Slug in APL 0.5.
								//'name'            => $post_slug,
								'post_type'       => $v2_pt_slug,
								'post_status'     => array(
									'draft',
									'pending',
									'publish',
									'future',
									'private',
									'trash',
								),
								'posts_per_page'  => 1,
							);
							$pl_query = new WP_Query( $args );
							if ( 1 > $pl_query->post_count ) {
								continue;
							}
							$tmp_post_parents[] = $v3_post_id;
						}
						$tmp_post_parent__in[ $v2_pt_slug ] = $tmp_post_parents;

						$p_post_parent_dynamic = json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true );
						$tmp_post_parent_dynamic[ $v2_pt_slug ] = $p_post_parent_dynamic[ $v2_pt_slug ] ?: false;
					}

				}

				if ( ! empty( $tmp2_post_type ) ) {
					$tmp_post_type[] = $tmp2_post_type;
				}


			}
			// AND if still empty
			if ( 'any' === $v1_post_types || empty( $tmp_post_type ) ) {
				$v2_pt_slug = 'any';
				$tmp_post_type[] = $v2_pt_slug;

				$p_tax_query = json_decode( json_encode( $apl_post_list->tax_query ), true );
				$tmp_tax_query[ $v2_pt_slug ] = array();
				if ( isset( $p_tax_query[ $v2_pt_slug ]  ) ) {
					$tmp_tax_query[ $v2_pt_slug ] = $this->reform_post_list_tax_query( $p_tax_query[ $v2_pt_slug ] );
				}
			}
		}

		$new_post_list->post_type            = $tmp_post_type;
		$new_post_list->tax_query            = $tmp_tax_query;
		$new_post_list->post_parent__in      = $tmp_post_parent__in;
		$new_post_list->post_parent_dynamic  = $tmp_post_parent_dynamic;

		$new_post_list->posts_per_page       = isset( $apl_post_list->posts_per_page )       ? $apl_post_list->posts_per_page       : $new_post_list->posts_per_page;
		$new_post_list->offset               = isset( $apl_post_list->offset )               ? $apl_post_list->offset               : $new_post_list->offset;
		$new_post_list->order_by             = isset( $apl_post_list->order_by )             ? $apl_post_list->order_by             : $new_post_list->order_by;
		$new_post_list->order                = isset( $apl_post_list->order )                ? $apl_post_list->order                : $new_post_list->order;
		$new_post_list->post_status          = isset( $apl_post_list->post_status )          ? $apl_post_list->post_status          : $new_post_list->post_status;
		$new_post_list->perm                 = isset( $apl_post_list->perm )                 ? $apl_post_list->perm                 : $new_post_list->perm;
		$new_post_list->author__bool         = isset( $apl_post_list->author__bool )         ? $apl_post_list->author__bool         : $new_post_list->author__bool;

		// Hotfix for Authod IDs not updating - If Else to keep structure the same.
		//$new_post_list->author__in           = isset( $apl_post_list->author__in )           ? $apl_post_list->author__in           : $new_post_list->author__in;
		if ( isset( $apl_post_list->author__in ) ) {
			$tmp_author__in = array();
			foreach ( $apl_post_list->author__in as $v1_author_ID ) {
				$tmp_author__in[] = intval( $v1_author_ID );
			}
			$new_post_list->author__in       = $tmp_author__in;
		}
		$new_post_list->ignore_sticky_posts  = isset( $apl_post_list->ignore_sticky_posts )  ? $apl_post_list->ignore_sticky_posts  : $new_post_list->ignore_sticky_posts;
		$new_post_list->post__not_in         = isset( $apl_post_list->post__not_in )         ? $apl_post_list->post__not_in         : $new_post_list->post__not_in;
		$new_post_list->pl_exclude_current   = isset( $apl_post_list->pl_exclude_current )   ? $apl_post_list->pl_exclude_current   : $new_post_list->pl_exclude_current;
		$new_post_list->pl_exclude_dupes     = isset( $apl_post_list->pl_exclude_dupes )     ? $apl_post_list->pl_exclude_dupes     : $new_post_list->pl_exclude_dupes;
		$new_post_list->pl_apl_design        = isset( $apl_post_list->pl_apl_design )        ? $apl_post_list->pl_apl_design        : $new_post_list->pl_apl_design;
		$new_post_list->pl_apl_design_id     = isset( $apl_post_list->pl_apl_design_id )     ? $apl_post_list->pl_apl_design_id     : $new_post_list->pl_apl_design_id;
		$new_post_list->pl_apl_design_slug   = isset( $apl_post_list->pl_apl_design_slug )   ? $apl_post_list->pl_apl_design_slug   : $new_post_list->pl_apl_design_slug;

		return $new_post_list;
	}

	/**
	 * Reform to Tax Query
	 *
	 * Used to set the post list's tax_query to the correct configuration in a given
	 * website environment.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $tax_query
	 * @return array
	 */
	private function reform_post_list_tax_query( $tax_query = array() ) {
		$rtn_tax_query = array();
		if ( empty( $tax_query ) ) {
			return $rtn_tax_query;
		}
		
		$relation = $tax_query['relation'] ?: 'OR';
		unset( $tax_query['relation'] );

		$tmp2_tax_arr = array();
		foreach ( $tax_query as $k3_ => $v3_tax_arr ) {
			$args = array(
				'taxonomy'   => $v3_tax_arr['taxonomy'],
				'hide_empty' => false,
			);
			$taxonomy_terms = new WP_Term_Query( $args );
			if ( null === $taxonomy_terms->terms ) {
				continue;
			}
			$tmp_tq_item = array(
				'taxonomy'           => '',
				'field'              => 'id', // Unmodified.
				'terms'              => array(),
				'include_children'   => false, // Unmodified.
				'operator'           => 'IN',

				'apl_terms_slug'     => array(),
				'apl_terms_dynamic'  => false,
			);

			$tmp_tq_item['taxonomy']           = $v3_tax_arr['taxonomy'];
			$tmp_tq_item['operator']           = $v3_tax_arr['operator']           ?: $tmp_tq_item['operator'];
			$tmp_tq_item['apl_terms_dynamic']  = $v3_tax_arr['apl_terms_dynamic']  ?: $tmp_tq_item['apl_terms_dynamic'];

			foreach ( $v3_tax_arr['terms'] as $k4_ => $v4_term_id ) {
				if ( 0 >= $v4_term_id ) {
					$tmp_tq_item['terms'][] = 0;
					continue;
				} else {
					foreach ( $taxonomy_terms->terms as $v5_term_obj ) {
						if ( $v5_term_obj->term_id === $v4_term_id && $v5_term_obj->slug === $v3_tax_arr['apl_terms_slug'][ $v4_term_id ] ) {
							$tmp_tq_item['terms'][]                                  = $v5_term_obj->term_id;
							$tmp_tq_item['apl_terms_slug'][ $v5_term_obj->term_id ]  = $v5_term_obj->slug;

							continue;
						} elseif ( $v5_term_obj->slug === $v3_tax_arr['apl_terms_slug'][ $v4_term_id ] ) {
							$tmp_tq_item['terms'][]                                  = $v5_term_obj->term_id;
							$tmp_tq_item['apl_terms_slug'][ $v5_term_obj->term_id ]  = $v5_term_obj->slug;

							continue;
						}
					}
				}
			}
			if ( empty( $tmp_tq_item['terms'] ) ) {
				$tmp_tq_item['terms'][] = 0;
			}
			$tmp2_tax_arr[] = $tmp_tq_item;
		}
		$rtn_tax_query              = $tmp2_tax_arr;
		$rtn_tax_query['relation']  = $relation;

		return $rtn_tax_query;
	}
	
	/**
	 * Object Type for APL Post Lists
	 *
	 * This sets the Post Lists as the custom APL_Post_List object.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $apl_post_list_arr Post List array for $this->apl_post_list_arr.
	 * @return array Custom APL Post List object.
	 */
	private function return_type_apl_post_lists( $apl_post_list_arr ) {
		$rtn_apl_post_list_arr = array();
		
		foreach ( $apl_post_list_arr as $apl_post_list ) {
			$tmp_apl_post_list = new APL_Post_List( $apl_post_list->slug );

			$tmp_apl_post_list->title                = $apl_post_list->title                ?: $tmp_apl_post_list->title;
			$tmp_apl_post_list->post_type            = $apl_post_list->post_type            ? json_decode( json_encode( $apl_post_list->post_type ), true ) : $tmp_apl_post_list->post_type ;
			$tmp_apl_post_list->tax_query            = $apl_post_list->tax_query            ? json_decode( json_encode( $apl_post_list->tax_query ), true ) : $tmp_apl_post_list->tax_query;
			$tmp_apl_post_list->post_parent__in      = $apl_post_list->post_parent__in      ? json_decode( json_encode( $apl_post_list->post_parent__in ), true ) : $tmp_apl_post_list->post_parent__in;
			$tmp_apl_post_list->post_parent_dynamic  = $apl_post_list->post_parent_dynamic  ? json_decode( json_encode( $apl_post_list->post_parent_dynamic ), true ) : $tmp_apl_post_list->post_parent_dynamic;
			$tmp_apl_post_list->posts_per_page       = $apl_post_list->posts_per_page       ?: $tmp_apl_post_list->posts_per_page;
			$tmp_apl_post_list->offset               = $apl_post_list->offset               ?: $tmp_apl_post_list->offset;
			$tmp_apl_post_list->order_by             = $apl_post_list->order_by             ?: $tmp_apl_post_list->order_by;
			$tmp_apl_post_list->order                = $apl_post_list->order                ?: $tmp_apl_post_list->order;
			$tmp_apl_post_list->post_status          = $apl_post_list->post_status          ? json_decode( json_encode( $apl_post_list->post_status ), true ) : $tmp_apl_post_list->post_status;
			$tmp_apl_post_list->perm                 = $apl_post_list->perm                 ?: $tmp_apl_post_list->perm;
			$tmp_apl_post_list->author__bool         = $apl_post_list->author__bool         ?: $tmp_apl_post_list->author__bool;
			$tmp_apl_post_list->author__in           = $apl_post_list->author__in           ?: $tmp_apl_post_list->author__in;
			$tmp_apl_post_list->ignore_sticky_posts  = $apl_post_list->ignore_sticky_posts  ?: $tmp_apl_post_list->ignore_sticky_posts;
			$tmp_apl_post_list->post__not_in         = $apl_post_list->post__not_in         ?: $tmp_apl_post_list->post__not_in;
			$tmp_apl_post_list->pl_exclude_current   = $apl_post_list->pl_exclude_current   ?: $tmp_apl_post_list->pl_exclude_current;
			$tmp_apl_post_list->pl_exclude_dupes     = $apl_post_list->pl_exclude_dupes     ?: $tmp_apl_post_list->pl_exclude_dupes;
			$tmp_apl_post_list->pl_apl_design        = $apl_post_list->pl_apl_design        ?: $tmp_apl_post_list->pl_apl_design;
			$tmp_apl_post_list->pl_apl_design_id     = $apl_post_list->pl_apl_design_id     ?: $tmp_apl_post_list->pl_apl_design_id;
			$tmp_apl_post_list->pl_apl_design_slug   = $apl_post_list->pl_apl_design_slug   ?: $tmp_apl_post_list->pl_apl_design_slug;

			$rtn_apl_post_list_arr[] = $tmp_apl_post_list;
		}
		
		return $rtn_apl_post_list_arr;
	}
	
	/**
	 * Object Type for APL Designs
	 *
	 * This sets the Designs as the custom APL_Design object.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @param array $apl_design_arr Design array for $this->apl_design_arr.
	 * @return array Custom APL Design object.
	 */
	private function return_type_apl_designs( $apl_design_arr ) {
		$rtn_apl_design_arr = array();
		
		
		foreach ( $apl_design_arr as $apl_design ) {
			// TODO Change to Unique ID.
			$tmp_apl_design = new APL_Design( $apl_design->slug );

			$tmp_apl_design->title    = $apl_design->title    ?: $tmp_apl_design->title;
			$tmp_apl_design->before   = $apl_design->before   ?: $tmp_apl_design->before;
			$tmp_apl_design->content  = $apl_design->content  ?: $tmp_apl_design->content;
			$tmp_apl_design->after    = $apl_design->after    ?: $tmp_apl_design->after;
			$tmp_apl_design->empty    = $apl_design->empty    ?: $tmp_apl_design->empty;
			
			$rtn_apl_design_arr[] = $tmp_apl_design;
		}
		
		return $rtn_apl_design_arr;
	}
	
//	/**
//	 * EXAMPLE IF NEED TO SET TO OBJECT.
//	 */
//	private function return_type_object_post_lists() {
//		
//	}
//	
//	/**
//	 * EXAMPLE IF NEED TO SET TO OBJECT.
//	 */
//	private function return_type_object_designs() {
//		
//	}
	
}
