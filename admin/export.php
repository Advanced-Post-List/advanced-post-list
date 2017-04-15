<?php

function APL_export()
{
    check_ajax_referer("APL_export");
    $outputFileData = get_option('APL_TMP_export_dataOutput');
    delete_option('APL_TMP_export_dataOutput');
    header('Content-type: application/json');
    header('Content-Disposition: attachment; filename="' . $_GET['filename'] . '.json"');
    
    echo trim(json_encode($outputFileData));
    exit();
}

?>