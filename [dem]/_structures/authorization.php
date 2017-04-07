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
    <script src="<?=$this->load_resource("js/main.js")?>"></script>
    <script>
        $.webshims.polyfill();

        $(function(){

        });
    </script>
</head>
<body>
<div class="page">
    <section class="authorization">
        <!--LOGIN_FORM-->
        <?=$interlayers['login_form']?>
        <!--/LOGIN_FORM-->
    </section>
</div>
</body>
</html>