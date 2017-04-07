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
    "catalog"
);

$page_subtitle = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->directory_title_data_cell(
    [
        $C_S::DB_PREFIX_alpha."catalog_subdirectories",
        $C_S::DB_PREFIX_alpha."catalog_subdirectories_content"
    ],
    $_AREA->{$C_E::_LANGUAGE},
    $_AREA->{$C_E::_ARGUMENTS}[0]
);
?>
<!DOCTYPE html>
<html lang="ru">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
    <link rel="stylesheet" href="<?=$this->load_resource("css/countdown.css") ?>">
    <link rel="stylesheet" href="<?=$this->load_resource("css/new/carousel.css") ?>">
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
        <div class="content gap catalog">
            <div class="track-lane gaps to-right"><h1><?="{$page_title} - {$page_subtitle}"?></h1></div>
            <!--CATALOG_SUBDIRECTORIES-->
            <?=$interlayers['catalog_subdirectories']?>
            <!--/CATALOG_SUBDIRECTORIES-->
            <section class="centered item-view">
                <!--ITEM_DETAILS-->
                <?=$interlayers['item_details']?>
                <!--/ITEM_DETAILS-->
            </section>
            <div id="disqus_thread"></div>
            <section class="section">
                <!--ITEMS_VIEWED-->
                <?=$interlayers['items_viewed']?>
                <!--/ITEMS_VIEWED-->
            </section>
            <section class="section">
                <!--ITEMS_BRANDED-->
                <?=$interlayers['items_branded']?>
                <!--/ITEMS_BRANDED-->
            </section>
            <section class="section">
                <!--ITEMS_POPULAR-->
                <?=$interlayers['items_popular']?>
                <!--/ITEMS_POPULAR-->
            </section>
            <div class="track-lane gaps to-right"></div>
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
    <script src="<?=$this->load_resource("javascript/countdown.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/request.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/rating.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/gallery.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/new/videoOverview.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/fastSearch.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/tabs.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/popUp.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/modules/basket.js") ?>"></script>
    <script src="<?=$this->load_resource("javascript/new/carousel.js") ?>"></script>

    <script src="<?=$this->load_resource("javascript/brasst_api.min.js") ?>"></script>
    <script>
        var app = app || {};

        $(function(){
            app.fastSearch.init();
            app.rating.init();
            app.gallery.init();
            app.videoOverview.init();
            app.tabs.init();
            app.popUp.init();
            app.basket.init();

            $("#userPhone").mask("+380 (99) 999-99-99");

            $(".countdown").countdown({
                onCreate: function(el, digits){
                    digits.closest(".digitsHolder.days").append("<p>дней</p>");
                    digits.closest(".digitsHolder.hours").append("<p>часов</p>");
                    digits.closest(".digitsHolder.minutes").append("<p>минут</p>");
                    digits.closest(".digitsHolder.seconds").append("<p>секунд</p>");
                },
                onTimeOut: function(el, finished){
                    if(finished) el.closest(".countdown-wrapper").find("h2").text("ация завершена");
                }
            });

            $(".carousel-holder").carousel({
                time: 200,
                easing: "ease-in-out"
            });

            app.brasst = new Brasst({ locale: 'ru' });
        });

        var disqus_shortname = 'sklad-koles',
            disqus_identifier = '<?="{$_AREA->{$C_E::_ARGUMENTS}[0]}_{$_AREA->{$C_E::_ARGUMENTS}[1]}"?>',
            disqus_url = '<?=$this->get_current_link("item_details/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}")?>';
            /*disqus_title = 'a unique title for each page where Disqus is present'*/

        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
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
