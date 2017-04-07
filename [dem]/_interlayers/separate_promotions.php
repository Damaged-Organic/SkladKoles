<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($delete_landmark, $promotions) = $supplied_data;
}

foreach($promotions as $value): ?>
    <div class="item">
        <figure class="borderBox">
            <img src="<?=$this->load_resource("slider/{$value['image']}", TRUE)?>?reload=<?=time()?>" alt="<?=$value['image']?>">
        </figure>
        <div class="hashtag">
            <p>Хэштег</p>
            <label><input type="text" name="_request[<?=$value['id']?>][hashtag]" value="<?=( !empty($value['hashtag']) ) ? $value['hashtag'] : '';?>" placeholder="#"></label>
        </div>
        <div class="date">
            <p>Выберите дату окончания акции</p>
            <label><input type="text" name="_request[end_date]" value="<?=( !empty($value['end_date']) ) ? $value['end_date'] : '';?>" class="saleDate" placeholder="дд-мм-гг"></label>
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
                <div class="container borderBox transition"></div>
                <textarea name="_request[description]"></textarea>
            </div>
        </div>
        <?php
        $delete_landmark['_request[promo_id]']    = $value['id'];
        $delete_landmark['_request[promo_image]'] = "slider/{$value['image']}";
        ?>
        <span class="delete transition" data-action="deleteSlide" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></span>
    </div>
<?php endforeach; ?>