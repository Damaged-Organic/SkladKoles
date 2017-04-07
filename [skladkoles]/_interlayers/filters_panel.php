<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($modifications, $filter_parameters, $brands_list) = $supplied_data;
    unset($modifications['brand']);
}

switch($_AREA->{$C_E::_ARGUMENTS}[0])
{
    case 'rims':
    case 'exclusive_rims':
        $parameter_type = 'rims';
    break;

    case 'tyres':
    case 'exclusive_tyres':
        $parameter_type = 'tyres';
    break;

    default:
        return FALSE;
    break;
}

$xml_filters_panel = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
        'AR_location' => "items_filter",
        'AR_method'   => "filter_data"
    ),
    JSON_UNESCAPED_SLASHES
);

$is_checked_equal = function($index, $item) use($filter_parameters) {
    if( isset($filter_parameters[$index]) && ($filter_parameters[$index] !== NULL) ) {
        return ( $filter_parameters[$index] == $item ) ? "checked" : NULL;
    } else {
        return NULL;
    }
};

$is_checked_isset = function($index, $item) use($filter_parameters) {
    return ( !empty($filter_parameters[$index][$item]) ) ? "checked" : NULL;
};
?>
<aside class="filters">
    <a href="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}?" . $C_E::_REQUEST . "[reset_filters]", $_AREA->{$C_E::_LANGUAGE})?>" class="clear-filter">
        <?=$xml_filters_panel->reset_filters?><span class="fa fa-remove"></span>
    </a>
    <div class="separator"></div>

    <?php if( !empty($brands_list) ): ?>
        <h2>Бренд</h2>
        <?=$brands_list?>
        <div class="separator"></div>
    <?php endif; ?>

    <form action="" id="baseFilter" method="POST" data-landmark='<?=$data_landmark?>' autocomplete="off">
        <?php if( !empty($modifications) ): ?>
            <h2><?=$xml_filters_panel->modification->headline?></h2>
            <?php foreach($modifications as $parameter_name => $parameters): ?>
                <a href="#" class="filter-button mods"><?=$xml_filters_panel->modification->{"type_{$parameter_name}"}?></a>
            <?php endforeach; ?>
            <?php foreach($modifications as $parameter_name => $parameters): ?>
                <div class="specifications-filter">
                    <h2><?=$xml_filters_panel->modification->{"type_{$parameter_name}"}?></h2>
                    <?php foreach($parameters as $value): ?>
                        <input type="radio" name="<?=$C_E::_REQUEST?>[<?=$parameter_type?>][<?=$parameter_name?>]" value="<?=$value?>" id="<?=$parameter_name?>_<?=$value?>"
                            <?=( !empty($filter_parameters['filter_modification'][$parameter_type][$parameter_name]) && ($filter_parameters['filter_modification'][$parameter_type][$parameter_name] == $value) ) ? "checked" : NULL;?>>
                        <label for="<?=$parameter_name?>_<?=$value?>"><?=$value?></label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <div class="separator"></div>
        <?php endif; ?>
        <a href="#" class="filter-button auto popUpButton" data-popup="#autoPopUp"><?=$xml_filters_panel->filtration->by_car_label?></a>

        <?php if( ($_AREA->{$C_E::_ARGUMENTS}[0] == 'tyres') || ($_AREA->{$C_E::_ARGUMENTS}[0] == 'exclusive_tyres') ): ?>
            <div class="separator"></div>
            <h2><?=$xml_filters_panel->seasons->headline?></h2>
            <input type="radio" name="<?=$C_E::_REQUEST?>[season]" value="summer" id="summerSeason"
                <?=$is_checked_equal('season', "S")?>>
            <label for="summerSeason"><?=$xml_filters_panel->seasons->summer?></label>
            <input type="radio" name="<?=$C_E::_REQUEST?>[season]" value="winter" id="winterSeason"
                <?=$is_checked_equal('season', "W")?>>
            <label for="winterSeason"><?=$xml_filters_panel->seasons->winter?></label>
            <input type="radio" name="<?=$C_E::_REQUEST?>[season]" value="all_season" id="fullYearSeason"
                <?=$is_checked_equal('season', "")?>>
            <label for="fullYearSeason"><?=$xml_filters_panel->seasons->all_season?></label>
        <?php endif; ?>

        <div class="separator"></div>
        <h2><?=$xml_filters_panel->sort->headline?></h2>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="price_asc" id="priceUp"
               <?=$is_checked_equal('sort', 'price_asc')?>>
        <label for="priceUp"><?=$xml_filters_panel->sort->price_up?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="price_desc" id="priceDown"
               <?=$is_checked_equal('sort', 'price_desc')?>>
        <label for="priceDown"><?=$xml_filters_panel->sort->price_down?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="alphabet" id="alphabet"
               <?=$is_checked_equal('sort', 'alphabet')?>>
        <label for="alphabet"><?=$xml_filters_panel->sort->price_alphabet?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="newest" id="new"
               <?=$is_checked_equal('sort', 'newest')?>>
        <label for="new"><?=$xml_filters_panel->sort->price_new?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="most_popular" id="popular"
               <?=$is_checked_equal('sort', 'most_popular')?>>
        <label for="popular"><?=$xml_filters_panel->sort->price_popular?></label>
        <input type="radio" name="<?=$C_E::_REQUEST?>[sort]" value="most_rated" id="rated"
                <?=$is_checked_equal('sort', 'most_rated')?>>
        <label for="rated"><?=$xml_filters_panel->sort->rating?></label>

        <div class="separator"></div>
        <h2><?=$xml_filters_panel->filtration->headline?></h2>
        <input type="checkbox" name="<?=$C_E::_REQUEST?>[available]" value="available" id="availability"
               <?=$is_checked_isset('filter_common', 'available')?>>
        <label for="availability"><?=$xml_filters_panel->label_available?></label>
        <?php if( ($_AREA->{$C_E::_ARGUMENTS}[0] === 'rims') || ($_AREA->{$C_E::_ARGUMENTS}[0] === 'tyres') ): ?>
            <input type="checkbox" name="<?=$C_E::_REQUEST?>[top]" value="top" id="topSale"
                   <?=$is_checked_isset('filter_common', 'top')?>>
            <label for="topSale"><?=$xml_filters_panel->label_top?></label>
            <input type="checkbox" name="<?=$C_E::_REQUEST?>[promotion]" value="promotion" id="onSale"
                   <?=$is_checked_isset('filter_common', 'promotion')?>>
            <label for="onSale"><?=$xml_filters_panel->label_promo?></label>
        <?php endif; ?>
        <!-- <a href="#" class="filter-button brands popUpButton" data-popup="#brandsPopUp"><?=$xml_filters_panel->label_brand?></a> -->
        <div class="separator"></div>
        <h2><?=$xml_filters_panel->price_headline?></h2>
        <div id="price-range" data-min="0" data-max="15000"
                              data-min-current="<?=( !empty($filter_parameters['filter_common']['price']['min']) ) ? $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($filter_parameters['filter_common']['price']['min'], FALSE, TRUE) : 0;?>"
                              data-max-current="<?=( !empty($filter_parameters['filter_common']['price']['max']) ) ? $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($filter_parameters['filter_common']['price']['max'], FALSE, TRUE) : 15000;?>">
            <input type="hidden" name="<?=$C_E::_REQUEST?>[price]" value="" id="price-input">
        </div>
        <div class="show-price-area">
            <p><?=$xml_filters_panel->price_from?> UAH<span id="min">
                    <?=( !empty($filter_parameters['filter_common']['price']['min']) ) ? $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($filter_parameters['filter_common']['price']['min'], FALSE, TRUE) : 0;?>
            </span></p>
            <p><?=$xml_filters_panel->price_to?> UAH<span id="max">
                    <?=( !empty($filter_parameters['filter_common']['price']['max']) ) ? $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($filter_parameters['filter_common']['price']['max'], FALSE, TRUE) : 10000;?>
            </span></p>
        </div>
        <div class="loading"></div>
    </form>
</aside>
