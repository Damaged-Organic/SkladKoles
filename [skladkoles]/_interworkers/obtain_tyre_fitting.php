<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#DDC PARAMETERS
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'tyre_fitting' => array('id', 'radius', 'cars', 'SUVs', 'jeeps')
];

$entity_orders = [
    'radius' => 'ASC'
];
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$tyre_fitting = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, NULL, $entity_orders, NULL);


return $tyre_fitting;
?>