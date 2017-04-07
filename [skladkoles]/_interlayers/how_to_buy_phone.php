<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_how_to_buy_phone = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<article class="item">
    <h2><?=$xml_how_to_buy_phone->headline?></h2>
    <p><?=$xml_how_to_buy_phone->body->item_1?></p>
    <p><?=$xml_how_to_buy_phone->body->item_2?></p>
    <p><?=$xml_how_to_buy_phone->body->item_3?></p>
</article>