<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/9/16
 * Time: 6:03 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;


use Phpfox;

class TagsVideoBlock extends \Phpfox_Component
{

    public function process()
    {
        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit) {
            return false;
        }

        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);
        $bIsSearch = $this->getParam('bIsSearch');

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $aRows = Phpfox::getService('ultimatevideo.browse')->getTagCloud($iLimit);
        if (empty($aRows)) {
            return false;
        }
        $this->template()->assign([
            'sHeader' => _p('Tags'),
            'aRows' => $aRows,
            'sCustomClassName' => 'p-block'
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Tags Video Limit'),
                'description' => _p('Define the limit of how many tags videos can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Tags Video Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Tags Video</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Tags Video Limit" must be greater than or equal to 0'
            ]
        ];
    }

}