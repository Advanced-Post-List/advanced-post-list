<?php
class APL_Shortcodes
{
    
}
class APL_InternalShortcodes
{
    private $_post;
    
    //TODO ADD post object to construct OR keep in replace function?
    public function __construct()
    {
        $shortcode_list = $this->shortcode_list();
        foreach ($shortcode_list as $tag)
        {
            add_shortcode($tag, array($this, $tag));
        }
        global $post;
    }
    public function remove() 
    {
        $shortcode_list = $this->shortcode_list();
        foreach ($shortcode_list as $tag)
        {
            remove_shortcode($tag);
        }
        unset($this->_post);
    }
    
    /*
     *      METHOD 1 (Current concept)
     * 1. Find any Registered (Internal) Shortcodes, 
     * 2. Grab the beginning and end of []
     * 3. Filter through searching for any attributes
     */
    /*
     *      METHOD 2 (Ideal)
     * 1. Init: register (internal) shortcodes; remove at end.
     * 2. Run shortcode function via WP function to call shortcode function.
     *      A. Grab shortcode w/ atts from preset_content, do_shortcode with the
     *           string, and then use the shortcode function properly.
     * 3. Set default atts, and add any new atts from param. shortcode_atts()
     * 4. Remove (internal) shortcodes.
     */
    /**
     * <p><b>Desc:</b> <p>
     * @access public
     * @param string $preset_content The design content for List Content 
     * @param string $post_content WP Post object.
     * 
     * @since 0.4.0
     * @version 0.4.0
     * 
     * @uses 
     * 
     * @tutorial 
     * <ol>
     * <li value="1"></li>
     * <li value="2"></li>
     * <li value="3"></li>
     * <li value="4"></li>
     * <li value="5">If, do <b>Step 6</b></li>
     * <li value="6"></li>
     * </ol>
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
    {
        //INIT
        $return_str = $preset_content;
        $this->_post = $post_content;
        
        $shortcode_tags = $this->shortcode_list();
        foreach ($shortcode_tags as $tag) 
        {
            while (preg_match('#\[' . $tag . '(.*?)?\]#', $return_str, $matches_default))
            {
                $return_str = preg_replace('#\[' . $tag . '(.*?)?\]#',
                                            do_shortcode($matches_default[0]),
                                            $return_str);
            }
        }
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
    public function ID($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'ID');
        
        $return_str = '';
        $return_str .= $this->_post->ID;
        
        return $return_str;
    }
    //TODO Try to combine [post_name] & [post_slug]
    public function post_name($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_name');
        
        $return_str = '';
        $return_str .= $this->_post->post_name;
        
        return $return_str;
    }
    public function post_slug($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_slug');
        
        $return_str = '';
        $return_str .= $this->_post->post_name;
        
        return $return_str;
    }
    //FIX Characters not showing correctly in Chinese
    //TODO Create compatability with UNICODE
    public function post_title($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_title');
        
        $return_str = '';
        $return_str .= htmlspecialchars($this->_post->post_title);
        
        return $return_str;
    }
    /**
     * <p><b>Desc:</b> <p>
     * @access public
     * @param array $atts Shortcode Attributes. 
     * @return string $return_str Author data from post/page.
     * 
     * @since 0.4.0
     * @version 0.4.0
     * 
     * @uses 
     * 
     * @tutorial 
     * <ol>
     * <li value="1"></li>
     * <li value="2"></li>
     * <li value="3"></li>
     * <li value="4"></li>
     * <li value="5">If, do <b>Step 6</b></li>
     * <li value="6"></li>
     * </ol>
     */
    
    
    //ADDED user_name (Alias) 
    //ADDED user_description (Alias)
    //REMOVED user_pass
    //REMOVED nickname
    //REMOVED primary_blog
    public function post_author($atts)
    {
        $atts_value = shortcode_atts( array(
            'label' => 'display_name'
        ), $atts, 'post_author');
        
        //Set Label Value and User Variables registered with APL
        $label_type = array(
            //'ID' => 'ID',
            
            //Data Object (WP's Standard)
            'ID'            => 'ID',
            'user_login'    => 'user_login',
            'user_name'     => 'user_login',
            'user_nicename' => 'user_nicename',
            'display_name'  => 'display_name',
            'user_email'    => 'user_email',
            'user_url'      => 'user_url',
            //TODO ADD data->user_registered for "Member Since: xx-xx-xxxx xx:xx:xx
            //TODO ADD/FIX user_registered date format
            
            //Back_Compat_Keys Array (Legacy)
            'description'       => 'user_description',
            'user_description'  => 'user_description',
            'user_firstname'    => 'user_firstname',
            'user_lastname'     => 'user_lastname'
            
            //TODO ADD roles (array)
            
            //TODO ADD Extension Hook
        );
        
        $return_str = '';
        $userData = get_userdata($this->_post->post_author);
        
        //Check to see IF an Author Label is valid with APL and IF key/prop even 
        //  exist on WP, including extension labels registered with APL & AP.
        //Otherwise, IF no variable exists, return default display_name.
        if (isset($label_type[$atts_value['label']]) && 
            $userData->has_prop($label_type[$atts_value['label']]))
        {
            $return_str .= $userData->get($label_type[$atts_value['label']]);
        }
        else
        {
            //TODO ADD Admin Error
            $return_str .= $userData->data->display_name;
        }
        
        return $return_str;
    }
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
