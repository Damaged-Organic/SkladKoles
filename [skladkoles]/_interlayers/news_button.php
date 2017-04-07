<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$news_per_lift = 6;

if( $_BOOT->involve_object("DB_Handler")->get_records_number($C_S::DB_PREFIX_alpha.'news') <= $news_per_lift) {
    return FALSE;
}

$xml_news_button = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$is_main = ( $_AREA->{$C_E::_DIRECTORY} === 'main' );

if( $is_main ): ?>
    <div class="more-button">
        <a href="<?=$this->get_current_link("news")?>"><?=$xml_news_button->button_more_news?></a>
    </div>
<?php else: ?>
    <div class="more-button lift-button">
        <a href="#"><?=$xml_news_button->button_get_more_news?></a>
    </div>
<?php endif; ?>