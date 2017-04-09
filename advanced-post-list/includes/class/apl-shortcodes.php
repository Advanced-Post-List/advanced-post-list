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

//Changed (Internal) Shortcodes from a function & callback object, to a Class 
//  object, encapsulating majority of the functionality to it's own instance.
//(DITTO) Changed (Internal) Shortcodes to a Class Object.
//Fixed Shortcodes error with mixed attributes.
//Added better default method with shortcode_atts.
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
    
    /**
     * Post Index in Post Lists to display a digit.  
     * 
     * @since 0.1.0
     * @version 0.4.0 - Added to Class Object. 
     * @access private
     * @var object $_item_count Preset Post List index/count.
     */
    private $_item_count;
    
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
            
            'post_permalink',
            'guid',
            
            'post_date',
            'post_date_gmt',
            'post_modified',
            'post_modified_gmt',
            
            'post_author',
            
            'post_thumb',
            
            'post_content',
            'post_excerpt',
            
            'comment_count',
            'post_comments',
            
            'post_parent',
            
            'post_type',
            'post_tags',
            'post_categories',
            'post_terms',
            
            'post_meta',
            
            'php_function',
            
            //LOOP SHORTCODE FUNCTIONS for $this->replace()
            'item_number', 
            //'final_end', //executed outside of class.
            
            //Extensions/Hook
            'post_pdf' //Kalin's PDF Plugin (obsolete?)
        );
        
        
        return $return_array;
    }
    
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
    public function replace($preset_content, $wp_post)
    {
        //INIT
        $return_str = $preset_content;
        $this->_post = $wp_post;
        
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
                                            $return_str,
                                            1);
            }
        }
        
        $this->_item_count++;
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
        ), $atts, 'post_date_gmt');
        $return_str = '';
        
        //STEP 1
        $return_str .= mysql2date($atts_value['format'], $this->_post->post_date_gmt);
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Date Modified Shortcode. 
     * 
     * Desc: Adds the post/page modified date.
     * 
     * 1. Get & Add to return Post's Formatted Modified Date.  
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
     * @return string Post Modified.
     */
    public function post_modified($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'format' => 'm-d-Y'
        ), $atts, 'post_modified');
        $return_str = '';
        
        //STEP 1
        $return_str .= mysql2date($atts_value['format'], $this->_post->post_modified);
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Date Modified GMT Shortcode. 
     * 
     * Desc: Adds the post/page modified date GMT.
     * 
     * 1. Get & Add to return Post's Formatted Modified Date GMT.  
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
     * @return string Post Modified GMT.
     */
    public function post_modified_gmt($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'format' => 'm-d-Y'
        ), $atts, 'post_modified_gmt');
        $return_str = '';
        
        //STEP 1
        $return_str .= mysql2date($atts_value['format'], $this->_post->post_modified_gmt);
        
        //STEP 2
        return $return_str;
    }
    
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
     * @link https://codex.wordpress.org/Function_Reference/get_userdata
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
        $return_str = '';
        
        //STEP 1
        $label_type = array(
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
            //TODO ADD A dynamic method for displaying author data.
            //TODO ADD Extension Hook
        );
        
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
    
    //TODO ADD Default Text?
    //TODO ADD Improved method for checking for site media on page, and then
    //         adding images [post_attachments]. Thus preventing outside images 
    //         that can't be changed into a "Thumbnail" in WP.
    //           Note: Create a shortcode designed specifically for grabbing <img> 
    //                 from post_content.
    /**
     * Post Thumb Shortcode. 
     * 
     * Desc: Adds a Post Thumb/Featured Image URL associated with the post, but 
     * can also grab an image within WP_Post->post_content (extract=on/force). 
     * Image Sizes include (default) thumbnail, medium, large, full, 
     * and custom "XX, XX" (closest image size available).
     * 
     * Note: Post_Content Images are not resizable.
     * 
     * 1. If 'size' string is numeric, then convert 'size' to an array(xx, yy).
     * 2. Grab Featured Image from Post/Page w/ 'size'.
     * 3. If 'extract' is 'on' OR 'force', add to return the src URL in img tag 
     *    from $post->post_content.
     * 4. Return string.
     * 
     * @since 0.1.0
     * @version 0.3.0 - Added 'extract' attribute. 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     *                  Added Custom Size support.
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $size      Sets the image size used by WP's function. 
     *                              (thumbnail, medium, large, full, and 
     *                              custom "XX, XX"). 
     *      @type string $extract   Extract from post_content (none, on, & force). 
     * }
     * @return string Post Image URL.
     */
    public function post_thumb($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'size'      => 'thumbnail',
            'extract'   => 'none'
        ), $atts, 'post_thumb');
        $return_str ='';
        
        //STEP 1
        if (is_numeric(substr($atts_value['size'], 0, 1)) && substr($atts_value['size'], 0, 1) != '0')
        {
            $atts_value['size'] = explode(',', $atts_value['size']);
            foreach ($atts_value['size'] as $key => $value)
            {
                $atts_value['size'][$key] = intval($value);
            }
        }
        
        //STEP 2
        if ( strtolower($atts_value['extract']) != "force" && current_theme_supports('post-thumbnails'))
        {
            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($this->_post->ID), $atts_value['size']);
            if ($featured_image)
            {
                $return_str .= $featured_image[0];
            }
        }
        
        //EXTRACT/FALLBACK IMAGE (No Featured Image)
        //STEP 3
        if ( strtolower($atts_value['extract']) != 'none' && empty($return_str) )
        {
            //Parse and grab src="{}"
            preg_match_all('/src="([^"]+)"/', $this->_post->post_content, $matches);
            if (!empty($matches[1]))
            {
                //TODO ADD Offset? OR save for post_attachments
                $return_str .= $matches[1][0];
            }
        }
        
        //STEP 4
        return $return_str;
    }
    
    /**
     * Post Content Shortcode. 
     * 
     * Desc: Adds the Post_Content. 
     * 
     * 1. Add to return the Post's Post_Content. 
     * 2. Return string. 
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts None.
     * @return string Post->Post_Content.
     */
    public function post_content($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array() , $atts, 'post_content');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->post_content;
        
        //STEP 2
        return $return_str;
    }
    
    /**
     * Post Excerpt Shortcode. 
     * 
     * Desc: Adds the Post Excerpt, or a substring of Post Content.
     * 
     * 1. IF Post_Excerpt is empty, then use Post_Content with X amount of 
     *    characters (Default 250.). Otherwise skip to STEP 4.
     * 2. Convert 'length' Variable Type to Int.
     * 3. Add a substring from Post_Content to return. X amount of length and
     *    with shortcodes stripped out.
     * 4. Add Post_Excerpt to return.
     * 5. Return String.
     * 
     * @since 0.1.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     *                  Also changed substr() to mp_substr() for encoding compatability.
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $length    Sets the character length.  
     * }
     * @return string Post->Excerpt OR Post->Post_Content substring.
     */
    public function post_excerpt($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'length' => '250'
        ) , $atts, 'post_excerpt');
        $return_str = '';
        
        //STEP 1
        if(empty($this->_post->post_excerpt))
        {
            //STEP 2 
            if (is_numeric($atts_value['length']))
            {
                $atts_value['length'] = intval($atts_value['length']);
            }
            else
            {
                $atts_value['length'] = 250;
            }
            
            //BUG? Possible if length is longer than string.
            //STEP 3
            $encoding = mb_internal_encoding();
            $return_str = strip_shortcodes(mb_substr(strip_tags($this->_post->post_content),
                                                     0,
                                                     $atts_value['length'],
                                                     $encoding));
            //$return_str = mb_substr(strip_shortcodes(strip_tags($this->_post->post_content)), 
            //                        0, 
            //                        $atts_value['length'] , 
            //                        $encoding);
        }
        
        //STEP 4
        else
        {
            $return_str .= $this->_post->post_excerpt;
        }
        
        //STEP 5
        return $return_str;
    }
    
    /**
     * Post Comment Count Shortcode. 
     * 
     * Desc: Adds the Post Comment_Count, which displays the amount of comments
     * per post.
     * 
     * 1. Add to return Post Comment_Count.
     * 2. Return string.
     * 
     * @since 0.1.0
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts None.
     * @return string Post->Comment_Count.
     */
    public function comment_count($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(), $atts, 'comment_count');
        $return_str = '';
        
        //STEP 1
        $return_str .= $this->_post->comment_count;
        
        //STEP 2
        return $return_str;
    }
    
    //TODO ADD Max Amount
    //TODO ADD Sort (OrderBy & Order)
    //TODO ADD Include/Exclude (Author?) User_ID Filter
    //TODO ADD Date Filter
    //TODO ADD Preset Design. Default would use this, but a custom preset would
    //         have its own design.
    /**
     * Post Comment Shortcode. 
     * 
     * Desc: Adds an HTML string to display comments in a set format.
     * 
     * 1. Get Kalin's PDF comments. Support old method. 
     * 2. Get 'approved' comments from Post ID. 
     * 3. Add to return the Before Attribute. 
     * 4. For each Post_Comments, add formatted string to return, w/ author link 
     *    if available.
     * 5. Add to return the After Attribute. 
     * 6. Return string.
     * 
     * @since 0.1.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @link https://codex.wordpress.org/Function_Reference/get_comments
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $before    Adds a string before the list. 
     *      @type string $after     Adds a string after the list.
     * }
     * @return string Post Excerpt OR formatted Post_Content.
     */
    public function post_comments($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'before' => '',
            'after' => ''
        ), $atts, 'post_comments');
        $return_str = '';
        
        //STEP 1
        if (defined("KALINS_PDF_COMMENT_CALLBACK"))
        {
            return call_user_func(KALINS_PDF_COMMENT_CALLBACK);
        }
        
        
        //STEP 2
        $args = array(
            'status' => 'approve',
            'post_id' => $this->_post->ID
        );
        $post_comments = get_comments($args, $this->_post->ID);
        
        //STEP 3
        $return_str .= $atts_value['before'];
        
        //STEP 4
        foreach ($post_comments as $comment)
        {
            if ($comment->comment_author_url == "")
            {
                $comment_author = $comment->comment_author;
            }
            else
            {
                $comment_author = '<a href="' . $comment->comment_author_url . '" >' . $comment->comment_author . "</a>";
            }
            
            $return_str .= '<p>' . $comment_author . 
                           " - " . $comment->comment_author_email . 
                           " - " . get_comment_date(null, $comment->comment_ID) . 
                           " @ " . get_comment_date(get_option('time_format'), $comment->comment_ID) . 
                           "<br />" . $comment->comment_content . "</p>";
        }
        
        //STEP 5
        $return_str .= $atts_value['after'];
        
        //STEP 6 
        return $return_str;
    }
    
    /**
     * Page Parent Shortcode. 
     * 
     * Desc: Adds Page Parent Title, and optionally with link.
     * 
     * 1. Check to see if Post Parent is set. 
     * 2. Grab Post Parent Title w/ Link if set to 'true'. 
     * 3. Return string.
     * 
     * @since 0.1.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $link      Returns as html link if true. 
     * }
     * @return string Post Parent Title OR as html link.
     */
    public function post_parent($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'link' => 'true'
        ), $atts, 'post_parent');
        $return_str = '';
        
        //STEP 1
        if ($this->_post->post_parent != 0)
        {
            //STEP 2
            if (strtolower($atts_value['link']) == "false")
            {
                $return_str .= get_the_title($this->_post->post_parent);
            }
            else
            {
                $return_str .= '<a href="' . get_permalink($parentID) . '" >' . get_the_title($parentID) . '</a>';
            }
        }
        
        //STEP 3
        return $return_str;
    }
    
    /*
    'Posts',                    'Posts'
    'singular_name' =>          'Post'
    'add_new' =>                'Add New post'
    'add_new_item' =>           'Add New Post'
    'edit_item' =>              'Edit Post'
    'new_item' =>               'New Post'
    'view_item' =>              'View Post'
    'view_items' =>             'View Posts'
    'search_items' =>           'Search Posts'
    'not_found' =>              'No posts found.'
    'not_found_in_trash' =>     'No posts found in Trash.'
    'parent_item_colon' =>      'Parent Page:'
    'all_items' =>              'All Posts'
    'archives' =>               'Post Archives'
    'attributes' =>             'Post Attributes'
    'insert_into_item' =>       'Insert into post'
    'uploaded_to_this_item' =>  'Uploaded to this post'
    'featured_image' =>         'Featured Image'
    'set_featured_image' =>     'Set featured image'
    'remove_featured_image' =>  'Remove featured image'
    'use_featured_image' =>     'Use as featured image'
    'filter_items_list' =>      'Filter posts list'
    'items_list_navigation' =>  'Posts list navigation'
    'items_list' =>             'Posts list'
    */
    //TODO ADD Array String to filter through like this->post_author
    /**
     * Post Type Shortcode. 
     * 
     * Desc: Adds the Post Type (Label) Name associated with the post/page ID.
     * 
     * 1. Get Post Type Object associated with the post. 
     * 2. Get the set label from object, otherwise get name. 
     * 3. Return string.
     * 
     * @since 0.4.0
     * 
     * @link https://codex.wordpress.org/Function_Reference/get_post_type_object
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $label     Gets post_type->labels, or defaults to 'name'.
     * }
     * @return string Post Type (Label) Name.
     */
    public function post_type($atts)
    {
        $atts_value = shortcode_atts( array(
            'label' => 'name'
        ), $atts, 'post_type');
        $return_str = '';
        
        //STEP 1
        $post_type_obj = get_post_type_object($this->_post->post_type);
        
        //STEP 2
        $label = $atts_value['label']; 
        if ( isset($post_type_obj->labels->$label) )
        {
            $return_str .= $post_type_obj->labels->$label;
        }
        else if ( isset($post_type_obj->labels->name) )
        {
            $return_str .= $post_type_obj->labels->name;
        }
        
        //STEP 3
        return $return_str;
    }
    
    /**
     * Categories Shortcode. 
     * 
     * Desc: Adds Categories associated with Post/Page. 
     * 
     * 1. Set Attribute 'Links' to a Boolean variable. 
     * 2. Get the categories. If none, do Step 4. 
     * 3. For each category there is add the category name, w/ link if true, 
     *    and add a delimiter except for last category.
     * 4. Return string.
     * 
     * @since 0.1.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $delimiter     Inserts a separator, default is ", ".
     *      @type string $links         Return as an html link if true.
     * }
     * @return string Categories used in post/page.
     */
    public function post_categories($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'delimiter' => ', ',
            'links' => 'true'
        ), $atts, 'post_categories');
        $return_str = '';
        
        //STEP 1
        $atts_value['links'] = TRUE;
        if (strtolower($atts_value['links']) == 'false')
        {
            $atts_value['links'] = FALSE;
        }
        
        //STEP 2
        $post_categories = get_the_category($this->_post->ID);
        $array_total = count($post_categories);
        $i = 1;
        if ($post_categories)
        {
            //STEP 3
            foreach ($post_categories as $category)
            {
                if ($atts_value['links'])
                {
                    $return_str .= '<a href="' . get_tag_link($category->term_id) . '" >' . $category->name . '</a>';
                }
                else
                {
                    $return_str .= $category->name;
                }

                if ($array_total > $i)
                {
                    $return_str .= $atts_value['delimiter'];
                }
                $i++;
            }
        }
        
        //STEP 4
        return $return_str;
    }
    
    /**
     * Tags Shortcode. 
     * 
     * Desc: Adds Tags associated with Post/Page. 
     * 
     * 1. Set Attribute 'Links' to a Boolean variable. 
     * 2. Get the tags. If none, do Step 4. 
     * 3. For each tag there is add the tag name, w/ link if true, and add a 
     *    delimiter except for last tag.
     * 4. Return string.
     * 
     * @since 0.1.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $delimiter     Inserts a separator, default is ", ".
     *      @type string $links         Return as an html link if true.
     * }
     * @return string Tags used in post/page.
     */
    public function post_tags($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'delimiter' => ', ',
            'links' => 'true'
        ), $atts, 'post_tags');
        $return_str = '';
        
        
        //STEP 1
        $atts_value['links'] = TRUE;
        if (strtolower($atts_value['links']) == 'false')
        {
            $atts_value['links'] = FALSE;
        }
        
        //STEP 2
        $post_tags = get_the_tags($this->_post->ID);
        $array_total = count($post_tags);
        $i = 1;
        if ($post_tags)
        {
            //STEP 3
            foreach ($post_tags as $tag)
            {
                if ($atts_value['links'])
                {
                    $return_str .= '<a href="' . get_tag_link($tag->term_id) . '" >' . $tag->name . '</a>';
                }
                else
                {
                    $return_str .= $tag->name;
                }

                if ($array_total > $i)
                {
                    $return_str .= $atts_value['delimiter'];
                }
                $i++;
            }
        }
        
        //STEP 4
        return $return_str;
    }
    
    /**
     * Post Terms Shortcode. 
     * 
     * Desc: Adds (Custom) Taxonomy Terms associated with Post/Page. Displays both
     * post and page types, and will display terms from taxonomies that are set 
     * to public
     * 
     * 1. Get Taxonomy Terms if Taxonomy is valid, otherwise get (default) Categories. 
     * 2. Convert $atts['links'] to a boolean variable. 
     * 3. If terms exists format them to return, otherwise add an empty message 
     *    and do Step 6.
     * 4. Slice list of terms to max amount, and store array total and (i)ndex.
     * 5. For each term there is add the term name, w/ link if true, and add a
          delimiter except for last term.
     * 6. Return string.
     * 
     * @since 0.3.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $taxonomy          [Req] Gets the terms from taxonomy, defaults to 
     *                                      'category'. 
     *      @type string $delimiter         Inserts a separator, default is ", ".
     *      @type boolean $links            Return as an HTML link if true.
     *      @type integer $max              Total amount to display. 
     *      @type string $empty_message     Display a message if no terms are returned.
     * }
     * @return string Taxonomy Terms used in post/page.
     */
    public function post_terms($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'taxonomy' => 'category',
            'delimiter' => ', ',
            'links' => 'true',
            'max' => '0',
            'empty_message' => ''
        ), $atts, 'post_terms');
        $return_str = '';
        
        //STEP 1
        if (!taxonomy_exists($atts_value['taxonomy']))
        {
            $atts_value['taxonomy'] = 'category';
        }
        $terms = get_the_terms($this->_post->ID, $atts_value['taxonomy']);
        
        //STEP 2
        $links = TRUE;
        if (strtolower($atts_value['links']) == 'false')
        {
            $links = FALSE;
        }
        
        //STEP 3
        if ($terms)
        {
            //STEP 4
            $i = 1;
            $array_total = count($terms);
            if ($atts_value['max'] != '0')
            {
                if ($array_total > intval($atts_value['max']))
                {
                    $terms = array_slice($terms, 0, intval($atts_value['max']));
                    $array_total = count($terms);
                }
            }
            //STEP 5
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

                if ($array_total > $i)
                {
                    $return_str .= $atts_value['delimiter'];
                }

                $i++;
            }
        }
        else
        {
            $return_str .= $atts_value['empty_message'];
        }
        
        //STEP 6
        return $return_str;
        
    }
    
    /**
     * Post Meta Shortcode. 
     * 
     * Desc: Adds Post MetaData used within posts/pages. Returns empty if nothing
     * is found.
     * 
     * 1. If post_meta 'name' is valid then get MetaData and add to return, 
     *    otherwise skip to Step 2.
     * 2. Return string.
     * 
     * @since 0.3.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $name      Meta Name/Label used within post.
     * }
     * @return string Taxonomy Terms used in post/page.
     */
    public function post_meta($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'name' => ''
        ), $atts, 'post_meta');
        $return_str = '';
        
        //STEP 1
        if(!empty($atts_value['name']) && metadata_exists('post', $this->_post->ID, $atts_value['name']))
        {
            $post_meta_arr = get_post_meta($this->_post->ID, $atts_value['name'], FALSE);
            foreach ($post_meta_arr as $meta)
            {
                $return_str .= $meta;
            }
        }
        //TODO ADD Else Alert to Admin that metadata is invalid or doesn't exist.
        
        //STEP 2
        return $return_str;
        
    }
    
    //ADDED Check if function exists.
    /**
     * PHP Function Shortcode. 
     * 
     * Desc: Adds custom php functions within the environment/instance, and displays
     * the returned data as a string.
     * 
     * 1. Check to see if Constant Variable is set, if not, add error then do Step 4. 
     * 2. Check to see if a function 'name' is given, if empty, add error then do 
     *    Step 4.
     * 3. If the function exists, do the function /w 'param'.
     * 4. Return string.
     * 
     * @since 0.3.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in
     *                  functions for setting default attributes & do_shortcode().
     *                  Added Constant APL_ALLOW_PHP to require.
     *                  Added Check if function exists.
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type string $name      [REQ] PHP Function Name. 
     *      @type string $param     Data to send to custom php function.
     * }
     * @return string Returned data from PHP Function.
     */
    public function php_function($atts)
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'name' => '',
            'param' => ''
        ), $atts, 'php_function');
        $return_str = '';
        
        //STEP 1
        if (!defined("KALINS_ALLOW_PHP") && KALINS_ALLOW_PHP !== true || 
            !defined("APL_ALLOW_PHP") && APL_ALLOW_PHP !== true)
        {
            $return_str .= 'Error: add define("APL_ALLOW_PHP", true); to your '.
                            'wp-config.php for php_function to work.';
        }
        //STEP 2
        else if (empty($atts_value['name']))
        {
            $return_str .= 'Error: Name shortcode attribute must have a name. '.
                           'For ex. [php_function name="FUNCTION_NAME"]';
        }
        //STEP 3
        else if (function_exists($atts_value['name']))
        {
            if (!empty($atts_value['param']))
            {
                $return_str .= call_user_func($atts_value['name'], 
                                              $this->_post, 
                                              $atts_value['param']);
            }
            else
            {
                $return_str .= call_user_func($atts_value['name'], 
                                              $this->_post);
            }
        }
        
        //STEP 4
        return $return_str;
    }
    
    /**
     * Post List Item Number Shortcode. 
     * 
     * Desc: Adds a Numeric value to each post; by X amount of increments, and 
     * starting from where it is offset as. 
     * 
     * 1. If any non-digits are present, reset to default. 
     * 2. Convert variables to integer types. 
     * 3. Add item number with increment and added offset. 
     * 4. Return string.
     * 
     * @since 0.3.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in 
     *                  functions for setting default attributes & do_shortcode(). 
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type integer $offset       Digit to start from. 
     *      @type integer $increment    Amount to increase by. 
     * }
     * @return string Index value. 
     */
    public function item_number()
    {
        //INIT
        $atts_value = shortcode_atts( array(
            'offset' => '1',
            'increment' => '1'
        ), $atts, 'item_number');
        $return_str = '';
        
        //STEP 1
        if (!is_numeric($atts_value['increment']))
        {
            $atts_value['increment'] = '1';
        }
        if (!is_numeric($atts_value['offset']))
        {
            $atts_value['offset'] = '1';
        }
        //STEP 2
        $atts_value['increment'] = intval($atts_value['increment']);
        $atts_value['offset'] = intval($atts_value['offset']);
        
        //STEP 3
        $return_str .= (string) (($this->_item_count * $atts_value['increment']) + $atts_value['offset']);
        
        //STEP 4
        return $return_str;
    }
    
    /**
     * Final End Post List Shortcode. 
     * 
     * Desc: Determines the End of the Final post to display. 
     * 
     * 1. Get everything except everything after the last Final_End. 
     * 2. Strip all Final_End shortcodes. 
     * 3. Return string. 
     * 
     * @since 0.3.0 
     * @version 0.4.0 - Changed to Class function, and uses WP's built-in 
     *                  functions for setting default attributes & do_shortcode(). 
     * 
     * @param array $atts {
     *      
     *      Shortcode Attributes. 
     *      
     *      @type integer $content      Preset Content that will be displayed. 
     * }
     * @return string Preset (Post List) Content. 
     */
    public function final_end($content)
    {
        //INIT
        $return_str = '';
        
        //STEP 1
        $return_str .= substr($content, 0, strrpos($content, "[final_end]"));
        
        //STEP 2
        $return_str = str_replace('[final_end]', '', $return_str);
        
        //STEP 3
        return $return_str;
    }
    
    //ADDED Check if plugin is active.
    public function post_pdf($atts)
    {
        $att_value = shortcode_atts( array(), $atts, 'post_pdf');
        $return_str = '';
        
        if (is_plugin_active('kalins-pdf-creation-station'))
        {
            if ($this->_post->post_type == "post")
            {
                $postID = "po_" . $this->post_id;
            }
            else if($this->_post->post_type == "page")
            {
                $postID = "pg_" . $this->post_id;
            }
            $return_str .= get_bloginfo('wpurl') . '/wp-content/plugins/kalins-pdf-creation-station/kalins_pdf_create.php?singlepost=' . $postID;
        }
        
        return $return_str;
        
    }

}
?>
