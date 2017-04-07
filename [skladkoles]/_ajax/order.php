<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.25) ) {
    $this->cancel_ajax_request();
}

$available_methods = ['delivery_type', 'make_order'];

if( !in_array($_AREA->{$C_AR::AR_METHOD}, $available_methods, TRUE) ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

switch($_AREA->{$C_AR::AR_METHOD})
{
    case 'delivery_type':
        if( empty($_AREA->{$C_E::_REQUEST}['type']) ) {
            $this->cancel_ajax_request();
        }

        switch($_AREA->{$C_E::_REQUEST}['type'])
        {
            case 'pickup':
                $this->satisfy_ajax_request(
                    json_encode(
                        ['interlayer' => $this->load_inter('layer', 'delivery_pickup')]
                    )
                );
            break;

            case 'shipping':
                $regions = $this->load_inter('worker', 'obtain_regions');

                $this->satisfy_ajax_request(
                    json_encode(
                        ['interlayer' => $this->load_inter('layer', 'delivery_shipping', $regions)]
                    )
                );
            break;

            default:
                $this->cancel_ajax_request();
            break;
        }
    break;

    case 'make_order':
        if( empty($_AREA->{$C_E::_REQUEST}['userName']) ||
            empty($_AREA->{$C_E::_REQUEST}['userEmail']) ||
            empty($_AREA->{$C_E::_REQUEST}['userPhone']) ||
            empty($_AREA->{$C_E::_REQUEST}['deliveryType'])) {
            $this->cancel_ajax_request();
        }

        $current_arg_0 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[0]}" : NULL;
        $current_arg_1 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[1]}" : NULL;
        $current_link  = $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}{$current_arg_0}{$current_arg_1}", $_AREA->{$C_E::_LANGUAGE});

        if( ($order_data = $this->load_inter('worker', 'check_order_data', $_AREA->{$C_E::_REQUEST})) === FALSE ) {
            $interlayer = '
                <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                <span class="fa fa-times-circle"></span>
                <p>Возможно, некоторые введенные данные неверны или имеют некорректный формат. Пожалуйста, вернитесь на предыдущие шаги и проверьте введенные данные, или нажмите кнопку "Завершить", чтобы начать процесс с начала.</p>
                <a href="'.$current_link.'">Завершить</a>
            ';

            $this->satisfy_ajax_request(json_encode([
                'interlayer' => $interlayer,
                'linkUrl'    => $current_link,
                'linkText'   => 'закрыть'
            ]));

            $this->satisfy_ajax_request(

            );
        }

        $cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();

        if( !$cart_items ) {
            $interlayer = '
                <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                <span class="fa fa-times-circle"></span>
                <p>Ваш заказ уже принят. Если вы хотите заказать другие товары, пожалуйста, добавьте их в корзину и повторите процесс заказа.</p>
                <a href="'.$current_link.'">Завершить</a>
            ';

            $this->satisfy_ajax_request(json_encode([
                'interlayer' => $interlayer,
                'linkUrl'    => $current_link,
                'linkText'   => 'закрыть'
            ]));
        }

        $counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

        if( ($order_data['order_code'] = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('OrderHandler', [$db_handler])->insert_order($order_data, $cart_items, $counted_cart_items)) == FALSE ) {
            $this->cancel_ajax_request();
        }

        $result = $this->load_inter("worker", "send_order_data", [$order_data, $cart_items, $counted_cart_items]);

        if( !$result ) {
            $interlayer = '
                <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                <span class="fa fa-times-circle"></span>
                <p>Приносим извинения за возможные неудобства, но к сожалению, что-то пошло не так и система не смогла обработать заказ. Пожалуйста, повторите попытку позднее.</p>
                <a href="'.$current_link.'">Завершить</a>
            ';

            $this->satisfy_ajax_request(json_encode([
                'interlayer' => $interlayer,
                'linkUrl'    => $current_link,
                'linkText'   => 'закрыть'
            ]));
        } else {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->clear_cart();

            $interlayer = '
                <h2>ПРОЦЕСС ПРИОБРЕТЕНИЯ ЗАВЕРШЕН УСПЕШНО</h2>
                <span class="fa fa-check-circle"></span>
                <p>Ваш заказ успешно принят. Наши менеджеры свяжутся с ваши в максимально короткие сроки.Информация о вашем заказе выслана на указаный e-mail.</p>
                <p class="orderNumber">Номер вашего заказа: <span>'.$order_data['order_code'].'</span></p>
                <a href="'.$current_link.'">Завершить</a>
            ';

            $this->satisfy_ajax_request(json_encode([
                'interlayer' => $interlayer,
                'linkUrl'    => $current_link,
                'linkText'   => 'закрыть'
            ]));
        }
    break;
}
?>