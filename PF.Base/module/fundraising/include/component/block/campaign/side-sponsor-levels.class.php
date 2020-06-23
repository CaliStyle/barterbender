<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Side_Sponsor_Levels extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$aCampaign  = $this->getParam('aFrCampaign');
		if(!$aCampaign['sponsor_level'])
		{
			return false;
		}

		foreach($aCampaign['sponsor_level'] as &$aLevel)
		{
			$aLevel['amount_text'] = Phpfox::getService('fundraising')->getCurrencyText($aLevel['amount'], $aCampaign['currency']);
		}

		$this->template()->assign(array(
				'aCampaign' => $aCampaign
			)
		);
		return 'block';
    }
    
}

?>