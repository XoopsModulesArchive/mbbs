<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

require_once XOOPS_ROOT_PATH . '/modules/mbbs/class/textsanitizer.php';

class MbbsPost extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();

        $this->initVar('mbbs_id', XOBJ_DTYPE_INT, null, false);

        $this->initVar('mbbs_uid', XOBJ_DTYPE_INT, null, false);

        $this->initVar('mbbs_name', XOBJ_DTYPE_TXTBOX, null, true, 100);

        $this->initVar('mbbs_text', XOBJ_DTYPE_TXTAREA, null, true, null);

        $this->initVar('mbbs_time', XOBJ_DTYPE_INT, null, false);

        $this->initVar('dobr', XOBJ_DTYPE_INT, 0);
    }

    public function mbbsVar($key, $format = 's')
    {
        $ret = $this->vars[$key]['value'];

        switch ($this->vars[$key]['data_type']) {
            case XOBJ_DTYPE_TXTBOX:
                switch (mb_strtolower($format)) {
                    case 's':
                    case 'show':
                    case 'e':
                    case 'edit':
                        $ts = MyTextSanitizer::getInstance();

                        return $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'p':
                    case 'preview':
                    case 'f':
                    case 'formpreview':
                        $ts = MyTextSanitizer::getInstance();

                        return $ts->htmlSpecialChars($ts->stripSlashesGPC($ret));
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_TXTAREA:
                switch (mb_strtolower($format)) {
                    case 's':
                    case 'show':
                        $ts = &mbbsTextSanitizer::getInstance();
                        $html = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode = (!isset($this->vars['doxcode']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $image = (!isset($this->vars['doimage']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $br = (!isset($this->vars['dobr']['value']) || 1 == $this->vars['dobr']['value']) ? 1 : 0;

                        return $ts->displayTarea($ret, $html, $smiley, $xcode, $image, $br);
                        break 1;
                    case 'e':
                    case 'edit':
                        return htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        $ts = &mbbsTextSanitizer::getInstance();
                        $html = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode = (!isset($this->vars['doxcode']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $image = (!isset($this->vars['doimage']['value']) || 1 == $this->vars['doxcode']['value']) ? 1 : 0;
                        $br = (!isset($this->vars['dobr']['value']) || 1 == $this->vars['dobr']['value']) ? 1 : 0;

                        return $ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts = &mbbsTextSanitizer::getInstance();

                        return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_ARRAY:
                $ret = unserialize($ret);
                break;
            case XOBJ_DTYPE_SOURCE:
                switch (mb_strtolower($format)) {
                    case 's':
                    case 'show':
                        break 1;
                    case 'e':
                    case 'edit':
                        return htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        $ts = MyTextSanitizer::getInstance();

                        return $ts->stripSlashesGPC($ret);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts = MyTextSanitizer::getInstance();

                        return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            default:
                if ('' != $this->vars[$key]['options'] && '' != $ret) {
                    switch (mb_strtolower($format)) {
                        case 's':
                        case 'show':
                            $selected = explode('|', $ret);
                            $options = explode('|', $this->vars[$key]['options']);
                            $i = 1;
                            $ret = [];
                            foreach ($options as $op) {
                                if (in_array($i, $selected, true)) {
                                    $ret[] = $op;
                                }

                                $i++;
                            }

                            return implode(', ', $ret);
                        case 'e':
                        case 'edit':
                            $ret = explode('|', $ret);
                            break 1;
                        default:
                            break 1;
                    }
                }
                break;
        }

        return $ret;
    }
}

class MbbsPostHandler extends XoopsObjectHandler
{
    public function &create($isNew = true)
    {
        $module = new MbbsPost();

        if ($isNew) {
            $module->setNew();
        }

        return $module;
    }

    public function get($id)
    {
        $id = (int)$id;

        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('mbbs') . ' WHERE mbbs_id = ' . $id; # @@@

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $module = new MbbsPost(); # @@@

                $module->assignVars($this->db->fetchArray($result));

                return $module;
            }
        }

        return false;
    }

    public function insert(XoopsObject $module)
    {
        if ('mbbspost' != mb_strtolower(get_class($module))) {
            return false;
        }

        if (!$module->isDirty()) {
            return false;
        }

        if (!$module->cleanVars()) {
            return false;
        }

        foreach ($module->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($module->isNew()) {
            $mbbs_id = $this->db->genId('mbbs_mbbs_id_seq'); # @@@

            $sql = sprintf('INSERT INTO %s (mbbs_id, mbbs_uid, mbbs_name, mbbs_text, mbbs_time) VALUES (%u, %u, %s, %s, %u)', $this->db->prefix('mbbs'), $mbbs_id, $mbbs_uid, $this->db->quoteString($mbbs_name), $this->db->quoteString($mbbs_text), $mbbs_time);
        } else {
            $sql = sprintf('UPDATE %s SET mbbs_uid = %u, mbbs_name = %s, mbbs_text = %s, mbbs_time = %u WHERE mbbs_id = %u', $this->db->prefix('mbbs'), $mbbs_uid, $this->db->quoteString($mbbs_name), $this->db->quoteString($mbbs_text), $mbbs_time, $mbbs_id); # @@@
        }

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        if (empty($mbbs_id)) { # @@@
            $mbbs_id = $this->db->getInsertId(); # @@@
        }

        $module->assignVar('mbbs_id', $mbbs_id); # @@@

        return true;
    }

    public function delete(XoopsObject $module) # @@@
    {
        if ('mbbspost' != mb_strtolower(get_class($module))) {
            return false;
        }

        $id = $module->getVar('mbbs_id'); # @@@
        $sql = sprintf('DELETE FROM %s WHERE mbbs_id = %u', $this->db->prefix('mbbs'), $id); # @@@
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        // $sql = 'SELECT *, COUNT(*) AS count FROM ' . $this->db->prefix('mbbs'); # @@@
        $sql = 'SELECT * FROM ' . $this->db->prefix('mbbs'); # @@@
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            // $sql .= ' GROUP BY *** ORDER BY mbbs_id DESC'; # @@@
            $sql .= ' ORDER BY mbbs_id ' . $criteria->getOrder(); # @@@
            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $module = new MbbsPost();

            $module->assignVars($myrow);

            // $module->setUserCount($myrow['count']);

            if (!$id_as_key) {
                $ret[] = &$module;
            } else {
                $ret[$myrow['mbbs_id']] = &$module;
            }

            unset($module);
        }

        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('mbbs'); # @@@

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->query($sql)) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }
}
