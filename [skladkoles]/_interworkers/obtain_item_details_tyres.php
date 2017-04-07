<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($item_id, $items_filter) = $supplied_data;
}

if( $_AREA->{$C_E::_ARGUMENTS}[0] === 'tyres' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_tyres';
} elseif( $_AREA->{$C_E::_ARGUMENTS}[0] === 'exclusive_tyres' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_tyres_exclusive';
}

if( ($item_id = $_BOOT->involve_object('InputPurifier')->purge_integer($item_id)) === FALSE ) {
    throw new notFoundException("Bad value");
} elseif( !$_BOOT->involve_object('DB_Handler')->is_value_exists($current_table, 'id', $item_id) ) {
    throw new notFoundException("Value does not exist");
}

$db_handler = $_BOOT->involve_object('DB_Handler');
$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogInput", [$db_handler])->increment_views($current_table, $item_id);

$input_entity_conditions = [
    'unique_code' => ['<>', 'NULL', '']
];

if( !empty($items_filter['filter_car_modification']['tyres']) )
{
    if( !empty($items_filter['filter_car_modification']['tyres']['specific']) )
    {
        end($items_filter['filter_car_modification']['tyres']['specific']);
        $last_key = key($items_filter['filter_car_modification']['tyres']['specific']);

        $array_size = count($items_filter['filter_car_modification']['tyres']['specific']);

        foreach($items_filter['filter_car_modification']['tyres']['specific'] as $key => $value)
        {
            $logic_condition = ( $key === 0 ) ? 'AND' : 'OR';

            if( $array_size === 1 ) {
                $bracket = NULL;
            } elseif( $key === 0 ) {
                $bracket = '(';
            } elseif( $key === $last_key ) {
                $bracket = ')';
            } else {
                $bracket = NULL;
            }

            $input_entity_conditions = array_merge($input_entity_conditions, [
                [
                    'r'  => ['=', $value['r'], '', $logic_condition, $bracket],
                    'w'  => ['=', $value['w'], 'AND'],
                    'h'  => ['=', $value['h'], 'AND']
                ]
            ]);
        }
    }
}

#DDC parameters
$entity_tables_fields = [
    $current_table => [
        'id', 'unique_code', 'date_created', 'brand', 'model_name', 'season', 'r', 'w', 'h', 'load_rate', 'speed', 'extra', 'stock', 'retail', 'promo', 'is_top', 'promotion_id', 'views', 'description', 'video', 'rating_score', 'rating_votes'
    ]
];

$entity_conditions = array_merge($input_entity_conditions, ['id' => ['=', $item_id, 'AND']]);

$entity_orders = ['r' => 'ASC'];

$entity_limits = [1];

$options = ['group' => ['brand', 'model_name']];
#END\DDC parameters

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$tyre['item'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, $entity_limits, $options)[0];

#DDC parameters
$entity_conditions = array_merge([
    'unique_code' => ['<>', 'NULL', ''],
    'brand'       => ['=', $tyre['item']['brand'], 'AND'],
    'model_name'  => ['=', $tyre['item']['model_name'], 'AND']
], $input_entity_conditions);
#END\DDC parameters

$tyre['modifications'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, NULL);

#DDC parameters
$entity_conditions = [
    'unique_code' => ['<>', 'NULL', ''],
    'brand'       => ['=', $tyre['item']['brand'], 'AND'],
    'model_name'  => ['=', $tyre['item']['model_name'], 'AND']
];
#END\DDC parameters

$tyre['modifications'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, NULL);

#DDC parameters description
$entity_tables_fields = [
    'skladkoles_items_descriptions' => [
        'id', 'unique_code', 'type', 'description'
    ]
];

$entity_conditions = [
    'unique_code' => ['=', $tyre['item']['unique_code'], ''],
];

$entity_limits = [1];
#END\DDC parameters description

$tyre['description'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, NULL, $entity_limits)[0];

return $tyre;
?>
