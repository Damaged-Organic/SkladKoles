<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_delivery_pickup = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<p><?=$xml_delivery_pickup->information->item_1?></p>
<p><?=$xml_delivery_pickup->information->item_2?></p>
<p><?=$xml_delivery_pickup->information->item_3?></p>
<div class="delivery-exclusive">
    <p><?=$xml_delivery_pickup->information->item_4?></p>
</div>