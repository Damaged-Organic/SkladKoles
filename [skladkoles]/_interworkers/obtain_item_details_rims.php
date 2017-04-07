<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($item_id, $items_filter) = $supplied_data;
}

if( $_AREA->{$C_E::_ARGUMENTS}[0] === 'rims' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_rims';
} elseif( $_AREA->{$C_E::_ARGUMENTS}[0] === 'exclusive_rims' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_rims_exclusive';
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

if( !empty($items_filter['filter_car_modification']['rims']) )
{
    if( !empty($items_filter['filter_car_modification']['rims']['pcd_stud']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['pcd_stud' => ['=', $items_filter['filter_car_modification']['rims']['pcd_stud'], 'AND']]);
    }

    if( !empty($items_filter['filter_car_modification']['rims']['pcd_dia']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, [
            [
                'pcd_dia'       => ['=', $items_filter['filter_car_modification']['rims']['pcd_dia'], ''],
                'pcd_dia_extra' => ['=', $items_filter['filter_car_modification']['rims']['pcd_dia_extra'], 'OR']
            ]
        ]);
    }

    /*if( !empty($items_filter['filter_car_modification']['rims']['ch']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['ch' => ['=', $items_filter['filter_car_modification']['rims']['ch'], 'AND']]);
    }*/

    if( !empty($items_filter['filter_car_modification']['rims']['specific']) )
    {
        end($items_filter['filter_car_modification']['rims']['specific']);
        $last_key = key($items_filter['filter_car_modification']['rims']['specific']);

        $array_size = count($items_filter['filter_car_modification']['rims']['specific']);

        foreach($items_filter['filter_car_modification']['rims']['specific'] as $key => $value)
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
                    //'et' => ['=', $value['et'], '', $logic_condition, $bracket],
                    //'w'  => ['=', $value['w'], 'AND'],
                    'r'  => ['=', $value['r'], '', $logic_condition, $bracket]
                ]
            ]);
        }
    }
}

#DDC parameters
$entity_tables_fields = [
    $current_table => [
        'id', 'unique_code', 'date_created', 'brand', 'model_name', 'code', 'paint', 'pcd_stud', 'pcd_dia', 'pcd_dia_extra', 'w', 'r', 'et', 'ch', 'rim_type', 'stock', 'retail', 'promo', 'is_top', 'promotion_id', 'views', 'description', 'video', 'rating_score', 'rating_votes'
    ]
];

$entity_conditions = array_merge($input_entity_conditions, ['id' => ['=', $item_id, 'AND']]);

$entity_orders = ['r' => 'ASC'];

$entity_limits = [1];

$options = ['group' => ['brand', 'model_name', 'code', 'paint']];
#END\DDC parameters

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$rim['item'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, $entity_limits, $options)[0];

#DDC parameters
if( $rim['item']['model_name'] == NULL )
{
    $entity_conditions = array_merge([
        'unique_code' => ['<>', 'NULL', ''],
        'brand'       => ['=', $rim['item']['brand'], 'AND'],
        'code'        => ['=', $rim['item']['code'], 'AND'],
        'paint'       => ['=', $rim['item']['paint'], 'AND'],
    ], $input_entity_conditions);
} elseif( $rim['item']['code'] == NULL ) {
    $entity_conditions = array_merge([
        'unique_code' => ['<>', 'NULL', ''],
        'brand'       => ['=', $rim['item']['brand'], 'AND'],
        'model_name'  => ['=', $rim['item']['model_name'], 'AND'],
        'paint'       => ['=', $rim['item']['paint'], 'AND'],
    ], $input_entity_conditions);
} else {
    $entity_conditions = array_merge([
        'unique_code' => ['<>', 'NULL', ''],
        'brand'       => ['=', $rim['item']['brand'], 'AND'],
        'model_name'  => ['=', $rim['item']['model_name'], 'AND'],
        'code'        => ['=', $rim['item']['code'], 'AND'],
        'paint'       => ['=', $rim['item']['paint'], 'AND']
    ], $input_entity_conditions);
}
#END\DDC parameters

$rim['modifications'] = $_BOOT->involve_object(
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
    'unique_code' => ['=', $rim['item']['unique_code'], ''],
];

$entity_limits = [1];
#END\DDC parameters description

$rim['description'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, NULL, $entity_limits)[0];

return $rim;
?>
