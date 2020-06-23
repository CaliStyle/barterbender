<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ListingSlideShow extends Phpfox_Component
{
    public function process()
    {
        $bIsInHomePage = $this->getParam('bIsInHomePage');
        $iLimit = $this->getParam('limit', 4);

        if (!$bIsInHomePage || !$iLimit) {
            return false;
        }

        $aSlideShowListing = Phpfox::getService("advancedmarketplace")->frontend_getFeatureListings($iLimit);

        if (empty($aSlideShowListing)) {
            return false;
        }

        $this->template()->assign(array(
            'sHeader' => _p('advancedmarketplace.feature_listings'),
            'corepath' => phpfox::getParam('core.path'),
            'aSlideShowListing' => $aSlideShowListing,
            'core_url' => phpfox::getParam('core.path'),
            'sCustomClassName' => 'p-block',
        ));

        return 'block';
    }

    /**
     * Block settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('advancedmarketplace_block_slideshow_limit_info'),
                'description' => _p('advancedmarketplace_block_slideshow_limit_description'),
                'value' => 4,
                'var_name' => 'limit',
                'type' => 'integer'
            ]
        ];
    }

    /**
     * Validation
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
                    ['var_name' => _p('advancedmarketplace_block_slideshow_limit_info')])
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_listingslideshow_clean')) ? eval($sPlugin) : false);
    }
}
