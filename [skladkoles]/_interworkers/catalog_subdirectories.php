<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#DDC PARAMETERS
$entity_content = [
    $C_S::DB_PREFIX_alpha.'catalog_subdirectories_content' => [$_AREA->{$C_E::_LANGUAGE}]
];

$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'catalog_subdirectories'         => ['id', 'record_order', 'directory', 'image', 'image_thumb'],
    $C_S::DB_PREFIX_alpha.'catalog_subdirectories_content' => ['title', 'text']
];

$entity_orders = [
    'record_order' => 'ASC'
];
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$intro_about = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell($entity_content, $entity_tables_fields, NULL, $entity_orders, NULL);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($intro_about) ) {
    throw new procException("Corrupt dynamic data cell");
}

return $intro_about;
?>
