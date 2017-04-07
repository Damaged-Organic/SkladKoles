<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $feedback_data = $supplied_data;
}

$xml_email_common_data = $_BOOT->involve_object("XML_Handler")->get_xml(
    "email_common_data",
    $_AREA->{$C_E::_LANGUAGE}
);

$message_HTML = [
    'admin' => $this->load_inter("layer", "email/feedback", [$feedback_data, $is_admin_version = TRUE]),
    'user'  => $this->load_inter("layer", "email/feedback", [$feedback_data, $is_admin_version = FALSE]),
];

$email_data = [
    'admin' => [
        'set_from'     => [
            'email'     => NO_REPLY_EMAIL,
            'full_name' => $xml_email_common_data->from_company
        ],
        'add_reply_to' => [
            'email' 	=> NO_REPLY_EMAIL,
            'full_name' => $xml_email_common_data->from_company
        ],
        'add_address' => [
            [
                'email'     => FEEDBACK_EMAIL,
                'full_name' => $xml_email_common_data->from_company
            ]
        ],
        'subject'      => $xml_email_common_data->subject_feedback,
        'msg_HTML'     => $message_HTML['admin'],
        'alt_body'	   => $xml_email_common_data->alt_body
    ],
    'user' => [
        'set_from'     => [
            'email'     => NO_REPLY_EMAIL,
            'full_name' => $xml_email_common_data->from_company
        ],
        'add_reply_to' => [
            'email' 	=> NO_REPLY_EMAIL,
            'full_name' => $xml_email_common_data->from_company
        ],
        'add_address'  => [
            [
                'email'     => $feedback_data['email'],
                'full_name' => $feedback_data['name']
            ]
        ],
        'subject'      => $xml_email_common_data->subject_feedback,
        'msg_HTML'     => $message_HTML['user'],
        'alt_body'	   => $xml_email_common_data->alt_body
    ]
];

$db_handler = $_BOOT->involve_object('DB_Handler');

$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->process_email($feedback_data['email']);

if( !$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->send_mail($email_data['admin']) ||
    !$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->send_mail($email_data['user']) ) {
    return FALSE;
} else {
    return TRUE;
}
?>