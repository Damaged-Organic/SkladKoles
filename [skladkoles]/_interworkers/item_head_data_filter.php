<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($brands_data, $items_filter) = $supplied_data;
}

$brand_title = NULL;
if( !empty($brands_data) && !empty($items_filter['filter_common']) )
{
    if( !empty($items_filter['filter_common']['brand']) )
    {
        $brand_id = $items_filter['filter_common']['brand'];

        $brand = array_values(array_filter($brands_data, function($value) use($brand_id) {
            return $value['brand'] == $brand_id;
        }));

        if( !empty($brand) ) {
            $brand_title = ( !empty($brand[0]['title']) ) ? $brand[0]['title'] : NULL;
        }
    }
}

$head_data_filter = [];
if( $brand_title )
{
    switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
    {
        case 'rims':
        case 'exclusive_rims':
            $title = "Диски {$brand_title} купить в Киеве, цены на диски {$brand_title} | Skladkoles";
            $description = "Большой ассортимент дисков {$brand_title} с доставкой по всей Украине только на Skladkoles. ✔ Низкие цены ✔ Гарантия ✔ Акции ☎ (095)639-4-777";
        break;

        break;

        case 'tyres':
        case 'exclusive_tyres':
            $title = "Шины {$brand_title} купить в Киеве, цены на шины {$brand_title} | Skladkoles";
            $description = "Большой ассортимент шин {$brand_title} с доставкой по всей Украине только на Skladkoles. ✔ Низкие цены ✔ Гарантия ✔ Акции ☎ (095)639-4-777";
        break;

        default:
            throw new procException("Undefined subdirectory");
        break;
    }

    $head_data_filter = ['title' => $title, 'description' => $description];
}

return $head_data_filter;
?>
