<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Homepage_Most_Liked extends Phpfox_Component
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
        $aMostLikedCampaigns = Phpfox::getService('fundraising.campaign')->getCampaigns($sType = 'most-liked', $iLimit);
        if (!$aMostLikedCampaigns) {
            return false;
        }
        $this->template()->assign(array(
            'aMostLikedCampaigns' => $aMostLikedCampaigns,
            'sHeader' => _p('most_liked')
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
                'info' => _p('Number of Most Liked Campaigns Limit'),
                'description' => _p('Define the limit of how many campaigns in most liked campaigns block can be displayed when viewing the fundraising section. Set 0 will hide this block.'),
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
                'title' => '"Number of Most Liked Campaigns Limit" must be greater than or equal to 0'
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

        (($sPlugin = Phpfox_Plugin::get('fundraising.component_block_homepage_most_liked_clean')) ? eval($sPlugin) : false);
    }

}
