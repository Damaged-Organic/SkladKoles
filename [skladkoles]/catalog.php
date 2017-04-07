<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

unset(
	$_SESSION['search']
);

#MAIN_FILTRATION
if( !empty($_AREA->{$C_E::_REQUEST}) )
{
    list($cars_filter, $car_bar) = $this->load_inter("worker", "items_filter_cars", $_AREA->{$C_E::_REQUEST});

    if( $cars_filter ) {
        $query_builder_auto = $this->load_inter("worker", "filter/query_builder_auto");

        $query_string = $query_builder_auto(['auto' => $_AREA->{$C_E::_REQUEST}]);
    } else {
        $query_string = NULL;
    }

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

    if( !empty($_AREA->{$C_E::_REQUEST}['available']) )
        $_SESSION['filter_parameters']['filter_common']['available'] = TRUE;

    if( isset($_AREA->{$C_E::_REQUEST}['get_rims_by_car']) ) {
        $this->redirect_and_exit('subcatalog/rims' . $query_string);
    } elseif( isset($_AREA->{$C_E::_REQUEST}['get_tyres_by_car']) ) {
        $this->redirect_and_exit('subcatalog/tyres' . $query_string);
    }
}
#END/MAIN_FILTRATION

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head'                   => $this->load_inter('worker', 'head'),
    'directories'            => $this->load_inter('worker', 'directories'),
    'catalog_subdirectories' => $this->load_inter('worker', 'catalog_subdirectories'),
    'items_combined_top'     => $this->load_inter('worker', 'obtain_items_combined_top'),
    'contacts_working_hours' => $this->load_inter('worker', 'contacts_working_hours'),
    'contacts_addresses'     => $this->load_inter('worker', 'contacts_addresses'),
    'contacts_phones'        => $this->load_inter('worker', 'contacts_phones')
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
    'catalog_subdirectories'      => $this->load_inter('layer', 'catalog_subdirectories',
                                        $interworkers['catalog_subdirectories']
                                     ),
    'items_top'                   => $this->load_inter('layer', 'items_top',
                                        $interworkers['items_combined_top']
                                     ),
    'items_top_button'            => $this->load_inter('layer', 'items_top_button'),
    'footer_shop_information'     => $this->load_inter('layer', 'footer_shop_information',
                                        $interworkers['contacts_working_hours']
                                     ),
    'footer_shop_contacts'        => $this->load_inter('layer', 'footer_shop_contacts', [
                                        $interworkers['contacts_addresses'],
                                        $interworkers['contacts_phones']
                                     ]),
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
