<?php
namespace _bone\system;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use bootException;

use _bone\system\DEM_Settings
    as Settings;

class IntrusionCountermeasures
{
    const IC_TOKEN = "IC_token";

    function __construct()
    {
        $this->csrf_verify_security_token();
    }

    private function csrf_verify_security_token()
    {
        if( !isset($_SESSION[self::IC_TOKEN]) ) {
            $_SESSION[self::IC_TOKEN] = $this->csrf_generate_security_token();
        }

        if( empty($_REQUEST[self::IC_TOKEN]) ) {
            unset($_REQUEST, $_POST);
        } else {
            $combined_token = [
                'original' => hash(
                    'sha512',
                    session_id() . $_SESSION[self::IC_TOKEN]['hash'] . $_SESSION[self::IC_TOKEN]['time']
                ),
                'request' => hash(
                    'sha512',
                    session_id() . $_REQUEST[self::IC_TOKEN] . $_SESSION[self::IC_TOKEN]['time']
                )
            ];

            if( $combined_token['original'] !== $combined_token['request'] ) {
                unset($_REQUEST, $_POST);

                //throw new bootException("SECURITY BREACH: request token mismatch");
            } /*elseif( $_SESSION[self::IC_TOKEN]['time'] <= microtime(TRUE) ) {
                unset($_REQUEST, $_POST);

                //throw new bootException("SECURITY BREACH: request token expired");
            }*/ else {
                unset($_REQUEST[self::IC_TOKEN], $_POST[self::IC_TOKEN]);
            }
        }

        if( !Settings::is_ajax_request() ) {
            //$_SESSION[self::IC_TOKEN] = $this->csrf_generate_security_token();
        }
    }

    private function csrf_generate_security_token()
    {
        return [
            'hash' => uniqid(mt_rand(), TRUE),
            'time' => microtime(TRUE) + 3600
        ];
    }

    public function is_ajax_cooled_down($cool_down)
    {
        if( isset($_SESSION['IC']['last_AR_time']) ) {
            $cooled_down = (round((microtime(TRUE) - $_SESSION['IC']['last_AR_time']), 1) >= $cool_down) ? TRUE : FALSE;
        } else {
            $cooled_down = TRUE;
        }

        $_SESSION['IC']['last_AR_time'] = microtime(TRUE);

        return $cooled_down;
    }

    public function xss_escape_output($output)
    {
        return htmlspecialchars(strip_tags(trim($output)), ENT_COMPAT | ENT_HTML5, 'UTF-8');
    }
}
?>
