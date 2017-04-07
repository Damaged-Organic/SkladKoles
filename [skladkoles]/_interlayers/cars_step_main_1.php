<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $auto_mark = $supplied_data;
}

#DDC car parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'vehicles' => [
        'vendor', 'car'
    ]
];

$entity_conditions = [
    'vendor' => ['=', str_replace('_', ' ', $auto_mark), '']
];

$entity_orders = ['car' => 'ASC'];

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$cars = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, NULL, ['group' => ['car']]);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($cars) ) {
    return FALSE;
}
#END\DDC car parameters

?>
<option value selected>Модель</option>
<?php foreach($cars as $key => $value): ?>
    <option value="<?=$value['car']?>"><?=$value['car']?></option>
<?php endforeach ?>