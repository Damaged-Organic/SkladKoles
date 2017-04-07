<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( isset($supplied_data) ) {
    $cart_items_layer = $this->load_inter('layer', 'cart_items', $supplied_data);
}

$xml_basket_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$argument_0 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[0]}" : NULL;
$argument_1 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[1]}" : NULL;
$data_landmark = json_encode(
    [
        $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
        $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}{$argument_0}{$argument_1}", $_AREA->{$C_E::_LANGUAGE}),
        $C_AR::AR_LOCATION => "cart",
        $C_AR::AR_METHOD   => "act"
    ],
    JSON_UNESCAPED_SLASHES
);
?>
<span class="close"><?=$xml_basket_popup->close_popup?></span>
<div class="track-lane gaps to-right"><h2><?=$xml_basket_popup->headline?></h2></div>
<div class="centered">
    <div class="tabs">
        <ul>
            <li class="tabs-label" data-step="0"><?=$xml_basket_popup->steps->first?></li>
            <li class="tabs-label disabled" data-step="1"><?=$xml_basket_popup->steps->second?></li>
            <li class="tabs-label disabled" data-step="2"><?=$xml_basket_popup->steps->third?></li>
            <li class="tabs-label disabled" data-step="3"><?=$xml_basket_popup->steps->fourth?></li>
        </ul>
        <div class="tabs-content goods">
            <h2><?=$xml_basket_popup->cart_subheadline?></h2>
            <div class="basket-goods-grid" data-landmark='<?=$data_landmark?>'>
                <!--CART_ITEMS-->
                <?=$cart_items_layer?>
                <!--/CART_ITEMS-->
            </div>
        </div>
        <!--CART_ITEMS-->
        <?=$this->load_inter('layer', 'order_form')?>
        <!--/CART_ITEMS-->
        <div class="tabs-content result"></div>
    </div>
</div>