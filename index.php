<?php
/**
 * CHEERS! Unlimited web-oriented system skeleton,
 * designed and developed by the virtuous pagans
 *
 * This is the Limbo - single entry point for the entire application
 *
 * @author V01K
 * @copyright CHEERS! Unlimited
 *
 * @version 1.2
 */
# ------------------------------------------
# Session handling                         |
# ------------------------------------------
$current_cookie_params = session_get_cookie_params();

$current_root_domain = '.skladkoles.dev';

session_set_cookie_params(
    $current_cookie_params['lifetime'],
    $current_cookie_params['path'],
    $current_root_domain,
    $current_cookie_params['secure'],
    $current_cookie_params['httponly']
);

session_name('dem_session');

session_start();

# ------------------------------------------
# Boot handling                            |
# ------------------------------------------

define("BASEPATH", $_SERVER['DOCUMENT_ROOT']."/");

require(BASEPATH . "_bone/system/boot/dem_trafficLight.php");
require(BASEPATH . "_bone/system/boot/dem_exceptions.php");
require(BASEPATH . "_bone/system/boot/dem_autoLoader.php");

use _bone\classes\containers\constantsNamespaces
    as Namespaces,
    _bone\classes\containers\constantsMoods
    as Moods;

use _bone\system\IntrusionCountermeasures
    as IC,
    _bone\system\DEM_Settings
    as Settings,
	_bone\system\DEM_Gateway
    as Gateway,
    _bone\system\DEM_BootLoader
    as BootLoader,
    _bone\system\DEM_Sarcophagus
    as Sarcophagus;

use _bone\classes\containers\genericContainerConstants
    as ContainerConstants,
    _bone\classes\factories\loaderClass
    as loaderClass;

# ------------------------------------------
# Bones handling                           |
# ------------------------------------------

try {
    new Settings(
        new ContainerConstants('_bone\classes\containers\constantsEnvironment', NULL),
        new ContainerConstants('_bone\classes\containers\constantsAjaxRequest', NULL)
    );

    Settings::register_intrusionCountermeasures(
        new IC
    );

    (new Gateway())->process_environment()
                   ->process_ajaxRequest();

    Settings::register_bootLoader(
        new BootLoader(
            new loaderClass,
            new ContainerConstants('_bone\classes\containers\constantsNamespaces', Namespaces::MEAT_COMMON),
            new ContainerConstants('_bone\classes\containers\constantsMoods', Moods::LAZYBONES)
        )
    );

    (new Sarcophagus)->assign_directory();
} catch(procException $procEX) {
    # ------------------------------------------
    # ProcExceptions                           |
    # ------------------------------------------
    $procEX->handle_exception(
        $procEX->getMessage(),
        [
            'file' => $procEX->getFile(),
            'line' => $procEX->getLine()
        ],
        $procEX->getTrace()
    );
} catch(coreException $coreEX) {
    # ------------------------------------------
    # CoreExceptions                           |
    # ------------------------------------------
    $coreEX->handle_exception(
        $coreEX->getMessage(),
        $coreEX->getTrace()
    );
} catch(bootException $bootEX) {
    # ------------------------------------------
    # BootExceptions                           |
    # ------------------------------------------
    $bootEX->handle_exception(
        $bootEX->getMessage(),
        [
            'file' => $bootEX->getFile(),
            'line' => $bootEX->getLine()
        ]
    );
} catch(notFoundException $notFoundEX) {
    # ------------------------------------------
    # notFoundExceptions - default 404 page    |
    # ------------------------------------------
    $notFoundEX->handle_exception(
        $notFoundEX->getMessage()
    );
}
?>
