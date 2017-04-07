<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_news = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

if( empty($supplied_data) ): ?>
    <p class="empty"><?=$xml_news->no_data?></p>
<?php else:
    $news = array_chunk($supplied_data, 3);

    $xml_calendar = $_BOOT->involve_object("XML_Handler")->get_xml(
        "calendar",
        $_AREA->{$C_E::_LANGUAGE}
    );

    $month_names_pattern = array_map(
        function($item) { return "/{$item}/"; },
        array_keys((array)$xml_calendar->months)
    );

    $is_main = ( $_AREA->{$C_E::_DIRECTORY} === 'main' );

    if( !$is_main )
    {
        $data_landmark = json_encode(
            array(
                'IC_token'    => $_SESSION['IC_token']['hash'],
                'AR_origin'   => $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE}),
                'AR_location' => "news",
                'AR_method'   => "get_news"
            ),
            JSON_UNESCAPED_SLASHES
        );
    }

    if( !$is_main ): ?>
        <div class="lift-data" <?=( $data_landmark ) ? "data-landmark='{$data_landmark}'" : ""; ?>>
    <?php endif; ?>
        <?php foreach($news as $value): ?>
            <?php if( count($value) == 3 ): ?>
                <?php $state_class = ( !empty($state_class) ) ? (( $state_class == "state-one" ) ? "state-two" : "state-one") : "state-one"; ?>
                <div class="news-container <?=$state_class?>">
                    <div class="column first">
                        <?php $key = 0; ?>
                        <div class="item lift">
                            <?php if( defined('WHITELIGHT') ): ?>
                                <a href="<?=$this->get_current_link("news/{$value[$key]['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
                            <?php endif; ?>
                            <figure>
                                <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>">
                                    <img src="<?=$this->load_resource("news/{$value[$key]['image']}")?>" alt="<?=$value[$key]['image']?>">
                                </a>
                            </figure>
                            <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>"><?=$value[$key]['title']?></a>
                            <time datetime="<?=date('Y-m-d', $value[$key]['date_created'])?>">
                                <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value[$key]['date_created'])))?>
                            </time>
                            <?php
                                $article_text = strip_tags($value[$key]['text']);
                                $article_text = ( mb_strlen($article_text, 'utf-8') > 600 ) ? trim(mb_substr($article_text, 0, 600, 'utf-8')) : $article_text;
                            ?>
                            <p><?=$article_text?></p>
                        </div>
                    </div>
                    <div class="column second">
                        <?php $key++; ?>
                        <div class="item lift">
                            <?php if( defined('WHITELIGHT') ): ?>
                                <a href="<?=$this->get_current_link("news/{$value[$key]['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
                            <?php endif; ?>
                            <figure>
                                <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>">
                                    <img src="<?=$this->load_resource("news/{$value[$key]['image_thumb']}")?>" alt="<?=$value[$key]['image']?>">
                                </a>
                            </figure>
                            <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>"><?=$value[$key]['title']?></a>
                            <time datetime="<?=date('Y-m-d', $value[$key]['date_created'])?>">
                                <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value[$key]['date_created'])))?>
                            </time>
                            <?php
                                $article_text = strip_tags($value[$key]['text']);
                                $article_text = ( mb_strlen($article_text, 'utf-8') > 500 ) ? trim(mb_substr($article_text, 0, 500, 'utf-8')) : $article_text;
                            ?>
                            <p><?=$article_text?></p>
                        </div>
                        <?php $key++; ?>
                        <div class="item lift">
                            <?php if( defined('WHITELIGHT') ): ?>
                                <a href="<?=$this->get_current_link("news/{$value[$key]['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
                            <?php endif; ?>
                            <figure>
                                <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>">
                                    <img src="<?=$this->load_resource("news/{$value[$key]['image_thumb']}")?>" alt="<?=$value[$key]['image']?>">
                                </a>
                            </figure>
                            <a href="<?=$this->get_current_link("news_detailed/{$value[$key]['id']}")?>"><?=$value[$key]['title']?></a>
                            <time datetime="<?=date('Y-m-d', $value[$key]['date_created'])?>">
                                <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value[$key]['date_created'])))?>
                            </time>
                            <?php
                                $article_text = strip_tags($value[$key]['text']);
                                $article_text = ( mb_strlen($article_text, 'utf-8') > 500 ) ? trim(mb_substr($article_text, 0, 500, 'utf-8')) : $article_text;
                            ?>
                            <p><?=$article_text?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="news-container">
                    <?php if( !empty($value[0]) ): ?>
                        <div class="column first">
                            <div class="item lift">
                                <?php if( defined('WHITELIGHT') ): ?>
                                    <a href="<?=$this->get_current_link("news/{$value[0]['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
                                <?php endif; ?>
                                <figure><img src="<?=$this->load_resource("news/{$value[0]['image']}")?>" alt="<?=$value[0]['image']?>"></figure>
                                <a href="<?=$this->get_current_link("news_detailed/{$value[0]['id']}")?>"><?=$value[0]['title']?></a>
                                <time datetime="<?=date('Y-m-d', $value[0]['date_created'])?>">
                                    <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value[0]['date_created'])))?>
                                </time>
                                <?php
                                    $article_text = strip_tags($value[0]['text']);
                                    $article_text = ( mb_strlen($article_text, 'utf-8') > 600 ) ? trim(mb_substr($article_text, 0, 600, 'utf-8')) : $article_text;
                                ?>
                                <p><?=$article_text?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if( !empty($value[1]) ): ?>
                        <div class="column second">
                            <div class="item lift">
                                <?php if( defined('WHITELIGHT') ): ?>
                                    <a href="<?=$this->get_current_link("news/{$value[1]['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать новость" target="_blank"></a>
                                <?php endif; ?>
                                <figure><img src="<?=$this->load_resource("news/{$value[1]['image']}")?>" alt="<?=$value[1]['image']?>"></figure>
                                <a href="<?=$this->get_current_link("news_detailed/{$value[1]['id']}")?>"><?=$value[1]['title']?></a>
                                <time datetime="<?=date('Y-m-d', $value[1]['date_created'])?>">
                                    <?=preg_replace($month_names_pattern, (array)$xml_calendar->months, strtolower(date('F, d, Y', $value[1]['date_created'])))?>
                                </time>
                                <?php
                                    $article_text = strip_tags($value[1]['text']);
                                    $article_text = ( mb_strlen($article_text, 'utf-8') > 600 ) ? trim(mb_substr($article_text, 0, 600, 'utf-8')) : $article_text;
                                ?>
                                <p><?=$article_text?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php if( !$is_main ): ?>
        </div>
    <?php endif; ?>
<?php endif; ?>