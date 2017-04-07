<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $items_main_promo = $supplied_data;
}

$xml_items_main = $_BOOT->involve_object("XML_Handler")->get_xml(
    'items_main',
    $_AREA->{$C_E::_LANGUAGE}
);

if( empty($items_main_promo) ): ?>
    <p class="empty"><?=$xml_items_main->no_data?></p>
<?php else:
    foreach($items_main_promo as $value): ?>
    <div class="item flexySlide">
        <div class="ribbon">
            <img src="<?=$this->load_resource("images/ribbon.png")?>" alt="promotion">
        </div>
        <figure>
            <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
            <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
        </figure>
        <p>
            <?="{$value['brand']}"?>
        </p>
        <h2>
            <?=( mb_strlen($value['model_name'], 'utf-8') > 15 ) ? mb_substr($value['model_name'], 0, 15, 'utf-8')."..." : $value['model_name']?>
            <?=( !empty($value['code']) ) ? $value['code'] : NULL?>
            <?=( !empty($value['paint']) ) ? $value['paint'] : NULL?>
        </h2>
        <div class="price">
            <?php if( $value['promo'] ): ?>
                <span class="old">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                <span class="from">от</span>
                <span>
                    UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?>
                </span>
            <?php else: ?>
                <span class="from">от</span>
                <span>
                    UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?>
                </span>
            <?php endif; ?>
        </div>
        <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}")?>"><?=$xml_items_main->button_details?></a>
    </div>
    <?php endforeach;
endif; ?>