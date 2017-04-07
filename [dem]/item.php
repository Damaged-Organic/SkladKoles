<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$db_handler = $_BOOT->involve_object("DB_Handler");

if( !$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    throw new notFoundException('Unauthorized access forbidden');
} else {
    define('WHITELIGHT', TRUE);
}

if( empty($_AREA->{$C_E::_ARGUMENTS}[0]) || empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) {
    throw new notFoundException('Cannot access page without specified parameters');
} else {
    switch($_AREA->{$C_E::_ARGUMENTS}[0])
    {
        case 'rims':
            $item_type   = 'rims';
            $item_table  = $C_S::DB_PREFIX_alpha."items_rims";
            $interworker = 'obtain_item_rims_data';
        break;

        case 'exclusive_rims':
            $item_type  = 'exclusive_rims';
            $item_table = $C_S::DB_PREFIX_alpha."items_rims_exclusive";
            $interworker = 'obtain_item_rims_data';
        break;

        case 'tyres':
            $item_type  = 'tyres';
            $item_table = $C_S::DB_PREFIX_alpha."items_tyres";
            $interworker = 'obtain_item_tyres_data';
        break;

        case 'exclusive_tyres':
            $item_type  = 'exclusive_tyres';
            $item_table = $C_S::DB_PREFIX_alpha."items_tyres_exclusive";
            $interworker = 'obtain_item_tyres_data';
        break;

        default:
            throw new procException('Wrong item category');
        break;
    }

    if( !$_BOOT->involve_object("DB_Handler")->is_value_exists($item_table, 'id', $_AREA->{$C_E::_ARGUMENTS}[1]) ) {
        throw new procException('Item does not exist');
    } else {
        $item_id = $_AREA->{$C_E::_ARGUMENTS}[1];
    }
}

$interworkers = [
    'head_data'  => $this->load_inter('worker', 'obtain_head_data'),
    'item_data'  => $this->load_inter('worker', $interworker, [$item_id, $item_type, $item_table]),
    'promo_data' => $this->load_inter('worker', 'promo_data'),
];

$interlayers = [
    'head'   => $this->load_inter('layer', 'head', $interworkers['head_data']),
    'item'   => $this->load_inter('layer', 'item', [$interworkers['item_data'], $item_id, $item_type, $item_table, $interworkers['promo_data']]),
    'footer' => $this->load_inter('layer', 'footer')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}.php");
?>