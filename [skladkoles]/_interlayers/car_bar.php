<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    return FALSE;
} else {
    $car_data = $supplied_data;
}

$xml_cars_brands_popup = $_BOOT->involve_object("XML_Handler")->get_xml(
    'cars_popup',
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<p><?=$xml_cars_brands_popup->tabs->vehicle?> <span><?=$car_data['vendor']?></span></p>
<p><?=$xml_cars_brands_popup->tabs->model?> <span><?=$car_data['car']?></span></p>
<p><?=$xml_cars_brands_popup->tabs->year?> <span><?=$car_data['year']?></span></p>
<p><?=$xml_cars_brands_popup->tabs->modification?> <span><?=$car_data['modification']?></span></p>
<span class="open-characteristics">характеристики >> </span>
<div class="characteristics">
    <p>заводские диски:</p>
    <ul>
        <?php if( !empty($car_data['wheels_factory'][0]) ): ?>
            <?php $car_data['wheels_factory'] = explode(',', $car_data['wheels_factory'][0]); ?>
            <?php foreach($car_data['wheels_factory'] as $value): ?>
                <li><?="PCD {$car_data['pcd']}, DIA {$car_data['dia']}, {$value}"?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>заводская комплектация не указана</li>
        <?php endif; ?>
    </ul>
    <?php if( !empty($car_data['wheels_replace'][0]) ): ?>
        <p>варианты замены дисков:</p>
        <ul>
        <?php foreach($car_data['wheels_replace'] as $value): ?>
            <li><?="PCD {$car_data['pcd']}, DIA {$car_data['dia']}, {$value}"?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <p>заводские шины:</p>
    <ul>
        <?php if( !empty($car_data['tyres_factory'][0]) ): ?>
            <?php $car_data['tyres_factory'] = explode(',', $car_data['tyres_factory'][0]); ?>
            <?php foreach($car_data['tyres_factory'] as $value): ?>
                <li><?=$value?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>заводская комплектация не указана</li>
        <?php endif; ?>
    </ul>
    <?php if( !empty($car_data['tyres_replace'][0]) ): ?>
        <p>варианты замены шин:</p>
        <ul>
        <?php foreach($car_data['tyres_replace'] as $value): ?>
            <li><?=$value?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>