<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class DEM_autoLoader
{
    function __construct() {
        $this->assign_autoload();
    }

    /**
     * @name assign_autoload
     *
     * Initiate autoload. As subfolder structure is matching namespaces, so no autoload function is defined
     */
    private function assign_autoload()
    {
        spl_autoload_extensions('.php');
        spl_autoload_register();
    }
}

return new DEM_autoLoader;
?>