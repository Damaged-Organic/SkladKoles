<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $news_recent = $supplied_data;
}

$xml_news_recent = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$xml_calendar = $_BOOT->involve_object("XML_Handler")->get_xml(
    "calendar",
    $_AREA->{$C_E::_LANGUAGE}
);

$month_names_pattern = array_map(
    function($item) { return "/{$item}/"; },
    array_keys((array)$xml_calendar->months)
);
?>
<div class="track-lane gaps"><h2><?=$xml_news_recent->headline?></h2></div>
<div class="centered news-detailed best">
    <?php foreach($news_recent as $value): ?>
        <div class="item">
            <figure>
                <a href="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$value['id']}", $_AREA->{$C_E::_LANGUAGE})?>">
                    <img src="<?=$this->load_resource("news/{$value['image']}")?>" alt="<?=$value['image']?>">
                </a>
            </figure>
            <a href="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$value['id']}", $_AREA->{$C_E::_LANGUAGE})?>">
                <?=$value['title']?>
            </a>
            <time datetime="<?=date('Y-m-d', $value['date_created'])?>">
                <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value['date_created'])))?>
            </time>
            <?php
                $article_text = strip_tags($value['text']);
                $article_text = ( mb_strlen($article_text, 'utf-8') > 300 ) ? trim(mb_substr($article_text, 0, 300, 'utf-8')) : $article_text;
            ?>
            <p><?=$article_text?></p>
        </div>
    <?php endforeach; ?>
</div>