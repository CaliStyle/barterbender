<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/23/16
 * Time: 3:08 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;


use Phpfox;

class UserPlaylistChecklistBlock extends \Phpfox_Component
{
    public function process()
    {
        $iVideoId = $this->request()->get('id');
        $aItems = Phpfox::getService('ultimatevideo.playlist')->getAllPlaylistOfUser($iVideoId, false);

        $this->template()->assign([
            'iVideoId' => $iVideoId,
            'aItems' => $aItems,
        ]);
    }
}