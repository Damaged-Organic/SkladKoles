<?php
namespace _bone\contracts;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

interface factoryLoader
{
	public function load_object($object_name, array $construct_arguments = []);
}
?>