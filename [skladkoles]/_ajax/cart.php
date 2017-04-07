<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.1) ) {
    $this->cancel_ajax_request();
}

$available_methods = ['add_item', 'act'];

if( !in_array($_AREA->{$C_AR::AR_METHOD}, $available_methods, TRUE) ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

switch($_AREA->{$C_AR::AR_METHOD})
{
    case 'add_item':
        if( empty($_AREA->{$C_E::_REQUEST}['item_type']) ||
            empty($_AREA->{$C_E::_REQUEST}['id']) ) {
            $this->cancel_ajax_request();
        } elseif(
            ($item_type = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['item_type'])) === FALSE ||
            ($unique_code = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['id'])) === FALSE ) {
            $this->cancel_ajax_request();
        } else {
            if( !$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->add_item($item_type, $unique_code) ) {
                $this->cancel_ajax_request();
            }
        }
    break;

    case 'act':
        if( empty($_AREA->{$C_E::_REQUEST}['action']) )  {
            $this->cancel_ajax_request();
        }

        if( empty($_AREA->{$C_E::_REQUEST}['type']) ||
            empty($_AREA->{$C_E::_REQUEST}['id']) ) {
            $this->cancel_ajax_request();
        } elseif(
            ($item_type = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['type'])) === FALSE ||
            ($unique_code = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['id'])) === FALSE ) {
            $this->cancel_ajax_request();
        }

        switch($_AREA->{$C_E::_REQUEST}['action'])
        {
            case 'increase':
                if( !$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->add_item($item_type, $unique_code) ) {
                    $this->cancel_ajax_request();
                }
            break;

            case 'decrease':
                if( !$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->decrease_item($item_type, $unique_code) ) {
                    $this->cancel_ajax_request();
                }
            break;

            case 'remove':
                if( !$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->delete_item($item_type, $unique_code) ) {
                    $this->cancel_ajax_request();
                }
            break;
        }

        $find_item_price_by_id = function($cart_items, $unique_code)
        {
            foreach( $cart_items as $value )
            {
                if( $value['id'] == $unique_code)
                    return ( $value['promo'] ) ?: $value['retail'];
            }
        };

        $cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
        $counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

        $total_price = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($counted_cart_items['price']);

        switch($_AREA->{$C_E::_REQUEST}['action'])
        {
            case 'increase':
            case 'decrease':
                $price = $find_item_price_by_id($cart_items, $unique_code);

                if( !$price )
                    $this->cancel_ajax_request();

                $quantity = $_SESSION['user_cart'][$item_type][$unique_code];
                $price    = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price(bcmul($quantity, $price, 2));

                $response = [
                    'total_price' => "<p>Стоимость: <span>{$total_price} UAH</span></p>",
                    'count'       => $quantity,
                    'price'       => "{$price} UAH"
                ];
            break;

            case 'remove':
                $response = [
                    'total_price' => "<p>Стоимость: <span>{$total_price} UAH</span></p>",
                    'is_last'     => ( empty(array_filter($_SESSION['user_cart'])) ),
                    'message'     => "<p class=\"empty\">Сейчас в вашей корзине пусто. Если вы хотите сделать заказ, пожалуйста, добавьте сюда товары с помощью кнопки \"Купить\"</p>",
                    'status'      => "removed"
                ];
            break;
        }

        $this->satisfy_ajax_request(json_encode($response));
    break;
}

$cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();

$cart_items_layer  = $this->load_inter('layer', 'cart_items', $cart_items);

$cart_widget_layer = $this->load_inter('layer', 'basket_widget',
    $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items)
);

if( !$cart_items_layer ) {
    $this->cancel_ajax_request();
} else {
    $this->satisfy_ajax_request(json_encode([
        'basketGoods' => $cart_items_layer,
        'widgetInfo'  => $cart_widget_layer
    ]));
}
?>
