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
// SAMPLE OF PHPDOC DESCRIPTION
/**
 * <p><b>Desc:</b> Class object to handle of the query functions. Represents the
 *                 shadowed version of WP_Query Class. To add additional funtions 
 *                 that WP_Query doesn't have.</p>
 * @access public
 * @since 0.3
 * @version 0.3.b8 Fixed using too many nested code. Complete overhaul.
 * 
 */
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

    // SAMPLE OF PHPDOC DESCRIPTION
    /**
     * <p><b>Desc:</b> Adds post/page dynamics, sets the multi-dimensional 
     * query string array, and slims down the amount of query strings to be used.</p>
     * @access public
     * @param object $presetObj APL Preset Post List Object
     * 
     * @since 0.3.0
     * @version 0.3.b8 Fixed nested code, and no longer queries post in construct.
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Add page dynamics to the presetObj.</li>
     * <li value="2">Set the query strings from the presetObj</li>
     * <li value="3">Merge any simular queries to lessen the amount of queries.</li>
     * </ol>
     */
    public function __construct($presetObj)
    {
        //STEP 1 - Add page dynamics to the presetObj.
        $presetObj = $this->set_presetObj_page_vals($presetObj);
        //STEP 2 - Set the query strings from the presetObj
        $_query_str_array = $this->set_query($presetObj);
        //STEP 3 - Merge any simular queries to lessen the amount of queries.
        $this ->_query_str_array = $this->query_str_consolidate($_query_str_array);
    }
    /**
     * <p><b>Desc:</b> Sets the initial values for query_str.</p>
     * @access private
     * @return array Initial query_str array.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Instead of using 'any', use all relevant post types except for 
     * attachment, revision, and nav_menu_item.</li>
     * <li value="2">Set all other variables of query_str to a default value to use.</li>
     * <li value="3">Return query string argument array.</li>
     * </ol>
     */
    private function set_query_init()
    {
        //STEP 1 - Instead of using 'any', use all relevant post types except for
        //          attachment, revision, and nav_menu_item.
        $post_type_list = get_post_types('', 'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_list[$value]);
        }
        unset($value);
        unset($skip_post_types);
        
        //STEP 2 - Set all other variables of query_str to a default value to use.
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
            'nopaging' => FALSE,
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'perm' => 'readable',
        );
        
        //STEP 3 - Return query string argument array.
        return $arg;
        
    }
    
    //REF: (wp_query_ref) https://gist.github.com/EkoJr/7352549
    /**
     * <p><b>Desc:</b> Used as a repeating function to set multiple query strings.
     * REF: (wp_query_ref) https://gist.github.com/EkoJr/7352549</p>
     * @access private
     * @param object $presetObj APL preset post list objects.
     * @return array Multi-dimensional query_str array
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Clone the param object (REQUIRED)</li>
     * <li value="2">Set defaults for query_str.</li>
     * <li value="3">Set boolean to determine if the function is repeating.</li>
     * <li value="4">If a post types exists, then do <b>steps 5-7</b>.</li>
     * <li value="5">Go through post parents, if any, and if more than one 
     * exists in the post type, then repeat this function with 1 less.</li>
     * <li value="6">Set query_str's tax_query array variables.</li>
     * <li value="7">If more post types exist, then repeat this function.</li>
     * <li value="8">If no post types exist, then cycle through any post parents, 
     * and if more than one exists, repeat this function.</li>
     * <li value="9">Add the rest of the base query_str values.</li>
     * <li value="10">If more both public and private visibility setting is enabled, 
     * then duplicate the variable and change one to private. Otherwise only 
     * change it if private is enabled.</li>
     * <li value="11">Return all collected query strings.</li>
     * </ol>
     */
    private function set_query($presetObj)
    {
        //STEP 1 - Clone the param object (REQUIRED).
        //Found out that relying on the param to be seperate from the call
        // stack produces the param to be the same object; acing like a pointer?
        $preset = clone $presetObj;
        $preset->_postTax = clone $presetObj->_postTax;
        
        //Used for collecting and returning an array of $query_str; Multi-Dimensional.
        //array(array)
        $query_str_arrays = array();
        //STEP 2 - Set defaults for query_str.
        $query_str = $this->set_query_init();
        //STEP 3 - Set boolean to determine if the function is repeating.
        //This is used to prevent repeating when the scope of the presetObj has
        // already been finished. This is caused by post_parents that have 
        // post_types/taxonomies and causes the possibility of using set_query 
        // twice in one instance.
        $set_query_used = FALSE;
        
        //STEP 4 - If a post types exists, then do steps 5-7.
        ////POST_TYPES & TAXONOMIES + POST_PARENTS
        //DON'T USE A FOR LOOP for post_types
        $post_type_key = key((array) $preset->_postTax);
        if ($post_type_key !== null) //or use !empty()?
        {
            $query_str['post_type'] = array();
            //STEP 5 - Go through post parents, if any, and if more than one
            //          exists in the post type, then repeat this function 
            //          with 1 less.
            //Use this type of FOR loop in order to use the index as a counter.
            for ($i = 0; $i < count($preset->_postParents); $i++)
            {
                if (get_post_type($preset->_postParents[$i]) == $post_type_key)
                {
                    $query_str['post_parent'] = array_shift($preset->_postParents);
                    
                    //Cycle through rest of the array to check to see if there is
                    // another match before deciding to repeat this function.
                    //Index ($i) needs to cap inside to serve as a break.
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
                }
            }
            //STEP 6 - Set query_str's tax_query array variables.
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
                    'taxonomy'          => $taxonomy_slug,
                    'field'             => 'id',
                    'terms'             => $taxonomy_value->terms,
                    'include_children'  => false,
                    'operator'          => $term_operator
                );

                
            }
            $query_str['tax_query']['relation'] = $tax_operator;
            
            $query_str['post_type'] = array($post_type_key);
            unset($preset->_postTax->$post_type_key);
            
            //STEP 7 - If more post types exist, then repeat this function.
            if (count((array) $preset->_postTax) > 0 && $set_query_used === FALSE)
            {
                $query_str_arrays = array_merge($query_str_arrays, $this->set_query($preset));
            }
            
            
        }
        //STEP 8 - If no post types exist, then cycle through any post parents,
        //          and if more than one exists, repeat this function.
        ////POST PARENTS (w/o post_type/Tax)
        elseif (count($preset->_postParents) > 0)//catches the remaining
        {
            
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
        
        //STEP 9 - Add the rest of the base query_str values.
        $query_str = array_merge($query_str, $this->set_query_base_val($preset));
        
        //Step 10 - If more both public and private visibility setting is enabled,
        //           then duplicate the variable and change one to private. Otherwise
        //           only change it if private is enabled.
        if (count((array)$preset->_postVisibility) === 2)
        {
            $query_str_arrays[] = $query_str;
            $query_str['post_status'][] = 'private';
        }
        else if ($preset->_postVisibility[0] === 'private')
        {
            $query_str['post_status'][] = 'private';
        }
        //Otherwise leave alone.
        
        //STEP 11 - Return all collected query strings.
        $query_str_arrays[] = $query_str;
        
        return $query_str_arrays;
        
    }
    /**
     * <p><b>Desc:</b> Sets the base query_str values.</p>
     * @access private
     * @param object $presetObj APL preset post list objects.
     * @return array query_str base values.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Add author filter settings.</li>
     * <li value="2">Add post status filter settings.</li>
     * <li value="3">Add order by settings.</li>
     * <li value="4">Add user's read perm filter.</li>
     * <li value="5">Add or Remove post ids</li>
     * <li value="6">Add whether to ignore sticky settings.</li>
     * <li value="7">Return query_str's base variable values.</li>
     * </ol>
     */
    private function set_query_base_val($presetObj)
    {
        //INIT
        $arg = array();
        
        ////AUTHOR FILTER////
        //STEP 1 - Add author filter settings.
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
        //STEP 2 - Add post status filter settings.
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
        //STEP 3 - Add order by settings.
        if (!empty($presetObj->_listOrder))
        {
            $arg['order'] = $presetObj->_listOrder;
        }
        if (!empty($presetObj->_listOrderBy))
        {
            $arg['orderby'] = $presetObj->_listOrderBy;
        }
        
        //STEP 4 - Add user's read perm filter.
        ////Permissions////
        if (!empty($presetObj->_userPerm))
        {
            $arg['perm'] = $presetObj->_userPerm;
        }
        
        //STEP 5 - Add or Remove post ids
        ////posts in////
        //not in use with presetObj yet, but will be used in $this->query
        
        ////posts not in////
        if (!empty($presetObj->_listExcludePosts))
        {
            foreach ($presetObj->_listExcludePosts as $i => $post_id)
            {
                if ($post_id !== 0 && !empty($post_id))
                {
                    $arg['post__not_in'][] = $post_id;
                }
            }
            $arg['post__not_in'] = array_unique($arg['post__not_in']);
        }
        
        //STEP 6 - Add whether to ignore sticky settings.
        ////Ignore Stickies////
        if (!empty($presetObj->_listIgnoreSticky))
        {
            $arg['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
        }
        
        if (!empty($presetObj->_listCount))
        {
            $arg['posts_per_page'] = $presetObj->_listCount + count($arg['post__not_in']);
        }
        
        //STEP 7 - Return query_str's base variable values.
        return $arg;
        
    }
    /**
     * <p><b>Desc:</b> Merges any simular query strings.</p>
     * @access private
     * @param array $query_str_array Multi-dimensional query_str array.
     * @return $query_str_array Multi-dimensional query_str array.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Go through string and match post parents with the same 
     * post status or tax query.</li>
     * <li value="2">Return (modified) query_str_array.</li>
     * </ol>
     */
    private function query_str_consolidate($query_str_array)
    {
        //STEP 1 - Go through string and match post parents with the same 
        //          post status or tax query.
        for ($i = 0; $i < count($query_str_array); $i++)
        {
            if (empty($query_str_array[$i]['post_parent']))
            {
                for ($j = $i + 1; $j < count($query_str_array); $j++)
                {
                    //IF there isn't a post_parent that would void a merge and
                    // IF both query_str does have or not have private post_status
                    
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
        
        //STEP 2 - Return (modified) query_str_array.
        return $query_str_array;
    }
    // SAMPLE OF PHPDOC DESCRIPTION
    /**
     * <p><b>Desc:</b> Merges two query_str arrays.</p>
     * @access private protected public
     * @param array $query_str1 Query string values.
     * @param array $query_str2 Query string values.
     * @return array Query string values.
     * 
     * @since 0.3.b8
     */
    private function query_str_merge($query_str1, $query_str2)
    {
        $query_str1['post_type'] = array_merge($query_str1['post_type'], $query_str2['post_type']);
        return $query_str1;
    }
    /**
     * <p><b>Desc:</b> Checks to see if there is a 100% relation.</p>
     * @access private
     * @param array $tax_query1 The query string's tax query.
     * @param array $tax_query2 The query string's tax query.
     * @return boolean 100% relation.
     * 
     * @since version/info string [unspecified format]
     * @version versionstring [unspecified format]
     * 
     * @uses file.ext|elementname|class::methodname()|class::$variablename|
     *        functionname()|function functionname description of how the 
     *        element is used
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Check and return false if taxomonies do not have 100% relation</li>
     * <li value="2">Return true if there is a 100% relation.</li>
     * </ol>
     */
    private function tax_query_match($tax_query1, $tax_query2)
    {
        //STEP 1 - Check and return false if taxomonies do not have 100% relation
        if ($tax_query1['relation'] === $tax_query2['relation'])
        {
            
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
        //STEP 2 - Return true if there is a 100% relation.
        return TRUE;
    }
    //Instead of querying or using the global post, just grab the post ID and let
    // WP do that type of work.
    // SAMPLE OF PHPDOC DESCRIPTION
    /**
     * <p><b>Desc:</b> Adds the current global post's values if dynamic settings 
     * are checked.</p>
     * @access private
     * @param object $presetObj APL preset post list object.
     * @return object APL's (modified) preset post list object.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Get the current $post ID.</li>
     * <li value="2">If excluding current post/page is checked, then add post_ID</li>
     * <li value="3">If the post parent dynamic/'current page' option is checked. 
     * Then see if the post is hierarchical and add it to post parents array.</li>
     * <li value="4">If any include/dynamic taxonomy terms are selected, then add 
     * the (global) post's values.</li>
     * <li value="5">Return (modified) presetObj</li>
     * </ol>
     */
    private function set_presetObj_page_vals($presetObj)
    {
        //STEP 1 - Get the current $post ID.
        //Current post/page ID
        $post_ID = get_the_ID();
        
        
        //STEP 2 - If excluding current post/page is checked, then add post_ID
        if ($presetObj->_listExcludeCurrent === TRUE && !empty($post_ID))
        {
            $presetObj->_listExcludePosts[] = $post_ID;
        }
        ////////////////////////////////////////////////////////////////////////
        //// PAGE PARENTS //////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //STEP 3 - If the post parent dynamic/'current page' option is checked. Then
        //          see if the post is hierarchical and add it to post parents array.
        $post_post_type = get_post_type($post_ID);
        $post_hierarchical = is_post_type_hierarchical($post_post_type);
        foreach ($presetObj->_postParents as $key => $value)
        {
            //If dynamic/current post is enabled, zero (0)
            if (intval($value) === 0)
            {
                //If the post is a valid page parent (hierarchical), then 
                // replace 0 with page ID
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
        //STEP 4 - If any include/dynamic taxonomy terms are selected, then add
        // the (global) post's values.
        $post_taxonomies = get_post_taxonomies($post_ID);
        $args_post_terms = array('orderby'  => 'term_id', 
                                 'order'    => 'ASC', 
                                 'fields'   => 'ids');
        foreach($presetObj->_postTax as $preset_post_type => $preset_pt_value)
        {
            if ($post_post_type === $preset_post_type)
            {
                foreach ($preset_pt_value->taxonomies as $preset_taxonomy => $preset_tax_value)
                {
                    if ($preset_tax_value->include_terms === TRUE)
                    {
                        foreach ($post_taxonomies as $post_taxonomy_value)
                        {
                            if ($preset_taxonomy === $post_taxonomy_value && !empty($post_ID))
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
        }
        
        //STEP 5 - Return (modified) presetObj
        return $presetObj;
        
    }
    //Name: Query Posts
    //(Note: Function needs to repeat itself for each query string before a query 
    //       is made, and each function instance needs to use only one query)
    //1) If more query string exist, do this function
    //2) Get post according to query string (taxonomies/terms and other params)
    //3) Check requirements/filters that WP didn't get
    //
    //4) Return Final WP_Query Object or Page/Post IDs
    //
    
    //More INFO on the $arg go to https://gist.github.com/EkoJr/7352549
    // SAMPLE OF PHPDOC DESCRIPTION
    /**
     * <p><b>Desc:</b> Queries multiple instances of this function if there is more 
     * than one query_str.</p>
     * @access public
     * @param array $query_str_array Multi-dimensional query_str array.
     * @param boolean $repeated This function repeated.
     * @return mixed WP_Query class if unrepeated, otherwise array of post_IDs.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">If this is NOT the first and last instance of this function. 
     * Then repeat this function if more queries are present, and query/collect 
     * the posts IDs.</li>
     * <li value="2">FINAL Query and order the post IDs collected. Return results</li>
     * </ol>
     */
    public function query_wp($query_str_array, $repeated = FALSE)
    {
        $post_in_IDs = array();
        $post_not_in_IDs = array();
        $final_query_str = array();
        
        //STEP 1 - If this is NOT the first and last instance of this function. 
        //        Then repeat this function if more queries are present, and 
        //        query/collect the posts IDs.
        if ($repeated === TRUE)
        {
            $query_str = array_shift($query_str_array);
            
            //If more query strings exist, then repeat this function. When returned
            // merge post ids for final query.
            if (!empty($query_str_array))
            {
                $post_in_IDs = array_merge($this->query_wp($query_str_array, TRUE), $post_in_IDs);
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
            
            //Sets the query string to just query IDs
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
        //STEP 2 - FINAL Query and order the post IDs collected. Return results
        else //$repeated === FALSE
        {
            $post_in_IDs = array_merge($this->query_wp($query_str_array, TRUE));
            $query_str = array_shift($query_str_array);
            
            //$tmp_post_in_IDs = array();
            foreach ($query_str['post__not_in'] as $post_not_value)
            {
                foreach ($post_in_IDs as $key => $post_in_value)
                {
                    if ($post_in_value === $post_not_value)
                    {
                        unset($post_in_IDs[$key]);
                    }
                }
            }
            $post_in_IDs = array_merge($post_in_IDs);
            
            if (empty($post_in_IDs))
            {
                $post_in_IDs[] = 0;
            }
            
            //Set FINAL query_str with post IDs
            
            
            $final_query_str['post__in'] = $post_in_IDs;
            $final_query_str['post_type'] = 'any';
            $final_query_str['nopaging'] = FALSE;
            $final_query_str['order'] = $query_str['order'];
            $final_query_str['orderby'] = $query_str['orderby'];
            $final_query_str['ignore_sticky_posts'] = $query_str['ignore_sticky_posts'];
            
            $final_query_str['posts_per_page'] = $query_str['posts_per_page'] - count($query_str['post__not_in']);
            
            //Get FINAL Query Object
            $final_Query_Obj = new WP_Query($final_query_str);
            
//            if (!empty($query_str['post__not_in']))
//            {
//                $post_not_in_IDs = $query_str['post__not_in'];
//            }
//            $final_Query_Obj = $this->post__not_in($final_Query_Obj, $post_not_in_IDs);
            
            return $final_Query_Obj;
        }
    }
    // SAMPLE OF PHPDOC DESCRIPTION
    /**
     * <p><b>Desc:</b></p>
     * @access private
     * @param object $Query_Obj WP_Query class.
     * @param array $post_not_in_IDs Posts to exclude/remove.
     * @return object WP_Query class (modified) object.
     * 
     * @since 0.3.b8
     * 
     * @tutorial 
     * <ol>
     * <li value="1">Go though posts.</li>
     * <li value="2">If a post matches one of the excluded IDs, then remove the 
     * post from both post, posts, and post counts in WP_Query class.</li>
     * <li value="3">Return WP_Query class.</li>
     * </ol>
     */
    private function post__not_in($Query_Obj, $post_not_in_IDs)
    {
        //$posts = $Query_Obj->posts;
        //STEP 1 - Go though posts.
        foreach ($Query_Obj->posts as $i => $post)
        {
            foreach ($post_not_in_IDs as $post_not_ID)
            {
                //STEP 2 - If a post matches one of the excluded IDs, then remove
                //          the post from both post, posts, and post counts in 
                //          WP_Query class.
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
        
        //STEP 3 - Return WP_Query class.
        return $Query_Obj;
    }
}

?>
