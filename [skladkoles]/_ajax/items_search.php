<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.5) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "get_items_search" ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}['count']) ) {
    $this->cancel_ajax_request();
} elseif( empty($_SESSION['search']) ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

if( ($search = $_BOOT->involve_object("InputPurifier")->purge_string($_SESSION['search'])) === FALSE ) {
    $this->cancel_ajax_request();
}

$top_items_worker = $this->load_inter('worker', 'obtain_items_combined_search', ['search' => $search, 'count' => $_AREA->{$C_E::_REQUEST}['count']]);

if( !$top_items_worker ) {
    $this->cancel_ajax_request();
}

$top_items_layer = $this->load_inter('layer', 'items_search', $top_items_worker);

$object_db_handler = $_BOOT->involve_object("DB_Handler");

if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$object_db_handler])->count_search_items_data_cell(explode(' ', $search)) <= ($_AREA->{$C_E::_REQUEST}['count'] + count($top_items_worker)) ) {
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