<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($order_data, $cart_items, $counted_cart_items, $is_admin_version) = $supplied_data;
}

$db_handler = $_BOOT->involve_object('DB_Handler');

$xml_email_order = $_BOOT->involve_object("XML_Handler")->get_xml(
    "email_order",
    $_AREA->{$C_E::_LANGUAGE}
);

$total_price = NULL;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?=$xml_email_order->title?></title>
    <style type="text/css">
        #outlook a{
            padding:0;
        }
        body{
            width:100% !important;
            -webkit-text-size-adjust:100%;
            -ms-text-size-adjust:100%;
            margin:0;
            padding:0;
        }
        .ExternalClass{
            width:100%;
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass td,
        .ExternalClass div{
            line-height: 100%;
        }
        #backgroundTable{
            margin:0;
            padding:0;
            width:100% !important;
            line-height: 100% !important;
        }

        img{
            outline:none;
            text-decoration:none;
            -ms-interpolation-mode: bicubic;
        }
        a img {
            border:none;
        }
        .image_fix {
            display:block;
        }

        p{
            margin: 1em 0;
        }

        h1, h2, h3, h4, h5, h6{
            color: #f77462 !important;
        }
        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a{
            color: blue !important;
        }
        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
            color: red !important;
        }
        h1 a:visited, h2 a:visited, h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited{
            color: purple !important;
        }
        table td{
            border-collapse: collapse;
        }
        table{
            border-collapse:collapse;
            mso-table-lspace:0pt;
            mso-table-rspace:0pt;
        }
        a{
            color: orange;
        }

        #top-bar{
            border-radius: 5px 5px 0 0;
        }
        #footer{
            border-radius: 0px 0px 5px 5px;
        }
        table td[class=w20]{
            width: 20px !important;
        }
        table td[class=w80]{
            width: 80px !important;
        }
        table td[class=w215]{
            width: 215px !important;
        }
        table[class=w400], table td[class=w400]{
            width: 400px !important;
        }
        table td[class=w480]{
            width: 480px !important;
        }
        table[class=w600], table td[class=w600]{
            width: 600px !important;
        }

        @media only screen and (max-width: 600px){
            table, table[class=w600]{
                width: 100% !important;
            }
            table td[class=w80]{
                width: 120px !important;
            }
            table td[class=w215]{
                width: auto !important;
            }
        }
    </style>
</head>
<body>
<!--wrapper start-->
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" bgcolor="#fff">
<!--template gap start-->
<tr>
    <td>
        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" class="w600">
            <tr>
                <td height="20" class="h20"></td>
            </tr>
        </table>
    </td>
</tr>
<!--template gap end-->
<!--header lane start-->
<tr>
    <td>
        <table id="top-bar" cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#5c5c5c">
            <tr><td height="10" class="h10" colspan="4"></td></tr>
            <tr>
                <td width="20" class="w20"></td>
                <td width="480" class="w480">
                    <?php if( !$is_admin_version ): ?>
                        <a href="<?=$this->get_current_link()?>" title="<?=$xml_email_order->unsubscribe->tip?>" style="font-family: Arial; color: #fff; text-decoration: none; font-size: 12px;">
                            <?=$xml_email_order->unsubscribe->label?>
                        </a>
                    <?php endif; ?>
                </td>
                <td width="80" class="w80"></td>
                <td width="20" class="w20"></td>
            </tr>
            <tr><td height="10" class="h10" colspan="4"></td></tr>
        </table>
    </td>
</tr>
<!--header lane end-->
<!--header logo start-->
<tr>
    <td>
        <table cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#6dc5dd">
            <tr><td height="10" class="h10"></td></tr>
            <tr>
                <td class="w215" width="215" align="center">
                    <img class="image_fix" src="<?=$this->load_resource("images/logo-white.png")?>" alt="logo" title="wheels logo" width="215" height="60"/>
                </td>
            </tr>
            <tr><td height="10" class="h10"></td></tr>
        </table>
    </td>
