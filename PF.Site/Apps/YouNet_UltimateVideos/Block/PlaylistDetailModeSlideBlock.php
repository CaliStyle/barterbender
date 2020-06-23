<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class PlaylistDetailModeSlideBlock extends Phpfox_Component
{
    public function process()
    {
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);
        $bIsSearch = $this->getParam('bIsSearch');

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $iPlaylistId = $this->getParam('playlist_id');
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $aLists = Phpfox::getService('ultimatevideo.playlist')->getVideosSlideShow($iPlaylistId);
        $iPlayId = $this->request()->get('play', 0);
        $iStartSlide = 0;
        if ($iPlayId) {
            foreach ($aLists as $aList) {
                if ($aList['video_id'] == $iPlayId) {
                    break;
                }
                $iStartSlide += 1;
            }
        }

        $this->template()->assign([
            'aItems' => $aLists,
            'iPlayId' => $iPlayId,
            'iTotalVideo' => count($aLists),
            'iStartSlide' => $iStartSlide,
            'corePath' => $corePath,
            'iPlaylistId' => $iPlaylistId,
            'bShowCommand' => false,
            'bIsSearch' => false,
            'bIsPagesView' => false,
        ]);
    }
}