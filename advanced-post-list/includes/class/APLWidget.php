<?php
class APLWidget extends WP_Widget
{
  function __construct()
  {
//    $widget_ops = array('classname' => 'widget_KalinsPostList', 'description' => __("Display a customized list of posts or pages"));
//    $this->WP_Widget('kalinsPostList', __("Kalin's Post List"), $widget_ops);
    $widget_ops = array('classname' => 'widget_APL', 'description' => __("Display a customized list of posts or pages"));
    $this->WP_Widget('advancedPostList', __("Advanced Post List"), $widget_ops);    
  }

  // This code displays the widget on the screen.
  function widget($args, $instance)
  {
    extract($args);
    echo $before_widget;
    if (!empty($instance['title']))
    {
      echo $before_title . $instance['title'] . $after_title;
    }

	//Old function for displaying post lists? It's...
	//APLCore::APL_display($preset_name);
    kalinsPost_show($instance['k_preset']);

    echo $after_widget;
  }

  // Updates the settings.
  function update($new_instance, $old_instance)
  {
    return $new_instance;
  }

  function form($instance)
  {
    
  	$APLPresetDbObj = new APLPresetDbObj('default');

    echo '<div>';
    echo '<label for="' . $this->get_field_id("title") . '">Title:</label>';
    echo '<input type="text" class="widefat" ';
    echo 'name="' . $this->get_field_name("title") . '" ';
    echo 'id="' . $this->get_field_id("title") . '" ';
    if(isset($instance["title"])){
    	echo 'value="' . $instance["title"] . '"' ;
    }
    echo '/><br/><br/>';
    
    echo '<label for="' . $this->get_field_id("k_preset") . '">Preset Name:</ label>';
    echo '<select class="widefat" ';
    echo 'name="' . $this->get_field_name("k_preset") . '" ';
    echo 'id="' . $this->get_field_id("k_preset") . '" >';

    //$selectVal = $instance['k_preset'];

    foreach ($APLPresetDbObj->_preset_db as $key => $value)
    {
      if (isset($instance['k_preset']) && $key == $instance['k_preset'])
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


}
?>
