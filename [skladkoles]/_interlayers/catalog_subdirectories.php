<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $catalog_subdirectories = $supplied_data;
}

$xml_payment_types = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$is_active = function($item) use($_AREA, $C_E) {
    return ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) && ($_AREA->{$C_E::_ARGUMENTS}[0] == $item) ) ? 'class="active"' : "";
};

$current_directory = ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) ? $_AREA->{$C_E::_ARGUMENTS}[0] : NULL;
?>
<!--<ul class="categories">
    <?php foreach($catalog_subdirectories as $value): ?>
        <?php if( $value['directory'] == 'spares' ): ?>
            <li class="accessories-categorie <?=( $_AREA->{$C_E::_DIRECTORY} == 'spares' ) ? "active" : "";?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$value['image_thumb']}")?>" alt="<?=$value['image']?>">
                    <figcaption><h2><?=$value['title']?></h2></figcaption>
                </figure>
                <ul>
                    <li>
                        <a href="<?=$this->get_current_link("{$value['directory']}/rings")?>" <?=$is_active('rings')?>><?=$xml_payment_types->spares->rings?></a>
                    </li>
                    <li>
                        <a href="<?=$this->get_current_link("{$value['directory']}/bolts")?>" <?=$is_active('bolts')?>><?=$xml_payment_types->spares->bolts?></a>
                    </li>
                    <li>
                        <a href="<?=$this->get_current_link("{$value['directory']}/nuts")?>" <?=$is_active('nuts')?>><?=$xml_payment_types->spares->nuts?></a>
                    </li>
                    <li>
                        <a href="<?=$this->get_current_link("{$value['directory']}/locks")?>" <?=$is_active('locks')?>><?=$xml_payment_types->spares->locks?></a>
                    </li>
                    <li>
                        <a href="<?=$this->get_current_link("{$value['directory']}/logos")?>" <?=$is_active('logos')?>><?=$xml_payment_types->spares->logos?></a>
                    </li>
                </ul>
            </li>
        <?php else: ?>
            <li <?=$class_active = ( $value['directory'] == $current_directory ) ? 'class="active"' : NULL;?>>
                <a href="<?=$this->get_current_link("subcatalog/{$value['directory']}")?>">
                    <figure>
                        <img src="<?=$this->load_resource("images/categories/{$value['image_thumb']}")?>" alt="<?=$value['image']?>">
                        <figcaption><h2><?=$value['title']?></h2></figcaption>
                    </figure>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>-->
<ul class="categories">
    <?php foreach($catalog_subdirectories as $value): ?>
        <li <?=$class_active = ( $value['directory'] == $current_directory ) ? 'class="active"' : NULL;?>>
            <?php if( $value['directory'] == 'spares' ): ?>
                <a href="<?=$this->get_current_link($value['directory'])?>">
            <?php else: ?>
                <a href="<?=$this->get_current_link("subcatalog/{$value['directory']}")?>">
            <?php endif; ?>
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$value['image_thumb']}")?>" alt="<?=$value['image_thumb']?>">
                    <figcaption><h2><?=$value['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
    <?php endforeach ?>
</ul>
