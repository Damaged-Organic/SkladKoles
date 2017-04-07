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
    case 'add_promotions':
        $next_max_id = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->get_max_promo_id();

        if( !empty($_FILES['file']) ) {
            $filenames = $this->load_inter('worker', 'process_files_promo', [$_FILES['file'], ++$next_max_id]);
        } else {
            $this->satisfy_ajax_request("<p>Произошла ошибка</p>");
        }

        if( empty($filenames) ) {
            $this->satisfy_ajax_request("<p>Произошла ошибка</p>");
        }

        foreach($filenames as $filename) {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->add_promotions($filename);
        }

        $new_promo_data = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->get_new_promo($filenames);

        if( !$new_promo_data ) {
            $this->satisfy_ajax_request("<p>Произошла ошибка</p>");
        } else {
            $delete_landmark = [
                $_IC::IC_TOKEN      => $_SESSION['IC_token']['hash'],
                $C_AR::AR_ORIGIN    => $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE}),
                $C_AR::AR_LOCATION  => "promotions",
                $C_AR::AR_METHOD    => "delete_promotions",
                '_request[promo_id]'    => NULL,
                '_request[promo_image]' => NULL
            ];

            $interlayer["interlayer"] = $this->load_inter('layer', 'separate_promotions', [$delete_landmark, $new_promo_data]);
            $interlayer["message"]    = "<p>Данные успешно сохранены</p>";

            if( $interlayer["interlayer"] ) {
                $this->satisfy_ajax_request(json_encode($interlayer));
            } else {
                $this->satisfy_ajax_request("<p>Произошла ошибка</p>");
            }
        }
    break;

    case 'delete_promotions':
        if( empty($_AREA->{$C_E::_REQUEST}['promo_id']) ) {
            $this->cancel_ajax_request();
        } elseif( ($promo_id = $_BOOT->involve_object("InputPurifier")->purge_integer($_AREA->{$C_E::_REQUEST}['promo_id'])) === FALSE ) {
            $this->cancel_ajax_request();
        } elseif( !$_BOOT->involve_object("DB_Handler")->is_value_exists($C_S::DB_PREFIX_alpha.'special_offers', 'id', $promo_id) ) {
            $this->cancel_ajax_request();
        }

        if( empty($_AREA->{$C_E::_REQUEST}['promo_image']) ) {
            $this->cancel_ajax_request();
        }

        if(
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_promo($promo_id) &&
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_image($_AREA->{$C_E::_REQUEST}['promo_image']) ) {
            $this->satisfy_ajax_request();
        }
    break;

    case 'save_promotions':
        $result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->update_promo($_AREA->{$C_E::_REQUEST});

        if( !$result ) {
            $this->satisfy_ajax_request(json_encode(['message' => "<p>Произошла ошибка</p>"]));
        } else {
            $this->satisfy_ajax_request(json_encode(['message' => "<p>Данные успешно сохранены</p>"]));
        }
    break;

    default:
        $this->cancel_ajax_request();
    break;
}
?>