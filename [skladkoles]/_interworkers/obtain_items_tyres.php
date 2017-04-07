<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

#Sorting parameters
$input_entity_orders = [];

if( !empty($supplied_data['sort']) )
{
    switch($supplied_data['sort'])
    {
        case 'price_asc':
            $input_entity_orders = ['sort_price' => 'ASC'];
        break;

        case 'price_desc':
            $input_entity_orders = ['sort_price' => 'DESC'];
        break;

        case 'alphabet':
            $input_entity_orders = ['brand' => 'ASC', 'model_name' => 'ASC'];
        break;

        case 'newest':
            $input_entity_orders = ['date_created' => 'DESC'];
        break;

        case 'most_popular':
            $input_entity_orders = ['views' => 'DESC'];
        break;

        case 'most_rated':
            $input_entity_orders = ['rating_score' => 'DESC'];
        break;
    }
}
#END\Sorting parameters

#Filtering parameters
$input_entity_conditions = [];

if( !empty($supplied_data['filter_common']) )
{
    if( !empty($supplied_data['filter_common']['available']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['stock' => ['>', '0', 'AND']]);
    }

    if( !empty($supplied_data['filter_common']['promotion']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, [
            [
                'promotion_id' => ['<>', 'NULL', ''],
                'promo'        => ['<>', 'NULL', 'OR'],
            ]
        ]);
    }

    if( !empty($supplied_data['filter_common']['top']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['is_top' => ['=', 'Y', 'AND']]);
    }

    if( !empty($supplied_data['filter_common']['price']) )
    {
        $input_entity_conditions = array_merge($input_entity_conditions, [
            [
                'retail' => ['BETWEEN', [$supplied_data['filter_common']['price']['min'], $supplied_data['filter_common']['price']['max']], ''],
                'promo'  => ['BETWEEN', [$supplied_data['filter_common']['price']['min'], $supplied_data['filter_common']['price']['max']], 'OR']
            ]
        ]);
    }

    if( !empty($supplied_data['filter_common']['brand']) ) {
        $supplied_data['filter_common']['brand'] = urldecode($supplied_data['filter_common']['brand']);
        $input_entity_conditions = array_merge($input_entity_conditions, ['brand' => ['=', $supplied_data['filter_common']['brand'], 'AND']]);
    }
}

if( !empty($supplied_data['filter_modification']) )
{
    if( !empty($supplied_data['filter_modification']['tyres']['r']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['r' => ['=', $supplied_data['filter_modification']['tyres']['r'], 'AND']]);
    }

    if( !empty($supplied_data['filter_modification']['tyres']['w']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['w' => ['=', $supplied_data['filter_modification']['tyres']['w'], 'AND']]);
    }

    if( !empty($supplied_data['filter_modification']['tyres']['h']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['h' => ['=', $supplied_data['filter_modification']['tyres']['h'], 'AND']]);
    }

    if( !empty($supplied_data['filter_modification']['tyres']['load_rate']) ) {
        $input_entity_conditions = array_merge($input_entity_conditions, ['load_rate' => ['=', $supplied_data['filter_modification']['tyres']['load_rate'], 'AND']]);
    }
}

if( !empty($supplied_data['filter_car_modification']['tyres']) )
{
    if( !empty($supplied_data['filter_car_modification']['tyres']['specific']) )
    {
        end($supplied_data['filter_car_modification']['tyres']['specific']);
        $last_key = key($supplied_data['filter_car_modification']['tyres']['specific']);

        $array_size = count($supplied_data['filter_car_modification']['tyres']['specific']);

        foreach($supplied_data['filter_car_modification']['tyres']['specific'] as $key => $value)
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

if( isset($supplied_data['season']) && ($supplied_data['season'] !== NULL) ) {
    $input_entity_conditions = array_merge($input_entity_conditions, ['season' => ['=', $supplied_data['season'], 'AND']]);
}
#END\Filtering parameters

$db_handler = $_BOOT->involve_object('DB_Handler');

#DDC parameters
if( $_AREA->{$C_E::_ARGUMENTS}[0] === 'tyres' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_tyres';
} elseif( $_AREA->{$C_E::_ARGUMENTS}[0] === 'exclusive_tyres' ) {
    $current_table = $C_S::DB_PREFIX_alpha.'items_tyres_exclusive';
}

$entity_tables_fields = [
    $current_table => [
        'id', 'unique_code', 'date_created', 'brand', 'model_name', 'season', 'r', 'w', 'h', 'load_rate', 'speed', 'extra', 'stock', 'retail', 'promo', 'is_top', 'promotion_id', 'views', 'rating_score', 'location', ["IF({$current_table}.promo <> 'NULL', {$current_table}.promo, {$current_table}.retail) AS sort_price"]
    ]
];

if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    $entity_conditions = array_merge(['unique_code' => ['<>', 'NULL', '']], $input_entity_conditions);
} else {
    $entity_conditions = array_merge(
        [
            'unique_code' => ['<>', 'NULL', ''],
            'brand'       => ['<>', 'NULL', 'AND'],
            'model_name'  => ['<>', 'NULL', 'AND']
        ],
        $input_entity_conditions
    );
}

$entity_orders = ( !empty($input_entity_orders) ) ? $input_entity_orders : ['date_created' => 'DESC'];

$entity_limits = ( !empty($supplied_data['pagination']) ) ? [$supplied_data['pagination'][0], $supplied_data['pagination'][1]] : [];

$options = ['group' => ['brand', 'model_name', 'season']];

// If sorting is default, items with a given location should occur first
if( empty($input_entity_orders) ) {
    $options = array_merge($options, [
        'order' => ['location' => ['kiev_Склад Колес', 'kiev_Склад Колёс', 'lv_Склад Колес', 'lv_Склад Колёс']]
    ]);
}
#END\DDC parameters

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$items_tyres['items'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, $entity_limits, $options);

$items_tyres['modifications'] = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, $entity_orders, NULL);

return ( $_BOOT->involve_object("DataThralls")->is_filled_array($items_tyres) ) ? $items_tyres : [];
?>
