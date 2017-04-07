<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $directories = $supplied_data;
}

$xml_footer_directories = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<h2><?=$xml_footer_directories->headline?></h2>
<?php foreach($directories as $value): ?>
    <a href="<?=$this->get_current_link($value['directory'])?>"><?=$value['title']?></a>
<?php endforeach; ?>
