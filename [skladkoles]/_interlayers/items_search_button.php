<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data['search']) ) {
    return FALSE;
} else {
    $search = explode(' ', $supplied_data['search']);
}

$items_per_lift = 12;

$object_db_handler = $_BOOT->involve_object("DB_Handler");

if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler])->count_search_items_data_cell($search) <= $items_per_lift) {
    return FALSE;
}

$xml_items_search_button = $_BOOT->involve_object("XML_Handler")->get_xml(
    'items_top_button',
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<div class="more-button lift-button">
    <a href="#"><?=$xml_items_search_button->button_get_more_items?></a>
</div>