<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($item_data, $item_id, $item_type, $item_table, $promo_data) = $supplied_data;
}

$delete_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_item",
    $C_AR::AR_METHOD   => "modification_delete",
    '_request[item_type]' => $item_type,
    '_request[item_table]' => $item_table,
    '_request[item_id]' => NULL
];

$delete_image_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_item",
    $C_AR::AR_METHOD   => "delete_image",
    '_request[item_type]'  => $item_type,
    '_request[item_image]' => NULL
];

if( ($item_type == 'rims') || ($item_type == 'exclusive_rims') )
{
    $add_landmark = [
        $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
        $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
        $C_AR::AR_LOCATION => "process_item",
        $C_AR::AR_METHOD   => "modification_add",
        '_request[item_type]'  => $item_type,
        '_request[item_table]' => $item_table,
        '_request[item][brand]'      => $item_data['item']['brand'],
        '_request[item][model_name]' => $item_data['item']['model_name'],
        '_request[item][code]'       => $item_data['item']['code'],
        '_request[item][paint]'      => $item_data['item']['paint'],
    ];
} else {
    $add_landmark = [
        $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
        $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
        $C_AR::AR_LOCATION => "process_item",
        $C_AR::AR_METHOD   => "modification_add",
        '_request[item_type]'  => $item_type,
        '_request[item_table]' => $item_table,
        '_request[item][brand]'      => $item_data['item']['brand'],
        '_request[item][model_name]' => $item_data['item']['model_name']
    ];
}

