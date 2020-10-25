<?php

$modversion['name'] = _MI_MBBS_NAME;
$modversion['version'] = 0.93;
$modversion['description'] = _MI_MBBS_DESC;
$modversion['credits'] = 'c & c';
$modversion['author'] = 'c & c';
$modversion['help'] = '';
$modversion['license'] = '';
$modversion['official'] = 0;
$modversion['image'] = 'images/slogo.gif';
$modversion['dirname'] = basename(__DIR__);

$modversion['onInstall'] = 'include/functions.php';

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][] = 'mbbs';

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

$modversion['templates'][1]['file'] = 'mbbs_form.html';
$modversion['templates'][1]['description'] = '';

$modversion['blocks'][1]['file'] = 'mbbs.php';
$modversion['blocks'][1]['name'] = _MI_MBBS_BNAME;
$modversion['blocks'][1]['description'] = _MI_MBBS_BDESC;
$modversion['blocks'][1]['show_func'] = 'b_mbbs_show';
$modversion['blocks'][1]['options'] = '';
$modversion['blocks'][1]['edit_func'] = '';
$modversion['blocks'][1]['template'] = 'mbbs_block_show.html';

$modversion['hasMain'] = 1;

$modversion['config'][1] = [
    'name' => 'message',
'title' => '_MI_MBBS_MESSAGE',
'description' => '_MI_MBBS_MESSAGE_DESC',
'formtype' => 'textarea',
'valuetype' => 'text',
'default' => '',
'options' => '',
];
$modversion['config'][] = [
    'name' => 'show_max',
'title' => '_MI_MBBS_MAX_PER_PAGE',
'description' => '_MI_MBBS_MAX_PER_PAGE_DESC',
'formtype' => 'select',
'valuetype' => 'int',
'default' => '5',
'options' => ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30],
];
