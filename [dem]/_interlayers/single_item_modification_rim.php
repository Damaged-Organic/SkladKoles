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
        <a href="#" class="delete transition borderBox" data-action="deleteRow" data-landmark='<?=json_encode($delete_landmark, JSON_UNESCAPED_SLASHES)?>'></a>
    </td>
</tr>