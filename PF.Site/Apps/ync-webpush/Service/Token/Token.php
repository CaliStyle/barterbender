<?php
namespace Apps\YNC_WebPush\Service\Token;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;

class Token extends Phpfox_Service
{
    /**
     * Setting constructor.
     */
    private $_sUTTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_browser_token');
        $this->_sUTTable = Phpfox::getT('yncwebpush_user_token');
    }

    public function getAllUserToken($iUserId, $bActive = true, $bOnlyToken = false)
    {
        if (!$iUserId) {
            return false;
        }
        $sCacheId = $this->cache()->set('yncwebpush_user_tokens_' . $iUserId);

        if (!$aTokens = $this->cache()->get($sCacheId)) {

            $aTokens = db()->select('ut.*')
                ->from($this->_sUTTable, 'ut')
                ->join(':user', 'u', 'ut.user_id = u.user_id')
                ->where('ut.user_id = ' . (int)$iUserId . ($bActive ? ' AND ut.is_active = 1' : ''))
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aTokens);
        }
        if ($bOnlyToken && count($aTokens)) {
            $aTokens = array_map(function ($arr) {
                return $arr['token'];
            }, $aTokens);
        }
        return $aTokens;
    }

    public function countUserBrowsers($iUserId)
    {
        $aRows = db()->select('browser, COUNT(token) as total_count')
            ->from($this->_sUTTable)
            ->where('is_active = 1 AND user_id = ' . (int)$iUserId)
            ->group('browser')
            ->execute('getSlaveRows');
        return $aRows;
    }
}