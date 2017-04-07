<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) : ?>
    <p class="result">Поисковой запрос пуст</p>
<?php elseif( (strlen($supplied_data) < 3) || (strlen($supplied_data) > 32) ) : ?>
    <p class="result">Поисковой запрос должен содержать не менее 3-х и не более 32-х символов</p>
<?php else: ?>
    <p class="result">Результаты поиска по запросу «<span><?=$_IC->xss_escape_output($supplied_data)?></span>» :</p>
<?php endif; ?>