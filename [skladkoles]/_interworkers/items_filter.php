<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $filter_data = $supplied_data;
}

$filter_parameters = [];

#Sorting
if( !empty($filter_data['sort']) )
{
    switch($filter_data['sort'])
    {
        case 'price_asc':
            $filter_parameters['sort'] = "price_asc";
        break;

        case 'price_desc':
            $filter_parameters['sort'] = "price_desc";
        break;

        case 'alphabet':
            $filter_parameters['sort'] = "alphabet";
        break;

        case 'newest':
            $filter_parameters['sort'] = "newest";
        break;

        case 'most_popular':
            $filter_parameters['sort'] = "most_popular";
        break;

        case 'most_rated':
            $filter_parameters['sort'] = "most_rated";
        break;

        default:
            return FALSE;
        break;
    }
}
#END\Sorting

#Common filters
if( !empty($filter_data['available']) ) {
    $filter_parameters['filter_common']['available'] = TRUE;
}

if( !empty($filter_data['promotion']) ) {
    $filter_parameters['filter_common']['promotion'] = TRUE;
}

if( !empty($filter_data['top']) ) {
    $filter_parameters['filter_common']['top'] = TRUE;
}

if( !empty($filter_data['price']) )
{
    list($price_min, $price_max) = explode(',', $filter_data['price']);

    if( ($price_min = $_BOOT->involve_object("InputPurifier")->purge_integer($price_min)) !== FALSE &&
        ($price_max = $_BOOT->involve_object("InputPurifier")->purge_integer($price_max)) !== FALSE ){
        if( $price_min >= 0 && $price_max > 0 && $price_min <= $price_max ) {
            $filter_parameters['filter_common']['price'] = [
                'min' => $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($price_min, $convert_back = TRUE),
                'max' => $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($price_max, $convert_back = TRUE),
            ];
        }
    }
}

if( !empty($filter_data['brand']) ) {
    if( ($brand = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['brand'])) !== FALSE ) {
        $filter_parameters['filter_common']['brand'] = str_replace('_', ' ', $brand);
    }
}
#END\Common filters

#Modifications filters
if( !empty($filter_data['rims']['pcd_stud']) ) {
    if( ($pcd_stud = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['rims']['pcd_stud'])) !== FALSE ) {
        $filter_parameters['filter_modification']['rims']['pcd_stud'] = $pcd_stud;
    }
}

if( !empty($filter_data['rims']['pcd_dia']) ) {
    if( ($pcd_dia = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['rims']['pcd_dia'])) !== FALSE ) {
        $filter_parameters['filter_modification']['rims']['pcd_dia'] = $pcd_dia;
        $filter_parameters['filter_modification']['rims']['pcd_dia_extra'] = $pcd_dia;
    }
}

if( !empty($filter_data['rims']['w']) ) {
    if( ($w = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['rims']['w'])) !== FALSE ) {
        $filter_parameters['filter_modification']['rims']['w'] = $w;
    }
}

if( !empty($filter_data['rims']['r']) ) {
    if( ($r = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['rims']['r'])) !== FALSE ) {
        $filter_parameters['filter_modification']['rims']['r'] = $r;
    }
}

if( !empty($filter_data['tyres']['w']) ) {
    if( ($w = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['tyres']['w'])) !== FALSE ) {
        $filter_parameters['filter_modification']['tyres']['w'] = $w;
    }
}

if( !empty($filter_data['tyres']['r']) ) {
    if( ($r = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['tyres']['r'])) !== FALSE ) {
        $filter_parameters['filter_modification']['tyres']['r'] = $r;
    }
}

if( !empty($filter_data['tyres']['h']) ) {
    if( ($h = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['tyres']['h'])) !== FALSE ) {
        $filter_parameters['filter_modification']['tyres']['h'] = $h;
    }
}

if( !empty($filter_data['tyres']['load_rate']) ) {
    if( ($load_rate = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['tyres']['load_rate'])) !== FALSE ) {
        $filter_parameters['filter_modification']['tyres']['load_rate'] = $load_rate;
    }
}
#END\Modifications filters

if( !empty($filter_data['season']) ) {
    if( ($season = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['season'])) !== FALSE )
    {
        switch($season)
        {
            case 'summer':
                $season = 'S';
            break;

            case 'winter':
                $season = 'W';
            break;

            case 'all_season':
                $season = '';
            break;
        }

        $filter_parameters['season'] = $season;
    }
}

return $filter_parameters;
?>
