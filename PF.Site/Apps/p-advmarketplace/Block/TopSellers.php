<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class TopSellers extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        $bIsSearch = $this->getParam('bIsSearch');
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('advancedmarketplace.helper')->bIsSideLocation($blockLocation);
        if ($bIsSearch && !$isSideLocation) {
            return false;
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $aItems = Phpfox::getService('advancedmarketplace')->getTopSellers($iLimit, 'total_listing DESC');

        if (empty($aItems)) {
            return false;
        }

        if ($isSideLocation) {
            $this->template()->assign('sModeViewDefault', 'list');
        } else {
            $this->template()->assign('sModeViewDefault', 'grid');
        }

        $this->template()->assign(array(
                'sHeader' => _p('top_sellers'),
                'aItems' => $aItems,
                'sCustomClassName' => 'p-block',
                'showClearListing' => true,
                'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
            )
        );

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Sellers Limit'),
                'description' => _p('Define the limit of how many top sellers can be displayed when viewing the listing section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Top Sellers Block Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Top Sellers</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Suggestion Top Sellers Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
