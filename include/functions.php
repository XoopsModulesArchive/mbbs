<?php

function mbbsSmilies($textarea_id)
{
    $myts = MyTextSanitizer::getInstance();

    $smiles = $myts->getSmileys();

    $emoticon = '';

    if (empty($smileys)) {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        if ($result = $db->query('SELECT * FROM ' . $db->prefix('smiles') . ' WHERE display=1')) {
            while (false !== ($smiles = $db->fetchArray($result))) {
                //hack smilies move for the smilies !!

                $emoticon .= "<img src='" . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smiles['smile_url'], ENT_QUOTES | ENT_HTML5) . "' border='0' onmouseover='style.cursor=\"hand\"' alt='' onclick='xoopsCodeSmilie(\"" . $textarea_id . '", " ' . $smiles['code'] . " \");'>";

                //fin du hack
            }
        }
    } else {
        $count = count($smiles);

        for ($i = 0; $i < $count; $i++) {
            if (1 == $smiles[$i]['display']) {
                //hack bis

                $emoticon .= "<img src='" . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smiles['smile_url'], ENT_QUOTES | ENT_HTML5) . "' border='0' alt='' onclick='xoopsCodeSmilie(\"" . $textarea_id . '", " ' . $smiles[$i]['code'] . " \");' onmouseover='style.cursor=\"hand\"'>";

                //fin du hack
            }
        }
    }

    //hack for more

    $emoticon .= "&nbsp;[<a href='#moresmiley' onmouseover='style.cursor=\"hand\"' alt='' onclick='openWithSelfMain(\"" . XOOPS_URL . '/misc.php?action=showpopups&amp;type=smilies&amp;target=' . $textarea_id . "\",\"smilies\",300,475);'>" . _MORE . '</a>]';

    return $emoticon;
}  //fin du hack

function xoops_module_install_mbbs($var)
{
    $moduleHandler = xoops_getHandler('module');

    $module = $moduleHandler->get($var->getVar('mid'));

    $module->setVar('weight', 0);

    if (!$moduleHandler->insert($module)) {
        return false;
    }

    return true;
}
