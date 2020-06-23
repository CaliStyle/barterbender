<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/23/16
 * Time: 3:08 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

class YourPlaylistsBlock extends \Phpfox_Component
{
    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }
        $iLimit = $this->getParam('limit', 6);
        $bDisplayViewMoreLink = $this->getParam('display_view_more');

        $aItems = Phpfox::getService('ultimatevideo.playlist')->getPlaylistsOfCurrentUser($iLimit);

        $this->template()->assign([
            'sHeader' => _p('your_playlists'),
            'aItems' => $aItems,
            'sCustomClassName' => 'p-block',
            'iTotalWatchLater' => Phpfox::getService('ultimatevideo.callback')->getWatchLaterVideoTotal(),
            'iTotalFavorite' => Phpfox::getService('ultimatevideo.callback')->getFavoriteVideoTotal(),
        ]);

        if ($bDisplayViewMoreLink) {
            $this->template()->assign([
                'aFooter' => array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', 'view_myplaylist'))
            ]);
        }

        return 'block';
    }

    public function getSettings()
    {
        return array(
            array(
                'info' => _p('display_view_more_link'),
                'description' => _p('display_view_more_link'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('playlist_limit'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            )
        );
    }

    public function getValidation()
    {
        return array(
            'limit' => array(
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('playlist_limit_is_required_and_must_greater_or_equal_zero')
            )
        );
    }
}