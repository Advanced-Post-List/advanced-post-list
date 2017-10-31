<?php
/**
 * APL Preset Db Class
 *
 * Object for handling the Preset Database
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.1.0
 * @deprecated 0.4.0 Use Builtin Post Meta
 */

/**
 * APL Preset Database
 *
 * Database Object to contain Preset Objects to be saved to the database.
 *
 * @since 0.1.0
 */
class APL_Preset_Db {

	/**
	 * Preset Database slug.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string
	 */
	var $_preset_db_name;

	/**
	 * Database Array.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array
	 */
	var $_preset_db;
	
	/**
	 * Database Array.
	 *
	 * @since 0.1.0
	 * @since 0.4.0  changed name from $_preset_db to $post_list_db.
	 * @access public
	 * @var array
	 */
	var $post_list_db;
	
	/**
	 * Database Array.
	 *
	 * @since 0.4.0
	 * @access public
	 * @var array=>string
	 */
	var $design_db;

	/**
	 * Delete on Deactivate.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string
	 */
	var $_delete;

	/**
	 * Preset Database Object Constructor.
	 *
	 * Loads Preset Database Option data. If no data exists with the same name,
	 * then a new database options is created and saved to the developer's
	 * WordPress database.
	 *
	 * STEP 1: Store Preset_Database-Name to $this->Preset_Database-Name.
	 * STEP 2: Load 'Preset DbOptions' values, if any then skip Steps 3-4.
	 * STEP 3: Set (this) values to default values.
	 * STEP 4: Save 'Preset DbOptions.
	 *
	 * @since 0.1.0
	 *
	 * @param string $db_name Optional. Database slug.
	 * @return void
	 */
	public function __construct( $db_name = '' ) {
		// DEFAULTS.
		$this->_preset_db_name = 'default';
		$this->_preset_db = new stdClass();
		$this->_delete = 'true';

		if ( ! empty( $db_name ) ) {
			// STEP 1.
			$this->_preset_db_name = 'APL_preset_db-' . $db_name;
			// STEP 2.
			$this->option_load_db();
		}
	}

	/**
	 * Reset Database Structure to Version.
	 *
	 * Resets the object structure according the the version number.
	 *
	 * @since 0.1.0
	 * @access (for functions: only use if private)
	 *
	 * @param string $version Plugin version number.
	 * @return void
	 */
	public function reset_to_version( $version ) {
		$this->reset_to_base();
		
		foreach ( $this as $key => &$value ) {
			$value = null;
			unset( $this->$key );
		}
		if ( version_compare( '0.4.0', $version, '>' ) ) {
			$this->reset_to_base();
		} elseif ( version_compare( '0.3.a1', $version, '<=' ) && version_compare( '0.3.b5', $version, '>' ) ) {
			$this->reset_to_03a1();
		} elseif ( version_compare( '0.3.b5', $version, '<=' ) && version_compare( '0.4.0', $version, '>' ) ) {
			$this->reset_to_03b5();
		} else {
			//if ( version_compare( '0.4.0', $version, '<=' ) && version_compare( 'X.X.X', $version, '>' ) )
			$this->reset_to_040();
		}
	}

	/**
	 * Reset to Base.
	 *
	 * Resets the object to the original structure.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @return void
	 */
	private function reset_to_base() {
		$this->_preset_db_name = '';
		$this->_preset_db = new stdClass();
		$this->_delete = 'true';
	}
	
	/**
	 * 
	 */
	private function reset_to_040() {
		$this->_preset_db_name = '';
		$this->post_list_db = array();
		$this->design_db = array();
		$this->_delete = 'true';
	}

