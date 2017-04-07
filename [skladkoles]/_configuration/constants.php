<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

define("SUBSYSTEM_LINK", "skladkoles.dev");

define("DB_HOST", "localhost");
define("DB_NAME", "cheersun_wheels");
define("DB_USER", "root");
define("DB_PASS", "root");

define("DB_CSET", "utf8");

define("NO_REPLY_EMAIL", "no-reply@skladkoles.com.ua");
define("FEEDBACK_EMAIL", "feedback@skladkoles.com.ua");
define("ORDER_EMAIL", "order@skladkoles.com.ua");
?>
