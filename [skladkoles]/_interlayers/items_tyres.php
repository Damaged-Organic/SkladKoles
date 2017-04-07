<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$items_tyres = [];

if( !empty($supplied_data['items']) && !empty($supplied_data['modifications']) )
{
    $transform_modifications = function(&$input_array)
    {
        $mediate_array = [];

        foreach($input_array as $value) {
            $mediate_array["{$value['brand']}_{$value['model_name']}_{$value['season']}"][] = $value;
        }

        return $mediate_array;
    };

    if( $_AREA->{$C_E::_ARGUMENTS}[0] == "tyres" ) {
        $tyres_type = "tyres";
    } elseif( $_AREA->{$C_E::_ARGUMENTS}[0] == "exclusive_tyres" ) {
        $tyres_type = "exclusive_tyres";
    }

    $items_tyres = array_map(
        function($item) use($_BOOT, $C_N, $tyres_type) { return $item + $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->provide_item_type($tyres_type); },
        $supplied_data['items']
    );

    $items_tyres_modifications = $transform_modifications($supplied_data['modifications']);
}

$xml_items_tyres = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

if( empty($items_tyres) ): ?>
    <p class="empty"><?=$xml_items_tyres->no_data?></p>
<?php else:
    foreach($items_tyres as $value): ?>
        <div class="item-wrap">
            <div class="item">
                <?php if( !empty($value['promotion_id']) || !empty($value['promo']) ): ?>
                    <div class="ribbon">
                        <img src="<?=$this->load_resource("images/ribbon.png")?>" alt="promotion">
                    </div>
                <?php elseif( $value['type'] == 'exclusive_tyres' ): ?>
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
                    <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}");?>">
                        <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
                    </a>
                </figure>
                <p>
                    <?="{$value['brand']}"?>
                </p>
                <h2>
                    <?=( mb_strlen($value['model_name'], 'utf-8') > 18 ) ? mb_substr($value['model_name'], 0, 18, 'utf-8')."..." : $value['model_name']?>
                </h2>
                <h3>
                    <?php if( !empty($value['season']) && ($value['season'] == 'S') ): ?>
                        (<?=$xml_items_tyres->season_summer_label?>)
                    <?php elseif( !empty($value['season']) && ($value['season'] == 'W') ): ?>
                        (<?=$xml_items_tyres->season_winter_label?>)
                    <?php else: ?>
                        (<?=$xml_items_tyres->season_all_label?>)
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
                            <span class="from"><?=$xml_items_tyres->from_label?></span>
                            <span>
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?>
                    </span>
                        <?php else: ?>
                            <span class="from"><?=$xml_items_tyres->from_label?></span>
                            <span>
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?>
                    </span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="from">цена не указана</span>
                        <span></span>
                    <?php endif; ?>
                </div>
                <p class="code"><span>ID:</span>T-<?=str_pad($value['unique_code'], 4, '0', STR_PAD_LEFT)?></p>
                <?php if( !empty($value['brand']) && !empty($value['model_name']) ): ?>
                    <a href="<?=$this->get_current_link("item_details/{$value['type']}/{$value['id']}");?>"><?=$xml_items_tyres->button_detailed?></a>
                <?php endif; ?>
            </div>
            <div class="specifications">
                <h2><?=$xml_items_tyres->modifications_label?></h2>
                <div class="spec-lists">
                    <?php
                    unset($mods);

                    $sliced_items_tyres_modifications = array_slice(
                        $items_tyres_modifications["{$value['brand']}_{$value['model_name']}_{$value['season']}"], 0, 3
                    );

                    $other_items_tyres_modifications = array_slice(
                        $items_tyres_modifications["{$value['brand']}_{$value['model_name']}_{$value['season']}"], 3
                    );

                    foreach($sliced_items_tyres_modifications as $modifications)
                    {
                        $mods['r'][]         = $modifications['r'];
                        $mods['w_h'][]       = "{$modifications['w']}/{$modifications['h']}";
                        $mods['load_rate'][] = $modifications['load_rate'];
                    }
                    ?>
                    <ul>
                        <li class="title" title="<?=$xml_items_tyres->title_r?>">R</li>
                        <li><?=implode('</li><li>', $mods['r'])?></li>
                    </ul>
                    <ul>
                        <li class="title" title="<?=$xml_items_tyres->title_w_h?>">W/H</li>
                        <li><?=implode('</li><li>', $mods['w_h'])?></li>
                    </ul>
                    <ul>
                        <li class="title" title="<?=$xml_items_tyres->title_load_rate?>">SR</li>
                        <li><?=implode('</li><li>', $mods['load_rate'])?></li>
                    </ul>
                    <?php if( !empty($other_items_tyres_modifications) ):
                        $mods_amount = count($other_items_tyres_modifications);
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
