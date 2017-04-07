<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use _bone\system\DEM_Settings
    as Settings;

use _bone\classes\containers\constantsEnvironment
    as Environment;

$default_link = "http://skladkoles.dev";
$default_path = "{$default_link}/[" . Settings::get_environment_parameters()->{Environment::_SUBSYSTEM} . "]";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
<head>
    <title>Sklad Koles</title>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="icon" type="image/png" href="<?="{$default_path}/images/favicon.png"?>">
    <link rel="stylesheet" href="<?="{$default_path}/css/stabilizer.css"?>">
    <link rel="stylesheet" href="<?="{$default_path}/css/main.css"?>">
</head>
<body>
<div class="page notFound">
    <header>
        <a href="<?=$default_link?>" title="back to main page">
            <img src="<?="{$default_path}/images/logo-white.png"?>" alt="wheels logo">
        </a>
    </header>
    <section>
        <h1>СТРАНИЦА НЕ НАЙДЕНА</h1>
        <h2>ВОЗНИКЛА ОШИБКА. ЗАПРАШИВАЕМОЙ СТРАНИЦЫ НЕ СУЩЕСТВУЕТ</h2>
        <div class="circle"><span>404</span></div>
    </section>
</div>
</body>
</html>
