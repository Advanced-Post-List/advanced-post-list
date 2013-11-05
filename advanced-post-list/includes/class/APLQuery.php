<?php
//TODO COMPLETELY REDO THIS CLASS AND SET IT UP TO HAVE A HANDLER AND CHILD CLASS
//PLEASE NOTE: MAKING A CHILD CLASS OF WP_QUERY MAY BE SOMETHING RESERVED LATER 
//  FOR PREMIUM USE. THE OPTION IS STILL UP FOR DEBATE, BUT IF FOLLOWED THROUGH
//  SOME FEATURES MAY REQUIRE CONTINUAL SUPPORT.
//LIST OF POSSIBLE FIXES AND FEATURES EXTENDING COULD OFFER
// * Better sticky support
// * Can add additional sorting methods

class WP_Query_child extends WP_Query
{
    
}
class APLQuery
{
    /**
     * @var array
     * @since 0.3.0
     * @todo Remove this...
     */
    public $_posts;
    /**
     * <p><b>Desc:</b> Plugin's shadowed version of WP_Query Class. This class
     *                 was created to add additional funtions that WP_Query
     *                 doesn't have.</p>
     * @access public
     * @param Object $presetObj Holds the post list preset data.
     * 
     * @since 0.3.0
     * @todo Needs work and to be organzied. This is just some code slapped
     *       into a class at the moment to prepare for future modifications 
     *       and additions.
     * 
     * @uses file.ext|elementname|class::methodname()|class::$variablename|
     *        functionname()|function functionname description of how the 
     *        element is used
     * 
     * @tutorial 
     * <ol>
     * <li value="1"></li>
     * <li value="2"></li>
     * </ol>
     */
    
    
    //// Notes for apl_query_mod Oct 2013 ////
    
    //Name: Constructor
    //1) Store/set query settings from the presetObj to be used for WP_Query
    //2) Query Posts
    //3) Return ??? - Final WP_Query object, Posts Array
    //   OR Set - Posts
    
    //Name: Set Query 
    //1) For each post type, do step 2
    //2) Set query according to taxonomies/terms and other params
    //3) Set any global variable
    //4) Return query string/array/object
    //
    //REF: (wp_query_ref) https://gist.github.com/luetkemj/2023628
    //
    
