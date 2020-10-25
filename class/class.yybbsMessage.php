<?php

class yybbsMessage extends XoopsObject
{
    public function __construct($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('bbs_id', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('serial', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);

        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 64);

        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, false, 64);

        $this->initVar('url', XOBJ_DTYPE_URL, 'http://', false, 64);

        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 64);

        $this->initVar('message', XOBJ_DTYPE_TXTAREA, null, false, null);

        $this->initVar('icon', XOBJ_DTYPE_TXTBOX, null, false, 24);

        $this->initVar('col', XOBJ_DTYPE_TXTBOX, null, false, 8);

        $this->initVar('passwd', XOBJ_DTYPE_TXTBOX, null, false, 34);

        $this->initVar('parent', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('inputdate', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('update_date', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 22);

        if (is_array($id)) {
            $this->assignVars($id);
        }
    }

    public function getArray()
    {
        $ret = [];

        $ret['id'] = $this->getVar('id');

        $ret['serial'] = $this->getVar('serial');

        $ret['name'] = $this->getEmailName();

        $ret['url'] = $this->getUrl();

        $ret['title'] = stripslashes($this->getVar('title', 's'));

        $ret['message'] = stripslashes($this->getVar('message', 's'));

        $ret['icon'] = $this->getVar('icon');

        $ret['col'] = $this->getVar('col');

        $ret['parent_id'] = $this->getVar('parent');

        $ret['date'] = $this->getInputDate();

        return ($ret);
    }

    public function getEmailName()
    {
        if ($this->getVar('email')) {
            return (sprintf('<a href="mailto:%s">%s</a>', $this->getVar('email', 's'), stripslashes($this->getVar('name', 's'))));
        }
  

        return ($this->getVar('name'));
    }

    public function getUrl()
    {
        $url = $this->getVar('url', 's');

        if ('http://' != $url and mb_strlen($url) > 7) {
            return ("<a href='" . $url . "' target='_blank'><img src='./images/home.gif' border=0 align=top alt='HomePage'></a>");
        }
  

        return ('');
    }

    public function getInputDate()
    {
        $inputdate = $this->getVar('inputdate');

        return (formatTimestamp($inputdate, 'Y/m/d') . '(' . formatTimestamp($inputdate, 'D') . ') ' . formatTimestamp($inputdate, 'H:i'));
        //		return ( date ( "Y/m/d", $inputdate )."(".date("D",$inputdate ).") ".date ( "H:i", $inputdate ) );
    }

    public function setPasswd($passwd)
    {
        $this->setVar('passwd', md5($passwd));
    }

    public function checkPasswd($passwd)
    {
        if ($this->getVar('passwd', 'e') == md5('')) {
            return false;
        }  

        if ($this->getVar('passwd', 'e') == md5($passwd)) {
            return true;
        }
  

        return false;
    }
}

class yybbsMessageHandler extends XoopsObjectHandler
{
    public function &create($isNew = true)
    {
        $message = new yybbsMessage();

        if ($isNew) {
            $message->setNew();
        }

        return $message;
    }

    public function get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs') . ' WHERE id=' . (int)$id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $message = new yybbsMessage();

                $message->assignVars($this->db->fetchArray($result));

                $message->unsetNew();

                return $message;
            }
        }

        return false;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs');

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
            $message = new yybbsMessage();

            $message->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$message;
            } else {
                $ret[$myrow['id']] = &$message;
            }

            unset($message);
        }

        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('yybbs');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    public function insert(XoopsObject $msg, $force = false)
    {
        if ('yybbsmessage' != get_class($msg)) {
            return false;
        }

        $isNew = $msg->isNew();

        if (!$msg->isDirty()) {
            return true;
        }

        if (!$msg->cleanVars()) {
            return false;
        }

        foreach ($msg->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($isNew) {
            $sql = sprintf(
                'INSERT INTO %s ( serial, uid, name, email, url, title, message, icon, col, passwd, parent, inputdate, update_date, ip, bbs_id ) VALUES ( %u, %u, %s, %s, %s, %s, %s, %s, %s, %s, %u, %u, %u, %s, %u )',
                $this->db->prefix('yybbs'),
                $serial,
                $uid,
                $this->db->quoteString($name),
                $this->db->quoteString($email),
                $this->db->quoteString($url),
                $this->db->quoteString($title),
                $this->db->quoteString($message),
                $this->db->quoteString($icon),
                $this->db->quoteString($col),
                $this->db->quoteString($passwd),
                $parent,
                $inputdate,
                $update_date,
                $this->db->quoteString($ip),
                $bbs_id
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET serial=%u, uid=%u, name=%s, email=%s, url=%s, title=%s, message=%s, icon=%s, col=%s, passwd=%s, parent=%u, inputdate=%u, update_date=%u, ip=%s, bbs_id=%u WHERE id=%u',
                $this->db->prefix('yybbs'),
                $serial,
                $uid,
                $this->db->quoteString($name),
                $this->db->quoteString($email),
                $this->db->quoteString($url),
                $this->db->quoteString($title),
                $this->db->quoteString($message),
                $this->db->quoteString($icon),
                $this->db->quoteString($col),
                $this->db->quoteString($passwd),
                $parent,
                $inputdate,
                $update_date,
                $this->db->quoteString($ip),
                $bbs_id,
                $id
            );
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        if (empty($id)) {
            $id = $this->db->getInsertId();
        }

        //        $msg->assignVar('id', $id);

        return true;
    }

    public function delete($message)
    {
        if ('yybbsmessage' != get_class($message)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE id = %u', $this->db->prefix('yybbs'), $message->getVar('id'));

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

    public function getMessageBySerial($bbs_id, $serial)
    {
        $criteria = new CriteriaCompo();

        $criteria->add(new Criteria('bbs_id', (int)$bbs_id, '='));

        $criteria->add(new Criteria('serial', (int)$serial, '='));

        $criteria->setLimit(1);

        if ($ret = $this->getObjects($criteria)) {
            return $ret[0];
        }
  

        return false;
    }
}
