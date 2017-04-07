<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class DataThralls
{
	# |1.1| - custom is_array() with !empty() check
	public function is_filled_array(&$data_array)
	{
		return ( !empty($data_array) && is_array($data_array) ) ? TRUE : FALSE ;
	}
	
	# |1.2| - custom is_object() with !empty() check
	public function is_filled_object(&$data_object)
	{
		return ( !empty($data_object) && is_object($data_object) ) ? TRUE : FALSE ;
	}
	
	# |1.3| - get first key of an array
	public function array_first_key(&$data_array)
	{
		reset($data_array);
		return key($data_array);
	}
	
	# |1.4| - get last key of an array
	public function array_last_key(&$data_array)
	{
		end($data_array);
		return key($data_array);
	}
}
?>