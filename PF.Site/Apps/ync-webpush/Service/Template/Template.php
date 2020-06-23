<?php
namespace Apps\YNC_WebPush\Service\Template;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;

/**
 * Class Template
 * @package Apps\YNC_WebPush\Service\Template
 */
class Template extends Phpfox_Service
{

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_template');
    }

    /**
     * @param string $sSelect
     * @return array|bool|int|string
     */
    public function getAllTemplates($sSelect = 't.*')
    {
        $sCacheId = $this->cache()->set('yncwebpush_template_manage_' . md5($sSelect));
        $this->cache()->group('yncwebpush_template', $sCacheId);
        if (!$aItems = $this->cache()->get($sCacheId)) {
            $aItems = db()->select($sSelect)
                ->from($this->_sTable, 't')
                ->order('t.time_stamp DESC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aItems);
        }
        return $aItems;
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        if (!$iId) {
            return false;
        }
        return db()->select('*')
            ->from($this->_sTable)
            ->where('template_id =' . (int)$iId)
            ->execute('getRow');
    }

    public function getForManage($sCond, $iLimit = 5, $iPage = 1, &$iCnt)
    {
        $iCnt = db()->select('COUNT(*)')
            ->from($this->_sTable, 't')
            ->where($sCond)
            ->execute('getField');
        $aItems = [];
        if ($iCnt) {
            $aItems = db()->select('t.*')
                ->from($this->_sTable, 't')
                ->where($sCond)
                ->limit($iPage, $iLimit, $iCnt)
                ->order('t.time_stamp DESC')
                ->execute('getSlaveRows');
        }
        return $aItems;
    }

    public function checkExistedTemplate($sName)
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('template_name LIKE \'' . $sName . '\'')
            ->execute('getField');
    }
}