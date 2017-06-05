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
	public function __construct( $old_version, $preset_db = null, $options = null ) {
		if ( empty( $old_version ) || ( empty( $preset_db ) && empty( $options ) ) ) {
			return new WP_Error( 'apl_updater', __( 'APL Updater Class Error: empty version and/or empty APL Options & APL Preset Db is being passed to the Updater Class.', 'advanced-post-list' ) );
			return;
		}

		// INIT.
		$this->options = array();
		$this->preset_db = new APL_Preset_Db();

		// FILL IN VARIABLES.
		if ( ! empty( $options ) ) {
			$this->options = $options;
		}
		if ( ! empty( $preset_db ) ) {
			$this->preset_db = $preset_db;
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
			if ( version_compare( '0.3.b5', $old_version, '>' ) ) {
				$this->APL_upgrade_to_03b5();
			}
			if ( version_compare( '0.4.0', $old_version, '>' ) ) {
				$this->apl_upgrade_to_040();
			}
		}

		/* **** DOWNGRADES **** */
		// DOWNGRADE FROM 0.3.X TO BASE.
		/*
        if ( version_compare( '0.3.b5', $oldversion, '<' ) ) {
            $this->APL_downgrade_from_03b5();
        }
        if ( version_compare( '0.3.a1', $oldversion, '<' ) ) {
            $this->APL_downgrade_from_03a();
        }
		*/

		/* **** UPDATE VERSION NUMBER **** */
		// APL_VERSION - equals the file version located in "advanced-post-list.php".
		if ( isset( $this->options['version'] ) && version_compare( APL_VERSION, $old_version, '>' ) ) {
			$this->options['version'] = APL_VERSION;
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
			$this->preset_db->_preset_db->$key = new APL_Preset();
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
		$return_preset = new APL_Preset();
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
	 * Upgrade APL to 0.4.0.
	 *
	 * Upgrades Plugin to "0.4.0".
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see Function/method/class relied on
	 * @link URL
	 * @global type $varname Description.
	 * @global type $varname Description.
	 *
	 * @return void
	 */
	private function apl_upgrade_to_040() {
		if ( ! empty( $this->preset_db ) ) {
			$this->preset_db = $this->apl_upgrade_return_preset_db_03b5_to_040( $this->preset_db );
			//unset( $this->preset_db->_preset_db );
			
		}
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
	private function apl_upgrade_return_preset_db_03b5_to_040( $old_preset_db ) {
		$return_preset_db = new APL_Preset_Db();
		$return_preset_db->reset_to_version( '0.3.b5' );

		foreach ( $return_preset_db as $key1 => $value1 ) {
			if ( '_preset_db' === $key1 && ! empty( $old_preset_db->$key1 ) ) {
				foreach ( $old_preset_db->_preset_db as $preset_key => $old_value ) {
					//$return_preset_db->_preset_db->$key2 = $this->apl_upgrade_preset_03b5_to_040( $key2, $value2 );
					//$return_preset = new APL_Preset();
					//$return_preset->reset_to_version( '0.4.0' );
					$new_post_list = new APL_Post_List( $preset_key );
					
					// (array)=>(string)
					if ( isset( $old_value->_postParents ) ) {
						$new_post_list->post_parents = $old_value->_postParents;
					}
					// (object).
					if ( isset( $old_value->_postTax ) ) {
						$new_post_list->post_tax = $old_value->_postTax;
					}
					// (int).
					if ( isset( $old_value->_listCount ) ) {
						$new_post_list->list_count = $old_value->_listCount;
					}
					// (string)
					if ( isset( $old_value->_listOrderBy ) ) {
						$new_post_list->list_order_by = $old_value->_listOrderBy;
					}
					// (string)
					if ( isset( $old_value_listOrder ) ) {
						$new_post_list->list_order = $old_value->_listOrder;
					}
					// (array)=>(string)
					if ( isset( $old_value->_postVisibility ) ) {
						$new_post_list->post_visibility = $old_value->_postVisibility;
					}
					// (array)=>(string)
					if ( isset( $old_value->_postStatus ) ) {
						$new_post_list->post_status = $old_value->_postStatus;
					}
					// (string)
					if ( isset( $old_value->_userPerm ) ) {
						$new_post_list->user_perm = $old_value->_userPerm;
					}
					// (string)
					if ( isset( $old_value->_postAuthorOperator ) ) {
						$new_post_list->post_author_operator = $old_value->_postAuthorOperator;
					}
					// (array)=>(int)
					if ( isset( $old_value->_postAuthorIDs ) ) {
						$new_post_list->post_author_ids = $old_value->_postAuthorIDs;
					}
					// (boolean)
					if ( isset( $old_value->_listIgnoreSticky ) ) {
						$new_post_list->list_ignore_sticky = $old_value->_listIgnoreSticky;
					}
					// (boolean)
					if ( isset( $old_value->_postExcludeCurrent ) ) { //_listExcludeCurrent
						$new_post_list->list_exclude_current = $old_value->_postExcludeCurrent;
					}
					// (boolean)
					if ( isset( $old_value->_listExcludeDuplicates ) ) {
						$new_post_list->list_exclude_duplicates = $old_value->_listExcludeDuplicates;
					}
					// (array)=>(int)
					if ( isset( $old_value->_listExcludePosts ) ) {
						$new_post_list->list_exclude_posts = $old_value->_listExcludePosts;
					}
					
					// TODO Change to Design slug. Default: preset_name.
					$new_post_list->apl_design = '';
					$apl_design = new APL_Design( $preset_key );

					if ( isset( $old_value->_before ) ) {
						$apl_design->before = $old_value->_before;
					}
					if ( isset( $old_value->_content ) ) {
						$apl_design->content = $old_value->_content;
					}
					if ( isset( $old_value->_after ) ) {
						$apl_design->after = $old_value->_after;
					}
					if ( isset( $old_value->_exit ) ) {
						$apl_design->empty = $old_value->_exit;
					}
					$apl_design->save_design();
					$return_preset_db->design_db[] = array(
						'id'    => $apl_design->id,
						'slug'  => $apl_design->slug,
						'title' => $apl_design->title,
					);

					$new_post_list->apl_design = $apl_design->slug;
					$new_post_list->save_post_list();
					$return_preset_db->post_list_db[] = array(
						'id'    => $new_post_list->id,
						'slug'  => $new_post_list->slug,
						'title' => $new_post_list->title,
					);
				}
			} elseif ( ! empty( $old_preset_db->$key1 ) ) {
				// Other unmodified APL_Options ( _preset_db_name & _delete )
				$return_preset_db->$key1 = $old_preset_db->$key1;
			}
		}

		unset( $return_preset_db->_preset_db );
		return $return_preset_db;
	}
}
