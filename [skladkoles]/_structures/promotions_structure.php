<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

/*$xml_main_structure = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);*/
?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
    <link rel="stylesheet" href="<?=$this->load_resource("css/countdown.css") ?>">
</head>
<body>

    <!--GOOGLE_ANALYTICS-->
        <?=$interlayers['analyticstracking']?>
    <!--/GOOGLE_ANALYTICS-->

    <div class="page mh">
        <div class="section">
            <header class="header">
                <div class="additional-head">
                    <ul>
                        <!--CREDENTIALS-->
                        <?=$interlayers['credentials']?>
                        <!--/CREDENTIALS-->
                        <li id="basket-widget">
                            <div class="to-basket">
                                <!-- <a href="#" class="popUpButton" data-popup="#basket"><i class="fa fa-shopping-cart"></i></a> -->
                                <a href="<?=$this->get_current_link('cart')?>">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            </div>
                            <div class="info">
                                <!--BASKET_WIDGET-->
                                <?=$interlayers['basket_widget']?>
                                <!--/BASKET_WIDGET-->
                            </div>
                        </li>
                        <li id="search-widget">
                            <!--SEARCH_WIDGET-->
                            <?=$interlayers['search_widget']?>
                            <!--/SEARCH_WIDGET-->
                        </li>
                    </ul>
                </div>
                <div class="head">
                    <!--LOGO-->
                    <?=$interlayers['logo']?>
                    <!--/LOGO-->
                    <!--DIRECTORIES-->
                    <?=$interlayers['directories']?>
                    <!--/DIRECTORIES-->
                </div>
            </header>
            <!--DEM_MANAGER_WIDGET-->
            <?=$interlayers['dem_manager_widget']?>
            <!--/DEM_MANAGER_WIDGET-->
        </div>
        <div class="content gap" id="salePage">
            <!--SPECIAL_OFFERS_EXTENDED-->
            <?=$interlayers['special_offers_extended']?>
            <!--/SPECIAL_OFFERS_EXTENDED-->
        </div>
        <div class="preFooter"></div>
    </div>
    <footer class="footer fixed">
        <div class="centered">
            <ul>
                <li>
                    <!--FOOTER_SHOP_INFORMATION-->
                    <?=$interlayers['footer_shop_information']?>
                    <!--/FOOTER_SHOP_INFORMATION-->
                </li>
                <li>
                    <!--FOOTER_SHOP_CONTACTS-->
                    <?=$interlayers['footer_shop_contacts']?>
                    <!--/FOOTER_SHOP_CONTACTS-->
                </li>
                <li>
                    <!--FOOTER_DIRECTORIES-->
                    <?=$interlayers['footer_directories']?>
                    <!--/FOOTER_DIRECTORIES-->
                </li>
                <li>
                    <!--FOOTER_DEVELOPERS-->
                    <?=$interlayers['footer_developers']?>
                    <!--/FOOTER_DEVELOPERS-->
                </li>
            </ul>
        </div>
    </footer>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/social-likes/3.0.13/social-likes.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>

    <script src="<?=$this->load_resource("javascript/countdown.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/mask.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/request.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/fastSearch.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/tabs.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/popUp.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/basket.js") ?>"></script>

    <script>
        var app = app || {};

        $(function(){
            app.fastSearch.init();
            app.tabs.init();
            app.popUp.init();
            app.basket.init();

            $(".counter").countdown({
                onCreate: function(el, digits){
                    digits.closest(".digitsHolder.days").append("<p>дней</p>");
                    digits.closest(".digitsHolder.hours").append("<p>часов</p>");
                    digits.closest(".digitsHolder.minutes").append("<p>минут</p>");
                    digits.closest(".digitsHolder.seconds").append("<p>секунд</p>");
                },
                onTimeOut: function(el, finished){
                    if(finished) {
                        var counterWrapper = el.closest(".counter-wrapper").find("h2").text("акция завершена");
                    }
                }
            });

            $("#userPhone").mask("+380 (99) 999-99-99");
        });
    </script>

    <!--YANDEX_METRIKA-->
    <?=$interlayers['yandexmetrika']?>
    <!--/YANDEX_METRIKA-->

</body>
</html>
