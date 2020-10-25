<?php

require dirname(__DIR__, 2) . '/mainfile.php';
// require_once XOOPS_ROOT_PATH . '/class/template.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/modules/mbbs/include/functions.php';

// $GLOBALS['xoopsOption']['template_main'] = 'mbbs_form.html';

$mode = $_POST['mode'] ?? '';
switch ($mode) {
    case 'post':
        $error = [];
        $mbbs_uid = $_POST['mbbs_uid'];
        $mbbs_name = $_POST['mbbs_name'];
        $mbbs_text = $_POST['mbbs_text'];
        if (!$mbbs_name) {
            $error['mbbs_name'] = _MBBS_ENAME;
        }
        if (!$mbbs_text) {
            $error['mbbs_text'] = _MBBS_ETEXT;
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($mbbs_text) > 250) {
                $error['mbbs_text'] = _MBBS_ECHAR;
            }
        } else {
            if (mb_strlen($mbbs_text) > 500) {
                $error['mbbs_text'] = _MBBS_ECHAR;
            }
        }

        if (!$error) {
            $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');

            $mbbs = $mbbsHandler->create();

            $mbbs->setVar('mbbs_uid', $mbbs_uid);

            $mbbs->setVar('mbbs_name', $mbbs_name);

            $mbbs->setVar('mbbs_text', $mbbs_text);

            $mbbs->setVar('mbbs_time', time());

            if ($mbbsHandler->insert($mbbs)) {
                redirect_header('popup.php', 3, _MBBS_SUBMIT);

                // echo _MBBS_ERROR';
            }
        }

        xoops_header();

        require XOOPS_ROOT_PATH . '/modules/mbbs/mbbsform.php';
        $form->display();

        echo '<div align="center"><input type="button" value="' . _CLOSE . "\" onClick=\"window.opener.location.reload(); window.close();\"></div>\n";

        xoops_footer();
        exit;

    default:
        //		require XOOPS_ROOT_PATH . '/header.php';
        xoops_header();

        $mbbs_uid = 0;
        $mbbs_name = $mbbs_text = '';
        if (is_object($xoopsUser)) {
            $mbbs_uid = $xoopsUser->getVar('uid');

            $mbbs_name = $xoopsUser->getVar('uname');
        }

        //		$xoopsTpl = new XoopsTpl();
        require XOOPS_ROOT_PATH . '/modules/mbbs/mbbsform.php';
        //		$form->assign($xoopsTpl);
        $form->display();

        echo '<div align="center"><input type="button" value="' . _CLOSE . "\" onClick=\"window.opener.location.reload(); window.close();\"></div>\n";

        // var_dump($form);

        //		require XOOPS_ROOT_PATH . '/footer.php';
        xoops_footer();
        exit;
}
