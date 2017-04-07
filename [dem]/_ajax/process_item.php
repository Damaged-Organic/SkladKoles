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

switch($_AREA->{$C_E::_REQUEST}['item_type'])
{
    case 'rims':
        $items_table  = $C_S::DB_PREFIX_alpha."items_rims";
    break;

    case 'exclusive_rims':
        $items_table = $C_S::DB_PREFIX_alpha."items_rims_exclusive";
    break;

    case 'tyres':
        $items_table = $C_S::DB_PREFIX_alpha."items_tyres";
    break;

    case 'exclusive_tyres':
        $items_table = $C_S::DB_PREFIX_alpha."items_tyres_exclusive";
    break;

    default:
        $this->cancel_ajax_request();
    break;
}

switch($_AREA->{$C_AR::AR_METHOD})
{
    case 'item_update':
        $items_array = [];

        if( isset($_AREA->{$C_E::_REQUEST}['reload']) ) {
            $response["reload"] = TRUE;
            $response["link"]   = $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE});
        } else {
            $response["reload"] = FALSE;
            $response["link"]   = NULL;
        }

        if( $_AREA->{$C_E::_REQUEST}['item_type'] == 'rims' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_rims' )
        {
            if( ($_AREA->{$C_E::_REQUEST}['brand'] = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['brand'])) === FALSE ||
                ($_AREA->{$C_E::_REQUEST}['model_name'] === "" && $_AREA->{$C_E::_REQUEST}['code'] === "") ||
                ($_AREA->{$C_E::_REQUEST}['paint'] === "")) {
                $response["reload"]  = FALSE;
                $response["message"] = "<p>Ошибка при обновлении данных</p>";

                $this->satisfy_ajax_request(json_encode($response));
            }
        } elseif( $_AREA->{$C_E::_REQUEST}['item_type'] == 'tyres' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_tyres' ) {
            if( ($_AREA->{$C_E::_REQUEST}['brand'] = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['brand'])) === FALSE ||
                ($_AREA->{$C_E::_REQUEST}['model_name'] === "") ) {
                $response["reload"]  = FALSE;
                $response["message"] = "<p>Ошибка при обновлении данных</p>";

                $this->satisfy_ajax_request(json_encode($response));
            }
        }

        #UPLOAD FILES
        if( !empty($_FILES['file']['name'][0]) ) {
            //print_r($_FILES);
            $this->load_inter('worker', 'process_files', [$_AREA->{$C_E::_REQUEST}['item_type'], $_FILES['file'], $_AREA->{$C_E::_REQUEST}]);
        }

        #DELETE FILES
        if( !empty($_AREA->{$C_E::_REQUEST}['delete_images']) ) {
            foreach( $_AREA->{$C_E::_REQUEST}['delete_images'] as $value ) {
                $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_image($value);
            }
        }

        if($_AREA->{$C_E::_REQUEST}['item_type'] == 'rims' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_rims') {
            #UPDATE RIMS
            if( !empty($_AREA->{$C_E::_REQUEST}['item_image']) ) {
                $images_list = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])
                    ->rename_item_rim_image($_AREA->{$C_E::_REQUEST}, $_AREA->{$C_E::_REQUEST}['item_image']);
            } else {
                $images_list = [];
            }

            if( !empty($_AREA->{$C_E::_REQUEST}['items']) )
            {
                foreach($_AREA->{$C_E::_REQUEST}['items'] as $key => $item)
                {
                    $items_array[$key] = array_merge(
                        [
                            'brand'        => $_AREA->{$C_E::_REQUEST}['brand'],
                            'model_name'   => $_AREA->{$C_E::_REQUEST}['model_name'],
                            'code'         => $_AREA->{$C_E::_REQUEST}['code'],
                            'paint'        => $_AREA->{$C_E::_REQUEST}['paint'],
                            'description'  => $_AREA->{$C_E::_REQUEST}['description'],
                            'video'        => $_AREA->{$C_E::_REQUEST}['video'],
                            'views'        => $_AREA->{$C_E::_REQUEST}['views'],
                            'promotion_id' => (!empty($_AREA->{$C_E::_REQUEST}['promotion_id'])) ? $_AREA->{$C_E::_REQUEST}['promotion_id'] : NULL,
                            'is_top'       => (!empty($_AREA->{$C_E::_REQUEST}['is_top'])) ? 'Y' : 'N',
                        ],
                        $item
                    );
                }
            } else {
                $items_array[$_AREA->{$C_E::_REQUEST}['item_id']] = [
                    'brand'        => $_AREA->{$C_E::_REQUEST}['brand'],
                    'model_name'   => $_AREA->{$C_E::_REQUEST}['model_name'],
                    'code'         => $_AREA->{$C_E::_REQUEST}['code'],
                    'paint'        => $_AREA->{$C_E::_REQUEST}['paint'],
                    'description'  => $_AREA->{$C_E::_REQUEST}['description'],
                    'video'        => $_AREA->{$C_E::_REQUEST}['video'],
                    'views'        => $_AREA->{$C_E::_REQUEST}['views'],
                    'promotion_id' => (!empty($_AREA->{$C_E::_REQUEST}['promotion_id'])) ? $_AREA->{$C_E::_REQUEST}['promotion_id'] : NULL,
                    'is_top'       => (!empty($_AREA->{$C_E::_REQUEST}['is_top'])) ? 'Y' : 'N',
                ];
            }

            if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->update_item_rim($items_table, $items_array) )
            {
                $hiddenPictureName = '<input type="hidden" name="_request[item_image]" value="' . "{$_AREA->{$C_E::_REQUEST}['brand']}_{$_AREA->{$C_E::_REQUEST}['model_name']}_{$_AREA->{$C_E::_REQUEST}['code']}_{$_AREA->{$C_E::_REQUEST}['paint']}" . '" class="kludge">';

                foreach($images_list as $key => $image)
                {
                    $photos[] = '
                        <li>
                            <input type="checkbox" name="_request[delete_images][]" value="' . $image['thumb'] . '" id="delete_' . $key . '">
                            <label for="delete_' . $key . '">
                                <img src="' . $this->load_resource("items/{$image['thumb']}", TRUE) . '?reload=' . time() . '" alt="' . $image['thumb'] . '">
                            </label>
                        </li>
                    ';
                }

                $response = array_merge($response, [
                    'hiddenPictureName' => $hiddenPictureName,
                    'photos'            => ( !empty($photos) ) ? implode('', $photos) : NULL,
                    'message'           => "<p>Данные успешно обновлены</p>"
                ]);

                $this->satisfy_ajax_request(json_encode($response));
            }
        } elseif($_AREA->{$C_E::_REQUEST}['item_type'] == 'tyres' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_tyres') {
            #UPDATE TYRES
            if( !empty($_AREA->{$C_E::_REQUEST}['item_image']) ) {
                $images_list = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])
                    ->rename_item_tyre_image($_AREA->{$C_E::_REQUEST}, $_AREA->{$C_E::_REQUEST}['item_image']);
            } else {
                $images_list = [];
            }

            if( !empty($_AREA->{$C_E::_REQUEST}['items']) )
            {
                foreach($_AREA->{$C_E::_REQUEST}['items'] as $key => $item)
                {
                    $items_array[$key] = array_merge(
                        [
                            'brand'        => $_AREA->{$C_E::_REQUEST}['brand'],
                            'model_name'   => $_AREA->{$C_E::_REQUEST}['model_name'],
                            'description'  => $_AREA->{$C_E::_REQUEST}['description'],
                            'video'        => $_AREA->{$C_E::_REQUEST}['video'],
                            'views'        => $_AREA->{$C_E::_REQUEST}['views'],
                            'promotion_id' => (!empty($_AREA->{$C_E::_REQUEST}['promotion_id'])) ? $_AREA->{$C_E::_REQUEST}['promotion_id'] : NULL,
                            'is_top'       => (!empty($_AREA->{$C_E::_REQUEST}['is_top'])) ? 'Y' : 'N',
                        ],
                        $item
                    );
                }
            } else {
                $items_array[$_AREA->{$C_E::_REQUEST}['item_id']] = [
                    'brand'        => $_AREA->{$C_E::_REQUEST}['brand'],
                    'model_name'   => $_AREA->{$C_E::_REQUEST}['model_name'],
                    'description'  => $_AREA->{$C_E::_REQUEST}['description'],
                    'video'        => $_AREA->{$C_E::_REQUEST}['video'],
                    'views'        => $_AREA->{$C_E::_REQUEST}['views'],
                    'promotion_id' => (!empty($_AREA->{$C_E::_REQUEST}['promotion_id'])) ? $_AREA->{$C_E::_REQUEST}['promotion_id'] : NULL,
                    'is_top'       => (!empty($_AREA->{$C_E::_REQUEST}['is_top'])) ? 'Y' : 'N',
                ];
            }

            if( $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->update_item_tyre($items_table, $items_array) )
            {
                $hiddenPictureName = '<input type="hidden" name="_request[item_image]" value="' . "{$_AREA->{$C_E::_REQUEST}['brand']}_{$_AREA->{$C_E::_REQUEST}['model_name']}" . '" class="kludge">';

                foreach($images_list as $key => $image)
                {
                    $photos[] = '
                        <li>
                            <input type="checkbox" name="_request[delete_images][]" value="' . $image['thumb'] . '" id="delete_' . $key . '">
                            <label for="delete_' . $key . '">
                                <img src="' . $this->load_resource("items/{$image['thumb']}", TRUE) . '?reload=' . time() . '" alt="' . $image['thumb'] . '">
                            </label>
                        </li>
                    ';
                }

                $response = array_merge($response, [
                    'hiddenPictureName' => $hiddenPictureName,
                    'photos'            => ( !empty($photos) ) ? implode('', $photos) : NULL,
                    'message'           => "<p>Данные успешно обновлены</p>"
                ]);

                $this->satisfy_ajax_request(json_encode($response));
            }
        }
    break;

    case 'modification_delete':
        $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_item_modification($items_table, $_AREA->{$C_E::_REQUEST}['item_id']);
        $this->satisfy_ajax_request();
    break;

    case 'modification_add':
        if($_AREA->{$C_E::_REQUEST}['item_type'] == 'rims' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_rims') {
            $modification = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->add_modification_rim($items_table, $_AREA->{$C_E::_REQUEST}['item']);
        } elseif($_AREA->{$C_E::_REQUEST}['item_type'] == 'tyres' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_tyres') {
            $modification = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->add_modification_tyre($items_table, $_AREA->{$C_E::_REQUEST}['item']);
        }

        if( $modification )
        {
            $delete_landmark = [
                $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
                $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
                $C_AR::AR_LOCATION => "process_item",
                $C_AR::AR_METHOD   => "modification_delete",
                '_request[item_type]' => $_AREA->{$C_E::_REQUEST}['item_type'],
                '_request[item_table]' => $_AREA->{$C_E::_REQUEST}['item_table'],
                '_request[item_id]' => $modification['id']
            ];

            if($_AREA->{$C_E::_REQUEST}['item_type'] == 'rims' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_rims') {
                $interlayer["interlayer"] = $this->load_inter('layer', 'single_item_modification_rim', [$modification, $delete_landmark]);
            } elseif($_AREA->{$C_E::_REQUEST}['item_type'] == 'tyres' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_tyres') {
                $interlayer["interlayer"] = $this->load_inter('layer', 'single_item_modification_tyre', [$modification, $delete_landmark]);
            }
        } else {
            $this->cancel_ajax_request();
        }

        if( $interlayer["interlayer"] ) {
            $this->satisfy_ajax_request(json_encode($interlayer));
        } else {
            $this->cancel_ajax_request();
        }
    break;

    case 'item_delete':
        if($_AREA->{$C_E::_REQUEST}['item_type'] == 'rims' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_rims') {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_item_rim($items_table, $_AREA->{$C_E::_REQUEST}['item_id']);
        } elseif($_AREA->{$C_E::_REQUEST}['item_type'] == 'tyres' || $_AREA->{$C_E::_REQUEST}['item_type'] == 'exclusive_tyres') {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_item_tyre($items_table, $_AREA->{$C_E::_REQUEST}['item_id']);
        }

        #DELETE FILES
        if( !empty($_AREA->{$C_E::_REQUEST}['item_image']) ) {
            $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object('CatalogInput', [$db_handler])->delete_images($_AREA->{$C_E::_REQUEST}['item_type'], $_AREA->{$C_E::_REQUEST}['item_image']);
        }

        $this->satisfy_ajax_request($C_S::SUBSYSTEM_alpha_link."/subcatalog/{$_AREA->{$C_E::_REQUEST}['item_type']}");
    break;

    default:
        $this->cancel_ajax_request();
    break;
}
?>
