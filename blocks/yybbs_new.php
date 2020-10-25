<?php

function b_yybbs_newthread_show($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $bbs_no = [];

    $bbs_page_limit = [];

    $block['fullmode'] = false;

    if ($options[1]) {
        $block['fullmode'] = true;
    }

    $sql = sprintf('SELECT m.id, m.serial, m.bbs_id, m.uid, m.name, m.title, m.parent, m.inputdate, m.update_date, b.title bbs_title FROM %s m, %s b WHERE m.parent=0 AND m.bbs_id=b.bbs_id ORDER BY update_date DESC', $db->prefix('yybbs'), $db->prefix('yybbs_bbs'));

    if (!$res = $db->query($sql, (int)$options[0], 0)) {
        return false;
    }

    while (false !== ($myrow = $db->fetchArray($res))) {
        $name = $myrow['name'];

        $update_date = $myrow['update_date'];

        // lastpost name

        $sql = sprintf('SELECT name, uid, update_date FROM %s WHERE parent=%u ORDER BY inputdate DESC', $db->prefix('yybbs'), $myrow['id']);

        $res_child = $db->query($sql);

        if ($item = $db->fetchArray($res_child)) {
            $name = $item['name'];

            $update_date = $item['update_date'];
        }

        $message = [];

        $message['id'] = $myrow['id'];

        $message['serial'] = $myrow['serial'];

        $message['bbs_id'] = $myrow['bbs_id'];

        $message['uid'] = $myrow['uid'];

        $message['name'] = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

        $message['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

        $message['parent'] = $myrow['parent'];

        $message['inputdate'] = formatTimestamp($myrow['inputdate'], 'm');

        $message['update_date'] = formatTimestamp($update_date, 'm');

        if ($options[1]) {
            $message['bbs_title'] = htmlspecialchars($myrow['bbs_title'], ENT_QUOTES | ENT_HTML5);
        }

        if (isset($bbs_page_limit[$myrow['bbs_id']])) {
            $bbs_no[$myrow['bbs_id']]++;

            if ($bbs_no[$myrow['bbs_id']] > $bbs_page_limit[$myrow['bbs_id']]) {
                $page = $bbs_no[$myrow['bbs_id']] / $bbs_page_limit[$myrow['bbs_id']];

                if ($bbs_no[$myrow['bbs_id']] % $bbs_page_limit[$myrow['bbs_id']]) {
                    $page++;
                }

                $message['page'] = (int)$page;
            } else {
                $message['page'] = 1;
            }
        } else {
            $sql = sprintf('SELECT page_limit FROM %s WHERE bbs_id=%u', $db->prefix('yybbs_bbs'), $myrow['bbs_id']);

            if ($res2 = $db->query($sql)) {
                if ($ar = $db->fetchArray($res2)) {
                    $bbs_page_limit[$myrow['bbs_id']] = $ar['page_limit'];

                    $bbs_no[$myrow['bbs_id']]++;

                    $message['page'] = 1;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $block['messages'][] = &$message;

        unset($message);
    }

    return $block;
}

function b_yybbs_newthread_edit($options)
{
    $inputtag = "<input type='text' name='options[0]' value='" . $options[0] . "'>";

    $form = sprintf(_MB_YYBBS_DISPLAY, $inputtag);

    $form .= '<br>' . _MB_YYBBS_DISPLAYF . "&nbsp;<input type='radio' name='options[1]' value='1'";

    if (1 == $options[1]) {
        $form .= ' checked';
    }

    $form .= '>&nbsp;' . _YES . "<input type='radio' name='options[1]' value='0'";

    if (0 == $options[1]) {
        $form .= ' checked';
    }

    $form .= '>&nbsp;' . _NO;

    return $form;
}
