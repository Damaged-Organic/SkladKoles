<?php
namespace _meat\classes\external;

use coreException;

use _bone\system\constants\constantsSetup
    as Setup;

use _meat\classes\common\DB_Handler, PDO;

class PHPMailerLink
{
    private $mailer_instance;

    private $db_handler   = NULL;
    private $emails_table = NULL;

    function __construct(DB_Handler $db_handler)
    {
        require_once dirname(__FILE__) . '/PHPMailer/PHPMailerAutoload.php';

        $this->mailer_instance = new \PHPMailer;

        $this->db_handler   = $db_handler;
        $this->emails_table = Setup::DB_PREFIX_alpha."emails";
    }

    public function send_mail($email_data)
    {
        try {
            //Clear class property so no previous address will be used to send mail
            $this->mailer_instance->ClearAllRecipients();

            $this->mailer_instance->CharSet = "UTF-8";

            $this->mailer_instance->setFrom($email_data['set_from']['email'], $email_data['set_from']['full_name']);
            $this->mailer_instance->addReplyTo($email_data['add_reply_to']['email'], $email_data['add_reply_to']['full_name']);

            foreach($email_data['add_address'] as $add_address) {
                $this->mailer_instance->addAddress($add_address['email'], $add_address['full_name']);
            }

            $this->mailer_instance->Subject = $email_data['subject'];
            $this->mailer_instance->msgHTML($email_data['msg_HTML']);
            $this->mailer_instance->altBody = $email_data['alt_body'];

            if( !empty($email_data['attachment']) ) {
                foreach($email_data['attachment'] as $file) {
                    $this->mailer_instance->addAttachment($file, explode('_', basename($file))[2]);
                }
            }

            return ( $this->mailer_instance->send() ) ? TRUE : FALSE;
        } catch (phpmailerException $PHPMEX) {
            throw new CoreException("phpmailerException: {$PHPMEX->errorMessage()}");
        }
    }

    public function process_email($email)
    {
        $processed = FALSE;

        if( ($result = $this->is_email_exists($email)) === FALSE ) {
            $processed = $this->insert_email($email);
        } else {
            if( !$this->is_email_active($result) ) {
                $processed = $this->force_email_active($email);
            } else {
                $processed = TRUE;
            }
        }

        return $processed;
    }

    private function is_email_exists($email)
    {
        try{
            $statement = $this->db_handler->data_object->prepare(
                "SELECT is_active FROM {$this->emails_table} WHERE email = :email"
            );

            $statement->execute([':email' => $email]);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( $statement->rowCount() > 0 ) ? $statement_out[0] : FALSE;
    }

    private function is_email_active($result)
    {
        return ( $result['is_active'] === 'Y' ) ? TRUE : FALSE;
    }

    private function insert_email($email)
    {
        try{
            $statement = $this->db_handler->data_object->prepare(
                "INSERT INTO {$this->emails_table} (email, is_active) VALUES (:email, :is_active)"
            );

            $statement->execute([':email' => $email, ':is_active' => 'Y']);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( $statement ) ? TRUE : FALSE;
    }

    private function force_email_active($email)
    {
        try{
            $statement = $this->db_handler->data_object->prepare(
                "UPDATE {$this->emails_table} SET is_active = :is_active WHERE email = :email"
            );

            $statement->execute([':is_active' => 'Y', ':email' => $email]);
            $statement_out = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $PDOEX) {
            throw new CoreException("PDOException: {$PDOEX->getMessage()}");
        }

        return ( $statement ) ? TRUE : FALSE;
    }
}
?>