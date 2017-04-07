<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $news_single = $supplied_data;
}

$xml_calendar = $_BOOT->involve_object("XML_Handler")->get_xml(
    "calendar",
    $_AREA->{$C_E::_LANGUAGE}
);

$month_names_pattern = array_map(
    function($item) { return "/{$item}/"; },
    array_keys((array)$xml_calendar->months)
);
?>
<article class="centered news-detailed">
    <h2><?=$news_single['title']?></h2>
    <figure><img src="<?=$this->load_resource("news/{$news_single['image']}")?>" alt="<?=$news_single['image']?>"></figure>
    <time datetime="<?=date('Y-m-d', $news_single['date_created'])?>">
        <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $news_single['date_created'])))?>
    </time>
    <?=$news_single['text']?>
    <ul class="social-likes" id="social" data-url="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$news_single['id']}", $_AREA->{$C_E::_LANGUAGE})?>" data-title="<?=$news_single['title']?>" data-counters="yes" data-zeroes="yes">
        <li class="facebook">Facebook</li>
        <li class="twitter">Twitter</li>
        <li class="vkontakte">Vkontakte</li>
        <li class="plusone">Google +</li>
    </ul>
    <?php if( defined('WHITELIGHT') ): ?>
        <a href="<?=$this->get_current_link("news/{$news_single['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
    <?php endif; ?>
</article>