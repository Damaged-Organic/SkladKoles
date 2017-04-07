<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !defined('WHITELIGHT') ) exit();
?>
<!DOCTYPE html>
<html>
<head>
    <!--HEAD-->
    <?=$interlayers['head']?>
    <!--/HEAD-->
</head>
<body>
<div class="page">
    <header id="header"><h1>добавление и изменение данных</h1></header>
    <section class="content addZone">
        <div class="wingify">
            <div class="layer main borderBox">
                <span>добавить товары</span>
            </div>
            <div class="layer left borderBox">
                <ul>
                    <li><a href="<?=$this->get_current_link("add/items/rims")?>" class="transition">Диски</a></li>
                    <li><a href="<?=$this->get_current_link("add/items/tyres")?>" class="transition">Шины</a></li>
                    <li><a href="<?=$this->get_current_link("add/items/exclusive_rims")?>" class="transition">Эксклюзивные диски</a></li>
                    <li><a href="<?=$this->get_current_link("add/items/exclusive_tyres")?>" class="transition">Эксклюзивные шины</a></li>
                    <li><a href="<?=$this->get_current_link("upload_excel")?>" class="transition">Залить excel</a></li>
                </ul>
            </div>
            <div class="layer right borderBox">
                <ul>
                    <li><a href="<?=$this->get_current_link("spares/rings")?>" class="transition">Кольца</a></li>
                    <li><a href="<?=$this->get_current_link("spares/bolts")?>" class="transition">Болты</a></li>
                    <li><a href="<?=$this->get_current_link("spares/nuts")?>" class="transition">Гайки</a></li>
                    <li><a href="<?=$this->get_current_link("spares/locks")?>" class="transition">Секретки</a></li>
                    <li><a href="<?=$this->get_current_link("spares/logos")?>" class="transition">Логотипы</a></li>
                    <li><a href="<?=$this->get_current_link("spares/pins")?>" class="transition">Шпильки</a></li>
                </ul>
            </div>
        </div>

        <div class="item borderBox">
            <a href="<?=$this->get_current_link("add/news")?>" class="transition newspaper">добавить новость</a>
        </div>
        <div class="item borderBox">
            <a href="<?=$this->get_current_link("promotions")?>" class="transition sale">добавить акцию</a>
        </div>

        <div class="item borderBox">
            <div class="currentRate">
                <span>Рекомендуемый курс: <span class="rate"></span></span>
            </div>
            <form action="<?=$this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE})?>" id="currencyChange" method="POST">
                <label for="currencyRate"></label>
                <input type="text" name="_request[currency_rate]" value="<?=number_format($_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_currency_rate(), 2)?>" placeholder="" id="currencyRate" class="borderBox transition">
                <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE})?>">
                <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="set_currency_rate">
                <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="set_rate">
                <button type="submit" class="borderBox transition">изменить</button>
            </form>
            <div id="message" class="borderBox"></div>
        </div>

    </section>
    <div class="preFooter"></div>
</div>
<footer id="footer">
    <!--FOOTER-->
    <?=$interlayers['footer']?>
    <!--/FOOTER-->
</footer>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="<?=$this->load_resource("js/common.js")?>"></script>
<script src="<?=$this->load_resource("js/exchange.js")?>"></script>

<script>
    $(function(){
        app.exchange.init($(".addZone"));
    });
</script>

</body>
</html>
