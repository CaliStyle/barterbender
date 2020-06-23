<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Mail_Mail extends Phpfox_Service 
{

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('contest_emailtemplate');
	}
	

	public function getEmailTemplateByTypeId($iTemplateType, $iContestId = 0)
	{

		// When template type is thanks participant, we get it from contest info , not from emailtemplate table
		if($iTemplateType == Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('thanks_participant') && $iContestId)
		{
			$aRow = $this->database()->select('*')
				->from(Phpfox::getT('contest_email_condition'))
				->where('contest_id = ' . $iContestId)
				->execute('getSlaveRow');

			$aRow['content'] = $aRow['message'];
		}
		else
		{
			$aRow = $this->database()->select('*')
				->from(Phpfox::getT('contest_emailtemplate'))
				->where('type_id = ' . $iTemplateType)
				->execute('getSlaveRow');
		}

		

        if(!isset($aRow['subject']))
            $aRow['subject'] = "";
        if(!isset($aRow['content']))
            $aRow['content'] = "";

		return $aRow;
	}

	public function getEmailTemplateByTypeName($sTypeName)
	{
		$iTypeId = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName($sTypeName);

		return $this->getEmailTemplateByTypeId($iTypeId);
	}

	/**
	 * get email template and generate message based on campaign_id
	 * @TODO: static cache email template here , write test
	 * @by minhta
	 * @param $iTemplateType, $iContestId
	 * @return array
	 */
	public function getEmailMessageAndSubjectFromTemplate($iTemplateType, $iContestId)
	{
		$aTemplate = Phpfox::getService('contest.mail')->getEmailTemplateByTypeId($iTemplateType, $iContestId);;

		$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

		$sMessage = Phpfox::getService('contest.mail.process')->parseTemplate($aTemplate['content'], $aContest);

        //$sMessage = str_replace('"', '\'', $sMessage);
        //$sMessage = preg_replace('/[\r]+/', '', $sMessage);
        
		$sSubject = Phpfox::getService('contest.mail.process')->parseTemplate($aTemplate['subject'], $aContest);

		return array(
			'message' => $sMessage,
			'subject' => $sSubject
		);

		
	}
	
}

?>
