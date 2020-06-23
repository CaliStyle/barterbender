<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox;
use Phpfox_Component;

class BlogList extends Phpfox_Component
{
    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ynblog.helper')->bIsSideLocation($sBlockLocation);

        if (($bIsSearch || Phpfox::getLib('module')->getFullControllerName() == 'ynblog.following') && !$bIsSideLocation) {
            return false;
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        $iCacheTime = $this->getParam('cache_time', 5);

        $aFooter = array();
        $sDataSource = $this->getParam('data_source', 'latest');

        $sBlockHeader = $this->getParam('title', 'blogs');

        switch ($sDataSource) {
            case 'featured':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('featured_blog', $iLimit, null, 'AND ab.is_featured = 1',$iCacheTime);
                $sBlockHeader = _p('featured_blogs');
                $aFooter = array(_p('all_featured_blogs') => $this->url()->makeUrl('ynblog', array('sort' => 'featured')));
                break;
            case 'most_popular':
                if (Phpfox::isModule('track')) {
                    $sDefinedTime = $this->getParam('defined_time', 'all_time');
                    $aItems = Phpfox::getService('ynblog.blog')->getPopularPosts('most_popular', $iLimit, $sDefinedTime,$iCacheTime);
                } else {
                    $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_read', $iLimit, 'ab.total_view DESC', ' AND ab.total_view > 0',$iCacheTime);
                }
                $sBlockHeader = _p('most_popular_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'most-viewed')));
                break;
            case 'most_viewed':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_read', $iLimit, 'ab.total_view DESC', ' AND ab.total_view > 0',$iCacheTime);
                $sBlockHeader = _p('most_viewed_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'most-viewed')));
                break;
            case 'most_liked':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_liked', $iLimit, 'ab.total_like DESC', ' AND ab.total_like > 0',$iCacheTime);
                $sBlockHeader = _p('most_liked_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'most-liked')));
                break;
            case 'most_favorited':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_favorited', $iLimit, 'ab.total_favorite DESC', ' AND ab.total_favorite > 0',$iCacheTime);
                $sBlockHeader = _p('most_favorited_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'most_favorite')));
                break;
            case 'most_commented':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_discussed', $iLimit, 'ab.total_comment DESC', ' AND ab.total_comment > 0',$iCacheTime);
                $sBlockHeader = _p('most_discussed_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'most-talked')));
                break;
            case 'recommended':
                $aTemps = Phpfox::getService('ynblog.blog')->getRecentPosts('based_on_your_following_bloggers', $iLimit, 'ab.time_stamp DESC', null, $iCacheTime);
                $aItems = [];
                foreach ($aTemps as $aTemp){
                    if(Phpfox::getService('ynblog.blog')->getCheckFollowingBloggers($aTemp['user_id'],Phpfox::getUserId())){
                        array_push($aItems,$aTemp);
                    }
                }
                $sBlockHeader = _p('based_on_your_following_bloggers');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'recommended')));
                break;
            case 'continue_reading':
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('continue_reading', $iLimit, 'ab.time_stamp DESC', null,$iCacheTime);
                $sBlockHeader = _p('continue_reading');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('view' => 'saved')));
                break;
            case 'related':
                if (($iBlogId = $this->getParam('blog_id', 0)) <= 0) {
                    return false;
                }
                $sBlockHeader = _p('related_blogs');
                $aItems = Phpfox::getService('ynblog.blog')->getRelatedBlogs($iBlogId, $iLimit);
                break;
            case 'more_from_user':
                $aBlog = $this->getParam('aBlog');
                if (empty($aBlog['blog_id'])) {
                    return false;
                }
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('same_author', $iLimit, null, 'AND ab.blog_id <> ' . $aBlog['blog_id'] . ' AND u.user_id = ' . $aBlog['user_id'],$iCacheTime);
                $sBlockHeader = _p('also_from_this_author');
                $aFooter = array(_p('view_more') => ynblog_profile($aBlog['user_name']));
                break;
            default:
                $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('recent_post', $iLimit, 'ab.time_stamp DESC', null,$iCacheTime);
                $sBlockHeader = _p('newest_blogs');
                $aFooter = array(_p('view_more') => $this->url()->makeUrl('ynblog', array('sort' => 'latest')));
                break;
        }

        $bIsSlider = $this->getParam('is_slider', 0) && !$bIsSideLocation;
        if (!empty($aItems)) {
            foreach ($aItems as $key => &$aItem) {
                Phpfox::getService('ynblog.blog')->retrievePermissionForBlog($aItem);
                $aItem['ranking'] = $key + 1;
                $aItem['image_url'] = Phpfox::getService('ynblog.helper')->getImagePath($aItem['image_path'], $aItem['server_id'], '_500', $aItem['is_old_suffix'], $bIsSlider);
            }
        } else {
            return false;
        }

        $iBlockId = $this->getParam('id', 0);
        $aViewModes = $this->getParam('view_modes', array());

        if (!$this->getParam('display_view_more', 0)) {
            $aFooter = array();
        } else if ($bIsSlider) {
            $aSliderFooter = $aFooter;
            $aFooter = array();
        }

        $sModeViewDefault = 'list';
        if ($bIsSideLocation) {
            $aSupportedViewModes = array();
        } else {
            $aSupportedViewModes = Phpfox::getService('ynblog.helper')->getSupportedViewModes();
            foreach ($aSupportedViewModes as $key => $aViewMode) {
                if (!in_array($key, $aViewModes)) {
                    unset($aSupportedViewModes[$key]);
                }
            }
            if (!empty($aSupportedViewModes)) {
                $sModeViewDefault = '';
            }
        }

        $this->setParam('sModeViewDefault', $sModeViewDefault);
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewId', 'p-blog-' . $iBlockId);

        $aInfo = Phpfox::getService('ynblog.blog')->getInfoConfig($sDataSource, $sBlockLocation);
        if ($this->getParam('display_ranking')) {
            $aInfo['display_ranking'] = 1;
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p($sBlockHeader),
                'aItems' => $aItems,
                'bIsSlider' => $bIsSlider,
                'bIsSideLocation' => $bIsSideLocation,
                'sDataSource' => $sDataSource,
                'aInfo' => $aInfo,
                'aFooter' => $aFooter,
                'aSliderFooter' => $aSliderFooter,
                'sTypeUnit' => array(
                    'plural' => 'favoriters',
                    'singular' => 'favoriter',
                ),
                'bShowModerator' => false,
                'bShowCommand' => 0,
                'sCustomClassName' => 'p-block',
            ));