    //Name: Query Posts
    //(Note: Function needs to repeat itself for each query string before a query 
    //       is made, and each function instance needs to use only one query)
    //1) If more query string exist, do this function
    //2) Get post according to query string (taxonomies/terms and other params)
    //3) Check requirements/filters that WP didn't get
    //
    //4) Return Final WP_Query Object or Page/Post IDs
    //
    private function arg_example()
    {
        
    
    $args = array( 

    //////Author Parameters - Show posts associated with certain author.
        'author' => '1,2,-3,',                     //(int) - use author id [use minus (-) to exclude authors by ID ex. 'author' => '-1,-2,-3,']


    //////Taxonomy Parameters - Show posts associated with certain taxonomy.
        //Important Note: tax_query takes an array of tax query arguments 
        // arrays (it takes an array of arrays)
        //This construct allows you to query multiple taxonomies by using the 
        // relation parameter in the first (outer) array to describe the boolean 
        // relationship between the taxonomy queries.
        'tax_query' => array(                       //(array) - use taxonomy parameters (available with Version 3.1).
            'relation' => 'OR',                     //(string) - Possible values are 'AND' or 'OR' and is the equivalent of ruuning a JOIN for each taxonomy
            array(
                'taxonomy' => 'color',              //(string) - Taxonomy.
                'field' => 'id',                    //ID//(string) - Select taxonomy term by ('id' or 'slug')
                'terms' => array( 103, 115, 206 ),  //ARRAY(INT)//(int/string/array) - Taxonomy term(s).
                'include_children' => false,        //FALSE     //(bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
                'operator' => 'IN'                  //IN        //(string) - Operator to test. Possible values are 'IN', 'NOT IN', 'AND'.
            ),
            array(
                'taxonomy' => 'actor',
                'field' => 'id',
                'terms' => array( 103, 115, 206 ),
                'include_children' => false,
                'operator' => 'AND'
             )
        ),

    //////Post & Page Parameters - Display content based on post and page parameters.
        //'p' => 1,                             //(int) - use post id.
        //'name' => 'hello-world',              //(string) - use post slug.
        //'page_id' => 1,                       //(int) - use page id.
        //'pagename' => 'sample-page',          //(string) - use page slug.
        //'pagename' => 'contact_us/canada',    //(string) - Display child page using the slug of the parent and the child page, separated ba slash
        'post_parent' => 1,                     //(int) - use page id. Return just the child Pages. (Only works with heirachical post types.) 
        'post__in' => array(1,2,3),             //(array) - use post ids. Specify posts to retrieve.
        'post__not_in' => array(1,2,3),         //(array) - use post ids. Specify post NOT to retrieve.
        //NOTE: you cannot combine 'post__in' and 'post__not_in' in the same query

    //////Type & Status Parameters - Show posts associated with certain type or status.
        'post_type' => array(                   //(string / array) - use post types. Retrieves posts by Post Types, default value is 'post';
                'post',                         // - a post.
                'page',                         // - a page.
                'revision',                     // - a revision.
                'attachment',                   // - an attachment. The default WP_Query sets 'post_status'=>'published', but atchments default to 'post_status'=>'inherit' so you'll need to set the status to 'inherit' or 'any'.
                'my-custom-post-type',          // - Custom Post Types (e.g. movies)
                ),  
        'post_status' => array(                 //(string / array) - use post status. Retrieves posts by Post Status, default value i'publish'.         
                'publish',                      // - a published post or page.
                'pending',                      // - post is pending review.
                'draft',                        // - a post in draft status.
                'auto-draft',                   // - a newly created post, with no content.
                'future',                       // - a post to publish in the future.
                'private',                      // - not visible to users who are not logged in.
                'inherit',                      // - a revision. see get_children.
                'trash'                         // - post is in trashbin (available with Version 2.9).
                ),

        //NOTE: The 'any' keyword available to both post_type and post_status 
        // queries cannot be used within an array.
        //DO NOT USE
        //'post_type' => 'any',                 // - retrieves any type except revisions and types with 'exclude_from_search' set to true.
        //'post_status' => 'any',               // - retrieves any status except those from post types with 'exclude_from_search' set to true.



    //////Pagination Parameters
        //'posts_per_page' => 10,               //(int) - number of post to show per page (available with Version 2.1). Use 'posts_per_page'=1 to show all posts. Note if the query is in a feed, wordpress overwrites this parameter with the stored 'posts_per_rss' option. Treimpose the limit, try using the 'post_limits' filter, or filter 'pre_option_posts_per_rss' and return -1
        //'posts_per_archive_page' => 10,       //(int) - number of posts to show per page - on archive pages only. Over-rides showposts anposts_per_page on pages where is_archive() or is_search() would be true
        'nopaging' => true,                     //(bool) - show all posts or use pagination. Default value is 'false', use paging.
        //'paged' => get_query_var('paged'),    ////(int) - number of page. Show the posts that would normally show up just on page X when usinthe "Older Entries" link.
                                                //NOTE: Use get_query_var('page'); if you want your query to work in a Page template that you've set as your static front page. The query variable 'page' holds the pagenumber for a single paginated Post or Page that includes the <!--nextpage--> Quicktag in the post content.



    //////Offset Parameter
        'offset' => 3,                          //(int) - number of post to displace or pass over.

    //////Order & Orderby Parameters - Sort retrieved posts.
        'order' => 'DESC',                      //(string) - Designates the ascending or descending order of the 'orderby' parameter. Defaultto 'DESC'.
                                                //  Possible Values:
                                                //  'ASC' - ascending order from lowest to highest values (1, 2, 3; a, b, c).
                                                //  'DESC' - descending order from highest to lowest values (3, 2, 1; c, b, a).
        'orderby' => 'date',                    //(string) - Sort retrieved posts by parameter. Defaults to 'date'.
                                                //Possible Values://
                                                //  'none' - No order (available with Version 2.8).
                                                //  'ID' - Order by post id. Note the captialization.
                                                //  'author' - Order by author.
                                                //  'title' - Order by title.
                                                //  'date' - Order by date.
                                                //  'modified' - Order by last modified date.
                                                //  'parent' - Order by post/page parent id.
                                                //  'rand' - Random order.
                                                //  'comment_count' - Order by number of comments (available with Version 2.9).
                                                //  'menu_order' - Order by Page Order. Used most often for Pages (Order field in the EdiPage Attributes box) and for Attachments (the integer fields in the Insert / Upload MediGallery dialog), but could be used for any post type with distinct 'menu_order' values (theall default to 0).
                                                //  'meta_value' - Note that a 'meta_key=keyname' must also be present in the query. Note alsthat the sorting will be alphabetical which is fine for strings (i.e. words), but can bunexpected for numbers (e.g. 1, 3, 34, 4, 56, 6, etc, rather than 1, 3, 4, 6, 34, 56 as yomight naturally expect).
                                                //  'meta_value_num' - Order by numeric meta value (available with Version 2.8). Also notthat a 'meta_key=keyname' must also be present in the query. This value allows for numericasorting as noted above in 'meta_value'.
                                                //  'title menu_order' - Order by both menu_order AND title at the same time. For more info see: http://wordpress.stackexchange.com/questions/2969/order-by-menu-order-and-title
                                                //  'post__in' - Preserve post ID order given in the post__in array (available with Version 3.5).


    //////Sticky Post Parameters - Show Sticky Posts or ignore them.
        'ignore_sticky_posts' => false,         //(bool) - ignore sticky posts or not. Default value is false, don't ignore. Ignore/excludsticky posts being included at the beginning of posts returned, but the sticky post will still be returned in the natural order othat list of posts returned.
        //NOTE: For more info on sticky post queries see: 
        //http://codex.wordpress.org/Class_Reference/WP_Query#Sticky_Post_Parameters


    //////Time Parameters - Show posts associated with a certain time period.
        //NOTE: May need meta_query
        'year' => 2012,                         //(int) - 4 digit year (e.g. 2011).
        'monthnum' => 3,                        //(int) - Month number (from 1 to 12).
        'w' =>  25,                             //(int) - Week of the year (from 0 to 53). Uses the MySQL WEEK command. The mode is dependenon the "start_of_week" option.
        'day' => 17,                            //(int) - Day of the month (from 1 to 31).
        'hour' => 13,                           //(int) - Hour (from 0 to 23).
        'minute' => 19,                         //(int) - Minute (from 0 to 60).
        'second' => 30,                         //(int) - Second (0 to 60).


    //////Permission Parameters - Display published posts, as well as private 
    ////// posts, if the user has the appropriate capability:
        'perm' => 'readable',                   //(string) Possible values are 'readable', 'editable' (possible more ie all capabilitiealthough I have not tested)

    //////Parameters relating to caching
        //'no_found_rows' => false,             //(bool) Default is false. WordPress uses SQL_CALC_FOUND_ROWS in most queries in order timplement pagination. Even when you don�t need pagination at all. By Setting this parameter to true you are telling wordPress not tcount the total rows and reducing load on the DB. Pagination will NOT WORK when this parameter is set to true. For more informatiosee: http://flavio.tordini.org/speed-up-wordpress-get_posts-and-query_posts-functions
        //'cache_results' => true,              //(bool) Default is true
        //'update_post_term_cache' => true,     //(bool) Default is true
        //'update_post_meta_cache' => true,     //(bool) Default is true
        //NOTE Caching is a good thing. Setting these to false is generally not advised. For more info on usage see: http://codex.wordpresorg/Class_Reference/WP_Query#Permission_Parameters

    //////Search Parameter
        's' => $s,                              //(string) - Passes along the query string variable from a search. For example usage see: http://www.wprecipes.com/how-to-display-the-number-of-results-in-wordpress-search 
        'exact' => true,                        //(bool) - flag to make it only match whole titles/posts - Default value is false. For more information see: https://gist.github.com/2023628#gistcomment-285118
        'sentence' => true,                     //(bool) - flag to make it do a phrase search - Default value is false. For more information see: https://gist.github.com/2023628#gistcomment-285118 NOTE: Previously 'sentence' was spelled 'sentance' per the gist comment linked above. I believe that was a typo. Have not tested personaly.

    //////Post Field Parameters
        //Not sure what these do. For more info see: 
        // http://codex.wordpress.org/Class_Reference/WP_Query#Post_Field_Parameters

    //////Filters
        //For more information on available Filters see: 
        // http://codex.wordpress.org/Class_Reference/WP_Query#Filters

    );
    }
    
//  object(APLPresetObj)[282]
//  public '_postParents' => 
//    array (size=1)
//      0 => string '26' (length=2)
//  public '_postTax' => 
//    object(stdClass)[283]
//      public 'post' => 
//        object(stdClass)[284]
//          public 'taxonomies' => 
//            object(stdClass)[285]
//              ...
//      public 'cpt02' => 
//        object(stdClass)[288]
//          public 'taxonomies' => 
//            object(stdClass)[289]
//              ...
//  public '_listCount' => int 5
//  public '_listOrderBy' => string 'date' (length=4)
//  public '_listOrder' => string 'DESC' (length=4)
//  public '_postVisibility' => 
//    array (size=1)
//      0 => string 'public' (length=6)
//  public '_postStatus' => 
//    array (size=1)
//      0 => string 'publish' (length=7)
//  public '_userPerm' => string 'readable' (length=8)
//  public '_postAuthorOperator' => string 'include' (length=7)
//  public '_postAuthorIDs' => 
//    array (size=0)
//      empty
//  public '_listIgnoreSticky' => boolean false
//  public '_listExcludePosts' => 
//    array (size=1)
//      0 => int 42
//  public '_listExcludeDuplicates' => boolean false
//  public '_listExcludeCurrent' => boolean true
//  public '_exit' => string '' (length=0)
//  public '_before' => string '<p><hr/>' (length=8)
//  public '_content' => string '<a href="[post_permalink]">[post_title]</a> by [post_author] - [post_date]<br/>[post_excerpt]<hr/>' (length=98)
//  public '_after' => string '</p>' (length=4)
    
