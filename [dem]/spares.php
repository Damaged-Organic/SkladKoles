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

if( empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) {
    throw new notFoundException('Cannot access page without specified parameters');
} else {
    if( !in_array($_AREA->{$C_E::_ARGUMENTS}[0], ['rings', 'bolts', 'nuts', 'locks', 'logos', 'pins']) ) {
        throw new notFoundException('Wrong spares category');
    }

    $category = $_AREA->{$C_E::_ARGUMENTS}[0];
}

$interworkers = [
    'head_data'   => $this->load_inter('worker', 'obtain_head_data'),
    'spares_data' => $this->load_inter('worker', 'spares_data', $category)
];

$interlayers = [
    'head'   => $this->load_inter('layer', 'head', $interworkers['head_data']),
    'spares' => $this->load_inter('layer', 'spares', $interworkers['spares_data']),
    'footer' => $this->load_inter('layer', 'footer')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}.php");
?>
