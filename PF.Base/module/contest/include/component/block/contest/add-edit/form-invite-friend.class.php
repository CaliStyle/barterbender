<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Add_Edit_Form_Invite_Friend extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iId = $this->getParam('contest_id');

        $iEntryId = $this->getParam('entry_id');
		if(!$iId)
		{
			return false;
		}
        $bIsPopup = $this->getParam('is_popup');

        $aContest = Phpfox::getService('contest.contest')->getContestById($iId);

		if(!$aContest)
		{
			return false;
		}
		
		if($iEntryId)
		{
			Phpfox::getService('contest.mail.process')->setInviter(Phpfox::getUserId());
			Phpfox::getService('contest.mail.process')->setEntry($iEntryId);

			$aMessage = Phpfox::getService('contest.mail')->getEmailMessageAndSubjectFromTemplate(Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('invite_friend_view_entry_letter'), $aContest['contest_id']);
		}
		else
		{
			$aMessage = Phpfox::getService('contest.mail')->getEmailMessageAndSubjectFromTemplate(Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('invite_friend_letter'), $aContest['contest_id']);
		}
		

		// // to make message displayed correctly on text area
		$aMessage['message'] = str_replace("\n", "&#10;", $aMessage['message']);

        $this->template()->assign(array(
			'aMessage' => $aMessage,
			'aContest' => $aContest,
            'bIsPopup' => $bIsPopup,
            'iEntryId' => $iEntryId
        ));
    }
    
}

?>