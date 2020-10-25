<?php

$modversion['name'] = 'XOOPS YYBBS';
$modversion['version'] = 0.47;
$modversion['description'] = 'YYBBS for XOOPS (parent)';

$modversion['credits'] = 'MINAHITO';
$modversion['author'] = 'DEFAULT TEMPLATE DESIGN <br>KENT ( YY-BOARD PROGRAMER )';
$modversion['license'] = 'PHP Program...GPL see LICENSE<br><br>Default Template...KENT SCRIPT LICENCE<br> ( http://www.kent-web.com/)';
$modversion['official'] = 0;
$modversion['image'] = 'images/yybbs.gif';
$modversion['dirname'] = 'yybbs';

// Template
$modversion['templates'][0]['file'] = 'yybbs_index.html';
$modversion['templates'][0]['description'] = '';
$modversion['templates'][1]['file'] = 'yybbs_forum.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'yybbs_item.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'yybbs_form.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'yybbs_res.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'yybbs_revice.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'yybbs_system_message.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'yybbs_howto.html';
$modversion['templates'][7]['description'] = '';

// Block
$modversion['blocks'][1]['file'] = 'yybbs_new.php';
$modversion['blocks'][1]['name'] = _MI_YYBBS_BNAME1;
$modversion['blocks'][1]['show_func'] = 'b_yybbs_newthread_show';
$modversion['blocks'][1]['options'] = '10|0';
$modversion['blocks'][1]['edit_func'] = 'b_yybbs_newthread_edit';
$modversion['blocks'][1]['template'] = 'yybbs_block_newthread.html';

// Sql
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

$modversion['tables'][0] = 'yybbs';
$modversion['tables'][1] = 'yybbs_faceicon';
$modversion['tables'][2] = 'yybbs_bbs';

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'yybbs_search';

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Menu
$modversion['hasMain'] = 1;
// global $xoopsDB, $_POST, $_GET;
global $xoopsDB;
if (isset($xoopsDB)) {
    $res = $xoopsDB->query('select * from ' . $xoopsDB->prefix('yybbs_bbs') . ' order by priority');

    $num = $xoopsDB->getRowsNum($res);

    if ($num > 1) {
        $i = 0;

        while (false !== ($myrow = $xoopsDB->fetchArray($res))) {
            //			if ( $_POST['bbs_id'] == $myrow['bbs_id'] || $_GET['bbs_id'] == $myrow['bbs_id'] ) {

            //				$modversion['sub'][$i]['name'] = "<b>".$myrow['title']."</b>";

            //			} else {

            $modversion['sub'][$i]['name'] = $myrow['title'];

            //			}

            $modversion['sub'][$i++]['url'] = 'index.php?bbs_id=' . $myrow['bbs_id'];
        }
    }
}
