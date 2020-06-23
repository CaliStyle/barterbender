<?php
namespace Apps\YNC_Comment\Service;

use Phpfox;


defined('PHPFOX') or exit('NO DICE!');

class Emoticon extends \Phpfox_Service
{
    protected $_sTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynccomment_emoticon');
    }

    public function getAll()
    {
        return get_from_cache(['ynccomment.emoticons'], function () {
            return db()->select('*')->from($this->_sTable)->execute('getSlaveRows');
        }, 1);
    }

    public function getRecentEmoticon($iUserId = null)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        $iDay = PHPFOX_TIME - 86000 * 7;
        $aRecent = Phpfox::getService('ynccomment.tracking')->getTracking($iUserId, 'emoticon', $iDay);
        return $aRecent;
    }
}