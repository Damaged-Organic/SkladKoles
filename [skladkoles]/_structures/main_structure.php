<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_main_structure = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
    <link rel="stylesheet" href="<?=$this->load_resource("css/cSelect.css") ?>">
</head>
<body>

    <!--GOOGLE_ANALYTICS-->
        <?=$interlayers['analyticstracking']?>
    <!--/GOOGLE_ANALYTICS-->

    <div id="brasst_widget"></div>
    <div class="page">
        <div class="section intro">
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
            <!--INTRO_VIDEO-->
            <?=$interlayers['intro_video']?>
            <!--/INTRO_VIDEO-->

            <!--FILTERS_PANEL_MAIN-->
            <?=$interlayers['filters_panel_main']?>
            <!--/FILTERS_PANEL_MAIN-->

            <!--DEM_MANAGER_WIDGET-->
            <?=$interlayers['dem_manager_widget']?>
            <!--/DEM_MANAGER_WIDGET-->
        </div>
        <div class="content">
            <div class="section">
                <!--INTRO_ABOUT-->
                <?=$interlayers['intro_about']?>
                <!--/INTRO_ABOUT-->
            </div>
            <div class="section">
                <div class="track-lane gaps to-right"><h2>Каталог</h2></div>
                <!--MAIN_CATALOG_SUBDIRECTORIES-->
                <?=$interlayers['main_catalog_subdirectories']?>
                <!--/MAIN_CATALOG_SUBDIRECTORIES-->
            </div>
            <?php if( !empty($interworkers['special_offers']) ): ?>
                <div class="section">
                    <div class="track-lane gaps"><h2>Акции</h2></div>
                    <!--SPECIAL_OFFERS-->
                    <?=$interlayers['special_offers']?>
                    <!--/SPECIAL_OFFERS-->
                </div>
                <?php if( count($interworkers['obtain_items_combined_promo']) >= 4 ): ?>
                    <div class="section">
                        <div class="track-lane gaps to-right"></div>
                        <div class="onSale">
                            <div class="centered">
                                <div id="saleGoods" class="flexy">
                                    <div class="flexyHolder">
                                        <!--ITEMS_MAIN_PROMO-->
                                        <?=$interlayers['items_main_promo']?>
                                        <!--/ITEMS_MAIN_PROMO-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="section">
                <div class="track-lane gaps"><h2><?=$xml_main_structure->items_new?></h2></div>
                <div class="catalog onMain">
                    <div class="centered">
                        <!--ITEMS_MAIN_NEWEST-->
                        <?=$interlayers['items_main_newest']?>
                        <!--/ITEMS_MAIN_NEWEST-->
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="track-lane gaps to-right"><h2><?=$xml_main_structure->items_popular?></h2></div>
                <div class="catalog onMain">
                    <div class="centered">
                        <!--ITEMS_MAIN_POPULAR-->
                        <?=$interlayers['items_main_popular']?>
                        <!--/ITEMS_MAIN_POPULAR-->
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="track-lane gaps"><h2><?=$xml_main_structure->news?></h2></div>
                <div class="centered news">
                    <!--NEWS-->
                    <?=$interlayers['news']?>
                    <!--/NEWS-->
                    <!--NEWS_BUTTON-->
                    <?=$interlayers['news_button']?>
                    <!--/NEWS_BUTTON-->
                </div>
                <div class="track-lane gaps to-right"></div>
            </div>
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

    <script src="<?=$this->load_resource("javascript/cSelect.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/mask.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/request.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/intro.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/fastSearch.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/fastFilter.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/sales.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/tabs.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/popUp.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/basket.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/brasst_api.min.js") ?>"></script>

    <script>
        var app = app || {};

        $(function(){
            app.intro.init();
            app.fastSearch.init();
            app.fastFilter.init();
            app.sales.init();
            app.tabs.init();
            app.popUp.init();
            app.basket.init();

            $("#userPhone").mask("+380 (99) 999-99-99");

            app.brasst = new Brasst({ locale: 'ru' });
        });
    </script>

    <!--YANDEX_METRIKA-->
        <?=$interlayers['yandexmetrika']?>
    <!--/YANDEX_METRIKA-->

    <script type="text/javascript">
        (function(d, w, s) {
        var widgetId = '23144', gcw = d.createElement(s); gcw.type = 'text/javascript'; gcw.async = true;
        gcw.src = '//widgets.binotel.com/getcall/widgets/'+ widgetId +'.js';
        var sn = d.getElementsByTagName(s)[0]; sn.parentNode.insertBefore(gcw, sn);
        })(document, window, 'script');
    </script>

</body>
</html>
