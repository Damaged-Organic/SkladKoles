<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

const KEY_REPLACE   = ['search' => '_', 'replace' => '-'];
const VALUE_REPLACE = ['search' => '_', 'replace' => ' '];

function format($parameters)
{
    $formatted_parameters = [];

    array_walk($parameters, function($value, $key) use(&$formatted_parameters) {
        if( !in_array($key, ['auto_mark', 'auto_model', 'auto_year', 'auto_modification'], TRUE) )
            return;

        $formatted_key = str_replace(
            KEY_REPLACE['search'], KEY_REPLACE['replace'], $key
        );
        $formatted_value = str_replace(
            VALUE_REPLACE['search'], VALUE_REPLACE['replace'], $value
        );

        $formatted_parameters[$formatted_key] = $formatted_value;
    });

    return $formatted_parameters;
}

return function($parameters) {
    return format($parameters);
}
?>
