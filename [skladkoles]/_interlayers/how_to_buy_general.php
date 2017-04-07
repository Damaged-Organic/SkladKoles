<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_how_to_buy_general = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);
?>
<!-- <article class="item">
    <h2><?=$xml_how_to_buy_general->headline?></h2>
    <p><?=$xml_how_to_buy_general->body->item_1?></p>
    <p><?=$xml_how_to_buy_general->body->item_2?></p>
    <p><?=$xml_how_to_buy_general->body->item_3?></p>
    <p><?=$xml_how_to_buy_general->body->item_4?></p>
</article> -->
<article class="item">
    <h2>Как сделать заказ через сайт?</h2>
    <ul>
        <li><p>Выбрать интересующую модель с помощью фильтра на главной странице или в каталоге</p></li>
        <li><p>Ознакомиться с параметрами выбранной модели</p></li>
        <li><p>Нажать кнопку "Купить" и заполнить нужные поля</p></li>
        <li><p>Нажать "Заказать"</p></li>
    </ul>
    <p>Заказ готов. Ожидайте звонка менеджера!</p>
</article>
<article class="item">
    <h2>Как сделать заказ по телефону?</h2>
    <p>Позвоните по номерам, указанным на сайте. Наши менеджеры проведут полную консультацию и помогут подобрать оптимальный для Вас вариант, в том числе условия доставки и способ оплаты. Звоните!</p>
</article>
