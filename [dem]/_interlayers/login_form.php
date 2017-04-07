<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$login_attempt_info = ( $supplied_data ) ? implode('; ', $supplied_data) : NULL;
?>
<h1>АВТОРИЗАЦИЯ</h1>
<form action="<?=$this->get_current_link()?>" id="#authForm" method="POST" autocomplete="off">
    <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
    <input type="hidden" name="<?=$C_E::_REQUEST?>[login_action]" value="login">
    <label for="login">
        <input type="text" name="<?=$C_E::_REQUEST?>[user_email]" placeholder="Введите логин" class="transition borderBox" id="login">
    </label>
    <label for="password">
        <input type="password" name="<?=$C_E::_REQUEST?>[user_password]" placeholder="Введите пароль" class="transition borderBox" id="password">
    </label>
    <?php if( $login_attempt_info ): ?>
        <p><?=$login_attempt_info?></p>
    <?php endif; ?>
    <button type="submit" class="transition">ВОЙТИ</button>
</form>