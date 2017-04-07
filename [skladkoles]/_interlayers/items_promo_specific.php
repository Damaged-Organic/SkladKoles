<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    $promotion_items = NULL;
} else {
    $promotion_items = $supplied_data;
}

if( !empty($promotion_items)): ?>
<div class="track-lane gaps to-right"><h2>Акционные товары</h2></div>
<div class="catalog onMain">
    <div class="centered">
        <?php foreach($promotion_items as $value): ?>
            <div class="item-container">
                <div class="item">
                    <?php if( !empty($value['promotion_id']) || !empty($value['promo']) ): ?>
                        <div class="ribbon">
                            <img src="<?=$this->load_resource("images/ribbon.png")?>" alt="promotion">
                        </div>
                    <?php endif; ?>
                    <?php if( !empty($value['is_top']) && $value['is_top'] == 'Y' ): ?>
                        <div class="top">
                            <img src="<?=$this->load_resource("images/top.png")?>" alt="top">
                        </div>
                    <?php endif; ?>
                    <figure>
                        <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
                        <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}")?>">
                            <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
                        </a>
                    </figure>
                    <p>
                        <?="{$value['brand']}"?>
                    </p>
                    <?php
                        $title = (( !empty($value['model_name']) ) ? $value['model_name'] : NULL)
                            . (( !empty($value['code']) ) ? " " . $value['code'] : NULL)
                            . (( !empty($value['paint']) ) ?  " " . $value['paint'] : NULL);
                    ?>
                    <h2>
                        <?=( mb_strlen($title, 'utf-8') > 20 ) ? mb_substr($title, 0, 20, 'utf-8')."..." : $title;?>
                    </h2>
                    <div class="rating outer">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <span class="star <?=( round($value['rating_score']) >= $i ) ? 'active' : ''; ?>"></span>
                        <?php endfor; ?>
                    </div>
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
                    <?php if( defined('WHITELIGHT') ): ?>
                        <a href="<?=$this->get_current_link("item/{$value['type']}/{$value['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать данные товара" target="_blank"></a>
                    <?php endif; ?>
                </div>
                <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}")?>"><i class="fa fa-list-ul"></i>детальнее</a>
            </div>
        <?php endforeach ?>
    </div>
</div>
<?php endif ?>