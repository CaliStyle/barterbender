<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Invite extends Phpfox_Component
{

	public function process()
	{		
		$iId = $this->getParam('id');
		
          //$aFundraising = Phpfox::getService('fundraising')->getInviteForUser();                
		$aFundraising = Phpfox::getService('fundraising')->getFundraising($iId );
		
		if(empty($aFundraising))
		{
			return Phpfox_Error::display('<div class="error_message">'._p('fundraising_not_found').'</div>');
		}
		$sLetter = Phpfox::getParam('fundraising.friend_letter_template');
		$sFriendMessageTemplate = Phpfox::getService('fundraising')->parseVar($sLetter,$aFundraising);
		
		$this->template()->assign(array(
			'aForms' => $aFundraising,
			'sFriendMessageTemplate' => $sFriendMessageTemplate
		));	
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_block_invite_clean')) ? eval($sPlugin) : false);
	}
}

?>