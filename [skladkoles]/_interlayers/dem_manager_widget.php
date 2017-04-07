<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !defined('WHITELIGHT') ) {
    return FALSE;
}
?>
<div id="manage-panel">
    <a href="<?=$this->get_current_link("add", $C_S::SUBSYSTEM_beta)?>" class="manage fa fa-plus" title="Добавить информацию" target="_blank"></a>
    <a href="<?=$this->get_current_link("?" . $_IC::IC_TOKEN . "={$_SESSION['IC_token']['hash']}&" . $C_E::_REQUEST . "[login_action]=logout", $C_S::SUBSYSTEM_beta)?>" class="manage fa fa-sign-out" title="Выйти"></a>
    <span class="manage fa fa-chevron-right"></span>
</div>