<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $order_data = $supplied_data;
}

if( ($order_data['userName'] = $_BOOT->involve_object("InputPurifier")->purge_string($order_data['userName'])) === FALSE ) {
    return FALSE;
}

if( ($order_data['userPhone'] = $_BOOT->involve_object("InputPurifier")->purge_phone($order_data['userPhone'])) === FALSE ) {
    return FALSE;
}

if( !empty($order_data['userEmail']) )
{
    if( ($order_data['userEmail'] = $_BOOT->involve_object("InputPurifier")->purge_email($order_data['userEmail'])) === FALSE ) {
        return FALSE;
    }
}

if( !empty($order_data['userLocation']) )
{
    if( ($order_data['userLocation'] = $_BOOT->involve_object("InputPurifier")->purge_string($order_data['userLocation'])) === FALSE ) {
        return FALSE;
    }
}

if( !empty($order_data['userMessage']) )
{
    if( ($order_data['userMessage'] = $_BOOT->involve_object("InputPurifier")->purge_string($order_data['userMessage'])) === FALSE ) {
        return FALSE;
    }
}

// switch($order_data['deliveryType'])
// {
//     case 'pickup':
//         $order_data['deliveryType'] = 'pickup';
//     break;
//
//     case 'shipping':
//         $order_data['deliveryType'] = 'shipping';
//
//         if( empty($order_data['city']) ||
//             empty($order_data['address']) ||
//             empty($order_data['region']) ) {
//             return FALSE;
//         }
//
//         if( ($order_data['city'] = $_BOOT->involve_object("InputPurifier")->purge_string($order_data['city'])) === FALSE ) {
//             return FALSE;
//         }
//
//         if( ($order_data['address'] = $_BOOT->involve_object("InputPurifier")->purge_string($order_data['address'])) === FALSE ) {
//             return FALSE;
//         }
//
//         if( ($order_data['region'] = $_BOOT->involve_object("InputPurifier")->purge_integer($order_data['region'])) === FALSE ) {
//             return FALSE;
//         } elseif( !$_BOOT->involve_object("DB_Handler")->is_value_exists($C_S::DB_PREFIX_alpha."regions", 'id', $order_data['region']) ) {
//             return FALSE;
//         }
//     break;
// }

return $order_data;
?>
