<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $special_offers_details = $supplied_data[0];
}
?>
<div class="track-lane gaps"><h1>Акция</h1></div>
<div class="centered">
    <ul class="grid">
        <li class="full">
            <div class="photo">
                <figure><img src="<?=$this->load_resource("slider/{$special_offers_details['image']}")?>" alt=""></figure>
            </div>
            <div class="info">
                <?php if( !empty($special_offers_details['end_date']) ): ?>
                    <h2>до конца акции осталось</h2>
                    <div class="counter" data-timestamp="<?=date("n, j, Y H:m:s", $special_offers_details['end_date'])?>"></div>
                <?php else: ?>
                    <h2>акция действует на постоянной основе</h2>
                <?php endif ?>
                <p><?=$special_offers_details['description']?></p>
            </div>
        </li>
    </ul>
</div>