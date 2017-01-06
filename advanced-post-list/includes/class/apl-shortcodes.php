<?php
/**
 * APL (Internal) Shortcode API: APL_InternalShortcodes class
 * 
 * @package WP Advanced Post List
 * @subpackage Internal Shortcodes
 * @version 0.4.0
 */

/**
 * Advanced Post List - Internal Shortcodes
 * 
 * Handles all internal do_shortcodes when preset post lists are called. Each time
 * the class object is created, shortcodes are added, replace function used to
 * do_shortcodes, and ends by removing shortcodes (currently manual $this->remove).
 *
 * @since 0.4.0
 */
class APL_Shortcodes
{
    /**
     * Destruct Shortcode Remove
     * 
     * Desc: Description HERE.
     * 
     * 1. Step 1 
     * 2. Step 2, do _Step 3_ 
     * 3. Step 3 
     * 
     * Conclusion.
     * 
     * @since 0.4.0
     * @version 0.4.0
     * @access public
     * 
     * @global WP_Post $post Used to store in $this->_post.
     * 
     * @param string $preset_content The design content for List Content 
     * @param string $post_content WP Post object.
     * @param array $terms Terms to check.
     * @return array Terms that are not stopwords.
     */
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
 * Advanced Post List - Internal Shortcodes
 * 
 * Handles all internal do_shortcodes when preset post lists are called. Each time
 * the class object is created, shortcodes are added, replace function used to
 * do_shortcodes, and ends by removing shortcodes (currently manual $this->remove).
 *
 * @since 0.4.0
 */
class APL_InternalShortcodes
{
    
    /**
     * WP_Post object for holding post details accessed by shortcode functions. 
     * 
     * @since 0.4.0
     * @access private
     * @var object $_post WP_Post Object for holding data.
     */
    private $_post;
    
    //TODO ADD post object to construct OR keep in replace function?
    /**
     * APL (Internal) Shortcode Constructor
     * 
     * Desc: Loads/Adds all the internal shortcodes to WordPress, and sets the 
     * class _post to default with Global Post.
     * 
     * 1. Load a list of (internal) shortcode tags. 
     * 2. Set _post to default settings with Global $post. 
     * 
     * @since 0.4.0
     * @access public
     * 
     * @global WP_Post $post Used to store in $this->_post.
     */
    public function __construct()
    {
        //STEP 1
        $shortcode_list = $this->shortcode_list();
        foreach ($shortcode_list as $tag)
        {
            add_shortcode($tag, array($this, $tag));
        }
        //STEP 2
        global $post;
        $this->_post = $post;
        $this->_item_count = 0;
    }
    
    /**
     * Destruct Shortcode Remove. 
     * 
     * Desc: Removes the added shortcodes from construction, and unsets class. 
     * 
     * 1. Remove list of shortcodes from WordPress. 
     * 2. Unset _post object. 
     * 
     * Note: Magic Method __destruct wasn't working as intented, and would called
     * shortly after creating a new APL_Shortcode. Which occurs when more than 
     * one [post_list] is used.
     * 
     * @since 0.4.0
     * @access public
     */
    public function remove() 
    {
        //STEP 1
        $shortcode_list = $this->shortcode_list();
        foreach ($shortcode_list as $tag)
        {
            remove_shortcode($tag);
        }
        //STEP 2
        unset($this->_post);
    }
    
