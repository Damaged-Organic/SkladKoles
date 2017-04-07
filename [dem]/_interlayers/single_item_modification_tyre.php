<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($modification, $delete_landmark) = $supplied_data;
}
?>
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