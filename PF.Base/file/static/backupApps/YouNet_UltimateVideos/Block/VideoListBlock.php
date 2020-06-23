<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/11/16
 * Time: 9:28 AM
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

class VideoListBlock extends \Phpfox_Component
{
    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);

        $sModuleName = Phpfox::getLib('module')->getFullControllerName();
        if ($bIsSearch && (!$bIsSideLocation || $sModuleName != 'ultimatevideo.index')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 6);

        if (!$iLimit) {
            return false;
        }

        $cacheTime = $this->getParam('cache_time', 5);

        $aFooter = array();
        $sDataSource = $this->getParam('data_source', 'latest');
        switch ($sDataSource) {
            case 'most_viewed':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostViewedVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'most-viewed')));
                break;
            case 'most_liked':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostLikedVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'most-liked')));
                break;
            case 'most_commented':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostCommentedVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'most-commented')));
                break;
            case 'top_rated':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getTopRatedVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'highest-rated')));
                break;
            case 'featured':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getSlideshowVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'featured')));
                break;
            case 'recommended':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostRecommendedVideos($iLimit);
                break;
            case 'related':
                if (!defined('ULTIMATE_VIDEO_CATEGORY_ID') || !ULTIMATE_VIDEO_CATEGORY_ID) {
                    return false;
                }
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostRelatedVideos($iLimit);
                break;
            case 'more_from_user':
                if (!defined('ULTIMATE_VIDEO_OWNER_ID') || !ULTIMATE_VIDEO_OWNER_ID) {
                    return false;
                }
                $aItems = Phpfox::getService('ultimatevideo.browse')->getUserPostedVideos($iLimit, ULTIMATE_VIDEO_OWNER_ID);
                break;
            case 'watch_it_again':
                $aItems = Phpfox::getService('ultimatevideo.browse')->getWatchItAgainVideos($iLimit);
                break;
            case 'sponsor_video':
                if (!Phpfox::isAppActive('Core_BetterAds')) {
                    return false;
                }
                if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
                    return false;
                }
                $aItems = Phpfox::getService('ultimatevideo')->getSponsoredItems($iLimit, $cacheTime);
                if (empty($aItems)) {
                    return false;
                }

                foreach ($aItems as $key => $aItem) {
                    Phpfox::getService('ad.process')->addSponsorViewsCount($aItem['sponsor_id'], 'ultimatevideo_video');
                    $aItems[$key]['item_url'] = \Phpfox_Url::instance()->makeUrl('ad.sponsor', ['view' => $aItem['sponsor_id']]);
                }
                break;
            default:
                $aItems = Phpfox::getService('ultimatevideo.browse')->getMostRecentVideos($iLimit);
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ultimatevideo', array('sort' => 'latest')));
                break;
        }

        if (empty($aItems)) {
            return false;
        }

        Phpfox::getService('ultimatevideo.browse')->processRows($aItems);

        $iBlockId = $this->getParam('id', 0);
        $aViewModes = $this->getParam('view_modes', array());
        $bIsSlider = $this->getParam('is_slider', 0) && !$bIsSideLocation;

        if (!$this->getParam('display_view_more', 0) || $bIsSlider) {
            $aFooter = array();
        }

        if ($bIsSideLocation) {
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
        $this->setParam('sModeViewId', 'ultimatevideo-video-' . $iBlockId);

        $sCustomContainerClassName = '';
        $sCustomContainerClassName .= $bIsSideLocation ? ' none-border' : ' p-mode-view';

        $aInfo = Phpfox::getService('ultimatevideo')->getInfoConfig($sDataSource, $sBlockLocation);

        if ($this->getParam('display_ranking')) {
            $aInfo['display_ranking'] = 1;
        }

        $this->template()
            ->assign([
                'sHeader' => $this->getHeader($sDataSource),
                'bShowTotalView' => true,
                'bShowTotalLike' => true,
                'bShowTotalComment' => false,
                'aItems' => $aItems,
                'sCustomClassName' => 'p-block ' . $sDataSource,
                'sCustomContainerClassName' => $sCustomContainerClassName,
                'aInfo' => $aInfo,
                'bIsSlider' => $bIsSlider,
                'bIsSideLocation' => $bIsSideLocation,
                'sDataSource' => $sDataSource,
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
                    'latest' => _p('latest_videos'),
                    'most_viewed' => _p('most_viewed_videos'),
                    'most_liked' => _p('most_liked_videos'),
                    'most_commented' => _p('most_commented_videos'),
                    'top_rated' => _p('top_rated_videos'),
                    'featured' => _p('featured_videos'),
                    'recommended' => _p('recommended_videos'),
                    'related' => _p('related_videos'),
                    'more_from_user' => _p('more_from_user_videos'),
                    'watch_it_again' => _p('watch_it_again'),
                    'sponsor_video' => _p('ultimatevideo_sponsored_video')
                ),
                'type' => 'select',
                'var_name' => 'data_source',
            ),
            array(
                'info' => _p('display_ranking_of_each_video'),
                'description' => _p('display_ranking_of_each_video_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_ranking',
            ),
            array(
                'info' => _p('display_view_more_link'),
                'description' => _p('display_view_more_link_video_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('video_limit'),
                'description' => _p('video_limit_desc'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ),
            array(
                'info' => _p('cache_time'),
                'description' => _p('Define how long we should keep the cache for the videos by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ),
            array(
                'info' => _p('slider_format'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'is_slider',
            ),
            array(
                'info' => _p('view_modes'),
                'description' => _p('view_modes_desc'),
                'value' => array(
                    'grid',
                    'list',
                    'customcasual',
                ),
                'options' => array(
                    'grid' => _p('grid_view'),
                    'list' => _p('list_view'),
                    'customcasual' => _p('casual_view'),
                ),
                'type' => 'multi_checkbox',
                'var_name' => 'view_modes',
            )
        );
    }

    public function getValidation()
    {
        return array(
            'limit' => array(
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('video_limit_is_required_and_must_greater_or_equal_zero')
            )
        );
    }

    public function getHeader($dataSource)
    {
        switch ($dataSource) {
            case 'most_viewed':
                $header = _p('most_viewed_videos');
                break;
            case 'most_liked':
                $header = _p('most_liked_videos');
                break;
            case 'most_commented':
                $header = _p('most_commented_videos');
                break;
            case 'top_rated':
                $header = _p('top_rated_videos');
                break;
            case 'featured':
                $header = _p('featured_videos');
                break;
            case 'recommended':
                $header = _p('recommended_videos');
                break;
            case 'related':
                $header = _p('related_videos');
                break;
            case 'more_from_user':
                $header = _p('more_from_user_videos');
                break;
            case 'watch_it_again':
                $header = _p('watch_it_again');
                break;
            case 'sponsor_video':
                $header = _p('sponsored_videos');
                break;
            default:
                $header = _p('latest_videos');
                break;
        }

        return $header;
    }
}