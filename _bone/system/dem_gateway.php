<?php
namespace _bone\system;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use bootException, coreException, notFoundException;

use _bone\classes\containers\constantsEnvironment
    as Environment,
    _bone\classes\containers\constantsAjaxRequest
    as AjaxRequest;

use _bone\system\DEM_Settings
    as Settings;

class DEM_Gateway
{
    private $host = NULL;
    private $path = NULL;

    private $request_data = NULL;

    public function __construct()
    {
        list(
            $this->host,
            $this->path
        ) = $this->get_url();

        $this->request_data = $this->get_request_data();
    }

    # ------------------------------------------
    # STAGE 1                                  |
    # ------------------------------------------

    private function get_url()
    {
        $url = parse_url($this->get_current_clean_url());

        if( empty($url['host']) ) {
            throw new bootException("Undefined host");
        } else {
            $host = $this->parse_url_host($url['host']);
            $path = $this->parse_url_path($url['path']);
        }

        return [$host, $path];
    }

    private function get_current_clean_url()
    {
        $url = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        return ( $url === filter_var($url, FILTER_VALIDATE_URL)) ? $url : FALSE;
    }

    private function parse_url_host($host_string)
    {
        $available_hosts = Settings::essential(Environment::_DOMAIN);

        $find_host_key = function($host) use($available_hosts)
        {
            foreach($host as $key => $value)
            {
                if( in_array($value, $available_hosts, TRUE) ) {
                    return $key;
                }
            }

            throw new coreException("Domain name not recognized");
        };

        $host = explode('.', $host_string);

        if( ($www_key = array_search('www', $host)) !== FALSE ) {
            unset($host[$www_key]);
        }

        $host = array_slice($host, 0, $find_host_key($host)+1);

        return array_reverse($host);
    }

    private function parse_url_path($path_string)
    {
        $path = explode('/', trim($path_string, '/'));

        return $path;
    }

    # ------------------------------------------
    # STAGE 2                                  |
    # ------------------------------------------

    private function get_request_data()
    {
        $request_data = [];

        if( !empty($_GET) ) {
            $request_data += $_GET;
        }

        if( !empty($_POST) ) {
            $request_data += $_POST;
        }

        unset($_REQUEST, $_GET, $_POST);

        if( !empty($request_data[Environment::_REQUEST]) ) {
            $environment_request = $request_data[Environment::_REQUEST];
        } elseif( !empty($request_data) ) {
            $environment_request = $request_data;
        } else {
            $environment_request = NULL;
        }

        Settings::set_environment_parameters([
            Environment::_REQUEST => $environment_request
        ]);

        return $request_data;
    }

    # ------------------------------------------
    # STAGE 3                                  |
    # ------------------------------------------

    public function process_environment()
    {
        if( empty($this->host) ) {
            throw new bootException("Undefined host property");
        }

        Settings::set_environment_parameters([
            Environment::_DOMAIN    => $this->collect_verified_host_domain($this->host),
            Environment::_SUBSYSTEM => $this->collect_verified_host_sybsystem($this->host)
        ]);

        Settings::set_environment_parameters([
            Environment::_LANGUAGE  => $this->collect_verified_host_language($this->host),
            Environment::_DIRECTORY => ($match_directory = $this->collect_verified_path_directory($this->path)),
            Environment::_ARGUMENTS => ($match_arguments = $this->collect_verified_path_arguments($this->path))
        ]);

        $this->match_url_request_pattern($match_directory, $match_arguments);

        return $this;
    }

    private function collect_verified_host_domain(array $host_data)
    {
        $verified_domain = NULL;

        if( empty($host_data[0]) ) {
            throw new bootException("Domain name could not be empty");
        } else {
            if( ($key = array_search($host_data[0], Settings::essential(Environment::_DOMAIN), TRUE)) !== FALSE ) {
                $verified_domain = Settings::essential(Environment::_DOMAIN, $key);
            } else {
                throw new bootException("Unknown domain parameter");
            }
        }

        return $verified_domain;
    }

    private function collect_verified_host_sybsystem(array $host_data)
    {
        $verified_subsystem = Settings::essential(Environment::_SUBSYSTEM, 0);

        if( !empty($host_data[1]) )
        {
            /*TODO: KILL THIS ON PROD*/
            /*$host_data = array_filter($host_data, function($item) {
                return $item != 'test';
            });

            $host_data = array_values($host_data);*/
            /*END TODO: KILL THIS ON PROD*/

            if( !in_array($host_data[1], Settings::essential(Environment::_SUBSYSTEM), TRUE) &&
                !empty($host_data[2]) ) {
                throw new notFoundException("Unable to verify host sybsystem");
            }

            if( ($key = array_search($host_data[1], Settings::essential(Environment::_SUBSYSTEM), TRUE)) !== FALSE ) {
                $verified_subsystem = Settings::essential(Environment::_SUBSYSTEM, $key);
            }
        }

        return $verified_subsystem;
    }

