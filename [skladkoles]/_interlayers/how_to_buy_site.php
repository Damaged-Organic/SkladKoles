<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $how_to_buy_steps = $supplied_data;
}

$xml_how_to_buy_site = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<h2><?=$xml_how_to_buy_site->headline?></h2>
<?php foreach($how_to_buy_steps as $value): ?>
    <div class="item">
        <div class="cell">
            <span class="number"><?=$value['record_order']?></span>
            <h2><?=$value['title']?></h2>
            <p><?=$value['text']?></p>
        </div>
        <div class="cell">
            <figure>
                <a href="<?=$this->load_resource("how-to-buy/{$value['image']}")?>" data-alt="picture of step <?=$value['record_order']?>" class="lightbox">
                    <img src="<?=$this->load_resource("how-to-buy/{$value['image']}")?>" alt="picture of step <?=$value['record_order']?>">
                </a>
            </figure>
        </div>
    </div>
<?php endforeach; ?>