<?php
namespace _meat\classes\external;

use coreException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler, PDO;

class PHPLoginLink
{
    private $db_handler  = NULL;
    private $users_table = NULL;

    function __construct(DB_Handler $db_handler, $request_data)
    {
        $this->db_handler  = $db_handler;
        $this->users_table = Setup::DB_PREFIX_beta."users";

        //library
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            exit('Sorry, this script does not run on a PHP version smaller than 5.3.7 !');
        } else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            require_once(dirname(__FILE__) . "/PHPLogin/libs/password_compatibility_library.php");
        }

        //configuration
        require_once dirname(__FILE__) . "/PHPLogin/config/config.php";

        //language
        require_once dirname(__FILE__) . "/PHPLogin/config/ru.php";

        require_once dirname(__FILE__) . "/PHPLogin/PHPLogin.php";

        $this->login_instance = new \PHPLogin($this->db_handler, $this->users_table, $request_data);
    }

    public function is_user_logged_in()
    {
        return $this->login_instance->isUserLoggedIn();
    }

    public function get_errors()
    {
        return $this->login_instance->errors;
    }

    public function get_messages()
    {
        return $this->login_instance->messages;
    }
}
?>