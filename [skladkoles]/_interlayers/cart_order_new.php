<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    list($cart_items, $form_error) = $supplied_data;
}

if( !empty($cart_items) ): ?>
    <section class="section order-holder">
        <div class="scrollable-holder">
            <div class="scrollable">
                <h2>Форма заказа</h2>

                <?php if( $form_error ): ?>
                    <div class="error-holder">
                        <p><?=$form_error?></p>
                    </div>
                <?php endif; ?>

                <ul>
                    <li>
                        <p>Оплата: <span>Приват 24 или терминал, Наличные, Наложенный платеж</span></p>
                    </li>
                    <li>
                        <p>Доставка: <span>Новая Почта, Интайм, Доставка по Киеву, Самовывоз</span></p>
                    </li>
                </ul>
                <p>Самовывозом товар можно забрать из офиса в городе Киев, проспект Академика Глушкова, 6В; самовывоз возможен в любой удобный для Вас день, с 9:00 до 19:00, после согласования заказа с менеджером.</p>
                <p>Так как ассортимент товара очень широк, мы не можем весь его разместить на витринах выставки. Поэтому, просим согласовывать ваш визит заранее.</p>
                <p class="attention">Внимание! Эксклюзивные товары требуют несколько больше времени для доставки, около 2-3 недель.</p>
                <form action="<?=$this->get_current_link('cart/order')?>" method="POST" id="order-form">
                    <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                    <div class="field-holder">
                        <input type="text" name="<?=$C_E::_REQUEST?>[userName]" placeholder="Введите ваше имя" id="order-name" data-rule-required="true" data-msg-required="Поле является обязательным"
                               value="<?php if($_AREA->{$C_E::_REQUEST}['userName']) echo htmlspecialchars($_AREA->{$C_E::_REQUEST}['userName']); ?>">
                    </div>
                    <div class="field-holder">
                        <input type="tel" name="<?=$C_E::_REQUEST?>[userPhone]" placeholder="Введите ваш телефон" id="order-phone" data-rule-required="true" data-msg-required="Поле является обязательным" data-mask="+380 (99) 999-99-99" data-reverse="true"
                               value="<?php if($_AREA->{$C_E::_REQUEST}['userPhone']) echo htmlspecialchars($_AREA->{$C_E::_REQUEST}['userPhone']); ?>">
                    </div>
                    <div class="field-holder">
                        <input type="text" name="<?=$C_E::_REQUEST?>[userEmail]" placeholder="Введите ваш e-mail" id="order-email" data-rule-email="true" data-msg-email="Введите корректный адрес почты"
                               value="<?php if($_AREA->{$C_E::_REQUEST}['userEmail']) echo htmlspecialchars($_AREA->{$C_E::_REQUEST}['userEmail']); ?>">
                    </div>
                    <div class="field-holder">
                        <input type="text" name="<?=$C_E::_REQUEST?>[userLocation]" placeholder="Введите полный адрес" id="order-location"
                               value="<?php if($_AREA->{$C_E::_REQUEST}['userLocation']) echo htmlspecialchars($_AREA->{$C_E::_REQUEST}['userLocation']); ?>">
                    </div>
                    <div class="field-holder">
                        <textarea name="<?=$C_E::_REQUEST?>[userMessage]" id="order-message" placeholder="Дополнительная информация"><?php if($_AREA->{$C_E::_REQUEST}['userMessage']) echo htmlspecialchars($_AREA->{$C_E::_REQUEST}['userMessage']); ?></textarea>
                    </div>
                    <button type="submit" onClick="javascript: ga('send', 'event', 'button', 'click', 'to-order');">
                        <span>Заказать</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>
