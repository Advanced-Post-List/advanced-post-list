<?php
//TODO COMPLETELY REDO THIS CLASS AND SET IT UP TO HAVE A HANDLER AND CHILD CLASS
//PLEASE NOTE: MAKING A CHILD CLASS OF WP_QUERY MAY BE SOMETHING RESERVED LATER 
//  FOR PREMIUM USE. THE OPTION IS STILL UP FOR DEBATE, BUT IF FOLLOWED THROUGH
//  SOME FEATURES MAY REQUIRE CONTINUAL SUPPORT.
//
//LIST OF POSSIBLE FIXES AND FEATURES EXTENDING COULD OFFER
// * Better sticky support
// * Can add additional sorting methods


class WP_Query_child extends WP_Query
{
    
}
////////////////////////////////////////////////////////////////////////////////
//****************************************************************************//
////////////////////////////////////////////////////////////////////////////////
class APLQuery
{
    /**
     * @var array
     * @since 0.3.0
     * @todo Remove this...
     */
    public $_posts;
    
    /**
     * @var array
     * @since 0.3.b7
     */
    public $_query_str_array;
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
    
    //More INFO on the $arg go to https://gist.github.com/7352549.git
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
        
        $arg = array(
            'author' => '',
            'tax_query' => array(),
            //'post_parent' => 0,
            'post__in' => array(),
            'post__not_in' => array(),//DO NOT USE IN WP_Query - there will be a manual function at the end
            'post_type' => $post_type_list,
            'post_status' => array(
                    'publish',
                    ),
            'nopaging' => true,
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'perm' => 'readable',
        );
        
        return $arg;
        
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //DRAFT STEPS
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
    ////////////////////////////////////////////////////////////////////////////
    //TODO - WRITE FINAL STEPS
    //
    
