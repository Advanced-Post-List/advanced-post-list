<?php

class APLCallback
{//class used just for all the preg_replace_callback function calls

  function postPDFCallback()
  {
    if ($this->post_type == "page")
    {
      $postID = "pg_" . $this->post_id;
    }
    else
    {
      $postID = "po_" . $this->post_id;
    }
    return get_bloginfo('wpurl') . '/wp-content/plugins/kalins-pdf-creation-station/kalins_pdf_create.php?singlepost=' . $postID;
  }

  function postExcerptCallback($matches)
  {


    $pageContent = strip_tags($this->page->post_content);
//return "blah" .$exLength;
    if ($this->page->post_excerpt == "")
    {//if there's no excerpt applied to the post, extract one
//$postCallback->pageContent = strip_tags($page->post_content);
//$str = preg_replace_callback('#\[ *post_excerpt *(length=[\'|\"]([^\'\"]*)[\'|\"])? *\]#', array(&$postCallback, 'postExcerptCallback'), $str);
      if (isset($matches[2]))
      {
        $exLength = intval($matches[2]);
      }
      else
      {
        $exLength = 250;
      }

//return "blah";

      if (strlen($pageContent) > $exLength)
      {
        return strip_shortcodes(substr($pageContent, 0, $exLength)) . "..."; //clean up and return excerpt
      }
      else
      {
        return strip_shortcodes($pageContent);
      }
    }
    else
    {//if there is a post excerpt just use it and don't generate our own
      /* if(isset($matches[2])){//uncomment this if/else statement if you want the manual excerpt to be trimmed to the passed in length
        $exLength = intval($matches[2]);
        return substr($this->page->post_excerpt, 0, $exLength);
        }else{
        return $this->page->post_excerpt;
        } */
      return $this->page->post_excerpt;
    }
  }

  function postDateCallback($matches)
  {
    if (isset($matches[2]))
    {//geez, regex's are awesome. the [2] grabs the second internal portion of the regex, the actual shortcode param value, the () within the ()
      return mysql2date($matches[2], $this->curDate, $translate = true); //translate the wordpress formatted date into whatever date formatting the user passed in
    }
    else
    {
      return mysql2date("m-d-Y", $this->curDate, $translate = true); //otherwise do a simple day-month-year format
    }
  }

  function postCountCallback($matches)
  {
    if (isset($matches[4]))
    {
      $increment = $matches[4];
    }
    else
    {
      $increment = 1; //default is to increment by 1 each loop
    }

    if (isset($matches[2]))
    {
      return $this->itemCount * $increment + $matches[2];
    }
    else
    {
      return $this->itemCount * $increment + 1; //default is to start at 1
    }
  }

  function postMetaCallback($matches)
  {
    $arr = get_post_meta($this->page->ID, $matches[2]);
    return $arr[0];
  }

  function postCategoriesCallback($matches)
  {
    $catString = "";

    $categories = get_the_category($this->page->ID);
    $last_item = end($categories);

    if (isset($matches[2]))
    {
      $delimeter = $matches[2];
    }
    else
    {
      $delimeter = ', ';
    }

    if (isset($matches[4]) && strtolower($matches[4]) == 'false')
    {
      $links = false;
    }
    else
    {
      $links = true;
    }

    foreach ($categories as $category)
    {
      if ($links)
      {
        $catString = $catString . '<a href="' . get_category_link($category->cat_ID) . '" >' . $category->cat_name . '</a>';
      }
      else
      {
        $catString = $catString . $category->cat_name;
      }
      if ($category != $last_item)
      {
        $catString = $catString . $delimeter;
      }
    }

    return $catString;
  }

  function postTagsCallback($matches)
  {

    $catString = "";
    $categories = get_the_tags($this->page->ID);

    if (!$categories)
    {
      return "";
    }

    $last_item = end($categories);

    if (isset($matches[2]))
    {
      $delimeter = $matches[2];
    }
    else
    {
      $delimeter = ', ';
    }

    if (isset($matches[4]) && strtolower($matches[4]) == 'false')
    {
      $links = false;
    }
    else
    {
      $links = true;
    }

    foreach ($categories as $category)
    {
      if ($links)
      {
        $catString = $catString . '<a href="' . get_tag_link($category->term_id) . '" >' . $category->name . '</a>';
      }
      else
      {
        $catString = $catString . $category->name;
      }

      if ($category != $last_item)
      {
        $catString = $catString . $delimeter;
      }
    }

    return $catString;
  }
  //FIX Bug when using params that are not in order.
  //  Current idea is to check the first 4 chars and grab the value from the 
  //   next Array Variable.
  function postTermsCallback($matches)
  {
    //Add Empty Message param
    //Add Taxonomy param
    $taxonomy = 'category';
    if (isset($matches[2]) && taxonomy_exists($matches[2]))
    {
        $taxonomy = $matches[2];
    }
    
    $terms = get_the_terms($this->page->ID, $taxonomy);
    
    //If no terms exist, then return an Empty Message
    if (!$terms)
    {
        $empty = '';
        if (isset($matches[10]))
        {
            $empty = $matches[10];
        }
        return $empty;
    }

    $last_item = end($terms);
    
    $delimiter = ', ';
    if (isset($matches[4]))
    {
      $delimiter = $matches[4];
    }
    
    $links = true;
    if (isset($matches[6]) && strtolower($matches[6]) == 'false')
    {
      $links = false;
    }
    
    //FIX Check if value is an int
    $termString = "";
    $i = -1;
    if (isset($matches[8]))
    {
        $i = intval($matches[8]) - 1;
    }
    foreach ($terms as $term)
    {
      if ($links)
      {
        $termString = $termString . '<a href="' . get_tag_link($term->term_id) . '" >' . $term->name . '</a>';
      }
      else
      {
        $termString = $termString . $term->name;
      }

      if ($term != $last_item && ($i > 0 || $i < 0))
      {
        $termString = $termString . $delimiter;
      }
      if ($i == 0)
      {
          return $termString;
      }
      $i--;
    }

    return $termString;
  }

