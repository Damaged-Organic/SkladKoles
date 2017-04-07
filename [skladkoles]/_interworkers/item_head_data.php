<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $item = $supplied_data['item'];
}

$xml_item_details_tyres = $_BOOT->involve_object("XML_Handler")->get_xml(
    'item_details_tyres',
    $_AREA->{$C_E::_LANGUAGE}
);

switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
{
    case 'rims':
    case 'exclusive_rims':
        $product      = 'Диск';
        $product_case = 'диск';

        $title = preg_replace('/\s+/', ' ', "{$item['item']['brand']} {$item['item']['model_name']} {$item['item']['code']} {$item['item']['paint']}");

        if( !empty($item['item']['rim_type']) ) {
            $title .= " ({$item['item']['rim_type']})";
        }
    break;

    break;

    case 'tyres':
    case 'exclusive_tyres':
        $product      = 'Шина';
        $product_case = 'шину';

        $title = "{$item['item']['brand']} {$item['item']['model_name']}";

        if( !empty($item['item']['season']) && ($item['item']['season'] == 'S') ) {
            $title .= " ({$xml_item_details_tyres->season_summer_label})";
        } elseif( !empty($item['item']['season']) && ($item['item']['season'] == 'W') ) {
            $title .= " ({$xml_item_details_tyres->season_winter_label})";
        } else {
            $title .= " ({$xml_item_details_tyres->season_all_label})";
        }
    break;

    default:
        throw new procException("Undefined subdirectory");
    break;
}

$item_head_data = [
    'title'       => "{$product} {$title} купить в Киеве, Харькове, Днепропетровске, Одессе, цены, фото | Склад Колес",
    'description' =>
        "На SkladKoles можно купить по самым низким ценам {$product_case} {$title}. Акции, скидки, доставка по всей Украине. Звоните - +38 (095) 639-4-777."
    ,
];

return $item_head_data;
?>
