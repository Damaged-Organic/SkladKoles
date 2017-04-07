<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$db_handler   = $_BOOT->involve_object("DB_Handler");
$phpLoginLink = $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}]);

if( $phpLoginLink->is_user_logged_in() ) {
    $this->redirect_and_exit("http://skladkoles.dev");
} else {
    define('WHITELIGHT', TRUE);
}

if( ($login_errors = $phpLoginLink->get_errors()) ) {
    $login_attempt_info = $login_errors;
} elseif(
    ($login_messages = $phpLoginLink->get_messages()) ) {
    $login_attempt_info = $login_messages;
} else {
    $login_attempt_info = NULL;
}

$interworkers = [
    'head_data' => $this->load_inter('worker', 'obtain_head_data')
];

$interlayers = [
    'head'       => $this->load_inter('layer', 'head', $interworkers['head_data']),
    'login_form' => $this->load_inter('layer', 'login_form', $login_attempt_info)
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}.php");
?>
