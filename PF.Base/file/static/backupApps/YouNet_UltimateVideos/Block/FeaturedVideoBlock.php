<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/9/16
 * Time: 6:11 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;


use Phpfox;

class FeaturedVideoBlock extends \Phpfox_Component
{

    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aItems =  Phpfox::getService('ultimatevideo.browse')
            ->getMostFeaturedVideos($iLimit);

        if(empty($aItems)){
            return false;
        }
        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);

        $this->template()->assign([
            'sHeader'=> _p('Featured').ultimatevideo_video_view_mode(),
            'aItems'=>$aItems
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Featured Video Limit'),
                'description' => _p('Define the limit of how many featured videos can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Featured Video Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Featured Video</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Featured Video Limit" must be greater than or equal to 0'
            ]
        ];
    }
}