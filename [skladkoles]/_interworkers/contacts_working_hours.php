<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$table = $C_S::DB_PREFIX_alpha."contacts_working_hours";

$contacts_working_hours = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->static_data_cell($table, NULL, $_AREA->{$C_E::_LANGUAGE});

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($contacts_working_hours) ) {
    throw new procException("Corrupt static data cell");
}

return $contacts_working_hours;
?>