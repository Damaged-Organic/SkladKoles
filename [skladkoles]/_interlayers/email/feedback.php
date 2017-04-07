<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interworker is missing required data");
} else {
    list($feedback_data, $is_admin_version) = $supplied_data;
}

$xml_email_feedback = $_BOOT->involve_object("XML_Handler")->get_xml(
    "email_feedback",
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?=( !empty($feedback_data['subject']) ) ? $feedback_data['subject'] : $xml_email_feedback->title?></title>
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
                            <a href="<?=$this->get_current_link()?>" title="<?=$xml_email_feedback->unsubscribe->tip?>" style="font-family: Arial; color: #fff; text-decoration: none; font-size: 12px;">
                                <?=$xml_email_feedback->unsubscribe->label?>
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
                            <?=$xml_email_feedback->headline->admin?>
                        <?php else: ?>
                            <?=$xml_email_feedback->headline->user?>
                        <?php endif; ?>
                        </h1>
                        <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: left;">
                        <?php if( $is_admin_version ): ?>
                            <?=$xml_email_feedback->message->admin?>
                        <?php else: ?>
                            <?=$xml_email_feedback->message->user?>
                        <?php endif; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" align="center" class="w600" width="600" bgcolor="#fff">
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="1px" bordercolor="#ccc" bgcolor="#fff" width="600">
                                        <tr bgcolor="#efefef">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_feedback->user_data->name?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$feedback_data['name']?></p>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#fff">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_feedback->user_data->email?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$feedback_data['email']?></p>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#efefef">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_feedback->user_data->phone?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$feedback_data['phone']?></p>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#fff">
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;"><?=$xml_email_feedback->user_data->subject?></p>
                                            </td>
                                            <td>
                                                <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: center;">
                                                    <?=( !empty($feedback_data['subject']) ) ? $feedback_data['subject'] : $xml_email_feedback->no_subject?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-family: Arial; color: #6dc5dd; font-size: 16px; text-align: left;">
                                    <?php if( $is_admin_version ): ?>
                                        <?=$xml_email_feedback->user_data->message->admin?>
                                    <?php else: ?>
                                        <?=$xml_email_feedback->user_data->message->user?>
                                    <?php endif; ?>
                                    </p>
                                    <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: left;">
                                        <?=$feedback_data['message']?>
                                    </p>
                                    <?php if( !$is_admin_version ): ?>
                                        <p style="font-family: Arial; color: #5c5c5c; font-size: 14px; text-align: right;">
                                            <?=$xml_email_feedback->bottomline?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
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
                            <?=$xml_email_feedback->footerline?>
                            <a href="<?=$this->get_current_link()?>" title="<?=$xml_email_feedback->site_link->tip?>" style="font-family: Arial; color: #fff; font-size: 11px;">
                                <?=$xml_email_feedback->site_link->label?>
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