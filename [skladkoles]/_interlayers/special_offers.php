<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    return FALSE;
} else {
    $special_offers = $supplied_data;
}
?>
<div class="centered special-offers">
    <div id="sale">
        <div class="pictureContainer">
            <a href="<?=$this->get_current_link('promotion_details')?>/<?=$special_offers[0]['id']?>" class="saleLink">
                <figure><img src="<?=$this->load_resource("slider/{$special_offers[0]['image']}")?>" alt="slide <?=$special_offers[0]['id']?>"></figure>
            </a>
        </div>
        <div class="nav">
            <?php foreach($special_offers as $value): ?>
                <figure data-text="посмотреть" class="point"><img src="<?=$this->load_resource("slider/{$value['image']}")?>" alt="slide <?=$value['id']?>" data-link="<?=$this->get_current_link('promotion_details')?>/<?=$value['id']?>"></figure>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="more-button"><a href="<?=$this->get_current_link('promotions')?>">больше</a></div>
    <?php if( defined('WHITELIGHT') ): ?>
        <a href="<?=$this->get_current_link("promotions", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать данные товара" target="_blank"></a>
    <?php endif; ?>
</div>