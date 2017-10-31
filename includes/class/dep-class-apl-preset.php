<?php
/**
 * APL Preset Class
 *
 * Preset Object used by Preset Db.
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package WordPress
 * @subpackage advanced-post-list.php
 * @since 0.1.0
 * @deprecated 0.4.0 Use APL_Post_List and APL_Design class
 */

/**
 * APL Preset
 *
 * Preset Object that is used by Preset Db to store within the database.
 *
 * @since 0.1.0
 */
class APL_Preset {

	/**
	 * Filter by Page Parents.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array=>string
	 */
	public $_postParents;

	/**
	 * Filter by Post Type and Taxonomy structure.
	 *
	 * @since 0.3.0
	 * @var object
	 */
	public $_postTax;

	/**
	 * Post List Amount.
	 *
	 * @since 0.1.0
	 * @version 0.3.0  - Changed (string) to (int).
	 * @var int
	 */
	public $_listCount;

	/**
	 * Order Filter By.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $_listOrderBy;

	/**
	 * Order Filter Ascending or Descending.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $_listOrder;

	/**
	 * Filter by Post Visibility.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $_postVisibility;

	/**
	 * Filter by Post Status.
	 *
	 * @since 0.3.0
	 * @version 0.3.b5 - Change from (string) to (array) => (string).
	 * @var array
	 */
	public $_postStatus;

	/**
	 * Filter by User Permissions.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	public $_userPerm;

	/**
	 * Operator for Author ID filter.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	public $_postAuthorOperator;

	/**
	 * Filter by Author ID.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $_postAuthorIDs;

	/**
	 * Filter Stickies.
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $_listIgnoreSticky;

	/**
	 * Filter Posts.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $_listExcludePosts;

	/**
	 * Filter Duplicates.
	 *
	 * @since 0.3.0
	 * @var boolean
	 */
	public $_listExcludeDuplicates;

	/**
	 * Filter Current Post/Page.
	 *
	 * @since 0.1.0
	 * @version 0.3.0 - changed (string) to (boolean).
	 * @var boolean
	 */
	public $_listExcludeCurrent;

	/**
	 * Design for APL Preset Loop.
	 *
	 * @since 0.4.0
	 * @var string
	 */
	public $apl_design;

	/**
	 * Constructor.
	 *
	 * Object constructor.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 - Changed: Filter for Page Parents, Post Status.
	 *                Added: Filter for Custom Post Type and Taxonomy support,
	 *                       User Perms, Author IDs, Author Include/Exclude,
	 *                       Ignore Sticky Posts, Exclude Duplicates, Exclude Posts.
	 *                       Design for Empty Message.
	 * @since 0.4.0 - Changed: before, content, after, & exit to APL_Design Class.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_postParents = (array) array();
		$this->_postTax     = (object) new stdClass();

		$this->_listCount   = (int) 5;

		$this->_listOrderBy = (string) '';
		$this->_listOrder   = (string) '';

		$this->_postVisibility        = (array) array( 'public' );
		$this->_postStatus            = (array) array( 'publish' );
		$this->_userPerm              = (string) 'readable';
		$this->_postAuthorOperator    = (string) 'none';
		$this->_postAuthorIDs         = (array) array();
		$this->_listIgnoreSticky      = (bool) false;
		$this->_listExcludeCurrent    = (bool) true;
		$this->_listExcludeDuplicates = (bool) false;
		$this->_listExcludePosts      = array();

		$this->apl_design = (string) '';
		// TODO Change to Design slug. Default: preset_name.
	}

	/**
	 * Returns the APL Design slug.
	 *
	 * Gets and returns $this->apl_design.
	 *
	 * @since 0.4.0
	 *
	 * @return string Returns the slug from variable APL Design.
	 */
	public function get_apl_design() {
		$apl_design = apply_filters( 'apl_design_slug', $this->apl_design, $this );
		return $apl_design;
	}

