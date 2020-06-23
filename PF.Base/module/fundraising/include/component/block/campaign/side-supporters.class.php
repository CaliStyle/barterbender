<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Side_Supporters extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iLimit = 9;
		$aCampaign = $this->getParam('aFrCampaign');
		$aSupporters = Phpfox::getService('fundraising.user')->getTopSupportersOfCampaign($iLimit, $aCampaign['campaign_id']);
		if(!count($aSupporters))
		{
			return false;
		}
		$this->template()->assign(array(
				'aSupporters' => $aSupporters,
				'iCampaignId' => $aCampaign['campaign_id'],
				'sHeader' => _p('supporters'),
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl('fundraising.user', ['view' => 'supporter', 'id' => $aCampaign['campaign_id']])
                )
			)
		);
		return 'block';
    }
    
}

?>