<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

const KEY_REPLACE   = ['search' => '-', 'replace' => '_'];
const VALUE_REPLACE = ['search' => ' ', 'replace' => '_'];

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
                        $nested_query_array[] =
                            str_replace(KEY_REPLACE['search'], KEY_REPLACE['replace'], $nested_key) .
                            "-" .
                            str_replace(VALUE_REPLACE['search'], VALUE_REPLACE['replace'], $nested_value)
                        ;
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
