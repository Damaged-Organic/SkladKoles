<?php
namespace _bone\classes\containers;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

/**
 * Class constantsNamespaces
 * @package _bone\classes\containers
 *
 * Meat namespaces for objects involvement in DEM_BootLoader
 */
class constantsNamespaces
{
    const MEAT_COMMON   = '_meat\classes\common';
	const MEAT_EXTERNAL = '_meat\classes\external';
    const MEAT_SPECIFIC = '_meat\classes\specific';
}
?>