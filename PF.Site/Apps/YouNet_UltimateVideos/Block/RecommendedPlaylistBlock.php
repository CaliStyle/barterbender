<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/10/16
 * Time: 5:28 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class RecommendedPlaylistBlock extends Phpfox_Component
{
    public function process()
    {
        if (!$this->getParam('bIsSearch')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit) {
            return false;
        }

        $aItems =  Phpfox::getService('ultimatevideo.playlist.browse')
            ->getMostRecentPlaylists($iLimit);
        if(empty($aItems)){
            return false;
        }
        Phpfox::getService('ultimatevideo.playlist.browse')->processRows($aItems);
        $this->template()
            ->assign([
                'bMultiViewMode' => true,
            ]);
        $this->template()->assign([
            'sHeader'=> _p('recommended_playlist'). ultimatevideo_playlist_view_mode(),
            'bShowTotalView'=> true,
            'bShowTotalLike'=> true,
            'bShowTotalComment'=> false,
            'bShowCommand' => true,
            'aItems'=>$aItems
        ]);

        return 'block';

    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Recommended Playlist Limit'),
                'description' => _p('Define the limit of how many recommended playlist can be displayed when viewing the section. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Recommended Playlist Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Recommended Playlist</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Recommended Playlist Limit" must be greater than or equal to 0'
            ]
        ];
    }
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'bMultiViewMode',
                'bShowCommand',
            )
        );
    }
}