<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#DDC PARAMETERS
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'regions' => array('id', 'region', 'image')
];

$entity_orders = [
    'region' => 'ASC'
];
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$regions = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, NULL, $entity_orders, NULL);


return $regions;
?>