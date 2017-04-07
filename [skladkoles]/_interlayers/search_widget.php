<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !empty($supplied_data) ) {
    $search = $_IC->xss_escape_output($supplied_data);
}

$origin = $this->get_current_link('search');
$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $origin,
        'AR_location' => "fast_search",
        'AR_method'   => "search"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<form action="<?=$origin?>" method="POST" id="searchForm" autocomplete="off">
    <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
    <input type="search" name="<?=$C_E::_REQUEST?>[search]" value="<?=( !empty($search) ) ? $search : NULL;?>" placeholder="Поиск ...">
    <button><i class="fa fa-search"></i></button>
</form>
<div class="fastSearch" data-landmark='<?=$data_landmark?>'></div>