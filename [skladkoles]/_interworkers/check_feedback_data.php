<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $feedback_data = $supplied_data;
}

if( ($feedback_data['name'] = $_BOOT->involve_object("InputPurifier")->purge_string($feedback_data['name'])) === FALSE ) {
    return FALSE;
}

if( ($feedback_data['email'] = $_BOOT->involve_object("InputPurifier")->purge_email($feedback_data['email'])) === FALSE ) {
    return FALSE;
}

if( ($feedback_data['phone'] = $_BOOT->involve_object("InputPurifier")->purge_phone($feedback_data['phone'])) === FALSE ) {
    return FALSE;
}

if( ($feedback_data['message'] = $_BOOT->involve_object("InputPurifier")->purge_string($feedback_data['message'])) === FALSE ) {
    return FALSE;
}

if( !empty($feedback_data['subject']) )
{
    if( ($feedback_data['subject'] = $_BOOT->involve_object("InputPurifier")->purge_string($feedback_data['subject'])) === FALSE ) {
        return FALSE;
    }
}

return $feedback_data;
?>