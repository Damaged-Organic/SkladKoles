<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$object_db_handler   = $_BOOT->involve_object("DB_Handler");
$object_catalogCells = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler]);

$viewed_items = $object_catalogCells->viewed_items_data_cell();

return ( $_BOOT->involve_object("DataThralls")->is_filled_array($viewed_items) ) ? $viewed_items : [];
?>