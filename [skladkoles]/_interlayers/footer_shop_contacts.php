<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($addresses, $phones) = $supplied_data;
}

$xml_footer_shop_contacts = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<h2><?=$xml_footer_shop_contacts->headline?></h2>
<address>
    <?php foreach($addresses as $value): ?>
        <p><?=$value['address']?></p>
    <?php endforeach; ?>
    <?php foreach($phones as $value): ?>
        <a href="tel:<?=str_replace(["(", ")", "-", " "], "", $value['phone'])?>"><?=$value['phone']?></a>
    <?php endforeach; ?>
</address>