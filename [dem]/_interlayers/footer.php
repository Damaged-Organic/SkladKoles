<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$current_year = (new \DateTime())->format('Y');
?>
<small class="copyright">Web Production by <span>CHEERS</span>, 2014 - <?=$current_year?>. Все права сохранены.</small>
<small class="copyright">Использование материалов сайта без согласия его авторов запрещено.</small>
<figure><a href="#"><img src="<?=$this->load_resource("images/cheersunlimited.png")?>" alt="CHEERS - Unlimited Web Production"></a></figure>
