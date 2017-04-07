<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($type, $brands, $modifications) = $supplied_data;
    $existing_brands = ( $modifications['brand'] ) ?: [];
}

$xml_brands_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<span class="close"><?=$xml_brands_popup->close_popup?></span>
<div class="track-lane gaps to-right separated"><h2><?=$xml_brands_popup->headline?></h2></div>
<div class="centered">
<?php foreach($brands as $value): ?>
    <input type="radio" name="<?=$C_E::_REQUEST?>[brand]" value="<?=$value['brand']?>" id="<?=$value['brand']?>" form="baseFilter"
           <?=( !in_array(strtolower(str_replace('_', ' ', $value['brand'])), $existing_brands) ) ? 'disabled' : NULL;?>
           <?=( !empty($_SESSION['filter_parameters']['filter_common']['brand']) && ($_SESSION['filter_parameters']['filter_common']['brand'] == $value['brand']) ) ? "checked" : NULL;?>>
    <label for="<?=$value['brand']?>">
        <img src="<?=$this->load_resource("images/brands/{$type}/{$value['image']}")?>" alt="<?=$value['image']?>">
    </label>
<?php endforeach; ?>
</div>