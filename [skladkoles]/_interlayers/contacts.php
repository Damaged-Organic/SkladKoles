<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($working_hours, $addresses, $phones) = $supplied_data;
}

$xml_contacts = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<section class="centered">
    <h1><?=$xml_contacts->headline?></h1>
    <ul class="info">
        <li>
            <p><?=$xml_contacts->information->item_1?></p>
            <p>
                <strong>
                    <?=$xml_contacts->information->item_2?>
                </strong>
            </p>
        </li>
        <li>
            <h2><i class="fa fa-phone"></i><?=$xml_contacts->our_phones?></h2>
            <address class="phones">
                <?php foreach($phones as $value): ?>
                    <a href="tel:<?=str_replace(["(", ")", "-", " "], "", $value['phone'])?>"><?=$value['phone']?></a>
                <?php endforeach; ?>
            </address>
        </li>
        <li>
            <h2><i class="fa fa-location-arrow"></i><?=$xml_contacts->our_addresses?></h2>
            <address class="address">
                <?php foreach($addresses as $value): ?>
                    <p><?=$value['address']?></p>
                <?php endforeach; ?>
            </address>
        </li>
        <li>
            <h2><i class="fa fa-calendar"></i><?=$xml_contacts->working_hours?></h2>
            <?php foreach($working_hours as $value): ?>
                <span><?=$value['working_hours']?></span>
            <?php endforeach; ?>
        </li>
    </ul>
</section>