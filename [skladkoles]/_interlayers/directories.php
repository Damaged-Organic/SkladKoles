<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);
?>
<nav class="menu">
    <ul>
        <?php foreach($supplied_data as $value): ?>
            <?php $link_class = ( $_AREA->{$C_E::_DIRECTORY} === $value['directory'] ) ? "class=\"active\"" : NULL; ?>
            <?php
                if( $value['directory'] == 'catalog' ) {
                    if( ($_AREA->{$C_E::_DIRECTORY} == 'subcatalog') ||
                        ($_AREA->{$C_E::_DIRECTORY} == 'spares') ||
                        ($_AREA->{$C_E::_DIRECTORY} == 'item_details') ) {
                        $link_class = "class=\"active\"";
                    }
                }
            ?>
            <li>
                <a href="<?=$this->get_current_link($value['directory'])?>" <?=$link_class?>>
                    <?=$value['title']?>
                </a>
                <?php if( $value['directory'] == 'catalog' ): ?>
                    <ul>
                        <li>
                            <a href="<?=$this->get_current_link('subcatalog/rims')?>">Диски</a>
                        </li>
                        <li>
                            <a href="<?=$this->get_current_link('subcatalog/tyres')?>">Шины</a>
                        </li>
                        <li>
                            <a href="<?=$this->get_current_link('subcatalog/exclusive_rims')?>">Эксклюзивные диски</a>
                        </li>
                        <li>
                            <a href="<?=$this->get_current_link('subcatalog/exclusive_tyres')?>">Эксклюзивные шины</a>
                        </li>
                        <li>
                            <a href="<?=$this->get_current_link('spares')?>">Комплектующие</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
