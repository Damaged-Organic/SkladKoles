<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $items = [
        'quantity' => $supplied_data['quantity'],
        'price'    => $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($supplied_data['price'])
    ];
} else {
    $items = [
        'quantity' => 0,
        'price'    => '0.00'
    ];
}
?>
<p>Корзина:</p>
<?php if( $items['quantity'] ): ?>
    <span>&nbsp;<?=$items['quantity']?> ед. | <span><?=$items['price']?> UAH</span></span>
<?php else: ?>
    <span><span>пока пусто</span></span>
<?php endif; ?>
