<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.25) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "filter_cars_main" ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

if( empty($_AREA->{$C_E::_REQUEST}['next']) || empty($_AREA->{$C_E::_REQUEST}['value']) ) {
    $this->cancel_ajax_request();
} else {
    if( ($_AREA->{$C_E::_REQUEST}['value'] = $_BOOT->involve_object("InputPurifier")->purge_string($_AREA->{$C_E::_REQUEST}['value'])) === FALSE )
        $this->cancel_ajax_request();
}

if( $_AREA->{$C_E::_REQUEST}['next'] === 'auto-model' ) {
    $_SESSION['items_filter_cars_main']['auto-mark'] = $_AREA->{$C_E::_REQUEST}['value'];

    $interlayer = "cars_step_main_1";
} elseif( $_AREA->{$C_E::_REQUEST}['next'] === 'auto-year' ) {
    $_SESSION['items_filter_cars_main']['auto-model'] = $_AREA->{$C_E::_REQUEST}['value'];

    $interlayer = "cars_step_main_2";
} elseif( $_AREA->{$C_E::_REQUEST}['next'] === 'auto-modification' ) {
    $_SESSION['items_filter_cars_main']['auto-year'] = $_AREA->{$C_E::_REQUEST}['value'];

    $interlayer = "cars_step_main_3";
} else {
    $this->cancel_ajax_request();
}

$interlayer = $this->load_inter('layer', $interlayer, $_AREA->{$C_E::_REQUEST}['value']);

if( !$interlayer ) {
    $this->cancel_ajax_request();
} else {
    $this->satisfy_ajax_request(json_encode([
        'interlayer' => $interlayer
    ]));
}
?>