        return 'block';
    }

    public function getSettings()
    {
        return array(
            array(
                'info' => _p('data_source'),
                'value' => 'latest',
                'options' => array(
                    'latest' => _p('newest_blogs'),
                    'most_popular' => _p('most_popular_blogs'),
                    'most_viewed' => _p('most_viewed_blogs'),
                    'most_liked' => _p('most_liked_blogs'),
                    'most_favorited' => _p('most_favorited_blogs'),
                    'most_commented' => _p('most_discussed_blogs'),
                    'featured' => _p('featured_blogs'),
                    'recommended' => _p('based_on_your_following_bloggers'),
                    'continue_reading' => _p('continue_reading'),
                    'related' => _p('related_blogs'),
                    'more_from_user' => _p('also_from_this_author'),
                ),
                'type' => 'select',
                'var_name' => 'data_source',
            ),
            array(
                'info' => _p('defined_time'),
                'description' => _p('defined_time_info'),
                'value' => 'all_time',
                'options' => array(
                    'all_time' => _p('all_time'),
                    'this_month' => _p('this_month'),
                    'this_week' => _p('this_week'),
                ),
                'type' => 'select',
                'var_name' => 'defined_time',
            ),
            array(
                'info' => _p('display_ranking_of_each_blog'),
                'description' => _p('display_ranking_of_each_blog_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_ranking',
            ),
            array(
                'info' => _p('display_view_more_link'),
                'description' => _p('display_view_more_link_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('blog_limit'),
                'description' => _p('blog_limit_desc'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ),
            array(
                'info' => _p('cache_time'),
                'description' => _p('Define how long we should keep the cache for the blogs by minutes. 0 means we do not cache data for this block.'),
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
                    'list',
                    'grid',
                    'casual',
                ),
                'options' => array(
                    'list' => _p('list_view'),
                    'grid' => _p('grid_view'),
                    'casual' => _p('casual_view'),
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
                'title' => _p('blog_limit_is_required_and_must_greater_or_equal_zero')
            )
        );
    }
}
