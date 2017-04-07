<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$page_title = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->directory_title_data_cell(
    [
        $C_S::DB_PREFIX_alpha."directories",
        $C_S::DB_PREFIX_alpha."directories_content"
    ],
    $_AREA->{$C_E::_LANGUAGE},
    $_AREA->{$C_E::_DIRECTORY}
);
?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
    <script>
        $.webshims.polyfill();

        $(function(){
            tabs.init();
            popUps.init();
            basket.init();

            var newsContainer = $(".news");
            liftUp.init(newsContainer);
            $("#userPhone").mask("+38 (099) 999-99-99");
        });
    </script>
</head>
<body>

    <!--GOOGLE_ANALYTICS-->
        <?=$interlayers['analyticstracking']?>
    <!--/GOOGLE_ANALYTICS-->

    <div class="page">
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
        <div class="content gap">
            <div class="track-lane gaps to-right"><h2><?=$page_title?></h2></div>
                <div class="centered news">
                    <!--NEWS-->
                    <?=$interlayers['news']?>
                    <!--/NEWS-->
                    <!--NEWS_BUTTON-->
                    <?=$interlayers['news_button']?>
                    <!--/NEWS_BUTTON-->
                </div>
            <div class="track-lane gaps"></div>
        </div>
        <footer class="footer">
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
    </div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/social-likes/3.0.13/social-likes.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>

    <script src="<?=$this->load_resource("javascript/mask.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/request.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/fastSearch.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/liftUp.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/tabs.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/popUp.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/basket.js") ?>"></script>

    <script>
        var app = app || {};

        $(function(){
            app.fastSearch.init();
            app.liftUp.init($(".news"));
            app.tabs.init();
            app.popUp.init();
            app.basket.init();

            $("#userPhone").mask("+380 (99) 999-99-99");
        });
    </script>

    <!--YANDEX_METRIKA-->
        <?=$interlayers['yandexmetrika']?>
    <!--/YANDEX_METRIKA-->

</body>
</html>
