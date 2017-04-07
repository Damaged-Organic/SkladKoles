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

$interworkers = [
    'head_data' => $this->load_inter('worker', 'obtain_head_data'),
    'news_data' => $this->load_inter('worker', 'obtain_news_data', $_AREA->{$C_E::_ARGUMENTS}[0])
];

$interlayers = [
    'head'   => $this->load_inter('layer', 'head', $interworkers['head_data']),
    'news'   => $this->load_inter('layer', 'news', $interworkers['news_data']),
    'footer' => $this->load_inter('layer', 'footer')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}.php");
?>