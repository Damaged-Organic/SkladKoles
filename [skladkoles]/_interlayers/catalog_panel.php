<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_catalog_panel = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$catalog_title = $_BOOT->involve_object(
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

$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
        'AR_location' => "navigation",
        'AR_method'   => "set_items_number"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<a href="#" class="filter-view"><?=$xml_catalog_panel->filter_label?></a>
<span class="current-page">
    Вы находитесь на
    <span id="catalogPage"><?=( !empty($_SESSION['pagination']['current_page']) ) ? $_SESSION['pagination']['current_page'] : 1;?></span>
    странице раздела "<?=$catalog_title?>"
</span>
<div class="select">
    <a href="#"><?=( !empty($_SESSION['pagination']['records_per_page']) ) ? $_SESSION['pagination']['records_per_page'] : 12;?></a>
    <ul data-landmark='<?=$data_landmark?>'>
        <li class="count" data-count="12">12</li>
        <li class="count" data-count="24">24</li>
        <li class="count" data-count="48">48</li>
    </ul>
</div>
<a href="#" class="cell-view" title="<?=$xml_catalog_panel->view->cell?>"><span class="fa fa-th-large"></span></a>
<a href="#" class="list-view" title="<?=$xml_catalog_panel->view->list?>"><span class="fa fa-th-list"></span></a>