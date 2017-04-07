<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$items_per_lift = 12;

$object_db_handler = $_BOOT->involve_object("DB_Handler");

if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler])->count_top_items_data_cell() <= $items_per_lift) {
    return FALSE;
}

$xml_items_top_button = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<div class="more-button lift-button">
    <a href="#"><?=$xml_items_top_button->button_get_more_items?></a>
</div>