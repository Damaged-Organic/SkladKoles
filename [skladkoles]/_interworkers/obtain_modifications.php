<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $type = $supplied_data;
}

switch($type)
{
    case 'rims':
        # ----------------------------------------------------------------------------------------------------
        # BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                               |
        # ----------------------------------------------------------------------------------------------------

        // $entity_tables_fields = [
        //     $C_S::DB_PREFIX_alpha.'items_rims' => [
        //         'brand', 'pcd_stud', 'pcd_dia', 'w', 'r'
        //     ]
        // ];
        //
        // $group_by = ['group' => ['brand', 'pcd_stud', 'pcd_dia', 'w', 'r']];
        //
        // $table_type = 'rims';

        $table_name = $C_S::DB_PREFIX_alpha.'items_rims';
        $table_type = 'rims';

        $query_string = "
            SELECT
            (
                SELECT group_concat(DISTINCT LOWER(brand) ORDER BY brand) FROM {$table_name}
            ) as brand,
            (
                SELECT group_concat(DISTINCT pcd_stud ORDER BY pcd_stud) FROM {$table_name}
            ) as pcd_stud,
            (
                SELECT group_concat(DISTINCT pcd_dia ORDER BY pcd_dia) FROM {$table_name}
            ) as pcd_dia,
            (
                SELECT group_concat(DISTINCT w ORDER BY w) FROM {$table_name}
            ) as w,
            (
                SELECT group_concat(DISTINCT r ORDER BY r) FROM {$table_name}
            ) as r
        ";
    break;

    case 'exclusive_rims':
        # ----------------------------------------------------------------------------------------------------
        # BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                               |
        # ----------------------------------------------------------------------------------------------------

        // $entity_tables_fields = [
        //     $C_S::DB_PREFIX_alpha.'items_rims_exclusive' => [
        //         'brand', 'pcd_stud', 'pcd_dia', 'w', 'r'
        //     ]
        // ];
        //
        // $group_by = ['group' => ['brand', 'pcd_stud', 'pcd_dia', 'w', 'r']];
        //
        // $table_type = 'exclusive_rims';

        $table_name = $C_S::DB_PREFIX_alpha.'items_rims_exclusive';
        $table_type = 'exclusive_rims';

        $query_string = "
            SELECT
            (
                SELECT group_concat(DISTINCT LOWER(brand) ORDER BY brand) FROM {$table_name}
            ) as brand,
            (
                SELECT group_concat(DISTINCT pcd_stud ORDER BY pcd_stud) FROM {$table_name}
            ) as pcd_stud,
            (
                SELECT group_concat(DISTINCT pcd_dia ORDER BY pcd_dia) FROM {$table_name}
            ) as pcd_dia,
            (
                SELECT group_concat(DISTINCT w ORDER BY w) FROM {$table_name}
            ) as w,
            (
                SELECT group_concat(DISTINCT r ORDER BY r) FROM {$table_name}
            ) as r
        ";
    break;

    case 'tyres':
        # ----------------------------------------------------------------------------------------------------
        # BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                               |
        # ----------------------------------------------------------------------------------------------------

        // $entity_tables_fields = [
        //     $C_S::DB_PREFIX_alpha.'items_tyres' => [
        //         'brand', 'r', 'w', 'h'
        //     ]
        // ];
        //
        // $group_by = ['group' => ['brand', 'r', 'w', 'h']];
        //
        // $table_type = 'tyres';

        $table_name = $C_S::DB_PREFIX_alpha.'items_tyres';
        $table_type = 'tyres';

        $query_string = "
            SELECT
            (
                SELECT group_concat(DISTINCT LOWER(brand) ORDER BY brand) FROM {$table_name}
            ) as brand,
            (
                SELECT group_concat(DISTINCT r ORDER BY r) FROM {$table_name}
            ) as r,
            (
                SELECT group_concat(DISTINCT w ORDER BY w) FROM {$table_name}
            ) as w,
            (
                SELECT group_concat(DISTINCT h ORDER BY h) FROM {$table_name}
            ) as h
        ";
    break;

    case 'exclusive_tyres':
        # ----------------------------------------------------------------------------------------------------
        # BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                               |
        # ----------------------------------------------------------------------------------------------------

        // $entity_tables_fields = [
        //     $C_S::DB_PREFIX_alpha.'items_tyres_exclusive' => [
        //         'brand', 'r', 'w', 'h'
        //     ]
        // ];
        //
        // $group_by = ['group' => ['brand', 'r', 'w', 'h']];
        //
        // $table_type = 'exclusive_tyres';

        $table_name = $C_S::DB_PREFIX_alpha.'items_tyres_exclusive';
        $table_type = 'exclusive_tyres';

        $query_string = "
            SELECT
            (
                SELECT group_concat(DISTINCT LOWER(brand) ORDER BY brand) FROM {$table_name}
            ) as brand,
            (
                SELECT group_concat(DISTINCT r ORDER BY r) FROM {$table_name}
            ) as r,
            (
                SELECT group_concat(DISTINCT w ORDER BY w) FROM {$table_name}
            ) as w,
            (
                SELECT group_concat(DISTINCT h ORDER BY h) FROM {$table_name}
            ) as h
        ";
    break;

    default:
        return FALSE;
    break;
}

# ----------------------------------------------------------------------------------------------------
# BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                               |
# ----------------------------------------------------------------------------------------------------

// if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
//     throw new procException("Table(s) does not exists");
// }
//
// $modifications = $_BOOT->involve_object(
//     "DB_CellConstructor",
//     [$_BOOT->involve_object("DB_Handler")]
// )->dynamic_data_cell(NULL, $entity_tables_fields, NULL, NULL, NULL, $group_by);
//
// $transform_modifications = function($input_array) use($table_type)
// {
//     if( empty($input_array) )
//         return NULL;
//
//     $output_array = [];
//
//     $input_array_keys = array_keys($input_array[0]);
//
//     foreach($input_array as $value) {
//         foreach($input_array_keys as $key) {
//             $output_array[$key][] = mb_strtolower($value[$key], 'UTF-8');
//         }
//     }
//
//     $output_array = array_map(
//         function($item) {
//             sort($item, SORT_NATURAL);
//             return array_filter(array_unique($item));
//         },
//         $output_array
//     );
//
//     return [$table_type => $output_array];
// };
//
// return $transform_modifications($modifications);

# ----------------------------------------------------------------------------------------------------
# FIXED: BOTTLENECK - PRODUCES HEAVY CPU LOAD                                                        |
# ----------------------------------------------------------------------------------------------------

$statement = $_BOOT->involve_object("DB_Handler")->data_object->prepare($query_string);
$statement->execute();
$statement_out = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

$statement->closeCursor();

foreach($statement_out as $group => $values) {
    $modifications[$table_type][$group] = explode(',', $values);
}

return $modifications;
?>
