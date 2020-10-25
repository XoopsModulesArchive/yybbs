<?php

include '../../../mainfile.php';
require XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';

xoops_cp_header();

$bbsHandler = new yybbsBBSHandler($xoopsDB);

$bbs_id = 0;
if (isset($_POST['bbs_id'])) {
    $bbs_id = $_POST['bbs_id'];
} elseif (isset($_GET['bbs_id'])) {
    $bbs_id = $_GET['bbs_id'];
}

if ($bbs_id) {
    $bbs = $bbsHandler->get($bbs_id);

    if (!$bbs) {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_NO_BBS);
    }
} else {
    $bbs = $bbsHandler->create();
}

$op = '';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
} elseif (isset($_GET['op'])) {
    $op = $_GET['op'];
}

if ('delete' == $op) { // del
    if (1 == $_POST['ok']) {
        if ($bbsHandler->delete($bbs)) {
            redirect_header($_SERVER['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBTRUE);
        } else {
            redirect_header($_SERVER['SCRIPT_NAME'], 1, _MD_A_YYBBS_FALSE);
        }
    } else {
        xoops_confirm(['op' => 'delete', 'bbs_id' => $bbs->getVar('bbs_id'), 'ok' => 1], $HTTP_SERVER_VARS['SCRIPT_NAME'], _MD_A_YYBBS_BBSDELETE);

        xoops_cp_footer();

        exit;
    }
}

if (isset($_POST['submit'])) {
    $bbs->setVar('title', $_POST['title']);

    $bbs->setVar('priority', $_POST['priority']);

    $bbs->setVar('ex', $_POST['ex']);

    $bbs->setPagelimit($_POST['page_limit']);

    if ($bbsHandler->insert($bbs)) {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBTRUE);
    } else {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBFALSE);
    }
}

include './header.php';

// list
$criteria = new Criteria();
$criteria->setSort('priority');

$bbsList = $bbsHandler->getObjects($criteria);
?>
<h4><?php print _MD_A_YYBBS_BBS_LIST; ?></h2>
    <table border='0' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='bg2'>
                <table border='0' cellpadding='4' cellspacing='1'>
                    <tr class='bg3' align='center'>
                        <td>BBS_ID</td>
                        <td><?php print _MD_A_YYBBS_LANG_TITLE; ?></td>
                        <td><?php print _MD_A_YYBBS_LANG_PRIORITY; ?></td>
                        <td><?php print _MD_A_YYBBS_LANG_EX; ?></td>
                        <td><?php print _MD_A_YYBBS_LANG_ACTION; ?></td>
                    </tr>
                    <?php
                    foreach ($bbsList as $b) {
                        ?>
                        <tr class='bg1'>
                            <td align='left'><?php print $b->getVar('bbs_id'); ?></td>
                            <td align='left'><?php print $b->getVar('title', 's'); ?></td>
                            <td align='left'><?php print $b->getVar('priority'); ?></td>
                            <td align='left'><?php print $b->getVar('ex', 's'); ?></td>
                            <td><a href='<?php print $_SERVER['SCRIPT_NAME']; ?>?op=edit&bbs_id=<?php print $b->getVar('bbs_id'); ?>'><?php print _MD_A_YYBBS_LANG_EDIT; ?></a> | <a
                                        href='<?php print $_SERVER['SCRIPT_NAME']; ?>?op=delete&bbs_id=<?php print $b->getVar('bbs_id'); ?>'><?php print _MD_A_YYBBS_LANG_DELETE; ?></a></td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </td>
        </tr>
    </table>
    <?php
    $form = new XoopsThemeForm('YYBBS BBS SETTING', 'bbs', $HTTP_SERVER_VARS['SCRIPT_NAME']);
    $form->addElement(new XoopsFormHidden('bbs_id', $bbs->getVar('bbs_id')));
    $form->addElement(new XoopsFormText(_MD_A_YYBBS_LANG_TITLE, 'title', 64, 64, $bbs->getVar('title', 'e')));
    $form->addElement(new XoopsFormDhtmlTextArea(_MD_A_YYBBS_LANG_EX, 'ex', $bbs->getVar('ex', 'e')));
    $form->addElement(new XoopsFormText(_MD_A_YYBBS_LANG_PRIORITY, 'priority', 3, 3, $bbs->getVar('priority')));
    $form->addElement(new XoopsFormText(_MD_A_YYBBS_LANG_PAGELIMIT, 'page_limit', 3, 3, $bbs->getVar('page_limit')));

    $form->addElement(new XoopsFormButton('', 'submit', _MD_A_YYBBS_LANG_REGIST, 'submit'));
    $form->display();

    xoops_cp_footer();

    ?>
