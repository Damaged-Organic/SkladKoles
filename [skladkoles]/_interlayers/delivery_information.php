<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_delivery_information = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<section class="delivery">
    <h1><?=$xml_delivery_information->headline?></h1>
    <p><?=$xml_delivery_information->body->item_1?></p>
    <p><?=$xml_delivery_information->body->item_2?></p>
    <!--<p><?=$xml_delivery_information->body->item_3?></p>-->
    <ul>
        <li><?=$xml_delivery_information->list->item_1?></li>
        <li><?=$xml_delivery_information->list->item_2?></li>
        <li><?=$xml_delivery_information->list->item_3?></li>
    </ul>
    <p class="orange"><?=$xml_delivery_information->bottomline?></p>
</section>