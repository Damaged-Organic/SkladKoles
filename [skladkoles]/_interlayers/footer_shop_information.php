<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_footer_shop_information = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<h2><?=$xml_footer_shop_information->headline?></h2>
<small><?=$xml_footer_shop_information->shop_name?></small>
<p><?=$xml_footer_shop_information->working_hours?></p>
<?php foreach($supplied_data as $value): ?>
    <span class="work"><?=$value['working_hours']?></span>
<?php endforeach; ?>
<div class="social-likes" data-counters="no">
    <div class="facebook" title="<?=$xml_footer_shop_information->share_link?> <?=$xml_footer_shop_information->facebook?>"></div>
    <div class="twitter" title="<?=$xml_footer_shop_information->share_link?> <?=$xml_footer_shop_information->twitter?>"></div>
    <div class="vkontakte" title="<?=$xml_footer_shop_information->share_link?> <?=$xml_footer_shop_information->vkontakte?>"></div>
    <div class="plusone" title="<?=$xml_footer_shop_information->share_link?> <?=$xml_footer_shop_information->google_plus?>"></div>
</div>