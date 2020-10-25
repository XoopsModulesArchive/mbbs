<?php

require dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$start = (isset($_GET['start'])) ? (int)$_GET['start'] : 0;
$pagenum = 10;
$mode = $_REQUEST['mode'] ?? '';
switch ($mode) {
    case 'editItem':
        xoops_cp_header();

        $id = (isset($_REQUEST['id'])) ? (int)$_REQUEST['id'] : 0;
        if ($id < 0) {
            redirect_header('index.php', 3, _AM_MBBS_NO_ITEM);
        }

        $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');
        $criteria = new Criteria('mbbs_id', $id);
        $mbbs = &$mbbsHandler->getObjects($criteria, true);

        $form = new XoopsThemeForm(_AM_MBBS_EDIT_FORM, 'mbbs_form', $_SERVER['PHP_SELF']);
        $form->addElement(new XoopsFormHidden('mode', 'editItem_ok'));
        $form->addElement(new XoopsFormHidden('mbbs_id', $id));
        $form->addElement(new XoopsFormLabel('Name', ($mbbs[$id]->getVar('mbbs_uid')) ? XoopsUserUtility::getUnameFromId($mbbs[$id]->getVar('mbbs_uid')) : $mbbs[$id]->getVar('mbbs_name')));
        $form->addElement(new XoopsFormTextArea('Message', 'mbbs_text', $mbbs[$id]->getVar('mbbs_text', 'E'), 3, 30), true);
        $form->addElement(new XoopsFormButton('', '', _SUBMIT, 'submit'));
        $form->display();

        xoops_cp_footer();
        exit;

    case 'editItem_ok':
        $mbbs_id = (isset($_POST['mbbs_id'])) ? (int)$_POST['mbbs_id'] : 0;
        if ($mbbs_id <= 0) {
        }

        xoops_cp_header();

        $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');
        $mbbs        = $mbbsHandler->get($mbbs_id);
        $mbbs->setVar('mbbs_text', $_POST['mbbs_text']);
        if (!$mbbsHandler->insert($mbbs)) {
            redirect_header('index.php', 3, _AM_MBBS_EDIT_ERROR);
        }

        redirect_header('index.php', 3, _AM_MBBS_EDIT_SUCCESS);
        exit;

    case 'deleteItem':
        $id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

        xoops_cp_header();

        xoops_confirm(['id' => $id, 'mode' => 'deleteItem_ok'], $_SERVER['PHP_SELF'], _AM_MBBS_DELETE_CONFIRM);

        xoops_cp_footer();
        exit;

    case 'deleteItem_ok':
        $id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

        $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');
        $mbbs        = $mbbsHandler->get($id);
        $mbbsHandler->delete($mbbs);

        redirect_header($_SERVER['PHP_SELF'], 3, _AM_MBBS_DELETE_SUCCESS);
        exit;

    case 'deleteAllItem':
        $id = (is_array($_POST['id'])) ? implode('|', $_POST['id']) : 0;
        if (!$id) {
            redirect_header('index.php', 3, 'Error : Delete Error !!'); # @@@
        }

        xoops_cp_header();

        xoops_confirm(['id' => $id, 'mode' => 'deleteAllItem_ok'], $_SERVER['PHP_SELF'], _AM_MBBS_DELETE_CONFIRM);

        xoops_cp_footer();
        exit;

    case 'deleteAllItem_ok':
        $id = (isset($_POST['id'])) ? explode('|', $_POST['id']) : 0;
        $id = (is_array($id)) ? array_map('intval', $id) : 0;

        $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');
        for ($i = 0, $count = count($id); $i < $count; $i++) {
            $mbbs = $mbbsHandler->get($id[$i]);

            $mbbsHandler->delete($mbbs);
        }

        redirect_header($_SERVER['PHP_SELF'], 3, _AM_MBBS_DELETE_SUCCESS);
        exit;

    default:
        xoops_cp_header();

        $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');
        $criteria = new CriteriaCompo();

        $count = $mbbsHandler->getCount();

        echo "<h2>MiniBBS Administration Panel</h2>\n"; # @@@

        //		echo "<div class=\"outer\"><div class=\"even\"></div></div>\n";

        //		echo "<br>\n";

        // echo $count;

        echo "<table class=\"outer\" width=\"100%\">\n";
        echo "<form method=\"post\" action=\"index.php\" id=\"mbbs_form\" name=\"mbbs_form\">\n";
        echo "<input type=\"hidden\" id=\"mode\" name=\"mode\" value=\"deleteAllItem\">\n";
        echo "<tr align=\"center\">\n";
        echo '<th><img src="' . XOOPS_URL . "/modules/mbbs/images/spacer.gif\" width=\"5\" height=\"1\" alt=\"\"></th>\n";
        echo '<th><img src="' . XOOPS_URL . '/modules/mbbs/images/spacer.gif" width="40" height="1" alt="" style="display: block;">' . _AM_MBBS_ID . '<img src="' . XOOPS_URL . "/modules/mbbs/images/spacer.gif\" width=\"40\" height=\"1\" alt=\"\" style=\"display: block;\"></th>\n";
        echo '<th><img src="' . XOOPS_URL . '/modules/mbbs/images/spacer.gif" width="140" height="1" alt="" style="display: block;">' . _AM_MBBS_NAME . '<img src="' . XOOPS_URL . "/modules/mbbs/images/spacer.gif\" width=\"140\" height=\"1\" alt=\"\" style=\"display: block;\"></th>\n";
        echo '<th width="100%"><img src="'
             . XOOPS_URL
             . '/modules/mbbs/images/spacer.gif" width="100%" height="1" alt="" style="display: block;">'
             . _AM_MBBS_MESSAGE
             . '<img src="'
             . XOOPS_URL
             . "/modules/mbbs/images/spacer.gif\" width=\"100%\" height=\"1\" alt=\"\" style=\"display: block;\"></th>\n";
        echo '<th colspan="3"><img src="'
             . XOOPS_URL
             . '/modules/mbbs/images/spacer.gif" width="190" height="1" alt="" style="display: block;">'
             . _AM_MBBS_ACTION
             . '<img src="'
             . XOOPS_URL
             . "/modules/mbbs/images/spacer.gif\" width=\"190\" height=\"1\" alt=\"\" style=\"display: block;\"></th>\n";
        echo "</tr>\n";

        $criteria->setStart($start);
        $criteria->setLimit($pagenum);
        $mbbs = &$mbbsHandler->getObjects($criteria, true);
        $class = 'odd';
        foreach (array_keys($mbbs) as $i) {
            $class = ('odd' == $class) ? 'even' : 'odd';

            echo "<tr class=\"$class\" align=\"center\">\n";

            echo '<th><img src="' . XOOPS_URL . "/modules/mbbs/images/spacer.gif\" width=\"5\" height=\"1\" alt=\"\"></th>\n";

            echo '<td align="right">' . $mbbs[$i]->getVar('mbbs_id') . "</td>\n";

            echo '<td>' . (($mbbs[$i]->getVar('mbbs_uid')) ? XoopsUserUtility::getUnameFromId($mbbs[$i]->getVar('mbbs_uid')) : $mbbs[$i]->getVar('mbbs_name')) . "</td>\n";

            echo '<td><textarea readonly="readonly" style="width: 98%;">' . $mbbs[$i]->getVar('mbbs_text', 'E') . "</textarea></td>\n";

            echo '<td><a href="' . XOOPS_URL . '/modules/mbbs/admin/index.php?mode=editItem&amp;id=' . $mbbs[$i]->getVar('mbbs_id') . '"><img src="' . XOOPS_URL . "/modules/mbbs/images/edit.gif\" alt=\"\"></a></td>\n";

            echo '<td><a href="' . XOOPS_URL . '/modules/mbbs/admin/index.php?mode=deleteItem&amp;id=' . $mbbs[$i]->getVar('mbbs_id') . '"><img src="' . XOOPS_URL . "/modules/mbbs/images/delete.gif\" alt=\"\"></a></td>\n";

            echo '<td><input type="checkbox" id="id[]" name="id[]" value="' . $mbbs[$i]->getVar('mbbs_id') . "\"></td>\n";

            echo "</tr>\n";
        }

        echo "<tr>\n";
        echo "<td colspan=\"7\" align=\"right\" class=\"foot\"><input type=\"checkbox\" id=\"mbbs_checkall\" name=\"mbbs_checkall\" onClick=\"xoopsCheckAll('mbbs_form', 'mbbs_checkall');\">\n";
        echo '<input type="submit" value="' . _DELETE . "\"></td>\n";
        echo "</tr>\n";
        echo "</form>\n";
        echo "</table><br>\n";

        $nav = new XoopsPageNav($count, $pagenum, $start);
        echo $nav->renderImageNav();

        xoops_cp_footer();
        exit;
}
