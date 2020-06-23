<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Announcement_Process extends Phpfox_service {

    public function __construct() {
        $this->_sTable = Phpfox::getT('contest_announcement');
    }

    public function add($aVals) {
        $aInsert = array();
        $oFilter = Phpfox::getLib('parse.input');


        $aInsert['user_id'] = $aVals['user_id'];
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $aInsert['headline'] = $oFilter->clean($aVals['headline']);
        $aInsert['link'] = $oFilter->clean($aVals['link']);
        $aInsert['content'] = $oFilter->clean($aVals['content']);
        $aInsert['contest_id'] = $aVals['contest_id'];
        

        if(isset($aVals['announcement_id']) && (int)$aVals['announcement_id'] > 0)
        {
            $iId = $aVals['announcement_id'];
            $iResult = $this->database()->update($this->_sTable, $aInsert, 'announcement_id = ' . (int) $iId);            

            Phpfox::getLib("url")->send('contest',array($aVals['contest_id']), _p('contest.announcement_successfully_updated'));
        }
        else
        {
           $iId = $this->database()->insert($this->_sTable, $aInsert);   
           Phpfox::getLib("url")->send('contest',array($aVals['contest_id']), _p('contest.announcement_added_successfully'));
        }


        return $iId;
    }
    
    public function delete($iAnnouncementId){
        $bResult = $this->database()->delete($this->_sTable,'announcement_id = '.$iAnnouncementId);

        return $bResult;
    }

}
