<?php
namespace _meat\classes\specific;

use coreException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler, PDO;

use PDOException;

class CatalogInput
{
    private $db_handler = NULL;

    private $rims_required_cells = [
        "UNIQUE CODE", "BRAND", "NAME", "PAINT", "PCD STUD", "PCD DIA", "W", "R", "ET", "CH", "RIM TYPE", "STOCK", "DEALER", "RETAIL"
    ];

    private $tyres_required_cells = [
        "UNIQUE CODE", "BRAND", "NAME", "SEASON", "R", "W", "H", "STOCK", "DEALER", "RETAIL"
    ];

    private $spares_required_cells = [
        //"UNIQUE CODE", "BRAND", "SPECS", "SIZE", "RETAIL"
        "UNIQUE CODE", "BRAND", "RETAIL"
    ];

    function __construct(DB_Handler $db_handler)
    {
        $this->db_handler = $db_handler;
    }

    public function update_catalog(array $catalog_data)
    {
        if( empty($catalog_data['data']) )
            return FALSE;

        switch( $catalog_data['type'] )
        {
            case 'rims':
            case 'exclusive_rims':
                $result = $this->update_rims_catalog($this->collect_rims_data($catalog_data['data']), $catalog_data['type']);
            break;

            case 'tyres':
            case 'exclusive_tyres':
                $result = $this->update_tyres_catalog($this->collect_tyres_data($catalog_data['data']), $catalog_data['type']);
            break;

            case 'spares':
                $result = $this->update_spares_catalog($this->collect_spares_data($catalog_data['data']));
            break;

            default:
                return FALSE;
            break;
        }

        return ( $result ) ? TRUE : FALSE;
    }

    public function delete_catalog($type)
    {
        switch( $type )
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
        }

        if( !$table )
            return FALSE;

        $this->db_handler->data_object
            ->prepare("TRUNCATE TABLE {$table}")
            ->execute()
        ;

