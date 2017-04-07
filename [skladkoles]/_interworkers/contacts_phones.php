<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$table = $C_S::DB_PREFIX_alpha."contacts_phones";

$contacts_phones = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->static_data_cell($table, NULL, NULL);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($contacts_phones) ) {
    throw new procException("Corrupt static data cell");
}

return $contacts_phones;
?>