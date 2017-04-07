<?php
namespace _bone\classes\factories;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException;

use ReflectionClass;

use _bone\contracts\factoryLoader
    as factoryLoader;

class loaderClass implements factoryLoader
{
	public function load_object($object_name, array $construct_arguments = [])
	{
		if( !class_exists($object_name, $autoload = TRUE) ) {
			throw new coreException("Class [{$object_name}] does not exists");
		} else {
			$class = new ReflectionClass($object_name);
	
			return $class->newInstanceArgs($construct_arguments);
		}
	}
}
?>