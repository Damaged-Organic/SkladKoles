<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

function build($filter_parameters) {
    $query_array = [];

    array_walk($filter_parameters, function($value, $key) use(&$query_array)
    {
        if( $value )
        {
            if( is_array($value) ) {
                $nested_query_array = [];

                array_walk($value, function($nested_value, $nested_key) use(&$nested_query_array) {
                    if( $nested_value )
                        $nested_query_array[] = $nested_key . "-" . $nested_value;
                });

                $nested_query_string = implode(';', $nested_query_array);

                if( $nested_query_string )
                    $query_array[] = $nested_query_string;
            } else {
                $query_array[] = $key . "-" . $value;
            }
        }
    });

    $query_string = implode(';', $query_array);

    return ( $query_string ) ? "/" . $query_string : NULL;
}

return function($filter_parameters) {
    return build($filter_parameters);
};
?>
