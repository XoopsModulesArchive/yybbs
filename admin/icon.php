<?php

include '../../../mainfile.php';
require XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsIcon.php';

xoops_cp_header();

$configHandler = xoops_getHandler('config');
$xoopsConfigUser = $configHandler->getConfigsByCat(XOOPS_CONF_USER);

$iconHandler = new yybbsIconHandler($xoopsDB);

$id = 0;
if (isset($_POST['id'])) {
    $id = $_POST['id'];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if ($id) {
    $icon = $iconHandler->get($id);

    if (!$icon) {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_NO_ICON);
    }
} else {
    $icon = $iconHandler->create();
}

$op = $_GET['op'] ?? $_POST['op'] ?? '';

if ('delete' == $op) { // del
    if (1 == $_POST['ok'] || 1 == $_GET['ok']) {
        // 削除を命じられたアイコンを消す

        unlink(XOOPS_UPLOAD_PATH . '/' . $icon->getVar('icon', 'e'));

        // アイコン自体を消す

        if (!$iconHandler->delete($icon)) {
            redirect_header($_SERVER['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBFALSE);

            exit;
        }  

        redirect_header($_SERVER['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBTRUE);

        exit;
    }  

    xoops_confirm(['op' => 'delete', 'id' => $icon->getVar('id'), 'ok' => 1], $HTTP_SERVER_VARS['SCRIPT_NAME'], _MD_A_YYBBS_ICONDELETE);

    xoops_cp_footer();

    exit;
}

if (isset($_POST['submit'])) {
    $icon->setVar('name', $_POST['name']);

    $icon->setVar('priority', $_POST['priority']);

    // アップロードを処理する

    require_once XOOPS_ROOT_PATH . '/class/uploader.php';

    // 設定はアバターを流用

    $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'], $xoopsConfigUser['avatar_maxsize'], $xoopsConfigUser['avatar_width'], $xoopsConfigUser['avatar_height']);

    if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
        $uploader->setPrefix('yyb');

        if ($uploader->upload()) {
            $file = $uploader->getSavedFileName();

            // 古いファイルを消す

            if (!$icon->isNew()) {
                unlink(XOOPS_UPLOAD_PATH . '/' . $icon->getVar('icon', 'e'));
            }

            $icon->setVar('icon', $file);
        } else {
            redirect_header($_SERVER['SCRIPT_NAME'], 1, $uploader->getErrors());
        }
    } else {
        if ($icon->isNew()) {
            redirect_header($_SERVER['SCRIPT_NAME'], 1, _MD_A_YYBBS_NOFOFILE);
        }
    }

    if ($iconHandler->insert($icon)) {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBTRUE);
    } else {
        redirect_header($HTTP_SERVER_VARS['SCRIPT_NAME'], 1, _MD_A_YYBBS_DBFALSE);
    }
}

include './header.php';

// list
$criteria = new Criteria();
$criteria->setSort('priority');

$iconList = $iconHandler->getObjects($criteria);
?>
<h4 style='text-align:left;'><?php print _MD_A_YYBBS_FACEICON_LIST; ?></h4>
<form action='<?php print $_SERVER['SCRIPT_NAME']; ?>' method='post'>
    <input type='hidden' name='op' value='edit_pr'>
    <p align='center'>
    <table border='0' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='bg2'>
                <table border='0' cellpadding='4' cellspacing='1'>
                    <tr class='bg3' align='center'>
                        <td><?php print _MD_A_YYBBS_LANG_ICON; ?></td>
                        <td><?php print _MD_A_YYBBS_LANG_ICONNAME; ?></td>
                        <td><?php print _MD_A_YYBBS_LANG_PRIORITY; ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    foreach ($iconList as $i) {
                        print "<tr class='bg1'>";

                        print "<td align='left'><input type='hidden' value='" . $i->getVar('id') . "' name='id[]'>";

                        print "<img src='" . XOOPS_UPLOAD_URL . '/' . $i->getVar('icon', 'e') . "'></td>";

                        print '<td>' . $i->getVar('name') . '</td>';

                        print "<td align='left'><input type='text' name='new_pr[]' value='" . $i->getVar('priority') . "' size=3 maxlength=3></td>";

                        print "<td><a href='" . $HTTP_SERVER_VARS['SCRIPT_NAME'] . '?op=edit_icon&id=' . $i->getVar('id') . "'>" . _MD_A_YYBBS_LANG_EDIT . "</a> | <a href='" . $HTTP_SERVER_VARS['SCRIPT_NAME'] . '?op=delete&id=' . $i->getVar('id') . "'>" . _MD_A_YYBBS_LANG_DELETE . '</a> </td></tr>';
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
    </p></form>
<?php

$form = new XoopsThemeForm('YYBBS FACE ICON', 'faceicon', $_SERVER['SCRIPT_NAME']);
$form->setExtra('enctype="multipart/form-data"');
$form->addElement(new XoopsFormText(_MD_A_YYBBS_LANG_ICONNAME, 'name', 16, 60, $icon->getVar('name', 'e')));
$form->addElement(new XoopsFormText(_MD_A_YYBBS_LANG_PRIORITY, 'priority', 3, 3, $icon->getVar('priority', 'e')));

$form->addElement(new XoopsFormHidden('id', $icon->getVar('id')));
$form->addElement(new XoopsFormFile(_MD_A_YYBBS_LANG_ICON, 'face_icon', $xoopsConfigUser['avatar_maxsize'], true));
$form->addElement(new XoopsFormButton('', 'submit', _MD_A_YYBBS_LANG_REGIST, 'submit'));

$form->display();

xoops_cp_footer();

?>
