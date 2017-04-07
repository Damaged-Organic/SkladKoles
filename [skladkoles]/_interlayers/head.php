<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data['item_head_data_filter']) ) {
    $title       = $supplied_data['item_head_data_filter']['title'];
    $description = $supplied_data['item_head_data_filter']['description'];
} else {
    $title       = $supplied_data['title'];
    $description = $supplied_data['description'];
}
?>
<title><?=$title?></title>
<meta charset="UTF-8">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="Keywords" content="<?=$supplied_data['keywords']?>">
<meta name="Description" content="<?=$description?>">

<meta property="og:title" content="<?=$title?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?=$this->get_current_link($_AREA->{$C_E::_DIRECTORY}.( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ? "/{$_AREA->{$C_E::_ARGUMENTS}[0]}" : NULL ).( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ? "/{$_AREA->{$C_E::_ARGUMENTS}[1]}" : NULL ), $_AREA->{$C_E::_LANGUAGE})?>">

<?php if( !empty($supplied_data['news_single']) ): ?>
    <meta property="og:description" content="<?=mb_substr(strip_tags($supplied_data['news_single'][0]['text']), 0, 64, 'utf-8')?>...">
<?php else: ?>
    <meta property="og:description" content="<?=$description?>">
<?php endif; ?>

<?php if( !empty($supplied_data['news_single']) ): ?>
    <meta property="og:image" content="<?=$this->load_resource("news/{$supplied_data['news_single'][0]['image']}")?>">
<?php else: ?>
    <meta property="og:image" content="<?=$this->load_resource("images/og_image.jpg")?>">
<?php endif; ?>

<link rel="icon" type="image/png" href="<?=$this->load_resource("images/favicon.png")?>">

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?=$this->load_resource("css/stabilizer.css") ?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/common.css") ?>">
<link rel="stylesheet" href="<?=$this->load_resource("css/main.css") ?>">
