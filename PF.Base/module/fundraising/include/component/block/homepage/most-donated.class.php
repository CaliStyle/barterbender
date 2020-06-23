<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Homepage_Most_Donated extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bInHomepage = $this->getParam('bInHomepageFr');
        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit || !$bInHomepage) {
            return false;
        }
        $aMostDonatedCampaigns = Phpfox::getService('fundraising.campaign')->getCampaigns($sType = 'most-donated',
            $iLimit);
        if (!$aMostDonatedCampaigns) {
            return false;
        }
        $this->template()->assign(array(
            'aMostDonatedCampaigns' => $aMostDonatedCampaigns,
            'sHeader' => _p('most_donated')
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
                'info' => _p('Number of Most Donated Campaigns Limit'),
                'description' => _p('Define the limit of how many campaigns in most donated campaigns block can be displayed when viewing the fundraising section. Set 0 will hide this block.'),
                'value' => 10,
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
                'title' => '"Number of Most Donated Campaigns Limit" must be greater than or equal to 0'
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

        (($sPlugin = Phpfox_Plugin::get('fundraising.component_block_homepage_most_donated_clean')) ? eval($sPlugin) : false);
    }

}
