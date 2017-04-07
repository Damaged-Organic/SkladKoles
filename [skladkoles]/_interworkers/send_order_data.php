<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($order_data, $cart_items, $counted_cart_items, $order_id) = $supplied_data;

    $order_data['order_code'] = $order_id;
}

$db_handler = $_BOOT->involve_object('DB_Handler');

$xml_email_common_data = $_BOOT->involve_object("XML_Handler")->get_xml(
    "email_common_data",
    $_AREA->{$C_E::_LANGUAGE}
);

$message_HTML = [
    'admin' => $this->load_inter("layer", "email/order", [$order_data, $cart_items, $counted_cart_items, $is_admin_version = TRUE])
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
                'email'     => ORDER_EMAIL,
                'full_name' => $xml_email_common_data->from_company
            ]
        ],
        'subject'      => "{$xml_email_common_data->subject_order} {$order_data['order_code']}",
        'msg_HTML'     => $message_HTML['admin'],
        'alt_body'	   => $xml_email_common_data->alt_body
    ]
];

if( !$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->send_mail($email_data['admin']) ) {
    $send = FALSE;
} else {
    $send = TRUE;
}

if( $order_data['userEmail'] )
{
    $message_HTML = [
        'user' => $this->load_inter("layer", "email/order", [$order_data, $cart_items, $counted_cart_items, $is_admin_version = FALSE])
    ];

    $email_data = [
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
                    'email'     => $order_data['userEmail'],
                    'full_name' => $order_data['userName']
                ]
            ],
            'subject'      => "{$xml_email_common_data->subject_order} {$order_data['order_code']}",
            'msg_HTML'     => $message_HTML['user'],
            'alt_body'	   => $xml_email_common_data->alt_body
        ]
    ];

    $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->process_email($order_data['userEmail']);

    if( !$_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPMailerLink", [$db_handler])->send_mail($email_data['user']) ) {
        $send = FALSE;
    } else {
        $send = TRUE;
    }
}

return $send;
?>
