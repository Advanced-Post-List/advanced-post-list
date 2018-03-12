<?php
/**
 * APL Shortcodes
 *
 * APL Shortcode API: APL_InternalShortcodes class
 *
 * @link https://github.com/Advanced-Post-List/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.4.0
 */

/**
 * APL Shortcodes
 *
 * Handles all internal do_shortcodes when preset post lists are called. Each time
 * the class object is created, shortcodes are added, replace function used to
 * do_shortcodes, and ends by removing shortcodes (currently manual $this->remove).
 *
 * @since 0.4.0
 */
class APL_Shortcodes {
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
	 * @param string $preset_content The design content for List Content.
	 * @param string $post_content WP Post object.
	 * @param array $terms Terms to check.
	 * @return array Terms that are not stopwords.
	 */
}

/**
 * APL Internal Shortcodes
 *
 * Handles all internal do_shortcodes when preset post lists are called. Each time
 * the class object is created, shortcodes are added, replace function used to
 * do_shortcodes, and ends by removing shortcodes (currently manual $this->remove).
 *
 * @since 0.4.0
 */
class APL_Internal_Shortcodes {
	/**
	 * WP_Post object for holding post details accessed by shortcode functions
	 *
	 * @since 0.4.0
	 * @access private
	 * @var object $_post WP_Post Object for holding data.
	 */
	private $_post;

	/**
	 * Post Index in Post Lists to display a digit
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Added to Class Object.
	 * @access private
	 * @var object $_item_count Preset Post List index/count.
	 */
	private $_item_count;

	/**
	 * APL (Internal) Shortcode Constructor
	 *
	 * Desc: Loads/Adds all the internal shortcodes to WordPress, and sets the
	 * class _post to default with Global Post.
	 *
	 * STEP 1: Load a list of (internal) shortcode tags.
	 * STEP 2: Set _post to default settings with Global $post.
	 *
	 * @since 0.4.0
	 * @access public
	 *
	 * @global WP_Post $post Used to store in $this->_post.
	 */
	public function __construct() {
		// STEP 1.
		$shortcode_list = $this->shortcode_list();
		foreach ( $shortcode_list as $tag ) {
			add_shortcode( $tag, array( $this, $tag ) );
		}

		// STEP 2.
		global $post;
		$this->_post = $post;
		$this->_item_count = 0;
	}

	/**
	 * Destruct Shortcode Remove
	 *
	 * Removes the shortcodes added from construction, and unset the class.
	 * Note: Magic Method __destruct wasn't working as intended, and would called
	 *       shortly after creating a new APL_Shortcode. Which occurs when more
	 *       than one [post_list] is used.
	 *
	 * STEP 1: Remove list of shortcodes from WordPress.
	 * STEP 2: Unset _post object.
	 *
	 * @since 0.4.0
	 * @access public
	 */
	public function remove() {
		// STEP 1.
		$shortcode_list = $this->shortcode_list();
		foreach ( $shortcode_list as $tag ) {
			remove_shortcode( $tag );
		}
		// STEP 2.
		unset( $this->_post );
	}

	/**
	 * List of shortcode tags
	 *
	 * Returns an array of shortcode tags used when public shortcodes are used.
	 *
	 * @ignore
	 * @todo Extension support for additional functionality.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @return array Internal Shortcode Tags used to add, replace, remove, etc.
	 */
	private function shortcode_list() {
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

			//LOOP SHORTCODE FUNCTIONS for $this->replace().
			'item_number',

			// executed outside of class.
			//'final_end',

			// Extensions/Hook.
			// Kalin's PDF Plugin (obsolete?).
			'post_pdf',
		);

