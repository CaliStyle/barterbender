<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Participant_Process extends Phpfox_service {

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('contest_participant');
    }

    public function joinContest ($iContestId, $iUserId)
    {
        $iParticipantId = Phpfox::getService('contest.participant.process')->insertNewParticipantEntryIfNeccessary($iContestId, $iUserId);


        //update old entry
        $aUpdate = array(
            'is_joined' => 1
            );

        if($iId = $this->database()->update($this->_sTable, $aUpdate, 'participant_id = ' . $iParticipantId))
		{
			$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
			if(isset($aContest['total_participant']))
			$this->database()->update(Phpfox::getT('contest'), array('total_participant' => $aContest['total_participant'] + 1), 'contest_id = ' . $iContestId);


            Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('join_contest', $iContestId);

		}
		return $iId;
    }

    public function leaveContest($iContestId, $iUserId)
    {
        if(Phpfox::getService('contest.participant')->isJoinedContest($iUserId, $iContestId))
        {
             $aUpdate = array(
                'is_joined' => 0
                );

            if($iId = $this->database()->update($this->_sTable, $aUpdate, 'user_id = ' . $iUserId . ' AND contest_id = ' . $iContestId)){
            	$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
				if(isset($aContest['total_participant']))
					$this->database()->update(Phpfox::getT('contest'), array('total_participant' => $aContest['total_participant'] - 1), 'contest_id = ' . $iContestId);


                Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('leave_contest', $iContestId);

				return $iId;
            }
        }
        else
        {
            return false;
        }
       
    }

    public function followContest($iContestId, $iUserId, $iType)
    {

        $iParticipantId = Phpfox::getService('contest.participant.process')->insertNewParticipantEntryIfNeccessary($iContestId, $iUserId);

        if ($iType == 1) {
            //follow
            $aUpdate = array(
                'is_followed' => 1
                );

            $bResult = $this->database()->update($this->_sTable, $aUpdate, 'participant_id = ' . $iParticipantId);
            if($bResult)
            {
                 Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('follow_contest', $iContestId);
            }

            return $bResult;

        } else if ($iType == 0) {
            //un follow
             $aUpdate = array(
                'is_followed' => 0
                );

            return $this->database()->update($this->_sTable, $aUpdate, 'participant_id = ' . $iParticipantId);
        }


    }


    public function favoriteContest($iContestId, $iUserId, $iType)
    {

        $iParticipantId = Phpfox::getService('contest.participant.process')->insertNewParticipantEntryIfNeccessary($iContestId, $iUserId);

        if ($iType == 1) {
            //follow
            $aUpdate = array(
                'is_favorite' => 1
                );

           
            $bResult = $this->database()->update($this->_sTable, $aUpdate, 'participant_id = ' . $iParticipantId);
			$iItemId = $iParticipantId;
			if(PHpfox::isModule('foxfavorite'))
			{
				(($sPlugin = Phpfox_Plugin::get('contest.service_process_addfavorite_end')) ? eval($sPlugin) : false);
			}
			
		
            if($bResult)
            {
                 Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('favorite_contest', $iContestId);
            }
            return $bResult;

        } else if ($iType == 0) {
            //un follow
             $aUpdate = array(
                'is_favorite' => 0
                );
			$iItemId = $iParticipantId;
			
			if(PHpfox::isModule('foxfavorite'))
			{
				(($sPlugin = Phpfox_Plugin::get('contest.service_process_deletefavorite_end')) ? eval($sPlugin) : false);	
			}
			return $this->database()->update($this->_sTable, $aUpdate, 'participant_id = ' . $iParticipantId);
        }


    }

    // return participant ID
    public function insertNewParticipantEntryIfNeccessary($iContestId, $iUserId)
    {
         $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('contest_participant'))
                ->where('user_id = ' . $iUserId . ' AND contest_id = ' . $iContestId)
                ->execute('getRow');

        if(!$aRow)
        {
            // create new entry in participant table
            $aInsert = array(
                'contest_id' => $iContestId,
                'user_id' => $iUserId
                );

            $iId = $this->database()->insert($this->_sTable, $aInsert);

            return $iId;
        }
        else
        {
            return $aRow['participant_id'];
        }
    }


}