  function commentCallback($matches)
  {

    /*
      global $post;
      $post = $this->page;//set global post object just for comments
      query_posts('p=' .$this->page->ID);//for some reason this is also necessary so other plugins have access to values normally inside The Loop
     */

    if (defined("KALINS_PDF_COMMENT_CALLBACK"))
    {
      return call_user_func(KALINS_PDF_COMMENT_CALLBACK);
    }

    $comments = get_comments('status=approve&post_id=' . $this->page->ID);
    $commentString = $matches[2];

    foreach ($comments as $comment)
    {
      if ($comment->comment_author_url == "")
      {
        $authorString = $comment->comment_author;
      }
      else
      {
        $authorString = '<a href="' . $comment->comment_author_url . '" >' . $comment->comment_author . "</a>";
      }
      $commentString = $commentString . '<p>' . $authorString . "- " . $comment->comment_author_email . " - " . get_comment_date(null, $comment->comment_ID) . " @ " . get_comment_date(get_option('time_format'), $comment->comment_ID) . "<br />" . $comment->comment_content . "</p>";
    }

    return $commentString . $matches[4];
  }
  
  function postAuthorCallback($matches)
  {
    $userInfo = get_userdata($this->page->post_author);
    if(isset($matches[2]))
    {
    	return $userInfo->$matches[2];   
    }
    return $userInfo->display_name;
  }
  
  function postThumbCallback($matches)
  {
  	$imageUrl = "";
  	$mode = "none";
  	
  	if(isset($matches[4]))
  	{
  		switch(strtolower($matches[4]))
  		{
  			case "force":
  			case "on":
  				$mode = strtolower($matches[4]);
  				break;
  		}
  	}
  	
  	if ($mode != "force" && current_theme_supports('post-thumbnails'))
  	{
  	  if(isset($matches[2]))
  	  {
  		  $imageSize = $matches[2];
  	  }
  	  else
  	  {
  		  $imageSize = "full";
  	  }
  	 
  	  //the documentation for wp_get_attachment_image_src says you can pass in an array of width and height but that didn't seem to work.
  	  //It just returned the size closest to the values I passed in so we are stuck with the four options: thumbnail, medium, large or full
  	  $arr = wp_get_attachment_image_src(get_post_thumbnail_id($this->page->ID), $imageSize);
    
  	  $imageUrl = $arr[0];
  	}

  	//if we couldn't find an image and we have an "extract" paramater, search the page content for an image tag and extract its url
  	if ($imageUrl == "" && $mode != "none")
  	{
  		//found two regex's to do this. Not sure which is better so I randomly picked the second one
  		//both only grab the first image in the page so I'm not able to do the option where it can randomly select an image from the page instead of just the first one
  		//$postImages = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $this->page->post_content, $postMatches);
  		$postImages = preg_match_all("/<img .*?(?=src)src=\"([^\"]+)\"/si", $this->page->post_content, $postMatches);
  		
  		//I couldn't find a way to get the image ID without doing a whole other query so unfortunately we are stuck with the image as it appears in the page and the size parameter does not work here

  		//if we found an image tag with url
  		if(isset($postMatches[1]) && isset($postMatches[1][0]))
  		{
  		  $imageUrl = $postMatches[1][0];
  		}
  	}
  	 
  	return $imageUrl;
  }

  function postParentCallback($matches)
  {
    $parentID = $this->page->post_parent;

    if ($parentID == 0)
    {
      return "";
    }

    if ($matches[2] == "false")
    {
      return get_the_title($parentID);
    }
    else
    {
      return '<a href="' . get_permalink($parentID) . '" >' . get_the_title($parentID) . '</a>';
    }
  }

  function functionCallback($matches)
  {//call a user defined function through shortcode
    if (!defined("KALINS_ALLOW_PHP") || KALINS_ALLOW_PHP !== true)
    {
      return ' Error: add define("KALINS_ALLOW_PHP", true); to your wp-config.php for php_function to work. ';
    }

    if (!$matches[2])
    {
      return ' Error: injected PHP function must have a name. Add a name parameter to your php_function shortcode. ';
    }

    /*
      global $post;
      $post = $this->page;
      query_posts('p=' .$this->page->ID);//set global post object and post data so custom function has access to it
     */

    if ($matches[4])
    {
      return call_user_func($matches[2], $this->page, $matches[4]);
    }
    else
    {
      return call_user_func($matches[2], $this->page);
    }
  }

}

?>
