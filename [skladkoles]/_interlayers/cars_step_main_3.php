<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $auto_year = $supplied_data;
}

if( empty($_SESSION['items_filter_cars_main']['auto-mark']) ||
    empty($_SESSION['items_filter_cars_main']['auto-model'])) {
    return FALSE;
}

#DDC car parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'vehicles' => [
        'vendor', 'car', 'year', 'modification'
    ]
];

$entity_conditions = [
    'vendor' => ['=', str_replace('_', ' ', $_SESSION['items_filter_cars_main']['auto-mark']), ''],
    'car'    => ['=', $_SESSION['items_filter_cars_main']['auto-model'], 'AND'],
    'year'   => ['=', $auto_year, 'AND']
];

$entity_orders = ['modification' => 'ASC'];

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$cars = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, NULL, ['group' => ['modification']]);

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($cars) ) {
    return FALSE;
}
#END\DDC car parameters
?>
<option value selected>Модификация</option>
<?php foreach($cars as $value): ?>
    <option value="<?=$value['modification']?>"><?=$value['modification']?></option>
<?php endforeach ?>