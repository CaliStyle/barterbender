<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/26/16
 * Time: 10:06 AM
 */

namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

use Core\Route\Controller;

class IndexController extends \Phpfox_Component
{
    public function process()
    {
        if (!setting('ynuv_app_enabled')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        $sView = $this->request()->get('view');
        if ($sView == 'pending' && !user('ultimatevideo.ynuv_can_approve_video')) {
            $this->url()->send('ultimatevideo');
        }
        $bIsSearch = false;
        $bCheckPage = false;
        $aParentModule = $this->getParam('aParentModule');
        $bIsInHomePage = $this->_checkIsInHomePage();

        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {

            if (($this->request()->get('req1') == 'pages' && Phpfox::isModule('pages') == false) ||
                ($aParentModule['module_id'] == 'pages' && Phpfox::getService('pages')->hasPerm($aParentModule['item_id'], 'ultimatevideo.view_browse_videos') == false)) {
                return \Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
            }
        }
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $bIsSearch = true;
            Controller::$name = true;
        } else {

            Controller::$name = '';
        }

        $bIsUserProfile = $this->getParam('bIsProfile');
        $aUser = [];
        $bShowModeration = (user('ynuv_can_approve_video') || user('ynuv_can_delete_video_of_other_user') || user('ynuv_can_feature_video'));

        $bCurrentUser = false;
        if ($bIsUserProfile) {
            $aUser = $this->getParam('aUser');
            $userId = intval($aUser['user_id']);
            $currentId = intval(Phpfox::getUserId());
            $res = $userId - $currentId;
            if (defined('PHPFOX_IS_USER_PROFILE') && $res == 0) {
                $bCurrentUser = true;
            }
            $this->search()->setCondition('AND video.is_approved = 1');
        }

        if ((!defined('PHPFOX_IS_PAGES_VIEW') && user('ynuv_can_upload_video') && !defined('PHPFOX_IS_USER_PROFILE')) || $bCurrentUser) {
            sectionMenu(_p('share_a_video'), url('/ultimatevideo/add'));
        }
        if (!(defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW) && user('ynuv_can_add_playlist')) {
            sectionMenu(_p('create_a_playlist'), url('/ultimatevideo/addplaylist'));
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        Phpfox::getService('ultimatevideo.callback')->buildFilterMenu();

        $sView = $this->request()->get('view');
        $aSort = [
            'latest' => ['video.time_stamp', _p('Latest')],
            'most-viewed' => ['video.total_view', _p('most_viewed')],
            'most-liked' => ['video.total_like', _p('most_liked')],
            'most-commented' => ['video.total_comment', _p('most_commented')],
            'highest-rated' => ['video.rating', _p('highest_rated')],
            'featured' => ['video.video_id', _p('Featured')]
        ];
        $aSearchFields = [
            'type' => 'ultimatevideo',
            'field' => 'video.video_id',
            'search_tool' => [
                'table_alias' => 'video',
                'search' => [
                    'action' => (($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('ultimatevideo', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('ultimatevideo', array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'ultimatevideo/view_' . $this->request()->get('view') . '/')),
                    'default_value' => _p('search_videos'),
                    'name' => 'search',
                    'field' => array('video.title'),
                ],
                'sort' => $aSort,
                'show' => [12, 24, 36],
            ],
        ];
        if ($bIsInHomePage) {
            $aSearchFields['search_tool']['no_filters'] = [_p('sort'), _p('show'), _p('when')];
            unset($aSearchFields['search_tool']['custom_filters']);
        }

        $this->search()->set($aSearchFields);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('ynultimatevideo_videos'),
            'hide_view' => ['pending', 'my'],
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            foreach (['sort', 'show', 'when', 'view', 's', 'user', 'tag'] as $temp) {
                if (!empty($_GET[$temp])) {
                    $bIsSearch = true;
                    break;
                }
            }
        }

        $sCategory = null;
        if ($this->request()->get(($bIsProfile ? 'req3' : 'req2')) == 'category') {
            $bIsSearch = true;
            $sCategory = $this->request()->getInt(($bIsProfile ? 'req4' : 'req3'));
            $sCategoryList = Phpfox::getService('ultimatevideo.category')->getCategoryIdDescendants($sCategory);
            $this->search()->setCondition('AND video.category_id IN( ' . $sCategoryList . ')');
        }

        if ($this->request()->get('sort') && $this->request()->get('sort') == 'featured') {
            $this->search()->setCondition(' AND video.is_featured=1 ');
        }

        if ($bIsSearch) {
            switch ($sView) {
                case 'history':
                    Phpfox::isUser(true);
                    $bShowModeration = false;
                    break;
                case 'later':
                    Phpfox::isUser(true);
                    $bShowModeration = false;
                    break;
                case 'favorite':
                    Phpfox::isUser(true);
                    $bShowModeration = true;
                    break;
                case 'my':
                    Phpfox::isUser(true);
                    $this->search()->setCondition(' AND video.module_id !="pages" AND video.module_id !="groups" AND video.user_id=' . intval(Phpfox::getUserId()));
                    break;
                case 'pending':
                    Phpfox::isUser(true);
                    $this->search()->setCondition(' AND video.is_approved=0');
                    break;
                case 'featured':
                    $this->search()->setCondition(' AND video.is_approved=0 AND video.status=1 AND video.is_featured=1');
                    break;
                default:
                    if (defined('PHPFOX_IS_PAGES_VIEW') && $aParentModule != null) {
                        if (Phpfox::isAdmin()) {
                            $this->search()->setCondition(' AND video.module_id like "' . $aParentModule['module_id'] . '" AND video.item_id =' . $aParentModule['item_id']);
                        } else {
                            $this->search()->setCondition(' AND video.module_id like "' . $aParentModule['module_id'] . '" AND video.item_id =' . $aParentModule['item_id'] . ' AND video.is_approved!=0');
                        }
                        break;
                    }
                    if (defined('PHPFOX_IS_USER_PROFILE')) {

                        $this->search()->setCondition(' AND video.user_id=' . intval($aUser['user_id']));
                        break;
                    }
                    $this->search()->setCondition(' AND video.is_approved=1 AND video.status=1 AND video.module_id !="pages" AND video.module_id !="groups"');
                    break;
            }
            if ($sView != 'history' && $sView != 'pending') {
                $this->search()->setCondition(' AND video.privacy IN (%PRIVACY%)');
            }
            // mode view for video search page
            // hiding viewmode in my video pages is requested by ThaoTLH
            // in https://docs.google.com/spreadsheets/d/1MqNGsFwNjVQwbOyu2Dtmqvawj9OH5DLxd2yt8ExLoCE/edit#gid=858725754
            // and approved by PM HanhNTH
            if (!empty($sView) && in_array($sView, array('my', 'favorite', 'later', 'history'))) {
                $aSupportedViewModes = array(
                    'grid' => array(
                        'key' => 'grid',
                        'title' => _p('Grid View'),
                        'icon' => 'th',
                    )
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
            }

            $aInfo = array(
                'view' => 1,
                'like' => 1,
                'rating' => 1,
            );

            $this->setParam('aSupportedViewModes', $aSupportedViewModes);
            $this->setParam('sModeViewId', 'ultimatevideo-video-listing');
            $this->template()->assign(array('aInfo' => $aInfo));
        }

        $iFromUser = $this->request()->get('user');
        if ($iFromUser) {
            $this->search()->setCondition(' AND video.module_id !="pages" AND video.module_id !="groups" AND video.user_id=' . intval($iFromUser));
        }
        $aItems = null;

        if ($bIsSearch) {
            $this->search()->setContinueSearch(true);
            $this->search()->browse()->setPagingMode(Phpfox::getParam('ultimatevideo.ynuv_paging_mode', 'loadmore'));
            $this->search()->browse()->params($aBrowseParams)->execute();
            $aItems = $this->search()->browse()->getRows();

            //Get video permissions
            foreach($aItems as $key => $aItem) {
                Phpfox::getService('ultimatevideo')->getPermissions($aItems[$key]);
            }

            $aParamsPager = array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $this->search()->browse()->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );
            Phpfox::getLib('pager')->set($aParamsPager);
        }

        $bIsNoItem = false;
        if (!$bIsSearch) {
            if (!Phpfox::getService('ultimatevideo.browse')->getMostRecommendedVideos(5) && !Phpfox::getService('ultimatevideo.browse')->getSlideshowVideos(5) && !Phpfox::getService('ultimatevideo.browse')->getWatchItAgainVideos(5) && !Phpfox::getService('ultimatevideo.playlist.browse')
                    ->getMostRecentPlaylists(5)) {
                $bIsNoItem = true;
            }
        }
        if ($aParentModule['module_id'] == 'groups' || $aParentModule['module_id'] == 'pages') {
            if ($aParentModule['module_id'] == 'pages' && Phpfox::isModule('pages') && Phpfox::getService('pages')->isAdmin($aParentModule['item_id'])) {
                $bCheckPage = true; // is owner of page
            } elseif ($aParentModule['module_id'] == 'groups' && Phpfox::isModule('groups') && Phpfox::getService('groups')->isAdmin($aParentModule['item_id'])) {
                $bCheckPage = true; // is owner of page
            }
        }

        if (defined('PHPFOX_IS_PAGES_VIEW')) {
            $bShowModeration = false;
        }

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $this->setParam('sCategory', $sCategory);
        $this->setParam('bIsSearch', $bIsSearch);
        $cnt = $this->search()->browse()->getCount();
        $this->template()->assign([
            'iCnt' => $cnt,
            'aItems' => $aItems,
            'bVideoView' => true,
            'bIsSearch' => $bIsSearch,
            'sView' => $sView,
            'bShowTotalView' => true,
            'bShowTotalLike' => true,
            'bShowTotalComment' => false,
            'bShowModeration' => $bShowModeration,
            'corePath' => $corePath,
            'bIsPagesView' => defined('PHPFOX_IS_PAGES_VIEW') ? true : false,
            'bIsUserProfile' => $bIsUserProfile,
            'iPage' => $this->request()->get('page'),
            'bIsNoItem' => $bIsNoItem,
            'bCheckPage' => $bCheckPage,
            'bIsInHomePage' => $bIsInHomePage
        ]);

        if ($bShowModeration) {
            $aModerationMenu = [];
            if (user('ynuv_can_delete_video_of_other_user')) {
                $aModerationMenu[] = [
                    'phrase' => _p('core.delete'),
                    'action' => 'delete'
                ];
            }

            if (user('ynuv_can_approve_video') && $sView == 'pending') {
                $aModerationMenu[] = [
                    'phrase' => _p('Approve'),
                    'action' => 'approve'
                ];
            }
            if (user('ynuv_can_feature_video')) {
                $aModerationMenu[] = [
                    'phrase' => _p('Feature'),
                    'action' => 'feature'
                ];
                $aModerationMenu[] = [
                    'phrase' => _p('Un-Feature'),
                    'action' => 'unfeature'
                ];
            }
            if ($sView == 'history') {
                $aModerationMenu[] = [
                    'phrase' => _p('remove_all_from_history'),
                    'action' => 'history'
                ];
            }
            if ($sView == 'later') {
                $aModerationMenu[] = [
                    'phrase' => _p('unwatched_all_selected'),
                    'action' => 'unwatched'
                ];
            }
            // Special case for favorite
            if ($sView == 'favorite') {
                $aModerationMenu = [[
                    'phrase' => _p('unfavorite_all_selected'),
                    'action' => 'unfavorite'
                ]];
            }
            $this->setParam('global_moderation', [
                'name' => 'ultimatevideo',
                'ajax' => 'ultimatevideo.moderation',
                'menu' => $aModerationMenu
            ]);
        }

        if ($this->request()->get('user')) {
            $aUser = Phpfox::getService('user')->getUser($this->request()->get('user'));
            $this->template()->setBreadCrumb(_p('name_s_videos', ['name' => $aUser['full_name']]), '');
        } else {
            $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo', null, false))
                ->setTitle(_p('ultimate_videos'));
        }
        if ($bIsSearch) {
            if ($sCategory) {
                $aCategory = Phpfox::getService('ultimatevideo.category')->getCategoryById($sCategory);
                if ($aCategory['parent_id'] > 0 && $aParentCategory = Phpfox::getService('ultimatevideo.category')->getCategoryById($aCategory['parent_id'])) {
                    if ($aParentCategory['parent_id'] && $aGrandParentCategory = Phpfox::getService('ultimatevideo.category')->getCategoryById($aParentCategory['parent_id'])) {
                        $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(_p($aGrandParentCategory['title'])), $this->url()->permalink('ultimatevideo.category', $aGrandParentCategory['category_id'], $aGrandParentCategory['title']));
                    }

                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(_p($aParentCategory['title'])), $this->url()->permalink('ultimatevideo.category', $aParentCategory['category_id'], $aParentCategory['title']));
                }

                $this->template()->setBreadCrumb(_p($aCategory['title']), Phpfox::permalink('ultimatevideo.category', $aCategory['category_id'],
                    $aCategory['title']));
            } else {
                $this->template()->setBreadCrumb(_p('all_videos'), Phpfox::permalink('ultimatevideo', null, false))
                    ->setTitle(_p('all_videos'));
            }
        }
        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aParentModule['item_id'], 'ultimatevideo.view_browse_videos')) {
                $this->template()->assign(['aSearchTool' => []]);
                return \Phpfox_Error::display(_p('cannot_display_this_section_due_to_privacy'));
            }
            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
                ->setBreadCrumb(_p("ultimate_videos"), $aParentModule['url'] . 'ultimatevideo/');
        }

        $this->template()
            ->setMeta('keywords', Phpfox::getParam('ultimatevideo.ynuv_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('ultimatevideo.ynuv_meta_description'));

        return 'true';
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $sTempView = $this->request()->get('view', false);

        if (!$sTempView && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('s')
            && !$this->request()->get('search')
            && !$this->request()->get('tag')
            && empty($this->request()->get('req2'))
        ) {
            $bIsInHomePage = true;
        }

        return $bIsInHomePage;
    }
}