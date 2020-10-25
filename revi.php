<?php

include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';

require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsMessage.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsIcon.php';

[$name, $email, $icon, $col, $passwd, $url] = yybbsBBSHandler::userInit($xoopsUser);

$bbs_id = 0;
if (isset($_GET['bbs_id'])) {
    $bbs_id = (int)$_GET['bbs_id'];
} elseif (isset($_POST['bbs_id'])) {
    $bbs_id = (int)$_POST['bbs_id'];
} else {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_NO_SELECTED);
}

$messageHandler = new yybbsMessageHandler($xoopsDB);
$bbsHandler = new yybbsBBSHandler($xoopsDB);
$bbs = $bbsHandler->get($bbs_id);
if (!$bbs) {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_UNDEFINED);
}

$xoopsTpl->assign('head_message', _MD_YYBBS_REVICE_HEADMES);

// 修正を指示されたメッセージを得る
$message = null;
if ('edit' == $_POST['mode'] or 'dele' == $_POST['mode']) {
    $message = $messageHandler->getMessageBySerial($bbs->bbs_id(), (int)$_POST['no']);
} elseif ('revice' == $_POST['mode']) {
    $message = $messageHandler->get((int)$_POST['parent']);
}

// 権限およびパスワードのチェック
$flag = false;
if (is_object($message)) {
    if (is_object($xoopsUser)) {
        if ($xoopsUser->isAdmin()) {
            $flag = true;
        } elseif ($message->getVar('uid') == $xoopsUser->uid()) {
            $flag = true;
        } elseif (0 == $message->getVar('uid')) {
            $flag = $message->checkPasswd($_POST['passwd']);
        }
    } elseif (0 == $message->getVar('uid')) {
        $flag = $message->checkPasswd($_POST['passwd']);
    }
}

// パスワード不一致ならエラーへ
if (!$flag) {
    $GLOBALS['xoopsOption']['template_main'] = 'yybbs_system_message.html';

    $xoopsTpl->assign('system_title', _MD_YYBBS_ERRMES);

    $xoopsTpl->assign('system_message', _MD_YYBBS_ERRMES_PASSWD);

    $xoopsTpl->assign('lang_goback', _MD_YYBBS_GO_BBS);

    require XOOPS_ROOT_PATH . '/footer.php';

    exit;
}

// mode = revice なら、修正する
if ('revice' == $_POST['mode']) {
    $message->setVar('name', $_POST['name']);

    $message->setVar('email', $_POST['email']);

    $message->setVar('title', $_POST['title']);

    $message->setVar('message', $_POST['message']);

    $message->setVar('url', $_POST['url']);

    $message->setVar('icon', $_POST['icon']);

    $message->setVar('col', $_POST['col']);

    if ($messageHandler->insert($message)) {
        redirect_header('index.php?bbs_id=' . $bbs_id, 1, _MD_YYBBS_REVICE_TRUE);
    } else {
        redirect_header('index.php?bbs_id=' . $bbs_id, 2, _MD_YYBBS_ERRMES_POST);
    }
} elseif ('dele' == $_POST['mode']) {    // 削除
    if ($messageHandler->delete($message)) {
        redirect_header('index.php?bbs_id=' . $bbs_id, 1, _MD_YYBBS_DELETE_TRUE);
    } else {
        redirect_header('index.php?bbs_id=' . $bbs_id, 2, _MD_YYBBS_ERRMES_POST);
    }
}

$GLOBALS['xoopsOption']['template_main'] = 'yybbs_revice.html';

// フォームのセット
$xoopsTpl->assign('form_action', $_SERVER['SCRIPT_NAME']);
$xoopsTpl->assign('form_mode', 'revice');
$xoopsTpl->assign('form_parent', $message->getVar('id'));    // ここでは parent を使用する

$xoopsTpl->assign('form_bbs_id', $message->getVar('bbs_id'));
$xoopsTpl->assign('form_name', $message->getVar('name', 'e'));
$xoopsTpl->assign('form_email', $message->getVar('email', 'e'));
$xoopsTpl->assign('form_title', $message->getVar('title', 'e'));
$xoopsTpl->assign('form_message', $message->getVar('message', 'e'));
$xoopsTpl->assign('form_url', $message->getVar('url', 'e'));
$xoopsTpl->assign('form_now_icon', $message->getVar('icon'));
$xoopsTpl->assign('form_now_col', $message->getVar('col'));
$xoopsTpl->assign('form_passwd', $_POST['passwd']);

$xoopsTpl->assign('form_icon', $bbs->getFaceIcon());
$xoopsTpl->assign('form_color', $bbs->getColors());

require XOOPS_ROOT_PATH . '/footer.php';
