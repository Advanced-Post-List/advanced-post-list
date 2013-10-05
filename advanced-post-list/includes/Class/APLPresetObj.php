<?php

class APLPresetObj
{
  //Varibles
  //Holds the content that the user can modify
  var $_before = '';
  var $_content = '';
  var $_after = '';
  
  //Holds all the category & tag data that is used for filtering
  var $_catsSelected;//All//(int) array
  var $_tagsSelected;//All
  var $_catsInclude = false;//Boolean Unchecked
  var $_tagsInclude = false;//Boolean Unchecked
  var $_catsRequired = false;//Boolean Unchecked
  var $_tagsRequired = false;//Boolean Unchecked
  
  //Settings that will be used for how this is displayed in the list
  var $_listOrder;//Desc
  
  var $_listOrderBy;//(string) Type
  var $_listAmount;//(int) howmany to display
  
  //Post attributes
  var $_postType;//(string) post or page
  var $_postParent;
  //leave out the current page/post that the plugin is displaying on.
  var $_postExcludeCurrent= false;//Boolean Unchecked
  
  
  function __construct()
  {
    
  }
  
}
?>
