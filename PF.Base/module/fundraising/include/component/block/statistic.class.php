<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Statistic extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sType = $this->request()->get('sType');
        $aTransactions = $this->getParam('aTransactions', false);
        $aCampaignStats = $this->getParam('aCampaignStats', false);
		$iIsAdmin = $this->getParam('iIsAdmin', 0);
        $aPager = $this->getParam('aPager');
        Phpfox::getLib('pager')->set(array('page' => $aPager['iPage'], 'size' => $aPager['iLimit'], 'count' => $aPager['iTotal']));
        if(Phpfox::isAdminPanel())
            $sUrl = 'admincp.fundraising.statistic';
        else
            $sUrl = 'fundraising.list.' . $this->request()->getInt('req3');

        $this->template()->assign(array(
            'aTransactions' => $aTransactions,
            'aCampaignStats' => $aCampaignStats,
            'sType' => $sType,
            'sUrl' => $sUrl,
            'iPage' => $aPager['iPage'],
            'iIsAdmin' => $iIsAdmin
        ));

        return 'block';
    }

}

?>