<?php
namespace _meat\classes\specific;

use coreException, procException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler;

use PDO;

class Cart
{
    private $db_handler = NULL;

    function __construct(DB_Handler $db_handler)
    {
        $this->db_handler = $db_handler;
    }

    private function assign_table($item_type)
    {
        $table = NULL;

        switch($item_type)
        {
            case 'rims':
                $table = Setup::DB_PREFIX_alpha."items_rims";
            break;

            case 'exclusive_rims':
                $table = Setup::DB_PREFIX_alpha."items_rims_exclusive";
            break;

            case 'tyres':
                $table = Setup::DB_PREFIX_alpha."items_tyres";
            break;

            case 'exclusive_tyres':
                $table = Setup::DB_PREFIX_alpha."items_tyres_exclusive";
            break;

            case 'spares':
                $table = Setup::DB_PREFIX_alpha."items_spares";
            break;

            default:
                throw new coreException("Unknown item type");
            break;
        }

        return $table;
    }

    public function get_quantity_if_item_exists($item_type, $unique_code)
    {
        return ( !empty($_SESSION['user_cart'][$item_type][$unique_code]) ) ? $_SESSION['user_cart'][$item_type][$unique_code] : FALSE;
    }

    public function add_item($item_type, $unique_code)
    {
        $current_table = $this->assign_table($item_type);

        if( !$this->db_handler->is_value_exists($current_table, 'id', $unique_code) ) {
            return FALSE;
        }

        if( $this->get_quantity_if_item_exists($item_type, $unique_code) ) {
            //check if stock is fine
            ++$_SESSION['user_cart'][$item_type][$unique_code];
        } else {
            switch($item_type)
            {
                case 'rims':
                case 'exclusive_rims':
                case 'tyres':
                case 'exclusive_tyres':
                    $_SESSION['user_cart'][$item_type][$unique_code] = 4;
                break;

                case 'spares':
                    $_SESSION['user_cart'][$item_type][$unique_code] = 1;
                break;
            }
        }

        return TRUE;
    }

    public function decrease_item($item_type, $unique_code)
    {
        $current_table = $this->assign_table($item_type);

        if( !$this->db_handler->is_value_exists($current_table, 'id', $unique_code) ) {
            return FALSE;
        }

        if( ($quantity = $this->get_quantity_if_item_exists($item_type, $unique_code)) !== FALSE ) {
            if( $quantity > 1 ) {
                --$_SESSION['user_cart'][$item_type][$unique_code];
            } else {
                //$this->delete_item($item_type, $unique_code);
                return FALSE;
            }
        } else {
            return FALSE;
        }

        return TRUE;
    }

