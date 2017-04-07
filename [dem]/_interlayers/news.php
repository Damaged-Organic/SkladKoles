<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $news_data = $supplied_data[0];
}

$article_id = ( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) ? $_AREA->{$C_E::_ARGUMENTS}[1] : NULL;

$delete_image_landmark = [
    $_IC::IC_TOKEN      => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN    => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$article_id}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION  => "process_news",
    $C_AR::AR_METHOD    => "delete_image",
    '_request[news_id]' => $news_data['id']
];

$danger_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$article_id}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_news",
    $C_AR::AR_METHOD   => "item_delete",
    '_request[news_id]'    => NULL,
    '_request[news_image]' => NULL
];
?>
<header id="header"><h1>редактирование новости</h1></header>
<section class="content newsZone">
    <div class="breadcrumbs">
        <a href="<?=$this->get_current_link("add")?>" class="transition">вернуться на главную</a>
    </div>
    <form action="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="news" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="_request[news_id]" value="<?=$news_data['id']?>">
        <div class="picture">
            <figure class="borderBox">
                <img src="<?=$this->load_resource("news/{$news_data['image']}", TRUE)?>" alt="<?=$news_data['image']?>">
            </figure>
            <?php $delete_image_landmark['_request[news_image]'] = "news/{$news_data['image_thumb']}"; ?>
            <span class="delete transition" data-action="deletePicture" data-landmark='<?=json_encode($delete_image_landmark, JSON_UNESCAPED_SLASHES)?>'></span>
            <input type="file" name="file[]" value="" id="file" accept=".jpg, .png, .gif">
            <label for="file" class="borderBox transition">Обновить фото</label>
        </div>
        <div class="info">
            <label for="newsTitle">
                <span>Заголовок новости</span>
                <input type="text" name="_request[title]" value="<?=$news_data['title']?>" id="newsTitle" class="transition borderBox">
            </label>
            <!--QUILL-->
            <div class="editorWrapper">
                <p class="fieldTitle">Текст новости</p>
                <div class="editor borderBox">
                    <div class="toolbar">
                        <span class="ql-format-group">
                            <span class="ql-format-button ql-bold"></span>
                            <span class="ql-format-separator"></span>
                            <span class="ql-format-button ql-italic"></span>
                            <span class="ql-format-separator"></span>
                            <span class="ql-format-button ql-strike"></span>
                            <span class="ql-format-separator"></span>
                            <span class="ql-format-button ql-underline"></span>
                        </span>
                        <span class="ql-format-group">
                            <span class="ql-format-button ql-link"></span>
                        </span>
                        <span class="ql-format-group">
                            <select class="ql-align">
                                <option value="left" selected></option>
                                <option value="center"></option>
                                <option value="right"></option>
                                <option value="justify"></option>
                            </select>
                        </span>
                    </div>
                    <div class="container borderBox transition"><?=$news_data['text']?></div>
                    <textarea name="_request[text]"><?=$news_data['text']?></textarea>
                </div>
            </div>
            <!--/QUILL-->
            <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
            <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE})?>">
            <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="process_news">
            <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="news_update">
        </div>
        <div class="progressBar borderBox transition"></div>
        <button type="submit" class="borderBox transition gapTop">сохранить</button>
    </form>
    <div class="dangerArea">
        <hr>
        <?php
            $danger_landmark['_request[news_id]']    = $news_data['id'];
            $danger_landmark['_request[news_image]'] = "news/{$news_data['image_thumb']}";
        ?>
        <a class="delete borderBox transition" data-action="delete" data-landmark='<?=json_encode($danger_landmark, JSON_UNESCAPED_SLASHES)?>'>удалить</a>
        <hr>
    </div>
    <div class="confirmDialog transition">
        <span class="choose transition yes" data-choice="true"></span>
        <span class="choose transition no" data-choice="false"></span>
    </div>
    <div id="message" class="borderBox"></div>
</section>