<?php

include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';

require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsMessage.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsIcon.php';

[$name, $email, $icon, $col, $passwd, $url] = yybbsBBSHandler::userInit($xoopsUser);

$GLOBALS['xoopsOption']['template_main'] = 'yybbs_res.html';

$bbs_id = 0;
if (isset($_POST['bbs_id'])) {
    $bbs_id = (int)$_POST['bbs_id'];
} else {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_NO_SELECTED);
}

$bbsHandler = new yybbsBBSHandler($xoopsDB);
$messageHandler = new yybbsMessageHandler($xoopsDB);

$bbs = $bbsHandler->get($bbs_id);
if (!is_object($bbs)) {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_UNDEFINED);
}

$parentMessage = $messageHandler->get($_POST['no']);
if (!is_object($parentMessage)) {
    redirect_header('index.php', 1, _MD_YYBBS_BBS_REQERR);
}

$tmp = explode("\n", $parentMessage->getVar('message', 'e'));
$mes = '';
foreach ($tmp as $i) {
    $mes .= '> ' . $i . "\n";
}

$colors = $bbs->getColors();
if (!$col) {
    $col = $colors[0];
}

$head_message = sprintf(_MD_YYBBS_RESPONCE_HEADMES, $parentMessage->getVar('serial'));
$xoopsTpl->assign('head_message', $head_message);

$xoopsTpl->assign('serial', $parentMessage->getVar('serial'));
$xoopsTpl->assign('title', $parentMessage->getVar('title', 's'));
$xoopsTpl->assign('name', $parentMessage->getEmailName());
$xoopsTpl->assign('date', $parentMessage->getInputDate());
$xoopsTpl->assign('message', $parentMessage->getVar('message', 's'));

// getRes
$criteria_c = new CriteriaCompo();
$criteria_c->add(new Criteria('parent', $parentMessage->getVar('id'), '='));
$criteria_c->setSort('inputdate');
$childMessages = $messageHandler->getObjects($criteria_c);
foreach ($childMessages as $res) {
    $xoopsTpl->append('res', $res->getArray());
}

$xoopsTpl->assign('form_action', 'regist.php');
$xoopsTpl->assign('form_mode', 'regist');
$xoopsTpl->assign('form_parent', $parentMessage->getVar('id'));

$xoopsTpl->assign('form_title', 'Re:' . $parentMessage->getVar('title', 'e'));
$xoopsTpl->assign('form_message', $mes);

$xoopsTpl->assign('form_name', $name);
$xoopsTpl->assign('form_email', $email);
$xoopsTpl->assign('form_url', $url);
$xoopsTpl->assign('form_now_icon', $icon);
$xoopsTpl->assign('form_now_col', $col);
$xoopsTpl->assign('form_passwd', $passwd);

$xoopsTpl->assign('form_icon', $bbs->getFaceIcon());
$xoopsTpl->assign('form_color', $colors);
$xoopsTpl->assign('form_bbs_id', $bbs->bbs_id());

require XOOPS_ROOT_PATH . '/footer.php';
