<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($spare, $delete_landmark) = $supplied_data;
}
?>
<tr>
    <td><?=$spare['unique_code']?></td>
    <td>
        <input type="text" name="_request[<?=$value['id']?>][brand]" value="<?=$value['brand']?>" placeholder="..." class="transition borderBox">
    </td>
    <td>
        <input type="text" name="_request[<?=$spare['id']?>][item_specs]" value="<?=$spare['item_specs']?>" placeholder="..." class="transition borderBox">
    </td>
    <td>
        <input type="text" name="_request[<?=$spare['id']?>][size]" value="<?=$spare['size']?>" placeholder="..." class="transition borderBox">
    </td>
    <td>
        <input type="text" name="_request[<?=$spare['id']?>][retail]" value="<?=$spare['retail']?>" placeholder="..." class="transition borderBox">
    </td>
    <?php $delete_landmark['_request[spare_id]'] = $spare['id']; ?>
    <td>
        <a href="#" class="delete transition" data-action="deleteRow" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></a>
    </td>
</tr>