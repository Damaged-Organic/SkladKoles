<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use _bone\system\constants\constantsSetup
    as Setup;

use _bone\classes\containers\constantsEnvironment
    as Environment;

use _bone\system\DEM_Settings
    as Settings;

/**
 * Class bootException
 *
 * Handles system boot and bone level exceptions that might occur during system start,
 * prints raw "error/file/line output"
 * in specified source according to ERROR_MODE
 */
class bootException extends Exception
{
    private $directory_exceptions = "exceptions_log";

    public function handle_exception($error_name, $current_trace)
    {
        $error_data = [
            'name' => "BootEX: '{$error_name}'",
            'path'  => array_merge([
                'file'     => NULL,
                'line'     => NULL,
                'class'    => NULL,
                'type'     => NULL,
                'function' => NULL
            ], $current_trace)
        ];

        switch(Setup::ERROR_MODE)
        {
            case 'dev':
                $message = "[{$error_data['name']} in {$error_data['path']['file']} @ line {$error_data['path']['line']}]";
            break;

            case 'rel':
                $message = Setup::SYSTEM_FAILURE;

                $logfile = [
                    'path' => BASEPATH . "/{$this->directory_exceptions}/" . basename($error_data['path']['file'], '.php') . ".log",
                    'text' => date('d-m-Y H:i:s') . " [{$error_data['name']} in {$error_data['path']['file']} @ line ({$error_data['path']['line']})]" . PHP_EOL
                ];

                file_put_contents($logfile['path'], $logfile['text'], FILE_APPEND);
            break;

            default:
                $message = "Unknown error reporting mode";
            break;
        }

        exit($message);
    }
}

/**
 * Class coreException
 *
 * Handles meat exceptions in core classes which represent system logic,
 * prints layer-formatted "error/file/line/class/type/function" and backtrace output
 * in specified source according to ERROR_MODE
 */
class coreException extends Exception
{
    private $directory_exceptions = "exceptions_log";
    private $directory_bonelayers = "bonelayers";

    public function handle_exception($error_name, $back_trace)
    {
        $error_data = [
            'name'  => "CoreEX: '{$error_name}'",
            'path'  => array_merge([
                'file'     => NULL,
                'line'     => NULL,
                'class'    => NULL,
                'type'     => NULL,
                'function' => NULL
            ], $back_trace[0]),
            'trace' => []
        ];

        $back_trace = array_slice($back_trace, 1);

        foreach($back_trace as $back_trace_out)
        {
            $error_trace_string = "{$back_trace_out['file']} @ line ({$back_trace_out['line']}) ";

            if( !empty($back_trace_out['class']) ) {
                $error_trace_string .= "from {$back_trace_out['class']}{$back_trace_out['type']}";
            }

            if( !empty($back_trace_out['function']) ) {
                $error_trace_string .= $back_trace_out['function']."()";
            }

            $error_data['trace'][] = $error_trace_string;
        }

        switch(Setup::ERROR_MODE)
        {
            case 'dev':
                $message = NULL;

                require(BASEPATH . "/_bone/system/{$this->directory_bonelayers}/exceptions.php");
            break;

            case 'rel':
                $message = Setup::SYSTEM_FAILURE;

                $error = "[{$error_data['name']} in {$error_data['path']['file']} @ line ({$error_data['path']['line']}) from {$error_data['path']['class']}{$error_data['path']['type']}{$error_data['path']['function']}()]";
                $trace = ( !empty($error_data['trace']) ) ? "< |#| " . implode(' < |#| ', $error_data['trace']) : "";

                $logfile = [
                    'path' => BASEPATH . "/{$this->directory_exceptions}/" . strtolower($error_data['path']['class']) . ".log",
                    'text' => date('d-m-Y H:i:s') . " {$error} {$trace}" . PHP_EOL
                ];

                file_put_contents($logfile['path'], $logfile['text'], FILE_APPEND);
            break;

            default:
                $message = "Unknown error reporting mode";
            break;
        }

        exit($message);
    }
}

/**
 * Class procException
 *
 * Handles processing exceptions from interlayers and interworkers,
 * prints layer-formatted "error/file/line" and backtrace output
 * in specified source according to ERROR_MODE
 */
class procException extends Exception
{
    private $directory_exceptions = "exceptions_log";
    private $directory_bonelayers = "bonelayers";

    public function handle_exception($error_name, $current_trace, $back_trace)
    {
        $error_data = [
            'name'  => "ProcEX: '{$error_name}'",
            'path'  => array_merge([
                'file'     => NULL,
                'line'     => NULL,
                'class'    => NULL,
                'type'     => NULL,
                'function' => NULL
            ], $current_trace),
            'trace' => []
        ];

        foreach($back_trace as $back_trace_out)
        {
            $error_trace_string = "{$back_trace_out['file']} @ line ({$back_trace_out['line']}) ";

            if( !empty($back_trace_out['class']) ) {
                $error_trace_string .= "from ::{$back_trace_out['class']}{$back_trace_out['type']}";
            }
            if( !empty($back_trace_out['function']) ) {
                $error_trace_string .= $back_trace_out['function']."()";
            }

            $error_data['trace'][] = $error_trace_string;
        }

        switch(Setup::ERROR_MODE)
        {
            case 'dev':
                $message = NULL;

                require(BASEPATH . "/_bone/system/{$this->directory_bonelayers}/exceptions.php");
            break;

            case 'rel':
                $message = Setup::SYSTEM_FAILURE;

                $error = "[{$error_data['name']} in {$error_data['path']['file']} @ line ({$error_data['path']['line']})]";
                $trace = ( !empty($error_data['trace']) ) ? "< |#| " . implode(' < |#| ', $error_data['trace']) : "";

                $logfile = [
                    'path' => BASEPATH . "/{$this->directory_exceptions}/" . basename($error_data['path']['file'], '.php') . ".log",
                    'text' => date('d-m-Y H:i:s') . " {$error} {$trace}" . PHP_EOL
                ];

                file_put_contents($logfile['path'], $logfile['text'], FILE_APPEND);
            break;

            default:
                $message = "Unknown error reporting mode";
            break;
        }

        exit($message);
    }
}

class notFoundException extends Exception
{
    public function handle_exception($error_name)
    {
        $error_data = [
            'name' => "notFoundEX: '{$error_name}'"
        ];

        switch(Setup::ERROR_MODE)
        {
            case 'dev':
                $message = "[{$error_data['name']} - gateway could not properly handle the request]";
            break;

            case 'rel':
                $message = NULL;

                require("[" . Settings::get_environment_parameters()->{Environment::_SUBSYSTEM} . "]/404.php");
            break;

            default:
                $message = "Unknown error reporting mode";
            break;
        }

        exit($message);
    }
}
?>