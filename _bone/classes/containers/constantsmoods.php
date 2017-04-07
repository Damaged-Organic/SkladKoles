<?php
namespace _bone\classes\containers;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

/**
 * Class constantsMoods
 * @package _bone\classes\containers
 *
 * Behaviour moods for objects involvement in DEM_BootLoader
 */
class constantsMoods
{
    const LAZYBONES  = 'lazybones';
    const WORKAHOLIC = 'workaholic';
    const DICKHEAD   = 'dickhead';
}
?>