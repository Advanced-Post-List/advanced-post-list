<?php

class APLPresetObj
{
    //Varibles
    //Holds the content that the user can modify
    //TODO Create a better method for varible types. All varibles are 
    // recieving string values when used. Nothing bad, just good practice.
    
    
    /**
     * @var array => string
     * @since 0.1.0
     * @version 0.3.0 - changed (string) to (array) => (string)
     */
    public $_postParents;
    
    /**
     * @var object
     * @since 0.3.0
     */
    public $_postTax;
    
    /**
     * @var int
     * @since 0.1.0
     * @version 0.3.0  - changed (string) to (int)
     */
    public $_listCount;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_listOrderBy;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_listOrder;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_postVisibility;
    
    /**
     * @var array
     * @since 0.3.0
     * @version 0.3.b5 - Change from (string) to (array) => (string)
     */
    public $_postStatus;
    
    /**
     * @var string
     * @since 0.3.0 
     */
    public $_userPerm;
    
    /**
     * @var string
     * @since 0.3.0 
     */
    public $_postAuthorOperator;
    
    /**
     * @var array
     * @since 0.3.0 
     */
    public $_postAuthorIDs;
    
    /**
     * @var boolean
     * @since 0.3.0 
     */
    public $_listIgnoreSticky;
    
    /**
     * @var array
     * @since 0.3.0 
     */
    public $_listExcludePosts;

    /**
     * @var boolean
     * @since 0.3.0 
     */
    public $_listExcludeDuplicates;
    
    /**
     * @var boolean
     * @since 0.1.0
     * @version 0.3.0 - changed (string) to (boolean)
     */
    public $_listExcludeCurrent; 
    
    /**
     * @var string
     * @since 0.3.0 
     */
    public $_exit;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_before;

    /**
     * @var string
     * @since 0.1.0
     */
    public $_content;

    /**
     * @var string
     * @since 0.1.0
     */
    public $_after;
    
    
    
    

    public function __construct()
    {

        $this->_postParents = (array) array();
        $this->_postTax = (object) new stdClass();
        
        $this->_listCount = (int) 5;
        
        $this->_listOrderBy = (string)'';
        $this->_listOrder = (string) '';
        
        $this->_postVisibility = (array) array('public');
        $this->_postStatus = (array) array('publish');//Changed
        $this->_userPerm = (string) 'readable';//Added
        $this->_postAuthorOperator = (string) 'none';//Added
        $this->_postAuthorIDs = (array) array();//Added
        $this->_listIgnoreSticky = (bool) FALSE;//Added
        $this->_listExcludeCurrent = (bool) TRUE;
        $this->_listExcludeDuplicates = (bool) FALSE;//Added
        $this->_listExcludePosts = array();//Added
        
        $this->_exit = (string) '';
        $this->_before = (string) '';
        $this->_content = (string) '';
        $this->_after = (string) '';
        
    }
    
    public function reset_to_version($version)
    {
        foreach ($this as $key => &$value)
        {
            $value = null;
            unset($this->$key);
        }
        if (version_compare('0.3.a1', $version, '>'))
        {
            $this->reset_to_base();
        }
        else if (version_compare('0.3.a1', $version, '<=') && version_compare('0.3.b5', $version, '>'))
        {
            $this->reset_to_03a1();
        }
        else //if (version_compare('0.3.a1', $oldversion, '>'))
        {
            $this->reset_to_03b5();
        }
        
    }
    
    private function reset_to_base()
    {
        $this->_before = '';
        $this->_content = '';
        $this->_after = '';
        $this->_catsSelected = ''; //All//(int) array
        $this->_tagsSelected = ''; //All
        $this->_catsInclude = 'false'; //Boolean Unchecked
        $this->_tagsInclude = 'false'; //Boolean Unchecked
        $this->_catsRequired = 'false'; //Boolean Unchecked
        $this->_tagsRequired = 'false'; //Boolean Unchecked
        $this->_listOrder = ''; //Desc
        $this->_listOrderBy = ''; //(string) Type
        $this->_listAmount = ''; //(int) howmany to display
        $this->_postType = ''; //(string) post or page
        $this->_postParent = '';
        $this->_postExcludeCurrent = 'false'; //Boolean Unchecked
    }
    
    private function reset_to_03a1()
    {
        $this->_postParent = (array) array();
        $this->_postTax = (object) new stdClass();
        
        $this->_listAmount = (int) 5;
        
        $this->_listOrderBy = (string)'';
        $this->_listOrder = (string) '';
        
        $this->_postStatus = (string) '';
        
        $this->_postExcludeCurrent = (bool) true;
        
        $this->_before = (string) '';
        $this->_content = (string) '';
        $this->_after = (string) '';
    }
    private function reset_to_03b5()
    {
        $this->_postParents = (array) array();
        $this->_postTax = (object) new stdClass();
        
        $this->_listCount = (int) 5;
        
        $this->_listOrderBy = (string)'';
        $this->_listOrder = (string) '';
        
        $this->_postVisibility = (array) array('public');
        $this->_postStatus = (array) array('publish');//Changed
        $this->_userPerm = (string) 'readable';//Added
        $this->_postAuthorOperator = (string) 'none';//Added
        $this->_postAuthorIDs = (array) array();//Added
        $this->_listIgnoreSticky = (bool) FALSE;//Added
        $this->_listExcludeCurrent = (bool) TRUE;
        $this->_listExcludeDuplicates = (bool) FALSE;//Added
        $this->_listExcludePosts = array();//Added
        
        $this->_exit = (string) '';
        $this->_before = (string) '';
        $this->_content = (string) '';
        $this->_after = (string) '';
    }
}
