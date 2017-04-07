<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

unset(
	$_SESSION['search']
);

if( isset($_SESSION['visited_subdirectory']) && ($_SESSION['visited_subdirectory'] != $_AREA->{$C_E::_ARGUMENTS}[0]) ) {
    unset(
		$_SESSION['filter_parameters'], $_SESSION['pagination']
	);
}

#MAIN_FILTRATION
if( isset($_AREA->{$C_E::_REQUEST}['get_by_modifications']) )
{
    $query_builder = $this->load_inter("worker", "filter/query_builder");

    $query_string = $query_builder($_AREA->{$C_E::_REQUEST});

    $this->soft_redirect_and_exit($this->get_current_link("subcatalog/{$_AREA->{$C_E::_ARGUMENTS}[0]}" . $query_string));
}
#END/MAIN_FILTRATION

if( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) && !empty($_AREA->{$C_E::_ARGUMENTS}[1]) )
{
    $filter_parameters = [];

    # PARSER

    $parsed_parameters = array_filter(explode(';', $_AREA->{$C_E::_ARGUMENTS}[1]));

    foreach($parsed_parameters as $parsed_parameter)
    {
        $filter_parameter = array_filter(explode('-', $parsed_parameter, 2));

        if( count($filter_parameter) != 2 )
            continue;

        if( in_array($filter_parameter[0], ['r', 'w', 'pcd_stud', 'pcd_dia', 'h'], TRUE) ) {
            if( !isset($filter_parameters[$_AREA->{$C_E::_ARGUMENTS}[0]]) )
                $filter_parameters[$_AREA->{$C_E::_ARGUMENTS}[0]] = [];

            $filter_parameters[$_AREA->{$C_E::_ARGUMENTS}[0]] = array_merge([
                $filter_parameter[0] => $filter_parameter[1]
            ], $filter_parameters[$_AREA->{$C_E::_ARGUMENTS}[0]]);
        } else {
            $filter_parameters[$filter_parameter[0]] = $filter_parameter[1];
        }
    }

    # ENDPARSER

    # Modifications
    $items_filter = $this->load_inter("worker", "items_filter", $filter_parameters);

    if( !empty($_SESSION['filter_parameters']['filter_car_modification']) &&
        !empty($items_filter['filter_modification']) )
    {
        unset($_SESSION['filter_parameters']['filter_car_modification']);
        $_SESSION['filter_parameters'] = $items_filter;
    } elseif( !empty($_SESSION['filter_parameters']['filter_car_modification']) ) {
        $_SESSION['filter_parameters'] = $items_filter = array_merge($items_filter, ['filter_car_modification' => $_SESSION['filter_parameters']['filter_car_modification']]);
    } else {
        $_SESSION['filter_parameters'] = $items_filter;
    }
    # End Modifications

    # Pagination
    if( !empty($filter_parameters['page']) ) {
        $_SESSION['pagination']['current_page'] = $filter_parameters['page'];
    }

    if( !empty($filter_parameters['records_per_page']) ) {
        $_SESSION['pagination']['records_per_page'] = $filter_parameters['records_per_page'];
    }
    # End Pagination

    # Cars
    # auto_mark:bmw;auto_model:3-series_(E90);auto_year=2012;auto_modification:325i
    if( !empty($filter_parameters["auto_mark"]) &&
        !empty($filter_parameters["auto_model"]) &&
        !empty($filter_parameters["auto_year"]) &&
        !empty($filter_parameters["auto_modification"]) )
    {
        $query_formatter_auto = $this->load_inter("worker", "filter/query_formatter_auto");

        $filter_parameters = $query_formatter_auto($filter_parameters);

        list($cars_filter, $car_bar) = $this->load_inter("worker", "items_filter_cars", $filter_parameters);

        $_SESSION['filter_parameters']['car_bar'] = $car_bar;

        if( !empty($_SESSION['filter_parameters']) && !empty($_SESSION['filter_parameters']['filter_modification']) ) {
            unset($_SESSION['filter_parameters']['filter_modification']);
            $_SESSION['filter_parameters'] = $cars_filter = array_merge(['filter_car_modification' => $cars_filter], $_SESSION['filter_parameters']);
        } elseif( !empty($_SESSION['filter_parameters']) ) {
            unset($_SESSION['filter_parameters']['filter_car_modification']);
            $_SESSION['filter_parameters'] = $cars_filter = array_merge(['filter_car_modification' => $cars_filter], $_SESSION['filter_parameters']);
        } else {
            $_SESSION['filter_parameters'] = ['filter_car_modification' => $cars_filter];
        }
    }
    # End Cars
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

$_SESSION['visited_subdirectory'] = $_AREA->{$C_E::_ARGUMENTS}[0];

if( isset($_AREA->{$C_E::_REQUEST}['reset_filters']) ) {
    $_SESSION['filter_parameters'] = NULL;
    $_SESSION['pagination']        = NULL;
    $this->soft_redirect_and_exit("{$_AREA->{$C_E::_ARGUMENTS}[0]}");
}

if( !empty($_SESSION['filter_parameters']) ) {
    $items_filter = $_SESSION['filter_parameters'];
} else {
    $items_filter = [];
}

switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
{
    case 'rims':
    case 'exclusive_rims':
        if( empty($_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]]) ) {
            $modifications = $_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]] = $this->load_inter('worker', 'obtain_modifications', $_AREA->{$C_E::_ARGUMENTS}[0])[$_AREA->{$C_E::_ARGUMENTS}[0]];
        } else {
            $modifications = $_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]];
        }

        if( empty($_SESSION['cache']['brands']['rims']) ) {
            $brands_data = $this->load_inter('worker', 'obtain_brands', "rims");
        } else {
            $brands_data = $_SESSION['cache']['brands']['rims'];
        }

        $brands_popup = $this->load_inter('layer', 'brands_popup',
            ['rims', $brands_data, $modifications]
        );

        $brands_list = $this->load_inter('layer', 'brands_list',
            ['rims', $brands_data, $modifications]
        );

        $records_number = count($this->load_inter('worker', 'obtain_items_rims', $items_filter)['items']);

        $inter = [
            'layer'  => 'items_rims',
            'worker' => 'obtain_items_rims'
        ];
    break;

    case 'tyres':
    case 'exclusive_tyres':
        if( empty($_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]]) ) {
            $modifications = $_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]] = $this->load_inter('worker', 'obtain_modifications', $_AREA->{$C_E::_ARGUMENTS}[0])[$_AREA->{$C_E::_ARGUMENTS}[0]];
        } else {
            $modifications = $_SESSION['cache']['modifications'][$_AREA->{$C_E::_ARGUMENTS}[0]];
        }

        if( empty($_SESSION['cache']['brands']['tyres']) ) {
            $brands_data = $this->load_inter('worker', 'obtain_brands', "tyres");
        } else {
            $brands_data = $_SESSION['cache']['brands']['tyres'];
        }

        $brands_popup = $this->load_inter('layer', 'brands_popup',
            ['tyres', $brands_data, $modifications]
        );

        $brands_list = $this->load_inter('layer', 'brands_list',
            ['tyres', $brands_data, $modifications]
        );

        $records_number = count($this->load_inter('worker', 'obtain_items_tyres', $items_filter)['items']);

        $inter = [
            'layer'  => 'items_tyres',
            'worker' => 'obtain_items_tyres'
        ];
    break;

    default:
        throw new procException("Undefined subdirectory");
    break;
}

