<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

unset(
	$_SESSION['search']
);

if( empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) {
    throw new notFoundException("Item id value is not set");
}

if( !empty($_SESSION['filter_parameters']) ) {
    $items_filter = $_SESSION['filter_parameters'];
} else {
    $items_filter = [];
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
{
    case 'rims':
    case 'exclusive_rims':
        $item_layer = $this->load_inter('layer', 'item_details_rims',
            $item = $this->load_inter('worker', 'obtain_item_details_rims', [$_AREA->{$C_E::_ARGUMENTS}[1], $items_filter])
        );
    break;

    break;

    case 'tyres':
    case 'exclusive_tyres':
        $item_layer = $this->load_inter('layer', 'item_details_tyres',
            $item = $this->load_inter('worker', 'obtain_item_details_tyres', [$_AREA->{$C_E::_ARGUMENTS}[1], $items_filter])
        );
    break;

    default:
        throw new procException("Undefined subdirectory");
    break;
}

$db_handler = $_BOOT->involve_object('DB_Handler');
$object_catalogCells = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogCells", [$db_handler]);
$object_catalogCells->enqueue_viewed_item($_AREA->{$C_E::_ARGUMENTS}[0], $_AREA->{$C_E::_ARGUMENTS}[1]);

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head'                          => $this->load_inter('worker', 'head', ['directory' => "catalog"]),
    'item_head_data'                => $this->load_inter('worker', 'item_head_data', ['item' => $item]),
    'directories'                   => $this->load_inter('worker', 'directories'),
    'intro_about'                   => $this->load_inter('worker', 'intro_about'),
    'catalog_subdirectories'        => $this->load_inter('worker', 'catalog_subdirectories'),
    'items_viewed'                  => $this->load_inter('worker', 'obtain_items_viewed'),
    'obtain_items_combined_branded' => $this->load_inter('worker', 'obtain_items_combined_branded', [
                                            'limit' => 20,
                                            'brand' => $item['item']['brand'],
                                       ]),
    'obtain_items_combined_popular' => $this->load_inter('worker', 'obtain_items_combined_popular', [
                                            'limit' => 20
                                       ]),
    'contacts_working_hours'        => $this->load_inter('worker', 'contacts_working_hours'),
    'contacts_addresses'            => $this->load_inter('worker', 'contacts_addresses'),
    'contacts_phones'               => $this->load_inter('worker', 'contacts_phones'),
];

$interlayers = [
    'head'                        => $this->load_inter('layer', 'head_new',
                                        array_merge(
                                            $interworkers['head'],
                                            ['item_head_data' => $interworkers['item_head_data']]
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
    'catalog_subdirectories'      => $this->load_inter('layer', 'catalog_subdirectories_new',
                                        $interworkers['catalog_subdirectories']
                                     ),
    'item_details'                => $item_layer,
    'items_viewed'                => $this->load_inter('layer', 'items_viewed',
                                        $interworkers['items_viewed']
                                     ),
    'items_branded'               => $this->load_inter('layer', 'items_branded_slider',
                                        $interworkers['obtain_items_combined_branded']
                                     ),
    'items_popular'               => $this->load_inter('layer', 'items_popular_slider',
                                        $interworkers['obtain_items_combined_popular']
                                     ),
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
