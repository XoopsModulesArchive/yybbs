<?php

include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsIcon.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/class.yybbsBBS.php';

$bbs_id = isset($_GET['bbs_id']) ? (int)$_GET['bbs_id'] : 0;

if ($bbs_id) {
    $bbsHandler = new yybbsBBSHandler($xoopsDB);

    $bbs = $bbsHandler->get($bbs_id);

    if (is_object($bbs)) {
        $icons = $bbs->getFaceIcon();

        xoops_header(false); ?>
        <div align="center">
            <h4>画像イメージ</h4>
            <table border=1 cellpadding=5 cellspacing=0 bgcolor="#FFFFFF">
                <tr>
                    <?php
                    $i = 0;

        foreach (array_keys($icons) as $icon) {
            if (0 == $i % 5 and $i) {
                print '</tr><tr>';
            } ?>
                        <td nowrap><img src="<?php print XOOPS_UPLOAD_URL . '/' . $icons[$icon]; ?>" ALIGN=middle alt="<?php print $icon; ?>"><b><?php print $icon; ?></b></td>
                        <?php
                        $i++;
        }

        for (; $i % 5; $i++) {
            print '<td>&nbsp;</td>';
        } ?>

                </tr>
            </table>
            <br>
            <form>
                <input type=button value="ウィンドウを閉じる" onClick="top.close();">
            </form>
        </div>

        <?php
    } else {
        print 'NO_BBS';
    }
} else {
    print 'NO_SETUP';
}

xoops_footer(); ?>

