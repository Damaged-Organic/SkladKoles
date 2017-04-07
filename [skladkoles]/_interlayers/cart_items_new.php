<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $cart_items = $supplied_data;
}

$xml_cart_items = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$argument_0 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[0]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[0]}" : NULL;
$argument_1 = ( !empty($_AREA->{$C_E::_ARGUMENTS}[1]) ) ? "/{$_AREA->{$C_E::_ARGUMENTS}[1]}" : NULL;
$data_landmark = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    $C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}{$argument_0}{$argument_1}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "cart",
    $C_AR::AR_METHOD   => "act"
];

if( !empty($cart_items) ): ?>
    <section class="section item-list-holder">
        <div class="scrollable-holder">
            <div class="scrollable">
                <h2>Список покупок</h2>

                <?php foreach( $cart_items as $value ): ?>

                    <?php
                        $current_data_landmark = NULL;
                        $current_data_landmark = $data_landmark + [
                            $C_E::_REQUEST => [
                                'type'   => $value['type'],
                                'id'     => $value['id'],
                                'action' => ""
                            ]
                        ];
                    ?>

                    <div class="item" data-landmark='<?=json_encode($current_data_landmark, JSON_UNESCAPED_SLASHES);?>'>
                        <div class="photo-holder">
                            <figure>
                                <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
                                <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
                            </figure>
                        </div>
                        <div class="info">

                            <?php switch($value['type']):
                                    case 'rims':
                                    case 'exclusive_rims': ?>
                                    <h3>
                                        <?="{$value['brand']} {$value['model_name']} {$value['code']} {$value['paint']}"?>
                                    </h3>
                                    <ul>
                                        <li>
                                            <p>Межосевое: <span><?="{$value['pcd_stud']}*{$value['pcd_dia']}"?></span></p>
                                        </li>
                                        <li>
                                            <p>Размер: <span><?="{$value['r']} x {$value['w']}"?></span></p>
                                        </li>
                                        <li>
                                            <p>Вылет: <span><?=$value['et']?></span></p>
                                        </li>
                                        <li>
                                            <p>Диаметр: <span><?=$value['ch']?></span></p>
                                        </li>
                                    </ul>
                            <?php break;
                                    case 'tyres':
                                    case 'exclusive_tyres':?>
                                    <h3>
                                        <?="{$value['brand']} {$value['model_name']}"?>
                                    </h3>
                                    <ul>
                                        <li>
                                            <p><?=$xml_cart_items->season->label?>:
                                                <span>
                                                    <?php if( !empty($value['season']) && ($value['season'] == 'S') ): ?>
                                                        <?=$xml_cart_items->season->summer?>
                                                    <?php elseif( !empty($value['season']) && ($value['season'] == 'W') ): ?>
                                                        <?=$xml_cart_items->season->winter?>
                                                    <?php else: ?>
                                                        <?=$xml_cart_items->season->all?>
                                                    <?php endif; ?>
                                                </span>
                                            </p>
                                        </li>
                                        <li>
                                            <p>Радиус: <span><?=$value['r']?></span></p>
                                        </li>
                                        <li>
                                            <p>Размер: <span><?="{$value['w']} / {$value['h']}"?></span></p>
                                        </li>
                                        <li>
                                            <p>Скорость: <span><?=$value['load_rate']?></span></p>
                                        </li>
                                    </ul>
                            <?php break;
                                    case 'spares': ?>
                                        <h3>
                                            Комплектующие
                                        </h3>
                                        <ul>
                                            <li>
                                                <p>Тип:
                                                    <span>
                                                        <?php
                                                            switch($value['item_type'])
                                                            {
                                                                case 'rings':
                                                                    echo("Кольца");
                                                                break;

                                                                case 'bolts':
                                                                    echo("Болты");
                                                                break;

                                                                case 'nuts':
                                                                    echo("Гайки");
                                                                break;

                                                                case 'locks':
                                                                    echo("Секретки");
                                                                break;

                                                                case 'logos':
                                                                    echo("Логотипы");
                                                                break;

                                                                case 'pins':
                                                                    echo("Шпильки");
                                                                break;
                                                            }
                                                        ?>
                                                    </span>
                                                </p>
                                            </li>
                                            <?php if( !empty($value['brand']) ): ?>
                                                <li>
                                                    <p>Бренд: <span><?=$value['brand']?></span></p>
                                                </li>
                                            <?php endif; ?>
                                            <?php if( !empty($value['item_specs']) ): ?>
                                                <li>
                                                    <p>Параметры: <span><?=$value['item_specs']?></span></p>
                                                </li>
                                            <?php endif; ?>
                                            <?php if( !empty($value['size']) ): ?>
                                                <li>
                                                    <p>Размер: <span><?=$value['size']?></span></p>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                <?php break;
                                  endswitch;?>

                            <div class="counter-holder">
                                <span class="counter-btn fa fa-minus action-btn" data-action-type="decrease"></span>
                                <span class="quantity"><?=$_SESSION['user_cart'][$value['type']][$value['id']]?></span>
                                <span class="counter-btn fa fa-plus action-btn" data-action-type="increase"></span>
                            </div>
                            <div class="price-holder">
                                <?php if( $value['promo'] ): ?>
                                    <?php $price = $value['promo']; ?>
                                <?php else: ?>
                                    <?php $price = $value['retail']; ?>
                                <?php endif; ?>
                                <span><?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($price*$_SESSION['user_cart'][$value['type']][$value['id']]);?> UAH</span>
                            </div>
                        </div>
                        <div class="close action-btn" data-action-type="remove">
                            <span class="fa fa-close"></span>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>
<?php endif; ?>
