<?php
namespace _bone\classes\containers;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class constantsAjaxRequest
{
    const IS_AJAX_REQUEST = "is_ajax_request";

    const AR_ORIGIN   = "AR_origin";
    const AR_LOCATION = "AR_location";
    const AR_METHOD   = "AR_method";
}
?>