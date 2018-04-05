<?php

if ( 'apl_get_display_post_types' ) {
	/**
	 * Get Post Types to Display.
	 *
	 * Displays a *valid* list of post types that also aren't on the global ignore list.
	 *
	 * @since 0.4.0
	 * @access private
	 *
	 * @see $this->_ignore_post_types.
	 *
	 * @return array List of Post Types.
	 */
	function apl_get_display_post_types() {
		$rtn_post_types = array();

		$options = apl_options_load();

		$ignore_post_types = apl_default_ignore_post_types();
		$ignore_post_types = apply_filters( 'apl_display_post_types_ignore', $ignore_post_types );
		$ignore_post_types = wp_parse_args( $ignore_post_types, $options['ignore_post_types'] );

		// Get all Post Types.
		$post_type_objs = get_post_types( '', 'objects' );
		// Remove ignored Post Types.
		foreach ( $ignore_post_types as $value ) {
			unset( $post_type_objs[ $value ] );
		}

		foreach ( $post_type_objs as $key => $value ) {
			$rtn_post_types[ $key ] = $value->labels->singular_name;
		}

		return $rtn_post_types;
	}
}

if ( 'apl_get_post_tax' ) {
	/**
	 * Get Post Type & Taxonomies
	 *
	 * Gets and returns an array of Post_Types => Taxonomies.
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @return array Post_Type = > Name, Taxonomy Array.
	 */
	function apl_get_post_tax() {
		$rtn_post_tax = array();

		$post_types = apl_get_display_post_types();

		// Add to rtn {post_type} => {array( taxonomies )}.
		$rtn_post_tax['any']['name'] = __( 'Any / All', 'advanced-post-list' );
		$taxonomy_names = get_taxonomies( '', 'names' );
		foreach ( $taxonomy_names as $name ) {
			$rtn_post_tax['any']['tax_arr'][] = $name;
		}

		foreach ( $post_types as $k_slug => $v_name ) {
			$rtn_post_tax[ $k_slug ]['name'] = $v_name;
			$rtn_post_tax[ $k_slug ]['tax_arr'] = get_object_taxonomies( $k_slug, 'names' );
		}

		// Return Post_Tax.
		return $rtn_post_tax;
	}
}

if ( 'apl_get_tax_terms' ) {
	/**
	 * Get Taxonomies & Terms
	 *
	 * Gets and returns an array of Taxonomies => Terms.
	 *
	 * @see get_terms()
	 * @link https://developer.wordpress.org/reference/functions/get_terms/
	 *
	 * @ignore
	 * @since 0.4.0
	 * @access private
	 *
	 * @return array Taxonomy => Term.
	 */
	function apl_get_tax_terms() {
		$rtn_tax_terms = array();

		// Get Taxonomy Names.
		$taxonomy_names = get_taxonomies( '', 'names' );

		// Loop foreach taxonomy. Get terms, and foreach term add to taxonomy.
		foreach ( $taxonomy_names as $taxonomy ) {
			$args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			);
			$terms = get_terms( $args );

			// Set slug.
			$rtn_tax_terms[ $taxonomy ] = array();
			foreach ( $terms as $term ) {
				$rtn_tax_terms[ $taxonomy ][] = $term->term_id;
			}
		}

		// Return Tax_Terms.
		return $rtn_tax_terms;
	}
}
