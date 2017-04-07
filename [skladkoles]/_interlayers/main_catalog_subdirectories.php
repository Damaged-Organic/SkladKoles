<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $catalog_subdirectories = $supplied_data;
}

$transform = function($input_array)
{
    $mediate_array = [];

    foreach($input_array as $value) {
        $mediate_array[$value['directory']] = $value;
    }

    return $mediate_array;
};

$catalog_subdirectories = $transform($catalog_subdirectories);
?>
<div class="centered catalog-categories">
    <div class="row">
        <div class="column first line-one">
            <div class="item">
                <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['rims']['directory']}")?>">
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['rims']['image']}")?>" alt="<?=$catalog_subdirectories['rims']['image']?>">
                    <div class="info">
                        <h2><?=$catalog_subdirectories['rims']['title']?></h2>
                        <p><?=$catalog_subdirectories['rims']['text']?></p>
                    </div>
                </a>
            </div>
        </div>
        <div class="column second line-one">
            <div class="item">
                <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['tyres']['directory']}")?>">
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['tyres']['image']}")?>" alt="<?=$catalog_subdirectories['tyres']['image']?>">
                    <div class="info">
                        <h2><?=$catalog_subdirectories['tyres']['title']?></h2>
                        <p><?=$catalog_subdirectories['tyres']['text']?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="column first line-two">
            <div class="item">
                <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['exclusive_rims']['directory']}")?>">
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['exclusive_rims']['image']}")?>" alt="<?=$catalog_subdirectories['exclusive_rims']['image']?>">
                    <div class="info">
                        <h2><?=$catalog_subdirectories['exclusive_rims']['title']?></h2>
                    </div>
                </a>
            </div>
            <div class="item">
                <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['exclusive_tyres']['directory']}")?>">
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['exclusive_tyres']['image']}")?>" alt="<?=$catalog_subdirectories['exclusive_tyres']['image']?>">
                    <div class="info">
                        <h2><?=$catalog_subdirectories['exclusive_tyres']['title']?></h2>
                    </div>
                </a>
            </div>
        </div>
        <div class="column second line-two">
            <div class="item">
                <a href="<?=$this->get_current_link("{$catalog_subdirectories['spares']['directory']}/rings")?>">
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['spares']['image']}")?>" alt="<?=$catalog_subdirectories['spares']['image']?>">
                    <div class="info">
                        <h2><?=$catalog_subdirectories['spares']['title']?></h2>
                        <p><?=$catalog_subdirectories['spares']['text']?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!--<div class="section catalog">
    <ul class="categories on-main">
        <li>
            <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['rims']['directory']}")?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['rims']['image_thumb']}")?>" alt="<?=$catalog_subdirectories['rims']['image_thumb']?>">
                    <figcaption><h2><?=$catalog_subdirectories['rims']['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
        <li>
            <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['tyres']['directory']}")?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['tyres']['image_thumb']}")?>" alt="<?=$catalog_subdirectories['tyres']['image_thumb']?>">
                    <figcaption><h2><?=$catalog_subdirectories['tyres']['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
        <li>
            <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['exclusive_rims']['directory']}")?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['exclusive_rims']['image_thumb']}")?>" alt="<?=$catalog_subdirectories['exclusive_rims']['image_thumb']?>">
                    <figcaption><h2><?=$catalog_subdirectories['exclusive_rims']['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
        <li>
            <a href="<?=$this->get_current_link("subcatalog/{$catalog_subdirectories['exclusive_tyres']['directory']}")?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['exclusive_tyres']['image_thumb']}")?>" alt="<?=$catalog_subdirectories['exclusive_tyres']['image_thumb']?>">
                    <figcaption><h2><?=$catalog_subdirectories['exclusive_tyres']['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
        <li>
            <a href="<?=$this->get_current_link($catalog_subdirectories['spares']['directory'])?>">
                <figure>
                    <img src="<?=$this->load_resource("images/categories/{$catalog_subdirectories['spares']['image_thumb']}")?>" alt="<?=$catalog_subdirectories['spares']['image_thumb']?>">
                    <figcaption><h2><?=$catalog_subdirectories['spares']['title']?></h2></figcaption>
                </figure>
            </a>
        </li>
    </ul>
</div>-->