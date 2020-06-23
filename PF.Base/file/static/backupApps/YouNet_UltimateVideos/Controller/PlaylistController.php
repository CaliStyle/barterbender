<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/26/16
 * Time: 10:06 AM
 */

namespace Apps\YouNet_UltimateVideos\Controller;

use Core\Route\Controller;
use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');


class PlaylistController extends Phpfox_Component
{
    public function process()
    {
        $bIsSearch = false;
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsSearch = true;
            Controller::$name = true;
        } else {

            Controller::$name = '';
        }
        $bIsInHomePage = $this->_checkIsInHomePage();
        $aCheckView = ['my', 'favorite', 'later', 'history', 'friend'];
        if (in_array($this->request()->get('req3'), $aCheckView)) {
            Phpfox::isUser(true);
        }
        if ($this->request()->get('req3') == 'pending') {
            $this->url()->send('ultimatevideo.playlist', ['view' => 'pending']);
            return;
        }

        if ($this->request()->get('req3') == 'my') {
            Phpfox::isUser(true);
            $this->url()->send('ultimatevideo.playlist', ['view' => 'my']);
            return;
        }
        if ($this->request()->get('req3') == 'friend') {
            Phpfox::isUser(true);
            $this->url()->send('ultimatevideo.playlist', ['view' => 'friend']);
            return;
        }

        $bIsUserProfile = $this->getParam('bIsProfile');
        $bShowModeration = (user('ynuv_can_delete_playlist_of_other_user') || user('ynuv_can_feature_playlist'));
        $aUser = [];
        if ($bIsUserProfile) {
            $aUser = $this->getParam('aUser');
        }
        $sView = $this->request()->get('view');
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