	/**
	 * Load Preset Database.
	 *
	 * Loads and stores database values to (this) class values.
	 *
	 * STEP 1: Get 'Preset DbOptions with the value stored in the class variable
	 * _preset_db_name.
	 * STEP 2: Store database variable to class values ( _preset_db & _delete ).
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function option_load_db() {
		// STEP 1.
		$options_db = get_option( $this->_preset_db_name );
		// STEP 2.
		if ( false === $options_db ) {
			$this->set_to_defaults();
			$this->options_save_db();
		} else {
			$this->_preset_db = $options_db->_preset_db;
			$this->_delete = $options_db->_delete;
		}
	}

	/**
	 * Save Preset Database.
	 *
	 * Save Preset Database class object to WP Options database.
	 *
	 * STEP 1: Save (this) class object to database.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function options_save_db() {
		// STEP 1.
		update_option( $this->_preset_db_name, $this );
	}

	/**
	 * Remove Preset Database.
	 *
	 * Deletes the Preset Database that is stored in WP Option database.
	 *
	 * STEP 1: Delete Options with the same Preset Db Options name.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function options_remove_db() {
		// STEP 1.
		delete_option( $this->_preset_db_name );
	}

	/**
	 * Set to defaults.
	 *
	 * Set Preset Database Object to Defaults.
	 *
	 * STEP 1: Set delete to 'true'.
	 * STEP 2: Create a temp Preset (stdclass). Hardcoded as a JSON string.
	 * STEP 3: JSON Decode and store in (this) class _preset_db value.
	 *
	 * @since 0.1.0
	 *
	 * @return void Returns a JSON string of the Standard Class.
	 */
	public function set_to_defaults() {
		$this->_preset_db = new stdClass();

		// STEP 1.
		$this->_delete = 'true';

		// STEP 2.
		$temp_preset = (string) '{
									"pageContentDivided_5":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":5,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p><hr\/>",
										"_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>",
										"_after":"<\/p>"
									},
									"postExcerptDivided_5":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":5,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p><hr\/>",
										"_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>",
										"_after":"<\/p>"
									},
									"simpleAttachmentList_10":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":10,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<ul>",
										"_content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>",
										"_after":"<\/ul>"
									},
									"images_5":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":5,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<hr \/>",
										"_content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>",
										"_after":"<hr \/>"
									},
									"pageDropdown_100":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":100,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">",
										"_content":"<option value=\"[post_permalink]\">[post_title]<\/option>",
										"_after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>"
									},
									"simplePostList_5":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":5,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p>",
										"_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ",
										"_after":"<\/p>"
									},
									"footerPageList_10":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":10,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p align=\"center\">",
										"_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ",
										"_after":"<\/p>"
									},
									"everythingNumbered_200":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":200,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p>All my pages and posts (roll over for titles):<br\/>",
										"_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ",
										"_after":"<\/p>"
									},
									"everythingID_200":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":200,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<p>All my pages and posts (roll over for titles):<br\/>",
										"_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ",
										"_after":"<\/p>"
									},
									"CSSTable":
									{
										"_postParents":[],
										"_postTax":{},
										"_listCount":9,
										"_listOrderBy":"date",
										"_listOrder":"DESC",
										"_postVisibility":["public"],
										"_postStatus":["publish"],
										"_userPerm":"readable",
										"_postAuthorOperator":"none",
										"_postAuthorIDs":[],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[],
										"_listExcludeDuplicates":false,
										"_postExcludeCurrent":true,
										"_exit":"",
										"_before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">",
										"_content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>",
										"_after":"<\/ul>"
									},
									"APL_Preset_Obj-jsonString_sample":
									{
										"_postParent":["0"],
										"_postTax":
										{
											"post":
											{
												"taxonomies":
												{
													"category":
													{
														"require_taxonomy":false,
														"require_terms":false,
														"include_terms":false,
														"terms":[1]
													},
													"post_tag":
													{
														"require_taxonomy":false,
														"require_terms":false,
														"include_terms":false,
														"terms":[0]
													}
												}
											}
										},
										"_listCount":5,
										"_listOrderBy":"random",
										"_listOrder":"DESC",
										"_postVisibility":["public","private"],
										"_postStatus":["publish","future","pending"],
										"_userPerm":"editable",
										"_postAuthorOperator":"include",
										"_postAuthorIDs":[1,2],
										"_listIgnoreSticky":false,
										"_listExcludePosts":[1,2,3],
										"_listExcludeDuplicates":true,
										"_postExcludeCurrent":true,
										"_exit":"<p>Exit Sample Message<\/p>",
										"_before":"<p><hr\/>",
										"_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>",
										"_after":"<\/p>"
									}
								}';
		// STEP 3.
		$this->_preset_db = json_decode( $temp_preset );
	}
}
