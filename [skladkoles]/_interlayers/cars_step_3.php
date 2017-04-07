<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $car_data = $supplied_data;
}

if( ($car_data['auto-mark'] = $_BOOT->involve_object("InputPurifier")->purge_string($car_data['auto-mark'])) === FALSE ||
    ($car_data['auto-model'] = $_BOOT->involve_object("InputPurifier")->purge_string($car_data['auto-model'])) === FALSE ||
    ($car_data['auto-year'] = $_BOOT->involve_object("InputPurifier")->purge_string($car_data['auto-year'])) === FALSE ) {
    return FALSE;
}

#DDC image parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'items_brands_cars' => [
        'car', 'image'
    ]
];

$entity_conditions = ['car' => ['=', $car_data['auto-mark'], '']];

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$brand_image = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, NULL, NULL)[0];

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($brand_image) ) {
    return FALSE;
}
#END\DDC image parameters

#DDC car parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'vehicles' => [
        'vendor', 'car', 'year', 'modification'
    ]
];

$entity_conditions = [
    'vendor' => ['=', str_replace('_', ' ', $car_data['auto-mark']), ''],
    'car'    => ['=', $car_data['auto-model'], 'AND'],
    'year'   => ['=', $car_data['auto-year'], 'AND']
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

$xml_cars_brands_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    'cars_popup',
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<h2><?=$xml_cars_brands_popup->subheadline_modification?></h2>
<div class="choice-sequence">
    <ul>
        <li><img src="<?=$this->load_resource("images/auto-mark/{$brand_image['image']}")?>" alt="<?=$car_data['auto-mark']?>"></li>
        <li><?=$car_data['auto-model']?></li>
        <li><?=$car_data['auto-year']?></li>
    </ul>
</div>
<?php foreach($cars as $value): ?>
    <input type="radio" name="<?=$C_E::_REQUEST?>[auto-modification]" value="<?=$value['modification']?>" id="<?=$value['modification']?>" data-step="3">
    <label for="<?=$value['modification']?>"><?=$value['modification']?></label>
<?php endforeach; ?>