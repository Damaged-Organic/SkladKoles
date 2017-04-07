<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_ajax_response = $_BOOT->involve_object("XML_Handler")->get_xml(
    "ajax_response",
    $_AREA->{$C_E::_LANGUAGE}
);

$error = [
    'headline' => "<h2>".$xml_ajax_response->error_headline."</h2>",
    'message'  => "<p>".$xml_ajax_response->error_general."</p>"
];

$success = [
    'headline' => "<h2>".$xml_ajax_response->success_headline_feedback."</h2>",
    'message'  => "<p>".$xml_ajax_response->success_message_feedback."</p>"
];

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request(implode('', $error));
} elseif( !$_IC->is_ajax_cooled_down(5) ) {
    $error['message'] = "<p>".$xml_ajax_response->error_cooldown."</p>";
    $this->cancel_ajax_request(implode('', $error));
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "send_feedback" ) {
    $this->cancel_ajax_request(implode('', $error));
}

if( empty($_AREA->{$C_E::_REQUEST}['name']) ||
    empty($_AREA->{$C_E::_REQUEST}['email']) ||
    empty($_AREA->{$C_E::_REQUEST}['phone']) ||
    empty($_AREA->{$C_E::_REQUEST}['message'])) {
    $error['message'] = "<p>".$xml_ajax_response->error_data."</p>";
    $this->cancel_ajax_request(implode('', $error));
}

$feedback_data = $this->load_inter("worker", "check_feedback_data", $_AREA->{$C_E::_REQUEST});

if( !$feedback_data ) {
    $error['message'] = "<p>".$xml_ajax_response->error_data."</p>";
    $this->cancel_ajax_request(implode('', $error));
}

$result = $this->load_inter("worker", "send_feedback_data", $feedback_data);

if( !$result ) {
    $error['message'] = "<p>".$xml_ajax_response->error_data."</p>";
    $this->cancel_ajax_request(implode('', $error));
} else {
    $this->satisfy_ajax_request(implode('', $success));
}
?>