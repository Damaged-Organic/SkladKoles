<?php
namespace _bone\contracts;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

interface containerConstants
{
    public function obtain_constants_set();
    public function obtain_default_constant();
}
?>