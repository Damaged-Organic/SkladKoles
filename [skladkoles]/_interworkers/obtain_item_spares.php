<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

/*if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $category = $supplied_data;
}

if( !in_array($category, ['rings', 'bolts', 'nuts', 'locks', 'logos'], TRUE) ) {
    throw new procException("Wrong spares category");
}*/

#DDC parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'items_spares' => [
        'id', 'unique_code', 'type', 'brand', 'item_specs', 'size', 'retail'
    ]
];

$entity_conditions = [
    'unique_code' => ['<>', 'NULL', ''],
    //'type'        => ['=', $category, 'AND']
];
#END\DDC parameters

$items_spares = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, NULL, NULL);

return [/*$category, */$items_spares];
?>