<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Invite extends Phpfox_Component
{

	public function process()
	{		
		$iId = $this->getParam('id');
		
          //$aPetition = Phpfox::getService('petition')->getInviteForUser();                
		$aPetition = Phpfox::getService('petition')->getPetition($iId );
		
		if(empty($aPetition))
		{
			return Phpfox_Error::display('<div class="error_message">'.Phpfox::getPhrase('petition.petition_not_found').'</div>');
		}
		$sLetter = Phpfox::getParam('petition.friend_letter_template');
		$sFriendMessageTemplate = Phpfox::getService('petition')->parseVar($sLetter,$aPetition);
		
		$this->template()->assign(array(
			'aForms' => $aPetition,
			'sFriendMessageTemplate' => $sFriendMessageTemplate
		));	
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_invite_clean')) ? eval($sPlugin) : false);
	}
}

?>