#PAGINATION
$current_page     = ( !empty($_SESSION['pagination']['current_page']) ) ? $_SESSION['pagination']['current_page'] : 1;
$records_per_page = ( !empty($_SESSION['pagination']['records_per_page']) ) ? $_SESSION['pagination']['records_per_page'] : 12;

$pagination_parameters = [
    'records_number'   => $records_number,
    'records_per_page' => $records_per_page,
    'pages_step'       => 7
];

if( is_object($_BOOT->involve_object("Pagination")->set_parameters($pagination_parameters)->set_current_page($current_page)) ) {
    $pagination = $_BOOT->involve_object("Pagination")->handle_pagination();
} else {
    throw new notFoundException("Current page does not exist");
}

$items_layer = $this->load_inter('layer', $inter['layer'],
    $this->load_inter('worker', $inter['worker'],
        $items_filter + ['pagination' => [$pagination['first_record'], $pagination['records_per_page']]]
    )
);
#END-PAGINATION

if( empty($_SESSION['cache']['brands']['cars']) ) {
    $car_brands = $_SESSION['cache']['brands']['cars'] = $this->load_inter('worker', 'obtain_brands_cars');
} else {
    $car_brands = $_SESSION['cache']['brands']['cars'];
}

if( !empty($_SESSION['filter_parameters']['car_bar']) ) {
    $car_bar = $_SESSION['filter_parameters']['car_bar'];
} else {
    $car_bar = NULL;
}

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head'                   => $this->load_inter('worker', 'head', [
                                    'directory' => "catalog"
                                ]),
    'item_head_data_filter'  => $this->load_inter('worker', 'item_head_data_filter', [
                                    $brands_data, $items_filter
                                ]),
    'directories'            => $this->load_inter('worker', 'directories'),
    'catalog_subdirectories' => $this->load_inter('worker', 'catalog_subdirectories'),
    'contacts_working_hours' => $this->load_inter('worker', 'contacts_working_hours'),
    'contacts_addresses'     => $this->load_inter('worker', 'contacts_addresses'),
    'contacts_phones'        => $this->load_inter('worker', 'contacts_phones')
];

