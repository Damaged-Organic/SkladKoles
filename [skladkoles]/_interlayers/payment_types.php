<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $payment_types = $supplied_data;
}

$xml_payment_types = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<section class="payments">
    <h1><?=$xml_payment_types->headline?></h1>
    <?php foreach($payment_types as $value): ?>
        <div class="item">
            <figure><img src="<?=$this->load_resource("images/{$value['icon']}")?>" alt="cash type"></figure>
            <h2><?=$value['title']?></h2>
            <p><?=$value['text']?></p>
        </div>
    <?php endforeach; ?>
</section>