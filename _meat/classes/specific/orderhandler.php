<?php
namespace _meat\classes\specific;

use coreException, procException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler, PDO;

class OrderHandler
{
    private $db_handler   = NULL;
    private $orders_table = NULL;

    function __construct(DB_Handler $db_handler)
    {
        $this->db_handler   = $db_handler;
        $this->orders_table = Setup::DB_PREFIX_alpha."orders";
    }

    public function generate_order_code()
    {
        $order_code = mt_rand(100000, 999999);

        return substr($order_code, 0, 3) . "-" . substr($order_code, 3);
    }

    public function insert_order($order_data, $cart_items, $counted_cart_items, $order_id)
    {
        $user_address = NULL;

        $order_data = [
            ':order_code'         => $order_id,
            ':order_date'         => time(),
            ':user_name'          => $order_data['userName'],
            ':user_email'         => $order_data['userEmail'],
            ':user_phone'         => $order_data['userPhone'],
            ':user_address'       => $user_address,
            ':items_json_encoded' => json_encode($cart_items, JSON_UNESCAPED_SLASHES),
            ':total_quantity'     => $counted_cart_items['quantity'],
            ':total_price'        => $counted_cart_items['price']
        ];

        if( $this->insert_statement($order_data) ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function insert_statement($order_data)
    {
        try{
            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$this->orders_table}
                            (order_code, order_date, user_name, user_email, user_phone, user_address, items_json_encoded, total_quantity, total_price)
                            VALUES
                            (:order_code, :order_date, :user_name, :user_email, :user_phone, :user_address, :items_json_encoded, :total_quantity, :total_price)"
            );

            $statement->execute($order_data);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( $statement ) ? TRUE : FALSE;
    }
}
?>
