<?php

class APLPresetDbObj
{

  /**
   * @var string
   * @since 0.1.0
   */
  var $_preset_db_name;

  /**
   * @var array(APLPresetObj())
   * @since 0.1.0
   */
  var $_preset_db;

  /**
   * @var string
   * @since 0.1.0
   */
  var $_delete;

  /**
   * Name: APL Preset Object Constructor
   * Desc: 
   * @param string $db_name 
   * return none
   * 
   * @since 0.1.0
   * 
   * 1) 
   */
  function __construct($db_name)
  {
    $this->_preset_db_name = 'APL_preset_db-' . $db_name;
    $this->option_load_db();
    
    //If data doesn't exist in options, then make one
    if (empty($this->_preset_db) && empty($this->_delete))
    {
      
      $this->set_to_defaults();
      $this->options_save_db();
      //$this->option_load_db();
    }
    
  }
  
  
  /**
   * Name: APL Load Preset Option Database
   * Desc: 
   * @param none
   * 
   * @since 0.1.0
   *  
   * 1) 
   */
  function option_load_db()
  {
    //$this->_option_db_name = 'APL_option_db_'.$db_name;
    $DBOptions = get_option($this->_preset_db_name);
    $this->_preset_db = $DBOptions->_preset_db;
    $this->_delete = $DBOptions->_delete;
  }
  /**
   * Name: APL Save Preset Option Database
   * Desc: 
   * @param none
   * 
   * @since 0.1.0
   *  
   * 1) 
   */
  function options_save_db()
  {
    
    update_option($this->_preset_db_name, $this);
  }
//  function options_save_db($newOptions)
//  {
//    
//    //$this->_option_db_name = 'APL_option_db_'.$db_name;
//    //$this->update_options_db();
//    if (isset($newOptions))
//    {
//      $this->_preset_db = $newOptions->_preset_db;
//      update_option($newOptions->_preset_db_name, $newOptions);
//    }
//    
//  }
  /**
   * Name: APL Remove Preset Option Database
   * Desc: 
   * @param none
   * 
   * @since 0.1.0
   *  
   * 1) 
   */
  function options_remove_db()
  {
    delete_option($this->_preset_db_name);
  }
  /**
   * Name: APL Set Presets to Default
   * Desc: 
   * @param none 
   * return none
   * 
   * @since 0.1.0
   *   
   * 1) 
   */
  function set_to_defaults()
  {
    $this->_preset_db_name = 'APL_preset_db-default';
    $this->_preset_db = new stdClass();
    //$this->_preset_db = array();
    $this->_delete = true;
    $tmpPreset = (string) '{"pageContentDivided_5":{
                                                    "_before":"<p><hr\/>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                            "postExcerptDivided_5":{
                                                    "_before":"<p><hr\/>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                         "simpleAttachmentList_10":{
                                                    "_before":"<ul>",
                                                    "_content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>",
                                                    "_after":"<\/ul>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                        "images_5":{
                                                    "_before":"<hr \/>",
                                                    "_content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>",
                                                    "_after":"<hr \/>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "pageDropdown_100":{
                                                    "_before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">",
                                                    "_content":"<option value=\"[post_permalink]\">[post_title]<\/option>",
                                                    "_after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "simplePostList_5":{
                                                    "_before":"<p>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                               "footerPageList_10":{
                                                     "_before":"<p align=\"center\">",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                          "everythingNumbered_200":{
                                                    "_before":"<p>All my pages and posts (roll over for titles):<br\/>",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "everythingID_200":{
                                                    "_before":"<p>All my pages and posts (roll over for titles):<br\/>",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                  "relatedPosts_5":{
                                                    "_before":"<p>Related posts: ",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                        "CSSTable":{
                                                    "_before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">",
                                                    "_content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>",
                                                    "_after":"<\/ul>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   }
                                             }';
    $this->_preset_db = json_decode($tmpPreset);

  }
  
  
 
  
  
}
/*
 * //Load Options if any
    foreach ($name as $key)
    {
      $preset_options[$name[$key]] = get_option('APL_preset_' . $name);
      //TODO create other options for creating groups of post 
      //      list settings.
    }
    if (!isset($preset_options))
    {
      install();
    }
*/
?>
