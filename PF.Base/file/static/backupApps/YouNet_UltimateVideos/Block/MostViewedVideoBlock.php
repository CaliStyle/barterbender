<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/9/16
 * Time: 6:04 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class MostViewedVideoBlock extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ultimatevideo.browse')->getMostViewedVideos($iLimit);
        if(!$aItems)
            return false;
        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);
        $this->template()
            ->assign([
                'sHeader'=> _p('most_viewed').ultimatevideo_video_view_mode(),
                'bShowTotalView'=> true,
                'bShowTotalLike'=> true,
                'bShowTotalComment'=> false,
                'aItems'=>$aItems,
            ]);
        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Most View Video Limit'),
                'description' => _p('Define the limit of how many most view videos can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Most View Video Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Most View Video</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Most View Video Limit" must be greater than or equal to 0'
            ]
        ];
    }
}