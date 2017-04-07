<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#DDC PARAMETERS
$entity_content = array(
    $C_S::DB_PREFIX_alpha.'how_to_buy_steps_content' => array($_AREA->{$C_E::_LANGUAGE})
);

$entity_tables_fields = array(
    $C_S::DB_PREFIX_alpha.'how_to_buy_steps'         => array('id', 'record_order', 'image'),
    $C_S::DB_PREFIX_alpha.'how_to_buy_steps_content' => array('title', 'text')
);

$entity_orders = array(
    'record_order' => 'ASC'
);
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$how_to_buy_steps = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell($entity_content, $entity_tables_fields, NULL, $entity_orders, NULL);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($how_to_buy_steps) ) {
    throw new procException("Corrupt dynamic data cell");
}

return $how_to_buy_steps;
?>