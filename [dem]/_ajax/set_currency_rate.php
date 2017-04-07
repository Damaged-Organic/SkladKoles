<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$db_handler = $_BOOT->involve_object("DB_Handler");

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0) ) {
    $this->cancel_ajax_request();
}

if( !($currency_rate = $_BOOT->involve_object("InputPurifier")->purge_float($_AREA->{$C_E::_REQUEST}['currency_rate'])) ) {
    $this->cancel_ajax_request("<p>Произошла ошибка - данные не соответствуют формату</p>");
}

if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->set_currency_rate($currency_rate) ) {
    $this->satisfy_ajax_request("<p>Текущий курс обновлен</p>");
} else {
    $this->cancel_ajax_request("<p>Произошла ошибка</p>");
}
?>