		return $return_array;
	}

	/**
	 * Shortcode Replace
	 *
	 * Replaces APL's internal shortcodes with RegEx and WP's Shortcode API
	 * with $this->[shortcode].
	 *
	 * STEP 1: Cycle through the list of internal shortcodes.
	 * STEP 2: While there is a match, grab the first match/shortcode.
	 * STEP 3: Do shortcode, and replace content from beginning to end.
	 * STEP 4: Return (Preset Content) string.
	 *
	 * @since 0.4.0
	 *
	 * @param string $preset_content The design content for List Content.
	 * @param string $wp_post WP Post object.
	 * @return string Preset Content with shortcodes replaced.
	 */
	public function replace( $preset_content, $wp_post ) {
		// INIT.
		$return_str = $preset_content;
		$this->_post = $wp_post;

		// STEP 1.
		$shortcode_tags = $this->shortcode_list();
		foreach ( $shortcode_tags as $tag ) {
			// STEP 2.
			while ( preg_match( '#\[' . $tag . '(.*?)?\]#', $return_str, $matches_default ) ) {
				// STEP 3.
				$return_str = preg_replace(
					'#\[' . $tag . '(.*?)?\]#',
					do_shortcode( $matches_default[0] ),
					$return_str,
					1
				);
			}
		}

		$this->_item_count++;
		// STEP 4.
		return $return_str;
	}

	/**
	 * ID Shortcode
	 *
	 * Adds the Post ID.
	 *
	 * STEP 1: Add post ID to return_str.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Post->ID.
	 */
	public function ID( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array() , $atts, 'ID' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->ID;

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Name Shortcode
	 *
	 * Adds the Post Slug (post_name).
	 *
	 * STEP 1: Add to return the Post Post_Name.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in functions
	 *                for setting default attributes & do_shortcode().
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string WP_Post->Post_Name.
	 */
	public function post_name( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array() , $atts, 'post_name' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->post_name;

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Slug Shortcode
	 *
	 * Adds the Post Slug from WP_Post->post_name.
	 *
	 * STEP 1: Add to return the Post Post_Name.
	 * STEP 2: Return string.
	 *
	 * @since 0.4.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Post->post_name.
	 */
	public function post_slug( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array() , $atts, 'post_slug' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->post_name;

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Title Shortcode
	 *
	 * Adds the post/page title.
	 *
	 * STEP 1: Set Length attr to valid int value.
	 * STEP 2: Add Title is shorter than length, otherwise trim to Length.
	 * STEP 3: Convert special characters to HTML entities.
	 * STEP 4: Return string.
	 *
	 * @todo Create compatability with UNICODE. Characters not showing correctly
	 *       in Chinese.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and WP's Shortcode API.
	 *                Added attr 'length'.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Post->post_title.
	 */
	public function post_title( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'length' => '250',
		) , $atts, 'post_title' );

		// STEP 1.
		if ( is_numeric( $atts_value['length'] ) ) {
			$atts_value['length'] = intval( $atts_value['length'] );
		} else {
			$atts_value['length'] = 250;
		}

		// STEP 2.
		$title_tmp = '';
		if ( strlen( $this->_post->post_title ) <= $atts_value['length'] ) {
			$title_tmp .= $this->_post->post_title;
		} else {
			$title_tmp .= substr( $this->_post->post_title, 0, $atts_value['length'] );
			if ( ' ' !== substr( $title_tmp, -1, 1 ) ) {
				$title_tmp = substr( $title_tmp, 0, strrpos( $title_tmp, ' ' ) );
			}
		}

		// STEP 3.
		$return_str = '';
		$return_str = htmlspecialchars( $title_tmp );

		// STEP 4.
		return $return_str;
	}

	/**
	 * Post Permalink (URL) Shortcode
	 *
	 * Adds the post/page permalink/URL.
	 *
	 * STEP 1: Get & Add to return Post's Permalink.
	 * STEP 2: Return string.
	 *
	 * @todo Add alias [post_link]
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Permalink associated with Post ID.
	 */
	public function post_permalink( $atts ) {
		$atts_value = shortcode_atts( array() , $atts, 'post_permalink' );

		$return_str = '';
		$return_str .= get_permalink( $this->_post->ID );

		return $return_str;
	}

	/**
	 * Post Guid (WP Default URL) Shortcode
	 *
	 * Adds the post/page Guid. WP's Default URL (.com/?p=396).
	 *
	 * STEP 1: Add to return Post's Guid.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Post Guid.
	 */
	public function guid( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array() , $atts, 'post_guid' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->guid;

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Date Shortcode
	 *
	 * Adds the post/page date.
	 *
	 * STEP 1: Get & Add to return Post's Formatted Date.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_date( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'format' => 'm-d-Y',
		), $atts, 'post_date' );
		$return_str = '';

		// STEP 1.
		$return_str .= mysql2date( $atts_value['format'], $this->_post->post_date );

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Date GMT Shortcode
	 *
	 * Adds the post/page date.
	 *
	 * STEP 1: Get & Add to return Post's Formatted GMT Date.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_date_gmt( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'format' => 'm-d-Y',
		), $atts, 'post_date_gmt' );
		$return_str = '';

		// STEP 1.
		$return_str .= mysql2date( $atts_value['format'], $this->_post->post_date_gmt );

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Date Modified Shortcode
	 *
	 * Adds the post/page modified date.
	 *
	 * STEP 1: Get & Add to return Post's Formatted Modified Date.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_modified( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'format' => 'm-d-Y',
		), $atts, 'post_modified' );
		$return_str = '';

		// STEP 1.
		$return_str .= mysql2date( $atts_value['format'], $this->_post->post_modified );

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Date Modified GMT Shortcode
	 *
	 * Adds the post/page modified date GMT.
	 *
	 * STEP 1: Get & Add to return Post's Formatted Modified Date GMT.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_modified_gmt( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'format' => 'm-d-Y',
		), $atts, 'post_modified_gmt' );
		$return_str = '';

		// STEP 1.
		$return_str .= mysql2date( $atts_value['format'], $this->_post->post_modified_gmt );

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Author Shortcode
	 *
	 * Adds the Author/User Data associated with the post.
	 *
	 * STEP 1: Set Label Types used within WP, and extensions. User_Friendly => WP_Friendly.
	 * STEP 2: Get Author/User Data associated with WP_Post.
	 * STEP 3: Add User Label to return, IF Label is valid with APL and IF data/prop
	 *         even exist in UserData (including extension labels registered with
	 *         APL & WP).
	 * STEP 4: Otherwise, IF no variable exists, add default display_name to return.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 - Changed to Callback Function, and added 'label' attribute.
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_author( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'label' => 'display_name',
		), $atts, 'post_author' );
		$return_str = '';

		// STEP 1.
		$label_type = array(
			// Data Object (WP's Standard)
			'ID'                  => 'ID',
			'user_login'          => 'user_login',
			'user_name'           => 'user_login',
			//'user_pass'           => 'user_pass',
			'user_nicename'       => 'user_nicename',
			'display_name'        => 'display_name',
			'user_email'          => 'user_email',
			'user_url'            => 'user_url',
			// Need data format.
			//'user_registered'     => 'user_registered',
			//'user_activation_key' => 'user_activation_key',
			//'user_status'         => 'user_status',
			//(multisite only)
			//'spam'                => 'spam',
			//(multisite only)
			//'deleted'             => 'deleted',
			// TODO ADD data->user_registered for "Member Since: xx-xx-xxxx xx:xx:xx".
			// TODO ADD/FIX user_registered date format.

			/* **** Back_Compat_Keys Array (Legacy) **** */
			'user_firstname'      => 'user_firstname',
			'user_lastname'       => 'user_lastname',
			'description'         => 'user_description',
			'user_description'    => 'user_description',
			//'user_level'          => 'user_level',
			//'wp_usersettings'     => 'wp_usersettings',
			//'wp_usersettingstime' => 'wp_usersettingstime',

			// TODO ADD roles (array).
			// TODO ADD A dynamic method for displaying author data.
			// TODO ADD Extension Hook.
		);

		// STEP 2.
		$userData = get_userdata( $this->_post->post_author );

		// STEP 3.
		if ( isset( $label_type[ $atts_value['label'] ] ) &&
			 $userData->has_prop( $label_type[ $atts_value['label'] ] ) ) {
			$return_str .= $userData->get( $label_type[ $atts_value['label'] ] );
		} else {
			// STEP 4.
			// TODO ADD Admin Error.
			$return_str .= $userData->data->display_name;
		}

		// STEP 5.
		return $return_str;
	}

	/**
	 * Post Thumb Shortcode
	 *
	 * Adds a Post Thumb/Featured Image URL associated with the post, but
	 * can also grab an image within WP_Post->post_content (extract=on/force).
	 * Image Sizes include (default) thumbnail, medium, large, full,
	 * and custom "XX, XX" (closest image size available).
	 *
	 * Note: Post_Content Images are not resizable.
	 *
	 * STEP 1: If 'size' string is numeric, then convert 'size' to an array(xx, yy).
	 * STEP 2: Grab Featured Image from Post/Page w/ 'size'.
	 * STEP 3: If 'extract' is 'on' OR 'force', add to return the src URL in img tag
	 *         from $post->post_content.
	 * STEP 4: Return string.
	 *
	 * @todo Improve method for grabbing site only images; which will improve how
	 *       images are handled in WP. However, CDN has become more of a standard.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 - Added 'extract' attribute.
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *                Added Custom Size support.
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
	public function post_thumb( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'size'      => 'thumbnail',
			'extract'   => 'none',
		), $atts, 'post_thumb' );
		$return_str = '';

		// STEP 1.
		if ( is_numeric( substr( $atts_value['size'], 0, 1 ) ) && substr( '0' !== $atts_value['size'], 0, 1 ) ) {
			$atts_value['size'] = explode( ',', $atts_value['size'] );
			foreach ( $atts_value['size'] as $key => $value ) {
				$atts_value['size'][ $key ] = intval( $value );
			}
		}

		// STEP 2.
		if ( 'force' !== strtolower( $atts_value['extract'] ) && current_theme_supports( 'post-thumbnails' ) ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->_post->ID ), $atts_value['size'] );
			if ( $featured_image ) {
				$return_str .= $featured_image[0];
			}
		}

		// EXTRACT/FALLBACK IMAGE (No Featured Image).
		// STEP 3.
		if ( 'none' !== strtolower( $atts_value['extract'] ) && empty( $return_str ) ) {
			// Parse and grab src="{}".
			preg_match_all( '/src="([^"]+)"/', $this->_post->post_content, $matches );
			if ( ! empty( $matches[1] ) ) {
				// TODO ADD Offset?
				$return_str .= $matches[1][0];
			}
		}

		// STEP 4.
		return $return_str;
	}

	/**
	 * Post Content Shortcode
	 *
	 * Adds the Post_Content.
	 *
	 * STEP 1: Add to return the Post's Post_Content.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts None.
	 * @return string Post->Post_Content.
	 */
	public function post_content( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array() , $atts, 'post_content' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->post_content;

		// STEP 2.
		return $return_str;
	}

	/**
	 * Post Excerpt Shortcode
	 *
	 * Adds the Post Excerpt, or a substring of Post Content.
	 *
	 * STEP 1: Convert 'length' Variable Type to Int.
	 * STEP 2: IF Post_Excerpt is empty, then use Post_Content with X amount of
	 *         characters (Default 250.). Otherwise skip to STEP 4.
	 * STEP 3: Add a substring from Post_Content to return. X amount of length
	 *         and with shortcodes stripped out.
	 * STEP 4: Add Post_Excerpt to return.
	 * STEP 5: Return String.
	 *
	 * @since 0.1.0
	 * @version 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                  functions for setting default attributes & do_shortcode().
	 *                  Also changed substr() to mp_substr() for encoding
	 *                  compatability.
	 *
	 * @param array $atts {
	 *
	 *      Shortcode Attributes.
	 *
	 *      @type string $length    Sets the character length.
	 * }
	 * @return string Post->Excerpt OR Post->Post_Content substring.
	 */
	public function post_excerpt( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'length' => '250',
		) , $atts, 'post_excerpt');
		$return_str = '';

		// STEP 1.
		if ( is_numeric( $atts_value['length'] ) ) {
			$atts_value['length'] = intval( $atts_value['length'] );
		} else {
			$atts_value['length'] = 250;
		}

		// STEP 2.
		if ( empty( $this->_post->post_excerpt ) ) {
			// BUG? Possible if length is longer than string.
			// STEP 3.
			$encoding = mb_internal_encoding();
			$return_str = strip_shortcodes( mb_substr(
				strip_tags( $this->_post->post_content ),
				0,
				$atts_value['length'],
				$encoding
			));
			if ( ' ' !== substr( $return_str, -1, 1 ) ) {
				$return_str = substr( $return_str, 0, strrpos( $return_str, ' ' ) );
			}
		} else {
			// STEP 4.
			if ( strlen( $this->_post->post_excerpt ) <= $atts_value['length'] ) {
				$return_str .= $this->_post->post_excerpt;
			} else {
				$return_str .= substr( $this->_post->post_excerpt, 0, $atts_value['length'] );
				if ( ' ' !== substr( $return_str, -1, 1 ) ) {
					$return_str = substr( $return_str, 0, strrpos( $return_str, ' ' ) );
				}
			}
		}

		// STEP 5.
		return $return_str;
	}

	/**
	 * Post Comment Count Shortcode
	 *
	 * Adds the Post Comment_Count, which displays the amount of comments
	 * per post.
	 *
	 * STEP 1: Add to return Post Comment_Count.
	 * STEP 2: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts None.
	 * @return string Post->Comment_Count.
	 */
	public function comment_count( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(), $atts, 'comment_count' );
		$return_str = '';

		// STEP 1.
		$return_str .= $this->_post->comment_count;

		// STEP 2.
		return $return_str;
	}

	// TODO ADD Max Amount
	// TODO ADD Sort (OrderBy & Order)
	// TODO ADD Include/Exclude (Author?) User_ID Filter
	// TODO ADD Date Filter
	// TODO ADD Preset Design. Default would use this, but a custom comment shortcode
	//          would have its own design.
	/**
	 * Post Comment Shortcode
	 *
	 * Adds an HTML string to display comments in a set format.
	 *
	 * STEP 1: Get Kalin's PDF comments. Support old method.
	 * STEP 2: Get 'approved' comments from Post ID.
	 * STEP 3: Add to return the Before Attribute.
	 * STEP 4: For each Post_Comments, add formatted string to return, w/ author link
	 *         if available.
	 * STEP 5: Add to return the After Attribute.
	 * STEP 6: Return string.
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
	 *      @type string $before Adds a string before the list.
	 *      @type string $after  Adds a string after the list.
	 * }
	 * @return string Post Excerpt OR formatted Post_Content.
	 */
	public function post_comments( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'before' => '',
			'after' => '',
		), $atts, 'post_comments' );
		$return_str = '';

		// STEP 1.
		if ( defined( 'KALINS_PDF_COMMENT_CALLBACK' ) ) {
			return call_user_func( KALINS_PDF_COMMENT_CALLBACK );
		}

		// STEP 2.
		$args = array(
			'status' => 'approve',
			'post_id' => $this->_post->ID,
		);
		$post_comments = get_comments( $args );

		// STEP 3.
		$return_str .= $atts_value['before'];

		// STEP 4.
		foreach ( $post_comments as $comment ) {
			if ( '' === $comment->comment_author_url ) {
				$comment_author = $comment->comment_author;
			} else {
				$comment_author = '<a href="' . $comment->comment_author_url . '" >' . $comment->comment_author . '</a>';
			}

			$return_str .= '<p>' . $comment_author .
						   ' - ' . $comment->comment_author_email .
						   ' - ' . get_comment_date( null, $comment->comment_ID ) .
						   ' @ ' . get_comment_date( get_option( 'time_format' ), $comment->comment_ID ) .
						   '<br />' . $comment->comment_content . '</p>';
		}

		// STEP 5.
		$return_str .= $atts_value['after'];

		// STEP 6.
		return $return_str;
	}

	/**
	 * Page Parent Shortcode
	 *
	 * Adds Page Parent Title, and optionally with link.
	 *
	 * STEP 1: Check to see if Post Parent is set.
	 * STEP 2: Grab Post Parent Title w/ Link if set to 'true'.
	 * STEP 3: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts {
	 *
	 *      Shortcode Attributes.
	 *
	 *      @type string $link      Returns as html link if true.
	 * }
	 * @return string Post Parent Title OR as html link.
	 */
	public function post_parent( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'link' => 'true',
		), $atts, 'post_parent' );
		$return_str = '';

		// STEP 1.
		if ( 0 !== $this->_post->post_parent ) {
			// STEP 2.
			if ( 'false' === strtolower( $atts_value['link'] ) ) {
				$return_str .= get_the_title( $this->_post->post_parent );
			} else {
				$return_str .= '<a href="' . get_permalink( $this->_post->post_parent ) . '" >' . get_the_title( $this->_post->post_parent ) . '</a>';
			}
		}

		// STEP 3.
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

	// TODO ADD Array String to filter through like this->post_author.
	/**
	 * Post Type Shortcode
	 *
	 * Adds the Post Type (Label) Name associated with the post/page ID.
	 *
	 * STEP 1: Get Post Type Object associated with the post.
	 * STEP 2: Get the set label from object, otherwise get name.
	 * STEP 3: Return string.
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
	public function post_type( $atts ) {
		$atts_value = shortcode_atts( array(
			'label' => 'name',
		), $atts, 'post_type' );
		$return_str = '';

		// STEP 1.
		$post_type_obj = get_post_type_object( $this->_post->post_type );

		// STEP 2.
		$label = $atts_value['label'];
		if ( isset( $post_type_obj->labels->$label ) ) {
			$return_str .= $post_type_obj->labels->$label;
		} elseif ( isset( $post_type_obj->labels->name ) ) {
			$return_str .= $post_type_obj->labels->name;
		}

		// STEP 3.
		return $return_str;
	}

	/**
	 * Categories Shortcode
	 *
	 * Adds Categories associated with Post/Page.
	 *
	 * STEP 1: Set Attribute 'Links' to a Boolean variable.
	 * STEP 2: Get the categories. If none, do Step 4.
	 * STEP 3: For each category there is add the category name, w/ link if true,
	 *         and add a delimiter except for last category.
	 * STEP 4: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_categories( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'delimiter' => ', ',
			'links'     => 'true',
		), $atts, 'post_categories');
		$return_str = '';

		// STEP 1.
		$links = true;
		if ( 'false' === strtolower( $atts_value['links'] ) ) {
			$links = false;
		}

		// STEP 2.
		$post_categories = get_the_category( $this->_post->ID );
		$array_total = count( $post_categories );
		$i = 1;
		if ( $post_categories ) {
			// STEP 3.
			foreach ( $post_categories as $category ) {
				if ( $links ) {
					$return_str .= '<a href="' . get_tag_link( $category->term_id ) . '" >' . $category->name . '</a>';
				} else {
					$return_str .= $category->name;
				}

				if ( $array_total > $i ) {
					$return_str .= $atts_value['delimiter'];
				}
				$i++;
			}
		}

		// STEP 4.
		return $return_str;
	}

	/**
	 * Tags Shortcode
	 *
	 * Adds Tags associated with Post/Page.
	 *
	 * STEP 1: Set Attribute 'Links' to a Boolean variable.
	 * STEP 2: Get the tags. If none, do Step 4.
	 * STEP 3: For each tag there is add the tag name, w/ link if true, and add a
	 *         delimiter except for last tag.
	 * STEP 4: Return string.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function post_tags( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'delimiter' => ', ',
			'links' => 'true',
		), $atts, 'post_tags');
		$return_str = '';

		// STEP 1.
		$links = true;
		if ( 'false' === strtolower( $atts_value['links'] ) ) {
			$links = false;
		}

		// STEP 2.
		$post_tags = get_the_tags( $this->_post->ID );
		$array_total = count( $post_tags );
		$i = 1;
		if ( $post_tags ) {
			// STEP 3.
			foreach ( $post_tags as $tag ) {
				if ( $links ) {
					$return_str .= '<a href="' . get_tag_link( $tag->term_id ) . '" >' . $tag->name . '</a>';
				} else {
					$return_str .= $tag->name;
				}

				if ( $array_total > $i ) {
					$return_str .= $atts_value['delimiter'];
				}
				$i++;
			}
		}

		// STEP 4.
		return $return_str;
	}

	/**
	 * Post Terms Shortcode
	 *
	 * Adds (Custom) Taxonomy Terms associated with Post/Page. Displays both
	 * post and page types, and will display terms from taxonomies that are set
	 * to public.
	 *
	 * STEP 1: Get Taxonomy Terms if Taxonomy is valid, otherwise get (default) Categories.
	 * STEP 2: Convert $atts['links'] to a boolean variable.
	 * STEP 3: If terms exists format them to return, otherwise add an empty message
	 *         and do Step 6.
	 * STEP 4: Slice list of terms to max amount, and store array total and (i)ndex.
	 * STEP 5: For each term there is add the term name, w/ link if true, and add a
		       delimiter except for last term.
	 * STEP 6: Return string.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts {
	 *
	 *      Shortcode Attributes.
	 *
	 *      @type string $taxonomy          [Req] Gets the terms from taxonomy,
	 *                                      defaults to 'category'.
	 *      @type string $delimiter         Inserts a separator, default is ", ".
	 *      @type boolean $links            Return as an HTML link if true.
	 *      @type integer $max              Total amount to display.
	 *      @type string $empty_message     Display a message if no terms are returned.
	 * }
	 * @return string Taxonomy Terms used in post/page.
	 */
	public function post_terms( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'taxonomy'      => 'category',
			'delimiter'     => ', ',
			'links'         => 'true',
			'max'           => '0',
			'empty_message' => '',
		), $atts, 'post_terms' );
		$return_str = '';

		// STEP 1.
		if ( ! taxonomy_exists( $atts_value['taxonomy'] ) ) {
			$atts_value['taxonomy'] = 'category';
		}
		$terms = get_the_terms( $this->_post->ID, $atts_value['taxonomy'] );

		// STEP 2.
		$links = true;
		if ( 'false' === strtolower( $atts_value['links'] ) ) {
			$links = false;
		}

		// STEP 3.
		if ( $terms ) {
			// TEP 4.
			$i = 1;
			$array_total = count( $terms );
			if ( '0' !== $atts_value['max'] ) {
				if ( $array_total > intval( $atts_value['max'] ) ) {
					$terms = array_slice( $terms, 0, intval( $atts_value['max'] ) );
					$array_total = count( $terms );
				}
			}
			// STEP 5.
			foreach ( $terms as $term_key => $term ) {
				if ( $links ) {
					$return_str .= '<a href="' . get_tag_link( $term->term_id ) . '" >' . $term->name . '</a>';
				} else {
					$return_str .= $term->name;
				}

				if ( $array_total > $i ) {
					$return_str .= $atts_value['delimiter'];
				}

				$i++;
			}
		} else {
			$return_str .= $atts_value['empty_message'];
		}

		// STEP 6.
		return $return_str;
	}

	/**
	 * Post Meta Shortcode
	 *
	 * Adds Post MetaData used within posts/pages. Returns empty if nothing
	 * is found.
	 *
	 * STEP 1: If post_meta 'name' is valid then get MetaData and add to return,
	 *         otherwise skip to Step 2.
	 * STEP 2: Return string.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $atts {
	 *
	 *      Shortcode Attributes.
	 *
	 *      @type string $name      Meta Name/Label used within post.
	 * }
	 * @return string Taxonomy Terms used in post/page.
	 */
	public function post_meta( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'name' => '',
		), $atts, 'post_meta' );
		$return_str = '';

		// STEP 1.
		if ( ! empty( $atts_value['name'] ) && metadata_exists( 'post', $this->_post->ID, $atts_value['name'] ) ) {
			$post_meta_arr = get_post_meta( $this->_post->ID, $atts_value['name'], false );
			foreach ( $post_meta_arr as $meta ) {
				$return_str .= $meta;
			}
		}
		// TODO ADD Else Alert to Admin that metadata is invalid or doesn't exist.

		// STEP 2.
		return $return_str;
	}

	/**
	 * PHP Function Shortcode
	 *
	 * Adds custom php functions within the environment/instance, and displays
	 * the returned data as a string.
	 *
	 * STEP 1: Check to see if Constant Variable is set, if not, add error then do Step 4.
	 * STEP 2: If the function exists, do the function /w 'param'.
	 * STEP 3: Return string.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in functions
	 *                for setting default attributes & do_shortcode().
	 *                Added Constant APL_ALLOW_PHP to require.
	 *                Added Check if function exists.
	 *                Added Else Check for unknown error.
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
	public function php_function( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'name' => '',
			'param' => '',
		), $atts, 'php_function');
		$return_str = '';

		// STEP 1.
		if ( ! defined( 'KALINS_ALLOW_PHP' ) && ! defined( 'APL_ALLOW_PHP' ) ) {
			$return_str  .= __( 'Error: Add define("APL_ALLOW_PHP", true); to wp-config.php for php_function to work.', 'advanced-post-list' );
		} elseif ( true !== APL_ALLOW_PHP || true !== APL_ALLOW_PHP ) {
			$return_str  .= __( 'Error: Change define("APL_ALLOW_PHP", true); in ', 'advanced-post-list' ) .
							__( 'wp-config.php for php_function to work.', 'advanced-post-list' );
		} elseif ( empty( $atts_value['name'] ) ) {
			$return_str  .= __( 'Error: Name shortcode attribute must have a name. ', 'advanced-post-list' );
		} elseif ( ! function_exists( $atts_value['name'] ) ) {
			$return_str  .= __( 'Error: Function does not exist. Check name in shortcode or is function name is loaded.', 'advanced-post-list');
							//__( 'For ex. ', 'advanced-post-list' ) . '\[php_function name=\"' . __( 'FUNCTION_NAME', 'advanced-post-list' ) . '\"\]';
							//__( 'For ex. &#91;php_function name="FUNCTION_NAME"&#93;', 'advanced-post-list' );
		} elseif ( function_exists( $atts_value['name'] ) ) {
			// STEP 2.
			if ( ! empty( $atts_value['param'] ) ) {
				$return_str .= call_user_func(
					$atts_value['name'],
					$this->_post,
					$atts_value['param']
				);
			} else {
				$return_str .= call_user_func(
					$atts_value['name'],
					$this->_post
				);
			}
		} else {
			$return_str  .= __( 'Error: Unknown Error.', 'advanced-post-list');
			$return_str  .= '<br />';
			$return_str  .= __( 'defined APL_ALLOW_PHP: ', 'advanced-post-list' ) . ! defined( 'APL_ALLOW_PHP' );
			$return_str  .= '<br />';
			$return_str  .= __( '$atts name:', 'advanced-post-list' ) . $atts_value['name'];
			$return_str  .= '<br />';
			$return_str  .= __( '$atts param:', 'advanced-post-list' ) . $atts_value['param'];
			$return_str  .= '<hr />';
		}

		// STEP 3.
		return $return_str;
	}

	/**
	 * Post List Item Number Shortcode
	 *
	 * Adds a Numeric value to each post; by X amount of increments, and
	 * starting from where it is offset as.
	 *
	 * STEP 1: If any non-digits are present, reset to default.
	 * STEP 2: Convert variables to integer types.
	 * STEP 3: Add item number with increment and added offset.
	 * STEP 4: Return string.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
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
	public function item_number( $atts ) {
		// INIT.
		$atts_value = shortcode_atts( array(
			'offset' => '1',
			'increment' => '1',
		), $atts, 'item_number' );
		$return_str = '';

		// STEP 1.
		if ( ! is_numeric( $atts_value['increment'] ) ) {
			$atts_value['increment'] = '1';
		}
		if ( ! is_numeric( $atts_value['offset'] ) ) {
			$atts_value['offset'] = '1';
		}

		// STEP 2.
		$atts_value['increment'] = intval( $atts_value['increment'] );
		$atts_value['offset'] = intval( $atts_value['offset'] );

		// STEP 3.
		$return_str .= (string) ( ( $this->_item_count * $atts_value['increment'] ) + $atts_value['offset'] );

		// STEP 4.
		return $return_str;
	}

	/**
	 * Final End Post List Shortcode
	 *
	 * Determines the End of the Final post to display.
	 *
	 * STEP 1: Get everything except everything after the last Final_End.
	 * STEP 2: Strip all Final_End shortcodes.
	 * STEP 3: Return string.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *
	 * @param array $content Preset Post List Content to be displayed
	 * @return string Preset ( Post List ) Content.
	 */
	public function final_end( $content ) {
		// INIT.
		$return_str = '';

		// STEP 1.
		$return_str .= substr( $content, 0, strrpos( $content, '[final_end]' ) );

		// STEP 2.
		$return_str = str_replace( '[final_end]', '', $return_str );

		// STEP 3.
		return $return_str;
	}

	/**
	 * Kalin's Post PDF Shortcode
	 *
	 * Shortcode for displaying Kalin's Post PDF Plugin.
	 *
	 * @since 0.3.0
	 * @since 0.4.0 - Changed to Class function, and uses WP's built-in
	 *                functions for setting default attributes & do_shortcode().
	 *                Added check if plugin is active.
	 *
	 * @param array $atts {
	 *
	 *      Shortcode Attributes.
	 *
	 *      @type integer $content      Preset Content that will be displayed.
	 * }
	 * @return string Preset (Post List) Content.
	 */
	public function post_pdf( $atts ) {
		$att_value = shortcode_atts( array(), $atts, 'post_pdf' );
		$return_str = '';

		if ( is_plugin_active( 'kalins-pdf-creation-station' ) ) {
			if ( 'post' === $this->_post->post_type ) {
				$postID = 'po_' . $this->post_id;
			} elseif ( 'page' === $this->_post->post_type ) {
				$postID = 'pg_' . $this->post_id;
			}
			$return_str .= get_bloginfo( 'wpurl' ) . '/wp-content/plugins/kalins-pdf-creation-station/kalins_pdf_create.php?singlepost=' . $postID;
		}

		return $return_str;
	}
}
