<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Homepage_Featured_Slideshow extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bInHomepage = $this->getParam('bInHomepageFr');
        $iLimit = $this->getParam('limit', 12);
        if (!$iLimit || !$bInHomepage) {
            return false;
        }
        $aFeaturedCampaigns = Phpfox::getService('fundraising.campaign')->getCampaigns($sType = 'featured', $iLimit);

        if (count($aFeaturedCampaigns) == 0) {
            return false;
        }

        foreach ($aFeaturedCampaigns as &$aCampaign) {
            $aCampaign = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aCampaign);
            $aCampaign['donor_list'] = Phpfox::getService('fundraising.user')->getDonorsOfCampaign($aCampaign['campaign_id'],$iPageSize = 14);
        }

        $style_responsive = Phpfox::getLib('template')->getStyleFolder();
        $this->template()->assign(array(
            'aFeaturedCampaigns' => $aFeaturedCampaigns,
            'sHeader' => _p('featured_campaigns'),
            'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
            'sCorePath' => Phpfox::getParam('core.path'),
            'sCoreFile' => Phpfox::getParam('core.url_file'),
            'sStyleRS' => $style_responsive
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
                'info' => _p('Number of Featured Campaigns Limit'),
                'description' => _p('Define the limit of how many campaigns in featured campaigns block can be displayed when viewing the fundraising section. Set 0 will hide this block.'),
                'value' => 12,
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
                'title' => '"Number of Featured Campaigns Limit" must be greater than or equal to 0'
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

        (($sPlugin = Phpfox_Plugin::get('fundraising.component_block_featured_slideshow_clean')) ? eval($sPlugin) : false);
    }

}
