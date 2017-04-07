<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$items_per_lift = 12;

$object_db_handler   = $_BOOT->involve_object("DB_Handler");
$object_catalogCells = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler]);

#Lift items selection role
if( !empty($supplied_data['count']) )
{
    if( ($count = $_BOOT->involve_object("InputPurifier")->purge_integer($supplied_data['count'])) === FALSE ) {
        return FALSE;
    } elseif( $object_catalogCells->count_top_items_data_cell() <= $count ) {
        return FALSE;
    }

    $items_per_lift = [$count, $items_per_lift];
}

$items_combined = $object_catalogCells->top_items_data_cell($items_per_lift);

return ( $_BOOT->involve_object("DataThralls")->is_filled_array($items_combined) ) ? $items_combined : [];
?>