    private function collect_verified_host_language(array $host_data)
    {
        $verified_language = Settings::available(Environment::_LANGUAGE, 0);

        if( !empty($host_data[1]) )
        {
            if( (!in_array($host_data[1], Settings::essential(Environment::_SUBSYSTEM), TRUE) &&
                 !in_array($host_data[1], Settings::available(Environment::_LANGUAGE), TRUE)) ||
                (in_array($host_data[1], Settings::available(Environment::_LANGUAGE), TRUE) &&
                 !empty($host_data[2])) ) {
                throw new notFoundException("Unable to verify host language (or subsystem is not set)");
            }

            if( ($key = array_search($host_data[1], Settings::available(Environment::_LANGUAGE), TRUE)) !== FALSE ) {
                $verified_language = Settings::available(Environment::_LANGUAGE, $key);
            }
        }

        if( !empty($host_data[2]) )
        {
            if( ($key = array_search($host_data[2], Settings::available(Environment::_LANGUAGE))) !== FALSE ) {
                $verified_language = Settings::available(Environment::_LANGUAGE, $key);
            } else {
                throw new notFoundException("Unknown language parameter");
            }
        };

        return $verified_language;
    }

    private function collect_verified_path_directory(array $path_data)
    {
        $verified_directory = Settings::available(Environment::_DIRECTORY, 0);

        if( !empty($path_data[0]) )
        {
            if( ($key = array_search($path_data[0], Settings::available(Environment::_DIRECTORY), TRUE)) !== FALSE ) {
                $verified_directory = Settings::available(Environment::_DIRECTORY, $key);
            } else {
                throw new notFoundException("Unknown directory parameter");
            }
        }

        return $verified_directory;
    }

    private function collect_verified_path_arguments(array $path_data)
    {
        $verified_arguments = [];

        if( count($path_data) > 1 ) {
            $verified_arguments = array_slice($path_data, 1);
        }

        return $verified_arguments;
    }

    private function match_url_request_pattern($match_directory, $match_arguments)
    {
        $url_request = $match_directory . (( !empty($match_arguments) ) ? "/" . implode('/', $match_arguments) : NULL);

        foreach(Settings::get_request_patterns() as $pattern)
        {
            if( preg_match($pattern, urldecode($url_request)) ) {
                return TRUE;
            }
        }

        throw new notFoundException("URL request pattern mismatch");
    }

    # ------------------------------------------
    # STAGE 4                                  |
    # ------------------------------------------

    public function process_ajaxRequest()
    {
        if( Settings::is_ajax_request() )
        {
            if( empty($this->request_data) ) {
                throw new bootException("Undefined request_data property");
            }

            $this->request_data = $this->collect_verified_ajax_data($this->request_data);

            Settings::set_environment_parameters([
                AjaxRequest::IS_AJAX_REQUEST => TRUE,
                AjaxRequest::AR_LOCATION     => $this->request_data[AjaxRequest::AR_LOCATION],
                AjaxRequest::AR_METHOD       => $this->request_data[AjaxRequest::AR_METHOD]
            ]);
        } else {
            Settings::set_environment_parameters([
                AjaxRequest::IS_AJAX_REQUEST => FALSE
            ]);
        }

        return $this;
    }

    private function collect_verified_ajax_data(array $request_data)
    {
        $verified_request_data = [];

        if( empty($request_data[AjaxRequest::AR_LOCATION]) || empty($request_data[AjaxRequest::AR_METHOD]) ) {
            throw new notFoundException("Undefined required AJAX parameters");
        } else {
            if( ($key = array_search($request_data[AjaxRequest::AR_LOCATION], Settings::available(AjaxRequest::AR_LOCATION), TRUE)) !== FALSE ) {
                $verified_request_data[AjaxRequest::AR_LOCATION] = Settings::available(AjaxRequest::AR_LOCATION, $key);
            } else {
                throw new notFoundException("Unknown AR location parameter");
            }

            if( ($key = array_search($request_data[AjaxRequest::AR_METHOD], Settings::available(AjaxRequest::AR_METHOD), TRUE)) !== FALSE ) {
                $verified_request_data[AjaxRequest::AR_METHOD] = Settings::available(AjaxRequest::AR_METHOD, $key);
            } else {
                throw new notFoundException("Unknown AR method parameter");
            }
        }

        return $verified_request_data;
    }
}
?>
