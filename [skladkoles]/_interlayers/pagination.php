<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $pagination = $supplied_data;
}

if( !$pagination['navigation_required'] ) {
    return FALSE;
}

$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
        'AR_location' => "navigation",
        'AR_method'   => "set_page"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<div class="navigation" data-landmark='<?=$data_landmark?>'>
    <?php if( $pagination['side_buttons']['page_prev'] ): ?>
        <a href="#" class="prev" data-current="<?=$pagination['current_page']-1?>"></a>
    <?php else: ?>
        <a href="#" class="prev" data-current=""></a>
    <?php endif; ?>
    <!--PAGES-->
    <?php foreach($pagination['navigation_items'] as $navigation_item): ?>
        <?php if( $navigation_item === 'separator' ): ?>
            <span class="points"></span>
            <span class="points"></span>
            <span class="points"></span>
        <?php else: ?>
            <a href="#" data-current="<?=$navigation_item?>" <?php if($navigation_item == $pagination['current_page']) echo 'class="active"'; ?>><?=$navigation_item?></a>
        <?php endif; ?>
    <?php endforeach; ?>
    <!--END-PAGES-->
    <?php if( $pagination['side_buttons']['page_next'] ): ?>
        <a href="#" class="next" data-current="<?=$pagination['current_page']+1?>"></a>
    <?php else: ?>
        <a href="#" class="next" data-current=""></a>
    <?php endif; ?>
</div>