    //TODO - Extension support for additional functionality
    /**
     * List of shortcode tags. 
     * 
     * Desc: Returns an array of shortcode tags used when public shortcodes 
     * are used. 
     * 
     * @since 0.4.0
     * @access private
     * 
     * @return array Internal Shortcode Tags used to add, replace, remove, etc..
     */
    private function shortcode_list()
    {
        $return_array = array(
            'ID',
            'post_name',
            'post_slug',
            'post_title',
            
            'post_author',
            
            'post_permalink',
            'guid',
            
            'post_date',
            'post_date_gmt',
            //'post_modified',
            //'post_modified_gmt',
            
            //'post_thumb',
            
            //'post_content',
            //'post_excerpt',
            
            //'comment_count',
            //'post_comments',
            
            //'post_parent',
            
            //'post_tags',
            //'post_categories',
            'post_terms'
            
            //'post_meta',
            
            //'item_number',
            //'final_end',
            //'php_function',
            
            //Extensions
            //'post_pdf' //Kalin's PDF Plugin (obsolete?)
        );
        //TODO - Extension support for additional functionality
        
        return $return_array;
    }
    public function replace($preset_content, $post_content)
    
    /**
     * Shortcode Replace
     * 
     * Desc: Replaces APL's internal shortcodes with RegEx and WP's Shortcode API
     * with $this->[shortcode]. 
     * 
     * 1. Cycle through the list of internal shortcodes. 
     * 2. While there is a match, grab the first match/shortcode. 
     * 3. Do shortcode, and replace content from beginning to end. 
     * 4. Return (Preset Content) string. 
     * 
     * @since 0.4.0
     * @version 0.4.0
     * @access public
     * 
     * @param string $preset_content The design content for List Content 
     * @param string $post_content WP Post object.
     * @return string Preset Content with shortcodes replaced. 
     */
    {
        //INIT
        $return_str = $preset_content;
        $this->_post = $post_content;
        
        //STEP 1
        $shortcode_tags = $this->shortcode_list();
        foreach ($shortcode_tags as $tag) 
        {
            //STEP 2
            while (preg_match('#\[' . $tag . '(.*?)?\]#', $return_str, $matches_default))
            {
                //STEP 3
                $return_str = preg_replace('#\[' . $tag . '(.*?)?\]#',
                                            do_shortcode($matches_default[0]),
                                            $return_str);
            }
        }
        //STEP 4
        return $return_str;
        
        
        /*
        while (preg_match('#\[post_terms *(.+?) ?\]#', $return_str, $matches_default))
        {
            $return_str = preg_replace('#\[post_terms *(.+?) ?\]#',
                                        do_shortcode($matches_default[0]),
                                        $return_str);
        }
        */
        
        //$default1 = preg_match('#\[test *(.+?) ?\]#', $str, $matches_default);
        ////$default_out = do_shortcode($matches_default[0]);
        //$default_out = preg_replace('#\[test *(.+?) ?\]#',
        //                             do_shortcode($matches_default[0]),
        //                             $str);
        
        /*
        //(Attempted) METHOD WITH WP REGEX FUNCTION
        $b = get_shortcode_regex(array('test'));
        $b1 = preg_match('/' . $b . '/s', $str, $matches_b);
        $str = preg_replace_callback('/' . $b . '/s',
                                     array($this, 'test_func'),
                                     $str);
        */
    }
    
    /**
     * ID Shortcode. 
     * 
     * Desc: Adds the Post ID. 
     * 
     * 1. Add post ID to return_str. 
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts
     * @return string Post->ID.
     */
    public function ID($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'ID');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->ID;
        
        //STEP 2
        return $return_str;
    }
    
    //TODO Try to combine [post_name] & [post_slug]
    /**
     * Post Name Shortcode
     * 
     * Desc: Adds the Post Slug (post_name).
     * 
     * 1. Add to return the Post Post_Name.  
     * 2. Return string.  
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts
     * @return string WP_Post->Post_Name.
     */
    public function post_name($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'post_name');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->post_name;
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Slug Shortcode
     * 
     * Desc: Adds the Post Slug from WP_Post->post_name.
     * 
     * 1. Add to return the Post Post_Name.  
     * 2. Return string.  
     * 
     * @since 0.4.0
     * 
     * @param array $atts
     * @return string Post->post_name.
     */
    public function post_slug($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'post_slug');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->post_name;
        
