    <?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Mail_Process extends Phpfox_Service 
{
	
    /**
     * key is value will be replaced, value is the field in aContest array
     * remember every entry having a corresponding phrase with prefix keywordsub_
     * @var array
     */
    private $_aReplace= array(
        '[title]' => 'contest_name',
        '[contest_url]' => 'contest_url',
        '[short_description]' => 'short_description',
        '[description]' => 'description',
        '[start_time]' => 'start_time',
        '[end_time]' => 'end_time',
        '[stop_time]' => 'stop_time',
        '[owner_name]' => 'owner_name', 
        // ex: keywordsub_inviter_name => phrase " Name of Inviter"
        '[inviter_name]' => 'inviter_name',
        '[site_name]' => 'site_name',
        '[award]' => 'award',
        '[participant_name]' => 'participant_name',
        '[entry_owner_name]' => 'entry_owner_name',
        '[entry_name]' => 'entry_name',
        '[entry_url]' => 'entry_url'
    );

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('contest_emailtemplate');
        $this->setInviter(Phpfox::getUserId());
	}


    // it is an implementation of inverse control, it depends on the set function
    // we will have the inviter name or not
    private $_iInviterId = 0;

    public function setInviter($iInviterId)
    {
        $this->_iInviterId = $iInviterId;
    }

    private $_iParticipantId = 0;

    public function setParticipant($iParticipantId)
    {
        $this->_iParticipantId = $iParticipantId;
    }

     private $_iEntryId = 0;

    public function setEntry($iEntryId)
    {
        $this->_iEntryId = $iEntryId;
    }

    // same as user id
    private $_iEntryOwnerId = 0;

    public function setEntryOwner($iEntryOwnerId)
    {
        $this->_iEntryOwnerId = $iEntryOwnerId;
    }

    public function addEmailTemplate($aVals) {

        $aRow = $this->database()->select('*')
        						 ->from($this->_sTable)
        					     ->where('type_id =' . $aVals['type_id'])
        					     ->execute('getSlaveRow');

        if($aRow) {
            $aUpdate = array(
                'subject' => $aVals['subject'],
                'content' => $aVals['content'],
                'time_stamp' => PHPFOX_TIME
            );

            $this->database()->update($this->_sTable, $aUpdate, 'type_id = ' . $aVals['type_id']);
            $iId = $aRow['emailtemplate_id'];
        } else {
            $aInsert = array(
                'type' => $aVals['type_id'],
                'subject' => $aVals['subject'],
                'content' => $aVals['content'],
                'time_stamp' => PHPFOX_TIME
            );

            $iId = $this->database()->insert($this->_sTable, $aInsert);
        }

        return $iId;
    }

     /**
     * parse text for showing on form based on the contest
     * it will replace some predefined symbol by the corresponding text
     * @by minhta
     * @param string $sToBeParsedText the text to be parsed 
     * @param array $aContest the corresponding contest
     * @return
     */
    public function parseTemplate($sToBeParsedText, $aContest, $iDonorId = 0, $iInviterId = 0) {
        //if a id of contest is passed
        if(!is_array($aContest))
        {
            $aContest = Phpfox::getService('contest.contest')->getContestById($aContest);
        }

        $aContest['site_name'] = Phpfox::getParam('core.site_title');


        // in case we need inviter name
        if($this->_iInviterId)
        {
            $aUser = Phpfox::getService('user')->getUser($this->_iInviterId);
            $aContest['inviter_name'] = $aUser['full_name'];
        }

        if($this->_iEntryId)
        {
            $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($this->_iEntryId);
            if($aEntry)
            {
                 $aContest['entry_name'] = $aEntry['title'];
                 $sEntryUrl = Phpfox::permalink('contest', $aEntry['contest_id'], $aEntry['contest_name']).'entry_'.$aEntry['entry_id'].'/';
                 $aContest['entry_url'] =  $sEntryUrl;
            }
           
        }


        if($this->_iParticipantId)
        {
            $aParticipant = Phpfox::getService('contest.participant')->getParticipantById($this->_iParticipantId);

            $aUser = Phpfox::getService('user')->getUser($aParticipant['user_id']);
            $aContest['participant_name'] = $aUser['full_name'];
        }

        if($this->_iEntryOwnerId)
        {
            $aUser = Phpfox::getService('user')->getUser($this->_iEntryOwnerId);
            $aContest['entry_owner_name'] = $aUser['full_name'];
        }



        $oDate = Phpfox::getLib('date');
        $aLink = Phpfox::getLib('url')->permalink('contest', $aContest['contest_id'], $aContest['contest_name']);
        $sLink = '<a href="' . $aLink . '" title = "' . $aContest['contest_name'] . '" target="_blank">' . $aLink . '</a>';
        $aContest['contest_url'] = $sLink;

        $format_datetime = 'l, F j, Y g:i a';

        $aContest['end_time'] = Phpfox::getTime($format_datetime, $aContest['end_time']); 
        $aContest['stop_time'] = Phpfox::getTime($format_datetime, $aContest['stop_time']);
        $aContest['start_time'] = Phpfox::getTime($format_datetime, $aContest['start_time']);
        
        //the trick here ot send html email along with description 
        $aContest['description'] = $aContest['description_parsed'];

        $aOnwerUser = Phpfox::getService('user')->getUser($aContest['user_id']);
        $aContest['owner_name'] = $aOnwerUser['full_name'];


        $aContest['award'] = $aContest['award_description'];

        $aBeReplaced = array();
        $aReplace = array();

        //setup replace and be replaced array
        foreach($this->_aReplace as $sBeReplaced => $sReplace)
        {
            if(isset($aContest[$sReplace]))
            {
                $aBeReplaced[] = $sBeReplaced;
                $aReplace[] = $aContest[$sReplace];
            }
        }

        $sParsedText = str_replace($aBeReplaced, $aReplace, $sToBeParsedText);

        return $sParsedText;
    }


    public function getAllReplaces()
    {
        return $this->_aReplace;
    }

    /**
     * in case of sending email to user of this site, we only need user id to send them
     * @return true if sending successfully
     */
    public function sendEmailTo($iTemplateType = 0, $iContestId = 0, $aReceivers = array(), $aCustomEmail = array())
    {
        if(!$aReceivers || !$iContestId)
        {
            return false;
        }
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

        if(!is_array($aReceivers))
        {
            $aReceivers = array($aReceivers);
        }

        $aEmailMessage = array(
            'message' => '',
            'subject' => ''
        );
        if($aCustomEmail && $iTemplateType == 0)
        {
            
            $aEmailMessage['message'] = Phpfox::getService('contest.mail.process')->parseTemplate($aCustomEmail['message'], $aContest);
            $aEmailMessage['subject'] = Phpfox::getService('contest.mail.process')->parseTemplate($aCustomEmail['subject'], $aContest);
        }
        else
        {
            
            $aEmailMessage = Phpfox::getService('contest.mail')->getEmailMessageAndSubjectFromTemplate($iTemplateType, $iContestId);
        }

        $aVal = array(
         'email_message' =>$aEmailMessage['message'],
         'email_subject' => $aEmailMessage['subject'],
         'contest_id' => $iContestId,
         'receivers' => serialize($aReceivers),
         'is_sent' => 0
        );

        return Phpfox::getService('contest.mail.send')->send(
                $sSubject = $aEmailMessage['subject'], 
                $sMessage = $aEmailMessage['message'], 
                $aReceivers);


    }


}

?>