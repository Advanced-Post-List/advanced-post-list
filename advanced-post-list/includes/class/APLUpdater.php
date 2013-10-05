<?php

class APLUpdater
{

    public $options;
    public $presetDbObj;
    
    
    public function __construct($old_version, $APLPresetDbObj = null, $APLOptions = null)
    {
        if (empty($old_version) || (empty($APLPresetDbObj) && empty($APLOptions)))
        {
            echo 'APL Updater Class Error: empty version and/or empty APL Options & APL Preset Db is being passed to the Updater Class.';
            return;
        }
        //INIT
        $this->options = array();
        $this->presetDbObj = new APLPresetDbObj();
        
        //FILL IN VARIABLES
        if (!empty($APLOptions))
        {
            $this->options = $APLOptions;
        }
        if (!empty($APLPresetDbObj))
        {
            $this->presetDbObj = $APLPresetDbObj;
        }
        //////////////////
        //// UPGRADES ////
        //CONVERT FROM KALIN'S POST LIST TO BASE
        if ($old_version == 'kalin')
        {
            $this->APL_convert_kalin_to_base();
            $old_version = '0.1.0';
        }
        //UPGRADE FROM BASE TO 0.3.X
        if (version_compare('0.3.a1', $old_version, '>'))
        {
            $this->APL_upgrade_to_03a1();
        }

        if (version_compare('0.3.b5', $old_version, '>'))
        {
            $this->APL_upgrade_to_03b5();
        }
        
        ////////////////////
        //// DOWNGRADES ////
        //DOWNGRADE FROM 0.3.X TO BASE
//        if (version_compare('0.3.b5', $oldversion, '<'))
//        {
//            $this->APL_downgrade_from_03b5();
//        }
//        if (version_compare('0.3.a1', $oldversion, '<'))
//        {
//            $this->APL_downgrade_from_03a();
//        }
        
        ////// UPDATE VERSION NUMBER //////
        //APL_VERSION - equals the file version located in advanced-post-list.php
        if (isset($this->options['version']) && version_compare(APL_VERSION, $old_version, '>'))
        {
            $this->options['version'] = APL_VERSION;
        }
        
    }
    private function APL_convert_kalin_to_base()
    {
        $tmp_preset_array = json_decode($this->presetDbObj['preset_arr']);
        
        $this->presetDbObj = new APLPresetDbObj();
        
        foreach ($tmp_preset_array as $key => $value)
        {
            $this->presetDbObj->_preset_db->$key = new APLPresetObj();
            $this->presetDbObj->_preset_db->$key->reset_to_version('0.1.0');
            
            $this->presetDbObj->_preset_db->$key = $this->APL_convert_preset_kalin_to_base($value);
        }
    }
    private function APL_convert_preset_kalin_to_base($old_presetObj)
    {
        $rtnPresetObj = new APLPresetObj();
        $rtnPresetObj->reset_to_version('0.1.0');
        
        $rtnPresetObj->_catsSelected            = $old_presetObj->categories;
        $rtnPresetObj->_tagsSelected            = $old_presetObj->tags;
        $rtnPresetObj->_postType                = $old_presetObj->post_type;
        $rtnPresetObj->_listOrderBy             = $old_presetObj->orderby;
        $rtnPresetObj->_listOrder               = $old_presetObj->order;
        $rtnPresetObj->_listAmount              = $old_presetObj->numberposts;
        $rtnPresetObj->_before                  = $old_presetObj->before;
        $rtnPresetObj->_content                 = $old_presetObj->content;
        $rtnPresetObj->_after                   = $old_presetObj->after;
        $rtnPresetObj->_postExcludeCurrent      = $old_presetObj->excludeCurrent;
        $rtnPresetObj->_catsInclude             = $old_presetObj->includeCats;
        $rtnPresetObj->_tagsInclude             = $old_presetObj->includeTags;
        
        if (isset($old_presetObj->post_parent))
        {
            $rtnPresetObj->_postParent          = $old_presetObj->post_parent;
        }
        
        if (isset($old_presetObj->requireAllCats))
        {
            $rtnPresetObj->_catsRequired        = $old_presetObj->requireAllCats;
        }
        if (isset($old_presetObj->requireAllTags))
        {
            $rtnPresetObj->_tagsRequired        = $old_presetObj->requireAllTags;
        }
        
        return $rtnPresetObj;
    }
    
    
    private function APL_upgrade_to_03a1()
    {
        if (!empty($this->options))
        {
            $this->options = $this->APL_upgrade_options_base_to_03a1($this->options);
        }
        if (!empty($this->presetDbObj))
        {
            $this->presetDbObj = $this->APL_upgrade_presetDbObj_base_to_03a1($this->presetDbObj);
        }
    }
    private function APL_upgrade_options_base_to_03a1($old_options)
    {
        //Init Defaults
        $rtnOptions = array();
        $rtnOptions['version']          = '0.3.a1';
        $rtnOptions['preset_db_names']  = array(0 => 'default');
        $rtnOptions['delete_core_db']   = true;
        $rtnOptions['error']            = '';
        //////// UPDATE/ADD ADMIN OPTIONS ////////
        $rtnOptions['jquery_ui_theme']  = 'overcast';
        
        foreach ($rtnOptions as $key => &$value)
        {
            if (!empty($old_options[$key]))
            {
                $value = $old_options[$key];
            }
        }
        
        return $rtnOptions;
    }
    private function APL_upgrade_presetDbObj_base_to_03a1($old_presetObj)
    {
        
        $rtnPresetDbObj = new APLPresetDbObj();
        $rtnPresetDbObj->reset_to_version('0.3.a1');
        
        foreach ($rtnPresetDbObj as $key1 => $value1)
        {
            if ($key1 == '_preset_db' && !empty($old_presetObj->$key1))
            {
                foreach($old_presetObj->_preset_db as $key2 => $value2)
                {
                    $rtnPresetDbObj->_preset_db->$key2 = $this->APL_upgrade_preset_base_to_03a1($value2);
                }
            }
            else if(!empty($old_presetObj->$key))
            {
                $rtnPresetDbObj->$key1 = $old_presetObj->$key1;
            }
            
        }
        return $rtnPresetDbObj;
        
    }
    private function APL_upgrade_preset_base_to_03a1($old_presetObj)
    {
        
        $rtnPresetObj = new APLPresetObj();
        $rtnPresetObj->reset_to_version('0.3.a1');
        
        // Step 4
        //// SET PARENT SETTING ////
        if ($old_presetObj->_postParent === 'current')
        {
            $rtnPresetObj->_postParent[0] = "-1";
        }
        else if ($old_presetObj->_postParent !== 'None' && $old_presetObj->_postParent !== '')
        {
            $rtnPresetObj->_postParent[0] = $old_presetObj->_postParent;
        }
        
        // Step 5
        //// SET POST TYPES & TAXONOMIES SETTINGS ////
        if ($old_presetObj->_catsSelected !== '')
        {

            $rtnPresetObj->_postTax->post->taxonomies->category->require_taxonomy = false; //NEW
            $rtnPresetObj->_postTax->post->taxonomies->category->require_terms = true;
            if ($old_presetObj->_catsRequired === 'false')
            {
                $rtnPresetObj->_postTax->post->taxonomies->category->require_terms = false;
            }
            $rtnPresetObj->_postTax->post->taxonomies->category->include_terms = true;
            if ($old_presetObj->_catsInclude === 'false')
            {
                $rtnPresetObj->_postTax->post->taxonomies->category->include_terms = false;
            }
            $terms = explode(',', $old_presetObj->_catsSelected);
            $i = 0;
            foreach ($terms as $term)
            {
                $rtnPresetObj->_postTax->post->taxonomies->category->terms[$i] = intval($term);
                $i++;
            }
            unset($rtnPresetObj->_postTax->post->taxonomies->category->terms[($i - 1)]);
        }
        
        if ($old_presetObj->_tagsSelected !== '')
        {

            $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_taxonomy = false; //NEW
            $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_terms = true;
            if ($old_presetObj->_tagsRequired === 'false')
            {
                $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_terms = false;
            }
            $rtnPresetObj->_postTax->post->taxonomies->post_tag->include_terms = true;
            if ($old_presetObj->_tagsInclude === 'false')
            {
                $rtnPresetObj->_postTax->post->taxonomies->post_tag->include_terms = false;
            }
            $terms = explode(',', $old_presetObj->_tagsSelected);
            $i = 0;
            foreach ($terms as $term)
            {
                $rtnPresetObj->_postTax->post->taxonomies->post_tag->terms[$i] = intval($term);
                $i++;
            }
            unset($rtnPresetObj->_postTax->post->taxonomies->post_tag->terms[($i - 1)]);
        }
        
        // Step 6
        //// SET THE LIST AMOUNT ////
        $rtnPresetObj->_listAmount = intval($old_presetObj->_listAmount);

        //// SET THE ORDER AND ORDERBY SETTINGS ////
        $rtnPresetObj->_listOrder = $old_presetObj->_listOrder;
        $rtnPresetObj->_listOrderBy = $old_presetObj->_listOrderBy;

        //// SET THE POST STATUS AS THE DEFAULT ////
        ////  SETTING                           ////
        $rtnPresetObj->_postStatus = 'publish';

        //// SET THE EXCLUDE CURRENT POST SETTING ////
        $rtnPresetObj->_postExcludeCurrent = true;
        if ($old_presetObj->_postExcludeCurrent === 'false')
        {
            $rtnPresetObj->_postExcludeCurrent = false;
        }

        //// SET THE STYLE (BEFORE/CONTENT/AFTER) //// 
        ////  CONTENT SETTINGS                    ////
        $rtnPresetObj->_before = $old_presetObj->_before;
        $rtnPresetObj->_content = $old_presetObj->_content;
        $rtnPresetObj->_after = $old_presetObj->_after;
        
        return $rtnPresetObj;
        
        
    }
    private function APL_upgrade_to_03b5()
    {
        if (!empty($this->options))
        {
            $this->options = $this->APL_upgrade_options_03a1_to_03b5($this->options);
        }
        if (!empty($this->presetDbObj))
        {
            $this->presetDbObj = $this->APL_upgrade_presetDbObj_03a1_to_03b5($this->presetDbObj);
        }
    }
    private function APL_upgrade_options_03a1_to_03b5($old_options)
    {
        //Init Defaults
        $rtnOptions = array();
        $rtnOptions['version'] = '0.3.b5';
        $rtnOptions['preset_db_names'] = array(0 => 'default');
        $rtnOptions['delete_core_db'] = true;
        $rtnOptions['error'] = '';
        $rtnOptions['jquery_ui_theme'] = 'overcast';
        //////// UPDATE/ADD ADMIN OPTIONS ////////
        $rtnOptions['default_exit'] = FALSE;
        $rtnOptions['default_exit_msg'] = '<p>Sorry, but no content is available at this time.</p>';
        
        //OVERWRITE DATA FROM THE ORIGINAL
        foreach ($rtnOptions as $key => &$value)
        {
            if (!empty($old_options[$key]))
            {
                $value = $old_options[$key];
            }
        }
        
        return $rtnOptions;
    }
    private function APL_upgrade_presetDbObj_03a1_to_03b5($old_presetObj)
    {
        
        $rtnPresetDbObj = new APLPresetDbObj();
        $rtnPresetDbObj->reset_to_version('0.3.b5');
        
        foreach ($rtnPresetDbObj as $key1 => $value1)
        {
            if ($key1 == '_preset_db' && !empty($old_presetObj->$key1))
            {
                foreach($old_presetObj->_preset_db as $key2 => $value2)
                {
                    $rtnPresetDbObj->_preset_db->$key2 = $this->APL_upgrade_preset_03a1_to_03b5($value2);
                }
            }
            else if(!empty($old_presetObj->$key))
            {
                $rtnPresetDbObj->$key1 = $old_presetObj->$key1;
            }
            
        }
        return $rtnPresetDbObj;
        
    }
    private function APL_upgrade_preset_03a1_to_03b5($old_presetObj)
    {
        
        $rtnPresetObj = new APLPresetObj();
        $rtnPresetObj->reset_to_version('0.3.b5');

        // Step 4
        //// SET PARENT SETTING ////
        if (isset($old_presetObj->_postParent))
        {
            $rtnPresetObj->_postParents = $old_presetObj->_postParent;
        };
        if (isset($old_presetObj->_postTax))
        {
            $rtnPresetObj->_postTax = $old_presetObj->_postTax;
        }
        if (isset($old_presetObj->_listAmount))
        {
            $rtnPresetObj->_listCount = $old_presetObj->_listAmount;
        }
        if (isset($old_presetObj->_listOrderBy))
        {
            $rtnPresetObj->_listOrderBy = $old_presetObj->_listOrderBy;
        }
        if (isset($old_presetObj->_listOrder))
        {
            $rtnPresetObj->_listOrder = $old_presetObj->_listOrder;
        }
        if ($old_presetObj->_postStatus === 'private')
        {
            $rtnPresetObj->_postVisibility = array('private');
        }
        if (isset($old_presetObj->_postStatus) && $old_presetObj->_postStatus !== 'private')
        {
            $rtnPresetObj->_postStatus = array($old_presetObj->_postStatus);
        }
        $rtnPresetObj->_userPerm = (string) 'readable'; //Added
        $rtnPresetObj->_postAuthorOperator = (string) 'none'; //Added
        $rtnPresetObj->_postAuthorIDs = (array) array(); //Added
        $rtnPresetObj->_listIgnoreSticky = (bool) FALSE; //Added
        $rtnPresetObj->_listExcludeCurrent = (bool) TRUE;
        if (isset($old_presetObj->_listExcludeCurrent))
        {
            $rtnPresetObj->_listExcludeCurrent = array($old_presetObj->_postExcludeCurrent);
        }
        $rtnPresetObj->_listExcludeDuplicates = (bool) FALSE; //Added
        $rtnPresetObj->_listExcludePosts = array(); //Added

        $rtnPresetObj->_exit = (string) ''; //Added
        $rtnPresetObj->_before = (string) '';
        if (isset($old_presetObj->_before))
        {
            $rtnPresetObj->_before = $old_presetObj->_before;
        }
        $rtnPresetObj->_content = (string) '';
        if (isset($old_presetObj->_content))
        {
            $rtnPresetObj->_content = $old_presetObj->_content;
        }
        $rtnPresetObj->_after = (string) '';
        if (isset($old_presetObj->_after))
        {
            $rtnPresetObj->_after = $old_presetObj->_after;
        }

        return $rtnPresetObj;
    }
    
}
?>
