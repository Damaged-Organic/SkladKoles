<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

unset(
    $_SESSION['filter_parameters'], $_SESSION['pagination'], $_SESSION['search']
);

if( empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) {
    throw new notFoundException("Item id value is not set");
}

$db_handler = $_BOOT->involve_object('DB_Handler');

$special_offers = $this->load_inter('worker', 'special_offers', [
    NULL,
    $id = $_AREA->{$C_E::_ARGUMENTS}[0]
]);

if( empty($special_offers) )
    throw new notFoundException("Item id value is not set");

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head'                          => $this->load_inter('worker', 'head'),
    'directories'                   => $this->load_inter('worker', 'directories'),
    'special_offers'                => $special_offers,
    'items_combined_promo'          => $this->load_inter('worker', 'obtain_items_combined_promo', [
                                           $id = $_AREA->{$C_E::_ARGUMENTS}[0]
                                       ]),
    'items_viewed'                  => $this->load_inter('worker', 'obtain_items_viewed'),
    'contacts_working_hours'        => $this->load_inter('worker', 'contacts_working_hours'),
    'contacts_addresses'            => $this->load_inter('worker', 'contacts_addresses'),
    'contacts_phones'               => $this->load_inter('worker', 'contacts_phones')
];

$interlayers = [
    'head'                            => $this->load_inter('layer', 'head',
                                          $interworkers['head']
                                      ),
    'credentials'                     => $this->load_inter('layer', 'credentials'),
    'logo'                            => $this->load_inter('layer', 'logo'),
    'directories'                     => $this->load_inter('layer', 'directories',
                                          $interworkers['directories']
                                      ),
    'search_widget'                   => $this->load_inter('layer', 'search_widget'),
    'basket_widget'                   => $this->load_inter('layer', 'basket_widget', $counted_cart_items),
    'basket_popup'                    => $this->load_inter('layer', 'basket_popup', $cart_items),
    'special_offers_extended_details' => $this->load_inter('layer', 'special_offers_extended_details',
                                          $interworkers['special_offers']
                                      ),
    'items_promo_specific'            => $this->load_inter('layer', 'items_promo_specific',
                                          $interworkers['items_combined_promo']
                                      ),
    'items_viewed'                    => $this->load_inter('layer', 'items_viewed',
                                          $interworkers['items_viewed']
                                      ),
    'footer_shop_information'         => $this->load_inter('layer', 'footer_shop_information',
                                          $interworkers['contacts_working_hours']
                                      ),
    'footer_shop_contacts'            => $this->load_inter('layer', 'footer_shop_contacts',
                                          [
                                              $interworkers['contacts_addresses'],
                                              $interworkers['contacts_phones']
                                          ]
                                     ),
    'footer_directories'             => $this->load_inter('layer', 'footer_directories',
                                         $interworkers['directories']
                                     ),
    'footer_developers'              => $this->load_inter('layer', 'footer_developers'),

    #DEM
    'dem_manager_widget'             => $this->load_inter('layer', 'dem_manager_widget'),

    #SEARCH
    'analyticstracking'              => $this->load_inter('layer', 'analyticstracking'),
    'yandexmetrika'                  => $this->load_inter('layer', 'yandexmetrika')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}_structure.php");
?>