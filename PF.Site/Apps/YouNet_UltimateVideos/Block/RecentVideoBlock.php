<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/11/16
 * Time: 9:28 AM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

class RecentVideoBlock extends \Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('iLimit',3);
        $this->clearParam('iLimit');

        $aItems = Phpfox::getService('ultimatevideo.browse')->getMostRecentVideos($iLimit);
        if(empty($aItems)){
            return false;
        }
        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);
        $this->template()
            ->assign([
                'sHeader'=> _p('recent_videos').ultimatevideo_video_view_mode(),
                'bShowTotalView'=> true,
                'bShowTotalLike'=> true,
                'bShowTotalComment'=> false,
                'aItems'=>$aItems,
            ]);

        return 'block';
    }
}