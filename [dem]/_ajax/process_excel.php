<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

ini_set('max_execution_time', 900);

$db_handler = $_BOOT->involve_object("DB_Handler");

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== 'excel_upload' && $_AREA->{$C_AR::AR_METHOD} !== 'excel_delete' ) {
    $this->cancel_ajax_request();
}

if( $_FILES['file'] || empty($_AREA->{$C_E::_REQUEST}['type']) )
{
    $mime_types = array(
        "application/zip",
        "application/excel",
        "application/x-excel",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/octet-stream"
    );

    $extensions = array(
        "xls",
        "xlsx"
    );

    $fileinfo = new finfo(FILEINFO_MIME_TYPE);

    foreach($_FILES['file']['name'] as $key => $value)
    {
        $mime = $fileinfo->file($_FILES['file']['tmp_name'][$key]);
        if( !in_array($mime, $mime_types, TRUE) ) {
            $this->satisfy_ajax_request("<p>Неверный формат файла</p>");
        }

        $input_extension_array = explode(".", $_FILES['file']['name'][$key]);
        if( !in_array(end($input_extension_array), $extensions, TRUE) ) {
            $this->satisfy_ajax_request("<p>Неверный формат файла</p>");
        }

        if( !in_array($_AREA->{$C_E::_REQUEST}['type'], ['rims', 'exclusive_rims', 'tyres', 'exclusive_tyres', 'spares'], TRUE) ) {
            $this->cancel_ajax_request("<p>Пожалуй, я не буду это заливать</p>");
        }

        if( $_AREA->{$C_E::_REQUEST}['submit'] === 'excel_upload' ) {
            $result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object(
                "CatalogInput",
                [$db_handler]
            )->update_catalog(
                [
                    'type' => $_AREA->{$C_E::_REQUEST}['type'],
                    'data' => $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPExcelLink")->read_file($_FILES['file']['name'][$key], $_FILES['file']['tmp_name'][$key])
                ]
            );

            if( !$result ) {
                $this->satisfy_ajax_request("<p>Произошла ошибка при обработке файла. Проверьте, соответствует ли он согласованному формату</p>");
            } else {
                $this->satisfy_ajax_request("<p>Данные загружены!</p>");
            }
        } elseif( $_AREA->{$C_E::_REQUEST}['submit'] === 'excel_upload_clean' ) {
            $result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object(
                "CatalogInput",
                [$db_handler]
            )->update_clean_catalog(
                [
                    'type' => $_AREA->{$C_E::_REQUEST}['type'],
                    'data' => $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPExcelLink")->read_file($_FILES['file']['name'][$key], $_FILES['file']['tmp_name'][$key])
                ]
            );

            if( !$result ) {
                $this->satisfy_ajax_request("<p>Произошла ошибка при обработке файла. Проверьте, соответствует ли он согласованному формату</p>");
            } else {
                $this->satisfy_ajax_request("<p>Данные синхронизированы!</p>");
            }
        } else {
            $this->satisfy_ajax_request("<p>Произошла ошибка при обработке запроса.</p>");
        }
    }
} elseif( $_AREA->{$C_AR::AR_METHOD} === 'excel_delete' ) {
    if( !in_array($_AREA->{$C_E::_REQUEST}['type'], ['rims', 'exclusive_rims', 'tyres', 'exclusive_tyres', 'spares'], TRUE) ) {
        $this->cancel_ajax_request("<p>Пожалуй, я не буду очищать эту таблицу</p>");
    }

    $result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
        ->involve_object(
            "CatalogInput", [$db_handler]
        )
        ->delete_catalog($_AREA->{$C_E::_REQUEST}['type'])
    ;

    if( !$result ) {
        $this->satisfy_ajax_request("<p>Произошла ошибка при очистке таблицы</p>");
    } else {
        $this->satisfy_ajax_request("<p>Таблица очищена!</p>");
    }
} else {
    $this->cancel_ajax_request();
}
?>
