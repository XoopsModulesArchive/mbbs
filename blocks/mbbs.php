<?php

require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

function b_mbbs_show()
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

    if (empty($xoopsModule) || 'mbbs' != $xoopsModule->getVar('dirname')) {
        $moduleHandler = xoops_getHandler('module');

        $module = $moduleHandler->getByDirname('mbbs');

        $configHandler = xoops_getHandler('config');

        $config = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = &$xoopsModule;

        $config = $xoopsModuleConfig;
    }

    $block = [];

    $start = (isset($_GET['mbbs'])) ? (int)$_GET['mbbs'] : 0;

    $pagenum = $config['show_max'];

    $mbbsHandler = xoops_getModuleHandler('post', 'mbbs');

    $count = $mbbsHandler->getCount();

    $criteria = new CriteriaCompo();

    $criteria->setOrder('DESC');

    $criteria->setStart($start);

    $criteria->setLimit($pagenum);

    $mbbs = &$mbbsHandler->getObjects($criteria, true);

    foreach (array_keys($mbbs) as $i) {
        $blk['name'] = ($mbbs[$i]->getVar('mbbs_uid')) ? XoopsUserUtility::getUnameFromId($mbbs[$i]->getVar('mbbs_uid')) : $mbbs[$i]->getVar('mbbs_name');

        $blk['text'] = $mbbs[$i]->mbbsVar('mbbs_text');

        // $blk['text'] = $mbbs[$i]->getVar('mbbs_text');

        $blk['time'] = formatTimestamp($mbbs[$i]->getVar('mbbs_time'), 'm');

        $block['blk'][] = &$blk;

        unset($blk);
    }

    $nav = new XoopsPageNav($count, $pagenum, $start, 'mbbs');

    $block['pagenav'] = $nav->renderNav();

    $block['btn'] = _MB_MBBS_SUBMIT;

    $block['msgs'] = $config['message'];

    return $block;
}
