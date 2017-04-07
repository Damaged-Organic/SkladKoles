<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $promotions = $supplied_data;
}

$delete_landmark = [
    $_IC::IC_TOKEN      => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN    => $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION  => "promotions",
    $C_AR::AR_METHOD    => "delete_promotions",
    '_request[promo_id]'    => NULL,
    '_request[promo_image]' => NULL
];
?>
<header id="header"><h1>Редактирование Акций</h1></header>
<section class="content saleZone">
    <div class="breadcrumbs">
        <a href="<?=$this->get_current_link("add")?>" class="transition">вернуться на главную</a>
    </div>
    <form action="<?=$this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="sale" autocomplete="off" enctype="multipart/form-data">
        <input type="file" name="file[]" value="" id="files" multiple="true" accept=".jpg, .jpeg, .png, .gif">
        <label for="files" class="borderBox transition"></label>
        <div class="progressBar">
            <div class="progress transition"></div>
        </div>
        <div class="viewZone">
            <?php if( !empty($promotions) ): ?>
                <?php foreach($promotions as $value): ?>
                    <div class="item">
                        <figure class="borderBox">
                            <img src="<?=$this->load_resource("slider/{$value['image']}", TRUE)?>" alt="<?=$value['image']?>">
                        </figure>
                        <div class="hashtag">
                            <p>Хештег(#):</p>
                            <label>
                                <input type="text" name="_request[<?=$value['id']?>][hashtag]" value="<?=( !empty($value['hashtag']) ) ? $value['hashtag'] : '';?>" placeholder="#">
                            </label>
                        </div>
                        <div class="date">
                            <p>Выберите дату окончания акции</p>
                            <label><input type="text" name="_request[<?=$value['id']?>][end_date]" value="<?=( !empty($value['end_date']) ) ? date('d-m-Y', $value['end_date']) : '';?>" class="saleDate" placeholder="дд-мм-гг"></label>
                        </div>
                        <div class="editorWrapper">
                            <p class="fieldTitle">Текст акции</p>
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
                                <div class="container borderBox transition"><?=$value['description']?></div>
                                <textarea name="_request[<?=$value['id']?>][description]"><?=$value['description']?></textarea>
                            </div>
                        </div>
                        <?php
                            $delete_landmark['_request[promo_id]']    = $value['id'];
                            $delete_landmark['_request[promo_image]'] = "slider/{$value['image']}";
                        ?>
                        <span class="delete transition" data-action="deleteSlide" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
        <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE})?>">
        <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="promotions">
        <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="add_promotions">
        <button type="submit" class="borderBox transition gapTop" data-action="save_promotions">сохранить</button>
    </form>
    <div class="confirmDialog transition">
        <span class="choose transition yes" data-choice="true"></span>
        <span class="choose transition no" data-choice="false"></span>
    </div>
    <div id="message" class="borderBox"></div>
</section>