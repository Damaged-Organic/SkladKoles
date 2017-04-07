<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

unset(
	$_SESSION['filter_parameters'], $_SESSION['pagination'], $_SESSION['search'], $_SESSION['visited_subdirectory']
);

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

#CARS, BRANDS, MODIFICATIONS
$filter_options = [];

if( empty($_SESSION['cache']['brands']['cars']) ) {
    $filter_options['brands']['cars'] = $_SESSION['cache']['brands']['cars'] = $this->load_inter('worker', 'obtain_brands_cars');
} else {
    $filter_options['brands']['cars'] = $_SESSION['cache']['brands']['cars'];
}

if( empty($_SESSION['cache']['modifications']['rims']) ) {
    $filter_options['modifications']['rims'] = $_SESSION['cache']['modifications']['rims'] = $this->load_inter('worker', 'obtain_modifications', 'rims')['rims'];
} else {
    $filter_options['modifications']['rims'] = $_SESSION['cache']['modifications']['rims'];
}

if( empty($_SESSION['cache']['modifications']['tyres']) ) {
    $filter_options['modifications']['tyres'] = $_SESSION['cache']['modifications']['tyres'] = $this->load_inter('worker', 'obtain_modifications', 'tyres')['tyres'];
} else {
    $filter_options['modifications']['tyres'] = $_SESSION['cache']['modifications']['tyres'];
}

if( empty($_SESSION['cache']['brands']['rims']) ) {
    $filter_options['brands']['rims'] = $this->load_inter('worker', 'obtain_brands', "rims");
} else {
    $filter_options['brands']['rims'] = $_SESSION['cache']['brands']['rims'];
}

if( empty($_SESSION['cache']['brands']['tyres']) ) {
    $filter_options['brands']['tyres'] = $this->load_inter('worker', 'obtain_brands', "tyres");
} else {
    $filter_options['brands']['tyres'] = $_SESSION['cache']['brands']['tyres'];
}
#END/CARS, BRANDS, MODIFICATIONS

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head'                          => $this->load_inter('worker', 'head'),
    'directories'                   => $this->load_inter('worker', 'directories'),
    'intro_about'                   => $this->load_inter('worker', 'intro_about'),
    'catalog_subdirectories'        => $this->load_inter('worker', 'catalog_subdirectories'),
    'special_offers'                => $this->load_inter('worker', 'special_offers', [
                                        [$limit = 4],
                                        NULL
                                    ]),
    'obtain_items_combined_promo'   => $this->load_inter('worker', 'obtain_items_combined_promo'),
    'obtain_items_combined_newest'  => $this->load_inter('worker', 'obtain_items_combined_newest'),
    'obtain_items_combined_popular' => $this->load_inter('worker', 'obtain_items_combined_popular'),
    'news'                          => $this->load_inter('worker', 'news'),
    'contacts_working_hours'        => $this->load_inter('worker', 'contacts_working_hours'),
    'contacts_addresses'            => $this->load_inter('worker', 'contacts_addresses'),
    'contacts_phones'               => $this->load_inter('worker', 'contacts_phones')
];

$interlayers = [
    'head'                        => $this->load_inter('layer', 'head',
                                        $interworkers['head']
                                     ),
    'credentials'                 => $this->load_inter('layer', 'credentials'),
    'logo'                        => $this->load_inter('layer', 'logo'),
    'directories'                 => $this->load_inter('layer', 'directories',
                                        $interworkers['directories']
                                     ),
    'search_widget'               => $this->load_inter('layer', 'search_widget'),
    'basket_widget'               => $this->load_inter('layer', 'basket_widget', $counted_cart_items),
    'basket_popup'                => $this->load_inter('layer', 'basket_popup', $cart_items),
    'intro_video'                 => $this->load_inter('layer', 'intro_video'),
    'filters_panel_main'          => $this->load_inter('layer', 'filters_panel_main',
                                        $filter_options
                                     ),
    'intro_about'                 => $this->load_inter('layer', 'intro_about',
                                        $interworkers['intro_about']
                                     ),
    'main_catalog_subdirectories' => $this->load_inter('layer', 'main_catalog_subdirectories',
                                        $interworkers['catalog_subdirectories']
                                     ),
    'special_offers'              => $this->load_inter('layer', 'special_offers',
                                        $interworkers['special_offers']
                                     ),
    'items_main_promo'            => $this->load_inter('layer', 'items_main_promo',
                                        $interworkers['obtain_items_combined_promo']
                                     ),
    'items_main_newest'           => $this->load_inter('layer', 'items_main_newest',
                                        $interworkers['obtain_items_combined_newest']
                                     ),
    'items_main_popular'          => $this->load_inter('layer', 'items_main_popular',
                                        $interworkers['obtain_items_combined_popular']
                                     ),
    'news'                        => $this->load_inter('layer', 'news',
                                        $interworkers['news']
                                     ),
    'news_button'                 => $this->load_inter('layer', 'news_button'),
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
