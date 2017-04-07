<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($category, $items_spares) = $supplied_data;
}

$add_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_spares",
    $C_AR::AR_METHOD   => "spares_add",
    '_request[category]' => $category
];

$delete_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "process_spares",
    $C_AR::AR_METHOD   => "spares_delete",
    '_request[spare_id]' => NULL
];
?>
<header id="header"><h1>редактирование комплектующих</h1></header>
<section class="content accessoriesZone">
    <div class="breadcrumbs">
        <a href="<?=$this->get_current_link("add")?>" class="transition">вернуться на главную</a>
    </div>
    <form action="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE})?>" method="POST" id="accessories" autocomplete="off">
        <table class="borderBox">
            <tr>
                <th>артикул</th>
                <th>бренд</th>
                <th>характеристики</th>
                <th>размер</th>
                <th>цена</th>
                <th></th>
            </tr>
            <?php foreach($items_spares as $value): ?>
                <tr>
                    <td><?=$value['unique_code']?></td>
                    <td>
                        <input type="text" name="_request[<?=$value['id']?>][brand]" value="<?=$value['brand']?>" placeholder="..." class="transition borderBox">
                    </td>
                    <td>
                        <input type="text" name="_request[<?=$value['id']?>][item_specs]" value="<?=$value['item_specs']?>" placeholder="..." class="transition borderBox">
                    </td>
                    <td>
                        <input type="text" name="_request[<?=$value['id']?>][size]" value="<?=$value['size']?>" placeholder="..." class="transition borderBox">
                    </td>
                    <td>
                        <input type="text" name="_request[<?=$value['id']?>][retail]" value="<?=$value['retail']?>" placeholder="..." class="transition borderBox">
                    </td>
                    <?php $delete_landmark['_request[spare_id]'] = $value['id']; ?>
                    <td>
                        <a href="#" class="delete transition" data-action="deleteRow" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <a href="#" class="add transition" data-action="addRow" data-landmark='<?=json_encode($add_landmark, JSON_UNESCAPED_SLASHES)?>'>добавить комплектующие</a>
        <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
        <input type="hidden" name="<?=$C_AR::AR_ORIGIN?>" value="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE})?>">
        <input type="hidden" name="<?=$C_AR::AR_LOCATION?>" value="process_spares">
        <input type="hidden" name="<?=$C_AR::AR_METHOD?>" value="spares_update">
        <button type="submit">сохранить</button>
    </form>
    <div class="confirmDialog transition">
        <span class="choose transition yes" data-choice="true"></span>
        <span class="choose transition no" data-choice="false"></span>
    </div>
    <div id="message" class="borderBox"></div>
</section>