<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class TopCategories extends Phpfox_Component
{

    public function process()
    {

        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 8);

        if (!$iLimit) {
            return false;
        }

        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('advancedmarketplace.helper')->bIsSideLocation($sBlockLocation);
        $bIsSearch = $this->getParam('bIsSearch');

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $aCategories = Phpfox::getService('advancedmarketplace.category')->getTopCategories($iLimit);

        if (empty($aCategories)) {
            return false;
        }

        if (!is_array($aCategories)) {
            return false;
        }

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'bIsSideLocation' => $bIsSideLocation,
                'sHeader' => _p('top_categories'),
                'sCustomClassName' => 'p-block',
            )
        );


        return 'block';
    }


    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Categories Limit'),
                'description' => _p('Define the limit of how many top categories can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 8,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Top Categories Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Top Categories</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Top Categories Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
