<?php
# DEAD
// if( !function_exists('is_access_direct') ||
//     is_access_direct() )
//     exit(NO_DIRECT_ACCESS);
//
// define('PARAMETER_DELIMITER', ';');
// define('NESTED_DELIMITER', ':');
//
// function parseNestedParameter($value)
// {
//     $parameters = array_filter(explode(PARAMETER_DELIMITER, $value));
//
//     foreach($parameters as $parameter)
//     {
//         $parameter = explode(NESTED_DELIMITER, $parameter);
//
//         $nested_parameters[$parameter[0]] = $parameter[1];
//     }
//
//     return $nested_parameters;
// }
//
// function queryParametersParser($query, $type)
// {
//     $filter_parameters = [];
//
//     foreach($query as $parameter_name => $parameter_value)
//     {
//         $filter_parameters[$parameter_name] = ( $parameter_name === $type )
//             ? parseNestedParameter($parameter_value)
//             : $parameter_value
//         ;
//     }
//
//     return $filter_parameters;
// };
//
// return function($query, $type) {
//     return queryParametersParser($query, $type);
// }
?>
