<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list($type, $brands, $modifications) = $supplied_data;
    $existing_brands = ( !empty($modifications['brand']) ) ? array_filter($modifications['brand']) : [];
}

if( $existing_brands ): ?>
    <div class="brands">
    <?php foreach($brands as $value): ?>
        <?php if(in_array(strtolower(str_replace('_', ' ', $value['brand'])), $existing_brands)): ?>
            <?php
                $active = ( !empty($_SESSION['filter_parameters']['filter_common']['brand']) && ($_SESSION['filter_parameters']['filter_common']['brand'] == $value['brand']) )
                    ? 'active'
                    : NULL
                ;
            ?>
            <a class="<?=$active?>" href="<?=$this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}")?>/brand-<?=$value['brand']?>">
                <?=$value['title']?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
