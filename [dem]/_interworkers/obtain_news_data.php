<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    $news_id = $supplied_data;
}

if( ($news_id = $_BOOT->involve_object("InputPurifier")->purge_integer($news_id)) === FALSE ) {
    $this->redirect_and_exit($this->get_current_link());
} elseif( !$_BOOT->involve_object("DB_Handler")->is_value_exists($C_S::DB_PREFIX_alpha.'news', 'id', $news_id) ) {
    $this->redirect_and_exit($this->get_current_link());
}

#DDC PARAMETERS
$entity_content = [
    $C_S::DB_PREFIX_alpha.'news_content' => array($_AREA->{$C_E::_LANGUAGE})
];

$entity_tables_fields = [
    $C_S::DB_PREFIX_alpha.'news'         => array('id', 'image', 'image_thumb'),
    $C_S::DB_PREFIX_alpha.'news_content' => array('title', 'text')
];

$entity_conditions = ['id' => ['=', $news_id, 'AND']];
#END::DDC PARAMETERS

if( !$_BOOT->involve_object("DB_Handler")->validate_tables(array_keys($entity_tables_fields)) ) {
    throw new procException("Table(s) does not exists");
}

$news = $_BOOT->involve_object(
    "DB_CellConstructor",
    [$_BOOT->involve_object("DB_Handler")]
)->dynamic_data_cell($entity_content, $entity_tables_fields, $entity_conditions, NULL, NULL);

return $news;
?>