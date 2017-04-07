<?php
namespace _meat\classes\specific;

use coreException, procException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler;

use PDO;

class CatalogCells
{
    const VIEWED_QUEUE_SIZE = 10;

    private $db_handler = NULL;

    private $viewed_items = [];

    function __construct(DB_Handler $DBH)
    {
        $this->db_handler = $DBH;

        $this->set_viewed_items();
    }

    private function combined_statement($statement_parameters)
    {
        try {
            $statement = $this->db_handler->data_object->prepare($statement_parameters['query_string']);
            $statement->execute($statement_parameters['execute_array']);

            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $statement_out;
    }

    private function top_items_query_string()
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        $top_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_table}.is_top = 'Y' AND {$rims_table}.brand <> 'NULL' AND {$rims_table}.model_name <> 'NULL'
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_table}.is_top = 'Y' AND {$tyres_table}.brand <> 'NULL' AND {$tyres_table}.model_name <> 'NULL'
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)
             UNION ALL
            (SELECT 'exclusive_rims' AS type,
                    {$rims_exclusive_table}.id,
                    {$rims_exclusive_table}.date_created,
                    {$rims_exclusive_table}.brand,
                    {$rims_exclusive_table}.model_name,
                    {$rims_exclusive_table}.code,
                    {$rims_exclusive_table}.paint,
                    {$rims_exclusive_table}.stock,
                    MIN({$rims_exclusive_table}.retail) AS retail,
                    {$rims_exclusive_table}.promo,
                    {$rims_exclusive_table}.is_top,
                    {$rims_exclusive_table}.promotion_id,
                    {$rims_exclusive_table}.rating_score
             FROM {$rims_exclusive_table}
             WHERE {$rims_exclusive_table}.is_top = 'Y' AND {$rims_exclusive_table}.brand <> 'NULL' AND {$rims_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)
             UNION ALL
            (SELECT 'exclusive_tyres' AS type,
                    {$tyres_exclusive_table}.id,
                    {$tyres_exclusive_table}.date_created,
                    {$tyres_exclusive_table}.brand,
                    {$tyres_exclusive_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_exclusive_table}.stock,
                    MIN({$tyres_exclusive_table}.retail) AS retail,
                    {$tyres_exclusive_table}.promo,
                    {$tyres_exclusive_table}.is_top,
                    {$tyres_exclusive_table}.promotion_id,
                    {$tyres_exclusive_table}.rating_score
             FROM {$tyres_exclusive_table}
             WHERE {$tyres_exclusive_table}.is_top = 'Y' AND {$tyres_exclusive_table}.brand <> 'NULL' AND {$tyres_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";

        return $top_items_query;
    }

    public function count_top_items_data_cell()
    {
        $statement_parameters['query_string']  = "SELECT COUNT(*) AS items_number FROM (" . $this->top_items_query_string() . ") AS top_items";
        $statement_parameters['execute_array'] = [];

        return $this->combined_statement($statement_parameters)[0]['items_number'];
    }

    public function top_items_data_cell($items_per_lift)
    {
        $items_per_lift = (array)$items_per_lift;

        $statement_parameters['query_string']  = $this->top_items_query_string() . "ORDER BY date_created DESC LIMIT " . str_repeat('?,', (count($items_per_lift) - 1)) . "?";

        foreach($items_per_lift as $value) {
            $statement_parameters['execute_array'][] = (int)$value;
        }

        return $this->combined_statement($statement_parameters);
    }

    private function promo_items_query_string()
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $top_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_table}.brand <> 'NULL' AND {$rims_table}.model_name <> 'NULL' AND {$rims_table}.promotion_id = :id_1
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_table}.brand <> 'NULL' AND {$tyres_table}.model_name <> 'NULL' AND {$tyres_table}.promotion_id = :id_2
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)";

        return $top_items_query;
    }

    public function promo_items_data_cell($promotion_id)
    {
        $statement_parameters['query_string'] = $this->promo_items_query_string() . "ORDER BY date_created DESC";

        $statement_parameters['execute_array'] = [
            ':id_1' => (int)$promotion_id,
            ':id_2' => (int)$promotion_id
        ];

        return $this->combined_statement($statement_parameters);
    }

    private function newest_items_query_string()
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        $top_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_table}.brand <> 'NULL' AND {$rims_table}.model_name <> 'NULL'
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_table}.brand <> 'NULL' AND {$tyres_table}.model_name <> 'NULL'
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)
             UNION ALL
            (SELECT 'exclusive_rims' AS type,
                    {$rims_exclusive_table}.id,
                    {$rims_exclusive_table}.date_created,
                    {$rims_exclusive_table}.brand,
                    {$rims_exclusive_table}.model_name,
                    {$rims_exclusive_table}.code,
                    {$rims_exclusive_table}.paint,
                    {$rims_exclusive_table}.stock,
                    MIN({$rims_exclusive_table}.retail) AS retail,
                    {$rims_exclusive_table}.promo,
                    {$rims_exclusive_table}.is_top,
                    {$rims_exclusive_table}.promotion_id,
                    {$rims_exclusive_table}.rating_score
             FROM {$rims_exclusive_table}
             WHERE {$rims_exclusive_table}.brand <> 'NULL' AND {$rims_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)
             UNION ALL
            (SELECT 'exclusive_tyres' AS type,
                    {$tyres_exclusive_table}.id,
                    {$tyres_exclusive_table}.date_created,
                    {$tyres_exclusive_table}.brand,
                    {$tyres_exclusive_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_exclusive_table}.stock,
                    MIN({$tyres_exclusive_table}.retail) AS retail,
                    {$tyres_exclusive_table}.promo,
                    {$tyres_exclusive_table}.is_top,
                    {$tyres_exclusive_table}.promotion_id,
                    {$tyres_exclusive_table}.rating_score
             FROM {$tyres_exclusive_table}
             WHERE {$tyres_exclusive_table}.brand <> 'NULL' AND {$tyres_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";

        return $top_items_query;
    }

    public function newest_items_data_cell($items)
    {
        $statement_parameters['query_string']  = $this->newest_items_query_string() . "ORDER BY date_created DESC LIMIT ?";

        $statement_parameters['execute_array'][] = (int)$items;

        return $this->combined_statement($statement_parameters);
    }

    private function popular_items_query_string()
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        $top_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.views,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_table}.brand <> 'NULL' AND {$rims_table}.model_name <> 'NULL'
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.views,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_table}.brand <> 'NULL' AND {$tyres_table}.model_name <> 'NULL'
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)
             UNION ALL
            (SELECT 'exclusive_rims' AS type,
                    {$rims_exclusive_table}.id,
                    {$rims_exclusive_table}.date_created,
                    {$rims_exclusive_table}.brand,
                    {$rims_exclusive_table}.model_name,
                    {$rims_exclusive_table}.code,
                    {$rims_exclusive_table}.paint,
                    {$rims_exclusive_table}.stock,
                    MIN({$rims_exclusive_table}.retail) AS retail,
                    {$rims_exclusive_table}.promo,
                    {$rims_exclusive_table}.is_top,
                    {$rims_exclusive_table}.promotion_id,
                    {$rims_exclusive_table}.views,
                    {$rims_exclusive_table}.rating_score
             FROM {$rims_exclusive_table}
             WHERE {$rims_exclusive_table}.brand <> 'NULL' AND {$rims_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)
             UNION ALL
            (SELECT 'exclusive_tyres' AS type,
                    {$tyres_exclusive_table}.id,
                    {$tyres_exclusive_table}.date_created,
                    {$tyres_exclusive_table}.brand,
                    {$tyres_exclusive_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_exclusive_table}.stock,
                    MIN({$tyres_exclusive_table}.retail) AS retail,
                    {$tyres_exclusive_table}.promo,
                    {$tyres_exclusive_table}.is_top,
                    {$tyres_exclusive_table}.promotion_id,
                    {$tyres_exclusive_table}.views,
                    {$tyres_exclusive_table}.rating_score
             FROM {$tyres_exclusive_table}
             WHERE {$tyres_exclusive_table}.brand <> 'NULL' AND {$tyres_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";

        return $top_items_query;
    }

    public function popular_items_data_cell($items)
    {
        $statement_parameters['query_string']  = $this->popular_items_query_string() . "ORDER BY views DESC LIMIT ?";

        $statement_parameters['execute_array'][] = (int)$items;

        return $this->combined_statement($statement_parameters);
    }

    // BRANDED items data cell

    private function branded_items_query_string()
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        $branded_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.views,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_table}.brand = :brand_1 AND {$rims_table}.model_name <> 'NULL'
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.views,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_table}.brand = :brand_2 AND {$tyres_table}.model_name <> 'NULL'
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)
             UNION ALL
            (SELECT 'exclusive_rims' AS type,
                    {$rims_exclusive_table}.id,
                    {$rims_exclusive_table}.date_created,
                    {$rims_exclusive_table}.brand,
                    {$rims_exclusive_table}.model_name,
                    {$rims_exclusive_table}.code,
                    {$rims_exclusive_table}.paint,
                    {$rims_exclusive_table}.stock,
                    MIN({$rims_exclusive_table}.retail) AS retail,
                    {$rims_exclusive_table}.promo,
                    {$rims_exclusive_table}.is_top,
                    {$rims_exclusive_table}.promotion_id,
                    {$rims_exclusive_table}.views,
                    {$rims_exclusive_table}.rating_score
             FROM {$rims_exclusive_table}
             WHERE {$rims_exclusive_table}.brand = :brand_3 AND {$rims_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)
             UNION ALL
            (SELECT 'exclusive_tyres' AS type,
                    {$tyres_exclusive_table}.id,
                    {$tyres_exclusive_table}.date_created,
                    {$tyres_exclusive_table}.brand,
                    {$tyres_exclusive_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_exclusive_table}.stock,
                    MIN({$tyres_exclusive_table}.retail) AS retail,
                    {$tyres_exclusive_table}.promo,
                    {$tyres_exclusive_table}.is_top,
                    {$tyres_exclusive_table}.promotion_id,
                    {$tyres_exclusive_table}.views,
                    {$tyres_exclusive_table}.rating_score
             FROM {$tyres_exclusive_table}
             WHERE {$tyres_exclusive_table}.brand = :brand_4 AND {$tyres_exclusive_table}.model_name <> 'NULL'
             GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";

        return $branded_items_query;
    }

    public function branded_items_data_cell($limit, $brand)
    {
        $statement_parameters['query_string']  = $this->branded_items_query_string() . "ORDER BY date_created DESC LIMIT :limit";

        $statement_parameters['execute_array'] = array_merge([
            ':brand_1' => $brand,
            ':brand_2' => $brand,
            ':brand_3' => $brand,
            ':brand_4' => $brand,
            ':limit'   => (int)$limit,
        ]);

        return $this->combined_statement($statement_parameters);
    }

    // END / BRANDED items data cell

    private function search_items_query_string($search_items)
    {
        $rims_table  = Setup::DB_PREFIX_alpha.'items_rims';
        $tyres_table = Setup::DB_PREFIX_alpha.'items_tyres';

        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        for($i = 0; $i < $search_items; $i++) {
            $rims_where[]  = "({$rims_table}.brand LIKE ? OR {$rims_table}.model_name LIKE ? OR {$rims_table}.code LIKE ? OR {$rims_table}.paint LIKE ?)";
            $tyres_where[] = "({$tyres_table}.brand LIKE ? OR {$tyres_table}.model_name LIKE ?)";

            $rims_exclusive_where[]  = "({$rims_exclusive_table}.brand LIKE ? OR {$rims_exclusive_table}.model_name LIKE ? OR {$rims_exclusive_table}.code LIKE ? OR {$rims_exclusive_table}.paint LIKE ?)";
            $tyres_exclusive_where[] = "({$tyres_exclusive_table}.brand LIKE ? OR {$tyres_exclusive_table}.model_name LIKE ?)";
        }

        $rims_where  = implode(' AND ', $rims_where);
        $tyres_where = implode(' AND ', $tyres_where);

        $rims_exclusive_where  = implode(' AND ', $rims_exclusive_where);
        $tyres_exclusive_where = implode(' AND ', $tyres_exclusive_where);

        $search_items_query = "
            (SELECT 'rims' AS type,
                    {$rims_table}.id,
                    {$rims_table}.date_created,
                    {$rims_table}.brand,
                    {$rims_table}.model_name,
                    {$rims_table}.code,
                    {$rims_table}.paint,
                    {$rims_table}.stock,
                    MIN({$rims_table}.retail) AS retail,
                    {$rims_table}.promo,
                    {$rims_table}.is_top,
                    {$rims_table}.promotion_id,
                    {$rims_table}.rating_score
             FROM {$rims_table}
             WHERE {$rims_where}
             GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)
             UNION ALL
            (SELECT 'tyres' AS type,
                    {$tyres_table}.id,
                    {$tyres_table}.date_created,
                    {$tyres_table}.brand,
                    {$tyres_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_table}.stock,
                    MIN({$tyres_table}.retail) AS retail,
                    {$tyres_table}.promo,
                    {$tyres_table}.is_top,
                    {$tyres_table}.promotion_id,
                    {$tyres_table}.rating_score
             FROM {$tyres_table}
             WHERE {$tyres_where}
             GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)
             UNION ALL
            (SELECT 'exclusive_rims' AS type,
                    {$rims_exclusive_table}.id,
                    {$rims_exclusive_table}.date_created,
                    {$rims_exclusive_table}.brand,
                    {$rims_exclusive_table}.model_name,
                    {$rims_exclusive_table}.code,
                    {$rims_exclusive_table}.paint,
                    {$rims_exclusive_table}.stock,
                    MIN({$rims_exclusive_table}.retail) AS retail,
                    {$rims_exclusive_table}.promo,
                    {$rims_exclusive_table}.is_top,
                    {$rims_exclusive_table}.promotion_id,
                    {$rims_exclusive_table}.rating_score
             FROM {$rims_exclusive_table}
             WHERE {$rims_exclusive_where}
             GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)
             UNION ALL
            (SELECT 'exclusive_tyres' AS type,
                    {$tyres_exclusive_table}.id,
                    {$tyres_exclusive_table}.date_created,
                    {$tyres_exclusive_table}.brand,
                    {$tyres_exclusive_table}.model_name,
                    NULL AS code,
                    NULL AS paint,
                    {$tyres_exclusive_table}.stock,
                    MIN({$tyres_exclusive_table}.retail) AS retail,
                    {$tyres_exclusive_table}.promo,
                    {$tyres_exclusive_table}.is_top,
                    {$tyres_exclusive_table}.promotion_id,
                    {$tyres_exclusive_table}.rating_score
             FROM {$tyres_exclusive_table}
             WHERE {$tyres_exclusive_where}
             GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";

        return $search_items_query;
    }

    public function count_search_items_data_cell($search)
    {
        foreach($search as $value)
        {
            for($i = 0; $i < 4; $i++) {
                $rims_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 2; $i++) {
                $tyres_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 4; $i++) {
                $rims_exclusive_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 2; $i++) {
                $tyres_exclusive_array[] = '%'.$value.'%';
            }
        }

        $statement_parameters['query_string']  = "SELECT COUNT(*) AS items_number FROM (" . $this->search_items_query_string(count($search)) . ") AS top_items";
        $statement_parameters['execute_array'] = array_merge($rims_array, $tyres_array, $rims_exclusive_array, $tyres_exclusive_array);

        return $this->combined_statement($statement_parameters)[0]['items_number'];
    }

    public function search_items_data_cell($search, $items_per_lift)
    {
        $items_per_lift = (array)$items_per_lift;

        foreach($search as $value)
        {
            for($i = 0; $i < 4; $i++) {
                $rims_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 2; $i++) {
                $tyres_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 4; $i++) {
                $rims_exclusive_array[] = '%'.$value.'%';
            }

            for($i = 0; $i < 2; $i++) {
                $tyres_exclusive_array[] = '%'.$value.'%';
            }
        }

        $statement_parameters['query_string']  = $this->search_items_query_string(count($search)) . "ORDER BY date_created DESC LIMIT " . str_repeat('?,', (count($items_per_lift) - 1)) . "?";

        foreach($items_per_lift as $value) {
            $statement_parameters['execute_array'][] = (int)$value;
        }

        $statement_parameters['execute_array'] = array_merge($rims_array, $tyres_array, $rims_exclusive_array, $tyres_exclusive_array, $statement_parameters['execute_array']);

        return $this->combined_statement($statement_parameters);
    }

    /* VIEWED ITEMS */

    public function set_viewed_items()
    {
        $this->viewed_items = ( !empty($_SESSION['viewed_items']) ) ? $_SESSION['viewed_items'] : [];
    }

    public function enqueue_viewed_item($type, $id)
    {
        array_unshift($this->viewed_items, implode('|', [$type, $id]));

        $this->viewed_items = array_unique($this->viewed_items);

        if( count($this->viewed_items) > self::VIEWED_QUEUE_SIZE )
            $this->dequeue_viewed_item();

        $_SESSION['viewed_items'] = $this->viewed_items;
    }

    public function dequeue_viewed_item()
    {
        if( !empty($this->viewed_items) )
            array_pop($this->viewed_items);
    }

    public function viewed_items_data_cell()
    {
        $transform = function($input_items)
        {
            $output_items = [];

            foreach($input_items as $value)
            {
                list($type, $id) = explode('|', $value);

                $output_items[$type][] = $id;
            }

            return $output_items;
        };

        $viewed_items = $transform($this->viewed_items);

        if( empty($viewed_items) )
            return FALSE;

        $execute_array = array_merge(
            ( !empty($viewed_items['rims']) ? $viewed_items['rims'] : []),
            ( !empty($viewed_items['exclusive_rims']) ? $viewed_items['exclusive_rims'] : []),
            ( !empty($viewed_items['tyres']) ? $viewed_items['tyres'] : []),
            ( !empty($viewed_items['exclusive_tyres']) ? $viewed_items['exclusive_tyres'] : [])
        );

        $statement_parameters['query_string'] = $this->viewed_items_query_string($transform($this->viewed_items));

        $statement_parameters['execute_array'] = $execute_array;

        $viewed_items = $this->viewed_items;

        $order = function($input_items) use ($viewed_items)
        {
            $output_items = [];

            foreach($viewed_items as $item)
            {
                list($type, $id) = explode('|', $item);

                foreach($input_items as $key => $value)
                {
                    if( $value['type'] == $type && $value['id'] == $id )
                    {
                        $output_items[] = $value;

                        unset($input_items[$key]);
                    }
                }
            }

            return $output_items;
        };

        return $order($this->combined_statement($statement_parameters));
    }

    private function viewed_items_query_string($viewed_items)
    {
        $search_items_query = [];

        $rims_table            = Setup::DB_PREFIX_alpha.'items_rims';
        $rims_exclusive_table  = Setup::DB_PREFIX_alpha.'items_rims_exclusive';
        $tyres_table           = Setup::DB_PREFIX_alpha.'items_tyres';
        $tyres_exclusive_table = Setup::DB_PREFIX_alpha.'items_tyres_exclusive';

        if( !empty($viewed_items['rims']) )
        {
            $in_string = rtrim(str_repeat('?,', count($viewed_items['rims'])), ',');

            $search_items_query[] = "
                (SELECT 'rims' AS type,
                        {$rims_table}.id,
                        {$rims_table}.date_created,
                        {$rims_table}.brand,
                        {$rims_table}.model_name,
                        {$rims_table}.code,
                        {$rims_table}.paint,
                        {$rims_table}.stock,
                        MIN({$rims_table}.retail) AS retail,
                        {$rims_table}.promo,
                        {$rims_table}.is_top,
                        {$rims_table}.promotion_id,
                        {$rims_table}.rating_score
                 FROM {$rims_table}
                 WHERE {$rims_table}.id IN ({$in_string})
                 GROUP BY {$rims_table}.brand, {$rims_table}.model_name, {$rims_table}.code, {$rims_table}.paint)";
        }

        if( !empty($viewed_items['exclusive_rims']) )
        {
            $in_string = rtrim(str_repeat('?,', count($viewed_items['exclusive_rims'])), ',');

            $search_items_query[] = "
                (SELECT 'exclusive_rims' AS type,
                        {$rims_exclusive_table}.id,
                        {$rims_exclusive_table}.date_created,
                        {$rims_exclusive_table}.brand,
                        {$rims_exclusive_table}.model_name,
                        {$rims_exclusive_table}.code,
                        {$rims_exclusive_table}.paint,
                        {$rims_exclusive_table}.stock,
                        MIN({$rims_exclusive_table}.retail) AS retail,
                        {$rims_exclusive_table}.promo,
                        {$rims_exclusive_table}.is_top,
                        {$rims_exclusive_table}.promotion_id,
                        {$rims_exclusive_table}.rating_score
                 FROM {$rims_exclusive_table}
                 WHERE {$rims_exclusive_table}.id IN ({$in_string})
                 GROUP BY {$rims_exclusive_table}.brand, {$rims_exclusive_table}.model_name, {$rims_exclusive_table}.code, {$rims_exclusive_table}.paint)";
        }

        if( !empty($viewed_items['tyres']) )
        {
            $in_string = rtrim(str_repeat('?,', count($viewed_items['tyres'])), ',');

            $search_items_query[] = "
                (SELECT 'tyres' AS type,
                        {$tyres_table}.id,
                        {$tyres_table}.date_created,
                        {$tyres_table}.brand,
                        {$tyres_table}.model_name,
                        NULL AS code,
                        NULL AS paint,
                        {$tyres_table}.stock,
                        MIN({$tyres_table}.retail) AS retail,
                        {$tyres_table}.promo,
                        {$tyres_table}.is_top,
                        {$tyres_table}.promotion_id,
                        {$tyres_table}.rating_score
                 FROM {$tyres_table}
                 WHERE {$tyres_table}.id IN ({$in_string})
                 GROUP BY {$tyres_table}.brand, {$tyres_table}.model_name)";
        }

        if( !empty($viewed_items['exclusive_tyres']) )
        {
            $in_string = rtrim(str_repeat('?,', count($viewed_items['exclusive_tyres'])), ',');

            $search_items_query[] = "
                (SELECT 'exclusive_tyres' AS type,
                        {$tyres_exclusive_table}.id,
                        {$tyres_exclusive_table}.date_created,
                        {$tyres_exclusive_table}.brand,
                        {$tyres_exclusive_table}.model_name,
                        NULL AS code,
                        NULL AS paint,
                        {$tyres_exclusive_table}.stock,
                        MIN({$tyres_exclusive_table}.retail) AS retail,
                        {$tyres_exclusive_table}.promo,
                        {$tyres_exclusive_table}.is_top,
                        {$tyres_exclusive_table}.promotion_id,
                        {$tyres_exclusive_table}.rating_score
                 FROM {$tyres_exclusive_table}
                 WHERE {$tyres_exclusive_table}.id IN ({$in_string})
                 GROUP BY {$tyres_exclusive_table}.brand, {$tyres_exclusive_table}.model_name)";
        }

        return implode(" UNION ALL ", $search_items_query);
    }
}
