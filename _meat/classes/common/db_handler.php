<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException, PDOException;

use PDO;

class DB_Handler
{
    public $data_object;

    function __construct()
    {
        $this->open_connection();
        register_shutdown_function(
            array($this, "close_connection")
        );
    }

    # ----------------------------------------------------------------------------------------------------
    # |1.0| - connection                                                                                 |
    # ----------------------------------------------------------------------------------------------------

    public function open_connection()
    {
        if( $this->data_object !== NULL ) {
            throw new coreException("Connection already established");
        }

        try {
            $this->data_object = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME."; charset=".DB_CSET, DB_USER, DB_PASS);
            $this->data_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->data_object->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }
    }

    public function close_connection()
    {
        if( $this->data_object === NULL ) {
            throw new coreException("Connection is not established");
        }

        $this->data_object = NULL;
    }

    # ----------------------------------------------------------------------------------------------------
    # |2.0| - tables validation                                                                          |
    # ----------------------------------------------------------------------------------------------------

    public function validate_tables($tables)
    {
        if( $this->data_object === NULL ) {
            throw new coreException("Connection is not established");
        }

        $execute_array = array();

        if( is_array($tables) ) {
            $expected_tables = count($tables);
            $tables_string   = str_repeat('?,', (count($tables)-1))."?";
            $execute_array   = $tables;
        } else {
            $expected_tables = 1;
            $tables_string   = '?';
            $execute_array[] = $tables;
        }

        try {
            $statement = $this->data_object->prepare("SELECT COUNT(*)
													  FROM information_schema.tables
													  WHERE table_schema = '" . DB_NAME . "' AND table_name IN ({$tables_string})");

            $statement->execute($execute_array);
            $validated_tables = $statement->fetch(PDO::FETCH_COLUMN, 0);
            $statement->closeCursor();

            if( (int)$validated_tables === $expected_tables ) {
                return TRUE;
            }
        } catch(PDOException $PDOEX) {
            throw new coreException("PDOException: {$PDOEX->getMessage()}");
        }

        return FALSE;
    }

    public function get_records_number($table)
    {
        return ( $this->validate_tables($table) ) ? $this->data_object->query("SELECT COUNT(*) FROM {$table}")->fetchColumn() : FALSE;
    }

    public function is_value_exists($table, $field, $value)
    {
        $items_number = NULL;
        //bad function! direct insert of $field
        //expects to receive valid $value, but still running a prepared statement
        if( $this->validate_tables($table) )
        {
            try {
                $statement = $this->data_object->prepare("SELECT COUNT(*) FROM {$table} WHERE {$field} = :{$field}");
                $statement->execute(array(":{$field}" => $value));

                $items_number = $statement->fetch(PDO::FETCH_COLUMN, 0);
            } catch(PDOException $PDOEX) {
                throw new coreException("PDOException: {$PDOEX->getMessage()}");
            }
        }

        return ($items_number != 0) ? TRUE : FALSE;
    }
}
?>