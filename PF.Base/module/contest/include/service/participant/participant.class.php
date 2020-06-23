<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Participant_Participant extends Phpfox_service {

    private $_aParticipants = array(
        '1' => array(
            'participant_id' => 1,
            'full_name' => 'name test 1'
        ),
        '2' => array(
            'participant_id' => 2,
            'full_name' => 'name test 2'
        ),
        '3' => array(
            'participant_id' => 3,
            'full_name' => 'name test 3'
        ),
    );

    public function getWinners($sType = 'recent') {
		$where = '1 = 1 ';
        $aRows = $this->database()->select('*,en.image_path as image_path_parse,en.entry_id as type_entry,en.total_like as total_like_entry, en.total_view as total_view_entry')
                ->from(Phpfox::getT('contest_winner'), 'ctw')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctw.user_id')
				->join(Phpfox::getT('contest_entry'), 'en', 'en.entry_id = ctw.entry_id')
				->join(Phpfox::getT('contest'),'ct','en.contest_id = ct.contest_id')
                ->where($where)
				->order('ctw.time_stamp desc')
                ->limit(6)
                ->execute('getRows');
				
		$aRows = Phpfox::getService('contest.contest')->implementsContestFields($aRows);
		
		return $aRows;
    }

     public function getTopWinners($sType = 'recent') {
        $where = '1 = 1 AND ct.privacy = 0 ';
        $aRows = $this->database()->select('*,en.image_path as image_path_parse,en.entry_id as type_entry,en.total_like as total_like_entry, en.total_view as total_view_entry,en.server_id as server_id')
                ->from(Phpfox::getT('contest_winner'), 'ctw')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctw.user_id')
                ->join(Phpfox::getT('contest_entry'), 'en', 'en.entry_id = ctw.entry_id')
                ->join(Phpfox::getT('contest'),'ct','en.contest_id = ct.contest_id')
                ->where($where)
                ->order('ctw.time_stamp desc')
                ->limit(6)
                ->execute('getRows');
                
        $aRows = Phpfox::getService('contest.contest')->implementsContestFields($aRows);
        
        return $aRows;
    }

    public function getParticipantsOfContest($iContestId) {
        $aRows = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'), 'ctp')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctp.user_id')
                ->where('ctp.contest_id = ' . $iContestId)
                ->execute('getRows');
        return $aRows;
    }

    public function get($iContestId, $iPage = 0, $iLimit) {
       
        $where = 'ctp.contest_id = ' . $iContestId.' and is_joined = 1';
        $iCnt = $this->database()->select('count(*)')
                ->from(Phpfox::getT('contest_participant'), 'ctp')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctp.user_id')
                ->where($where)
                ->execute('getSlaveField');
    
        $aRows = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'), 'ctp')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctp.user_id')
                ->where($where)
                ->limit($iPage, $iLimit)
                ->execute('getRows');
        return array($iCnt,$aRows);
    }
	
	public function getCountParticipant(){
		
		  $iCnt = $this->database()->select('count(distinct ctp.user_id)')
                ->from(Phpfox::getT('contest_participant'), 'ctp')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ctp.user_id')
                ->execute('getSlaveField');
			return $iCnt;
	}


    public function isJoinedContest($iUserId, $iContestId)
    {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('user_id = ' . $iUserId . ' AND contest_id = ' . $iContestId)
                ->execute('getRow');

        if(!$aRow || (isset($aRow['is_joined']) && !$aRow['is_joined']))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public function isFollowedContest($iUserId, $iContestId)
    {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('user_id = ' . $iUserId . ' AND contest_id = ' . $iContestId)
                ->execute('getRow');

        if(!$aRow || (isset($aRow['is_followed']) && !$aRow['is_followed']))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public function isFavoritedContest($iUserId, $iContestId)
    {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('user_id = ' . $iUserId . ' AND contest_id = ' . $iContestId)
                ->execute('getRow');

        if(!$aRow || (isset($aRow['is_favorite']) && !$aRow['is_favorite']))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public function getParticipantById ($iParticipantId)
    {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('participant_id = ' . $iParticipantId )
                ->execute('getRow');

        if(!$aRow)
        {
            return false;
        }
        else
        {
            return $aRow;
        }

    }
	
	public function removeAllFavoriteByContestId($iContestId){
		 $aRows = $this->database()->select('ctp.*,ct.user_id as contest_user_id')
                ->from(Phpfox::getT('contest_participant'), 'ctp')
				->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = ctp.contest_id')
                ->where('ctp.contest_id = ' . $iContestId)
                ->execute('getRows');
		foreach($aRows as $aRow){
			if($aRow['contest_user_id'] == Phpfox::getUserId())
			{
				Phpfox::getService('notification.process')->delete('contest_notice_favorite', $aRow['participant_id'], Phpfox::getUserId());
			}
		}
	}
	
	public function getListFollowingByContestId($contest_id){
		  $aRows = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('contest_id = ' . $contest_id.' and is_followed = 1' )
                ->execute('getRows');
		return $aRows;
	}

    public function getListParticipantByContestId($contest_id){
          $aRows = $this->database()->select('user_id')
                ->from(Phpfox::getT('contest_participant'))
                ->where('contest_id = ' . $contest_id.' and is_joined = 1' )
                ->execute('getRows');
        return $aRows;
    }

     public function getAllParticipantAndFollowerOfContest($iContestId){
        $aRows = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('contest_id = ' . $iContestId.' AND (is_joined = 1 OR is_followed = 1)' )
                ->execute('getRows');
        return $aRows;
    }

    public function getParticipantIdByContestAndUserId($iContestId, $iUserId)
    {
        $iParticipantId = $this->database()->select('participant_id')
                ->from(Phpfox::getT('contest_participant'))
                ->where('contest_id = ' . $iContestId . ' AND user_id = ' . $iUserId )
                ->execute('getSlaveField');
        return $iParticipantId ? $iParticipantId : false;
    }

}