</tr>
<!--header logo end-->
<!--content start-->
<tr>
    <td>
        <table cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#fff">
            <tr>
                <td>
                    <h1 style="font-family: Arial; color: #f77462; font-size: 24px; text-align: center;">
                        <?php if( $is_admin_version ): ?>
                            <?=$xml_email_order->headline->admin?>
                        <?php else: ?>
                            <?=$xml_email_order->headline->user?>
                        <?php endif; ?>
                    </h1>
                    <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: left;">
                        <?php if( $is_admin_version ): ?>
                            <?=$xml_email_order->message->admin?>
                        <?php else: ?>
                            <?=$xml_email_order->message->user?>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#fff">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="1px" bordercolor="#ccc" bgcolor="#fff" width="600">
                                    <tr bgcolor="#efefef">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_order->user_data->order_code?></p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$order_data['order_code']?></p>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#fff">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_order->user_data->name?></p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$order_data['userName']?></p>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#efefef">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_order->user_data->phone?></p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$order_data['userPhone']?></p>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#fff">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;">E-mail</p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=( $order_data['userEmail'] ) ?: '-'?></p>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#efefef">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;">Полный адрес</p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=( $order_data['userLocation'] ) ?: '-'?></p>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#fff">
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;">Дополнительно</p>
                                        </td>
                                        <td>
                                            <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=( $order_data['userMessage'] ) ?: '-'?></p>
                                        </td>
                                    </tr>
                                    <!--
                                    <?php if( !empty($order_data['city']) && !empty($order_data['address']) ): ?>
                                        <tr bgcolor="#efefef">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_order->user_data->city?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$order_data['city']?></p>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#fff">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_order->user_data->address?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$order_data['address']?></p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr bgcolor="#efefef">
                                            <td colspan="2">
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;">Самовывоз</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    -->
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" height="20" class="h20"></td>
            </tr>
            <?php foreach($cart_items as $value): ?>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#fff">
                            <tr>
                                <td valign="middle">
                                    <?php $item_image = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->get_item_image($value); ?>
                                    <img class="image_fix" src="<?=$this->load_resource("items/{$item_image}")?>" alt="<?=$item_image?>" title="wheel" width="150" height="150" />
                                </td>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="1" bordercolor="#ccc" bgcolor="#fff" width="400" class="w400" align="right">
                                        <?php switch($value['type']):
                                                case 'rims':
                                                case 'exclusive_rims': ?>
                                                <tr bgcolor="#5с5с5с">
                                                    <td>
                                                        <p style="font-family: Arial; color: #fff; font-size: 14px; text-align: center;"><?=$value['brand']?></p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #fff; font-size: 14px; text-align: center;"><?="{$value['model_name']} {$value['code']} {$value['paint']}"?></p>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#efefef">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">PCD</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?="{$value['pcd_stud']}*{$value['pcd_dia']}"?></p>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#fff">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">RxW</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?="{$value['r']} x {$value['w']}"?></p>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#efefef">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">ET</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['et']?></p>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#fff">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">CH</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['ch']?></p>
                                                    </td>
                                                </tr>
                                        <?php break;
                                              case 'tyres':
                                              case 'exclusive_tyres': ?>
                                              <tr bgcolor="#5с5с5с">
                                                    <td>
                                                        <p style="font-family: Arial; color: #fff; font-size: 14px; text-align: center;"><?=$value['brand']?></p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #fff; font-size: 14px; text-align: center;"><?=$value['model_name']?></p>
                                                    </td>
                                              </tr>
                                              <tr bgcolor="#efefef">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Сезон</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">
                                                            <?php if( !empty($value['season']) && ($value['season'] == 'S') ): ?>
                                                                Лето
                                                            <?php elseif( !empty($value['season']) && ($value['season'] == 'W') ): ?>
                                                                Зима
                                                            <?php else: ?>
                                                                Всесезон
                                                            <?php endif; ?>
                                                        </p>
                                                    </td>
                                              </tr>
                                              <tr bgcolor="#fff">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">R</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['r']?></p>
                                                    </td>
                                              </tr>
                                              <tr bgcolor="#efefef">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">W/H</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?="{$value['w']} / {$value['h']}"?></p>
                                                    </td>
                                              </tr>
                                              <tr bgcolor="#fff">
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">SR</p>
                                                    </td>
                                                    <td>
                                                        <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['load_rate']?></p>
                                                    </td>
                                              </tr>
                                        <?php break;
                                              case 'spares': ?>
                                              <tr bgcolor="#5с5с5с">
                                                  <td colspan="2">
                                                      <p style="font-family: Arial; color: #fff; font-size: 14px; text-align: center;">
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
                                                      </p>
                                                  </td>
                                              </tr>
                                              <?php if( !empty($value['brand']) ): ?>
                                                  <tr bgcolor="#efefef">
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Бренд</p>
                                                      </td>
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['brand']?></p>
                                                      </td>
                                                  </tr>
                                              <?php endif; ?>
                                              <?php if( !empty($value['item_specs']) ): ?>
                                                  <tr bgcolor="#efefef">
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Параметры</p>
                                                      </td>
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['item_specs']?></p>
                                                      </td>
                                                  </tr>
                                              <?php endif; ?>
                                              <?php if( !empty($value['size']) ): ?>
                                                  <tr bgcolor="#fff">
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Размер</p>
                                                      </td>
                                                      <td>
                                                          <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;"><?=$value['size']?></p>
                                                      </td>
                                                  </tr>
                                              <?php endif; ?>
                                        <?php break;
                                              endswitch;?>
                                        <tr bgcolor="#efefef">
                                            <td>
                                                <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Количество</p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">
                                                    <?=$_SESSION['user_cart'][$value['type']][$value['id']]?> ед.
                                                </p>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#fff">
                                            <td>
                                                <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">Цена</p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5с5с5с; font-size: 14px; text-align: center;">
                                                    <?php if( $value['promo'] ): ?>
                                                        <?php $price = $value['promo']; ?>
                                                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['promo'])?>
                                                    <?php else: ?>
                                                        <?php $price = $value['retail']; ?>
                                                        UAH <?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($value['retail'])?>
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20" class="h20"></td>
                </tr>
                <?php
                    $total_price += ($price * $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("Cart", [$db_handler])->get_quantity_if_item_exists($value['type'], $value['id']));
                ?>
            <?php endforeach; ?>
            <tr>
                <td>
                    <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: right;">
                        <?=$xml_email_order->total_price?>
                        <span style="font-family: Arial; color: #f77462; font-size: 18px;"><?=$_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)->involve_object("CatalogOutput")->convert_item_price($total_price)?> UAH</span>
                    </p>
                    <?php if( !$is_admin_version ): ?>
                        <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: right;">
                            <?=$xml_email_order->bottomline?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!--content end-->
<!--footer start-->
<tr>
    <td>
        <table id="footer" cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#5c5c5c">
            <tr><td height="10" class="h10" colspan="3"></td></tr>
            <tr>
                <td width="20" class="w20"></td>
                <td align="center">
                        <span style="font-family: Arial; color: #fff; font-size: 11px;">
                            <?=date('H:i, d.m.Y', time())?> -
                            <?=$xml_email_order->footerline?>
                            <a href="<?=$this->get_current_link()?>" title="<?=$xml_email_order->site_link->tip?>" style="font-family: Arial; color: #fff; font-size: 11px;">
                                <?=$xml_email_order->site_link->label?>
                            </a>
                        </span>
                </td>
                <td width="20" class="w20"></td>
            </tr>
            <tr><td height="10" colspan="3" class="h10"></td></tr>
        </table>
    </td>
</tr>
<!--footer end-->
<!--template gap start-->
<tr>
    <td>
        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" class="w600">
            <tr>
                <td height="20" class="h20"></td>
            </tr>
        </table>
    </td>
</tr>
<!--template gap end-->
</table>
<!--wrapper end-->
</body>
</html>
