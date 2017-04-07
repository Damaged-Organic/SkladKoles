<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$db_handler = $_BOOT->involve_object("DB_Handler");

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0) ) {
    $this->cancel_ajax_request();
}

switch($_AREA->{$C_AR::AR_METHOD})
{
    case 'spares_update':
        if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->update_spares($_AREA->{$C_E::_REQUEST}) ) {
            $this->satisfy_ajax_request("<p>Данные успешно обновлены</p>");
        } else {
            $this->cancel_ajax_request("<p>Произошла ошибка</p>");
        }
    break;

    case 'spares_add':
        if( !in_array($_AREA->{$C_E::_REQUEST}['category'], ['rings', 'bolts', 'nuts', 'locks', 'logos'], TRUE) ) {
            $this->cancel_ajax_request();
        }

        if( $spare = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->add_spares($_AREA->{$C_E::_REQUEST}['category']) )
        {
            $delete_landmark = [
                $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
                $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
                $C_AR::AR_LOCATION => "process_spares",
                $C_AR::AR_METHOD   => "spares_delete",
                '_request[spare_id]' => NULL
            ];

            $interlayer["interlayer"] = $this->load_inter('layer', 'single_item_spare', [$spare, $delete_landmark]);

            if( $interlayer["interlayer"] ) {
                $this->satisfy_ajax_request(json_encode($interlayer));
            } else {
                $this->cancel_ajax_request();
            }
        } else {
            $this->cancel_ajax_request("<p>Произошла ошибка</p>");
        }
    break;

    case 'spares_delete':
        if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_spares($_AREA->{$C_E::_REQUEST}['spare_id']) ) {
            $this->satisfy_ajax_request();
        } else {
            $this->cancel_ajax_request("<p>Произошла ошибка</p>");
        }
    break;

    default:
        $this->cancel_ajax_request();
    break;
}
?>