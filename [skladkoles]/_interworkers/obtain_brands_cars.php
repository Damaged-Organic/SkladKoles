<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$table = $C_S::DB_PREFIX_alpha."items_brands_cars";

$brands_cars = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->static_data_cell($table);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($brands_cars) ) {
    throw new procException("Corrupt data array");
}

return $brands_cars;
?>