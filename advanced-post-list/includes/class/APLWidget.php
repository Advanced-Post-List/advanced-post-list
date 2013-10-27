<?php
//// REFERENCE/CODEX - http://codex.wordpress.org/Widgets_API
class APLWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct('advanced-post-list_default',
                            __('Advanced Post Lists', 'APL-text_domain'),
                            array('description' => __('Display preset post lists', 'APL-text_domain')));
    }

    // This code displays the widget on the screen.
    function widget($args, $instance)
    {
        global $advanced_post_list;
        extract($args);
        
        echo $before_widget;
        if (!empty($instance['title']))
        {
            echo $before_title . $instance['title'] . $after_title;
        }

        echo $advanced_post_list->APL_display($instance['apl_preset']);

        echo $after_widget;
    }

    function form($instance)
    {
        $APLPresetDbObj = new APLPresetDbObj('default');

        echo '<div>';
        echo '<label for="' . $this->get_field_id("title") . '">Title:</label>';
        echo '<input type="text" class="widefat" ';
        echo 'name="' . $this->get_field_name("title") . '" ';
        echo 'id="' . $this->get_field_id("title") . '" ';
        if (isset($instance["title"]))
        {
            echo 'value="' . $instance["title"] . '"';
        }
        echo '/><br/><br/>';
        echo '<label for="' . $this->get_field_id("apl_preset") . '">Preset Name:</ label>';
        echo '<select class="widefat" ';
        echo 'name="' . $this->get_field_name("apl_preset") . '" ';
        echo 'id="' . $this->get_field_id("apl_preset") . '" >';
        foreach ($APLPresetDbObj->_preset_db as $key => $value)
        {
            if (isset($instance['apl_preset']) && $key == $instance['apl_preset'])
            {
                echo '<option value="' . $key . '" selected="yes" >' . $key . '</ option>';
            }
            else
            {
                echo '<option value="' . $key . '">' . $key . '</ option>';
            }
        }
        echo '</select><br/><br/></div>';
    }// end function form
    
    // Updates the settings.
    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

}

?>
