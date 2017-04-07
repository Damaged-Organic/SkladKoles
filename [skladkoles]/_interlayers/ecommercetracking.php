<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($order_id, $cart_items, $counted_cart_items) = $supplied_data;
}

$get_sku = function($type, $unique_code)
{
    switch($type) {
        case 'rims':
            $current_unique_code_prefix = "R";
        break;

        case 'exclusive_rims':
            $current_unique_code_prefix = "RU";
        break;

        case 'tyres':
            $current_unique_code_prefix = "T";
        break;

        case 'exclusive_tyres':
            $current_unique_code_prefix = "TU";
        break;

        case 'spares':
            $current_unique_code_prefix = "S";
        break;

        default:
            $current_unique_code_prefix = FALSE;
        break;
    }

    return ( $current_unique_code_prefix ) ? "{$current_unique_code_prefix}-{$unique_code}" : NULL;
};

$get_name = function($item)
{
    switch($item['type']) {
        case 'rims':
        case 'exclusive_rims':
            $name = str_replace('  ', ' ', "{$item['brand']} {$item['model_name']} {$item['code']} {$item['paint']}");
            $mod = [
                "PCD:{$item['pcd_stud']}*{$item['pcd_dia']}",
                "RxW:{$item['r']}x{$item['w']}",
                "ET:{$item['et']}",
                "CH:{$item['ch']}"
            ];
            $mod = implode('; ', $mod);

            $model_name = "{$name} ({$mod})";
        break;

        case 'tyres':
        case 'exclusive_tyres':
            $name = str_replace('  ', ' ', "{$item['brand']} {$item['model_name']}");
            $mod = [
                "R:{$item['r']}",
                "W/H:{$item['w']} / {$item['h']}",
                "SR:{$item['load_rate']}"
            ];
            $mod = implode('; ', $mod);

            $model_name = "{$name} ({$mod})";
        break;

        case 'spares':
            $name = NULL;
            switch($item['item_type'])
            {
                case 'rings':
                    $name = "Кольца";
                break;

                case 'bolts':
                    $name = "Болты";
                break;

                case 'nuts':
                    $name = "Гайки";
                break;

                case 'locks':
                    $name = "Секретки";
                break;

                case 'logos':
                    $name = "Логотипы";
                break;

                case 'pins':
                    $name = "Шпильки";
                break;
            }

            $mod = [];
            if( !empty($item['item_specs']) ) {
                $mod[] = "{$item['item_specs']}";
            }
            if( !empty($item['size']) ) {
                $mod[] = "{$item['size']}";
            }
            $mod = implode('; ', $mod);

            $model_name = "{$name} ({$mod})";
        break;

        default:
            $model_name = NULL;
        break;
    }

    return $model_name;
};

$get_price = function($item) use($_BOOT, $C_N)
{
    if( $item['promo'] )
        $price = $item['promo'];
    else
        $price = $item['retail'];

    $item_count = ( $_SESSION['user_cart'][$item['type']][$item['id']] ) ?: 1;

    return $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
        ->involve_object("CatalogOutput")
        ->convert_item_price($price*$item_count)
    ;
};

$get_quantity = function($item)
{
    return ( $_SESSION['user_cart'][$item['type']][$item['id']] ) ?: 1;
};

$getTransactionProducts = function($items) use($get_sku, $get_name, $get_price, $get_quantity)
{
    $transaction_products = [];

    foreach($items as $item) {
        $transaction_products[] = [
            'sku'      => $get_sku($item['type'], $item['unique_code']),
            'name'     => $get_name($item),
            'category' => $item['type'],
            'price'    => $get_price($item),
            'quantity' => $get_quantity($item),
        ];
    }

    return $transaction_products;
};

$cart_items = ( $cart_items ) ?: [];

$transactionTotal = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
    ->involve_object("CatalogOutput")
    ->convert_item_price($counted_cart_items['price'])
;

$data_layer = json_encode([
    'transactionId'          => $order_id,
    'transactionAffiliation' => 'Sklad Koles',
    'transactionTotal'       => $transactionTotal,
    'transactionProducts'    => $getTransactionProducts($cart_items)
]);

if( !empty($cart_items) ): ?>
<script>
    (window.dataLayer || (window.dataLayer = [])).push(<?=$data_layer?>);

    console.log(dataLayer);
</script>
<?php endif; ?>
