<?php

include '../../../mainfile.php';
require XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

xoops_cp_header();

if (XoopsSecurity::checkReferer()) {
    if ('v40up' == $_POST['op']) {
        $sql = 'alter table ' . $xoopsDB->prefix('yybbs_bbs') . ' modify ( page_limit tinyint (2) )';

        print $sql . '...';

        if ($xoopsDB->query($sql)) {
            print 'ok';
        } else {
            print '...It failed. Please make this SQL reference and work manually.';
        }

        xoops_cp_footer();

        exit;
    }
}
?>
<table>
    <tr>
        <td>
            <form action='./version_up.php' method='post'>
                <input type='hidden' name='op' value='v40up'>
                <input type='submit' value='v40up'>
            </form>
        </td>
        <td>
            The user who updates from version 0.3x series to version 0.4x series needs this processing.<br>
            In a version 0.3, there is a maximum of an element'page_limit' only to 9.
        </td>
    </tr>
</table>
<?php
xoops_cp_footer();

?>
