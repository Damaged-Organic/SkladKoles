<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $special_offers = $supplied_data;
}

$transform = function($input_array)
{
    $output_array = [];

    foreach($input_array as $value)
    {
        if( ($value['end_date'] != 0) && $value['end_date'] < time() ) {
            $output_array['ended'][] = $value;
        } else {
            $output_array['active'][] = $value;
        }
    }

    return $output_array;
};

$special_offers = $transform($special_offers);
?>
<div class="section">
<?php if( !empty($special_offers['active']) ): ?>
<div class="track-lane gaps"><h1>Текущие акции</h1></div>
<section class="centered">
    <ul class="grid">
        <?php foreach($special_offers['active'] as $value): ?>
            <?php $is_class_full = ( !empty($value['end_date']) || !empty($value['description']) ) ? TRUE : FALSE; ?>
            <li>
                <div class="photo">
                    <figure>
                        <a href="<?=$this->get_current_link('promotion_details')?>/<?=$value['id']?>">
                            <img src="<?=$this->load_resource("slider/{$value['image']}")?>" alt="<?=$value['image']?>">
                        </a>
                    </figure>
                </div>
                <div class="counter-wrapper">
                    <?php if( !empty($value['end_date']) ): ?>
                        <?php if( $value['end_date'] > time() ): ?>
                            <h2>до конца акции осталось</h2>
                            <div class="counter" data-timestamp="<?=date("n, j, Y H:m:s", $value['end_date'])?>"></div>
                        <?php else: ?>
                            <h2 class="centeredPromo">акция завершена</h2>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2 class="centeredPromo">акция действует на постоянной основе</h2>
                    <?php endif ?>
                </div>
                <a href="<?=$this->get_current_link('promotion_details')?>/<?=$value['id']?>" class="button">детальнее</a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php
    endif;

    if( !empty($special_offers['ended']) ):
?>
<div class="track-lane gaps"><h1>Завершенные акции</h1></div>
<section class="centered">
    <ul class="grid">
        <?php foreach($special_offers['ended'] as $value): ?>
            <?php $is_class_full = ( !empty($value['end_date']) || !empty($value['description']) ) ? TRUE : FALSE; ?>
            <li>
                <div class="photo">
                    <figure>
                        <a href="<?=$this->get_current_link('promotion_details')?>/<?=$value['id']?>">
                            <img src="<?=$this->load_resource("slider/{$value['image']}")?>" alt="<?=$value['image']?>">
                        </a>
                    </figure>
                </div>
                <div class="counter-wrapper">
                    <?php if( !empty($value['end_date']) ): ?>
                        <?php if( $value['end_date'] > time() ): ?>
                            <h2>до конца акции осталось</h2>
                            <div class="counter" data-timestamp="<?=date("n, j, Y H:m:s", $value['end_date'])?>"></div>
                        <?php else: ?>
                            <h2 class="centeredPromo">акция завершена</h2>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2 class="centeredPromo">акция действует на постоянной основе</h2>
                    <?php endif ?>
                </div>
                <a href="<?=$this->get_current_link('promotion_details')?>/<?=$value['id']?>" class="button">детальнее</a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
</div>
<?php if( !empty($special_offers['active']) && !empty($special_offers['ended']) ): ?>
    <div class="track-lane gaps to-right"></div>
<?php endif; ?>