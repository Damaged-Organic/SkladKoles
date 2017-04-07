<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $tyre_fitting = $supplied_data;
}

$xml_tyre_fitting = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<article class="item">
    <h2><?=$xml_tyre_fitting->headline?></h2>
    <p><?=$xml_tyre_fitting->body->item_1?></p>
    <p><?=$xml_tyre_fitting->body->item_2?></p>
    <p class="colorized"><?=$xml_tyre_fitting->bottom_line?></p>
</article>
<table>
    <caption><?=$xml_tyre_fitting->price_list?></caption>
    <tr>
        <th><?=$xml_tyre_fitting->table->radius?></th>
        <th><?=$xml_tyre_fitting->table->cars?></th>
        <th><?=$xml_tyre_fitting->table->SUVs?></th>
        <th><?=$xml_tyre_fitting->table->jeeps?></th>
    </tr>
    <?php foreach($tyre_fitting as $value): ?>
        <tr>
            <td><?=$value['radius']?></td>
            <td><?=( $value['cars'] != 0 ) ? "UAH {$value['cars']}" : "&ndash;";?></td>
            <td><?=( $value['SUVs'] != 0 ) ? "UAH {$value['SUVs']}" : "&ndash;";?></td>
            <td><?=( $value['jeeps'] != 0 ) ? "UAH {$value['jeeps']}" : "&ndash;";?></td>
        </tr>
    <?php endforeach; ?>
</table>