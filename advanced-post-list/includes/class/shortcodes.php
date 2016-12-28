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
        add_shortcode('ID', array($this, 'ID'));
        
        add_shortcode('test', array($this, 'test_func'));
        add_shortcode('post_name', array($this, 'post_name'));
        add_shortcode('post_slug', array($this, 'post_slug'));
        add_shortcode('post_terms', array($this, 'post_terms'));
        
    }
    public function __destruct() 
    {
        remove_shortcode('ID');
        
        remove_shortcode('test');
        remove_shortcode('post_name');
        remove_shortcode('post_slug');
        remove_shortcode('post_terms');
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
        $list = array(
            'ID',
            'post_name',
            'post_slug',
            'post_title',
            
            'post_author',
            
            'post_permalink',
            'guid',
            
            'post_date',
            'post_date_gmt',
            'post_modified',
            'post_modified_gmt',
            
            'post_thumb',
            
            'post_content',
            'post_excerpt',
            
            'comment_count',
            'post_comments',
            
            
            
            
            'post_parent',
            
            'post_tags',
            'post_categories',
            'post_terms',
            
            'post_meta',
            
            
            
            //Kalin's PDF Plugin (obsolete?)
            'post_pdf',
            
            'item_number',
            'final_end',
            'php_function',
            'test' //TEST
        );
        //TODO - Extension support for additional functionality
    }
    public function replace($preset_content, $post_content)
    {
        //INIT
        $return_str = $preset_content;
        $this->_post = $post_content;
        
        /*
        $shortcode_tags = $this->shortcode_list();
        foreach ($shortcode_tags as $tag) 
        {
            while (preg_match('#\['.$tag.' *(.+?) ?\]#', $str, $matches_default))
            {
                $default_out = preg_replace('#\['.$tag.' *(.+?) ?\]#',
                                            do_shortcode($matches_default[0]),
                                            $str);
            }
        }
        */
        
        
        while (preg_match('#\[post_terms *(.+?) ?\]#', $return_str, $matches_default))
        {
            $return_str = preg_replace('#\[post_terms *(.+?) ?\]#',
                                        do_shortcode($matches_default[0]),
                                        $return_str);
        }
        return $return_str;
        
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
    }
    public function post_slug($atts)
    {
        $atts_value = shortcode_atts( array() , $atts, 'post_slug');
        
        $return_str = '';
        $return_str .= $this->_post->post_name;
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
