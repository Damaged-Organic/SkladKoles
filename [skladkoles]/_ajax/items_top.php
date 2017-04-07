<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.5) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "get_items_top" ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}['count']) ) {
    $this->cancel_ajax_request();
}

$top_items_worker = $this->load_inter('worker', 'obtain_items_combined_top', ['count' => $_AREA->{$C_E::_REQUEST}['count']]);

if( !$top_items_worker ) {
    $this->cancel_ajax_request();
}

$top_items_layer = $this->load_inter('layer', 'items_top', $top_items_worker);

$object_db_handler = $_BOOT->involve_object("DB_Handler");

if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler])->count_top_items_data_cell() <= ($_AREA->{$C_E::_REQUEST}['count'] + count($top_items_worker)) ) {
    $is_last_item = TRUE;
} else {
    $is_last_item = FALSE;
}

if( !$top_items_layer ) {
    $this->cancel_ajax_request();
} else {
    $this->satisfy_ajax_request(json_encode([
        'isLastItem' => $is_last_item,
        'interlayer' => $top_items_layer
    ]));
}
?>