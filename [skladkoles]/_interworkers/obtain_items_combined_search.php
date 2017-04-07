<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$preparate = function($search_string)
{
    if( $search_string === FALSE ) {
        return FALSE;
    } elseif(
        (strlen($search_string) < 3) || (strlen($search_string) > 32) ) {
        return FALSE;
    } else {
        return TRUE;
    }
};

if( !$preparate($supplied_data['search']) ) {
    return [];
} else {
    $search = explode(' ', $supplied_data['search']);
}

$object_db_handler   = $_BOOT->involve_object("DB_Handler");
$object_catalogCells = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler]);

$items_per_lift = 12;

#Lift items selection role
if( !empty($supplied_data['count']) )
{
    if( ($count = $_BOOT->involve_object("InputPurifier")->purge_integer($supplied_data['count'])) === FALSE ) {
        return FALSE;
    } elseif( $object_catalogCells->count_search_items_data_cell($search) <= $count ) {
        return FALSE;
    }

    $items_per_lift = [$count, $items_per_lift];
}

if( !empty($supplied_data['limit']) )
{
    $items_per_lift = [0, $supplied_data['limit']];
}

$items_combined = $object_catalogCells->search_items_data_cell($search, $items_per_lift);

return ( $_BOOT->involve_object("DataThralls")->is_filled_array($items_combined) ) ? $items_combined : [];
?>