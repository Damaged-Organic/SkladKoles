<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( $_AREA->{$C_E::_DIRECTORY} === 'main' ) {
    $news_per_lift = 3;
} else {
    $news_per_lift = 6;
}

#Single article selection role
if( !empty($supplied_data['id']) )
{
    if( ($id = $_BOOT->involve_object("InputPurifier")->purge_integer($supplied_data['id'])) === FALSE ) {
        $this->redirect_and_exit($this->get_current_link());
    } elseif( !$_BOOT->involve_object("DB_Handler")->is_value_exists($C_S::DB_PREFIX_alpha.'news', 'id', $id) ) {
        $this->redirect_and_exit($this->get_current_link());
    }

    $input_entity_conditions = ['id' => ['=', $id, 'AND']];
}

#Lift article selection role
if( !empty($supplied_data['count']) )
{
    if( ($count = $_BOOT->involve_object("InputPurifier")->purge_integer($supplied_data['count'])) === FALSE ) {
        return FALSE;
    } elseif( $_BOOT->involve_object("DB_Handler")->get_records_number($C_S::DB_PREFIX_alpha.'news') <= $count ) {
        return FALSE;
    }

    $input_entity_limits = [$count, $news_per_lift];
}

$db_handler = $_BOOT->involve_object('DB_Handler');

#DDC PARAMETERS
$entity_content = [
    $C_S::DB_PREFIX_alpha.'news_content' => array($_AREA->{$C_E::_LANGUAGE})
];

$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'news'         => array('id', 'record_order', 'date_created', 'image', 'image_thumb', 'views'),
    $C_S::DB_PREFIX_alpha.'news_content' => array('title', 'text')
];

if( $_BOOT->assign_namespace($C_N::MEAT_EXTERNAL)->involve_object("PHPLoginLink", [$db_handler, $_AREA->{$C_E::_REQUEST}])->is_user_logged_in() ) {
    $entity_conditions = ( !empty($input_entity_conditions) ) ? $input_entity_conditions : NULL;
} else {
    $entity_conditions = ( !empty($input_entity_conditions) )
                         ? ['title' => ['<>', 'NULL', 'AND'], 'text' => ['<>', 'NULL', 'AND']] + $input_entity_conditions
                         : ['title' => ['<>', 'NULL', 'AND'], 'text' => ['<>', 'NULL', 'AND']];
}

$entity_orders = [
    'date_created' => 'DESC'
];

$entity_limits = ( !empty($input_entity_limits) ) ? $input_entity_limits : [$news_per_lift];
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$news = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell($entity_content, $entity_tables_fields, $entity_conditions, $entity_orders, $entity_limits);

return $news;
?>