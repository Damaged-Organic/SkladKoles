<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.1) ) {
    $this->cancel_ajax_request();
} elseif( ($_AREA->{$C_AR::AR_METHOD} !== "set_page") && ($_AREA->{$C_AR::AR_METHOD} !== "set_items_number") ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}) ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

if( !empty($_SESSION['filter_parameters']) ) {
    $items_filter = $_SESSION['filter_parameters'];
} else {
    $items_filter = [];
}

switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
{
    case 'rims':
    case 'exclusive_rims':
        $records_number = count($this->load_inter('worker', 'obtain_items_rims', $items_filter)['items']);

        $inter = [
            'layer'  => 'items_rims',
            'worker' => 'obtain_items_rims'
        ];
    break;

    case 'tyres':
    case 'exclusive_tyres':
        $records_number = count($this->load_inter('worker', 'obtain_items_tyres', $items_filter)['items']);

        $inter = [
            'layer'  => 'items_tyres',
            'worker' => 'obtain_items_tyres'
        ];
    break;

    default:
        $this->cancel_ajax_request();
    break;
}

#PAGINATION
$current_page     = $_SESSION['pagination']['current_page'] = 1;
$records_per_page = ( !empty($_SESSION['pagination']['records_per_page']) ) ? $_SESSION['pagination']['records_per_page'] : 12;

switch($_AREA->{$C_AR::AR_METHOD})
{
    case 'set_page':
        $current_page = $_SESSION['pagination']['current_page'] = ( !empty($_AREA->{$C_E::_REQUEST}) ) ? $_BOOT->involve_object("InputPurifier")->purge_integer($_AREA->{$C_E::_REQUEST}) : NULL;
    break;

    case 'set_items_number':
        $records_per_page = $_SESSION['pagination']['records_per_page'] = ( !empty($_AREA->{$C_E::_REQUEST}['count']) ) ? $_BOOT->involve_object("InputPurifier")->purge_integer($_AREA->{$C_E::_REQUEST}['count']) : 12;
    break;
}

$pagination_parameters = [
    'records_number'   => $records_number,
    'records_per_page' => $records_per_page,
    'pages_step'       => 7
];

if( is_object($_BOOT->involve_object("Pagination")->set_parameters($pagination_parameters)->set_current_page($current_page)) ) {
    $pagination = $_BOOT->involve_object("Pagination")->handle_pagination();
} else {
    $this->cancel_ajax_request();
}

$items_layer = $this->load_inter('layer', $inter['layer'],
    $this->load_inter('worker', $inter['worker'],
        $items_filter + ['pagination' => [$pagination['first_record'], $pagination['records_per_page']]]
    )
);

$pagination = $this->load_inter('layer', 'pagination', $pagination);
#END-PAGINATION

if( !$items_layer ) {
    $this->cancel_ajax_request();
} else {
    $this->satisfy_ajax_request(json_encode([
        'catalogGrid' => $items_layer,
        'navigation'  => $pagination
    ]));
}
?>