<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Highlight_Campaign extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sHeader = '';
        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }
        /**
         *this param is used when using iframe, to know the condition of badge code
         */
        $iStatus = 0;
        $bIsBadge = $this->getParam('bIsBadge', false);
        if ($bIsBadge) {
            $iCampaignId = $this->getParam('iCampaignId');
            $iStatus = $this->getParam('iStatus');
            if ($iCampaignId) {
                $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);
            }
        } else {
            $sHeader = _p('highlight_campaign');
            $aCampaign = Phpfox::getService('fundraising.campaign')->getHightlightCampaign();
        }

        if (!$aCampaign || (isset($aCampaign['is_active']) && $aCampaign['is_active'] == '0') || defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            return false;
        }

        $aCampaign['can_donate'] = 1;

        $aCampaign = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aCampaign);

        $aDonors = Phpfox::getService('fundraising.user')->getDonorsOfCampaign($aCampaign['campaign_id'], $iLimit);

        $this->template()->assign(array(
            'sHeader' => $sHeader,
            'core_path' => Phpfox::getParam('core.path'),
            'aCampaign' => $aCampaign,
            'aDonors' => $aDonors,
            'iStatus' => $iStatus,
            'aStatus' => Phpfox::getService('fundraising')->getAllBadgeStatus()

        ));
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Number of Donors in Highlight Campaign Block Limit'),
                'description' => _p('Define the limit of how many donors in highlight campaigns block can be displayed when viewing the fundraising section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Number of Donors in Highlight Campaign Block Limit" must be greater than or equal to 0'
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
            'limit',
        ));

        (($sPlugin = Phpfox_Plugin::get('fundraising.component_block_highlight_campaign_clean')) ? eval($sPlugin) : false);
    }

}
