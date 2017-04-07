<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_footer_developers = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$current_year = (new \DateTime())->format('Y');
?>
<h2><?=$xml_footer_developers->headline?></h2>
<!--<small>
    <?=$xml_footer_developers->created_in?> <a href="<?=$C_S::DEVELOPERS_LINK?>" target="_blank" rel="nofollow"><?=$C_S::DEVELOPERS?></a>, 2014 - <?=$current_year?>. <?=$xml_footer_developers->disclaimer?>
</small>-->
<small>
    Web production by <a href="<?=$C_S::DEVELOPERS_LINK?>" target="_blank" rel="nofollow"><?=$C_S::DEVELOPERS?></a>, 2014 - <?=$current_year?>. <?=$xml_footer_developers->disclaimer?>
</small>
<div class="birdy">
    <a href="<?=$C_S::DEVELOPERS_LINK?>" target="_blank" rel="nofollow">
        <img src="<?=$this->load_resource("images/cheers-logo.png")?>" alt="<?=$C_S::DEVELOPERS?>">
    </a>
</div>
