<?php

function APL_import()
{
    check_ajax_referer("APL_import");
    
    $TMPpresetDbObj = get_option('APL_TMP_import_presetDbObj');
    $presetDbObj = new APLPresetDbObj('default');
    $overwrite_list = explode(',', $_GET['overwrite']);
    
    foreach ($TMPpresetDbObj->_preset_db as $tmp_preset_name => $tmp_preset_value)
    {
        //ADD MISSING
        if (!isset($presetDbObj->_preset_db->$tmp_preset_name))
        {
            $presetDbObj->_preset_db->$tmp_preset_name = $tmp_preset_value;
        }
        //ADD TO CONFIRM OVERWRITE LIST {OBJECT}
        else
        {
            foreach ($overwrite_list as $value)
            {
                if ($tmp_preset_name == $value)
                {
                    $presetDbObj->_preset_db->$tmp_preset_name = $tmp_preset_value;
                    break;
                }
            }
        }
    }
    $presetDbObj->options_save_db;
    delete_option('APL_TMP_import_presetDbObj');
}
?>
