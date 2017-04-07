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

$db_handler = $_BOOT->involve_object('DB_Handler');

$total_price = NULL;

if( empty($cart_items) ): ?>
    <p class="empty"><?=$xml_cart_items->no_data?></p>
<?php else: ?>
    <ul class="goods-row">
        <li><?=$xml_cart_items->title->image?></li>
        <li><?=$xml_cart_items->title->name?></li>
        <li><?=$xml_cart_items->title->price?></li>
        <li><?=$xml_cart_items->title->quantity?></li>
        <li><?=$xml_cart_items->title->price_sum?></li>
        <li><?=$xml_cart_items->title->delete?></li>
    </ul>
    <?php foreach($cart_items as $value): ?>
        <ul class="goods-row">
            <li>
                <?php if( $value['promotion_id'] || $value['promo'] ): ?>
                    <figure class="ribbon">
                        <img src="<?=$this->load_resource("images/ribbon.png")?>" alt="promotion">
                    </figure>
                <?php endif; ?>
                <figure class="picture">
                    <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
                    <img src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>">
                </figure>
            </li>
            <li>
                <?php switch($value['type']):
                        case 'rims':
                        case 'exclusive_rims': ?>
                        <h2><?="{$value['brand']} {$value['model_name']} {$value['code']} {$value['paint']}"?></h2>
                        <p>PCD:<span><?="{$value['pcd_stud']}*{$value['pcd_dia']}"?></span></p>
                        <p>RxW:<span><?="{$value['r']} x {$value['w']}"?></span></p>
                        <p>ET:<span><?=$value['et']?></span></p>
                        <p>CH:<span><?=$value['ch']?></span></p>
                <?php break;
                        case 'tyres':
                        case 'exclusive_tyres':?>
                        <h2>
                            <?="{$value['brand']} {$value['model_name']}"?>
                        </h2>
                        <p><?=$xml_cart_items->season->label?>:<span>
                            <?php if( !empty($value['season']) && ($value['season'] == 'S') ): ?>
                                <?=$xml_cart_items->season->summer?>
                            <?php elseif( !empty($value['season']) && ($value['season'] == 'W') ): ?>
                                <?=$xml_cart_items->season->winter?>
                            <?php else: ?>
                                <?=$xml_cart_items->season->all?>
                            <?php endif; ?>
                        </span></p>
                        <p>R:<span><?=$value['r']?></span></p>
                        <p>W/H:<span><?="{$value['w']} / {$value['h']}"?></span></p>
                        <p>SR:<span><?=$value['load_rate']?></span></p>
                <?php break;
                        case 'spares': ?>
                        <h2>
                            Комплектующие
                        </h2>
                        <p>Тип:<span>
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
                        </span></p>
                        <?php if( !empty($value['item_specs']) ): ?>
                            <p>Параметры:<span><?=$value['item_specs']?></span></p>
                        <?php endif; ?>
                        <?php if( !empty($value['size']) ): ?>
                            <p>Размер:<span><?=$value['size']?></span></p>
                        <?php endif; ?>
                <?php break;
                      endswitch;?>
            </li>
            <li>
                <?php if( $value['promo'] ): ?>
                    <?php $price = $value['promo']; ?>
                    <span class="old-price">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?></span>
                    <span class="price">
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?>
                    </span>
                <?php else: ?>
                    <?php $price = $value['retail']; ?>
                    <span class="price">
                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?>
                    </span>
                <?php endif; ?>
            </li>
            <li>
                <div class="counter">
                    <span class="plus fa fa-plus-square" data-type="<?=$value['type']?>" data-id="<?=$value['id']?>" data-action="increase"></span>
                    <span class="count"><?=$_SESSION['user_cart'][$value['type']][$value['id']]?> ед.</span>
                    <span class="minus fa fa-minus-square" data-type="<?=$value['type']?>" data-id="<?=$value['id']?>" data-action="decrease"></span>
                </div>
            </li>
            <li><span class="price">UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($price*$_SESSION['user_cart'][$value['type']][$value['id']])?></span></li>
            <li><span class="remove fa fa-times-circle" data-type="<?=$value['type']?>" data-id="<?=$value['id']?>" data-action="remove"></span></li>
        </ul>
        <?php
            $total_price += ($price * $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("Cart", [$db_handler])->get_quantity_if_item_exists($value['type'], $value['id']));
        ?>
    <?php endforeach; ?>
    <div class="total-panel">
		<!--GA-->
        <a href="#" class="buttons nextStep" data-step="1" onClick="javascript: ga('send', 'event', 'button', 'click', 'to-order');"><?=$xml_cart_items->to_order?></a>
        <span class="buttons total"><?=$xml_cart_items->total?><span>UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($total_price)?></span></span>
    </div>
<?php endif; ?>
