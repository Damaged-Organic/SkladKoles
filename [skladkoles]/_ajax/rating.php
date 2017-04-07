<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( !$_AREA->{$C_AR::IS_AJAX_REQUEST} ) {
    $this->cancel_ajax_request();
} elseif( !$_IC->is_ajax_cooled_down(0.5) ) {
    $this->cancel_ajax_request();
} elseif( $_AREA->{$C_AR::AR_METHOD} !== "rate" ) {
    $this->cancel_ajax_request();
}

if( empty($_AREA->{$C_E::_REQUEST}['item_type']) ||
    empty($_AREA->{$C_E::_REQUEST}['id']) ||
    empty($_AREA->{$C_E::_REQUEST}['score']) ) {
    $this->cancel_ajax_request();
} elseif(
    ($item_type = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['item_type'])) === FALSE ||
    ($id = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['id'])) === FALSE ||
    ($score = $_BOOT->involve_object('InputPurifier')->purge_string($_AREA->{$C_E::_REQUEST}['score'])) === FALSE) {
    $this->cancel_ajax_request();
}

if( !empty($_SESSION['rating'][$item_type][$id]) )
    $this->cancel_ajax_request();

if( $score < 1 || $score > 5 )
    $this->cancel_ajax_request();

$db_handler = $_BOOT->involve_object('DB_Handler');

switch( $item_type )
{
    case 'rims':
    case 'exclusive_rims':
        $item = $this->load_inter('worker', 'obtain_item_details_rims', [$id, NULL]);
    break;

    break;

    case 'tyres':
    case 'exclusive_tyres':
        $item = $this->load_inter('worker', 'obtain_item_details_tyres', [$id, NULL]);
    break;

    default:
        throw new procException("Undefined subdirectory");
    break;
}

$new_score = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
    ->involve_object("CatalogInput")
    ->count_rate($item['item']['rating_score'], $item['item']['rating_votes'], $score);

$result = $_BOOT->assign_namespace($C_N::MEAT_SPECIFIC)
    ->involve_object("CatalogInput")
    ->update_rate($item_type, $item['item']['id'], $new_score);

if( !$result ) {
    $this->cancel_ajax_request();
} else {
    $_SESSION['rating'][$item_type][$item['item']['id']] = TRUE;

    $wheels = [];

    for($i = 5; $i >= 1; $i--) {
        $wheels[] = "<span class='star" . (( round($new_score) >= $i ) ? ' active' : '') . "' data-vote='" . $i . "'></span>";
    }

    $this->satisfy_ajax_request(json_encode([
        'voteCount'   => "<span>" . (++$item['item']['rating_votes']) . "</span> голосов",
        'totalRating' => "<span>" . round($new_score) . "</span> из <span>5</span>",
        'wheels'      => implode(' ', $wheels)
    ]));
}
?>