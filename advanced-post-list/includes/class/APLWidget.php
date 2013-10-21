<?php
class APLWidget extends WP_Widget
{
  function __construct()
  {
    
    //STEP 1
    //init
    $id_base        = 'advanced-post-list_default';
    $name           = __('Advanced Post Lists', 'APL-text_domain');
    $widget_options = array('description' => __( 'Display preset post lists', 'APL-text_domain' ));
    //$control_options = array();
    
    //STEP 2
    //Use Parent/WP_Widget's constructor
    parent::__construct($id_base, $name, $widget_options);
       
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
