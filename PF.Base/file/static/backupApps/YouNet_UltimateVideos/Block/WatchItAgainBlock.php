<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/10/16
 * Time: 5:33 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class WatchItAgainBlock extends Phpfox_Component
{

    public function process()
    {
        if ($this->getParam('bIsSearch')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        if(!Phpfox::getUserId()){
            return false;
        }

        $aItems = Phpfox::getService('ultimatevideo.browse')->getWatchItAgainVideos($iLimit);
        if(empty($aItems)){
            return false;
        }
        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);

        $this->template()
            ->assign([
                'sHeader'=> _p('Watch It Again') .ultimatevideo_video_view_mode(),
                'bShowTotalView'=> true,
                'bShowTotalLike'=> true,
                'bShowTotalComment'=> false,
                'bShowCommand' => true,
                'aItems'=>$aItems,
            ]);
        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Watch It Again Video Limit'),
                'description' => _p('Define the limit of how many watch it again videos can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Watch It Again Video Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Watch It Again Video</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Watch It Again Video Limit" must be greater than or equal to 0'
            ]
        ];
    }

    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'bShowCommand',
            )
        );
    }
}