<?php
#Direct access protection
define('GREENLIGHT', TRUE);

define('NO_DIRECT_ACCESS', "Direct access is forbidden");

function is_access_direct()
{
    if( !defined('GREENLIGHT') ) {
        return TRUE;
    } else {
        return FALSE;
    }
}
?>