    private function set_query_init()
    {
        //Instead of using 'any' in $arg['post_type'], use $post_type_list below.
        $post_type_list = get_post_types('', 'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_list[$value]);
        }
        unset($value);
        unset($skip_post_types);

        //\\vv  EXAMPLE  vv////
        $arg = array(
            'author' => '',//this will need to be passed to other queries
            
            'post__in' => array(1,2,3),
            'post__not_in' => array(1,2,3),//DO NOT USE - there will be a manual function at the end
            'post_type' => array(//Passes remaining post types
                    'post',
                    'page',
                    'revision',
                    'attachment',
                    'my-custom-post-type',
                    ),
            'post_status' => array(//passed
                    'publish',
                    'pending',
                    'draft',
                    'auto-draft',
                    'future',
                    'private',
                    'inherit',
                    'trash'
                    ),
            
            'order' => 'DESC',//Final or Pass for trimmings?
            'orderby' => 'date',//Final or Pass for trimmings?
            
            'perm' => 'readable',//Passed
            
            'nopaging' => true,//Final or ALL
            'ignore_sticky_posts' => false,//Maybe Final, or may be passed
            
        );////^^  EXAMPLE  ^^\\//
    }
    
    //Create an INIT function to set defaults?
    private function set_query($presetObj)
    {
        
        //\\vv  EXAMPLE  vv////
        $arg_example = array(
            'author' => '1,2,-3,',//this will need to be passed to other queries
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'color',
                    'field' => 'id',
                    'terms' => array( 103, 115, 206 ),
                    'include_children' => false,
                    'operator' => 'IN'
                ),
                array(
                    'taxonomy' => 'actor',
                    'field' => 'id',
                    'terms' => array( 103, 115, 206 ),
                    'include_children' => false,
                    'operator' => 'AND'
                 )
            ),
            'post_parent' => 1,
            'post__in' => array(1,2,3),
            'post__not_in' => array(1,2,3),//DO NOT USE - there will be a manual function at the end
            'post_type' => array(//Passes remaining post types
                    'post',
                    'page',
                    'revision',
                    'attachment',
                    'my-custom-post-type',
                    ),
            'post_status' => array(//passed
                    'publish',
                    'pending',
                    'draft',
                    'auto-draft',
                    'future',
                    'private',
                    'inherit',
                    'trash'
                    ),
            'nopaging' => true,//Final or ALL
            'order' => 'DESC',//Final or Pass for trimmings?
            'orderby' => 'date',//Final or Pass for trimmings?
            'ignore_sticky_posts' => false,//Maybe Final, or may be passed
            'perm' => 'readable',//Passed
            
        );////^^  EXAMPLE  ^^\\//
        
        
        
        //foreach post type
        //  (catch array) if there exists another (post_type) string, do this function, and send remaining strings
        //  if there is a page parent (including current). Set ID
        //    (catch array) if there is more parents, do this function, and send remaining IDs
        //  foreach taxonomy
        //    Check if require taxonomy is selected
        //    Add terms
        //    Check if required terms is selected
        //    Check if include current page is selected, and taxonomy matches. Add terms if any.
        //
        //Fill in query
        //
        //Set public or private
        // if both are selected, just duplicate 
        //

        
        //Clone since this is a repeating function and the variable keeps acting
        // like a pointer...to my surprise. I don't know if it is just objects that
        // are effected or I'm using 5.3 atm, but I thought you had to return the 
        // value if you wanted it changed. Not the ability to modify it a 
        // multitude of stacks in a repeating method.
        //Would making this method static prevent the presetObj acting like 
        // a pointer?
        //$preset = new APLPresetObj();
        $preset = clone $presetObj;
        $preset->_postTax = clone $presetObj->_postTax;
        
        //Used for colecting and returning an array of $query_str
        $query_str_arrays = array();//array(array) - Multi-Dimensional
        //Used for this current instance of set_query
        $query_str = array(); 
        
        //REPLACE Current Page Parent WITH CURRENT ID
        
        
        ////POST_TYPES & TAXONOMIES + POST_PARENTS
        //DON'T USE A FOR LOOP for post_types
        $post_type_key = key((array) $preset->_postTax);
        if ($post_type_key !== null) //or use !empty()?
        {
            //Cycle through the Page* Parent array and match the Post Type
            //Use this type of FOR loop in order to use the index as a counter
            for ($i = 0; $i < count($preset->_postParents); $i++)
            {
                
                
                //If post type matches
                if (get_post_type($preset->_postParents[$i]) == $post_type_key)
                {
                    //-vv- This would eliminate the 3 lines of code -vv-//
                    //$query_str['post_parent'] = array_shift($preset->_postParents);
                    $query_str['post_parent'] = $preset->_postParents[$i];
                    unset($preset->_postParents[$i]);
                    $preset->_postParents = array_values($preset->_postParents);
                    
                    //Cycle through rest of the array to check to see if there is
                    // another match before deciding to repeat this function.
                    //Index needs to be capped inside to serve as a break.
                    for ($i; $i < count($preset->_postParents); $i++)
                    {
                        if (get_post_type($preset->_postParents[$i]) == $post_type_key)
                        {
                            $query_str_arrays = array_merge($query_str_arrays, 
                                                            $this->set_query($preset));
                            $i = count($preset->_postParents);
                        }
                    }
                    //$i = count($preset->_postParents);
                }
            }

            $tax_operator = 'OR';
            foreach ($preset->_postTax->$post_type_key->taxonomies as $taxonomy_slug => $taxonomy_value)
            {
                
                $term_operator = 'IN';
                if ($taxonomy_value->require_terms === TRUE)
                {
                    $term_operator = 'AND';
                }
                if ($taxonomy_value->require_taxonomy === TRUE)
                {
                    $tax_operator = 'AND';
                }
                //Set query string's tax_query
                $query_str['tax_query'][] = array(
                    'taxonomy' => $taxonomy_slug,
                    'field' => 'id',
                    'terms' => $taxonomy_value->terms,
                    'include_children' => false,
                    'operator' => $term_operator
                );

                
            }
            $query_str['tax_query']['relation'] = $tax_operator;
            
            
            
            $query_str['post_type'] = $post_type_key;
            unset($preset->_postTax->$post_type_key);
            if (count((array) $preset->_postTax) > 0)
            {
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            
            
        }
        ////POST PARENTS reamining when there's no Post_Type/Taxonomy 
        //// help in presetObj.
        elseif (count($preset->_postParents) > 0)//catches the remaining
        {
            //Overwrites the default/init post_type (Any need to?)
            //$query_str['post_type'] = array();
            //If a Post Parents just happens to be set, then repeat this funtion
            if (!empty($query_str['post_parent']))
            {
                
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            //Set and continues adding the rest if any.
            elseif (count($preset->_postParents) > 1)
            {
                $query_str['post_parent'] = intval(array_shift($preset->_postParents));
                
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            else
            {
                $query_str['post_parent'] = intval(array_shift($preset->_postParents));
            }
            
        }
        else
        {
            return;
        }
        
        $query_str = array_merge($query_str, $this->set_query_base_val($preset));
        
        
        
        // If it is private and is the only visability, change/add private.
        // else if both visability status exists.
        //
        // Use as FINAL? 
        //
//        if (!empty($presetObj->_postVisibility))
//        {
//            
//        }
        if (count((array)$preset->_postVisibility) === 2)
        {
            //duplicate
            $query_str_arrays[] = clone $query_str;
            $query_str['post_status'][] = 'private';
            //$query_str_arrays[] = $query_str;
        }
        else if ($preset->_postVisibility[0] === 'private')
        {
            $query_str['post_status'][] = 'private';
        }
        //Otherwise leave alone.
        $query_str_arrays[] = $query_str;
        return $query_str_arrays;
        //return array_merge($query_str_arrays, $query_str);
        
        
        //PASSED - start with values that are passed across all strings
        //author
        //page_id
        //status
        //sort
        //FINAL
        //
        //
        
        //what requires additional queries?
        //Post Types
        //Parent
        //Private (do last to dup)
        
        
        //return $query_str_array;
    }
    private function set_query_base_val($presetObj)
    {
        //INIT
        $arg = array(
            //'author' => '',//this will need to be passed to other queries
            'post_status' => array(),
            'order' => 'DESC',//Final or Pass for trimmings?
            'orderby' => 'date',//Final or Pass for trimmings?
            'perm' => 'readable',//Passed
            //'post__in' => array(),
            'post__not_in' => array(),//DO NOT USE w/ WP_Query - there will be a manual function at the end
            'ignore_sticky_posts' => false,//Maybe Final, or may be passed
            'nopaging' => true,//Final or ALL   
        );
        
        ////AUTHOR FILTER////
        if ($presetObj->_postAuthorOperator != 'none' && !empty($presetObj->_postAuthorIDs))
        {
            $author_filter = '';
            $author_operator = '';
            if ($presetObj->_postAuthorOperator === 'exclude')
            {
                $author_operator = '-';
            }
            foreach ($presetObj->_postAuthorIDs as $i => $author_id)
            {
                $author_filter .= $author_operator . $author_id;
                //adds a comma if there is more IDs
                if ($i < (count($presetObj->_postAuthorIDs) - 1))
                {
                    $author_filter .= ',';
                }
            }
            $arg['author'] = $author_filter;
        }//END of Author Filter
        
        
        ////POST STATUS////
        //Don't need to worry about private value, it's in _postVisibility,
        // and will be used in set_query
        foreach ($presetObj->_postStatus as $key => $value)
        {
            $arg['post_status'][] = $value;
        }
        
        //Order/Sort
        if (!empty($presetObj->_listOrder))
        {
            $arg['order'] = $presetObj->_listOrder;
        }
        if (!empty($presetObj->_listOrderBy))
        {
            $arg['orderby'] = $presetObj->_listOrderBy;
        }
        
        //Permissions
        if (!empty($presetObj->_userPerm))
        {
            $arg['perm'] = $presetObj->_userPerm;
        }
        
        //posts in
        //not in use with presetObj yet, but will be used in $this->query
        
        //posts not in
        if (!empty($presetObj->_listExcludePosts))
        {
            foreach ($presetObj->_listExcludePosts as $i => $post_id)
            {
                if ($post_id !== 0)
                {
                    $arg['post__not_in'][] = $post_id;
                }
            }
        }
        $arg['post__not_in'] = array_unique($arg['post__not_in']);
        
        //Ignore Stickies
        if (!empty($presetObj->_listIgnoreSticky))
        {
            $arg['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
        }
        
        return $arg;
    }
    //instead of querying or using the global, just grab the post once and add it
    // to string(s).
    private function set_presetObj_current_page_vals($presetObj)
    {
        //Current post/page ID
        $current_ID = get_the_ID();
        
        //Determines whether the current post is capable of having children (page capabilities).
        //If there is no page, it will return false no matter what.
        $current_hierarchical = is_post_type_hierarchical(get_post_type($current_ID));
        //Replace Current Page Parent indicator with real current ID 
        
        foreach ($presetObj->_postParents as $key => $value)
        {
            if (intval($value) === 0)
            {
                if ($current_hierarchical)
                {
                    $presetObj->_postParents[$key] = $current_ID;
                }
                else
                {
                    unset($presetObj->_postParents[$key]);
                    $presetObj->_postParents = array_values($presetObj->_postParents);
                }
            }
        }
        //Removes and duplicates by using array_unique()
        $presetObj->_postParents = array_unique($presetObj->_postParents);
        
        
        
        ////POST TYPE & TAXONOMIES -> TERMS
        
        
        $current_taxonomies = get_post_taxonomies($current_ID);
        
        $args = array('orderby' => 'term_id', 
                      'order' => 'ASC', 
                      'fields' => 'ids');
        $current_taxonomy_terms = wp_get_object_terms($current_ID, $current_taxonomies, $args);
        
        
        foreach($presetObj->_postTax as $post_type => $pt_value)
        {
            foreach ($pt_value->taxonomies as $taxonomy => $tax_value)
            {
                if ($tax_value->include_terms === TRUE)
                {
                    
                }
            }
        }
//        Description
//        This function can be used within the loop. It will also return an array of the taxonomies with links to the taxonomy and name.
//
//        Usage
//
//        get_the_taxonomies();
//
//        Parameters
//        post
//        (int) (optional) The post ID to get taxonomies of.
//        Default: 0
//        args
//        (array) (optional) Overrides the defaults.
//        Default: None
//        Return Values
//        (Array) 
//        Array ( [taxnomy_slug] => Taxonomy Name: <a href='http://yourdomain.com/Term_Slug/'>Term Name</a>. )
//        //// ADD OTHER TAXONOMY TERMS IF INCLUDED IS CHECKED
//        $post_obj_post_type = $post_obj->post_type;
//
//        if (isset($presetObj->_postTax->$post_obj_post_type))
//        {
//
//            //$a = $presetObj->_postTax->$post_obj_post_type;
//            foreach ($post_obj->taxonomies as $taxonomy_name=>$taxonomy_object)
//            {
//                //$a = $presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->include_terms;
//                if ($presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->include_terms == true)
//                {
//                    $count = count($presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->terms);
//                    foreach ($taxonomy_object->terms as $term_ID)
//                    {
//                        $presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->terms[$count] = $term_ID;
//                        $count++;
//                    }
//                    //REMOVES ANY DUPLICATES THAT MAY HAVE BEEN ADDED
//                    $presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->terms = array_unique($presetObj->_postTax->$post_obj_post_type->taxonomies->$taxonomy_name->terms);
//                }
//            }
//        }
        
        return $presetObj;
    }
    private function query($query_str_array, $repeated = FALSE)
    {
        
        //if there is more than one query string
        // (Catch IDs) then repeat this.
        //Include any IDs
        //Remove and store posts__not_in for manual exclude
        //Use Wp_Query
        //Needs a custom post__not_in design. This will enable the use to include
        // and exclude posts/pages with exclude being done manually.
        //Return all post/page IDs if $repeated is TRUE
        //Return WP_Query Object
        //
        
    }
    private function post__not_in()
    {
        
        //for each exclude ID
        // if ID maches posts, then unset
        //
        
    }
    public function __construct($presetObj)
    {
        //DUPLICATE/CLONE FOR TESTING
        $presetObj2 = clone $presetObj;
        
        //var_dump($presetObj);
        //$this->set_query_init();
        //$this->set_query_base_val($query_str, $presetObj);
        ////TODO Complete this function, but also go into APLCore::APL_run
        //// and remove the simular design.
        $this->set_presetObj_current_page_vals($presetObj2);
        $query_str_array = $this->set_query($presetObj2);
        
        //MERGE SIMULAR QUERIES? - would merge matches and lessen the amount of queries.
        //$this->query($query_str_array);
        
        
        
        ////////////////////////////////////////////////////////////////////////
        //-^^- NEW -^^-/////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //-vv- REMOVE -vv-//////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        
        //Get the correct/useable post types 
        //-vv- Use this when nothing is selected instead of the 'any' option
        $post_type_names = get_post_types('',
                                          'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_names[$value]);
        }
        unset($value);
        unset($skip_post_types);
        //-^^-
        
        //// Pre-set Filters
        $author_filter = '';
        if ($presetObj->_postAuthorOperator != 'none' && !empty($presetObj->_postAuthorIDs))
        {
            $author_operator = '';
            if ($presetObj->_postAuthorOperator === 'exclude')
            {
                $author_operator = '-';
            }
            foreach ($presetObj->_postAuthorIDs as $i => $author_id)
            {
                $author_filter .= $author_operator . $author_id;
                if ($i < (count($presetObj->_postAuthorIDs) - 1))
                {
                    $author_filter .= ',';
                }
            }
        }
        
        
        $public_posts = array();
        $private_posts = array();
        //WHY IS THIS HERE?!? Why do they need to be split? Is it because of
        // issues combining the two?
        //Post Status Filter
        // There's post_status, but there is also 'post_status visibility'. All
        // the values can't be cross referenced except for 'private' (and public).
        // The problem is, you have to create an additional query if both public
        // and private is selected from the APL Admin UI, because there is no 
        // 'public' value. Only the 'private' value, and in turn acts as a switch.
        //
        foreach ($presetObj->_postVisibility as $visible)
        {
            if ($visible === 'private')
            {
                $private_presetObj = $presetObj;
                $private_presetObj->_postStatus[] = 'private';
                $private_arg_query = $this->APLQ_set_query($private_presetObj, $author_filter, $post_type_names);
                $private_posts = $this->APLQ_get_posts($private_arg_query['arg_query_reqSel'], 
                                                       $private_arg_query['arg_query_parents'], 
                                                       $post_type_names);
                        
            }
            else
            {
                $arg_query = $this->APLQ_set_query($presetObj, $author_filter, $post_type_names);
                $public_posts = $this->APLQ_get_posts($arg_query['arg_query_reqSel'], 
                                                      $arg_query['arg_query_parents'], 
                                                      $post_type_names);
            }
            
            
            
        }
        
        $post_types_used = array();
        $rtnPosts = array();
        if (!empty($private_posts) && !empty($public_posts))
        {
            $post_types_used = $private_posts['post_types_used'];
            foreach($public_posts['post_types_used'] as $post_type_value)
            {
                $post_types_used[] = $post_type_value;
            }
            $post_types_used  = array_unique($post_types_used);
            $rtnPosts = $this->APLQ_merge_private_public($private_posts['posts'], $public_posts['posts']);
        }
        else if (!empty($private_posts))
        {
            $post_types_used = $private_posts['post_types_used'];
            $rtnPosts = $private_posts['posts'];
        }
        else
        {
            $post_types_used = $public_posts['post_types_used'];
            $rtnPosts = $public_posts['posts'];
        }
        
        
         
        
        
        
        
        //// SORT
        //THIS IS SIMPLE BUT EFFECTIVE WAY TO SORT ALL THE POSTS
        // Also pretty unnessecary and garbage.
        $tmp_posts = array();
        $tmp_count = 0;
        
        $ex_arg_query = array();
        $ex_arg_query['post_type'] = $post_types_used;
        $ex_arg_query['post_status'] = 'any';
        $ex_arg_query['nopaging'] = true;
        $ex_arg_query['order'] = $presetObj->_listOrder;
        $ex_arg_query['orderby'] = $presetObj->_listOrderBy;
        
        $ex_arg_query['post__not_in'] = $presetObj->_listExcludePosts;
        //$ex_arg_query['suppress_filter'] = TRUE;
        $ex_arg_query['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
        $ex_arg_query['perm'] = 'readable';
        
        
        

        $APL_Query = new WP_Query($ex_arg_query);

        ////var_dump($APL_Query);
        
        foreach ($APL_Query->posts as $post)
        {
            foreach ($rtnPosts as $rtnPost)
            {
                if ($post->ID === $rtnPost->ID)
                {
                    $tmp_posts[$tmp_count] = $post;
                    $tmp_count++;
                    break;
                }
            }
        }
        $rtnPosts = $tmp_posts;
        
        //wp_reset_postdata();
        
        unset($APL_Query);

        
        if ($presetObj->_listCount == -1)
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    count($rtnPosts));
        }
        else
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    $presetObj->_listCount);
        }
        
        $this->_posts = $rtnPosts;
        //var_dump($this->_posts);
        //return $rtnPosts;
    }
    
    
    
    
    
    private function APLQ_set_query($presetObj, $author_filter, $post_type_names)
    {
        $tmp_postTax = (array) $presetObj->_postTax;
        if (empty($presetObj->_postParents) && empty($tmp_postTax))
        {
            //// DEFAULT IF POSTTAX AND PARENT IS EMPTY
            foreach ($post_type_names as $post_type_name)
            {
                $arg_query_parents = array();
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = array(
                    'post_type' => $post_type_name,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $presetObj->_listExcludePosts,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy,
                    
                    //'suppress_filters' => TRUE,
                    'author' => $author_filter,
                    'ignore_sticky_posts' => $presetObj->_listIgnoreSticky,
                    'perm' => $presetObj->_userPerm
                );
                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = array();
            }
        
        }
        else
        {




            //// POST PARENTS
            //TODO Add category and tag capabilities
            $arg_query_parents = array();
            foreach ($presetObj->_postParents as $parent_index => $parentID)
            {
                $arg_query_parents[$parent_index] = array(
                    'post_type' => get_post_type($parentID),
                    'post_parent' => $parentID,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $presetObj->_listExcludePosts,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy,
                    
                    //'suppress_filters' => TRUE,
                    'author' => $author_filter,
                    'ignore_sticky_posts' => $presetObj->_listIgnoreSticky,
                    'perm' => $presetObj->_userPerm
                
                );
            }

            //// REQUIRED AND SELECTED TAXONOMIES

            $arg_query_reqSel = array();
            foreach ($presetObj->_postTax as $post_type_name => $post_type_value)
            {


                $arg_selected = array();
                $arg_required = array();
                $count_req = 0;
                $count_sel = 0;
                foreach ($post_type_value->taxonomies as $taxonomy_name => $taxonomy_value)
                {
                    if (!empty($taxonomy_value->terms))
                    {
                        if ($taxonomy_value->require_taxonomy == true)
                        {
                            $arg_required['post_status'] = $presetObj->_postStatus;
                            $arg_required['order'] = $presetObj->_listOrder;
                            $arg_required['orderby'] = $presetObj->_listOrderBy;

                            $arg_required['post_type'] = $post_type_name;
                            $arg_required['tax_query']['relation'] = 'AND';
                            $arg_required['tax_query'][$count_req]['taxonomy'] = $taxonomy_name;


                            if ($taxonomy_value->terms[0] != 0)
                            {
                                $arg_required['tax_query'][$count_req]['field'] = 'id';
                                $arg_required['tax_query'][$count_req]['terms'] = $taxonomy_value->terms;
                                $arg_required['tax_query'][$count_req]['include_children'] = false;
                                $arg_required['tax_query'][$count_req]['operator'] = 'IN';
                            }




                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_required['tax_query'][$count_req]['operator'] = 'AND';
                            }

                            $arg_required['post__not_in'] = $presetObj->_listExcludePosts;
                            $arg_required['nopaging'] = true;


                            $arg_required['author'] = $author_filter;
                            //$arg_required['suppress_filters'] = TRUE;
                            $arg_required['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
                            $arg_required['perm'] = $presetObj->_userPerm;
                            $count_req++;
                        }
                        else
                        {
                            $arg_selected['post_status'] = $presetObj->_postStatus;
                            $arg_selected['order'] = $presetObj->_listOrder;
                            $arg_selected['orderby'] = $presetObj->_listOrderBy;

                            $arg_selected['post_type'] = $post_type_name;
                            $arg_selected['tax_query']['relation'] = 'OR';
                            $arg_selected['tax_query'][$count_sel]['taxonomy'] = $taxonomy_name;


                            if ($taxonomy_value->terms[0] != 0)
                            {
                                $arg_selected['tax_query'][$count_sel]['field'] = 'id';
                                $arg_selected['tax_query'][$count_sel]['terms'] = $taxonomy_value->terms;
                                $arg_selected['tax_query'][$count_sel]['include_children'] = false;
                                $arg_selected['tax_query'][$count_sel]['operator'] = 'IN';
                            }


                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_selected['tax_query'][$count_sel]['operator'] = 'AND';
                            }

                            $arg_selected['post__not_in'] = $presetObj->_listExcludePosts;
                            $arg_selected['nopaging'] = true;


                            $arg_selected['author'] = $author_filter;
                            //$arg_selected['suppress_filters'] = true;
                            $arg_selected['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
                            $arg_selected['perm'] = $presetObj->_userPerm;
                            $count_sel++;
                        }
                    }
                

                }
                unset($taxonomy_name);
                unset($taxonomy_value);
                unset($count_req);
                unset($count_sel);

                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = $arg_required;
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = $arg_selected;


                unset($arg_required);
                unset($arg_selected);
            }
            unset($post_type_name);
            unset($post_type_value);
        
        }
        $arg_query = array(
            'arg_query_parents' => $arg_query_parents,
            'arg_query_reqSel' => $arg_query_reqSel
        );
        
        return $arg_query;
    }
    private function APLQ_get_posts($arg_query_reqSel, $arg_query_parents, $post_type_names)
    {
        //// GET WP_QUERIES
        
        $posts_selected = array();
        $posts_required = array();
        foreach ($arg_query_reqSel as $post_type_name => $post_type_query)
        {
            //$a1 = $post_type_query['selected_taxonomy'];
            
            
            $APL_Query_selected = new WP_Query($post_type_query['selected_taxonomy']);
            if (isset($APL_Query_selected->posts))
            {
                $posts_selected[$post_type_name] = $APL_Query_selected->posts;
            }
            //wp_reset_postdata();
            unset($APL_Query_selected);
            
            $APL_Query_required = new WP_Query($post_type_query['required_taxonomy']);
            if (isset($APL_Query_required->posts))
            {
                $posts_required[$post_type_name] = $APL_Query_required->posts;
            }
            unset($APL_Query_required);
        }
        foreach ($arg_query_parents as $index => $arg_query_parent)
        {
            //$count = count($APL_Query_parents[$arg_query_parent['post_type']]);
            $APL_Query_parents = new WP_Query($arg_query_parent);
            
            //$query = new WP_Query( array( 'post_status' => array( 'publish' ) ) );
            $count = count($posts_parents[$arg_query_parent['post_type']]);
            foreach ($APL_Query_parents->posts as $post_parent)
            {
                
                $posts_parents[$arg_query_parent['post_type']][$count] = $post_parent;

                $count++;
                
            }

            unset($APL_Query_parents);
            
            
            //$posts_parents[$arg_query_parent['post_type']] = array_unique($posts_parents[$arg_query_parent['post_type']]);
        }
        //// MERGE POSTS
        $rtnPosts = array();
        $tmp_posts = array();
        foreach ($post_type_names as $post_type_name)
        {
            
            $tmp_count = 0;
            if (!empty ($posts_selected[$post_type_name]))
            {
                if (empty ($posts_required[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_selected[$post_type_name];
                }
                else 
                {
                    foreach ($posts_required[$post_type_name] as $post_req)
                    {
                        foreach ($posts_selected[$post_type_name] as $post_sel)
                        {
                            if ($post_req->ID == $post_sel->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $post_req;
                                $tmp_count++;
                            }
                        }
                    }
                }

            }
            else if (!empty ($posts_required[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $posts_required[$post_type_name];
            }
        }
        
        
        $rtnPosts = $tmp_posts;
        $tmp_posts = array();
        foreach($post_type_names as $post_type_name)
        {
            $tmp_count = 0;
            if (!empty($posts_parents[$post_type_name]))
            {
                if(empty($rtnPosts[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_parents[$post_type_name];
                }
                else
                {
                    foreach($rtnPosts[$post_type_name] as $post_rtn)
                    {
                        foreach($posts_parents[$post_type_name] as $post_par)
                        {
                            if ($post_par->ID == $post_rtn->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $post_par->ID;
                                $tmp_count++;
                            }
                        }
                    }
                }
            }
            
            else if (!empty($rtnPosts[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $rtnPosts[$post_type_name];
            }
            
        }
        
        $rtnPosts = $tmp_posts;
        
        //COMBINE POSTS FROM OTHER POST TYPES
        $tmp_posts = array();
        $tmp_count = 0;
        $post_types_used = array();
        foreach ($rtnPosts as $post_type_name => $post_type_posts)
        {
            $post_types_used[count($post_types_used)] = $post_type_name;
            foreach ($post_type_posts as $post)
            {
                $tmp_posts[$tmp_count] = $post;
                $tmp_count++;
            }
        }
        $rtnPosts['post_types_used'] = $post_types_used;
        $rtnPosts['posts'] = $tmp_posts;
        return $rtnPosts;
    }
    private function APLQ_merge_private_public($private_posts, $public_posts)
    {
        foreach ($private_posts as $private_post)
        {
            $public_posts[] = $private_post;
        }
        return $public_posts;
    }
}

?>
