<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Entry_Process extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_sTable = Phpfox::getT('contest_entry');
        $this->_sDirContest = Phpfox::getService('contest.contest')->getContestImageDir();
        if (!is_dir($this->_sDirContest)) {
            mkdir($this->_sDirContest);
        }
    }

    public function add($sTitle, $sSummary, $iItemId, $iItemType, $iContestId,$iSourceId = 2) {
        $oFilter = Phpfox::getLib('parse.input');

        $sTitle = $oFilter->clean($sTitle);

        $sSummary = $oFilter->clean($sSummary);
        $sSummaryParsed = $oFilter->prepare($sSummary);

        $aData = Phpfox::getService('contest.entry')->getDataFromItemToInsert($iItemType, $iItemId,$iSourceId);
        $aDataOfContest = array(
            'title' => $sTitle,
            'summary' => $sSummary,
            'summary_parsed' => $sSummaryParsed,
            'contest_id' => $iContestId,
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME,
            'approve_stamp' => 0,
            'item_id' => 0,
            'total_vote' => 0,
            'total_like' => 0,
            'total_dislike' => 0,
            'total_comment' => 0,
            'total_view' => 0,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'type' => $iItemType
        );

        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

       
        $aInsert = array_merge($aData, $aDataOfContest);

        $iEntryId = $this->database()->insert($this->_sTable, $aInsert);

        if($iItemType == Phpfox::getService('contest.constant')->getContestTypeIdByTypeName('blog'))
        {
            $this->duplicateBlogAttachment($iItemId, $iEntryId);
        }

        if(isset($aContest['is_auto_approve']) && $aContest['is_auto_approve'])
        {
           $this->approveEntry($iEntryId);
        }

        return $iEntryId;
    }

    public function duplicateBlogAttachment($iItemId, $iNewItemId)
    {
        //select all attachment of blog
        $aAttachments = $this->database()->select('*')
                ->from(Phpfox::getT('attachment'))
                ->where('category_id = \'blog\' AND item_id = ' . $iItemId . ' AND user_id = ' . Phpfox::getUserId())
                ->execute('getRows');

        $aInserts = array();
        foreach ($aAttachments as $aAttachment) {
            $aInsert = $aAttachment;
            unset($aInsert['attachment_id']);
            $aInsert['category_id'] =  'contest_entry_blog';
            $aInsert['item_id'] = $iNewItemId;
            $this->database()->insert(Phpfox::getT('attachment'), $aInsert);
            $aInserts[] = $aInsert;
        }
        
    }

    public function copyImageToContest($sFullSourcePath, $sOrigialSuffix = '', $aThumbnaili = array()) {
        $oImage = Phpfox::getLib('image');
        $ext = @pathinfo($sFullSourcePath, PATHINFO_EXTENSION);
        // copy file to contest directory
        if (!empty($ext)) {
            $sNewImageName = md5(PHPFOX_TIME . 'contest_video') . '%s.' . $ext;
        } else {
            $sNewImageName = md5(PHPFOX_TIME . 'contest_video') . '%s.jpg';
        }
        $sNewImageFullPath = Phpfox::getLib('file')->getBuiltDir(Phpfox::getService('contest.contest')->getContestImageDir()) . $sNewImageName;

		
        //copy original image to contest
        $sOriginalSource = sprintf($sFullSourcePath, $sOrigialSuffix);
        $sOriginalDes = sprintf($sNewImageFullPath, $sOrigialSuffix);
		     
        if (!Phpfox::getService('contest.helper')->copyImageFromFoxToContest($sOriginalSource, $sOriginalDes)) {
            return NULL;
        }

        // generate thumbnail
        foreach ($aThumbnaili as $iSize) {
            //copy images
            $oImage->createThumbnail($sOriginalSource, sprintf($sNewImageFullPath, '_' . $iSize), $iSize, $iSize);
        }

        return 'contest' . PHPFOX_DS . str_replace(Phpfox::getService('contest.contest')->getContestImageDir(), '', $sNewImageFullPath);
    }

    public function getShortBitlyUrl($sLongUrl) {
        try {
            $sLongUrl = urlencode($sLongUrl);

            $url = "http://api.bitly.com/v3/shorten?login=myshortlinkng&apiKey=R_0201be3efbcc7a1a0a0d1816802081d8&longUrl={$sLongUrl}&format=json";

            $result = @file_get_contents($url);

            $obj = json_decode($result, true);
            return ($obj['status_code'] == '200' ? $obj['data']['url'] : "");
        } catch (Exception $e) {
            return $sLongUrl;
        }
    }

    public function isVoted($user_id, $entry_id)
    {
        $aRow = $this->database()->select('*')
        ->from(Phpfox::getT('contest_entry_vote'))
        ->where('entry_id=' . $entry_id . ' and user_id = ' . $user_id)
        ->execute('getRow');
        
        return !empty($aRow) ? true : false;
    }

    public function deleteVote($user_id, $entry_id)
    {
        $is_voted = $this->isVoted($user_id, $entry_id);
        if (!$is_voted)
        {
            return true;
        }
        else
        {
            if ($this->database()->delete(Phpfox::getT('contest_entry_vote'), 'entry_id = ' . $entry_id . ' and user_id = ' . $user_id))
            {
                $this->database()->update($this->_sTable, array('total_vote' => 'total_vote - 1'), 'entry_id = '.$entry_id, false);
                return true;
            }
        }
        
        return false;
    }

    public function addVote($user_id, $entry_id)
    {
        $is_voted = $this->isVoted($user_id, $entry_id);
        if ($is_voted)
        {
            return true;
        }
        else
        {
            $aInsert = array('user_id' => $user_id, 'entry_id' => $entry_id);
            if ($this->database()->insert(Phpfox::getT('contest_entry_vote'), $aInsert))
            {
                $this->addNotificationVote(array('item_id' => $entry_id));
                $this->database()->update($this->_sTable, array('total_vote' => 'total_vote + 1'), 'entry_id = '.$entry_id, false);
                return true;
            }
        }
        
        return false;
    }
	
	public function loadPageForEntry($entry_id){
		$aEntry = Phpfox::getService('contest.entry')->getContestEntryById($entry_id);
		$url = Phpfox::permalink('contest', $aEntry['contest_id'], $aEntry['contest_name']).'entry_'.$aEntry['entry_id'].'/';
		return array($aEntry,$url);
	}
	
	public function approveEntry($entry_id){
		$this->database()->update(Phpfox::getT('contest_entry'),array(
			'status' => 1,
			'approve_stamp' => PHPFOX_TIME,
		),'entry_id = '.$entry_id);

        $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($entry_id);
        Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('approve_entry', $aEntry['contest_id'], $entry_id);

		$this->postFeed($entry_id);
	}
	
	public function postFeed($entry_id){
		$aCallback = null;

        if(!Phpfox::getService('contest.helper')->checkFeedExist($entry_id, $sTypeId = 'contest_entry'))
        {
            $aContest = Phpfox::getService('contest.entry')->getContestEntryById($entry_id);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('contest_entry', $entry_id, $aContest['privacy'], (isset($aContest['privacy_comment']) ? (int) $aContest['privacy_comment'] : 0),  0, $aContest['user_id']) : null);
        }
		
	}
	
	public function denyEntry($entry_id){
		$this->database()->update(Phpfox::getT('contest_entry'),array(
			'status' => 2,
			'approve_stamp' => PHPFOX_TIME,
		),'entry_id = '.$entry_id);

         $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($entry_id);
         Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('deny_entry', $aEntry['contest_id'], $entry_id);
	}
	
	public function winningEntry($aListVals){
		
        $iLastEntryId = null;
		foreach($aListVals as $key=>$aVals){
			$this->database()->delete(PHpfox::getT('contest_winner'),'entry_id = '.$aVals['entry_id']);
			$aInserts = array(
				'entry_id' => $aVals['entry_id'],
				'user_id' => $aVals['user_id'],
				'award' => $aVals['award'],
				'rank' => $aVals['rank'],
				'time_stamp' => PHPFOX_TIME
	 		);
			$this->database()->insert(Phpfox::getT('contest_winner'),$aInserts,'entry_id = '.$aVals['entry_id']);
            $iLastEntryId = $aVals['entry_id'];
		}

        $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($iLastEntryId);

        Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('inform_winning_entry', $aEntry['contest_id']);
	}
	
	public function deletewinningEntry($entry_id){
		$this->database()->delete(Phpfox::getT('contest_winner'),'entry_id = '.$entry_id);
	}
	
	public function addNotificationVote($aVals){
			
		$aEntry = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, en.title, en.entry_id, ct.privacy, ct.privacy_comment, ct.user_id as owner_contest, ct.contest_id, ct.contest_name')
			->from(Phpfox::getT('contest_entry'), 'en')
            ->join(Phpfox::getT('contest'), 'ct', 'ct.contest_id = en.contest_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = en.user_id')
			->where('en.entry_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
			$sLink = Phpfox::permalink('contest', $aEntry['contest_id'], $aEntry['contest_name']).'entry_'.$aEntry['entry_id'].'/';
		
			$aList = Phpfox::getService('contest.participant')->getListFollowingByContestId($aEntry['contest_id']);
			
			$aList[] = array('user_id' => $aEntry['user_id']);
			$aList[] = array('user_id' => $aEntry['owner_contest']);
			$hasSend = array();
		foreach($aList as $List){
            if(!in_array($List['user_id'], $hasSend)){
			    $hasSend[] = $List['user_id'];
				Phpfox::getLib('mail')->to($List['user_id'])
				->subject(_p('contest.full_name_voted_on_the_entry_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aEntry['title'])))
				->message(_p('contest.full_name_voted_on_the_entry_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aEntry['title'])))
				->notification('vote.add_new_vote')
				->send();			
		
				if (Phpfox::isModule('notification'))
				{
					Phpfox::getService('notification.process')->add('contest_entry_vote', $aEntry['entry_id'], $List['user_id']);
				}
            }

		}
    }

	public function viewEntry($entry_id,$total_view){


		if($this->database()->update(Phpfox::getT('contest_entry'),array(
			'total_view' => $total_view+1
		),'entry_id = '.$entry_id))
		{
			return $total_view+1;
		}
		return $total_view;
	}
	  
    public function removeWinningEntry($iEntryId)
    {
        return $this->database()->delete(Phpfox::getT('contest_winner'),'entry_id = '.$iEntryId);
    }

}

?>