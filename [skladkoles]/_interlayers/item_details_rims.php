<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $item = $supplied_data;
}

if( $_AREA->{$C_E::_ARGUMENTS}[0] === 'rims' ) {
    $current_type = "rims";
    $current_unique_code_prefix = "R";
} elseif( $_AREA->{$C_E::_ARGUMENTS}[0] === 'exclusive_rims' ) {
    $current_type = "exclusive_rims";
    $current_unique_code_prefix = "RU";
}

$item['item'] = $item['item'] + $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->provide_item_type($current_type);

$sort_by_radius = function($item_modifications)
{
    $output = [];

    foreach( $item_modifications as $item_modification )
    {
        $output[$item_modification['r']][] = $item_modification;
    }

    return $output;
};

$item_modifications = $sort_by_radius($item['modifications']);

if( empty($item['item']['promotion_id']) ) {
    $promotion = NULL;
} else {
    $promotion = $this->load_inter('worker', 'special_offers', [
        NULL,
        $id = $item['item']['promotion_id']
    ])[0];
}

$xml_item_details_rims = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

if( !empty($_SESSION['filter_parameters']['car_bar']) ) {
    $car_bar = $_SESSION['filter_parameters']['car_bar'];
} else {
    $car_bar = NULL;
}

if( !empty($_SESSION['filter_parameters']['filter_modification']['rims']) ) {
    $modifications = $_SESSION['filter_parameters']['filter_modification']['rims'];
} else {
    $modifications = NULL;
}

$landmark_array = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "cart",
    $C_AR::AR_METHOD   => "add_item",
    $C_E::_REQUEST     => ['item_type' => NULL, 'id' => NULL]
];

$rating_landmark_array = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}/{$_AREA->{$C_E::_ARGUMENTS}[1]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "rating",
    $C_AR::AR_METHOD   => "rate",
    $C_E::_REQUEST     => ['item_type' => NULL, 'id' => NULL]
];

$encode_data_landmark = function($landmark_array) {
    return json_encode($landmark_array, JSON_UNESCAPED_SLASHES);
}
?>
<div class="left-col">
    <div id="gallery">
        <?php
            $item_images = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($item['item'], $single_thumb = FALSE);
            $next_images = ( count($item_images) > 1 ) ? $item_images : [];
        ?>
        <div class="photo-wrapper">
            <?php if( !empty($item['item']['video']) ): ?>
                <div id="overview-holder">
                    <a href="#"><span class="fa fa-play"></span>Видеообзор</a>
                </div>
            <?php endif; ?>
            <figure class="mainPreivew photo">
                <img src="<?=$this->load_resource("items/{$item_images[0]['image']}")?>" alt="<?=$item_images[0]['image']?>">
            </figure>
        </div>
        <ul>
            <?php foreach($next_images as $key => $image): ?>
                <li <?=( $key == 0 ) ? 'class="active"' : NULL ?>>
                    <figure class="preview" data-path="<?=$this->load_resource("items/{$image['image']}")?>">
                        <img src="<?=$this->load_resource("items/{$image['thumb']}")?>" alt="<?=$image['thumb']?>">
                    </figure>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