        //STEP 2
        return $return_str;
    }
    
    //FIX Characters not showing correctly in Chinese
    //TODO Create compatability with UNICODE
    /**
     * Post Title Shortcode. 
     * 
     * Desc: Adds the post/page title.
     * 
     * 1. Add to return Post Post_Title.  
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts
     * @return string Post->post_title.
     */
    public function post_title($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'post_title');
        $return_str = '';
        
        //STEP 1
        $return_str .= htmlspecialchars($this->_post->post_title);
        
        //STEP 2
        return $return_str;
    }
    
    //TODO ADD [post_link] (Alias)
    /**
     * Post Permalink (URL) Shortcode. 
     * 
     * Desc: Adds the post/page permalink/URL.
     * 
     * 1. Get & Add to return Post's Permalink.  
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts
     * @return string Permalink associated with Post ID.
     */
    public function post_permalink($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_permalink');
        
        $return_str = '';
        $return_str .= get_permalink($this->_post->ID);
        
        return $return_str;
    }
    
    /**
     * Post Guid (WP Default URL) Shortcode. 
     * 
     * Desc: Adds the post/page Guid. WP's Default URL (.com/?p=396).
     * 
     * 1. Add to return Post's Guid.  
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {}.
     * @return string Post Guid.
     */
    public function guid($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'post_guid');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->guid;
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Date Shortcode. 
     * 
     * Desc: Adds the post/page date.
     * 
     * 1. Get & Add to return Post's Formatted Date.  
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @link http://php.net/manual/en/function.date.php
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes.
     *      
     *      @type string $format Used to format date via PHP function.
     *      
     * }
     * @return string Post Date.
     */
    public function post_date($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'format' => 'm-d-Y'
        ), $atts, 'post_date');
        $return_str = '';
        
        //STEP 1
        $return_str .= mysql2date($atts_value['format'], $this->_post->post_date);
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Date GMT Shortcode. 
     * 
     * Desc: Adds the post/page date.
     * 
     * 1. Get & Add to return Post's Formatted GMT Date.  
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @link http://php.net/manual/en/function.date.php
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes.
     *      
     *      @type string $format Used to format date via PHP function.
     *      
     * }
     * @return string Post Date GMT.
     */
    public function post_date_gmt($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'format' => 'm-d-Y'
        ), $atts, 'post_date');
        $return_str = '';
        
        //STEP 1
        $return_str .= mysql2date($atts_value['format'], $this->_post->post_date_gmt);
        
        //STEP 2
        return $return_str;
    }
    
    //ADDED user_name (Alias) 
    //ADDED user_description (Alias)
    //REMOVED user_pass
    //REMOVED nickname
    //REMOVED primary_blog
    /**
     * Post Author Shortcode. 
     * 
     * Desc: Adds the Author/User Data associated with the post. 
     * 
     * 1. Set Label Types used within WP, and extensions. User_Friendly => WP_Friendly. 
     * 2. Get Author/User Data associated with WP_Post. 
     * 3. Add User Label to return, IF Label is valid with APL and IF data/prop 
     *    even exist in UserData (including extension labels registered with 
     *    APL & WP). 
     * 4. Otherwise, IF no variable exists, add default display_name to return. 
     * 
     * @since 0.1.0
     * @version 0.3.0 - Changed to Callback Function, and added 'label' attribute. 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *     
     *     Shortcode Attributes. 
     *     
     *     @type string 'label' Used to display user data.
     * }
     * @return string User Data from WP_Post.
     */
    public function post_author($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'label' => 'display_name'
        ), $atts, 'post_author');
        
        //STEP 1
        $label_type = array(
            //'ID' => 'ID',
            
            //// Data Object (WP's Standard)
            'ID'            => 'ID',
            'user_login'    => 'user_login',
            'user_name'     => 'user_login',
            'user_nicename' => 'user_nicename',
            'display_name'  => 'display_name',
            'user_email'    => 'user_email',
            'user_url'      => 'user_url',
            //TODO ADD data->user_registered for "Member Since: xx-xx-xxxx xx:xx:xx
            //TODO ADD/FIX user_registered date format
            
            //// Back_Compat_Keys Array (Legacy)
            'description'       => 'user_description',
            'user_description'  => 'user_description',
            'user_firstname'    => 'user_firstname',
            'user_lastname'     => 'user_lastname'
            
            //TODO ADD roles (array)
            
            //TODO ADD Extension Hook
        );
        
        $return_str = '';
        //STEP 2
        $userData = get_userdata($this->_post->post_author);
        
        //STEP 3
        if (isset($label_type[$atts_value['label']]) && 
            $userData->has_prop($label_type[$atts_value['label']]))
        {
            $return_str .= $userData->get($label_type[$atts_value['label']]);
        }
        
        //STEP 4
        else
        {
            //TODO ADD Admin Error
            $return_str .= $userData->data->display_name;
        }
        
        //STEP 5
        return $return_str;
    }
    //TODO ADD [post_link] (Alias)
    public function post_permalink($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_permalink');
        
        $return_str = '';
        $return_str .= get_permalink($this->_post->ID);
        
        return $return_str;
    }
    
    public function guid($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_guid');
        
        $return_str = '';
        $return_str .= $this->_post->guid;
        
        return $return_str;
    }
    public function post_date($atts)
    {
        
    }
    //'post_date_gmt',
    //'post_modified',
    //'post_modified_gmt',

    //'post_thumb',

    //'post_content',
    //'post_excerpt',

    //'comment_count',
    //'post_comments',

    //'post_parent',

    //'post_tags',
    //'post_categories',
    ////////////////////////////////////////////////////////////////////////////
    ///////// TESTING //////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    /*
    public function test_func($atts)
    {
        //$att = shortcode_atts( array(
        //    'a' => 'apple',
        //    'b' => 'banana'
        //), $atts, 'test' );
        $a = "hello world";
        $b = $a;
        return $a;
    }*/
    //TODO Add public do_[shortcode] function?
    public function post_terms($atts)
    {
        $atts_value = shortcode_atts( array(
            'taxonomy' => 'category',
            'delimiter' => ', ',
            'links' => 'true',
            'max' => '0',
            'empty_message' => ''
        ), $atts, 'post_terms');
        
        //DO INPUT ($atts) CHECKS
        //Check if taxonomy slug correctly-exists, and if not,
        //  revert to default.
        if (!taxonomy_exists($atts_value['taxonomy']))
        {
            $atts_value['taxonomy'] = 'category';
        }
        
        //Grab Terms withing Taxonomy
        //Check to see if any terms were returned (!empty), 
        //  and if so, return empty string.
        $terms = get_the_terms($this->_post->ID, $atts_value['taxonomy']);
        if (!$terms)
        {
            //TODO ADD Empty_Message
            return $atts_value['empty_message'];
        }
        
        //Slice array to max amount to avoid extra steps before hand.
        $array_total = count($terms);
        if ($atts_value['max'] != '0')
        {
            if ($array_total > intval($atts_value['max']))
            {
                $terms = array_slice($terms, 0, intval($atts_value['max']));
                $array_total = count($terms);
            }
        }
        
        
        //FINAL INIT
        $return_str = '';
        $i = 1;
        $links = TRUE;
        
        //Convert $atts['links'] to a FALSE boolion to prevent multiple calls 
        //  to a function.
        if (strtolower($atts_value['links']) == 'false')
        {
            $links = FALSE;
        }
        
        foreach ($terms as $term_key => $term)
        {
            
            if ($links)
            {
              $return_str .= '<a href="' . get_tag_link($term->term_id) . '" >' . $term->name . '</a>';
            }
            else
            {
              $return_str .= $term->name;
            }
            
            //If not last, add delimiter
            if ($array_total > $i)
            {
                $return_str .= $atts_value['delimiter'];
            }
            
            $i++;
        }
        return $return_str;
        
    }
            
            
}
?>
