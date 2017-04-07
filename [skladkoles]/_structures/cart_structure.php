<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);
?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
</head>
<body>

    <?php if( $order_result && isset($order_status) ): ?>
        <!--GOOGLE_ECOMMERCE_TRACKING-->
            <?=$interlayers['ecommercetracking']?>
        <!--/GOOGLE_ECOMMERCE_TRACKING-->
    <?php endif; ?>

    <!--GOOGLE_ANALYTICS-->
        <?=$interlayers['analyticstracking']?>
    <!--/GOOGLE_ANALYTICS-->

    <div class="page" id="basket-holder">
        <div class="content">
            <div class="close-holder">
                <a href="<?=htmlspecialchars($backlink)?>"><span class="fa fa-close"></a></span>
            </div>
            <?php if( $order_result ): ?>
                <div class="track-lane to-right">
                    <h1>Результаты заказа</h1>
                </div>
                <div id="order-result">
                    <?=$order_result?>
                </div>
            <?php else: ?>
                <div class="track-lane to-right">
                    <h1>Корзина</h1>
                </div>
                <?php if( $cart_items ): ?>
                    <!--CART_ITEMS-->
                    <?=$interlayers['cart_items']?>
                    <!--/CART_ITEMS-->
                    <!--CART_ORDER-->
                    <?=$interlayers['cart_order']?>
                    <!--/CART_ORDER-->
                    <div id="total-price">
                        <p>Стоимость:
                            <span>
                                <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($counted_cart_items['price']);?> UAH
                            </span>
                        </p>
                    </div>
                <?php else: ?>
                    <p class="empty">Сейчас в вашей корзине пусто. Если вы хотите сделать заказ, пожалуйста, добавьте сюда товары с помощью кнопки "Купить"</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.9/jquery.mask.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/social-likes/3.0.13/social-likes.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>

    <script src="<?=$this->load_resource("javascript/new/fastSearch.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/new/basket.js") ?>"></script>
    <script>
        var app = app || {};

        $(function(){

            app.fastSearch.init();
            new app.Basket();

            $("form").validate();
            $("form").on("submit", function(){
                if(!$(this).valid()) return false;
            });

        });
    </script>

    <!--YANDEX_METRIKA-->
        <?=$interlayers['yandexmetrika']?>
    <!--/YANDEX_METRIKA-->

</body>
</html>