$danger_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_item",
    $C_AR::AR_METHOD   => "item_delete",
    '_request[item_type]'  => $item_type,
    '_request[item_table]' => $item_table,
    '_request[item_id]'    => NULL,
    '_request[item_image]' => NULL
];
?>
<header id="header"><h1>РЕДАКТИРОВАНИЕ ТОВАРНОЙ ПОЗИЦИИ</h1></header>
<section class="content itemZone">
    <div class="breadcrumbs">
        <a href="<?=$this->get_current_link("add")?>" class="transition">вернуться на главную</a>
    </div>
    <form action="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="goodsEdit" autocomplete="off" enctype="multipart/form-data">
        <!--<div class="picture">
            <figure class="borderBox">
                <?php $item_images = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image(array_merge($item_data['item'], ['type' => $item_type])); ?>
                <img src="<?=$this->load_resource("items/{$item_images}", TRUE)?>" alt="<?=$item_images?>">
                <?php $delete_image_landmark['_request[item_image]'] = "items/{$item_images}"; ?>
            </figure>
            <input type="hidden" name="_request[unique_code]" value="<?=$item_data['item']['unique_code']?>">
            <?php if( !empty($item_data['modifications']) ): ?>
                <span class="delete transition" data-action="deletePicture" data-landmark='<?=json_encode($delete_image_landmark, JSON_UNESCAPED_SLASHES)?>'></span>
                <input type="file" name="file[]" value="" id="file">
                <label for="file" class="borderBox transition">Обновить фото</label>
            <?php endif; ?>
        </div>-->
        <?php if( !empty($item_data['modifications']) ): ?>
            <div class="photos">
                <input type="file" name="file[]" value="" id="file" accept=".jpg, .png, .gif" multiple>
                <label for="file" class="borderBox transition">Обновить фотографии</label>
                <?php $item_images = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image(array_merge($item_data['item'], ['type' => $item_type]), $single_thumb = FALSE, $return_default = FALSE); ?>
                <div id="hiddenPictureName">
                    <?php if( ($item_type == 'rims') || ($item_type == 'exclusive_rims') ): ?>
                        <?php $item_image = "{$item_data['item']['brand']}_{$item_data['item']['model_name']}_{$item_data['item']['code']}_{$item_data['item']['paint']}"; ?>
                        <input type="hidden" name="_request[item_image]" value="<?=$item_image?>" class="kludge">
                    <?php else: ?>
                        <?php $item_image = "{$item_data['item']['brand']}_{$item_data['item']['model_name']}"; ?>
                        <input type="hidden" name="_request[item_image]" value="<?=$item_image?>" class="kludge">
                    <?php endif; ?>
                </div>
                <ul id="photoList">
                    <?php if( $item_images ): ?>
                        <?php foreach($item_images as $key => $value): ?>
                            <li>
                                <input type="checkbox" name="_request[delete_images][]" value="<?=$value['thumb']?>" id="delete_<?=$key?>">
                                <label for="delete_<?=$key?>">
                                    <img src="<?=$this->load_resource("items/{$value['thumb']}", TRUE)?>" alt="<?=$value['thumb']?>">
                                </label>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><p>К текущему товару не привязано никаких фотографий</p></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="info">
            <label for="brand">
                <span class="borderBox">Бренд</span>
                <input type="text" name="_request[brand]" value="<?=$item_data['item']['brand']?>" id="brand" placeholder="Введите название бренда..." class="borderBox">
            </label>
            <label for="naming">
                <span class="borderBox">Название</span>
                <input type="text" name="_request[model_name]" value="<?=$item_data['item']['model_name']?>" id="naming" placeholder="Введите название товарной позиции...." class="borderBox">
            </label>
            <?php if( ($item_type == 'rims') || ($item_type == 'exclusive_rims') ): ?>
                <label for="code">
                    <span class="borderBox">Код диска</span>
                    <input type="text" name="_request[code]" value="<?=$item_data['item']['code']?>" id="code" placeholder="Введите код диска..." class="borderBox">
                </label>
                <label for="color">
                    <span class="borderBox">цвет</span>
                    <input type="text" name="_request[paint]" value="<?=$item_data['item']['paint']?>" id="color" placeholder="Введите цвет товарной позиции..." class="borderBox">
                </label>
            <?php endif; ?>
            <div class="editorWrapper">
                <p class="fieldTitle">Текст товара</p>
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
                            <span class="ql-format-separator"></span>
                            <span class="ql-format-button ql-bullet"></span>
                        </span>
                        <!-- <span class="ql-format-group">
                            <span class="ql-format-button ql-link"></span>
                        </span>
                        <span class="ql-format-group">
                            <select class="ql-align">
                                <option value="left" selected></option>
                                <option value="center"></option>
                                <option value="right"></option>
                                <option value="justify"></option>
                            </select>
                        </span> -->
                    </div>
                    <div class="container borderBox transition"><?=$item_data['description']['description']?></div>
                    <textarea name="_request[description]"><?=$item_data['description']['description']?></textarea>
                </div>
                <label for="video">
                    <span class="borderBox">Видео ID</span>
                    <input type="text" name="_request[video]" value="<?=( !empty($item_data['item']['video']) ) ? $item_data['item']['video'] : NULL ?>" id="video" placeholder="Введите ID видео из YouTube..." class="borderBox">
                </label>
            </div>
            <label for="views" class="gapTop">
                <span class="borderBox">просмотров</span>
                <input type="text" name="_request[views]" value="<?=$item_data['item']['views']?>" id="views" placeholder="Текущее количество просмотров..." class="borderBox">
            </label>
            <?php if( !empty($item_data['modifications']) ): ?>
                <div class="checkboxArea">
                    <select name="_request[promotion_id]" id="sale">
                        <option value="" <?=( empty($item_data['item']['promotion_id']) ) ? "selected" : "";?>>без акции</option>
                        <?php foreach($promo_data as $value): ?>
                            <?php if( !empty($value['hashtag']) ): ?>
                                <option value="<?=$value['id']?>" <?=( $value['id'] == $item_data['item']['promotion_id'] ) ? "selected" : "";?>><?=$value['hashtag']?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <input type="checkbox" name="_request[is_top]" value="Y" id="top" <?=($item_data['item']['is_top'] === 'Y') ? "checked" : NULL;?>>
                    <label for="top" class="transition">хит продаж</label>
                </div>
            <?php endif; ?>
        </div>

        <aside class="additional">
            <?php if( !empty($item_data['modifications']) ): ?>
                <?php if( ($item_type == 'rims') || ($item_type == 'exclusive_rims') ): ?>
                    <table class="borderBox">
                        <tr>
                            <th>Артикул</th>
                            <th>PCD STUD</th>
                            <th>PCD DIA</th>
                            <th>PCD DIA EXTRA</th>
                            <th>R</th>
                            <th>W</th>
                            <th>ET</th>
                            <th>CH</th>
                            <th>TYPE</th>
                            <th>Наличие</th>
                            <th>Розн.</th>
                            <th>Дилл.</th>
                            <th>Промо</th>
                            <th></th>
                        </tr>
                        <?php foreach($item_data['modifications'] as $key => $modification): ?>
                            <tr>
                                <td><?=$modification['unique_code']?></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][pcd_stud]" value="<?=$modification['pcd_stud']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][pcd_dia]" value="<?=$modification['pcd_dia']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][pcd_dia_extra]" value="<?=$modification['pcd_dia_extra']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][r]" value="<?=$modification['r']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][w]" value="<?=$modification['w']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][et]" value="<?=$modification['et']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][ch]" value="<?=$modification['ch']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][rim_type]" value="<?=$modification['rim_type']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][stock]" value="<?=$modification['stock']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][retail]" value="<?=$modification['retail']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][dealer]" value="<?=$modification['dealer']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][promo]" value="<?=$modification['promo']?>" placeholder="..."></td>
                                <?php $delete_landmark['_request[item_id]'] = $modification['id']; ?>
                                <td>
                                    <?php if( $key != 0 ): ?>
                                        <a href="#" class="delete transition borderBox" data-action="deleteRow" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></a>
                                    <?php else: ?>
                                        <a href="#" class="delete transition borderBox"></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <table class="borderBox">
                        <tr>
                            <th>Артикул</th>
                            <th>Сезон</th>
                            <th>R</th>
                            <th>W</th>
                            <th>H</th>
                            <th>LOAD RATE</th>
                            <th>SPEED</th>
                            <th>EXTRA</th>
                            <th>Наличие</th>
                            <th>Розн.</th>
                            <th>Дилл.</th>
                            <th>Промо</th>
                            <th></th>
                        </tr>
                        <?php foreach($item_data['modifications'] as $modification): ?>
                            <tr>
                                <td><?=$modification['unique_code']?></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][season]" value="<?=$modification['season']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][r]" value="<?=$modification['r']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][w]" value="<?=$modification['w']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][h]" value="<?=$modification['h']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][load_rate]" value="<?=$modification['load_rate']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][speed]" value="<?=$modification['speed']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][extra]" value="<?=$modification['extra']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][stock]" value="<?=$modification['stock']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][retail]" value="<?=$modification['retail']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][dealer]" value="<?=$modification['dealer']?>" placeholder="..."></td>
                                <td><input type="text" name="_request[items][<?=$modification['id']?>][promo]" value="<?=$modification['promo']?>" placeholder="..."></td>
                                <?php $delete_landmark['_request[item_id]'] = $modification['id']; ?>
                                <td>
                                    <a href="#" class="delete transition borderBox" data-action="deleteRow" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
                <a href="#" class="add transition borderBox" data-action="addRow" data-landmark='<?=json_encode($add_landmark, JSON_UNESCAPED_SLASHES)?>'>добавить характеристики</a>
            <?php endif; ?>
        </aside>
        <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
        <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE})?>">
        <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="process_item">
        <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="item_update">
        <input type="hidden" name="_request[item_id]" value="<?=$item_id?>">
        <input type="hidden" name="_request[item_type]" value="<?=$item_type?>">
        <input type="hidden" name="_request[item_table]" value="<?=$item_table?>">
        <?php if( empty($item_data['modifications']) ): ?>
            <input type="hidden" name="_request[reload]" value="true">
        <?php endif; ?>
        <div class="progressBar borderBox transition"></div>
        <button type="submit" class="borderBox transition gapTop">сохранить</button>
    </form>
    <div class="dangerArea">
        <hr>
        <?php
        $danger_landmark['_request[item_id]']    = $item_data['item']['id'];
        $danger_landmark['_request[item_image]'] = ( !empty($item_image) ) ? $item_image : NULL;
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
