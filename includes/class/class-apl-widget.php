<?php
/**
 * APL Widget Child Class
 *
 * Updater object to Advanced Post List
 *
 * @link https://github.com/EkoJr/advanced-post-list/
 *
 * @package advanced-post-list
 * @since 0.3.0
 */

/**
 * APL Widget
 *
 * Updates APL's database.
 *
 * @link http://codex.wordpress.org/Widgets_API
 *
 * @since 0.3.0
 */
class APL_Widget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 0.3.0
	 */
	public function __construct() {
		parent::__construct(
			'advanced-post-list_default',
			__( 'Advanced Post Lists', 'advanced-post-list' ),
			array( 'description' => __( 'Display preset post lists', 'advanced-post-list' ) )
		);
	}

	/**
	 * Widget
	 *
	 * This code displays the widget on the screen.
	 *
	 * @since 0.3.0
	 *
	 * @global  APL_Core  $advanced_post_list
	 * @param   array     $args
	 * @param   ?         $instance
	 */
	public function widget( $args, $instance ) {
		global $advanced_post_list;
		extract( $args );

		echo $before_widget;
		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		echo $advanced_post_list->display_post_list( $instance['apl_preset'] );

		echo $after_widget;
	}

	/**
	 * Form
	 *
	 * @since 0.3.0
	 *
	 * @param type $instance
	 */
	public function form( $instance ) {
		$preset_db = new APL_Preset_Db( 'default' );

		echo '<div>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">Title:</label>';
		echo '<input type="text" class="widefat" ';
		echo 'name="' . $this->get_field_name( 'title' ) . '" ';
		echo 'id="' . $this->get_field_id( 'title' ) . '" ';
		if ( isset( $instance['title'] ) ) {
			echo 'value="' . $instance['title'] . '"';
		}
		echo '/><br/><br/>';
		echo '<label for="' . $this->get_field_id( 'apl_preset' ) . '">Preset Name:</ label>';
		echo '<select class="widefat" ';
		echo 'name="' . $this->get_field_name( 'apl_preset' ) . '" ';
		echo 'id="' . $this->get_field_id( 'apl_preset' ) . '" >';
		foreach ( $preset_db->_preset_db as $key => $value ) {
			if ( isset( $instance['apl_preset'] ) && $key === $instance['apl_preset'] ) {
				echo '<option value="' . $key . '" selected="yes" >' . $key . '</ option>';
			} else {
				echo '<option value="' . $key . '">' . $key . '</ option>';
			}
		}
		echo '</select><br/><br/></div>';
	}

	/**
	 * Update
	 *
	 * Updates the settings.
	 *
	 * @since 0.3.0
	 *
	 * @param type $new_instance
	 * @param type $old_instance
	 * @return type
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
