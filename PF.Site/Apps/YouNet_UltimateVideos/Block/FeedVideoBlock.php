<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/23/16
 * Time: 4:15 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

class FeedVideoBlock extends \Phpfox_Component
{
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $iVideoId = $this->getParam('custom_param_ultimatevideo_video_' . $iFeedId);
            $aVideo = Phpfox::getService('ultimatevideo')->getVideo($iVideoId);
            if (empty($aVideo['video_id'])) {
                return false;
            }
            $aVideos = array($aVideo);
            Phpfox::getService('ultimatevideo.browse')->processRows($aVideos);
            $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
            $this->template()->assign(array(
                    'aItem' => $aVideos[0],
                    'corePath' => $corePath,
                )
            );

            $this->clearParam('custom_param_ultimatevideo_video_' . $iFeedId);
        }

        return 'block';
    }
}