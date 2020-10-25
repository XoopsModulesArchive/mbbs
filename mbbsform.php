<?php

$form = new XoopsThemeForm(_MBBS_POST_FORM, 'mbbs_form', $_SERVER['PHP_SELF']);
$form->addElement(new XoopsFormHidden('mode', 'post'));
$form->addElement(new XoopsFormHidden('mbbs_uid', $mbbs_uid));
if ($mbbs_uid) {
    $form->addElement(new XoopsFormHidden('mbbs_name', $mbbs_name));

    $form->addElement(new XoopsFormLabel(_MBBS_NAME, $mbbs_name));
} else {
    $form->addElement(new XoopsFormText(_MBBS_NAME . (isset($error['mbbs_name']) ? ' ' . $error['mbbs_name'] : ''), 'mbbs_name', 40, 25, $mbbs_name), true);
}
$form->addElement(new XoopsFormTextArea(_MBBS_MESSAGE . (isset($error['mbbs_text']) ? ' ' . $error['mbbs_text'] : ''), 'mbbs_text', $mbbs_text, 3, 30), true);
// echo で強制的に表示されるので改造の必要有り
$emoticon = mbbsSmilies('mbbs_text');
$form->addElement(new XoopsFormLabel(_MBBS_EMOTICON, $emoticon));
$form->addElement(new XoopsFormButton('', '', _SUBMIT, 'submit'));
