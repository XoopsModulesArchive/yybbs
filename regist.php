<?php

include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsMessage.php';

// クッキーを作る
$ts = MyTextSanitizer::getInstance();
setcookie('xoops_yybbs', implode('<>', [$ts->stripSlashesGPC($_POST['name']), $_POST['email'], $_POST['icon'], $_POST['col'], $ts->stripSlashesGPC($_POST['passwd']), $ts->stripSlashesGPC($_POST['url'])]), time() + 60 * 60 * 24 * 30);

require XOOPS_ROOT_PATH . '/header.php';

$bbs_id = 0;
if (isset($_POST['bbs_id'])) {
    $bbs_id = (int)$_POST['bbs_id'];
} else {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_NO_SELECTED);
}

$bbsHandler = new yybbsBBSHandler($xoopsDB);
$bbsManager = $bbsHandler->get($bbs_id);
if (!$bbsManager) {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_UNDEFINED);
}

// エラーチェック
$ermessage = '';
if (!mb_strlen($_POST['name'])) {
    $ermessage = _MD_YYBBS_ERRMES_NAME;
} elseif (!mb_strlen($_POST['title'])) {
    $ermessage = _MD_YYBBS_ERRMES_TITLE;
} elseif (!mb_strlen($_POST['message'])) {
    $ermessage = _MD_YYBBS_ERRMES_MESSAGE;
} else {
    // uid チェック

    $uid = 0;

    if (is_object($xoopsUser)) {
        $uid = $xoopsUser->uid();
    }

    $messageHandler = new yybbsMessageHandler($xoopsDB);

    $message = $messageHandler->create();

    $message->setVar('serial', $bbsManager->getNextSerial());

    $message->setVar('uid', $uid);

    $message->setVar('bbs_id', $bbs_id);

    $message->setVar('name', $_POST['name']);

    $message->setVar('email', $_POST['email']);

    $message->setVar('url', $_POST['url']);

    $message->setVar('title', $_POST['title']);

    $message->setVar('message', $_POST['message']);

    $message->setVar('icon', $_POST['icon']);

    $message->setVar('col', $_POST['col']);

    $message->setPasswd($_POST['passwd']);

    $message->setVar('parent', $_POST['parent']);

    $message->setVar('inputdate', time());

    $message->setVar('update_date', $message->getVar('inputdate'));

    $message->setVar('ip', $HTTP_SERVER_VARS['REMOTE_ADDR']);

    if ($messageHandler->insert($message)) {
        if (!$bbsHandler->incrementSerial($bbsManager)) {
            print '失敗';
        }

        // 親を処理する

        if ($message->getVar('parent') > 0) {
            $parentMessage = $messageHandler->get($message->getVar('parent'));

            $parentMessage->setVar('update_date', $message->getVar('inputdate'));

            $messageHandler->insert($parentMessage);
        }

        redirect_header('index.php?bbs_id=' . $bbs_id, 1, _MD_YYBBS_POST_TRUE);
    } else {
        redirect_header('index.php?bbs_id=' . $bbs_id, 2, _MD_YYBBS_ERRMES_POST);
    }
}

$GLOBALS['xoopsOption']['template_main'] = 'yybbs_system_message.html';
$xoopsTpl->assign('system_title', _MD_YYBBS_ERRMES);
$xoopsTpl->assign('system_message', $ermessage);
$xoopsTpl->assign('lang_goback', _MD_YYBBS_GO_BBS);

require XOOPS_ROOT_PATH . '/footer.php';
