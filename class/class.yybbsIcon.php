<?php

class yybbsIcon extends XoopsObject
{
    public function __construct($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 32);

        $this->initVar('icon', XOBJ_DTYPE_TXTBOX, null, false, 30);

        $this->initVar('bbs_id', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('priority', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('type', XOBJ_DTYPE_INT, 0, false);

        if (is_array($id)) {
            $this->assignVars($id);
        }
    }
}

class yybbsIconHandler extends XoopsObjectHandler
{
    public function &create($isNew = true)
    {
        $icon = new yybbsIcon();

        if ($isNew) {
            $icon->setNew();
        }

        return $icon;
    }

    public function get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs_faceicon') . ' WHERE id=' . (int)$id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $icon = new yybbsIcon();

                $icon->assignVars($this->db->fetchArray($result));

                $icon->unsetNew();

                return $icon;
            }
        }

        return false;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('yybbs_faceicon');

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
            $icon = new yybbsIcon();

            $icon->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$icon;
            } else {
                $ret[$myrow['id']] = &$icon;
            }

            unset($icon);
        }

        return $ret;
    }

    public function insert(XoopsObject $icon, $force = false)
    {
        if ('yybbsicon' != get_class($icon)) {
            return false;
        }

        $isNew = $icon->isNew();

        if (!$icon->isDirty()) {
            return true;
        }

        if (!$icon->cleanVars()) {
            return false;
        }

        foreach ($icon->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($isNew) {
            $id = $this->db->genId($this->db->prefix('yybbs_faceicon') . '_id_seq');

            $sql = sprintf("INSERT INTO %s ( id, name, icon, priority ) VALUES ( %u, %s, '%s', %u )", $this->db->prefix('yybbs_faceicon'), $id, $this->db->quoteString($name), $icon, $priority);
        } else {
            $sql = sprintf("UPDATE %s SET name=%s, icon='%s', priority=%u WHERE id=%u", $this->db->prefix('yybbs_faceicon'), $this->db->quoteString($name), $icon, $priority, $id);
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        //        if (empty($id)) {

        //            $icon->setVar ( 'id', $this->db->getInsertId() );

        //        }

        return true;
    }

    public function delete(XoopsObject $icon, $force = false)
    {
        if ('yybbsicon' != get_class($icon)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE id = %u', $this->db->prefix('yybbs_faceicon'), $icon->getVar('id'));

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
}
