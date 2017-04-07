<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    $promotion_id = NULL;
} else {
    list($promotion_id) = $supplied_data;
}

$object_db_handler   = $_BOOT->involve_object("DB_Handler");
$object_catalogCells = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler]);

$items_combined = $object_catalogCells->promo_items_data_cell($promotion_id);

return ( $_BOOT->involve_object("DataThralls")->is_filled_array($items_combined) ) ? $items_combined : [];
?>