<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $catalog_subdirectories = $supplied_data;
}

$is_active = function($item) use($_AREA, $C_E) {
    return ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) && ($_AREA->{$C_E::_ARGUMENTS}[0] == $item) ) ? 'class="active"' : "";
};

if( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) {
    $current_directory = $_AREA->{$C_E::_ARGUMENTS}[0];
} else {
    $current_directory = $_AREA->{$C_E::_DIRECTORY};
}
?>
<ul class="categories inside-item">
    <?php foreach($catalog_subdirectories as $value): ?>
        <li <?=$class_active = ( $value['directory'] == $current_directory ) ? 'class="active"' : NULL;?>>
            <div class="btn">
                <?php if( $value['directory'] == 'spares' ): ?>
                    <a href="<?=$this->get_current_link($value['directory'])?>"><?=$value['title']?></a>
                <?php else: ?>
                    <a href="<?=$this->get_current_link("subcatalog/{$value['directory']}")?>"><?=$value['title']?></a>
                <?php endif; ?>
            </div>
        </li>
    <?php endforeach ?>
</ul>