    //CHANGE?  To set_query_str_arrays
    //            set_query_strs
    //            set_query_arrays
    //It's set to set_query since it's the main query
    private function set_query($presetObj)
    {
        //Clone since this is a repeating function and the variable keeps acting
        // like a pointer...to my surprise. I don't know if it is just objects that
        // are effected or I'm using 5.3 atm, but I thought you had to return the 
        // value/object if you wanted it changed. Not the ability to modify it a 
        // multitude of stacks in a repeating method.
        //Would making this method static prevent the presetObj acting like 
        // a pointer? Problem is fixed by just cloning objects.
        //$preset = new APLPresetObj();
        $preset = clone $presetObj;
        $preset->_postTax = clone $presetObj->_postTax;
        
        //Used for colecting and returning an array of $query_str
        $query_str_arrays = array();//array(array) - Multi-Dimensional
        //Used for this current instance of set_query
        $query_str = $this->set_query_init();
        //This is used to prevent repeating when the scope of the presetObj has
        // already been finished. This is caused by post_parents that have 
        // post_types/taxonomies and causes the possibility of using set_query 
        // twice in one instance. Could add another main elseif, but this probably
        // allows for less code. New function?
        $set_query_used = FALSE;
        
        ////POST_TYPES & TAXONOMIES + POST_PARENTS
        //DON'T USE A FOR LOOP for post_types
        $post_type_key = key((array) $preset->_postTax);
        if ($post_type_key !== null) //or use !empty()?
        {
            $query_str['post_type'] = array();
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
                            $set_query_used = TRUE;
                            $i = count($preset->_postParents);
                        }
                    }
                    //$i = count($preset->_postParents);
                }
            }

            $tax_operator = 'OR';
            foreach ($preset->_postTax->$post_type_key->taxonomies as $taxonomy_slug => $taxonomy_value)
            {
                if ($taxonomy_value->require_taxonomy === TRUE)
                {
                    $tax_operator = 'AND';
                }
                $term_operator = 'IN';
                if ($taxonomy_value->require_terms === TRUE)
                {
                    $term_operator = 'AND';
                }
                //For the Any/All setting
                if (in_array(0, $taxonomy_value->terms))
                {
                    //Does this need all terms added or leave empty
                    $taxonomy_value->terms = array();
                    
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
            
            $query_str['post_type'] = array($post_type_key);
            unset($preset->_postTax->$post_type_key);
            if (count((array) $preset->_postTax) > 0 && $set_query_used === FALSE)
            {
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            
            
        }
        ////POST PARENTS (w/o post_type/Tax) - remaining when there's no 
        //// Post_Type/Taxonomy in presetObj.
        elseif (count($preset->_postParents) > 0)//catches the remaining
        {
            //Overwrites the default/init post_type (Any need to?)
            //$query_str['post_type'] = array();
            
            //If a Post Parent arg is already set, then repeat this query. This
            // is just in case it happens to be set and to prevent overwriting.
            if (!empty($query_str['post_parent']))
            {
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            //Set and continues adding the rest of the page parents, if any.
            elseif (count($preset->_postParents) > 1)
            {
                $query_str['post_parent'] = intval(array_shift($preset->_postParents));
                $query_str['post_type'] = get_post_type($query_str['post_parent']);
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            else
            {
                $query_str['post_parent'] = intval(array_shift($preset->_postParents));
                $query_str['post_type'] = get_post_type($query_str['post_parent']);
            }
        }
//        else
//        {
//            
//            return;
//        }
        
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
            $query_str_arrays[] = $query_str;
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
        
    }
    private function set_query_base_val($presetObj)
    {
        //INIT
        $arg = array();
        
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
        if (!empty($presetObj->_postStatus))
        {
            $post_status_filter = array();
            foreach ($presetObj->_postStatus as $value)
            {
                $post_status_filter[] = $value;
            }
            $arg['post_status'] = $post_status_filter;
        }
            
        
        ////Order/Sort////
        if (!empty($presetObj->_listOrder))
        {
            $arg['order'] = $presetObj->_listOrder;
        }
        if (!empty($presetObj->_listOrderBy))
        {
            $arg['orderby'] = $presetObj->_listOrderBy;
        }
        
        ////Permissions////
        if (!empty($presetObj->_userPerm))
        {
            $arg['perm'] = $presetObj->_userPerm;
        }
        
        ////posts in////
        //not in use with presetObj yet, but will be used in $this->query
        
        ////posts not in////
        if (!empty($presetObj->_listExcludePosts))
        {
            foreach ($presetObj->_listExcludePosts as $i => $post_id)
            {
                if ($post_id !== 0)
                {
                    $arg['post__not_in'][] = $post_id;
                }
            }
            $arg['post__not_in'] = array_unique($arg['post__not_in']);
        }
        
        ////Ignore Stickies////
        if (!empty($presetObj->_listIgnoreSticky))
        {
            $arg['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
        }
        
        return $arg;
        
    }
    private function query_str_consolidate($query_str_array)
    {
        
        //What can be Merged
        // Post_Types that have Terms that match
        // Match Post Visibility/Status
        
        //What exempts from Merging
        // Post Parents
        // Public and Private Duplicates
        // Dif. Require Taxonomy
        // Dir. Require Terms
        // 
        // 
        for ($i = 0; $i < count($query_str_array); $i++)
        {
            if (empty($query_str_array[$i]['post_parent']))
            {
                for ($j = $i + 1; $j < count($query_str_array); $j++)
                {
                    //IF there isn't a post_parent that would void a merge and
                    // IF both query_str have or not have private post_status
                    //$c1 = $this->tax_query_match($query_str_array[$i]['tax_query'], $query_str_array[$j]['tax_query']);
                    if (empty($query_str_array[$j]['post_parent']) &&
                        in_array('private', $query_str_array[$i]['post_status']) === in_array('private', $query_str_array[$j]['post_status']) &&
                        $this->tax_query_match($query_str_array[$i]['tax_query'], $query_str_array[$j]['tax_query']))
                    {
                        $query_str_array[$i] = $this->query_str_merge($query_str_array[$i], $query_str_array[$j]);
                        unset($query_str_array[$j]);
                        $query_str_array = array_values($query_str_array);
                        $i--;
                    }
                }
            }
        }
        //$query_str_array = array_values($query_str_array);
        
//        foreach ($query_str_array as $key => $query_str1)
//        {
//            
//            if (empty($query_str1['post_parent']))
//            {
//
//            }
//            foreach ($query_str_array as $key2 => $query_str2)
//            {
//                
//                foreach ($query_str2['tax_query'] as $key3 => $value3);
//                
//            }
//            
//        }
        return $query_str_array;
    }
    
    private function query_str_merge($query_str1, $query_str2)
    {
        $query_str1['post_type'] = array_merge($query_str1['post_type'], $query_str2['post_type']);
        return $query_str1;
    }
    
    private function tax_query_match($tax_query1, $tax_query2)
    {
        // Init? Any variable needed?
        if ($tax_query1['relation'] === $tax_query2['relation'])
        {
            //Add/Skip one because of the 'relation' key
            for ($i = 0; $i < (count($tax_query1) - 1); $i++)
            {
                for ($j = 0; $j < (count($tax_query2) - 1); $j++)
                {
                    //Would have included the next IF statement if the 2 weren't
                    // required to have and not have an else return false.
                    if ($tax_query1[$i]['taxonomy'] === $tax_query2[$j]['taxonomy'])
                    {
                        $tax_match_found = TRUE;
                        if ($tax_query1[$i]['operator'] === $tax_query2[$j]['operator'])
                        {
                            foreach ($tax_query1[$i]['terms'] as $key => $value)
                            {
                                if (!in_array($value, $tax_query2[$j]['terms']))
                                {
                                    return FALSE;
                                }
                            }
                        }
                        else
                        {
                            return FALSE;
                        }
                    }
                }
                if (!$tax_match_found)
                {
                    return FALSE;
                }
            }
        }
        else
        {
            return FALSE;
        }
        return TRUE;
    }
    //Instead of querying or using the global post, just grab the post ID and let
    // WP do that type of work.
    private function set_presetObj_page_vals($presetObj)
    {
        //Current post/page ID
        $post_ID = get_the_ID();
        
        if ($presetObj->_listExcludeCurrent === TRUE)
        {
            $presetObj->_listExcludePosts[] = $post_ID;
        }
        ////////////////////////////////////////////////////////////////////////
        //// PAGE PARENTS //////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //Is the current post's post_type hierarchical? (page capabilities)
        //If there is no page, it will return false no matter what. Would have 
        // added an IF statement to the FOREACH loop to prevent unnecessary looping,
        // but the dynamic indicator, zero (0), still needs to be removed if the 
        // global post is not from a hierarchical post type.
        $post_post_type = get_post_type($post_ID);
        $post_hierarchical = is_post_type_hierarchical($post_post_type);
        foreach ($presetObj->_postParents as $key => $value)
        {
            //If include current post is enabled. Looks for the dynamic value, zero (0),
            // and replaces it with the global post ID if it is hierarchical (pages).
            // Otherwise it is removed to prevent any bugs.
            if (intval($value) === 0)
            {
                //If the post is a valid page parent, then replace 0 with page ID
                if ($post_hierarchical && !empty($post_ID))
                {
                    //Replace Current Page Parent indicator with the (real) page ID 
                    $presetObj->_postParents[$key] = $post_ID;
                }
                //Otherwise remove the invalid entry (value 0)
                else
                {
                    unset($presetObj->_postParents[$key]);
                    $presetObj->_postParents = array_values($presetObj->_postParents);
                }
            }
        }
        //Removes any duplicates by using array_unique()
        $presetObj->_postParents = array_values(array_unique($presetObj->_postParents));
        
        ////////////////////////////////////////////////////////////////////////
        //// POST TYPE & TAXONOMIES -> TERMS ///////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        $post_taxonomies = get_post_taxonomies($post_ID);
        $args_post_terms = array('orderby'  => 'term_id', 
                                 'order'    => 'ASC', 
                                 'fields'   => 'ids');
        foreach($presetObj->_postTax as $preset_post_type => $preset_pt_value)
        {
            //ADD? - Match post_types with (current) page/post for more strict
            // filtering. Right now terms will be added to taxonomies from any 
            // post_type that is selected to include terms...so it won't spread
            // to all post_types unless the user selects it.
            //if ($current_ID === $post_type)
            foreach ($preset_pt_value->taxonomies as $preset_taxonomy => $preset_tax_value)
            {
                if ($preset_tax_value->include_terms === TRUE)
                {
                    foreach ($post_taxonomies as $post_taxonomy_value)
                    {
                        if ($preset_taxonomy === $post_taxonomy_value)
                        {
                            //ALTERNATE FOR NEXT 3(5) LINES - Shorter but complex
                            //$preset_tax_value->terms = array_unique(
                            //    array_merge(
                            //        $preset_tax_value->terms, 
                            //        wp_get_object_terms(
                            //            $post_ID, 
                            //            $post_taxonomy_value, 
                            //            $args_post_terms
                            //        )
                            //    )
                            //);
                            $post_taxonomy_terms = wp_get_object_terms($post_ID, 
                                                                       $post_taxonomy_value, 
                                                                       $args_post_terms);
                            $preset_tax_value->terms = array_merge($preset_tax_value->terms, 
                                                                   (array) $post_taxonomy_terms);
                            $preset_tax_value->terms = array_unique($preset_tax_value->terms);
                        }
                    }
                }
            }
        }
        
        return $presetObj;
        
    }
    
    public function query_wp($query_str_array, $repeated = FALSE)
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
        
        $post_in_IDs = array();
        $post_not_in_IDs = array();
        $final_query_str = array();
        
        if ($repeated === TRUE)
        {
            
            $query_str = array_shift($query_str_array);
            
            //If more query strings exist, then repeat this function. When returned
            // merge post ids for final query.
            if (!empty($query_str_array))
            {
                $post_in_IDs = array_merge($this->query_wp($query_str_array, TRUE), $post_in_IDs);
                //$query_str['post__in'] = array_merge($query_str['post__in'], $post_in_IDs);
            }
            
            
            
            //Since post__in and post__not_in don't mix at all while querying. The
            // 2 variables are stored seperately.
            if (!empty($query_str['post__not_in']))
            {
                $post_not_in_IDs = $query_str['post__not_in'];

            }
            unset($query_str['post__not_in']);
            
            if (!empty($query_str['post__in']))
            {
                $post_in_IDs = array_merge($post_in_IDs, $query_str['post__in']);

            }
            unset($query_str['post__in']);
            
            
            
            $query_str['fields'] = 'ids';
            $Query_Obj = new WP_Query($query_str);
            
            $post_IDs = array();
            foreach ($Query_Obj->posts as $i => $post_ID)
            {
                $post_IDs[] = intval($post_ID);
            }
            
            $post_IDs = array_merge($post_IDs, $post_in_IDs);
            wp_reset_postdata();
            return $post_IDs;
            
        }
        else //$repeated === FALSE
        {
            $post_in_IDs = array_merge($this->query_wp($query_str_array, TRUE));
            
            $query_str = array_shift($query_str_array);
            
            $final_query_str['post__in'] = $post_in_IDs;
            $final_query_str['post_type'] = 'any';
            $final_query_str['nopaging'] = TRUE;
            $final_query_str['order'] = $query_str['order'];
            $final_query_str['orderby'] = $query_str['orderby'];
            $final_query_str['ignore_sticky_posts'] = $query_str['ignore_sticky_posts'];
            
            $final_Query_Obj = new WP_Query($final_query_str);
            
            //TODO finish the function to exclude posts
            if (!empty($query_str['post__not_in']))
            {
                $post_not_in_IDs = $query_str['post__not_in'];

            }
            $final_Query_Obj = $this->post__not_in($final_Query_Obj, $post_not_in_IDs);
            
            return $final_Query_Obj;
            
        }
        
    }
    private function post__not_in($Query_Obj, $post_not_in_IDs)
    {
        //$posts = $Query_Obj->posts;
        foreach ($Query_Obj->posts as $i => $post)
        {
            foreach ($post_not_in_IDs as $post_not_ID)
            {
                if ($post->ID === $post_not_ID)
                {
                    unset($Query_Obj->posts[$i]);
                    $Query_Obj->post_count -= 1;
                    $Query_Obj->found_posts -= 1;
                    if ($Query_Obj->post->ID === $post_not_ID)
                    {
                        $Query_Obj->post = $Query_Obj->posts[$i + 1];
                    }
                }
            }
        }
        $Query_Obj->posts = array_values($Query_Obj->posts);
        //rewind_posts(); //no work
        
        return $Query_Obj;
    }
    public function __construct($presetObj)
    {
        //DUPLICATE/CLONE FOR TESTING
        $presetObj2 = clone $presetObj;
        
        // TODO REMOVE SIMULAR CODE IN APLCore::APL_run THEN ENABLE NEXT LINE
        $presetObj2 = $this->set_presetObj_page_vals($presetObj2);
        //TODO Account for Any/All for taxonomies. May have to get_terms. TEST FIRST
        $_query_str_array = $this->set_query($presetObj2);
        
        //MERGE SIMULAR QUERIES? - would merge matches and lessen the amount of queries.
        $this ->_query_str_array = $this->query_str_consolidate($_query_str_array);
        //QUERY ARG ARRAY
        //return $this->query_wp($query_str_array);
        
        
        
        ////////////////////////////////////////////////////////////////////////
        //-^^- NEW -^^-/////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //--------------------------------------------------------------------//
        ////////////////////////////////////////////////////////////////////////
        //-vv- REMOVE -vv-//////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        /*
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
                if ($post->ID === $rtnPost)
                {
                    $tmp_posts[$tmp_count] = $post;
                    $tmp_count++;
                    break;
                }
                else if ($post->ID === $rtnPost->ID)
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
        
        //$this->_posts = $rtnPosts;
        //var_dump($this->_posts);
        //return $rtnPosts;
        
         */
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
