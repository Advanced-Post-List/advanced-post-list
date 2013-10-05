<?php

class APLExport
{
  function default_export()
  {
    header('Content-type: application/json');
    header('Content-Disposition: attachment; filename="name.json"');
    echo 'def456';
    return 'abc123';
  }
}
?>
