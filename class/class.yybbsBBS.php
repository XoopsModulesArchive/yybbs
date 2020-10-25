<?php

class yybbsBBS extends XoopsObject
{
    public $_bHandler = null;

    public $_iHandler = null;

    public function __construct($id = null)
    {
        $this->initVar('bbs_id', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 64);

        $this->initVar('page_limit', XOBJ_DTYPE_INT, 5, false);

        $this->initVar('ex', XOBJ_DTYPE_TXTAREA, null, false, null);

        $this->initVar('howto', XOBJ_DTYPE_TXTAREA, null, false, null);

        $this->initVar('color', XOBJ_DTYPE_TXTBOX, null, false, 128);

        $this->initVar('template_dir', XOBJ_DTYPE_TXTBOX, null, false, 64);

        $this->initVar('windows_opt', XOBJ_DTYPE_TXTBOX, null, false, 16);

        $this->initVar('serial', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('xooops_use', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('priority', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('status', XOBJ_DTYPE_INT, 0, false);

        if (is_array($id)) {
            $this->assignVars($id);
        }
    }

    public function setPagelimit($limit)
    {
        $limit = (int)$limit;

        if ($limit > 0 and $limit < 100) {
            $this->setVar('page_limit', $limit);
        }
    }

    public function bbs_id()
    {
        return $this->getVar('bbs_id');
    }

    public function getColors()
    {
        return explode(' ', $this->getVar('color'));
    }

    public function getNextSerial()
    {
        return $this->getVar('serial');
    }

    public function incrementSerial()
    {
        $this->setVar('serial', $this->getVar('serial') + 1);

        $bbsHandler = $this->_get_bHandler();

        $bbsHandler->insert($this);
    }

    public function getFaceIcon()
    {
        $ret = [];

        $iconHandler = $this->_get_iHandler();

        $criteria = new Criteria();

        $criteria->setSort('priority');

        $icons = $iconHandler->getObjects($criteria);

        foreach ($icons as $i) {
            $ret[$i->getVar('name')] = $i->getVar('icon');
        }

        return ($ret);
    }

    public function _get_bHandler()
    {
        if ('yybbshandler' == !get_class($this->_bHandler)) {
            $this->_bHandler = new yybbsBBSHandler($GLOBALS['xoopsDB']);
        }

        return $this->_bHandler;
    }

    public function _get_iHandler()
    {
        if ('yybbsicon' == !get_class($this->_iHandler)) {
            $this->_iHandler = new yybbsIconHandler($GLOBALS['xoopsDB']);
        }

        return $this->_iHandler;
    }
}

class yybbsBBSHandler extends XoopsObjectHandler
{
    public function &create($isNew = true)
    {
        $bbs = new yybbsBBS();

        if ($isNew) {
            $bbs->setNew();
        }

        return $bbs;
    }

    public function get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs_bbs') . ' WHERE bbs_id=' . (int)$id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $bbs = new yybbsBBS();

                $bbs->assignVars($this->db->fetchArray($result));

                $bbs->unsetNew();

                return $bbs;
            }
        }

        return false;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs_bbs');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $bbs = new yybbsBBS();

            $bbs->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$bbs;
            } else {
                $ret[$myrow['id']] = &$bbs;
            }

            unset($bbs);
        }

        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('yybbs_bbs');

        if (isset($criteria)) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    public function insert(XoopsObject $bbs, $force = false)
    {
        if ('yybbsbbs' != get_class($bbs)) {
            return false;
        }

        $isNew = $bbs->isNew();

        if (!$bbs->isDirty()) {
            return true;
        }

        if (!$bbs->cleanVars()) {
            return false;
        }

        foreach ($bbs->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($isNew) {
            $bbs_id = $this->db->genId($this->db->prefix('yybbs_bbs') . '_bbs_id_seq');

            $sql = sprintf('INSERT INTO %s ( bbs_id, title, ex, serial, priority, page_limit ) VALUES ( %u, %s, %s, %u, %u, %u )', $this->db->prefix('yybbs_bbs'), $bbs_id, $this->db->quoteString($title), $this->db->quoteString($ex), $serial, $priority, $page_limit);
        } else {
            $sql = sprintf('UPDATE %s SET title=%s, ex=%s, serial=%u, priority=%u, page_limit=%u WHERE bbs_id=%u', $this->db->prefix('yybbs_bbs'), $this->db->quoteString($title), $this->db->quoteString($ex), $serial, $priority, $page_limit, $bbs_id);
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        if (empty($bbs_id)) {
            $bbs_id = $this->db->getInsertId();

            $bbs->setVar('bbs_id', $bbs_id);
        }

        return true;
    }

    public function delete(XoopsObject $bbs, $force = false)
    {
        if ('yybbsbbs' != get_class($bbs)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE bbs_id = %u', $this->db->prefix('yybbs_bbs'), $bbs->getVar('bbs_id'));

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        return true;
    }

    public function userInit($xoopsUser)
    {
        global $HTTP_COOKIE_VARS;

        if (isset($HTTP_COOKIE_VARS['xoops_yybbs'])) {
            $ts = MyTextSanitizer::getInstance();

            $tmp = explode('<>', $HTTP_COOKIE_VARS['xoops_yybbs']);

            $name = $ts->stripSlashesGPC($ts->stripSlashesGPC($tmp[0]));

            $email = $tmp[1];

            $icon = $tmp[2];

            $col = $tmp[3];

            $passwd = $ts->stripSlashesGPC($ts->stripSlashesGPC($tmp[4]));

            $url = $ts->stripSlashesGPC($ts->stripSlashesGPC($tmp[5]));
        } else {
            if (is_object($xoopsUser)) {
                $name = $xoopsUser->getVar('name');

                if ('' == $name) {
                    $name = $xoopsUser->getVar('uname');
                }
            }

            $email = '';

            $icon = '';

            $url = 'http://';

            $col = 0;
        }

        return ([$name, $email, $icon, $col, $passwd, $url]);
    }

    public function incrementSerial($bbs, $force = false)
    {
        $bbs->setVar('serial', $bbs->getVar('serial') + 1);

        return ($this->insert($bbs, $force));
    }
}
