<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.25) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "filter_data_cars" ) {
    $this->cancel_ajax_request();
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#AUTHORIZATION
if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    define('WHITELIGHT', TRUE);
}
#END/AUTHORIZATION

if( empty($_AREA->{$C_E::_REQUEST}) ) {
    $this->cancel_ajax_request();
} elseif(
    !empty($_AREA->{$C_E::_REQUEST}["auto-mark"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-model"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-year"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-modification"]) ) {
    #STEP 1
    $interlayer = $this->load_inter('layer', 'cars_step_1', $_AREA->{$C_E::_REQUEST});

    if( !$interlayer ) {
        $this->cancel_ajax_request();
    } else {
        $this->satisfy_ajax_request(json_encode([
            'step'       => 1,
            'interlayer' => $interlayer
        ]));
    }
} elseif(
    !empty($_AREA->{$C_E::_REQUEST}["auto-mark"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-model"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-year"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-modification"]) ) {
    #STEP 2
    $interlayer = $this->load_inter('layer', 'cars_step_2', $_AREA->{$C_E::_REQUEST});

    if( !$interlayer ) {
        $this->cancel_ajax_request();
    } else {
        $this->satisfy_ajax_request(json_encode([
            'step'       => 2,
            'interlayer' => $interlayer
        ]));
    }
} elseif(
    !empty($_AREA->{$C_E::_REQUEST}["auto-mark"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-model"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-year"]) &&
    !isset($_AREA->{$C_E::_REQUEST}["auto-modification"]) ) {
    #STEP 3
    $interlayer = $this->load_inter('layer', 'cars_step_3', $_AREA->{$C_E::_REQUEST});

    if( !$interlayer ) {
        $this->cancel_ajax_request();
    } else {
        $this->satisfy_ajax_request(json_encode([
            'step'       => 3,
            'interlayer' => $interlayer
        ]));
    }
} elseif(
    !empty($_AREA->{$C_E::_REQUEST}["auto-mark"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-model"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-year"]) &&
    !empty($_AREA->{$C_E::_REQUEST}["auto-modification"]) ) {
    #STEP LAST
    list($cars_filter, $car_bar) = $this->load_inter("worker", "items_filter_cars", $_AREA->{$C_E::_REQUEST});

    $_SESSION['filter_parameters']['car_bar'] = $car_bar;

    if( !empty($_SESSION['filter_parameters']) && !empty($_SESSION['filter_parameters']['filter_modification']) ) {
        unset($_SESSION['filter_parameters']['filter_modification']);
        $_SESSION['filter_parameters'] = $cars_filter = array_merge(['filter_car_modification' => $cars_filter], $_SESSION['filter_parameters']);
    } elseif( !empty($_SESSION['filter_parameters']) ) {
        unset($_SESSION['filter_parameters']['filter_car_modification']);
        $_SESSION['filter_parameters'] = $cars_filter = array_merge(['filter_car_modification' => $cars_filter], $_SESSION['filter_parameters']);
    } else {
        $_SESSION['filter_parameters'] = ['filter_car_modification' => $cars_filter];
    }

    switch( $_AREA->{$C_E::_ARGUMENTS}[0] )
    {
        case 'rims':
        case 'exclusive_rims':
            $records_number = count($this->load_inter('worker', 'obtain_items_rims', $cars_filter)['items']);

            $inter = [
                'layer'  => 'items_rims',
                'worker' => 'obtain_items_rims'
            ];
        break;

        case 'tyres':
        case 'exclusive_tyres':
            $records_number = count($this->load_inter('worker', 'obtain_items_tyres', $cars_filter)['items']);

            $inter = [
                'layer'  => 'items_tyres',
                'worker' => 'obtain_items_tyres'
            ];
        break;

        default:
            $this->cancel_ajax_request();
        break;
    }

    #PAGINATION
    $current_page     = $_SESSION['pagination']['current_page'] = 1;
    $records_per_page = ( !empty($_SESSION['records_per_page']) ) ? $_SESSION['records_per_page'] : 12;

    $pagination_parameters = [
        'records_number'   => $records_number,
        'records_per_page' => 12,
        'pages_step'       => 7
    ];

    if( is_object($_BOOT->involve_object("Pagination")->set_parameters($pagination_parameters)->set_current_page($current_page)) ) {
        $pagination = $_BOOT->involve_object("Pagination")->handle_pagination();
    } else {
        $this->cancel_ajax_request();
    }

    $items_layer = $this->load_inter('layer', $inter['layer'],
        $this->load_inter('worker', $inter['worker'],
            $cars_filter + ['pagination' => [$pagination['first_record'], $pagination['records_per_page']]]
        )
    );

    $pagination = $this->load_inter('layer', 'pagination', $pagination);
    #END-PAGINATION

    if( !$items_layer ) {
        $this->cancel_ajax_request();
    } else {
        $this->satisfy_ajax_request(json_encode([
            'step'        => "last",
            'catalogGrid' => $items_layer,
            'navigation'  => $pagination,
            'autoResult'  => $this->load_inter('layer', 'car_bar', $car_bar)
        ]));
    }
}
?>