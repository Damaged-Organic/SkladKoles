<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    throw new procException("Interlayer is missing required data");
} else {
    list(/*$category, */$items_spares) = $supplied_data;
}

$transform_by_type = function($input_array)
{
    $output_array = [];

    foreach($input_array as $value) {
        $output_array[$value['type']][] = $value;
    }

    return $output_array;
};

$transform_by_spec_step = function($input_array)
{
    $output_array = [];

    foreach($input_array as $value)
    {
        if( !empty($value['size']) ) {
            preg_match("/(\p{L})([\d]+)(\p{L})(?P<step>(\d+)?(\.\d+)?)(.+)?/iu", $value['size'], $step_match);
            $step = "Шаг резьбы: {$step_match['step']}";
        } else {
            $step = "Другие";
        }

        $output_array[$step][] = $value;
    }

    return $output_array;
};

$transform_by_brands = function($input_array)
{
    $output_array = [];

    foreach($input_array as $value) {
        $brand = ( !empty($value['brand']) ) ? "Бренд \"{$value['brand']}\"" : "Другие";
        $output_array[$brand][] = $value;
    }

    return $output_array;
};

$item_spares_by_type = $transform_by_type($items_spares);

foreach($item_spares_by_type as $type => $items_spares)
{
    $items_colspan[$type] = ( (!empty($items_spares[0]['item_specs']) || !empty($items_spares[1]['item_specs']) && (!empty($items_spares[0]['size']) || !empty($items_spares[1]['size']))) ) ? 5 : 4;

    if( !empty($items_spares) )
    {
        switch($type)
        {
            case 'bolts':
            case 'nuts':
                $items_spares_by_brand[$type] = $transform_by_spec_step($items_spares);
            break;

            default:
                $items_spares_by_brand[$type] = $transform_by_brands($items_spares);
            break;
        }

        $brand_condition[$type] = (count($items_spares_by_brand[$type]) == 1 && !empty($items_spares_by_brand[$type]['Другие']));

        $items_spares_deep[$type] = $items_spares;

        ksort($items_spares_deep[$type]);
    } else {
        $items_spares_by_brand[$type] = [];
    }

    ksort($items_spares_by_brand[$type]);
}

$xml_main_structure = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$xml_payment_types = $_BOOT->involve_object("XML_Handler")->get_xml(
    'catalog_subdirectories',
    $_AREA->{$C_E::_LANGUAGE}
);

$landmark_array = [
    $_IC::IC_TOKEN     => $_SESSION['IC_token']['hash'],
    //$C_AR::AR_ORIGIN   => $this->get_current_link("{$_AREA->{$C_E::_DIRECTORY}}/{$_AREA->{$C_E::_ARGUMENTS}[0]}", $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_ORIGIN   => $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE}),
    $C_AR::AR_LOCATION => "cart",
    $C_AR::AR_METHOD   => "add_item",
    $C_E::_REQUEST     => ['item_type' => NULL, 'id' => NULL]
];

$encode_data_landmark = function($landmark_array) {
    return json_encode($landmark_array, JSON_UNESCAPED_SLASHES);
}
?>
<div class="contact-info">
    <p>
        Чтобы корректно подобрать комплектующие для Ваших колес, позвоните на любой из номеров, указанных на сайте. Наши менеджеры предоставят полную информацию и подберут комплектующие для Вашего автомобиля. Звоните!
    </p>
    <div class="btn">
        <a href="<?=$this->get_current_link("contacts")?>">Связаться с нами</a>
    </div>
</div>
<div class="tabs">
    <ul>

        <?php
            $i = 1;
            foreach($items_spares_by_brand as $category => $items_spares_by_brand_deep):
        ?>
            <li class="tabs-label <?php ( $i = 1 ) ? "active" : NULL; ?>">
                <figure>
                    <img src="<?=$this->load_resource("accessories/new/{$category}.jpg")?>" alt="<?=$category?>">
                </figure>
                <span><?=$xml_payment_types->spares->{$category}?></span>
            </li>
        <?php
            $i++;
            endforeach;
        ?>

    </ul>

    <?php foreach($items_spares_by_brand as $category => $items_spares_by_brand_deep): ?>
        <div class="tabs-content">
            <div class="info-holder">
                <h2><?=$xml_main_structure->{$category}->title?></h2>
                <?=$xml_main_structure->{$category}->info?>
                <p class="colorized"><?=$xml_main_structure->bottomline?></p>
            </div>

            <div class="list-holder">
                <?php if( empty($items_spares_deep[$category]) ): ?>
                    <p class="empty"><?=$xml_main_structure->no_data?></p>
                <?php else: ?>
                    <?php if( defined('WHITELIGHT') ): ?>
                        <a href="<?=$this->get_current_link("spares/{$category}", $C_S::SUBSYSTEM_beta);?>" class="edit fa fa-pencil" title="Редактировать комплектующие" target="_blank"></a>
                    <?php endif; ?>
                    <table>
                        <tr>
                            <th>Артикул</th>
                            <?=( !empty($items_spares_deep[$category][0]['item_specs']) || !empty($items_spares_deep[$category][1]['item_specs']) ) ? "<th>{$xml_main_structure->name_label}</th>" : NULL;?>
                            <?=( !empty($items_spares_deep[$category][0]['size']) || !empty($items_spares_deep[$category][1]['size']) ) ? "<th>{$xml_main_structure->size_label}</th>" : NULL;?>
                            <th><?=$xml_main_structure->price_label?></th>
                            <th><?=$xml_main_structure->button_buy?></th>
                        </tr>

                        <?php foreach($items_spares_by_brand_deep as $brand => $items): ?>
                            <?php if( !$brand_condition[$category] ): ?>
                                <tr>
                                    <td class="separator" colspan="<?=$items_colspan[$category]?>"><?=$brand?></td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($items as $value): ?>
                                <?php if($value['retail']): ?>
                                    <tr>
                                        <td><?="S-{$value['unique_code']}"?></td>
                                        <?=( !empty($value['item_specs']) ) ? "<td>{$value['item_specs']}</td>" : NULL;?>
                                        <?=( !empty($value['size']) ) ? "<td>{$value['size']}</td>" : NULL;?>
                                        <td><?=$value['retail']?></td>
                                        <?php
                                            $landmark_array[$C_E::_REQUEST] = ['item_type' => 'spares', 'id' => $value['id']];
                                            $data_landmark = $encode_data_landmark($landmark_array);
                                        ?>
                                        <td>
                                            <a href="#" class="addToCart" data-landmark='<?=$data_landmark?>'>
                                                <span class="loader">
                                                    <span class="fa fa-cog"></span>
                                                </span>
                                                <span class="title">
                                                    <span class="fa fa-shopping-cart"></span>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        <?php endforeach; ?>

                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
