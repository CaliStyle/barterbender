<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Side_Thankyou_Donors extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iLimit = 9;
		$aCampaign = $this->getParam('aFrCampaign');
		$aDonors = Phpfox::getService('fundraising.user')->getTopDonorsOfCampaign($iLimit, $aCampaign['campaign_id']);
		if(!count($aDonors))
		{
			return false;
		}

		foreach($aDonors as &$aDonor)
		{
			$aDonor['amount_text'] = Phpfox::getService('fundraising')->getCurrencyText($aDonor['amount'], $aCampaign['currency']);
		}
		
		$this->template()->assign(array(
				'aDonors' => $aDonors,
				'sHeader' => _p('thankyou_donors'),
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl('fundraising.user', ['view' => 'donor', 'id' => $aCampaign['campaign_id']])
                )
			)
		);
		return 'block';
    }
    
}

?>