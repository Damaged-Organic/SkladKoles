<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$db_handler = $_BOOT->involve_object('DB_Handler');

$form_error = NULL;

if( isset($_AREA->{$C_E::_ARGUMENTS}[0]) && ($_AREA->{$C_E::_ARGUMENTS}[0] == 'result') )
{
    if( empty($_SESSION['order_result']) )
        $this->redirect_and_exit('/cart');
}

if( empty($_SESSION['order_id']) ) {
    $order_id = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('OrderHandler', [$db_handler])->generate_order_code();
    $_SESSION['order_id'] = $order_id;
}

if( isset($_AREA->{$C_E::_ARGUMENTS}[0]) && ($_AREA->{$C_E::_ARGUMENTS}[0] == 'order') )
{
    if( empty($_AREA->{$C_E::_REQUEST}['userName']) ||
        empty($_AREA->{$C_E::_REQUEST}['userPhone'])) {
        $this->redirect_and_exit('/cart');
    }

    $order_data = $this->load_inter('worker', 'check_order_data', $_AREA->{$C_E::_REQUEST});

    if( $order_data === FALSE ) {
        $form_error = 'Возможно, некоторые введенные данные неверны или имеют некорректный формат. Пожалуйста, проверьте данные в форме заказа.';
    } else {
        $cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();

        if( !$cart_items )
        {
            $interlayer = '
                <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                <span class="fa fa-times-circle"></span>
                <p>Ваш заказ уже принят. Если вы хотите заказать другие товары, пожалуйста, добавьте их в корзину и повторите процесс заказа.</p>
                <a href="' . $this->get_current_link('catalog') . '">Вернуться в каталог</a>
            ';
            $_SESSION['order_result'] = $interlayer;
        } else {
            $counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
                ->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

            $order_data_result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
                ->involve_object('OrderHandler', [$db_handler])->insert_order($order_data, $cart_items, $counted_cart_items, $_SESSION['order_id']);

            if( !$order_data_result )
            {
                $interlayer = '
                    <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                    <span class="fa fa-times-circle"></span>
                    <p>Приносим извинения за возможные неудобства, но к сожалению, что-то пошло не так и система не смогла обработать заказ. Пожалуйста, повторите попытку позднее.</p>
                    <a href="' . $this->get_current_link('cart') . '">Вернуться в корзину</a>
                ';
                $_SESSION['order_result'] = $interlayer;
            }

            $result = $this->load_inter("worker", "send_order_data", [$order_data, $cart_items, $counted_cart_items, $_SESSION['order_id']]);

            if( !$result ) {
                $interlayer = '
                    <h2>ОШИБКА В ПРОЦЕССЕ ПРИОБРЕТЕНИЯ</h2>
                    <span class="fa fa-times-circle"></span>
                    <p>Приносим извинения за возможные неудобства, но к сожалению, что-то пошло не так и система не смогла обработать заказ. Пожалуйста, повторите попытку позднее.</p>
                    <a href="' . $this->get_current_link('cart') . '">Вернуться в корзину</a>
                ';
                $_SESSION['order_result'] = $interlayer;
            } else {
                $_SESSION['ecommercetracking'] = $this->load_inter(
                    'layer', 'ecommercetracking', [$_SESSION['order_id'], $cart_items, $counted_cart_items]
                );

                $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->clear_cart();

                $interlayer = '
                    <h2>ПРОЦЕСС ПРИОБРЕТЕНИЯ ЗАВЕРШЕН УСПЕШНО</h2>
                    <span class="fa fa-check-circle"></span>
                    <p>Ваш заказ успешно принят. Наши менеджеры свяжутся с ваши в максимально короткие сроки. Если вы указали ваш e-mail, на него также была отправлена информация о заказе.</p>
                    <p class="orderNumber">Номер вашего заказа: <span>' . $_SESSION['order_id'] . '</span></p>
                    <a href="' . $this->get_current_link('catalog') . '">Вернуться к покупкам</a>
                ';
                $_SESSION['order_result'] = $interlayer;
                $_SESSION['order_status'] = TRUE;
            }
        }

        unset($_SESSION['order_id']);

        $this->redirect_and_exit('/cart/result');
    }
}

if( !empty($_SESSION['order_result']) ) {
    $order_result = $_SESSION['order_result'];
    unset($_SESSION['order_result']);

    if( isset($_SESSION['order_status']) ) {
        $order_status = TRUE;
        unset($_SESSION['order_status']);
    }

    if( isset($_SESSION['ecommercetracking']) ) {
        $ecommercetracking = $_SESSION['ecommercetracking'];
        unset($_SESSION['ecommercetracking']);
    }
} else {
    $order_result = NULL;
}

if( isset($_SERVER['HTTP_REFERER']) &&
    (strpos($_SERVER['HTTP_REFERER'], $_AREA->{$C_E::_DOMAIN}) !== FALSE) &&
    (strpos($_SERVER['HTTP_REFERER'], 'cart') === FALSE) ) {
    $backlink = $_SERVER['HTTP_REFERER'];
} else {
    $backlink = $this->get_current_link('main');
}

unset(
    $_SESSION['filter_parameters'], $_SESSION['pagination'], $_SESSION['search']
);

$cart_items         = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->obtain_cart_items_data();
$counted_cart_items = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('Cart', [$db_handler])->count_cart_items($cart_items);

$interworkers = [
    'head' => $this->load_inter('worker', 'head')
];

$interlayers = [
    'head'       => $this->load_inter('layer', 'head_new', $interworkers['head']),
    'cart_items' => $this->load_inter('layer', 'cart_items_new', $cart_items),
    'cart_order' => $this->load_inter('layer', 'cart_order_new', [$cart_items, $form_error]),

    #DEM
    'dem_manager_widget' => $this->load_inter('layer', 'dem_manager_widget'),

    #SEARCH
    'analyticstracking' => $this->load_inter('layer', 'analyticstracking'),
    'ecommercetracking' => ( isset($ecommercetracking) ) ? $ecommercetracking : NULL,
    'yandexmetrika' => $this->load_inter('layer', 'yandexmetrika')
];

require_once(BASEPATH . "/[{$_AREA->{$C_E::_SUBSYSTEM}}]/_structures/{$_AREA->{$C_E::_DIRECTORY}}_structure.php");
?>
