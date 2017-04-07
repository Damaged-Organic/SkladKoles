<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    $filter_options = $supplied_data;
}

$transform = function($input_array) {
    $output_array = [];

    foreach( $input_array as $value ) {
        $output_array[$value['title']] = $value['brand'];
    }

    return $output_array;
};

$brands_rims  = array_intersect(
    $transform($filter_options['brands']['rims']),
    $filter_options['modifications']['rims']['brand']
);

$brands_tyres = array_intersect(
    $transform($filter_options['brands']['tyres']),
    $filter_options['modifications']['tyres']['brand']
);

$origin = $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE});
$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $origin,
        'AR_location' => "items_filter_cars_main",
        'AR_method'   => "filter_cars_main"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<div class="intro-info">
    <div id="intro-filter" data-landmark='<?=$data_landmark?>'>
        <section class="auto filter">
            <h2><span class="icon"></span>подобрать по авто</h2>
            <form action="<?=$this->get_current_link('catalog')?>" method="POST" id="filterAuto">
                <select name="<?=$C_E::_REQUEST?>[auto-mark]" id="autoMark" class="cSelect follows" data-placeholder="Марка" data-next="auto-model">
                    <option value selected>Марка</option>
                    <?php foreach($filter_options['brands']['cars'] as $value): ?>
                        <option value="<?=$value['car']?>"><?=$value['title']?></option>
                    <?php endforeach; ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[auto-model]" id="autoModel" class="cSelect follows" data-placeholder="Модель" data-next="auto-year"></select>
                <select name="<?=$C_E::_REQUEST?>[auto-year]" id="autoYear" class="cSelect follows" data-placeholder="Год выпуска" data-next="auto-modification"></select>
                <select name="<?=$C_E::_REQUEST?>[auto-modification]" id="autoModification" class="cSelect follows" data-placeholder="Модификация"></select>
                <input type="checkbox" name="<?=$C_E::_REQUEST?>[available]" value="available" id="autoAvailability">
                <label for="autoAvailability">только в наличии</label>
                <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                <button type="submit" name="<?=$C_E::_REQUEST?>[get_rims_by_car]">показать диски</button>
                <button type="submit" name="<?=$C_E::_REQUEST?>[get_tyres_by_car]">показать шины</button>
            </form>
        </section>
        <section class="discs filter">
            <h2><span class="icon"></span>подобрать диски</h2>
            <form action="<?=$this->get_current_link('subcatalog')?>/rims" method="POST" id="filterDisc">
                <select name="<?=$C_E::_REQUEST?>[brand]" id="discBrand" class="cSelect">
                    <option value selected>бренд</option>
                    <?php foreach($brands_rims as $key => $value): ?>
                        <option value="<?=$value?>"><?=$key?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[rims][r]" id="discRadius" class="cSelect">
                    <option value selected>радиус</option>
                    <?php foreach($filter_options['modifications']['rims']['r'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[rims][w]" id="diskWidth" class="cSelect">
                    <option value selected>ширина</option>
                    <?php foreach($filter_options['modifications']['rims']['w'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[rims][pcd_stud]" id="diskBoltCount" class="cSelect">
                    <option value selected>количество болтов</option>
                    <?php foreach($filter_options['modifications']['rims']['pcd_stud'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[rims][pcd_dia]" id="diskPCD" class="cSelect">
                    <option value selected>межосевое</option>
                    <?php foreach($filter_options['modifications']['rims']['pcd_dia'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <input type="checkbox" name="<?=$C_E::_REQUEST?>[available]" value="available" id="discAvailability">
                <label for="discAvailability">только в наличии</label>
                <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                <button type="submit" name="<?=$C_E::_REQUEST?>[get_by_modifications]">показать диски</button>
            </form>
        </section>
        <section class="tires filter">
            <h2><span class="icon"></span>подобрать шины</h2>
            <form action="<?=$this->get_current_link('subcatalog')?>/tyres" method="POST" id="filterTires">
                <select name="<?=$C_E::_REQUEST?>[brand]" id="tiresBrand" class="cSelect">
                    <option value selected>бренд</option>
                    <?php foreach($brands_tyres as $key => $value): ?>
                        <option value="<?=$value?>"><?=$key?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[tyres][r]" id="tiresRadius" class="cSelect">
                    <option value selected>радиус</option>
                    <?php foreach($filter_options['modifications']['tyres']['r'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[tyres][w]" id="tiresWidth" class="cSelect">
                    <option value selected>ширина</option>
                    <?php foreach($filter_options['modifications']['tyres']['w'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[tyres][h]" id="tiresHeight" class="cSelect">
                    <option value selected>высота профиля</option>
                    <?php foreach($filter_options['modifications']['tyres']['h'] as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
                <select name="<?=$C_E::_REQUEST?>[season]" id="tiresSeason" class="cSelect">
                    <option value selected>сезон</option>
                    <option value="summer">Летние</option>
                    <option value="winter">Зимние</option>
                    <option value="all_season">Всесезонные</option>
                </select>
                <input type="checkbox" name="<?=$C_E::_REQUEST?>[available]" value="available" id="tiresAvailability">
                <label for="tiresAvailability">только в наличии</label>
                <input type="hidden" name="<?=$_IC::IC_TOKEN?>" value="<?=$_SESSION['IC_token']['hash']?>">
                <button type="submit" name="<?=$C_E::_REQUEST?>[get_by_modifications]">показать шины</button>
            </form>
        </section>
    </div>
</div>
<div class="intro-more">
    <span class="fa fa-arrow-circle-down"></span>
    <p>Листайте ниже, чтобы познакомиться с нами</p>
</div>