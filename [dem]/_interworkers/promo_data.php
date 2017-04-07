<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#DDC PARAMETERS
$entity_content = array(
    $C_S::DB_PREFIX_alpha.'special_offers_content' => array($_AREA->{$C_E::_LANGUAGE})
);

$entity_tables_fields = array(
    $C_S::DB_PREFIX_alpha.'special_offers'         => array('id', 'record_order', 'end_date', 'hashtag'),
    $C_S::DB_PREFIX_alpha.'special_offers_content' => array('description', 'image')
);

$entity_orders = array(
    'id' => 'DESC'
);
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$special_offers = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell($entity_content, $entity_tables_fields, NULL, $entity_orders, NULL);

return $special_offers;
?>