        Phpfox::getService('ultimatevideo.callback')->buildFilterMenu($this);

        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $bIsSearch = true;
            Controller::$name = true;
        } else {
            Controller::$name = '';
        }

        $aSort = [
            'latest' => ['playlist.time_stamp', _p('Latest')],
            'most-viewed' => ['playlist.total_view', _p('most_viewed')],
            'most-liked' => ['playlist.total_like', _p('most_liked')],
            'most-commented' => ['playlist.total_comment', _p('most_commented')],
            'featured' => ['playlist.playlist_id', _p('Featured')]
        ];
        if ($sView == 'historyplaylist') {
            $aSort = array_merge(['recent' => ['history.time_stamp', _p('Recent Viewed')]], $aSort);
        } else {
            $this->search()->setCondition(' AND playlist.privacy IN (%PRIVACY%)');
        }
        $aSearchFields = [
            'type' => 'ultimatevideo_playlists',
            'field' => 'playlist.playlist_id',
            'search_tool' => [
                'table_alias' => 'playlist',
                'search' => [
                    'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'] . '.ultimatevideo.playlist') : $this->url()->makeUrl('ultimatevideo.playlist', ['view' => $this->request()->get('view')])),
                    'default_value' => _p('search_playlists'),
                    'name' => 'search',
                    'field' => 'playlist.title',
                ],
                'sort' => $aSort,
                'show' => [10, 15, 20],
            ],
        ];

        if ($bIsInHomePage) {
            $aSearchFields['search_tool']['no_filters'] = [_p('sort'), _p('show'), _p('when')];
            unset($aSearchFields['search_tool']['custom_filters']);
        }

        $this->search()->set($aSearchFields);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo.playlist',
            'alias' => 'playlist',
            'field' => 'playlist_id',
            'table' => Phpfox::getT('ynultimatevideo_playlists'),
            'hide_view' => ['pending', 'my'],
        ];

        if ((!defined('PHPFOX_IS_PAGES_VIEW') && user('ynuv_can_upload_video') && !defined('PHPFOX_IS_USER_PROFILE'))) {
            sectionMenu(_p('share_a_video'), url('/ultimatevideo/add'));
        }
        if (user('ynuv_can_add_playlist')) {
            sectionMenu(_p('create_a_playlist'), url('/ultimatevideo/addplaylist'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            foreach (['sort', 'show', 'when', 'view', 's', 'user', 'tag'] as $temp) {
                if (!empty($_GET[$temp])) {
                    $bIsSearch = true;
                }
            }
        }
        if ($this->request()->get('req3') == 'my_playlist') {
            $bIsSearch = true;
        }
        if ($this->request()->get('sort') && $this->request()->get('sort') == 'featured') {
            $this->search()->setCondition(' AND playlist.is_featured=1 ');
        }

        if ($sView)
            $bIsSearch = true;

        $aBreadCrumb = [
            'title' => _p('playlists'),
            'link' => empty($sView) ? $this->url()->makeUrl('ultimatevideo.playlist') : $this->url()->makeUrl('ultimatevideo.playlist.view_' . $sView)
        ];

        $sCategory = null;
        if ($this->request()->get(($bIsProfile ? 'req4' : 'req3')) == 'category') {
            $sCategory = $this->request()->getInt($bIsProfile ? 'req5' : 'req4');
            $bIsSearch = true;
            $sCategoryList = Phpfox::getService('ultimatevideo.category')->getCategoryIdDescendants($sCategory);
            $this->search()->setCondition('AND playlist.category_id IN( ' . $sCategoryList . ')');
        }

        switch ($sView) {
            case 'my':
            case 'myplaylist':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND playlist.user_id=' . intval(Phpfox::getUserId()));
                $aBreadCrumb['title'] = _p('my_playlists');
                break;
            case 'pendingplaylist':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND playlist.is_approved=0');
                $aBreadCrumb['title'] = _p('pending_playlist');
                break;
            case 'featured':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND playlist.is_featured=1');
                break;
            case 'historyplaylist':
                Phpfox::isUser(true);
                $bShowModeration = false;
                $aBreadCrumb['title'] = _p('playlists_history');
                break;
            case 'friendplaylist':
                Phpfox::isUser(true);
                $aBreadCrumb['title'] = _p('ultimatevideos_friends_playlists');
                break;
            default:
                if (defined('PHPFOX_IS_USER_PROFILE')) {
                    $this->search()->setCondition(' AND playlist.user_id=' . intval($aUser['user_id']) . ' AND playlist.is_approved = 1');
                    break;
                }
                $this->search()->setCondition(' AND playlist.is_approved=1');
                break;
        }
        $this->template()->setBreadCrumb($aBreadCrumb['title'], $aBreadCrumb['link']);

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
        }

        $iFromUser = $this->request()->get('user');
        if ($iFromUser) {
            $this->search()->setCondition(' AND playlist.user_id=' . intval($iFromUser));
        }

        $aItems = null;
        if ($bIsSearch) {
//            $this->search()->setCondition(true);
            $this->search()->browse()->setPagingMode(Phpfox::getParam('ultimatevideo.ynuv_paging_mode', 'loadmore'));

            $this->search()->browse()->params($aBrowseParams)->execute();

            $aItems = $this->search()->browse()->getRows();

            // Set pager
            $aParamsPager = array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $this->search()->browse()->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );

            Phpfox::getLib('pager')->set($aParamsPager);

            // mode view for video search page
            // hiding viewmode in my video pages is requested by ThaoTLH
            // in https://docs.google.com/spreadsheets/d/1MqNGsFwNjVQwbOyu2Dtmqvawj9OH5DLxd2yt8ExLoCE/edit#gid=858725754
            // and approved by PM HanhNTH
            if (!empty($sView) && in_array($sView, array('myplaylist', 'historyplaylist'))) {
                $aSupportedViewModes = array(
                    'grid' => array(
                        'key' => 'grid',
                        'title' => _p('grid_view'),
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
                );
            }

            $aInfo = array(
                'view' => 1,
            );

            $this->setParam('aSupportedViewModes', $aSupportedViewModes);
            $this->setParam('sModeViewId', 'ultimatevideo-playlist-listing');
            $this->template()->assign(array('aInfo' => $aInfo));
        }

        if ($bShowModeration) {
            $aModerationMenu = [];
            if (user('ynuv_can_approve_playlist') && $sView == 'pendingplaylist') {
                $aModerationMenu[] = [
                    'phrase' => _p('Approve'),
                    'action' => 'approve'
                ];
            }
            if (user('ynuv_can_delete_playlist_of_other_user')) {
                $aModerationMenu[] = [
                    'phrase' => _p('core.delete'),
                    'action' => 'delete'
                ];
            }

            if (user('ynuv_can_feature_playlist')) {
                $aModerationMenu[] = [
                    'phrase' => _p('Feature'),
                    'action' => 'feature'
                ];
                $aModerationMenu[] = [
                    'phrase' => _p('Un-Feature'),
                    'action' => 'unfeature'
                ];
            }
            if ($sView == 'historyplaylist') {
                $aModerationMenu[] = [
                    'phrase' => _p('remove_all_from_history'),
                    'action' => 'history'
                ];
            }
            $this->setParam('global_moderation', [
                'name' => 'ultimatevideo_playlist',
                'ajax' => 'ultimatevideo.playlist_moderation',
                'menu' => $aModerationMenu
            ]);
        }
        if (count($aItems)) {
            foreach ($aItems as $key => $aPlaylist) {
                $aItems[$key]['video_list'] = Phpfox::getService('ultimatevideo.playlist.browse')->getSomeVideoOfPlaylist($aPlaylist['playlist_id']);
            }
        }
        $bIsNoItem = false;

        if ($bIsInHomePage) {
            $bIsNoItem = true;
        }
        if ($this->getParam('bIsProfile')) {
            $bIsNoItem = false;
        }

        $this->setParam('bIsSearch', $bIsSearch);
        $this->setParam('sCategory', $sCategory);
        $this->template()->assign([
            'sView' => $sView,
            'aItems' => $aItems,
            'bVideoView' => true,
            'bIsSearch' => $bIsSearch,
            'bShowTotalView' => true,
            'bShowTotalLike' => true,
            'bShowModeration' => $bShowModeration,
            'bIsUserProfile' => $bIsUserProfile,
            'iPage' => $this->request()->get('page'),
            'bIsNoItem' => $bIsNoItem,
        ]);

        $this->template()
            ->setMeta('keywords', Phpfox::getParam('ultimatevideo.ynuv_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('ultimatevideo.ynuv_meta_description'));
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $sTempView = $this->request()->get('view', false);
        if (!$sTempView && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('user')
            && !$this->request()->get('show')
            && !$this->request()->get('s')
            && !$this->request()->get('tag')
            && empty($this->request()->get('req3'))
            && !defined(PHPFOX_IS_USER_PROFILE)
        ) {
            $bIsInHomePage = true;
        }

        return $bIsInHomePage;
    }
}