</div>
<div class="right-col">
    <div class="benefits-holder">
        <ul>
            <li>
                <span class="fa fa-diamond"></span>
                <p>Вас ждет индивидуальный подход</p>
            </li>
            <li>
                <span class="fa fa-compass"></span>
                <p>Наличие выставочного зала</p>
            </li>
            <li>
                <span class="fa fa-dollar"></span>
                <p>Вы сэкономите свое время и деньги</p>
            </li>
            <li>
                <span class="fa fa-chain"></span>
                <p>Мы гарантируем качество</p>
            </li>
        </ul>
    </div>
    <?php if( defined('WHITELIGHT') ): ?>
        <a href="<?=$this->get_current_link("item/{$item['item']['type']}/{$item['item']['id']}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать данные товара" target="_blank"></a>
    <?php endif; ?>
    <?php if( !empty($promotion) ): ?>
        <div class="item-sale">
            <div class="countdown-wrapper">
                <?php if( !empty($promotion['end_date']) ): ?>
                    <h2>до конца акции</h2>
                    <div class="countdown" data-timestamp="<?=date("n, j, Y H:m:s", $promotion['end_date'])?>"></div>
                <?php else: ?>
                    <h2 class="centeredPromo">акция действует на постоянной основе</h2>
                <?php endif ?>
            </div>
            <figure>
                <a href="<?=$this->get_current_link('promotion_details')?>/<?=$promotion['id']?>">
                    <img src="<?=$this->load_resource("slider/{$promotion['image']}")?>" alt="">
                </a>
            </figure>
        </div>
    <?php endif; ?>
    <?php if( $item['item']['type'] == 'exclusive_rims' ): ?>
        <div class="item-exclusive">
            <h2>Эксклюзивный товар</h2>
        </div>
    <?php endif; ?>
    <div class="guts-holder">
        <div class="item-header">
            <h2>
                <?="{$item['item']['brand']} {$item['item']['model_name']} {$item['item']['code']} {$item['item']['paint']}"?>
                <?php if( !empty($item['item']['rim_type']) ): ?>
                    (<?=$item['item']['rim_type']?>)
                <?php endif; ?>
            </h2>
            <figure class="brand">
                <?php if( $brand_image = $this->load_resource("images/brands/rims/" . strtolower(str_replace(' ', '_', $item['item']['brand'])) . ".jpg") ): ?>
                    <img src="<?=$brand_image?>" alt="<?=$item['item']['brand']?>">
                <?php endif ?>
            </figure>
        </div>
        <div class="rating-wrapper">
            <?php
                $rating_landmark_array[$C_E::_REQUEST] = ['item_type' => $current_type, 'id' => $item['item']['id']];
                $data_landmark = $encode_data_landmark($rating_landmark_array);
            ?>
            <div class="rating" data-landmark='<?=$data_landmark?>'>
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <span class="star <?=( round($item['item']['rating_score']) >= $i ) ? 'active' : ''; ?>" data-vote="<?=$i?>"></span>
                <?php endfor; ?>
            </div>
            <p id="voteCount"><span><?=round($item['item']['rating_votes'])?></span> голосов</p>
            <p id="totalRating"><span><?=round($item['item']['rating_score'])?></span> из <span>5</span></p>
        </div>
        <?php if( !empty($item['description']['description']) ): ?>
            <?php
                $item['description']['description'] = str_replace(['<br>', '</div>'], ['', '<br>'], $item['description']['description']);
                $item['description']['description'] = strip_tags($item['description']['description'], '<b><i><s><u><ul><li><br>');
            ?>
            <span>
                <?=$item['description']['description']?>
            </span>
        <?php endif; ?>
        <?php if( $modifications ): ?>
            <div class="most-valuable-holder">
                <h3>В таблице представлены модификации, которые могут вам подойти:</h3>
                <table>
                    <tr>
                        <th>Артикул</th>
                        <th>PCD</th>
                        <th>R x W
                        </th>
                        <th>ET</th>
                        <th>CH</th>
                        <th><?=$xml_item_details_rims->label_price?></th>
                        <th><?=$xml_item_details_rims->label_buy?></th>
                    </tr>

                    <?php
                        $isDiaEmpty  = ( empty($modifications['pcd_dia']) && empty($modifications['pcd_dia_extra']) ) ? TRUE : FALSE;
                        $isStudEmpty = ( empty($modifications['pcd_stud']) ) ? TRUE : FALSE;
                        $isWEmpty    = ( empty($modifications['w']) ) ? TRUE : FALSE;
                        $isREmpty    = ( empty($modifications['r']) ) ? TRUE : FALSE;
                    ?>

                    <?php foreach( $item['modifications'] as $value ): ?>
                        <?php
                            $isDiaEquals  = ( !$isDiaEmpty && ($modifications['pcd_dia'] == $value['pcd_dia'] || $modifications['pcd_dia_extra'] == $value['pcd_dia_extra']) ) ? TRUE : FALSE;
                            $isStudEquals = ( !$isStudEmpty && $modifications['pcd_stud'] == $value['pcd_stud'] ) ? TRUE : FALSE;
                            $isWEquals    = ( !$isWEmpty && $modifications['w'] == $value['w'] );
                            $isREquals    = ( !$isREmpty && $modifications['r'] == $value['r'] );

                            $pair1 = [$isDiaEquals, $isDiaEmpty];
                            $pair2 = [$isStudEquals, $isStudEmpty];
                            $pair3 = [$isWEquals, $isWEmpty];
                            $pair4 = [$isREquals, $isREmpty];

                            $displayModification = function($pair1, $pair2, $pair3, $pair4)
                            {
                                $combinations = [];
                                foreach($pair1 as $v1) {
                                    foreach($pair2 as $v2) {
                                        foreach($pair3 as $v3) {
                                            foreach($pair4 as $v4) {
                                                $combinations[] = [$v1, $v2, $v3, $v4];
                                            }
                                        }
                                    }
                                }

                                array_pop($combinations);

                                foreach($combinations as $combination)
                                {
                                    if( count(array_unique($combination)) === 1 )
                                        if( current($combination) === TRUE )
                                            return TRUE;
                                }

                                return FALSE;
                            };
                        ?>

                        <?php if( $displayModification($pair1, $pair2, $pair3, $pair4) ): ?>
                            <tr>
                                <td><?="{$current_unique_code_prefix}-{$value['unique_code']}"?></td>
                                <td>
                                    <?="{$value['pcd_stud']}*{$value['pcd_dia']}"?>
                                    <?php if( $value['pcd_dia_extra'] ): ?>
                                        / <?=$value['pcd_dia_extra']?>
                                    <?php endif ?>
                                </td>
                                <td><?="{$value['r']}x{$value['w']}"?></td>
                                <td><?=$value['et']?></td>
                                <td><?=number_format($value['ch'], 1);?></td>
                                <td>
                                    <?php if( !empty($value['promo']) ): ?>
                                        <span style="font-size: 0.875em; text-decoration: line-through;color: #989898;">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                                        <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?> UAH
                                    <?php else: ?>
                                        <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?> UAH
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $value['stock'] > 0 ): ?>
                                        <?php
                                            $landmark_array[$C_E::_REQUEST] = ['item_type' => $current_type, 'id' => $value['id']];
                                            $data_landmark = $encode_data_landmark($landmark_array);
                                        ?>
                                        <a href="#" class="addToCart" data-landmark='<?=$data_landmark?>'>
                                            <span class="loader">
                                                <span class="fa fa-cog"></span>
                                            </span>
                                            <span class="title">
                                                <span class="fa fa-shopping-cart"></span>
                                            </span>
                                        </a>
                                    <?php else: ?>
                                        <?=$xml_item_details_rims->not_available?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
        <div class="tabs">
            <?php if( $car_bar ): ?>
                <h3>Ниже представлены модификации, подходящие для автомобиля <?=$car_bar['vendor']?> <?=$car_bar['car']?> <?=$car_bar['modification']?> <?=$car_bar['year']?>-го года выпуска:</h3>
            <?php else: ?>
                <h3>
                    Все модификации диска
                    <?="{$item['item']['brand']} {$item['item']['model_name']} {$item['item']['code']} {$item['item']['paint']}:"?>
                </h3>
            <?php endif; ?>

            <ul>
                <?php foreach( $item_modifications as $radius => $item_modification): ?>
                    <li class="tabs-label"><?=$radius ?></li>
                <?php endforeach ?>
            </ul>

            <?php foreach( $item_modifications as $radius => $item_modification): ?>
                <div class="tabs-content">
                    <table>
                        <tr>
                            <th>Артикул</th>
                            <th>PCD</th>
                            <th>R x W
                            </th>
                            <th>ET</th>
                            <th>CH</th>
                            <th><?=$xml_item_details_rims->label_price?></th>
                            <th><?=$xml_item_details_rims->label_buy?></th>
                        </tr>

                        <?php foreach( $item_modification as $value ): ?>
                            <tr>
                                <td><?="{$current_unique_code_prefix}-{$value['unique_code']}"?></td>
                                <td>
                                    <?="{$value['pcd_stud']}*{$value['pcd_dia']}"?>
                                    <?php if( $value['pcd_dia_extra'] ): ?>
                                        / <?=$value['pcd_dia_extra']?>
                                    <?php endif ?>
                                </td>
                                <td><?="{$value['r']}x{$value['w']}"?></td>
                                <td><?=$value['et']?></td>
                                <td><?=number_format($value['ch'], 1);?></td>
                                <td>
                                    <?php if( !empty($value['promo']) ): ?>
                                        <span style="font-size: 0.875em; text-decoration: line-through;color: #989898;">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                                        <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?> UAH
                                    <?php else: ?>
                                        <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?> UAH
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $value['stock'] > 0 ): ?>
                                        <?php
                                            $landmark_array[$C_E::_REQUEST] = ['item_type' => $current_type, 'id' => $value['id']];
                                            $data_landmark = $encode_data_landmark($landmark_array);
                                        ?>
                                        <a href="#" class="addToCart" data-landmark='<?=$data_landmark?>'>
                                            <span class="loader">
                                                <span class="fa fa-cog"></span>
                                            </span>
                                            <span class="title">
                                                <span class="fa fa-shopping-cart"></span>
                                            </span>
                                        </a>
                                    <?php else: ?>
                                        <?=$xml_item_details_rims->not_available?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </table>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php if( !empty($item['item']['video']) ): ?>
    <div id="video-holder">
        <span class="close fa fa-times"></span>
        <div class="video">
            <iframe src="http://www.youtube.com/embed/<?=$item['item']['video'];?>?enablejsapi=1"></iframe>
        </div>
    </div>
<?php endif; ?>
