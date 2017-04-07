<?php
namespace _bone\system;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException;

use _bone\system\DEM_Settings
    as Settings;

use _bone\contracts\factoryLoader
    as factoryLoader,
    _bone\contracts\containerConstants
    as containerConstants;

class DEM_BootLoader
{
    #current behaviour and namespace constants set
    private $moodset_instance    = NULL;
	private $namespaces_instance = NULL;

    #current mood name and flag for one-off/constant usage
	private $mood = [
        'name'       => NULL,
        'disposable' => FALSE
    ];
	
	#current namespace for autoload lookup
    private $namespace = [
        'name'       => NULL,
        'disposable' => FALSE
    ];
	
	#current object production class
	private $object_factory = NULL;

    #set of all available objects
	private $instances = [];
	
	function __construct(factoryLoader $object_factory, containerConstants $namespaces_instance, containerConstants $moodset_instance) {
        //Assigns a factory class that will produce new instances of required objects
        $this->object_factory = $object_factory;

        $this->moodset_instance    = $moodset_instance;
		$this->namespaces_instance = $namespaces_instance;

        $this->assign_default_mood();
		$this->assign_default_namespace();
	}

    /**
     * @name assign_default_mood
     *
     * Assigns default mood at boot time
     */
    private function assign_default_mood()
    {
        $this->mood['name']       = $this->moodset_instance->obtain_default_constant();
        $this->mood['disposable'] = FALSE;
    }
	
	private function assign_default_namespace()
	{
		$this->namespace['name']       = $this->namespaces_instance->obtain_default_constant();
        $this->namespace['disposable'] = FALSE;
	}

    /**
     * @name verify_mood
     *
     * Checks whether mood exists in defined set
     *
     * @param $mood
     * @return bool
     */
    private function verify_mood($mood)
    {
        return ( in_array($mood, $this->moodset_instance->obtain_constants_set(), TRUE) );
    }
	
	private function verify_namespace($namespace)
	{
		return ( in_array($namespace, $this->namespaces_instance->obtain_constants_set(), TRUE) );
	}

    /**
     * @name assign_mood
     * @chained
     *
     * Assigns mood (if it exists) for single subsequent object involvement
     *
     * @param $mood
     * @return $this
     * @throws coreException
     */
    public function assign_mood($mood, $disposable = TRUE)
    {
        if( !$this->verify_mood($mood) )
			throw new coreException('Unknown mood');
		
		if( !is_bool($disposable) )
			throw new coreException('Disposable argument should be of type boolean');
		
        $this->mood['name']       = $mood;
        $this->mood['disposable'] = $disposable;

        return $this;
    }

    /**
     * @name assign_namespace
     *
     * Assigns namespace from which new objects are involved.
     *
     * @param $namespace [should match subfolders structure]
     */
    public function assign_namespace($namespace, $disposable = TRUE)
    {
		if( !$this->verify_namespace($namespace) )
			throw new coreException('Unknown namespace');
		
		if( !is_bool($disposable) )
			throw new coreException('Disposable argument should be of type boolean');
		
        $this->namespace['name']       = $namespace;
		$this->namespace['disposable'] = $disposable;
		
		return $this;
    }
	
	private function dispose_settings()
	{
		if( $this->mood['disposable'] === TRUE )
			$this->assign_default_mood();
			
		if( $this->namespace['disposable'] === TRUE )
			$this->assign_default_namespace();
	}
	
    /**
     * @name involve_object
     *
     * Combines current namespace with a passed object name and acts accordingly to current mood
     * Also, drops to default mood if current mood was set with assign_disposable_mood() (i.e. is disposable)
     *
     * @param $object_name
     * @param array $construct_arguments
     * @return null
     * @throws coreException
     */
    public function involve_object($object_name, $construct_arguments = [])
	{
        $namespaced_object_name = "{$this->namespace['name']}\\{$object_name}";

        $mood_set = $this->moodset_instance->obtain_constants_set();
		
		switch($this->mood['name'])
		{
            case $mood_set['LAZYBONES']:
                $object = $this->instance_lazybones($namespaced_object_name, $construct_arguments);
			break;
			
			case $mood_set['WORKAHOLIC']:
                $object = $this->instance_workaholic($namespaced_object_name, $construct_arguments);
			break;
			
			case $mood_set['DICKHEAD']:
				$object = $this->instance_dickhead($namespaced_object_name);
			break;

            default:
                throw new coreException('Unknown kind of mood');
            break;
		}

        #drops to default mood if current mood is one-off
        $this->dispose_settings();

        return $object;
	}

    /**
     * @name instance_lazybones
     *
     * Creates instance of an object if it does not exists, otherwise uses existing instance
     * Adds new instance to an $instances array
     *
     * @param $object_name
     * @param array $construct_arguments
     * @return mixed
     */
    private function instance_lazybones($object_name, $construct_arguments = [])
    {
        if( !in_array($object_name, array_keys($this->instances), TRUE) ) {
            $this->instances[$object_name] = $this->object_factory->load_object($object_name, $construct_arguments);
        }

        return $this->instances[$object_name];
    }

    /**
     * @name instance_workaholic
     *
     * Always creates a new instance
     * Ignores $instances array
     *
     * @param $object_name
     * @param array $construct_arguments
     * @return mixed
     */
    private function instance_workaholic($object_name, $construct_arguments = [])
    {
        return $this->object_factory->load_object($object_name, $construct_arguments);
    }

    /**
     * @name instance_dickhead
     *
     * Has no idea how to create a new instance
     * Looking in $instances array in attempt to find one
     *
     * @param $object_name
     * @return mixed
     */
    private function instance_dickhead($object_name)
    {
        return ( in_array($object_name, array_keys($this->instances), TRUE) ) ? $this->instances[$object_name] : NULL;
    }
}
?>