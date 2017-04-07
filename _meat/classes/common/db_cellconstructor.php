<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException, PDOException;

use _meat\classes\common\DB_Handler,
    PDO;

class DB_CellConstructor
{
    private $db_handler = NULL;

    function __construct(DB_Handler $DBH)
    {
        $this->db_handler = $DBH;
    }

    # ----------------------------------------------------------------------------------------------------
    # |1.0| - Static Data Cell - SDC                                                                     |
    # ----------------------------------------------------------------------------------------------------

    # |1.1|
    public function static_data_cell($table_name, $directory = NULL, $languages = NULL)
    {
        if( !$this->db_handler->validate_tables($table_name) ) {
            throw new coreException("Table '{$table_name}' does not exists");
        }

        $isset_directory  = !empty($directory);
        $isset_languages  = !empty($languages);

        $query_string  = NULL;
        $execute_array = array();

        $query_string = "SELECT * FROM {$table_name}";

        #::WHERE
        if( $isset_directory || $isset_languages ) {
            $query_string .= " WHERE";
        }

        if( $isset_directory ) {
            $query_string   .= " directory = ?";
            $execute_array[] = $directory;
        }

        #::AND
        if( $isset_directory && $isset_languages ) {
            $query_string .= " AND";
        }

        if( $isset_languages ) {
            if( !is_array($languages) ) $languages = (array)$languages;
            $query_string  .= " language IN (".str_repeat('?,', (count($languages)-1)) . "?)";
            $execute_array  = array_merge($execute_array, $languages);
        }

        $statement_out = array();

        try {
            $statement = $this->db_handler->data_object->prepare($query_string);
            $statement->execute($execute_array);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $statement->closeCursor();
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return (!empty($statement_out)) ? $statement_out : FALSE;
    }

    # ----------------------------------------------------------------------------------------------------
    # |2.0| - Menu Data Cell - MDC                                                                       |
    # ----------------------------------------------------------------------------------------------------

    # |2.1|
    public function menu_data_cell($tables, $languages, $initial_depth = NULL, $level = NULL)
    {
        $entity_table   = $tables[0];
        $content_table = $tables[1];

        if( !is_array($languages) ) $languages = (array)$languages;

        $language_string = NULL;
        $execute_array   = array();

        $language_string = str_repeat('?,', (count($languages)-1)) . "?";

        $query_string = "SELECT * FROM {$entity_table} AS t LEFT JOIN {$content_table} AS tl ON t.id = tl.parent_id WHERE tl.language IN ({$language_string})";

        $execute_array += $languages;

        if( $initial_depth !== NULL ) {
            $query_string .= " AND t.depth <= ?";
            $execute_array[] = $initial_depth;
        }

        if( $level !== NULL ) {
            $query_string .= " AND t.level = ?";
            $execute_array[] = $level;
        }

        $statement_out = array();

        try {
            $statement = $this->db_handler->data_object->prepare($query_string);
            $statement->execute($execute_array);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return (!empty($statement_out)) ? $statement_out : FALSE;
    }

    # ----------------------------------------------------------------------------------------------------
    # |3.0| - Dynamic Data Cell - DDC                                                                    |
    # ----------------------------------------------------------------------------------------------------

    # |3.1|
    public function dynamic_data_cell($languages, $tables_fields, $conditions, $orders, $limit, $option = NULL)
    {
        $entity_table  = key($tables_fields);
        $linked_tables = array_keys(array_slice($tables_fields, 1));

        $isset_languages  = !empty($languages);
        $isset_conditions = !empty($conditions);
        $isset_orders	  = !empty($orders);
        $isset_limit	  = !empty($limit);

        foreach($tables_fields as $key => $value)
        {
            foreach($value as $field)
            {
                if( !is_array($field) ) {
                    $fields_set[$field] = array('table' => $key);
                } else {
                    $fields_set[$field[0]] = array('table' => $key);
                }
            }
        }

        $query_string = NULL;

        $prefixed_fields = array();

        foreach($fields_set as $field_name => $properties)
        {
            if( strpos($field_name,'IF(') === FALSE ) {
                $prefixed_fields[] = "{$properties['table']}.{$field_name}";
            } else {
                $prefixed_fields[] = $field_name;
            }
        }

        /*echo "<pre>";
        var_dump($prefixed_fields);
        echo "</pre>";*/

        if( isset($option['count']) ) {
            $selection = "COUNT(id) ";
        } else {
            $selection = implode(',', $prefixed_fields);
        }

        if( !empty($option['sql_calc_found_rows']) ) {
            $sql_calc_found_rows = "SQL_CALC_FOUND_ROWS";
        } else {
            $sql_calc_found_rows = NULL;
        }

        #::SELECT
        $query_string = "SELECT {$sql_calc_found_rows} {$selection} FROM {$entity_table} ";

        #::JOIN
        foreach($linked_tables as $linked_table_name) {
            $query_string .= "LEFT JOIN {$linked_table_name} ON {$entity_table}.id = {$linked_table_name}.parent_id ";
        }

        #::WHERE
        if( $isset_languages || $isset_conditions )
        {
            $query_string .= "WHERE ";
        }

        $execute_array = array();

        #::LANGUAGES
        if( $isset_languages )
        {
            $language_array = array();

            foreach( $languages as $content_table => $language_fields )
            {
                if( array_search($content_table, $linked_tables) === FALSE ) {
                    throw new coreException("Language table '{$content_table}' is not set");
                } else {
                    $language_array[] = "{$content_table}.language IN (" . str_repeat('?,', (count($language_fields)-1)) . "?) ";
                    foreach($language_fields as $language_fields_name) {
                        $execute_array[] = $language_fields_name;
                    }
                }
            }
            $query_string .= implode(' OR ', $language_array);
        }

        #::CONDITIONS
        if( $isset_conditions )
        {
            $conditions_array = array();

            $conditions[key($conditions)][2] .= ' (';

            foreach( $conditions as $condition_field => $condition_properties )
            {
                if( (bool)count(array_filter(array_keys($condition_properties), 'is_string')) )
                {
                    $conditions_joined_array = array();

                    if( !empty($condition_properties[key($condition_properties)][4]) )
                    {
                        if( $condition_properties[key($condition_properties)][4] === '(' ) {
                            $start_brackets = '(';
                            $close_brackets = NULL;
                        } else {
                            $start_brackets = NULL;
                            $close_brackets = ')';
                        }
                    } else {
                        $start_brackets = NULL;
                        $close_brackets = NULL;
                    }

                    if( !empty($condition_properties[key($condition_properties)][3]) ) {
                        $logic_operator = $condition_properties[key($condition_properties)][3];
                    } else {
                        $logic_operator = 'AND';
                    }

                    foreach($condition_properties as $condition_joined_field => $joined_condition_properties)
                    {
                        if( array_search($condition_joined_field, array_keys($fields_set)) === FALSE ) {
                            throw new coreException("Condition field '{$condition_joined_field}' is not set");
                        } else {
                            switch($joined_condition_properties[0])
                            {
                                case 'BETWEEN':
                                    $conditions_joined_array[] = "{$joined_condition_properties[2]} ({$fields_set[$condition_joined_field]['table']}.{$condition_joined_field} {$joined_condition_properties[0]} ? AND ?)";
                                    $execute_array[]    = $joined_condition_properties[1][0];
                                    $execute_array[]    = $joined_condition_properties[1][1];
                                break;

                                default:
                                    $conditions_joined_array[] = "{$joined_condition_properties[2]} {$fields_set[$condition_joined_field]['table']}.{$condition_joined_field} {$joined_condition_properties[0]} ?";
                                    $execute_array[]    = $joined_condition_properties[1];
                                break;
                            }
                        }
                    }

                    $conditions_array[] = " {$logic_operator} {$start_brackets} (" . implode(' ', $conditions_joined_array) . ") {$close_brackets}";
                } else {
                    if( array_search($condition_field, array_keys($fields_set)) === FALSE ) {
                        throw new coreException("Condition field '{$condition_field}' is not set");
                    } else {
                        switch($condition_properties[0])
                        {
                            case 'BETWEEN':
                                $conditions_array[] = "{$condition_properties[2]} ({$fields_set[$condition_field]['table']}.{$condition_field} {$condition_properties[0]} ? AND ?)";
                                $execute_array[]    = $condition_properties[1][0];
                                $execute_array[]    = $condition_properties[1][1];
                            break;

                            default:
                                $conditions_array[] = "{$condition_properties[2]} {$fields_set[$condition_field]['table']}.{$condition_field} {$condition_properties[0]} ?";
                                $execute_array[]    = $condition_properties[1];
                            break;
                        }
                    }
                }
            }
            $query_string .= implode(' ', $conditions_array) . ")";
        }

        #GROUP BY
        if( !empty($option['group']) ) {
            $query_string .= " GROUP BY " . implode(",", array_map(
                function($field) use($entity_table) { return "{$entity_table}.{$field}"; },
                $option['group']
            ));
        }

        #ORDER
        if( $isset_orders )
        {
            $orders_array = array();

            foreach( $orders as $order_field => $order_property )
            {
                if( array_search($order_field, array_keys($fields_set)) === FALSE && $order_field !== 'sort_price' ) {
                    throw new coreException("Order field '{$order_field}' is not set");
                } else {
                    if( $order_field !== 'sort_price' ) {
                        $orders_array[] = "{$fields_set[$order_field]['table']}.{$order_field} {$order_property}";
                    } else {
                        $orders_array[] = "{$order_field} {$order_property}";
                    }
                }
            }

            // If sorting is default, items with a given location should occur first
            if( !empty($option['order']) ) {
                $option_order_field = key($option['order']);
                $query_string .= " ORDER BY FIELD(" . $option_order_field . ", '" . implode("','", $option['order'][$option_order_field]) . "') DESC, " . implode(',', $orders_array);
            } else {
                $query_string .= " ORDER BY " . implode(',', $orders_array) . ", id DESC";
            }
        }

        #LIMIT
        if( $isset_limit )
        {
            foreach( $limit as $value ) {
                $execute_array[] = (int)$value;
            }

            $query_string .= " LIMIT " . str_repeat('?,', (count($limit) - 1)) . "?";
        }

        $statement_out = array();

        try {
            $statement = $this->db_handler->data_object->prepare($query_string);
            $statement->execute($execute_array);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return (!empty($statement_out)) ? $statement_out : FALSE;
    }

    # ----------------------------------------------------------------------------------------------------
    # |4.0| - Simple Data Cell - SimpleDC                                                                |
    # ----------------------------------------------------------------------------------------------------

    # |4.1|
    public function directory_title_data_cell($tables, $language, $directory)
    {
        if( !$this->db_handler->validate_tables($tables) ) {
            throw new coreException("Table(s) does not exists");
        }

        $entity_table  = $tables[0];
        $content_table = $tables[1];

        $statement_out = array();

        try {
            $statement = $this->db_handler->data_object->prepare(
                "SELECT content_table.title FROM {$entity_table} AS entity_table LEFT JOIN {$content_table} AS content_table
				 ON entity_table.id = content_table.parent_id
			     WHERE entity_table.directory = :directory AND content_table.language = :language"
            );
            $statement->execute(array(':directory' => $directory, ':language' => $language));
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( $statement_out ) ? $statement_out[0]['title'] : FALSE;
    }
}
?>
