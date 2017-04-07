<?php
namespace _bone\system;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException, notFoundException;

use _bone\system\constants\constantsSetup
    as Setup;

use _bone\classes\containers\constantsEnvironment
    as Environment,
    _bone\classes\containers\constantsAjaxRequest
    as AjaxRequest;

use _bone\system\DEM_Settings
    as Settings;

/*
 * Bone extra classes aliasing
 */

class DEM_Sarcophagus
{
    private $constants   = [];
    private $environment = NULL;

    private $supplied_constants = [];
    private $supplied_objects   = [];

    function __construct()
    {
        $this->constants   = Settings::get_constants();
        $this->environment = Settings::get_environment_parameters();

        $this->supplied_constants = array_combine(
            ['S', 'E', 'AR', 'N', 'M'],
            [
                $this->constants['setup'],
                $this->constants['environment'],
                $this->constants['ajaxRequest'],
                $this->constants['namespaces'],
                $this->constants['moods']
            ]
        );

        $this->supplied_objects = [
            'IC'   => Settings::assign_intrusionCountermeasures(),
            'SET'  => Settings::assign_self(),
            'BOOT' => Settings::assign_bootloader(),
            'AREA' => $this->environment
        ];
    }

    public function assign_directory()
    {
        if( $this->environment->{AjaxRequest::IS_AJAX_REQUEST} ) {
            $directory = BASEPATH . "[{$this->environment->{Environment::_SUBSYSTEM}}]/_ajax/{$this->environment->{AjaxRequest::AR_LOCATION}}.php";
        } else {
            $directory = BASEPATH . "[{$this->environment->{Environment::_SUBSYSTEM}}]/{$this->environment->{Environment::_DIRECTORY}}.php";
        }

        $this->load_directory($directory);
    }

    private function load_directory($directory)
    {
        $constants_file = BASEPATH . "[{$this->environment->{Environment::_SUBSYSTEM}}]/_configuration/constants.php";

        if( file_exists($constants_file) ) {
            include($constants_file);
        }

        extract($this->supplied_constants, EXTR_PREFIX_ALL, 'C');
        extract($this->supplied_objects, EXTR_PREFIX_ALL, '');

        if( !file_exists($directory) ) {
            throw new notFoundException("Unable to locate requested directory file");
        } else {
            require_once($directory);
        }
    }

    private function load_inter($type, $inter_name, $supplied_data = NULL)
    {
        extract($this->supplied_constants, EXTR_PREFIX_ALL, 'C');
        extract($this->supplied_objects, EXTR_PREFIX_ALL, '');

        $inter = BASEPATH . "[{$this->environment->{Environment::_SUBSYSTEM}}]/_inter{$type}s/{$inter_name}.php";

        if( !file_exists($inter) ) {
            throw new coreException("Unable to locate requested inter file '{$inter_name}'");
        } else {
            switch($type)
            {
                case 'layer':
                    ob_start();
                    include($inter);
                    return ob_get_clean();
                break;

                case 'worker':
                    return include($inter);
                break;

                default:
                    throw new coreException("Unknown inter file type");
                break;
            }
        }
    }

    private function load_resource($resource_path, $use_alpha = NULL)
    {
        if( $use_alpha ) {
            $resource_path = "[" . Setup::SUBSYSTEM_alpha . "]/{$resource_path}";
        } else {
            $resource_path = "[{$this->environment->{Environment::_SUBSYSTEM}}]/{$resource_path}";
        }

        if( !file_exists(BASEPATH . "{$resource_path}") ) {
            //throw new coreException("File path '{$resource_path}' does not exist");
        } else {
            if( $use_alpha ) {
                // TODO: change on production
                return "http://" . Setup::SUBSYSTEM_alpha . ".dev/{$resource_path}";
            } else {
                return $this->get_current_link($resource_path);
            }
        }
    }

    private function redirect_and_exit($directory)
    {
        http_response_code(403);
		header("Location: {$directory}");
        exit;
    }

    private function soft_redirect_and_exit($directory)
    {
        http_response_code(302);
		header("Location: {$directory}");
        exit;
    }

    private function cancel_ajax_request($error_message = NULL)
    {
        http_response_code(400);
        exit( $error_message );
    }

    private function satisfy_ajax_request($success_message = NULL)
    {
        http_response_code(200);
        exit( $success_message );
    }

    private function get_current_link($postfix = NULL, $prefix = NULL)
    {
        if( $postfix &&
            (trim($postfix, '/') === Settings::available(Environment::_DIRECTORY, 0))) {
            $postfix = NULL;
        }

        if( $prefix &&
            (trim($prefix, '/') === Settings::available(Environment::_LANGUAGE, 0)) ) {
            $prefix = NULL;
        }

        return "http://" . (( $prefix ) ? "{$prefix}." : "") . SUBSYSTEM_LINK . (( $postfix ) ? "/{$postfix}" : "");
    }
}
?>
