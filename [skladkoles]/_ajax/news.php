<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.5) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "get_news" ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}['count']) ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

$news_worker = $this->load_inter('worker', 'news', ['count' => $_AREA->{$C_E::_REQUEST}['count']]);

if( !$news_worker ) {
    $this->cancel_ajax_request();
}

$news_layer = $this->load_inter('layer', 'news', $news_worker);


if( $_BOOT->involve_object("DB_Handler")->get_records_number($C_S::DB_PREFIX_alpha.'news') <= ($_AREA->{$C_E::_REQUEST}['count'] + count($news_worker)) ) {
    $is_last_item = TRUE;
} else {
    $is_last_item = FALSE;
}

if( !$news_layer ) {
    $this->cancel_ajax_request();
} else {
    $this->satisfy_ajax_request(json_encode([
        'isLastItem' => $is_last_item,
        'interlayer' => $news_layer
    ]));
}
?>
