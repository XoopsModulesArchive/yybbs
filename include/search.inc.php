<?php

function yybbs_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;

    $sql = 'SELECT id,bbs_id,uid,title,message,parent,inputdate from ' . $xoopsDB->prefix('yybbs');

    $whereflag = false;

    if (0 != $userid) {
        $sql .= ' where uid=' . $userid . ' ';

        $whereflag = true;
    }

    if (is_array($queryarray) && $count = count($queryarray)) {
        if ($whereflag) {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        }

        $sql .= " ((title LIKE '%$queryarray[0]%' OR message LIKE '%$queryarray[0]%')";

        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";

            $sql .= "(title LIKE '%$queryarray[$i]%' OR message LIKE '%$queryarray[$i]%')";
        }

        $sql .= ') ';
    }

    $sql .= ' ORDER BY inputdate DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $ret = [];

    $i = 0;

    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        if ($myrow['parent']) {
            $ret[$i]['link'] = 'index.php?bbs_id=' . $myrow['bbs_id'] . '&amp;page=' . getpage($myrow['bbs_id'], $myrow['parent']) . '#' . $myrow['id'];
        } else {
            $ret[$i]['link'] = 'index.php?bbs_id=' . $myrow['bbs_id'] . '&amp;page=' . getpage($myrow['bbs_id'], $myrow['id']) . '#' . $myrow['id'];
        }

        $ret[$i]['title'] = $myrow['title'];

        $ret[$i]['time'] = $myrow['inputdate'];

        $ret[$i]['uid'] = $myrow['uid'];

        $i++;
    }

    return $ret;
}

function getpage($bbs_id, $id)
{
    global $xoopsDB;

    // get bbs_data

    $sql = 'select page_limit from ' . $xoopsDB->prefix('yybbs_bbs') . ' where bbs_id=' . $bbs_id;

    $res = $xoopsDB->query($sql);

    $page = 1;

    if ($myrow = $xoopsDB->fetchArray($res)) {
        $page_limit = $myrow['page_limit'];

        $sql = 'select id from ' . $xoopsDB->prefix('yybbs') . ' where parent=0 and bbs_id=' . $bbs_id . ' order by update_date desc';

        $res2 = $xoopsDB->query($sql);

        $i = 1;

        while (false !== ($ar = $xoopsDB->fetchArray($res2))) {
            if ($i > $page_limit) {
                $page = ceil($i / $page_limit);
            }

            if ($ar['id'] == $id) {
                break;
            }

            $i++;
        }
    }

    return ($page);
}
