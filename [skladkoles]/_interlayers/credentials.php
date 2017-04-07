<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$phones = $this->load_inter('worker', 'contacts_phones');
?>
<li class="slogan">
    <!--<p>Официальные представители Disla в Украине</p>-->
    <!-- <p>Колеса для любой погоды. Любой дороги. Любой скорости.</p> -->
    <p>
        <span style="font-size: 1.125em; color: #f77462;">Акция!</span>
        При заказе через звонок - бесплатная доставка +
        <span style="font-size: 1em; color: #6dc5dd;">ПОДАРОК</span>
    </p>
</li>
<span style="position: relative; top: 2.5px; right: 15px; font-size: 1.125em; color: #f77462;">Звони сейчас!</span>
<?php if( !empty($phones) ): ?>
    <li class="phone">
        <i class="fa fa-phone"></i>
        <?php if( !empty($phones[1]) ): ?>
            <a href="tel:<?=str_replace(["(", ")", "-", " "], "", $phones[1]['phone'])?>"><?=$phones[1]['phone']?></a>
        <?php endif ?>
        <?php if( !empty($phones[2]) ): ?>
            <a href="tel:<?=str_replace(["(", ")", "-", " "], "", $phones[2]['phone'])?>"><?=$phones[2]['phone']?></a>
        <?php endif ?>
    </li>
<?php endif; ?>