$interlayers = [
    'head'                        => $this->load_inter('layer', 'head',
                                        array_merge(
                                            $interworkers['head'],
                                            ['item_head_data_filter' => $interworkers['item_head_data_filter']]
                                        )
                                     ),
    'credentials'                 => $this->load_inter('layer', 'credentials'),
    'logo'                        => $this->load_inter('layer', 'logo'),
    'directories'                 => $this->load_inter('layer', 'directories',
                                        $interworkers['directories']
                                     ),
    'search_widget'               => $this->load_inter('layer', 'search_widget'),
    'basket_widget'               => $this->load_inter('layer', 'basket_widget', $counted_cart_items),
    'basket_popup'                => $this->load_inter('layer', 'basket_popup', $cart_items),
    'catalog_subdirectories'      => $this->load_inter('layer', 'catalog_subdirectories',
                                        $interworkers['catalog_subdirectories']
                                     ),
    'catalog_panel'               => $this->load_inter('layer', 'catalog_panel'),
    'car_bar'                     => $this->load_inter('layer', 'car_bar', $car_bar),
    'filters_panel'               => $this->load_inter('layer', 'filters_panel',
                                        [
                                            $modifications, $items_filter, $brands_list
                                        ]
                                     ),
    'cars_popup'                  => $this->load_inter('layer', 'cars_popup',
                                        $car_brands
                                     ),
    'brands_popup'                => $brands_popup,
    'items_layer'                 => $items_layer,
    'pagination'                  => $this->load_inter('layer', 'pagination', $pagination),
    'footer_shop_information'     => $this->load_inter('layer', 'footer_shop_information',
                                        $interworkers['contacts_working_hours']
                                     ),
    'footer_shop_contacts'        => $this->load_inter('layer', 'footer_shop_contacts',
                                        [
                                            $interworkers['contacts_addresses'],
                                            $interworkers['contacts_phones']
                                        ]
                                     ),
    'footer_directories'          => $this->load_inter('layer', 'footer_directories',
                                        $interworkers['directories']
                                     ),
    'footer_developers'           => $this->load_inter('layer', 'footer_developers'),

    #DEM
    'dem_manager_widget'          => $this->load_inter('layer', 'dem_manager_widget'),

    #SEARCH
    'analyticstracking'           => $this->load_inter('layer', 'analyticstracking'),
    'yandexmetrika'               => $this->load_inter('layer', 'yandexmetrika')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}_structure.php");
?>
