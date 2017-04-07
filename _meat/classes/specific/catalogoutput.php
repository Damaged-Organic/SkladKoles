<?php
namespace _meat\classes\specific;

use coreException, procException;

use _bone\system\constants\constantsSetup
    as Setup,
    _bone\classes\containers\constantsNamespaces
    as Namespaces;

use _bone\system\DEM_Settings
    as Settings;

use _meat\classes\common\DB_Handler, PDO;

class CatalogOutput
{
    private $db_handler    = NULL;
    private $currency_rate = NULL;

    public function __construct()
    {
        $this->db_handler = Settings::assign_bootLoader()->assign_namespace(Namespaces::MEAT_COMMON)->involve_object("DB_Handler");
    }

    public function get_currency_rate()
    {
        if( empty($this->currency_rate) ) {
            $currency_rates_table = Setup::DB_PREFIX_alpha."currency_rates";

            try {
                $statement = $this->db_handler->data_object->prepare(
                    "SELECT rate FROM {$currency_rates_table} WHERE currency = :currency"
                );
                $statement->execute([
                    ':currency' => 'USD'
                ]);
                $currency_rate = $statement->fetch(PDO::FETCH_ASSOC);

                return (float)$currency_rate['rate'];
            } catch(PDOException $PDOEX) {
                throw new CoreException("PDOException: {$PDOEX->getMessage()}");
            }
        } else {
            return $this->currency_rate;
        }
    }

    public function set_currency_rate($input_rate)
    {
        $currency_rates_table = Setup::DB_PREFIX_alpha."currency_rates";

        try {
            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$currency_rates_table} SET rate = :rate WHERE currency = :currency"
            );
            $statement->execute([
                ':rate'     => $input_rate,
                ':currency' => 'USD'
            ]);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return TRUE;
    }

    private function round_down($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }

    public function convert_item_price($price, $convert_back = FALSE, $no_format = FALSE)
    {
        if( $convert_back ) {
            $converted_price = number_format($price/$this->get_currency_rate(), 2);
        } else {
            if( $no_format ) {
                $converted_price = $this->round_down($price*$this->get_currency_rate(), 0);
            } else {
                $converted_price = number_format($this->round_down($price*$this->get_currency_rate(), 0), 2, '.', ',');
            }
        }

        return $converted_price;
    }

    public function get_item_image($item, $single_thumb = TRUE, $return_default = TRUE)
    {
        switch($item['type'])
        {
            case 'rims':
                $folder  = 'rims';

                $filename = str_replace("/", "[slash]", "{$item['brand']}_{$item['model_name']}_{$item['code']}_{$item['paint']}");

                $default = [['image' => "no-photo-rims.jpg", 'thumb' => "no-photo-rims_thumb.jpg"]];
            break;

            case 'exclusive_rims':
                $folder  = 'exclusive_rims';

                $filename = str_replace("/", "[slash]", "{$item['brand']}_{$item['model_name']}_{$item['code']}_{$item['paint']}");

                $default = [['image' => "no-photo-rims.jpg", 'thumb' => "no-photo-rims_thumb.jpg"]];
            break;

            case 'tyres':
                $folder  = 'tyres';

                $filename = str_replace("/", "[slash]", "{$item['brand']}_{$item['model_name']}");

                $default = [['image' => "no-photo-tires.jpg", 'thumb' => "no-photo-tires_thumb.jpg"]];
            break;

            case 'exclusive_tyres':
                $folder  = 'exclusive_tyres';

                $filename = str_replace("/", "[slash]", "{$item['brand']}_{$item['model_name']}");

                $default = [['image' => "no-photo-tires.jpg", 'thumb' => "no-photo-tires_thumb.jpg"]];
            break;

            case 'spares':
                $folder  = 'spares';
                $image   = $item['item_type'];
                $default = '';

                return "{$folder}/{$image}.jpg";
            break;
        }

        #glob() square brackets hack
        $escapedBracketPath = str_replace('[', '\[', $filename);
        $escapedBracketPath = str_replace(']', '\]', $escapedBracketPath);

        $escapedBracketPath = str_replace('\[', '[[]', $escapedBracketPath);
        $escapedBracketPath = str_replace('\]', '[]]', $escapedBracketPath);
        #END/glob() square brackets hack

        $list = glob("?".Setup::SUBSYSTEM_alpha."?/items/{$folder}/{$escapedBracketPath}{.,_thumb.,_{1,2,3}.,_thumb_{1,2,3}.}{jpg,jpeg,png}", GLOB_BRACE);

        if( empty($list) ) {
            if( $return_default )
                return ( $single_thumb ) ? $default[0]['thumb'] : $default;
            else
                return FALSE;
        }

        foreach($list as $value)
        {
            if( strpos($value, 'thumb') ) {
                $thumbs_list[] = $folder . "/" . basename($value);
            } else {
                $images_list[] = $folder . "/" . basename($value);
            }
        }

        natsort($images_list);
        natsort($thumbs_list);

        foreach($images_list as $key => $value) {
            $new_list[$key]['image'] = $images_list[$key];
            $new_list[$key]['thumb'] = $thumbs_list[$key];
        }

        return ( $single_thumb ) ? $new_list[0]['thumb'] : $new_list;
    }

    public function provide_item_type($type)
    {
        if( !in_array($type, ['rims', 'exclusive_rims', 'tyres', 'exclusive_tyres'], TRUE) ) {
            throw new procException("Wrong item type");
        } else {
            return ['type' => $type];
        }
    }
}
