<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$items_rims = [];

if( !empty($supplied_data['items']) && !empty($supplied_data['modifications']) )
{
    $transform_modifications = function(&$input_array)
    {
        $mediate_array = [];

        foreach($input_array as $value) {
            $mediate_array["{$value['brand']}_{$value['model_name']}_{$value['code']}_{$value['paint']}"][] = $value;
        }

        return $mediate_array;
    };

    if( $_AREA->{$C_E::_ARGUMENTS}[0] == "rims" ) {
        $rim_type = "rims";
    } elseif( $_AREA->{$C_E::_ARGUMENTS}[0] == "exclusive_rims" ) {
        $rim_type = "exclusive_rims";
    }

    $items_rims = array_map(
        function($item) use($_BOOT, $C_N, $rim_type) { return $item + $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->provide_item_type($rim_type); },
        $supplied_data['items']
    );

    $items_rims_modifications = $transform_modifications($supplied_data['modifications']);
}

$xml_items_rims = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

if( empty($items_rims) ): ?>
    <p class="empty"><?=$xml_items_rims->no_data?></p>
<?php else:
    foreach($items_rims as $value): ?>
    <div class="item-wrap">
        <div class="item">
            <?php if( !empty($value['promotion_id']) || !empty($value['promo']) ): ?>
                <div class="ribbon">
                    <img src="<?=$this->load_resource("images/ribbon.png")?>" alt="exclusive">
                </div>
            <?php elseif( $value['type'] == 'exclusive_rims' ): ?>
                <div class="ribbon">
                    <img src="<?=$this->load_resource("images/ribbon-exclusive.png")?>" alt="promotion">
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
                <?=( mb_strlen($title, 'utf-8') > 25 ) ? mb_substr($title, 0, 25, 'utf-8')."..." : $title;?>
            </h2>
            <h3>
                <?php if( !empty($value['rim_type']) ): ?>
                    (<?=$value['rim_type']?>)
                <?php endif; ?>
            </h3>
            <div class="rating outer">
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <span class="star <?=( round($value['rating_score']) >= $i ) ? 'active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>
            <div class="price">
            <?php if( !empty($value['promo']) || !empty($value['retail']) ): ?>
                <?php if( !empty($value['promo']) ): ?>
                    <span class="old">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                    <span class="from"><?=$xml_items_rims->from_label?></span>
                    <span>
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?>
                    </span>
                <?php else: ?>
                    <span class="from"><?=$xml_items_rims->from_label?></span>
                    <span>
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?>
                    </span>
                <?php endif; ?>
            <?php else: ?>
                <span class="from">цена не указана</span>
                <span></span>
            <?php endif; ?>
            </div>
            <p class="code"><span>ID:</span>R-<?=str_pad($value['unique_code'], 4, '0', STR_PAD_LEFT)?></p>
            <?php if( !empty($value['brand']) && (!empty($value['model_name']) || !empty($value['code'])) && !empty($value['paint']) ): ?>
                <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}")?>"><?=$xml_items_rims->button_detailed?></a>
            <?php endif; ?>
        </div>
        <div class="specifications">
            <h2><?=$xml_items_rims->modifications_label?></h2>
            <div class="spec-lists">
                <?php
                    unset($mods);

                    $sliced_items_rims_modifications = array_slice(
                        $items_rims_modifications["{$value['brand']}_{$value['model_name']}_{$value['code']}_{$value['paint']}"], 0, 3
                    );

                    $other_items_rims_modifications = array_slice(
                        $items_rims_modifications["{$value['brand']}_{$value['model_name']}_{$value['code']}_{$value['paint']}"], 3
                    );

                    foreach($sliced_items_rims_modifications as $modifications)
                    {
                        $mods['pcd'][] = "{$modifications['pcd_stud']}*{$modifications['pcd_dia']}" . (( !empty($modifications['pcd_dia_extra']) ) ? " / {$modifications['pcd_dia_extra']}" : NULL);
                        $mods['r_w'][] = "{$modifications['r']}x{$modifications['w']}";
                        $mods['et'][]  = $modifications['et'];
                        $mods['ch'][]  = number_format($modifications['ch'], 1);
                    }
                ?>
                <ul>
                    <li class="title" title="<?=$xml_items_rims->title_pcd?>">PCD</li>
                    <li><?=implode('</li><li>', $mods['pcd'])?></li>
                </ul>
                <ul>
                    <li class="title" title="<?=$xml_items_rims->title_r_w?>">RxW</li>
                    <li><?=implode('</li><li>', $mods['r_w'])?></li>
                </ul>
                <ul>
                    <li class="title" title="<?=$xml_items_rims->title_et?>">ET</li>
                    <li><?=implode('</li><li>', $mods['et'])?></li>
                </ul>
                <ul>
                    <li class="title" title="<?=$xml_items_rims->title_ch?>">CH</li>
                    <li><?=implode('</li><li>', $mods['ch'])?></li>
                </ul>
                <?php if( !empty($other_items_rims_modifications) ):
                    $mods_amount = count($other_items_rims_modifications);
                    $mods_ending = ( $mods_amount > 1 ) ? ($mods_amount >= 2 && $mods_amount <= 4) ? "модификации" : "модификаций" : "модификация";
                    ?>
                    <p>...и еще <?="{$mods_amount} {$mods_ending}"?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if( defined('WHITELIGHT') ): ?>
            <a href="<?=$this->get_current_link("item/{$value['type']}/{$value['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать данные товара" target="_blank"></a>
        <?php endif; ?>
    </div>
<?php endforeach;
endif; ?>
