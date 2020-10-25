<?php

include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsMessage.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsIcon.php';

$bbsHandler = new yybbsBBSHandler($xoopsDB);
$messageHandler = new yybbsMessageHandler($xoopsDB);

$bbs_id = 0;
if (isset($_GET['bbs_id'])) {
    $bbs_id = (int)$_GET['bbs_id'];
} elseif (isset($_POST['bbs_id'])) {
    $bbs_id = (int)$_POST['bbs_id'];
} else {    // BBS が指定されていなければ
    $num = $bbsHandler->getCount();

    if (0 == $num) {
        print 'ERR';

        exit;
    } elseif (1 == $num) { // ＢＢＳがひとつしかない
        [$bbs] = $bbsHandler->getObjects();
    } else {    // 複数あるならインデックス表示
        $criteria = new Criteria();

        $criteria->setSort('priority');

        $GLOBALS['xoopsOption']['template_main'] = 'yybbs_index.html';

        $bbsList = $bbsHandler->getObjects($criteria);

        foreach ($bbsList as $b) {
            $xoopsTpl->append('bbs', ['bbs_id' => $b->getVar('bbs_id'), 'bbs_title' => $b->getVar('title', 's'), 'bbs_ex' => $b->getVar('ex', 's')]);
        }

        require XOOPS_ROOT_PATH . '/footer.php';

        exit;
    }
}

[$name, $email, $icon, $col, $passwd, $url] = yybbsBBSHandler::userInit($xoopsUser);

if (!is_object($bbs)) {
    $bbs = $bbsHandler->get($bbs_id);

    if (!$bbs) {
        redirect_header('index.php', 1, _MD_YYBBS_BBS_UNDEFINED);
    }
}

$page = 1;
if (isset($_POST['page'])) {
    $page = (int)$_POST['page'];
} elseif (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}

$start = ($page - 1) * ($bbs->getVar('page_limit'));

$colors = $bbs->getColors();

if (!$col) {
    $col = $colors[0];
}

$GLOBALS['xoopsOption']['template_main'] = 'yybbs_forum.html';

// フォームのセット
$xoopsTpl->assign('form_action', 'regist.php');
$xoopsTpl->assign('form_mode', 'regist');
$xoopsTpl->assign('form_parent', 0);

$xoopsTpl->assign('form_name', $name);
$xoopsTpl->assign('form_email', $email);
$xoopsTpl->assign('form_url', $url);
$xoopsTpl->assign('form_now_icon', $icon);
$xoopsTpl->assign('form_now_col', $col);
$xoopsTpl->assign('form_passwd', $passwd);

$xoopsTpl->assign('form_icon', $bbs->getFaceIcon());
$xoopsTpl->assign('form_color', $colors);

$xoopsTpl->assign('form_bbs_id', $bbs->bbs_id());
$xoopsTpl->assign('bbs_id', $bbs->bbs_id());

$xoopsTpl->assign('bbs_title', $bbs->getVar('title'));

// 親メッセージを得る
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('bbs_id', (int)$bbs->bbs_id(), '='));
$criteria->add(new Criteria('parent', 0, '='));
$criteria->setSort('update_date');
$criteria->setOrder('desc');
$criteria->setLimit($bbs->getVar('page_limit'));
$criteria->setStart($start);

$threadMessages = $messageHandler->getObjects($criteria);

foreach ($threadMessages as $message) {
    $mes_array = $message->getArray();

    $i = 0;

    $criteria_c = new CriteriaCompo();

    $criteria_c->add(new Criteria('parent', $message->getVar('id'), '='));

    $criteria_c->setSort('inputdate');

    $childMessages = $messageHandler->getObjects($criteria_c);

    foreach ($childMessages as $res) {
        $mes_array['res'][$i++] = $res->getArray();
    }

    $xoopsTpl->append('messages', $mes_array);

    unset($mes_array);

    unset($criteria_c);

    unset($childMessages);
}

$countMax = $messageHandler->getCount($criteria);

$pageMax = 0;
if ($countMax > $bbs->getVar('page_limit')) {
    $pageMax = ceil($countMax / $bbs->getVar('page_limit'));
}

$xoopsTpl->assign('count_max', $countMax);
$xoopsTpl->assign('page_max', $pageMax);
$xoopsTpl->assign('now_page', $page);
$xoopsTpl->assign('script_name', $_SERVER['SCRIPT_NAME']);

// page チェック
if ($page < 1) {
    $page = 1;
} elseif ($page > $pageMax) {
    $page = $pageMax;
}

if ($pageMax > 1) {
    if ($page > 1) {
        $xoopsTpl->assign('prev_page', $page - 1);
    }

    if ($page < $pageMax) {
        $xoopsTpl->assign('next_page', $page + 1);
    }
}
for ($i = 1; $i <= $pageMax; $i++) {
    $xoopsTpl->append('pages', $i);
}

require XOOPS_ROOT_PATH . '/footer.php';
