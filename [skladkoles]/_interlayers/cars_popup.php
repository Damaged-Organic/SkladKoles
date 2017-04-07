<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $car_brands = $supplied_data;
}

$xml_cars_brands_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
        'AR_location' => "items_filter_cars",
        'AR_method'   => "filter_data_cars"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<span class="close"><?=$xml_cars_brands_popup->close_popup?></span>
<div class="track-lane gaps to-right separated"><h2><?=$xml_cars_brands_popup->headline?></h2></div>
<div class="centered">
<form action="php/autoFilter.php" id="autoFilter" method="POST" data-landmark='<?=$data_landmark?>' autocomplete="off">
<div class="tabs" id="auto-tabs">
<ul>
    <li class="tabs-label">1. <?=$xml_cars_brands_popup->tabs->vehicle?></li>
    <li class="tabs-label disabled">2. <?=$xml_cars_brands_popup->tabs->model?></li>
    <li class="tabs-label disabled">3. <?=$xml_cars_brands_popup->tabs->year?></li>
    <li class="tabs-label disabled">4. <?=$xml_cars_brands_popup->tabs->modification?></li>
</ul>
<div class="tabs-content auto-mark">
<h2><?=$xml_cars_brands_popup->subheadline_vehicles?></h2>
<?php foreach($car_brands as $value): ?>
    <input type="radio" name="<?=$C_E::_REQUEST?>[auto-mark]" value="<?=$value['car']?>" id="<?=$value['car']?>" data-step="0">
    <label for="<?=$value['car']?>">
        <img src="<?=$this->load_resource("images/auto-mark/{$value['image']}")?>" alt="<?=$value['image']?>">
    </label>
<?php endforeach; ?>
</div>
<div class="tabs-content auto-model"></div>
<div class="tabs-content auto-year"></div>
<div class="tabs-content auto-modifications"></div>
</div>
</form>
</div>