<?php
namespace _bone\classes\containers;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException;

use ReflectionClass;

use \_bone\contracts\containerConstants
    as containerConstants;

class genericContainerConstants implements containerConstants
{
	#current constants set
    private $constants_set = [];

    #default constant value from current set
    private $default_constant = NULL;

    function __construct($container, $default = NULL) {
        $this->assign_constants_set($container);
        if( !is_null($default) ) {
            $this->assign_default_constant($default);
        }
    }

    /**
     * @name assign_constants_set
     *
     * Creates set of available class constants and assigns it to corresponding property
     */
    private function assign_constants_set($container)
    {
        $reflection = new ReflectionClass($container);
        $this->constants_set = $reflection->getConstants();
    }

    /**
     * @name assign_default_constant
     *
     * Sets default constant from set that is created by assign_constants_set()
     *
     * @param $default
     * @throws \Exception
     */
    private function assign_default_constant($default)
    {
        if( !in_array($default, $this->constants_set, TRUE) ) {
            throw new coreException('No such constant in container to set as default');
        } else {
            $this->default_constant = $default;
        }
    }

    /**
     * @name obtain_constants_set
     *
     * Obtains current constants set
     *
     * @return array
     */
    public function obtain_constants_set()
    {
        return $this->constants_set;
    }

    /**
     * @name obtain_default_constant
     *
     * Obtains default constant from current constants set
     *
     * @return null
     */
    public function obtain_default_constant()
    {
        if( is_null($this->default_constant) ) {
            throw new coreException('Default constant of this container is not set');
        } else {
            return $this->default_constant;
        }
    }
}
?>