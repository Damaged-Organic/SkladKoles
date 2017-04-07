<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $regions = $supplied_data;
}

$xml_delivery_information = $_BOOT->involve_object("XML_Handler")->get_xml(
    'delivery_information',
    $_AREA->{$C_E::_LANGUAGE}
);

$xml_delivery_shipping = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<a href="#" class="region-button"><?=$xml_delivery_shipping->choose_region?></a>
<label for="city"><?=$xml_delivery_shipping->input->city->label?>*</label>
<input type="text" name="_request[city]" value="" id="city" placeholder="<?=$xml_delivery_shipping->input->city->placeholder?>..." data-rule-required="true">
<label for="address"><?=$xml_delivery_shipping->input->address->label?>*</label>
<input type="text" name="_request[address]" value="" id="address" placeholder="<?=$xml_delivery_shipping->input->address->placeholder?>..." data-rule-required="true">
<p class="field-desc">* <?=$xml_delivery_shipping->all_fields_required?></p>
<div class="region">
<?php foreach($regions as $value): ?>
    <input type="radio" name="<?=$C_E::_REQUEST?>[region]" value="<?=$value['id']?>" id="<?=$value['id']?>" data-region="<?=$value['region']?>">
    <label for="<?=$value['id']?>">
        <figure>
            <img src="<?=$this->load_resource("regions/{$value['image']}")?>" alt="<?=$value['image']?>">
            <figcaption><?=$value['region']?></figcaption>
        </figure>
    </label>
<?php endforeach; ?>
</div>
<div class="shipping-rules">
    <p><?=$xml_delivery_information->body->item_1?></p>
    <p><?=$xml_delivery_information->body->item_2?></p>
    <p><?=$xml_delivery_information->body->item_3?></p>
    <ul>
        <li><?=$xml_delivery_information->list->item_1?></li>
        <li><?=$xml_delivery_information->list->item_2?></li>
        <li><?=$xml_delivery_information->list->item_3?></li>
    </ul>
    <p class="colorized"><?=$xml_delivery_information->bottomline?></p>
</div>