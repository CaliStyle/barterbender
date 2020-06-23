<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Donate extends Phpfox_Component
{
	public function process()	
	{
		Phpfox::getService('fundraising')->buildMenu();
		$iCampaignId = $this->request()->get('id');
		if(!Phpfox::getService('fundraising.permission')->canDonateCampaign($iCampaignId))
		{
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
		}
		$iAmount = $this->request()->get('amount', '');
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);
		if(!$aCampaign)
		{
			$this->url()->send('fundraising');	
		}


		$aVals = $this->request()->get('val');
		if($aVals)
		{
			Phpfox::getService('fundraising.campaign.process')->donate($aVals, $aVals['campaign_id']);
		}

		$this->template()->setTitle($aCampaign['title'])
				->setBreadCrumb(_p('fundraisings'), $aCampaign['module_id'] == 'fundraising' ? $this->url()->makeUrl('fundraising') : $this->url()->permalink('pages', $aCampaign['item_id'], 'fundraising') )
				->setBreadCrumb($aCampaign['title'], $this->url()->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']), true)
				->assign(array(
					'aCampaign' => $aCampaign,
					'iSponsorAmount' => $iAmount,
					'bIsGuest' => Phpfox::isUser() ? false : true,
				))->setHeader(array(
					'jquery.validate.js' => 'module_fundraising',
					'ynfundraising.js' => 'module_fundraising',
				))->setPhrase(array(
					'fundraising.this_field_is_required',
					'fundraising.please_enter_a_valid_number',
					'fundraising.please_enter_a_valid_email',
					'fundraising.please_enter_a_valid_url'
				));
	}
}

?>
