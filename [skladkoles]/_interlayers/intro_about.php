<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $about_information = $supplied_data;
}
?>
<div class="centered about">
    <?php foreach($about_information as $value): ?>
        <div class="item">
            <figure>
                <img src="<?=$this->load_resource("images/{$value['icon']}")?>" alt="about us">
            </figure>
            <h2><?=$value['title']?></h2>
            <p><?=$value['text']?></p>
        </div>
    <?php endforeach; ?>
</div>