<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);
?>
<title>DEM "Sklad Koles"</title>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="icon" type="image/png" href="<?=$this->load_resource("images/favicon.png")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/jquery-ui.min.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/quill.snow.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/editor.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/stabilizer.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/cSelect.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/settings.css")?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/main.css")?>">