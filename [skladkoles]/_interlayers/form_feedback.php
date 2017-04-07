<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

$xml_form_feedback = $_BOOT->involve_object("XML_Handler")->get_xml(
    basename(__FILE__, '.php'),
    $_AREA->{$C_E::_LANGUAGE}
);

$data_landmark = json_encode(
    array(
        'IC_token'    => $_SESSION['IC_token']['hash'],
        'AR_origin'   => $this->get_current_link($_AREA->{$C_E::_DIRECTORY}, $_AREA->{$C_E::_LANGUAGE}),
        'AR_location' => "feedback",
        'AR_method'   => "send_feedback"
    ),
    JSON_UNESCAPED_SLASHES
);
?>
<section class="centered">
    <h1><?=$xml_form_feedback->headline?></h1>
    <form action="" method="POST" id="contactsForm" data-landmark='<?=$data_landmark?>'>
        <input type="text" name="<?=$C_E::_REQUEST?>[name]" value="" placeholder="<?=$xml_form_feedback->form->name?>" id="userName" data-rule-required="true">
        <input type="email" name="<?=$C_E::_REQUEST?>[email]" value="" placeholder="<?=$xml_form_feedback->form->email?>" id="userEmail" data-rule-required="true" data-rule-email="true">
        <input type="tel" name="<?=$C_E::_REQUEST?>[phone]" value="" placeholder="+380 ( __ ) ___ __ __" id="userPhone" data-rule-required="true">
        <input type="text" name="<?=$C_E::_REQUEST?>[subject]" value="" placeholder="<?=$xml_form_feedback->form->subject?>" id="userTheme">
        <textarea name="<?=$C_E::_REQUEST?>[message]" id="userMsg" placeholder="<?=$xml_form_feedback->form->message?>" data-rule-required="true"></textarea>
        <button type="submit"><?=$xml_form_feedback->submit_button?></button>
    </form>
</section>