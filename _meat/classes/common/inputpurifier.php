<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class InputPurifier
{
    private function check_expected_type($expected_type, $input_variable)
    {
        return (gettype($input_variable) == $expected_type) ? TRUE : FALSE;
    }

    private function trim_input_variable(&$input_variable)
    {
        if( is_array($input_variable) ) {
            foreach($input_variable as $value) {
                $this->trim_input_variable($value);
            }
        } else {
            $input_variable = trim($input_variable);
        }
    }

    public function purge_string($input_string)
    {
        if( !$this->check_expected_type('string', $input_string) ) {
            return FALSE;
        }

        $this->trim_input_variable($input_string);

        $input_options = array(
            'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_NO_ENCODE_QUOTES
        );

        $sanitized_string = filter_var($input_string, FILTER_SANITIZE_STRING, $input_options);

        if( empty($sanitized_string) ) {
            return FALSE;
        }

        return $sanitized_string;
    }

    public function purge_integer($input_integer)
    {
        if( !$this->check_expected_type('integer', (integer)$input_integer) ) {
            return FALSE;
        }

        $this->trim_input_variable($input_integer);

        $sanitized_integer = filter_var($input_integer, FILTER_SANITIZE_NUMBER_INT);

        if( is_null($sanitized_integer) ) {
            return FALSE;
        }

        if( filter_var($sanitized_integer, FILTER_VALIDATE_INT) === FALSE ) {
            return FALSE;
        }

        return $sanitized_integer;
    }

    public function purge_float($input_float)
    {
        if( !$this->check_expected_type('double', (float)$input_float) ) {
            return FALSE;
        }

        $this->trim_input_variable($input_float);

        $sanitized_float = filter_var($input_float, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if( empty($sanitized_float) ) {
            return FALSE;
        }

        if( !filter_var($sanitized_float, FILTER_VALIDATE_FLOAT) ) {
            return FALSE;
        }

        return $sanitized_float;
    }

    public function purge_email($input_email)
    {
        if( !$this->check_expected_type('string', $input_email) ) {
            return FALSE;
        }

        $this->trim_input_variable($input_email);

        $sanitized_email = filter_var($input_email, FILTER_SANITIZE_EMAIL);

        if( empty($sanitized_email) || ($sanitized_email !== $input_email) ) {
            return FALSE;
        }

        if( !filter_var($sanitized_email, FILTER_VALIDATE_EMAIL) ) {
            return FALSE;
        }

        return $sanitized_email;
    }

    public function purge_phone($input_phone)
    {
        if( !$this->check_expected_type('string', $input_phone) ) {
            return FALSE;
        }

        $this->trim_input_variable($input_phone);

        $input_options = array(
            'flags' => FILTER_FLAG_STRIP_LOW
        );

        $sanitized_phone = filter_var($input_phone, FILTER_SANITIZE_STRING, $input_options);

        $regexp_phone = '#^\+380\s\([0-9]{2}\)\s([0-9]{3})-([0-9]{2})-([0-9]{2})$#';

        if( empty($sanitized_phone) || ($sanitized_phone !== $input_phone) ) {
            return FALSE;
        }

        if( !preg_match($regexp_phone, $sanitized_phone) ) {
            return FALSE;
        }

        return $sanitized_phone;
    }
}
?>
