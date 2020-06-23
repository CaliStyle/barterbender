<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Announcement_Announcement extends Phpfox_service {

    public function __construct() {
        $this->_sTable = Phpfox::getT('contest_announcement');
    }
   
    public function get($iContestId, $iPage = 0, $iLimit = 20)
    {
        $where = 'cta.contest_id = ' . $iContestId;
        
        $iCnt = $this->database()->select('count(*)')
                ->from($this->_sTable, 'cta')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cta.user_id')
                ->where($where)
                ->execute('getSlaveField');
       
        $aRows = $this->database()->select('*')
                ->from($this->_sTable, 'cta')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cta.user_id')
                ->where($where)
                ->limit($iPage, $iLimit)
                ->execute('getSlaveRows');
        
        return array($iCnt,$aRows);
    }
}