	/**
	 * Set Preset to Version.
	 *
	 * Sets Preset Object to a set version of variables.
	 *
	 * @since 0.3.0
	 *
	 * @param string $version A previous version number.
	 * @return void
	 */
	public function reset_to_version( $version ) {
		// STEP - Unsets $this object's variables.
		foreach ( $this as $key => &$value ) {
			$value = null;
			unset( $this->$key );
		}
		if ( version_compare( '0.3.a1', $version, '>' ) ) {
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
	 * Reset to initial/base.
	 *
	 * Resets the object to its initial verion values.
	 *
	 * @since 0.3.a
	 * @access private
	 *
	 * @return void
	 */
	private function reset_to_base() {
		$this->_before             = '';
		$this->_content            = '';
		$this->_after              = '';
		// array( int ) - All.
		$this->_catsSelected       = '';
		// (string) - All.
		$this->_tagsSelected       = '';
		// (boolean) - Unchecked.
		$this->_catsInclude        = 'false';
		// (boolean) - Unchecked.
		$this->_tagsInclude        = 'false';
		// (boolean) - Unchecked.
		$this->_catsRequired       = 'false';
		// (boolean) - Unchecked.
		$this->_tagsRequired       = 'false';
		// (string) - Desc.
		$this->_listOrder          = '';
		// (string) - Type.
		$this->_listOrderBy        = '';
		// (int) - Number of Posts.
		$this->_listAmount         = '';
		// (string) - Post Type. Example: post or page.
		$this->_postType           = '';

		$this->_postParent         = '';
		// (boolean) - Unchecked.
		$this->_postExcludeCurrent = 'false';
	}

	/**
	 * Reset to 0.3.a1.
	 *
	 * Sets the object to version 0.3.a1 variables.
	 *
	 * @since 0.3.a1
	 * @access private
	 *
	 * @return void
	 */
	private function reset_to_03a1() {
		$this->_postParent         = (array) array();
		$this->_postTax            = (object) new stdClass();
		$this->_listAmount         = (int) 5;
		$this->_listOrderBy        = (string) '';
		$this->_listOrder          = (string) '';
		$this->_postStatus         = (string) '';
		$this->_postExcludeCurrent = (bool) true;
		$this->_before             = (string) '';
		$this->_content            = (string) '';
		$this->_after              = (string) '';
	}
	/**
	 * Reset to 0.3.a1.
	 *
	 * Sets the object to version 0.3.b5 variables.
	 *
	 * @since 0.3.b5
	 * @access private
	 *
	 * @return void
	 */
	private function reset_to_03b5() {
		$this->_postParents            = (array) array();
		$this->_postTax                = (object) new stdClass();
		$this->_listCount              = (int) 5;
		$this->_listOrderBy            = (string) '';
		$this->_listOrder              = (string) '';
		$this->_postVisibility         = (array) array( 'public' );
		$this->_postStatus             = (array) array( 'publish' );
		$this->_userPerm               = (string) 'readable';
		$this->_postAuthorOperator     = (string) 'none';
		$this->_postAuthorIDs          = (array) array();
		$this->_listIgnoreSticky       = (bool) false;
		$this->_listExcludeCurrent     = (bool) true;
		$this->_listExcludeDuplicates  = (bool) false;
		$this->_listExcludePosts       = array();
		$this->_exit                   = (string) '';
		$this->_before                 = (string) '';
		$this->_content                = (string) '';
		$this->_after                  = (string) '';
	}

	/**
	 * Reset to 0.4.0.
	 *
	 * Sets the object to version 0.4.0 variables.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return void
	 */
	private function reset_to_040() {
		$this->_postParents           = (array) array();
		$this->_postTax               = (object) new stdClass();
		$this->_listCount             = (int) 5;
		$this->_listOrderBy           = (string) '';
		$this->_listOrder             = (string) '';
		$this->_postVisibility        = (array) array( 'public' );
		$this->_postStatus            = (array) array( 'publish' );
		$this->_userPerm              = (string) 'readable';
		$this->_postAuthorOperator    = (string) 'none';
		$this->_postAuthorIDs         = (array) array();
		$this->_listIgnoreSticky      = (bool) false;
		$this->_listExcludeCurrent    = (bool) true;
		$this->_listExcludeDuplicates = (bool) false;
		$this->_listExcludePosts      = array();
		$this->apl_design             = '';
	}
}
