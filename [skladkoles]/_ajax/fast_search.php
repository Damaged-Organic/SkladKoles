<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.025) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "search" ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}['search']) ) {
    $this->cancel_ajax_request();
} else {
    $search_string = $_BOOT->involve_object("InputPurifier")->purge_string($_AREA->{$C_E::_REQUEST}['search']);
}

$search_items = $this->load_inter('worker', 'obtain_items_combined_search',
    ['search' => $search_string, 'limit' => 5]
);

$search_items_layer = $this->load_inter('layer', 'fast_search', $search_items);

if( !$search_items_layer ) {
    $this->satisfy_ajax_request(json_encode([
        'interlayer' => NULL
    ]));
} else {
    $this->satisfy_ajax_request(json_encode([
        'interlayer' => $search_items_layer
    ]));
}
?>