        return TRUE;
    }

    public function update_clean_catalog(array $catalog_data)
    {
        if( empty($catalog_data['data']) )
            return FALSE;

        switch( $catalog_data['type'] )
        {
            case 'rims':
                $table = Setup::DB_PREFIX_alpha."items_rims";
                $result = $this->clean_wheels_catalog($this->collect_rims_data($catalog_data['data']), $table);
            break;

            case 'exclusive_rims':
                $table = Setup::DB_PREFIX_alpha."items_rims_exclusive";
                $result = $this->clean_wheels_catalog($this->collect_rims_data($catalog_data['data']), $table);
            break;

            case 'tyres':
                $table = Setup::DB_PREFIX_alpha."items_tyres";
                $result = $this->clean_wheels_catalog($this->collect_tyres_data($catalog_data['data']), $table);
            break;

            case 'exclusive_tyres':
                $table = Setup::DB_PREFIX_alpha."items_tyres_exclusive";
                $result = $this->clean_wheels_catalog($this->collect_tyres_data($catalog_data['data']), $table);
            break;

            case 'spares':
                $table = Setup::DB_PREFIX_alpha."items_spares";
                $result = $this->clean_spares_catalog($this->collect_spares_data($catalog_data['data']), $table);
            break;

            default:
                return FALSE;
            break;
        }

        return ( $result ) ? TRUE : FALSE;
    }

    private function clean_wheels_catalog($data, $table)
    {
        $statement = function($table, $query_string) {
            return "DELETE FROM {$table} WHERE NULLIF(description, ' ') IS NULL AND unique_code NOT IN {$query_string}";
        };

        return $this->clean_catalog($statement, $data, $table);
    }

    private function clean_spares_catalog($data, $table)
    {
        $statement = function($table, $query_string) {
            return "DELETE FROM {$table} WHERE unique_code NOT IN {$query_string}";
        };

        return $this->clean_catalog($statement, $data, $table);
    }

    private function clean_catalog($statement, $data, $table)
    {
        $prepared_data = $this->prepare_clean_data($data);

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                $statement($table, $prepared_data['query_string'])
            );

            $statement->execute($prepared_data['execute_array']);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            //return FALSE;
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    private function prepare_data($collected_data)
    {
        $query_array   = [];
        $execute_array = [];

        for($i = 0; $i < count($collected_data); $i++) {
            $query_array[] = "(?" . str_repeat(',?', (count($collected_data[$i]) - 1)) . ")";
            $execute_array = array_merge($execute_array, array_values($collected_data[$i]));
        }

        return ['query_string' => implode(',', $query_array), 'execute_array' => $execute_array];
    }

    private function prepare_descriptions_data($collected_data, $type)
    {
        $query_array   = [];
        $execute_array = [];

        for($i = 0; $i < count($collected_data); $i++) {
            $query_array[] = "(?,?)";
            $execute_array = array_merge($execute_array, [$collected_data[$i]['unique_code'], $type]);
        }

        return ['query_string' => implode(',', $query_array), 'execute_array' => $execute_array];
    }

    private function prepare_clean_data($collected_data)
    {
        $query_string  = '';
        $execute_array = [];

        for($i = 0; $i < count($collected_data); $i++) {
            $execute_array[] = $collected_data[$i]['unique_code'];
        }

        $query_string = "(?" . str_repeat(',?', (count($collected_data) - 1)) . ")";

        return ['query_string' => $query_string, 'execute_array' => $execute_array];
    }

    private function check_rims_cell_structure($cell_names_row)
    {
        $cell_names_row = array_unique($cell_names_row);
        return (count(array_intersect($cell_names_row, $this->rims_required_cells)) == count($this->rims_required_cells));
    }

    private function collect_rims_data($rims_data)
    {
        $collected_data = NULL;

        foreach($rims_data as $brand => $value)
        {
            if( !$this->check_rims_cell_structure($value[0]) )
                return FALSE;

            $rims_keys = array_flip(array_filter($value[0]));
            unset($value[0]);

            foreach($value as $row)
            {
                if( empty($row[$rims_keys['UNIQUE CODE']]) || $row[$rims_keys['UNIQUE CODE']] === "UNIQUE CODE" )
                    continue;

                $collected_data[] = [
                    'unique_code'   => $row[$rims_keys['UNIQUE CODE']],
                    'date_created'  => time(),
                    'brand'         => $row[$rims_keys['BRAND']],
                    'name'          => $row[$rims_keys['NAME']],
                    'code'          => NULL,
                    'paint'         => $row[$rims_keys['PAINT']],
                    'pcd_stud'      => $row[$rims_keys['PCD STUD']],
                    'pcd_dia'       => $row[$rims_keys['PCD DIA']],
                    'pcd_dia_extra' => $row[$rims_keys['PCD DIA EXTRA']],
                    'w'             => $row[$rims_keys['W']],
                    'r'             => $row[$rims_keys['R']],
                    'et'            => $row[$rims_keys['ET']],
                    'ch'            => $row[$rims_keys['CH']],
                    'rim_type'      => $row[$rims_keys['RIM TYPE']],
                    'location'      => $row[$rims_keys['LOCATION']],
                    'stock'         => $row[$rims_keys['STOCK']],
                    'dealer'        => $row[$rims_keys['DEALER']],
                    'retail'        => $row[$rims_keys['RETAIL']]
                ];
            }
        }

        return $collected_data;
    }

    private function update_rims_catalog($rims_data, $type)
    {
        $prepared_data = $this->prepare_data($rims_data);

        if( $type === 'rims' ) {
            $rims_table = Setup::DB_PREFIX_alpha."items_rims";
        } elseif( $type === 'exclusive_rims' ) {
            $rims_table = Setup::DB_PREFIX_alpha."items_rims_exclusive";
        } else {
            return FALSE;
        }

        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";
        $prepared_descriptions_data = $this->prepare_descriptions_data($rims_data, $type);

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$rims_table}
                        (unique_code, date_created, brand, model_name, code, paint, pcd_stud, pcd_dia, pcd_dia_extra, w, r, et, ch, rim_type, location, stock, dealer, retail)
                        VALUES {$prepared_data['query_string']}
                        ON DUPLICATE KEY UPDATE
                        brand         = VALUES(brand),
                        model_name    = VALUES(model_name),
                        code          = VALUES(code),
                        paint         = VALUES(paint),
                        pcd_stud      = VALUES(pcd_stud),
                        pcd_dia       = VALUES(pcd_dia),
                        pcd_dia_extra = VALUES(pcd_dia_extra),
                        w             = VALUES(w),
                        r             = VALUES(r),
                        et            = VALUES(et),
                        ch            = VALUES(ch),
                        rim_type      = VALUES(rim_type),
                        location      = VALUES(location),
                        stock         = VALUES(stock),
                        dealer        = VALUES(dealer),
                        retail        = VALUES(retail)"
            );
            $statement->execute($prepared_data['execute_array']);

            $statement = $this->db_handler->data_object->prepare(
                "INSERT IGNORE INTO {$items_descriptions_table}
                        (unique_code, type)
                        VALUES {$prepared_descriptions_data['query_string']}"
            );
            $statement->execute($prepared_descriptions_data['execute_array']);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            return FALSE;
            //throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    private function check_tyres_cell_structure($cell_names_row)
    {
        $cell_names_row = array_unique($cell_names_row);
        return (count(array_intersect($cell_names_row, $this->tyres_required_cells)) == count($this->tyres_required_cells));
    }

    private function collect_tyres_data($tyres_data)
    {
        $collected_data = NULL;

        $time = time();

        foreach($tyres_data as $sheet => $value)
        {
            if( !$this->check_tyres_cell_structure($value[0]) )
                return FALSE;

            $tyres_keys = array_flip(array_filter($value[0]));
            unset($value[0]);

            foreach($value as $row)
            {
                if( empty($row[$tyres_keys['UNIQUE CODE']]) || $row[$tyres_keys['UNIQUE CODE']] === "UNIQUE CODE" )
                    continue;

                $collected_data[] = [
                    'unique_code'  => $row[$tyres_keys['UNIQUE CODE']],
                    'date_created' => $time,
                    'brand'        => $row[$tyres_keys['BRAND']],
                    'name'         => $row[$tyres_keys['NAME']],
                    'season'       => $row[$tyres_keys['SEASON']],
                    'r'            => $row[$tyres_keys['R']],
                    'w'            => $row[$tyres_keys['W']],
                    'h'            => $row[$tyres_keys['H']],
                    'load_rate'    => $row[$tyres_keys['LOAD']],
                    'speed'        => $row[$tyres_keys['SPEED']],
                    'extra'        => $row[$tyres_keys['EXTRA']],
                    'location'     => $row[$tyres_keys['LOCATION']],
                    'stock'        => $row[$tyres_keys['STOCK']],
                    'dealer'       => $row[$tyres_keys['DEALER']],
                    'retail'       => $row[$tyres_keys['RETAIL']]
                ];
            }
        }

        return $collected_data;
    }

    private function update_tyres_catalog($tyres_data, $type)
    {
        $prepared_data = $this->prepare_data($tyres_data);

        if( $type === 'tyres' ) {
            $tyres_table = Setup::DB_PREFIX_alpha."items_tyres";
        } elseif( $type === 'exclusive_tyres' ) {
            $tyres_table = Setup::DB_PREFIX_alpha."items_tyres_exclusive";
        } else {
            return FALSE;
        }

        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";
        $prepared_descriptions_data = $this->prepare_descriptions_data($tyres_data, $type);

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$tyres_table}
                        (unique_code, date_created, brand, model_name, season, r, w, h, load_rate, speed, extra, location, stock, dealer, retail)
                        VALUES {$prepared_data['query_string']}
                        ON DUPLICATE KEY UPDATE
                        brand      = VALUES(brand),
                        model_name = VALUES(model_name),
                        season     = VALUES(season),
                        r          = VALUES(r),
                        w          = VALUES(w),
                        h          = VALUES(h),
                        load_rate  = VALUES(load_rate),
                        speed      = VALUES(speed),
                        extra      = VALUES(extra),
                        location   = VALUES(location),
                        stock      = VALUES(stock),
                        dealer     = VALUES(dealer),
                        retail     = VALUES(retail)"
            );
            $statement->execute($prepared_data['execute_array']);

            $statement = $this->db_handler->data_object->prepare(
                "INSERT IGNORE INTO {$items_descriptions_table}
                        (unique_code, type)
                        VALUES {$prepared_descriptions_data['query_string']}"
            );
            $statement->execute($prepared_descriptions_data['execute_array']);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            return FALSE;
            //throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    private function check_spares_cell_structure($cell_names_row)
    {
        $cell_names_row = array_unique($cell_names_row);
        return (count(array_intersect($cell_names_row, $this->spares_required_cells)) == count($this->spares_required_cells));
    }

    private function collect_spares_data($spares_data)
    {
        $collected_data = NULL;

        foreach($spares_data as $type => $value)
        {
            if( !$this->check_spares_cell_structure($value[0]) )
                return FALSE;

            $spares_keys = array_flip(array_filter($value[0]));
            unset($value[0]);

            switch($type)
            {
                case 'Кольца':
                    $type = 'rings';
                break;

                case 'Болты':
                    $type = 'bolts';
                break;

                case 'Гайки':
                    $type = 'nuts';
                break;

                case 'Секретки':
                    $type = 'locks';
                break;

                case 'Логотипы':
                    $type = 'logos';
                break;

                case 'Шпильки':
                    $type = 'pins';
                break;

                default:
                    return FALSE;
                break;
            }

            foreach($value as $row)
            {
                if( empty($row[$spares_keys['UNIQUE CODE']]) || $row[$spares_keys['UNIQUE CODE']] === "UNIQUE CODE" )
                    continue;

                $collected_data[] = [
                    'unique_code' => $row[$spares_keys['UNIQUE CODE']],
                    'brand'       => $row[$spares_keys['BRAND']],
                    'type'        => $type,
                    'item_specs'  => $row[$spares_keys['SPECS']],
                    'size'        => $row[$spares_keys['SIZE']],
                    'retail'      => $row[$spares_keys['RETAIL']],
                ];
            }
        }

        return $collected_data;
    }

    private function update_spares_catalog($spares_data)
    {
        $prepared_data = $this->prepare_data($spares_data);

        $spares_table = Setup::DB_PREFIX_alpha."items_spares";

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$spares_table}
                        (unique_code, brand, type, item_specs, size, retail)
                        VALUES {$prepared_data['query_string']}
                        ON DUPLICATE KEY UPDATE
                        brand      = VALUES(brand),
                        type       = VALUES(type),
						item_specs = VALUES(item_specs),
						size       = VALUES(size),
						retail     = VALUES(retail)"
            );

            $statement->execute($prepared_data['execute_array']);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            return FALSE;
            //throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function increment_views($items_table, $item_id)
    {
        if( !$this->db_handler->validate_tables($items_table) ) {
            return FALSE;
        }

        try {
            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$items_table} SET views = views + 1 WHERE id = :id"
            );

            $statement->execute([':id' => $item_id]);
        } catch(PDOException $PDOEX) {
            return FALSE;
        }
    }

    public function count_rate($current_score, $current_votes, $new_score)
    {
        return (($current_score * $current_votes) + $new_score) / ++$current_votes;
    }

    public function update_rate($items_type, $item_id, $new_score)
    {
        switch($items_type)
        {
            case 'rims':
                $items_table = Setup::DB_PREFIX_alpha."items_rims";
            break;

            case 'exclusive_rims':
                $items_table = Setup::DB_PREFIX_alpha."items_rims_exclusive";
            break;

            case 'tyres':
                $items_table = Setup::DB_PREFIX_alpha."items_tyres";
            break;

            case 'exclusive_tyres':
                $items_table = Setup::DB_PREFIX_alpha."items_tyres_exclusive";
            break;

            default:
                throw new CoreException("Rating update failed");
            break;
        }

        if( !$this->db_handler->validate_tables($items_table) ) {
            return FALSE;
        }

        try {
            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$items_table} SET rating_score = :new_score, rating_votes = rating_votes + 1 WHERE id = :id"
            );

            $statement->execute([':new_score' => $new_score, ':id' => $item_id]);

            return TRUE;
        } catch(PDOException $PDOEX) {
            return FALSE;
        }
    }

    /*
     * CMS
     */

    public function update_item_rim($items_table, $items_array)
    {
        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$items_table}
                 JOIN {$items_descriptions_table} ON {$items_table}.unique_code = {$items_descriptions_table}.unique_code
                 SET
                    {$items_table}.brand         = :brand,
                    {$items_table}.model_name    = :model_name,
                    {$items_table}.code          = :code,
                    {$items_table}.paint         = :paint,
                    {$items_table}.pcd_stud      = :pcd_stud,
                    {$items_table}.pcd_dia       = :pcd_dia,
                    {$items_table}.pcd_dia_extra = :pcd_dia_extra,
                    {$items_table}.w             = :w,
                    {$items_table}.r             = :r,
                    {$items_table}.et            = :et,
                    {$items_table}.ch            = :ch,
                    {$items_table}.rim_type      = :rim_type,
                    {$items_table}.stock         = :stock,
                    {$items_table}.dealer        = :dealer,
                    {$items_table}.retail        = :retail,
                    {$items_table}.promo         = :promo,
                    {$items_table}.is_top        = :is_top,
                    {$items_table}.promotion_id  = :promotion_id,
                    {$items_table}.description   = :description,
                    {$items_table}.video         = :video,
                    {$items_table}.views         = :views,
                    {$items_descriptions_table}.description = :item_description
                 WHERE {$items_table}.id = :id");

            foreach($items_array as $key => $value) {
                $statement->execute([
                    ':brand'         => ( !empty($value['brand']) ) ? $value['brand'] : NULL,
                    ':model_name'    => ( !empty($value['model_name']) ) ? $value['model_name'] : NULL,
                    ':code'          => ( !empty($value['code']) ) ? $value['code'] : NULL,
                    ':paint'         => ( !empty($value['paint']) ) ? $value['paint'] : NULL,
                    ':pcd_stud'      => ( !empty($value['pcd_stud']) ) ? $value['pcd_stud'] : NULL,
                    ':pcd_dia'       => ( !empty($value['pcd_dia']) ) ? $value['pcd_dia'] : NULL,
                    ':pcd_dia_extra' => ( !empty($value['pcd_dia_extra']) ) ? $value['pcd_dia_extra'] : NULL,
                    ':w'             => ( !empty($value['w']) ) ? $value['w'] : NULL,
                    ':r'             => ( !empty($value['r']) ) ? $value['r'] : NULL,
                    ':et'            => ( !empty($value['et']) ) ? $value['et'] : NULL,
                    ':ch'            => ( !empty($value['ch']) ) ? $value['ch'] : NULL,
                    ':rim_type'      => ( !empty($value['rim_type']) ) ? $value['rim_type'] : NULL,
                    ':stock'         => ( !empty($value['stock']) ) ? $value['stock'] : NULL,
                    ':dealer'        => ( !empty($value['dealer']) ) ? $value['dealer'] : NULL,
                    ':retail'        => ( !empty($value['retail']) ) ? $value['retail'] : NULL,
                    ':promo'         => ( !empty($value['promo']) ) ? $value['promo'] : NULL,
                    ':is_top'        => $value['is_top'],
                    ':promotion_id'  => $value['promotion_id'],
                    ':description'   => $value['description'],
                    ':video'         => ( !empty($value['video']) ) ? $value['video'] : NULL,
                    ':views'         => $value['views'],
                    ':item_description' => $value['description'],
                    ':id'            => $key
                ]);
            }

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function update_item_tyre($items_table, $items_array)
    {
        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";

        try{
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$items_table}
                 JOIN {$items_descriptions_table} ON {$items_table}.unique_code = {$items_descriptions_table}.unique_code
                 SET
                    {$items_table}.brand        = :brand,
                    {$items_table}.model_name   = :model_name,
                    {$items_table}.w            = :w,
                    {$items_table}.r            = :r,
                    {$items_table}.h            = :h,
                    {$items_table}.load_rate    = :load_rate,
                    {$items_table}.speed        = :speed,
                    {$items_table}.extra        = :extra,
                    {$items_table}.season       = :season,
                    {$items_table}.stock        = :stock,
                    {$items_table}.dealer       = :dealer,
                    {$items_table}.retail       = :retail,
                    {$items_table}.promo        = :promo,
                    {$items_table}.is_top       = :is_top,
                    {$items_table}.promotion_id = :promotion_id,
                    {$items_table}.description  = :description,
                    {$items_table}.video        = :video,
                    {$items_table}.views        = :views,
                    {$items_descriptions_table}.description = :item_description
                 WHERE {$items_table}.id = :id");

            foreach($items_array as $key => $value) {
                $statement->execute([
                    ':brand'        => ( !empty($value['brand']) ) ? $value['brand'] : NULL,
                    ':model_name'   => ( !empty($value['model_name']) ) ? $value['model_name'] : NULL,
                    ':w'            => ( !empty($value['w']) ) ? $value['w'] : NULL,
                    ':r'            => ( !empty($value['r']) ) ? $value['r'] : NULL,
                    ':h'            => ( !empty($value['h']) ) ? $value['h'] : NULL,
                    ':load_rate'    => ( !empty($value['load_rate']) ) ? $value['load_rate'] : NULL,
                    ':speed'        => ( !empty($value['speed']) ) ? $value['speed'] : NULL,
                    ':extra'        => ( !empty($value['extra']) ) ? $value['extra'] : NULL,
                    ':season'       => ( !empty($value['season']) ) ? $value['season'] : NULL,
                    ':stock'        => ( !empty($value['stock']) ) ? $value['stock'] : NULL,
                    ':dealer'       => ( !empty($value['dealer']) ) ? $value['dealer'] : NULL,
                    ':retail'       => ( !empty($value['retail']) ) ? $value['retail'] : NULL,
                    ':promo'        => ( !empty($value['promo']) ) ? $value['promo'] : NULL,
                    ':is_top'       => $value['is_top'],
                    ':promotion_id' => $value['promotion_id'],
                    ':description'  => $value['description'],
                    ':video'        => ( !empty($value['video']) ) ? $value['video'] : NULL,
                    ':views'        => $value['views'],
                    ':item_description' => $value['description'],
                    ':id'           => $key
                ]);
            }

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function rename_item_rim_image($item_data, $item_image)
    {
        $new_list = [];

        $item_image = str_replace("/", "[slash]", $item_image);

        #glob() square brackets hack
        $escapedBracketPath = str_replace('[', '\[', $item_image);
        $escapedBracketPath = str_replace(']', '\]', $escapedBracketPath);

        $escapedBracketPath = str_replace('\[', '[[]', $escapedBracketPath);
        $escapedBracketPath = str_replace('\]', '[]]', $escapedBracketPath);
        #END/glob() square brackets hack

        $list = glob("?".Setup::SUBSYSTEM_alpha."?/items/{$item_data['item_type']}/{$escapedBracketPath}{.,_thumb.,_{1,2,3}.,_thumb_{1,2,3}.}{jpg,jpeg,png}", GLOB_BRACE);

        $old_name = $item_image;
        $new_name = str_replace("/", "[slash]", "{$item_data['brand']}_{$item_data['model_name']}_{$item_data['code']}_{$item_data['paint']}");

        if( !empty($list) )
        {
            foreach ($list as $old_image)
            {
                $new_image = str_replace($old_name, $new_name, $old_image);

                rename($old_image, $new_image);

                if (strpos($new_image, 'thumb')) {
                    $thumbs_list[] = $item_data['item_type'] . "/" . basename($new_image);
                } else {
                    $images_list[] = $item_data['item_type'] . "/" . basename($new_image);
                }
            }

            natsort($images_list);
            natsort($thumbs_list);

            foreach ($images_list as $key => $value) {
                $new_list[$key]['image'] = $images_list[$key];
                $new_list[$key]['thumb'] = $thumbs_list[$key];
            }

            return $new_list;
        }

        return [];
    }

    public function rename_item_tyre_image($item_data, $item_image)
    {
        $new_list = [];

        $item_image = str_replace("/", "[slash]", $item_image);

        #glob() square brackets hack
        $escapedBracketPath = str_replace('[', '\[', $item_image);
        $escapedBracketPath = str_replace(']', '\]', $escapedBracketPath);

        $escapedBracketPath = str_replace('\[', '[[]', $escapedBracketPath);
        $escapedBracketPath = str_replace('\]', '[]]', $escapedBracketPath);
        #END/glob() square brackets hack

        $list = glob("?".Setup::SUBSYSTEM_alpha."?/items/{$item_data['item_type']}/{$escapedBracketPath}{.,_thumb.,_{1,2,3}.,_thumb_{1,2,3}.}{jpg,jpeg,png}", GLOB_BRACE);

        $old_name = $item_image;
        $new_name = str_replace("/", "[slash]", "{$item_data['brand']}_{$item_data['model_name']}");

        if( !empty($list) )
        {
            foreach ($list as $old_image) {
                $new_image = str_replace($old_name, $new_name, $old_image);

                rename($old_image, $new_image);

                if (strpos($new_image, 'thumb')) {
                    $thumbs_list[] = $item_data['item_type'] . "/" . basename($new_image);
                } else {
                    $images_list[] = $item_data['item_type'] . "/" . basename($new_image);
                }
            }

            natsort($images_list);
            natsort($thumbs_list);

            foreach ($images_list as $key => $value) {
                $new_list[$key]['image'] = $images_list[$key];
                $new_list[$key]['thumb'] = $thumbs_list[$key];
            }

            return $new_list;
        }

        return [];
    }

    public function delete_item_modification($items_table, $item_id)
    {
        try{
            $statement = $this->db_handler->data_object->prepare("DELETE FROM {$items_table} WHERE id = :id");
            $statement->execute(['id' => $item_id]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }
    }

    public function add_modification_rim($items_table, $item_parameters)
    {
        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT MAX(unique_code) AS max_unique_code FROM {$items_table}");
            $statement->execute();
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $max_unique_code = ++$statement_out[0]['max_unique_code'];

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$items_table} (unique_code, date_created, brand, model_name, code, paint, is_top) VALUES
                        (:unique_code, :date_created, :brand, :model_name, :code, :paint, :is_top)"
            );

            $statement->execute([
                ':unique_code' => $max_unique_code,
                ':date_created' => time(),
                ':brand' => $item_parameters['brand'],
                ':model_name' => $item_parameters['model_name'],
                ':code' => $item_parameters['code'],
                ':paint' => $item_parameters['paint'],
                ':is_top' => 'N'
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare("SELECT * FROM {$items_table} WHERE id = :id");
            $statement->execute([':id' => $last_id]);
            $modification = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $modification;
    }

    public function add_modification_tyre($items_table, $item_parameters)
    {
        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT MAX(unique_code) AS max_unique_code FROM {$items_table}");
            $statement->execute();
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $max_unique_code = ++$statement_out[0]['max_unique_code'];

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$items_table} (unique_code, date_created, brand, model_name, is_top) VALUES
                        (:unique_code, :date_created, :brand, :model_name, :is_top)"
            );

            $statement->execute([
                ':unique_code' => $max_unique_code,
                ':date_created' => time(),
                ':brand' => $item_parameters['brand'],
                ':model_name' => $item_parameters['model_name'],
                ':is_top' => 'N'
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare("SELECT * FROM {$items_table} WHERE id = :id");
            $statement->execute([':id' => $last_id]);
            $modification = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $modification;
    }

    public function delete_image($image_path)
    {
        $full_path = BASEPATH . "[".Setup::SUBSYSTEM_alpha."]/items/{$image_path}";

        if( file_exists($full_path) )
        {
            unlink($full_path);

            $full_path_thumb = str_replace('_thumb', '', $full_path);

            if( file_exists($full_path_thumb) )
                unlink($full_path_thumb);

            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_images($item_type, $item_image)
    {
        $list = glob("?".Setup::SUBSYSTEM_alpha."?/items/{$item_type}/{$item_image}{.,_thumb.,_{1,2,3}.,_thumb_{1,2,3}.}{jpg,jpeg,png}", GLOB_BRACE);

        if( !empty($list) )
        {
            foreach($list as $value) {
                unlink($value);
            }
        }
    }

    public function delete_item_rim($items_table, $item_id)
    {
        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT unique_code, brand, model_name, code, paint FROM {$items_table} WHERE id = :id");
            $statement->execute([':id' => $item_id]);
            $rim = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

            if( empty($rim['brand']) || empty($rim['model_name']) ) {
                $statement = $this->db_handler->data_object->prepare(
                    "DELETE FROM {$items_table} WHERE id = :id"
                );
                $statement->execute([
                    ':id' => $item_id
                ]);
            } else {
                if( $rim['code'] ) {
                    $statement = $this->db_handler->data_object->prepare(
                        "DELETE FROM {$items_table} WHERE brand = :brand AND model_name = :model_name AND code = :code AND paint = :paint"
                    );
                    $statement->execute([
                        ':brand'      => $rim['brand'],
                        ':model_name' => $rim['model_name'],
                        ':code'       => $rim['code'],
                        ':paint'      => $rim['paint']
                    ]);
                } else {
                    $statement = $this->db_handler->data_object->prepare(
                        "DELETE FROM {$items_table} WHERE brand = :brand AND model_name = :model_name AND paint = :paint"
                    );
                    $statement->execute([
                        ':brand'      => $rim['brand'],
                        ':model_name' => $rim['model_name'],
                        ':paint'      => $rim['paint']
                    ]);
                }
            }

            $statement = $this->db_handler->data_object->prepare("DELETE FROM {$items_descriptions_table} WHERE unique_code = :unique_code");
            $statement->execute([':unique_code' => $rim['unique_code']]);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }
    }

    public function delete_item_tyre($items_table, $item_id)
    {
        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT unique_code, brand, model_name FROM {$items_table} WHERE id = :id");
            $statement->execute([':id' => $item_id]);
            $tyre = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

            if( empty($tyre['brand']) || empty($tyre['model_name']) ) {
                $statement = $this->db_handler->data_object->prepare(
                    "DELETE FROM {$items_table} WHERE id = :id"
                );
                $statement->execute([
                    ':id' => $item_id
                ]);
            } else {
                $statement = $this->db_handler->data_object->prepare(
                    "DELETE FROM {$items_table} WHERE brand = :brand AND model_name = :model_name"
                );
                $statement->execute([
                    ':brand'      => $tyre['brand'],
                    ':model_name' => $tyre['model_name']
                ]);
            }

            $statement = $this->db_handler->data_object->prepare("DELETE FROM {$items_descriptions_table} WHERE unique_code = :unique_code");
            $statement->execute([':unique_code' => $tyre['unique_code']]);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }
    }

    public function add_catalog_item($item_type, $item_table)
    {
        $items_descriptions_table = Setup::DB_PREFIX_alpha."items_descriptions";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT MAX(unique_code) AS max_unique_code FROM {$item_table}");
            $statement->execute();
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $max_unique_code = ++$statement_out[0]['max_unique_code'];

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$item_table} (unique_code, date_created, is_top, views) VALUES
                        (:unique_code, :date_created, :is_top, :views)"
            );

            $statement->execute([
                ':unique_code'  => $max_unique_code,
                ':date_created' => time(),
                ':is_top'       => 'N',
                ':views'        => 0,
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT IGNORE INTO {$items_descriptions_table}
                        (unique_code, type) VALUES
                        (:unique_code, :type)"
            );
            $statement->execute([
                ':unique_code' => $max_unique_code,
                ':type'        => $item_type
            ]);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $last_id;
    }

    /*
     * NEWS
     */

    public function update_news_article($filename, $news_data)
    {
        $news_table         = Setup::DB_PREFIX_alpha."news";
        $news_table_content = Setup::DB_PREFIX_alpha."news_content";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$news_table}
                 LEFT JOIN {$news_table_content}
                    ON {$news_table}.id = {$news_table_content}.parent_id
                 SET
                    {$news_table}.image         = COALESCE(NULLIF(:image, ''),image),
                    {$news_table}.image_thumb   = COALESCE(NULLIF(:image_thumb, ''),image_thumb),
                    {$news_table_content}.title = :title,
                    {$news_table_content}.text  = :text
                 WHERE {$news_table}.id = :id");

            $statement->execute([
                ':image'       => $filename['image'],
                ':image_thumb' => $filename['image_thumb'],
                ':title'       => $news_data['title'],
                ':text'        => $news_data['text'],
                ':id'          => $news_data['news_id']
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function set_news_default_image($news_id, $filename)
    {
        $news_table = Setup::DB_PREFIX_alpha."news";

        try {
            $statement = $this->db_handler->data_object->prepare("UPDATE {$news_table} SET image = :image, image_thumb = :image_thumb WHERE id = :id");
            $statement->execute([
                ':image'       => $filename['image'],
                ':image_thumb' => $filename['image_thumb'],
                ':id'          => $news_id
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function delete_news($news_id)
    {
        $news_table         = Setup::DB_PREFIX_alpha."news";
        $news_table_content = Setup::DB_PREFIX_alpha."news_content";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "DELETE {$news_table}, {$news_table_content}
                 FROM {$news_table}
                 LEFT JOIN {$news_table_content}
                    ON {$news_table}.id = {$news_table_content}.parent_id
                 WHERE {$news_table}.id = :id"
            );
            $statement->execute([
                ':id' => $news_id
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function add_news($filename)
    {
        $news_table         = Setup::DB_PREFIX_alpha."news";
        $news_table_content = Setup::DB_PREFIX_alpha."news_content";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$news_table} (date_created, image, image_thumb)
                 VALUES (:date_created, :image, :image_thumb)"
            );
            $statement->execute([
                ':date_created' => time(),
                ':image'        => $filename['image'],
                ':image_thumb'  => $filename['image_thumb']
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$news_table_content} (parent_id, language)
                 VALUES (:parent_id, :language)"
            );
            $statement->execute([
                ':parent_id' => $last_id,
                ':language'  => 'ru'
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $last_id;
    }

    /*
     * SPARES
     */

    public function update_spares($spares_data)
    {
        $spares_table = Setup::DB_PREFIX_alpha."items_spares";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$spares_table} SET brand = :brand, item_specs = :item_specs, size = :size, retail = :retail WHERE id = :id"
            );

            foreach($spares_data as $key => $value)
            {
                $statement->execute([
                    ':brand'      => ( !empty($value['brand']) ) ? $value['brand'] : NULL,
                    ':item_specs' => ( !empty($value['item_specs']) ) ? $value['item_specs'] : NULL,
                    ':size'       => ( !empty($value['size']) ) ? $value['size'] : NULL,
                    ':retail'     => $value['retail'],
                    ':id'         => $key
                ]);
            }

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function add_spares($category)
    {
        $spares_table = Setup::DB_PREFIX_alpha."items_spares";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("SELECT MAX(unique_code) AS max_unique_code FROM {$spares_table} WHERE type = :type");
            $statement->execute([':type' => $category]);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $max_unique_code = ++$statement_out[0]['max_unique_code'];

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$spares_table} (unique_code, type) VALUES
                        (:unique_code, :type)"
            );

            $statement->execute([
                ':unique_code' => $max_unique_code,
                ':type'    => $category
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare("SELECT * FROM {$spares_table} WHERE id = :id");
            $statement->execute([':id' => $last_id]);
            $spare = $statement->fetchAll(PDO::FETCH_ASSOC)[0];

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $spare;
    }

    public function delete_spares($spare_id)
    {
        $spares_table = Setup::DB_PREFIX_alpha."items_spares";

        try {
            $statement = $this->db_handler->data_object->prepare("DELETE FROM {$spares_table} WHERE id = :id");
            $statement->execute([
                ':id' => $spare_id
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    /*
     * PROMO
     */

    public function get_max_promo_id()
    {
        $special_offers_table = Setup::DB_PREFIX_alpha."special_offers";

        try {
            $statement = $this->db_handler->data_object->prepare("SELECT MAX(id) AS max_id FROM {$special_offers_table}");
            $statement->execute();
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);

            $max_id = $statement_out[0]['max_id'];
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return $max_id;
    }

    public function add_promotions($filename)
    {
        $special_offers_table         = Setup::DB_PREFIX_alpha."special_offers";
        $special_offers_content_table = Setup::DB_PREFIX_alpha."special_offers_content";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("INSERT INTO {$special_offers_table} (record_order) VALUES(:record_order)");
            $statement->execute([
                ':record_order' => 0
            ]);

            $last_id = $this->db_handler->data_object->lastInsertId();

            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$special_offers_content_table} (parent_id, language, image)
                 VALUES(:parent_id, :language, :image)"
            );
            $statement->execute([
                ':parent_id' => $last_id,
                ':language'  => 'ru',
                ':image'     => $filename
            ]);

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function get_new_promo($filenames)
    {
        $special_offers_table         = Setup::DB_PREFIX_alpha."special_offers";
        $special_offers_content_table = Setup::DB_PREFIX_alpha."special_offers_content";

        $query_string = str_repeat("?,", (count($filenames) - 1)) . "?";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "SELECT {$special_offers_table}.id, {$special_offers_table}.record_order, {$special_offers_content_table}.image
                 FROM {$special_offers_table} LEFT JOIN {$special_offers_content_table}
                    ON {$special_offers_table}.id = {$special_offers_content_table}.parent_id
                 WHERE {$special_offers_content_table}.image IN ({$query_string})"
            );
            $statement->execute($filenames);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( !empty($statement_out) ) ? $statement_out : FALSE;
    }

    public function delete_promo($promo_id)
    {
        $special_offers_table         = Setup::DB_PREFIX_alpha."special_offers";
        $special_offers_content_table = Setup::DB_PREFIX_alpha."special_offers_content";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "DELETE {$special_offers_table}, {$special_offers_content_table}
                 FROM {$special_offers_table}
                 LEFT JOIN {$special_offers_content_table}
                    ON {$special_offers_table}.id = {$special_offers_content_table}.parent_id
                 WHERE {$special_offers_table}.id = :id"
            );
            $statement->execute([
                ':id' => $promo_id
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    public function update_promo($promo_data)
    {
        $special_offers_table         = Setup::DB_PREFIX_alpha."special_offers";
        $special_offers_content_table = Setup::DB_PREFIX_alpha."special_offers_content";

        try {
            $this->db_handler->data_object->beginTransaction();

            $statement = $this->db_handler->data_object->prepare("
                 UPDATE {$special_offers_table}
                 LEFT JOIN {$special_offers_content_table}
                    ON {$special_offers_table}.id = {$special_offers_content_table}.parent_id
                 SET
                    {$special_offers_table}.end_date = :end_date,
                    {$special_offers_table}.hashtag = :hashtag,
                    {$special_offers_content_table}.description  = :description
                 WHERE {$special_offers_table}.id = :id
                 ");

            foreach($promo_data as $id => $promo_item)
            {
                $statement->execute([
                    ':end_date'    => ( !empty($promo_item['end_date']) ) ? strtotime($promo_item['end_date']) : 0,
                    ':hashtag'     => ( !empty($promo_item['hashtag']) ) ? $promo_item['hashtag'] : NULL,
                    ':description' => ( !empty($promo_item['description']) ) ? $promo_item['description'] : NULL,
                    ':id'          => $id
                ]);
            }

            $this->db_handler->data_object->commit();
        } catch(PDOException $PDOEX) {
            $this->db_handler->data_object->rollBack();
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }
}
?>
