<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$tables = [$C_S::DB_PREFIX_alpha.'directories', $C_S::DB_PREFIX_alpha.'directories_content'];

if( !$_BOOT->involve_object("DB_Handler")->validate_tables($tables) ) {
    throw new procException("Table(s) does not exists");
}

$directories = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->menu_data_cell($tables, $_AREA->{$C_E::_LANGUAGE});

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($directories) ) {
    throw new procException("Corrupt directory data cell");
}

return $directories;
?>