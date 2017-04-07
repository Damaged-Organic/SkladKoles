<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_order_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$argument_0 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[0]}" : NULL;
$argument_1 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[1]}" : NULL;
$data_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}{$argument_0}{$argument_1}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "order",
    $C_AR::AR_METHOD   => NULL
];

$encode_landmark = function($landmark) {
    return json_encode($landmark, JSON_UNESCAPED_SLASHES);
}
?>
<?php $data_landmark[$C_AR::AR_METHOD] = "make_order"; ?>
<form action="php/order.php" method="POST" id="orderForm" data-landmark='<?=$encode_landmark($data_landmark)?>' autocomplete="off" data-step="3">
    <div class="tabs-content contacts">
        <h2><?=$xml_order_popup->headline->step_1?></h2>
        <label for="userName"><?=$xml_order_popup->input->name->label?>*</label>
        <input type="text" name="<?=$C_E::_REQUEST?>[userName]" value="" placeholder="<?=$xml_order_popup->input->name->placeholder?>..." id="userName" data-rule-required="true">
        <label for="userEmail"><?=$xml_order_popup->input->email->label?>*</label>
        <input type="email" name="<?=$C_E::_REQUEST?>[userEmail]" value="" placeholder="<?=$xml_order_popup->input->email->placeholder?>..." id="userEmail" data-rule-required="true" data-rule-email="true">
        <label for="userPhone"><?=$xml_order_popup->input->phone->label?>*</label>
        <input type="tel" name="<?=$C_E::_REQUEST?>[userPhone]" value="" placeholder="<?=$xml_order_popup->input->phone->placeholder?>..." id="userPhone" data-rule-required="true">
        <p>* <?=$xml_order_popup->all_fields_required?></p>
		<!--GA-->
        <a href="#" class="nextStep validateStep" data-step="2" onClick="javascript: ga('send', 'event', 'button', 'click', 'contacts');"><?=$xml_order_popup->next_step?></a>
    </div>
    <?php $data_landmark[$C_AR::AR_METHOD] = "delivery_type"; ?>
    <div class="tabs-content delivery" data-landmark='<?=$encode_landmark($data_landmark)?>'>
        <h2><?=$xml_order_popup->headline->step_2?></h2>
        <input type="radio" name="<?=$C_E::_REQUEST?>[deliveryType]" value="pickup" id="pickup" checked="true" class="deliveryType" data-type="pickup">
        <label for="pickup"><span></span><?=$xml_order_popup->pickup?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[deliveryType]" value="shipping" id="shipping" class="deliveryType" data-type="shipping">
        <label for="shipping"><span></span><?=$xml_order_popup->shipping?></label>
        <div class="delivery-content">
            <!--DELIVERY_PICKUP-->
            <?=$this->load_inter('layer', 'delivery_pickup')?>
            <!--/DELIVERY_PICKUP-->
        </div>
		<!--GA-->
        <button type="submit" onClick="javascript: ga('send', 'event', 'button', 'click', 'success');"><?=$xml_order_popup->submit_order?></button>
    </div>
</form>