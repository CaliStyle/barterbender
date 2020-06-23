<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Reason_Close_Campaign extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$iCampaignId = $this->getParam('iCampaignId');
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);

		if (!$aCampaign) {
			return false;
		}
		$this->template()->assign(array(
					'aCampaign' => $aCampaign
						)
				)
				->setPhrase(array(
					'fundraising.please_enter_the_reason'
				)
		);
	}

}

?>