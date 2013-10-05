<?php

class APLPresetDbObj
{

  var $_preset_db_name;
  var $_preset_db;
  var $_delete;

  function __construct($db_name)
  {
    $this->_preset_db_name = 'APL_preset_db-' . $db_name;
    $this->option_load_db();
    
    //If data doesn't exist in options, then make one
    $a = empty($this->_preset_db);
    if (empty($this->_preset_db) && empty($this->_delete))
    {
      
      $this->set_to_defaults();
      $this->options_save_db();
      $this->option_load_db();
    }
    
  }
  
  
  
  function option_load_db()
  {
    //$this->_option_db_name = 'APL_option_db_'.$db_name;
    $DBOptions = get_option($this->_preset_db_name);
    $this->_preset_db = $DBOptions->_preset_db;
    $this->_delete = $DBOptions->_delete;
  }
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
  function options_remove_db()
  {
    delete_option($this->_preset_db_name);
  }
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
//    //EDIT _preset_db
//    $APLPresetOptions['_preset_db'] = '{"pageContentDivided_5":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"postExcerptDivided_5":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"5","before":"<p><hr\/>","content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simpleAttachmentList_10":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"10","before":"<ul>","content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"images_5":{"categories":"","tags":"","post_type":"attachment","orderby":"post_date","order":"DESC","numberposts":"5","before":"<hr \/>","content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>","after":"<hr \/>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"pageDropdown_100":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"100","before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">","content":"<option value=\"[post_permalink]\">[post_title]<\/option>","after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"simplePostList_5":{"categories":"","tags":"","post_type":"post","orderby":"date","order":"DESC","numberposts":"5","before":"<p>","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"footerPageList_10":{"categories":"","tags":"","post_type":"page","orderby":"menu_order","order":"ASC","numberposts":"10","before":"<p align=\"center\">","content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"false"},"everythingNumbered_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"everythingID_200":{"categories":"","tags":"","post_type":"any","orderby":"date","order":"ASC","numberposts":"200","before":"<p>All my pages and posts (roll over for titles):<br\/>","content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"false","includeCats":"false","includeTags":"false"},"relatedPosts_5":{"categories":"","tags":"","post_type":"post","orderby":"rand","order":"DESC","numberposts":"5","before":"<p>Related posts: ","content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ","after":"<\/p>","excludeCurrent":"true","includeCats":"false","includeTags":"true"},"CSSTable":{"categories":"","tags":"","post_type":"post","orderby":"post_date","order":"DESC","numberposts":"15","before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">","content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>","after":"<\/ul>","excludeCurrent":"true","post_parent":"None","includeCats":"false","includeTags":"false","requireAllCats":"false","requireAllTags":"false"}}';


    
  }
  
  
  function load_preset($id_name)
  {
    return $this->_preset_db[$id_name];
  }
  function save_preset()
  {
    
  }
  function delete_preset()
  {
    
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
