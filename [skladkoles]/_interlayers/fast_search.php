<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    $search_results = NULL;
} else {
    $search_results = $supplied_data;
}

if( $search_results ): ?>
    <ul>
    <?php foreach( $search_results as $value ): ?>
        <li>
            <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}")?>">
                <figure>
                    <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
                    <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
                </figure>
                <p class="title">
                    <?php
                        $title = (( !empty($value['model_name']) ) ? $value['model_name'] : NULL)
                            . (( !empty($value['code']) ) ? " " . $value['code'] : NULL)
                            . (( !empty($value['paint']) ) ?  " " . $value['paint'] : NULL);
                    ?>
                    <span><?="{$value['brand']}"?></span>
                    <?=( mb_strlen($title, 'utf-8') > 15 ) ? mb_substr($title, 0, 15, 'utf-8')."..." : $title;?>
                </p>
                <p class="price">
                    <?php if( $value['promo'] ): ?>
                        от <span>UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?></span>
                    <?php else: ?>
                        от <span>UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                    <?php endif ?>
                </p>
            </a>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>