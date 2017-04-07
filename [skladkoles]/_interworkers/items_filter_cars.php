<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $filter_data = $supplied_data;
}

$filter_parameters = [];

if( empty($filter_data['auto-mark']) ||
    empty($filter_data['auto-model']) ||
    empty($filter_data['auto-year']) ||
    empty($filter_data['auto-modification']) ) {
    return FALSE;
} elseif(
    ($filter_data['auto-mark'] = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['auto-mark'])) === FALSE ||
    ($filter_data['auto-model'] = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['auto-model'])) === FALSE ||
    ($filter_data['auto-year'] = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['auto-year'])) === FALSE ||
    ($filter_data['auto-modification'] = $_BOOT->involve_object("InputPurifier")->purge_string($filter_data['auto-modification'])) === FALSE) {
    return FALSE;
}

#DDC car parameters
$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'vehicles' => [
        'vendor', 'car', 'year', 'modification', 'param_pcd', 'param_dia', 'tyres_factory', 'tyres_replace', 'wheels_factory', 'wheels_replace', 'wheels_tuning'
    ]
];

$entity_conditions = [
    'vendor'       => ['=', str_replace('_', ' ', $filter_data['auto-mark']), ''],
    'car'          => ['=', $filter_data['auto-model'], 'AND'],
    'year'         => ['=', $filter_data['auto-year'], 'AND'],
    'modification' => ['=', $filter_data['auto-modification'], 'AND']
];

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$modifications = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell(NULL, $entity_tables_fields, $entity_conditions, NULL, NULL)[0];

if( !$_BOOT->involve_object("DataThralls")->is_filled_array($modifications) ) {
    return FALSE;
}
#END\DDC car parameters

//PCD
$param_pcd = explode('*', $modifications['param_pcd']);
$filter_parameters['rims']['pcd_stud']      = $param_pcd[0];
$filter_parameters['rims']['pcd_dia']       = $param_pcd[1];
$filter_parameters['rims']['pcd_dia_extra'] = $param_pcd[1];

//CH
$filter_parameters['rims']['ch'] = $modifications['param_dia'];

//W x R ET
$wheels_factory = [];
if( !empty($modifications['wheels_factory']) )
{
    $wheels_factory = explode('|', $modifications['wheels_factory']);
    foreach($wheels_factory as $value)
    {
        $wheels_separated = explode(',', $value);
        foreach($wheels_separated as $value_2)
        {
            $params_separated = explode('ET', $value_2);
            $params_separated[0] = explode('x', $params_separated[0]);

            $filter_parameters['rims']['specific'][] = [
                //'et' => trim($params_separated[1]),
                //'w'  => number_format(trim($params_separated[0][0]), 1, '.', ''),
                'r'  => trim($params_separated[0][1])
            ];
        }
    }
}

$wheels_replace = [];
if( !empty($modifications['wheels_replace']) )
{
    $wheels_replace = explode('|', $modifications['wheels_replace']);
    foreach($wheels_replace as $value)
    {
        $wheels_separated = explode(',', $value);
        foreach($wheels_separated as $value_2)
        {
            $params_separated = explode('ET', $value_2);
            $params_separated[0] = explode('x', $params_separated[0]);

            $filter_parameters['rims']['specific'][] = [
                //'et' => trim($params_separated[1]),
                //'w'  => number_format(trim($params_separated[0][0]), 1, '.', ''),
                'r'  => trim($params_separated[0][1])
            ];
        }
    }
}

$tyres_factory = [];
if( !empty($modifications['tyres_factory']) )
{
    $tyres_factory = explode('|', $modifications['tyres_factory']);
    foreach($tyres_factory as $value)
    {
        $tyre_separated = explode('R', $value);
        $tyre_separated[0] = explode('/', $tyre_separated[0]);

        $filter_parameters['tyres']['specific'][] = [
            'r' => trim($tyre_separated[1]),
            'w' => trim($tyre_separated[0][0]),
            'h' => trim($tyre_separated[0][1])
        ];
    }
}

$tyres_replace = [];
if( !empty($modifications['tyres_replace']) )
{
    $tyres_replace = explode('|', $modifications['tyres_replace']);
    foreach($tyres_replace as $value)
    {
        $tyre_separated = explode('R', $value);
        $tyre_separated[0] = explode('/', $tyre_separated[0]);

        $filter_parameters['tyres']['specific'][] = [
            'r' => trim($tyre_separated[1]),
            'w' => trim($tyre_separated[0][0]),
            'h' => trim($tyre_separated[0][1])
        ];
    }
}

if( !empty($filter_parameters['rims']['specific']) ) {
    $filter_parameters['rims']['specific'] = array_map(
        function($item) { return ( is_array($item) ) ? array_unique($item) : $item; },
        $filter_parameters['rims']['specific']
    );
}

if( !empty($filter_parameters['tyres']['specific']) ) {
    $filter_parameters['tyres']['specific'] = array_map(
        function($item) { return ( is_array($item) ) ? array_unique($item) : $item; },
        $filter_parameters['tyres']['specific']
    );
}

$car_bar = [
    'vendor'         => $modifications['vendor'],
    'car'            => $modifications['car'],
    'year'           => $modifications['year'],
    'modification'   => $modifications['modification'],
    'pcd'            => $modifications['param_pcd'],
    'dia'            => $modifications['param_dia'],
    'wheels_factory' => $wheels_factory,
    'wheels_replace' => $wheels_replace,
    'tyres_factory'  => $tyres_factory,
    'tyres_replace'  => $tyres_replace
];

if( empty($filter_parameters['rims']['specific']) ) {
    $filter_parameters['rims']['pcd_stud'] = 'none';
}

if( empty($filter_parameters['tyres']['specific']) ) {
    $filter_parameters['tyres']['specific'][0] = [
        'r' => 'none',
        'w' => 'none',
        'h' => 'none'
    ];
}

return [$filter_parameters, $car_bar];
?>