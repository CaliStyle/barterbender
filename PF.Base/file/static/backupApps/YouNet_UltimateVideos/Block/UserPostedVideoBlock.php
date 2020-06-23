<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/12/16
 * Time: 11:23 AM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class UserPostedVideoBlock extends Phpfox_Component
{
    public function process()
    {

        if(!defined('ULTIMATE_VIDEO_OWNER_ID') || !ULTIMATE_VIDEO_OWNER_ID){
            return false;
        }

        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ultimatevideo.browse')->getUserPostedVideos($iLimit, ULTIMATE_VIDEO_OWNER_ID);
        if(empty($aItems)){
            return false;
        }
        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);
        $this->template()
            ->assign([
                'sHeader'=> _p('more_from_name',['name'=> strtr('<a style="display:inline-block" href=":href">:name</a>',[
                    ':name'=> ULTIMATE_VIDEO_USER_NAME,
                    ':href'=> url('ultimatevideo',['user'=> ULTIMATE_VIDEO_OWNER_ID]),
                ])]).ultimatevideo_video_view_mode(),
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
                'info' => _p('More From Owners Video Limit'),
                'description' => _p('Define the limit of how many more from owner videos can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('More From Owners Video Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>More From Owners Video</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"More From Owners Video Limit" must be greater than or equal to 0'
            ]
        ];
    }
}