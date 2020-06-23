<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/10/16
 * Time: 5:51 PM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

class PlaylistListBlock extends Phpfox_Component
{

    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);
        $sModuleName = Phpfox::getLib('module')->getFullControllerName();
        if ($bIsSearch && (!$bIsSideLocation || $sModuleName != 'ultimatevideo.playlist')) {
            return false;
        }

        $sDataSource = $this->getParam('data_source', 'latest');
        $iLimit = $this->getParam('limit', 10);

        if (!$iLimit) {
            return false;
        }

        $aFooter = array();
        switch ($sDataSource) {
            case 'most_viewed':
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostViewedPlaylists($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', array('sort' => 'most-viewed')));
                break;
            case 'most_liked':
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostLikedPlaylists($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', array('sort' => 'most-liked')));
                break;
            case 'most_commented':
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostCommentedPlaylists($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', array('sort' => 'most-commented')));
                break;
            case 'featured':
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getSlideshowPlaylists($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', array('sort' => 'featured')));
                break;
            case 'recommended':
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostRecommendedPlaylists($iLimit);
                break;
            case 'related':
                if (!defined('ULTIMATE_PLAYLIST_CATEGORY_ID') || !ULTIMATE_PLAYLIST_CATEGORY_ID) {
                    return false;
                }
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostRelatedPlaylists($iLimit);
                break;
            case 'more_from_user':
                if (!defined('ULTIMATE_PLAYLIST_OWNER_ID') || !ULTIMATE_PLAYLIST_OWNER_ID) {
                    return false;
                }
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getUserPostedPlaylists($iLimit, ULTIMATE_PLAYLIST_OWNER_ID);
                break;
            default:
                $aItems = Phpfox::getService('ultimatevideo.playlist.browse')->getMostRecentPlaylists($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo.playlist', array('sort' => 'latest')));
                break;
        }

        if (empty($aItems)) {
            return false;
        }

        Phpfox::getService('ultimatevideo.playlist.browse')->processRows($aItems);

        if (!$this->getParam('display_view_more', 0)) {
            $aFooter = array();
        }

        $iBlockId = $this->getParam('id', 0);
        $aViewModes = $this->getParam('view_modes', array());

        if ($bIsSideLocation || $sDataSource == 'featured') {
            $aSupportedViewModes = array(
                'grid' => array(
                    'key' => 'grid',
                    'title' => _p('Grid View'),
                    'icon' => 'th',
                ),
            );
        } else {
            $aSupportedViewModes = array(
                'grid' => array(
                    'key' => 'grid',
                    'title' => _p('grid_view'),
                    'icon' => 'th',
                ),
                'list' => array(
                    'key' => 'list',
                    'title' => _p('list_view'),
                    'icon' => 'list',
                ),
                'customcasual' => array(
                    'key' => 'customcasual',
                    'title' => _p('casual_view'),
                    'icon' => 'casual',
                ),
            );

            foreach ($aSupportedViewModes as $key => $aViewMode) {
                if (!in_array($key, $aViewModes)) {
                    unset($aSupportedViewModes[$key]);
                }
            }
        }

        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewId', 'ultimatevideo-playlist-' . $iBlockId);

        $sCustomContainerClassName = '';
        $sCustomContainerClassName .= $bIsSideLocation ? ' none-border' : ' p-mode-view';
        $sCustomContainerClassName .= ($sDataSource == 'featured' && !$bIsSideLocation) ? ' ultimatevideo-playlist-featured-container' : '';

        $aInfo = Phpfox::getService('ultimatevideo')->getPlaylistInfoConfig($sDataSource, $sBlockLocation);
        $this->template()->assign([
            'sHeader' => $this->getHeader($sDataSource),
            'bShowTotalView' => true,
            'bShowTotalLike' => true,
            'bShowTotalComment' => false,
            'sCustomContainerClassName' => $sCustomContainerClassName,
            'aItems' => $aItems,
            'aInfo' => $aInfo,
            'sCustomClassName' => 'p-block',
            'sDataSource' => $sDataSource,
            'bIsSideLocation' => $bIsSideLocation,
            'aFooter' => $aFooter,
            'bShowModeration' => 0,
            'bShowCommand' => 0,
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return array(
            array(
                'info' => _p('data_source'),
                'value' => 'latest',
                'options' => array(
                    'latest' => _p('latest_playlists'),
                    'most_viewed' => _p('most_viewed_playlists'),
                    'most_liked' => _p('most_liked_playlists'),
                    'most_commented' => _p('most_commented_playlists'),
                    'featured' => _p('featured_playlists'),
                    'recommended' => _p('recommended_playlists'),
                    'related' => _p('related_playlists'),
                    'more_from_user' => _p('more_from_user_playlists'),
                ),
                'type' => 'select',
                'var_name' => 'data_source',
            ),
            array(
                'info' => _p('display_view_more_link'),
                'description' => _p('display_view_more_link_playlist_desc'),
                'value' => true,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('playlist_limit'),
                'description' => _p('playlist_limit_desc'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ),
            array(
                'info' => _p('cache_time'),
                'description' => _p('Define how long we should keep the cache for the playlists by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ),
            array(
                'info' => _p('view_modes'),
                'description' => _p('view_modes_desc'),
                'value' => array('grid', 'list'),
                'options' => array(
                    'grid' => _p('grid_view'),
                    'list' => _p('list_view'),
                ),
                'type' => 'multi_checkbox',
                'var_name' => 'view_modes',
            )
        );
    }

    public function getHeader($dataSource) {
        switch ($dataSource) {
            case 'most_viewed':
                $header = _p('most_viewed_playlists');
                break;
            case 'most_liked':
                $header = _p('most_liked_playlists');
                break;
            case 'most_commented':
                $header = _p('most_commented_playlists');
                break;
            case 'featured':
                $header = _p('featured_playlists');
                break;
            case 'recommended':
                $header = _p('recommended_playlists');
                break;
            case 'related':
                $header = _p('related_playlists');
                break;
            case 'more_from_user':
                $header = _p('more_from_user_playlists');
                break;
            default:
                $header = _p('latest_playlists');
                break;
        }

        return $header;
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => 'playlist_limit_is_required_and_must_greater_or_equal_zero'
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