    public function delete_item($item_type, $unique_code)
    {
        if( $this->get_quantity_if_item_exists($item_type, $unique_code) ) {
            unset($_SESSION['user_cart'][$item_type][$unique_code]);
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function obtain_cart_items_data()
    {
        if( empty($_SESSION['user_cart']) ) {
            return FALSE;
        }

        $statement_parameters = [
            'query_string'  => [],
            'execute_array' => []
        ];

        $rims_table       = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table      = Setup::DB_PREFIX_alpha.'items_tyres';
        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';
        $spares_table     = Setup::DB_PREFIX_alpha.'items_spares';

        foreach($_SESSION['user_cart'] as $type => $value) {
            $unique_codes[$type] = array_keys($value);
        }

        if( !empty($unique_codes['rims']) ) {
            $statement_parameters['query_string']['rims'] = "
                (SELECT 'rims' AS type,
                        NULL AS item_type,
                        {$rims_table}.id,
                        {$rims_table}.unique_code,
                        NULL AS item_specs,
                        NULL AS size,
                        {$rims_table}.brand,
                        {$rims_table}.model_name,
                        {$rims_table}.code,
                        {$rims_table}.paint,
                        {$rims_table}.w,
                        {$rims_table}.r,
                        {$rims_table}.pcd_stud,
                        {$rims_table}.pcd_dia,
                        {$rims_table}.et,
                        {$rims_table}.ch,
                        NULL AS h,
                        NULL AS load_rate,
                        {$rims_table}.stock,
                        {$rims_table}.retail,
                        {$rims_table}.promo,
                        {$rims_table}.promotion_id
                 FROM {$rims_table}
                 WHERE {$rims_table}.id IN (" . str_repeat('?,', (count($unique_codes['rims']) - 1)) . "?) )";
            $statement_parameters['execute_array'] = array_merge($statement_parameters['execute_array'], $unique_codes['rims']);
        }

        if( !empty($unique_codes['exclusive_rims']) ) {
            $statement_parameters['query_string']['exclusive_rims'] = "
                (SELECT 'exclusive_rims' AS type,
                        NULL AS item_type,
                        {$rims_exclusive_table}.id,
                        {$rims_exclusive_table}.unique_code,
                        NULL AS item_specs,
                        NULL AS size,
                        {$rims_exclusive_table}.brand,
                        {$rims_exclusive_table}.model_name,
                        {$rims_exclusive_table}.code,
                        {$rims_exclusive_table}.paint,
                        {$rims_exclusive_table}.w,
                        {$rims_exclusive_table}.r,
                        {$rims_exclusive_table}.pcd_stud,
                        {$rims_exclusive_table}.pcd_dia,
                        {$rims_exclusive_table}.et,
                        {$rims_exclusive_table}.ch,
                        NULL AS h,
                        NULL AS load_rate,
                        {$rims_exclusive_table}.stock,
                        {$rims_exclusive_table}.retail,
                        {$rims_exclusive_table}.promo,
                        {$rims_exclusive_table}.promotion_id
                 FROM {$rims_exclusive_table}
                 WHERE {$rims_exclusive_table}.id IN (" . str_repeat('?,', (count($unique_codes['exclusive_rims']) - 1)) . "?) )";
            $statement_parameters['execute_array'] = array_merge($statement_parameters['execute_array'], $unique_codes['exclusive_rims']);
        }

        if( !empty($unique_codes['tyres']) ) {
            $statement_parameters['query_string']['tyres'] = "
                (SELECT 'tyres' AS type,
                        NULL AS item_type,
                        {$tyres_table}.id,
                        {$tyres_table}.unique_code,
                        NULL AS item_specs,
                        NULL AS size,
                        {$tyres_table}.brand,
                        {$tyres_table}.model_name,
                        NULL AS code,
                        NULL AS paint,
                        {$tyres_table}.w,
                        {$tyres_table}.r,
                        NULL AS pcd_stud,
                        NULL AS pcd_dia,
                        NULL AS et,
                        NULL AS ch,
                        {$tyres_table}.h,
                        {$tyres_table}.load_rate,
                        {$tyres_table}.stock,
                        {$tyres_table}.retail,
                        {$tyres_table}.promo,
                        {$tyres_table}.promotion_id
                 FROM {$tyres_table}
                 WHERE {$tyres_table}.id IN (" . str_repeat('?,', (count($unique_codes['tyres']) - 1)) . "?) )";
            $statement_parameters['execute_array'] = array_merge($statement_parameters['execute_array'], $unique_codes['tyres']);
        }

        if( !empty($unique_codes['exclusive_tyres']) ) {
            $statement_parameters['query_string']['exclusive_tyres'] = "
                (SELECT 'exclusive_tyres' AS type,
                        NULL AS item_type,
                        {$tyres_exclusive_table}.id,
                        {$tyres_exclusive_table}.unique_code,
                        NULL AS item_specs,
                        NULL AS size,
                        {$tyres_exclusive_table}.brand,
                        {$tyres_exclusive_table}.model_name,
                        NULL AS code,
                        NULL AS paint,
                        {$tyres_exclusive_table}.w,
                        {$tyres_exclusive_table}.r,
                        NULL AS pcd_stud,
                        NULL AS pcd_dia,
                        NULL AS et,
                        NULL AS ch,
                        {$tyres_exclusive_table}.h,
                        {$tyres_exclusive_table}.load_rate,
                        {$tyres_exclusive_table}.stock,
                        {$tyres_exclusive_table}.retail,
                        {$tyres_exclusive_table}.promo,
                        {$tyres_exclusive_table}.promotion_id
                 FROM {$tyres_exclusive_table}
                 WHERE {$tyres_exclusive_table}.id IN (" . str_repeat('?,', (count($unique_codes['exclusive_tyres']) - 1)) . "?) )";
            $statement_parameters['execute_array'] = array_merge($statement_parameters['execute_array'], $unique_codes['exclusive_tyres']);
        }

        if( !empty($unique_codes['spares']) ) {
            $statement_parameters['query_string']['spares'] = "
                (SELECT 'spares' AS type,
                        {$spares_table}.type AS item_type,
                        {$spares_table}.id,
                        {$spares_table}.unique_code,
                        {$spares_table}.item_specs,
                        {$spares_table}.size,
                        NULL AS brand,
                        NULL AS model_name,
                        NULL AS code,
                        NULL AS paint,
                        NULL AS w,
                        NULL AS r,
                        NULL AS pcd_stud,
                        NULL AS pcd_dia,
                        NULL AS et,
                        NULL AS ch,
                        NULL AS h,
                        NULL AS load_rate,
                        NULL AS stock,
                        {$spares_table}.retail AS retail,
                        NULL AS promo,
                        NULL AS promotion_id
                 FROM {$spares_table}
                 WHERE {$spares_table}.id IN (" . str_repeat('?,', (count($unique_codes['spares']) - 1)) . "?) )";
            $statement_parameters['execute_array'] = array_merge($statement_parameters['execute_array'], $unique_codes['spares']);
        }

        if( !empty($statement_parameters['query_string']) && !empty($statement_parameters['execute_array']) )
        {
            try {
                $statement = $this->db_handler->data_object->prepare(implode('UNION ALL', $statement_parameters['query_string']));
                $statement->execute($statement_parameters['execute_array']);

                $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $PDOEX) {
                throw new coreException("PDOException: {$PDOEX->getMessage()}");
            }

            return $statement_out;
        } else {
            return FALSE;
        }
    }

    public function count_cart_items($items_array)
    {
        if( empty($items_array) ) {
            return FALSE;
        }

        $total_price = NULL;
        $total_count = NULL;

        foreach($items_array as $value)
        {
            $total_price += (( !empty($value['promo']) ) ? $value['promo'] : $value['retail']) * $this->get_quantity_if_item_exists($value['type'], $value['id']);
            $total_count += (1 * $this->get_quantity_if_item_exists($value['type'], $value['id']));
        }

        return [
            'quantity' => $total_count,
            'price'    => $total_price
        ];
    }

    public function clear_cart()
    {
        unset($_SESSION['user_cart']);
    }
}
?>
