<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

class FeedPlaylistBlock extends \Phpfox_Component
{
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $iPlaylistId = $this->getParam('custom_param_ultimatevideo_playlist_' . $iFeedId);
            $aPlaylist = Phpfox::getService('ultimatevideo')->getPlaylist($iPlaylistId);
            if (empty($aPlaylist['playlist_id'])) {
                return false;
            }
            $aPlaylists = array($aPlaylist);
            Phpfox::getService('ultimatevideo.playlist.browse')->processRows($aPlaylists);
            $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideo';
            $this->template()->assign(array(
                    'aPitem' => $aPlaylists[0],
                    'corePath' => $corePath,
                )
            );

            $this->clearParam('custom_param_ultimatevideo_playlist_' . $iFeedId);
        }

        return 'block';
    }
}