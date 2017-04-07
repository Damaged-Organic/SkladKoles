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
    case 'news_update':
        if( ($news_id = $_BOOT->involve_object("InputPurifier")->purge_integer($_AREA->{$C_E::_REQUEST}['news_id'])) === FALSE ) {
            $this->cancel_ajax_request();
        } elseif( !$_BOOT->involve_object("DB_Handler")->is_value_exists($C_S::DB_PREFIX_alpha.'news', 'id', $news_id) ) {
            $this->cancel_ajax_request();
        }

        if( !empty($_FILES['file']) ) {
            $filename = $this->load_inter('worker', 'process_files_news', [$_FILES['file'], $news_id]);
        } else {
            $filename = ['image' => NULL, 'image_thumb' => NULL];
        }

        if( !$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->update_news_article($filename, $_AREA->{$C_E::_REQUEST}) ) {
            $response["message"] = "<p>Ошибка при обновлении данных</p>";
            $this->satisfy_ajax_request(json_encode($response));
        } else {
            $response["message"] = "<p>Данные успешно обновлены</p>";
            $this->satisfy_ajax_request(json_encode($response));
        }
    break;

    case 'delete_image':
        $filename = [
            'image'       => 'news_default.jpg',
            'image_thumb' => 'news_default_thumb.jpg'
        ];

        if( in_array(basename($_AREA->{$C_E::_REQUEST}['news_image']), $filename, TRUE) ) {
            $this->cancel_ajax_request();
        }

        if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_image($_AREA->{$C_E::_REQUEST}['news_image']) ) {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->set_news_default_image($_AREA->{$C_E::_REQUEST}['news_id'], $filename);
            $this->satisfy_ajax_request(
                $this->load_resource("news/news_default.jpg", TRUE)
            );
        } else {
            $this->cancel_ajax_request();
        }
    break;

    case 'item_delete':
        $filename = [
            'image'       => 'news_default.jpg',
            'image_thumb' => 'news_default_thumb.jpg'
        ];

        $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_news($_AREA->{$C_E::_REQUEST}['news_id']);

        if( !in_array(basename($_AREA->{$C_E::_REQUEST}['news_image']), $filename, TRUE) ) {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_image($_AREA->{$C_E::_REQUEST}['news_image']);
        }

        $this->satisfy_ajax_request($C_S::SUBSYSTEM_alpha_link."/news");
    break;

    default:
        $this->cancel_ajax_request();
    break;
}