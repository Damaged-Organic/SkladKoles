<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$directory = ( !empty($supplied_data['directory']) ) ? $supplied_data['directory'] : $_AREA->{$C_E::_DIRECTORY};

$table = $C_S::DB_PREFIX_alpha."heads";

$head = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->static_data_cell($table, $directory, $_AREA->{$C_E::_LANGUAGE});

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($head) ) {
    throw new procException("Corrupt data array");
}

return $head[0];
?>
