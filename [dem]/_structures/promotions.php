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
    <!--PROMO-->
    <?=$interlayers['promo']?>
    <!--/PROMO-->
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

<script src="<?=$this->load_resource("js/quill.min.js")?>"></script>
<script src="<?=$this->load_resource("js/editor.js")?>"></script>
<script src="<?=$this->load_resource("js/common.js")?>"></script>
<script src="<?=$this->load_resource("js/uploader.js")?>"></script>
<script src="<?=$this->load_resource("js/sale.js")?>"></script>

<script>
    var app = app || {};

    $(function(){
        app.editor.init($(".editorWrapper"));
        app.sale.init($(".saleZone"));
    });
</script>

</body>
</html>