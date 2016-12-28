<?php
class APL_Shortcodes
{
    
}
class APL_InternalShortcodes
{
    //TODO ADD post object to construct OR keep in replace function?
    public function __construct()
    {
        add_shortcode('test', array($this, 'test_func'));
        
    }
    public function __destruct() 
    {
        remove_shortcode('test');
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
    public function replace($preset_content, $post_content)
    {
        //INIT
        $return_str = $preset_content;
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
    }
            
}
?>
