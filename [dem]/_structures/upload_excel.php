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
    <header id="header"><h1>Массовая загрузка товарных позиций</h1></header>
    <section class="content excelZone">
        <div class="breadcrumbs">
            <a href="<?=$this->get_current_link("add")?>" class="transition">вернуться на главную</a>
        </div>
        <h2>Выберите категорию для обновления контента</h2>
        <div class="excelUpload">
            <form action="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}", $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="excelUpload" autocomplete="off" enctype="multipart/form-data" data-error-msg="выберите файл формата .xls или .xlsx для загрузки">
                <div class="choice">
                    <input type="radio" name="_request[type]" value="rims" id="rims" checked>
                    <label for="rims" class="borderBox transition">Диски</label>
                    <input type="radio" name="_request[type]" value="tyres" id="tyres">
                    <label for="tyres" class="borderBox transition">Шины</label>

                    <input type="radio" name="_request[type]" value="exclusive_rims" id="usedRims">
                    <label for="usedRims" class="borderBox transition">Эксклюзивные диски</label>
                    <input type="radio" name="_request[type]" value="exclusive_tyres" id="usedTyres">
                    <label for="usedTyres" class="borderBox transition">Эксклюзивные шины</label>

                    <input type="radio" name="_request[type]" value="spares" id="accessories">
                    <label for="accessories" class="borderBox transition">Комплектующие</label>
                </div>
                <div class="fileChoose">
                    <input type="file" name="file[]" value="" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" id="excel">
                    <label for="excel" class="borderBox transition"></label>
                    <div class="previewZone"></div>
                </div>
                <div class="progressBar transition">
                    <div class="progress transition"></div>
                </div>
                <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}", $_AREA->{$C_E::_LANGUAGE})?>">
                <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="process_excel">
                <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="excel_upload">

                <button type="submit" class="borderBox transition" name="_request[submit]" value="excel_upload">Загрузить</button>
                <button type="submit" class="delete borderBox transition" name="_request[submit]" value="excel_upload_clean">Синхронизировать</button>
            </form>
            <div class="confirmDialog transition">
                <span class="choose transition yes" data-choice="true"></span>
                <span class="choose transition no" data-choice="false"></span>
            </div>
        </div>
        <div class="dangerArea">
            <hr>
                <form action="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}", $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="excelDelete" autocomplete="off">
                    <div class="choice">
                        <input type="radio" name="_request[type]" value="rims" id="rims_1" checked>
                        <label for="rims_1" class="borderBox transition">Диски</label>
                        <input type="radio" name="_request[type]" value="tyres" id="tyres_1">
                        <label for="tyres_1" class="borderBox transition">Шины</label>

                        <input type="radio" name="_request[type]" value="exclusive_rims" id="usedRims_1">
                        <label for="usedRims_1" class="borderBox transition">Эксклюзивные диски</label>
                        <input type="radio" name="_request[type]" value="exclusive_tyres" id="usedTyres_1">
                        <label for="usedTyres_1" class="borderBox transition">Эксклюзивные шины</label>

                        <input type="radio" name="_request[type]" value="spares" id="accessories_1">
                        <label for="accessories_1" class="borderBox transition">Комплектующие</label>
                    </div>
                    <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                    <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}", $_AREA->{$C_E::_LANGUAGE})?>">
                    <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="process_excel">
                    <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="excel_delete">
                    <button type="submit" class="delete borderBox transition" data-action="delete">Очистить</button>
                </form>
            <hr>
            <div class="confirmDialog transition">
                <span class="choose transition yes" data-choice="true"></span>
                <span class="choose transition no" data-choice="false"></span>
            </div>
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
<script src="<?=$this->load_resource("js/uploader.js")?>"></script>
<script src="<?=$this->load_resource("js/excelUpload.js")?>"></script>
<script src="<?=$this->load_resource("js/excelDelete.js")?>"></script>
<script>
    var app = app || {};

    $(function(){
        app.excelUpload.init($(".excelUpload"));
        app.excelDelete.init($(".dangerArea"));
    });
</script>

</body>
</html>
