<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $type = $supplied_data;
}

switch($type)
{
    case 'rims':
    case 'exclusive_rims':
        $table = $C_S::DB_PREFIX_alpha."items_brands_rims";
    break;

    case 'tyres':
    case 'exclusive_tyres':
        $table = $C_S::DB_PREFIX_alpha."items_brands_tyres";
    break;

    default:
        throw new notFoundException("URL request pattern mismatch");
    break;
}

$brands = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->static_data_cell($table);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($brands) ) {
    throw new procException("Corrupt data array");
}

usort($brands, function($a, $b) {
	return strnatcmp($a['brand'], $b['brand']);
});

return $brands;
?>
