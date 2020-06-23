<?php
/**
 * User: hainm
 * Date: 8/16/16
 */

namespace Apps\YouNet_UltimateVideos\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Url;
use Phpfox_Component;

class Callback extends \Phpfox_Service
{
    public function getLinkVideo($params) {
        $videoTitle = db()->select('title')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where('video_id = '. (int)$params['item_id'])
            ->execute('getSlaveField');
        if(!empty($videoTitle)) {
            return Phpfox::permalink('ultimatevideo', $params['item_id'], Phpfox::getLib('parse.output')->clean($videoTitle));
        }
        return false;
    }

    public function enableSponsor($params)
    {
        if($params['section'] == 'video') {
            Phpfox::getService('ultimatevideo.process')->sponsor($params['item_id'], 1);
        }
    }

    public function getLink($params) {
        if($params['section'] == 'video') {
            $videoTitle = db()->select('title')
                            ->from(Phpfox::getT('ynultimatevideo_videos'))
                            ->where('video_id = '. (int)$params['item_id'])
                            ->execute('getSlaveField');
            if(!empty($videoTitle)) {
                return Phpfox::permalink('ultimatevideo', $params['item_id'], Phpfox::getLib('parse.output')->clean($videoTitle));
            }
            return false;
        }
    }

    public function getToSponsorInfoVideo($videoId)
    {
        $video = Phpfox::getService('ultimatevideo')->getSimpleVideo($videoId, 'video_id, title, description, image_path, image_server_id, user_id, video_id AS item_id');

        if (empty($video)) {
            return array('error' => _p('sponsor_error_video_not_found'));
        }

        $video['title'] = _p('ultimate_video_sponsor_title', array('title' => $video['title']));
        $video['paypal_msg'] = _p('ultimate_video_sponsor_paypal_message', array('title' => $video['title']));
        $video['link'] = Phpfox::permalink('ultimatevideo', $video['video_id'], $video['title']);
        $video['image_dir'] = 'core.url_pic';
        $size = 500;

        if (isset($video['image_server_id']) && $video['image_server_id'] == -1 && !empty($video['image_path'])) {
            $video['image_path'] = Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $video['image_path'];
        } elseif (isset($video['image_server_id']) && $video['image_server_id'] == -2 && !empty($video['image_path'])) {
            $video['image_path'] = str_replace('dailymotion.com/thumbnail/160x120', 'dailymotion.com/thumbnail/640x360',
                $video['image_path']);
        } elseif (empty($video['image_path'])) {
            $video['image_path'] = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg';
        } else {
            $sImagePath = $video['image_path'];
            $video['image_path'] = Phpfox::getLib('image.helper')->display([
                'server_id' => $video['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $sImagePath,
                'suffix' => '_' . $size,
                'return_url' => true
            ]);
            $video['fallback_image_path'] = Phpfox::getLib('image.helper')->display([
                'server_id' => $video['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $sImagePath,
                'suffix' => '_120',
                'return_url' => true
            ]);
        }


        $video = array_merge($video, [
            'redirect_completed' => 'ultimatevideo',
            'message_completed' => _p('purchase_video_sponsor_completed'),
            'redirect_pending_approval' => 'ultimatevideo',
            'message_pending_approval' => _p('purchase_video_sponsor_pending_approval')
        ]);

        return $video;
    }

    public function getGlobalPrivacySettings()
    {
        return [
            'ultimatevideo.default_privacy_setting' => [
                'phrase' => _p('ultimate_videos')
            ]
        ];
    }

    public function getNotificationInvitevideo($aNotification)
    {
        $aRow = \Phpfox::getService('ultimatevideo')->getVideo($aNotification['item_id']);

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sPhrase = _p('users_invited_you_for_viewing_video_title', array('users' => \Phpfox::getService('notification')->getUsers($aNotification), 'title' => \Phpfox::getLib('parse.output')->shorten($aRow['title'], \Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::permalink('ultimatevideo', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => ''
        );
    }

    public function getNotificationInviteplaylist($aNotification)
    {
        $aRow = \Phpfox::getService('ultimatevideo.playlist')->getPlaylistById($aNotification['item_id']);

        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $sPhrase = _p('users_invited_you_for_viewing_playlist_title', array('users' => \Phpfox::getService('notification')->getUsers($aNotification), 'title' => \Phpfox::getLib('parse.output')->shorten($aRow['title'], \Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => ''
        );
    }

    public function getWatchLaterVideoTotal()
    {
        if (!Phpfox::getUserId())
            return 0;
        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_watchlaters'), 'la')
            ->where('la.user_id=' . intval(Phpfox::getUserId()))
            ->execute('getSlaveField'));
    }

    public function getHistoryVideoTotal()
    {
        if (!Phpfox::getUserId())
            return 0;
        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_history'), 'hs')
            ->where('hs.user_id=' . intval(Phpfox::getUserId()) . ' AND hs.item_type = 0')
            ->execute('getSlaveField'));
    }

    public function getHistoryPlaylistTotal()
    {
        if (!Phpfox::getUserId())
            return 0;
        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_history'), 'hs')
            ->where('hs.user_id=' . intval(Phpfox::getUserId()) . ' AND hs.item_type = 1')
            ->execute('getSlaveField'));
    }

    public function getFavoriteVideoTotal()
    {
        if (!Phpfox::getUserId())
            return 0;

        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_favorites'), 'fa')
            ->where('fa.user_id=' . intval(Phpfox::getUserId()))
            ->execute('getSlaveField'));
    }

    /**
     * @return int
     */
    public function getPendingPlaylistTotal()
    {
        if (!Phpfox::getUserId())
            return 0;

        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'playlist')
            ->where('playlist.is_approved=0')
            ->execute('getSlaveField'));
    }

    /**
     * @return int
     */
    public function getPendingVideoTotal()
    {
        if (!Phpfox::getUserId())
            return 0;

        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'video')
            ->where('video.is_approved=0')
            ->execute('getSlaveField'));
    }

    public function pendingApproval()
    {
        return array(
            'phrase' => _p('ultimate_videos'),
            'value' => $this->getPendingVideoTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('ultimatevideo', array('view' => 'pending'))
        );
    }

    /**
     *
     */
    public function buildFilterMenu()
    {

        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $iMyVideosNumbers = (int)Phpfox::getService('ultimatevideo')->countMyVideosOfUser(Phpfox::getUserId());
            $iMyPlaylistsNumbers = (int)Phpfox::getService('ultimatevideo.playlist')->countMyPlaylistsOfUser(Phpfox::getUserId());
            $aFilterMenu = [
                _p('discover') => '',
                _p('playlists') => 'ultimatevideo.playlist',
                ($iMyVideosNumbers > 0 ? _p('my_videos') . '<span class="count-item">' . $iMyVideosNumbers . '</span>' : _p('my_videos')) => 'my',
                ($this->getFavoriteVideoTotal() > 0) ? _p('my_favorite_videos') . '<span class="count-item">' . $this->getFavoriteVideoTotal() . '</span>' : _p('my_favorite_videos') => 'favorite',
                ($iMyPlaylistsNumbers > 0 ? _p('my_playlists') . '<span class="count-item">' . $iMyPlaylistsNumbers . '</span>' : _p('my_playlists')) => 'ultimatevideo.playlist.view_myplaylist',
                ($this->getWatchLaterVideoTotal() > 0) ? _p('watch_later') . '<span class="count-item">' . $this->getWatchLaterVideoTotal() . '</span>' : _p('watch_later') => 'later',
                ($this->getHistoryVideoTotal() > 0) ? _p('videos_history') . '<span class="count-item">' . $this->getHistoryVideoTotal() . '</span>' : _p('videos_history') => 'history',
                ($this->getHistoryPlaylistTotal() > 0) ? _p('playlists_history') . '<span class="count-item">' . $this->getHistoryPlaylistTotal() . '</span>' : _p('playlists_history') => 'ultimatevideo.playlist.view_historyplaylist',
            ];

            if (user('ynuv_can_approve_video', 0) && ($iTotal = $this->getPendingVideoTotal()) > 0) {
                $aFilterMenu[_p('pending_videos') . '<span class="count-item">' . $iTotal . '</span>'] = 'pending';
            }

            if (user('ynuv_can_approve_playlist', 0) && ($iTotal = $this->getPendingPlaylistTotal()) > 0) {
                $aFilterMenu[_p('pending_playlist') . '<span class="count-item">' . $iTotal . '</span>'] = 'ultimatevideo.playlist.view_pendingplaylist';
            }

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id')) {
                $aFilterMenu[_p('ultimatevideos_friends_videos')] = 'friend';
                $aFilterMenu[_p('ultimatevideos_friends_playlists')] = 'ultimatevideo.playlist.view_friendplaylist';
            }

            \Phpfox_Template::instance()->buildSectionMenu('ultimatevideo', $aFilterMenu, false);
        }
    }

    public function getPagePerms()
    {
        $aPerms = array();

        $aPerms['ultimatevideo.share_videos'] = _p('who_can_share_videos_ultimate_videos');
        $aPerms['ultimatevideo.view_browse_videos'] = _p('who_can_view_videos_ultimate_videos');

        return $aPerms;
    }

    public function getGroupPerms()
    {
        $aPerms = array();
        $aPerms['ultimatevideo.share_videos'] = _p('who_can_share_videos_ultimate_videos');
        $aPerms['ultimatevideo.view_browse_videos'] = _p('who_can_view_videos_ultimate_videos');
        return $aPerms;
    }

    public function getProfileLink()
    {
        return 'profile.ultimatevideo';
    }

    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ultimatevideo.view_browse_videos') || !setting('ynuv_app_enabled')) {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('ultimate_videos'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'ultimatevideo/',
            'icon' => 'module/ultimatevideo_video.png',
            'landing' => 'ultimatevideo'
        );

        return $aMenus;
    }

    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'ultimatevideo.view_browse_videos') || !setting('ynuv_app_enabled')) {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('ultimate_videos'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'ultimatevideo/',
            'icon' => 'module/ultimatevideo_video.png',
            'landing' => 'ultimatevideo'
        );

        return $aMenus;
    }

    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ultimatevideo.share_videos') || !user('ynuv_can_upload_video')) {
            return null;
        }

        return array(
            array(
                'phrase' => _p('share_a_video'),
                'url' => Phpfox_Url::instance()->makeUrl('ultimatevideo.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            )
        );
    }

    public function getGroupSubMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'ultimatevideo.share_videos') || !user('ynuv_can_upload_video')) {
            return null;
        }

        return array(
            array(
                'phrase' => _p('share_a_video'),
                'url' => Phpfox_Url::instance()->makeUrl('ultimatevideo.add', array('module' => 'groups', 'item' => $aPage['page_id']))
            )
        );
    }

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeedVideo($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($aCallback === null) {
            $this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.parent_user_id');
        }
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = v.user_id');
        }
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ultimatevideo_video\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select('v.video_id, v.module_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.total_view, v.image_path, v.user_id, v.image_server_id,v.code, v.type, v.video_path,v.description,v.duration,v.item_id,v.module_id,v.video_server_id, v.location_latlng, v.location_name')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->where('v.is_approved = 1 AND v.video_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id'])) {
            return false;
        }

        if (Phpfox::getParam('core.allow_html')) {
            $oFilter = Phpfox::getLib('parse.input');
            $aRow['description'] = $oFilter->prepare(htmlspecialchars_decode($aRow['description']));
        }
        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'ultimatevideo.view_browse_videos'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'ultimatevideo.view_browse_videos'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'groups' && Phpfox::isModule('groups') && !Phpfox::getService('groups')->hasPerm($aRow['item_id'], 'ultimatevideo.view_browse_videos')))
        ) {
            return false;
        }
        if (isset($aRow['type'])) {
            $sVideoPath = ($aRow['video_server_id'] == -1 ? Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') : Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/') . sprintf($aRow['video_path'], '');
            if ($aRow['video_server_id'] > 0 && $aRow['type'] == 3) {
                $sVideoPath = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.path_file') . 'file/ynultimatevideo/' . sprintf($aRow['video_path'], ''), $aRow['video_server_id']);
                if(empty($sVideoPath)) {
                    $sVideoPath = Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/' . sprintf($aRow['video_path'], '');
                }
            }
            $sSourceType = Phpfox::getService('ultimatevideo')->getSourTypeNameFromId($aRow['type']);
            $adapter = Phpfox::getService('ultimatevideo')->getClass($sSourceType);
            $aParams = array(
                'video_id' => $aRow['video_id'],
                'code' => $aRow['code'],
                'view' => false,
                'mobile' => Phpfox::getService('ultimatevideo')->isMobile(),
                'count_video' => 0,
                'location' => $aRow['code'],
                'location1' => $sVideoPath,
                'duration' => $aRow['duration']
            );

            $embedCode = $adapter->compileVideo($aParams);
        }

        if ($bIsChildItem) {
            $aItem = array_merge($aRow, $aItem);
        }

        $sponsorId = 0;
        if($aItem['sponsor_feed_id'] == $aItem['feed_id']) {
            $sponsorId = Phpfox::getService('ad.get')->getFeedSponsors($aItem['feed_id']);
        }

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_link' => !empty($sponsorId) ? Phpfox_Url::instance()->makeUrl('ad.sponsor', ['view' => $sponsorId]) : Phpfox::permalink('ultimatevideo', $aRow['video_id'], $aRow['title']),
            'feed_content' => $aRow['description'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/video.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ultimatevideo_video',
            'like_type_id' => 'ultimatevideo_video',
        );

        if (!empty($aRow['location_name'])) {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng'])) {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }

        if ($aRow['module_id'] == 'pages' || $aRow['module_id'] == 'groups') {
            $aRow['parent_user_id'] = '';
            $aRow['parent_user_name'] = '';
        }
        if (empty($aRow['parent_user_id'])) {
            $aReturn['feed_info'] = _p('feed.shared_a_video');
        }
        if ($aCallback === null) {
            if (!empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST)) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
            }

            if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId()) {
                $aReturn['feed_mini'] = true;
                $aReturn['feed_mini_content'] = _p('feed.full_name_posted_a_href_link_a_video_a_on_a_href_profile_parent_full_name_a_s_a_href_profile_link_wall_a', array('full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']), 'link' => Phpfox::permalink('ultimatevideo', $aRow['video_id'], $aRow['title']), 'profile' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']), 'parent_full_name' => $aRow['parent_full_name'], 'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name'])));
                $aReturn['feed_title'] = '';
                unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
            }
        }

        if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId()) {
        } else {
            $aReturn['load_block'] = 'ultimatevideo.feed_video';
            $aReturn['embed_code'] = isset($embedCode) ? $embedCode : "";
        }

        $result = array_merge($aReturn, $aItem);
        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['item_id'])
                ->execute('getRow');

            $result['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $result['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($result['feed_info']);
            }
        }

        Phpfox_Component::setPublicParam('custom_param_ultimatevideo_video_' . $aItem['feed_id'], $aRow['video_id']);

        return $result;
    }

    public function addTrack($iId, $iUserId = null)
    {
        $aId = explode('_', $iId);
        if (count($aId) != 2) {
            return false;
        }

        $this->database()->insert(Phpfox::getT('track'), [
            'type_id' => 'ultimatevideo_' . $aId[0],
            'item_id' => (int)$aId[1],
            'ip_address' => Phpfox::getIp(),
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME
        ]);

        return true;
    }

    public function addLikeVideo($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('video_id, title, user_id')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where('video_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ultimatevideo_video\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ynultimatevideo_videos', 'video_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('ultimatevideo', $aRow['video_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name') . _p(' liked your video.'))
                ->message(Phpfox::getUserBy('full_name') . _p(' liked your video ') . '"<a href="' . $sLink . '">' . $aRow['title'] . '</a>"' . _p(' To view this video follow the link below ') . '<a href="' . $sLink . '">' . $sLink . '</a>"')
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ultimatevideo_likevideo', $aRow['video_id'], $aRow['user_id']);
        }

        return null;
    }

    public function deleteLikeVideo($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ultimatevideo_video\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ynultimatevideo_videos', 'video_id = ' . (int)$iItemId);
    }

    public function getNotificationLikevideo($aNotification)
    {
        $aRow = $this->database()->select('e.video_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.video_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = $sUsers . _p(' liked ') . Phpfox::getService('user')->gender($aRow['gender']) . _p(' own video ') . '"' . $sTitle . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = $sUsers . _p(' liked your video ') . '"' . $sTitle . '"';
        } else {
            $sPhrase = $sUsers . _p(' liked ') . '<span class="drop_data_user">' . $aRow['full_name'] . '
		\'s</span> video "' . $sTitle . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ultimatevideo', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getNotificationCommentvideo($aNotification)
    {
        $aRow = $this->database()->select('e.video_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.video_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = $sUsers . _p(' commented ') . Phpfox::getService('user')->gender($aRow['gender']) . _p(' own video ') . '"' . $sTitle . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = $sUsers . _p(' commented your video ') . '"' . $sTitle . '"';
        } else {
            $sPhrase = $sUsers . _p(' commented ') . '<span class="drop_data_user">' . $aRow['full_name'] . '
        \'s</span> video "' . $sTitle . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ultimatevideo', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getAjaxCommentVarVideo()
    {
        return 'ynuv_can_add_comment_on_video';
    }

    public function getCommentItemVideo($iId)
    {
        $aRow = $this->database()->select('video_id AS comment_item_id, user_id AS comment_user_id')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where('video_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0)) {
            \Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    public function addCommentVideo($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aVideo = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.video_id, v.privacy')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('ynultimatevideo_videos', 'total_comment', 'video_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ultimatevideo', $aVideo['video_id'], $aVideo['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aVideo['user_id'],
                'item_id' => $aVideo['video_id'],
                'owner_subject' => Phpfox::getUserBy('full_name') . _p(' commented on your video ') . $aVideo['title'],
                'owner_message' => Phpfox::getUserBy('full_name') . _p(' commented on your video ') . '<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below: ') . '<a href="' . $sLink . '">' . $sLink . '</a>',
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ultimatevideo_commentvideo',
                'mass_id' => 'ultimatevideo',
                'mass_subject' => (Phpfox::getUserId() == $aVideo['user_id']) ? (Phpfox::getUserBy('full_name') . _p(' commented on ') . Phpfox::getService('user')->gender($aVideo['gender']) . _p(' video.')) : Phpfox::getUserBy('full_name') . _p(' commented on ') . $aVideo['full_name'] . _p('\'s video.'),
                'mass_message' => (Phpfox::getUserId() == $aVideo['user_id']) ? (Phpfox::getUserBy('full_name') . _p(' commented on ') . Phpfox::getService('user')->gender($aVideo['gender'], 1) . _p(' video ') . '"<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below:') . '<a href="' . $sLink . '">' . $sLink . '</a>') : (Phpfox::getUserBy('full_name') . _p(' commented on ') . $aVideo['full_name'] . _p('\'s video ') . '"<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below:') . '<a href="' . $sLink . '">' . $sLink . '</a>'),
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    public function deleteCommentVideo($iId)
    {
        $this->database()->update(Phpfox::getT('ynultimatevideo_videos'), array('total_comment' => array('= total_comment -', 1)), 'video_id = ' . (int)$iId);
    }

    public function getNotificationVideoconvert($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.video_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = "";
        if ($aRow['status'] == 1) {
            $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' has just been uploaded successfully.');
        } elseif ($aRow['status'] == 4) {
            $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' was failed to upload. File not founds.');
        } elseif ($aRow['status'] == 3) {
            $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' was failed to upload. Video format is not supported by FFMPEG.');
        } elseif ($aRow['status'] == 5) {
            $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' was failed to upload. Audio files are not supported.');
        } elseif ($aRow['status'] == 7) {
            $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' was failed to upload. You may be over the site upload limit.  Try uploading a smaller file, or delete some files to free up space.');
        }
        $sLink = Phpfox::getLib('url')->permalink('ultimatevideo', $aRow['video_id'], $aRow['title']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'ultimatevideo')
        );
    }

    public function getNotificationVideoapprove($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.video_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' has just been approved.');

        $sLink = Phpfox::getLib('url')->permalink('ultimatevideo', $aRow['video_id'], $aRow['title']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'ultimatevideo')
        );
    }

    public function getNotificationVideofeature($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.video_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_video_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' has just been featured.');

        $sLink = Phpfox::getLib('url')->permalink('ultimatevideo', $aRow['video_id'], $aRow['title']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'ultimatevideo')
        );
    }

    public function getNotificationPlaylistapprove($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.playlist_id, v.privacy')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.playlist_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_playlist_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' has just been approved.');

        $sLink = Phpfox::getLib('url')->permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'ultimatevideo')
        );
    }

    public function getNotificationPlaylistfeature($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.playlist_id, v.privacy')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.playlist_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_playlist_space') . '"' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"' . _p(' has just been featured.');

        $sLink = Phpfox::getLib('url')->permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'ultimatevideo')
        );
    }

    public function updateCounterList()
    {
        $aList = array();

        $aList[] = array(
            'name' => _p('users_ultimate_video_count'),
            'id' => 'ultimatevideo-total'
        );

        $aList[] = array(
            'name' => _p('update_users_activity_ultimate_video_points'),
            'id' => 'ultimatevideo-activity'
        );

        return $aList;
    }

    /**
     * @param $iId
     * @param $iPage
     * @param $iPageLimit
     * @return mixed
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'ultimatevideo-total') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(v.video_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ynultimatevideo_videos'), 'v', 'v.user_id = u.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_field'), array('total_ultimatevideo' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        } elseif ($iId == 'ultimatevideo-activity') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('m.user_id, m.activity_ultimatevideo, m.activity_points, m.activity_total, COUNT(v.video_id) AS total_items')
                ->from(Phpfox::getT('user_activity'), 'm')
                ->leftJoin(Phpfox::getT('ynultimatevideo_videos'), 'v', 'v.user_id = m.user_id')
                ->group('m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_activity'), array(
                    'activity_points' => (($aRow['activity_total'] - ($aRow['activity_points'] * user('ynuv_points_when_add_video'))) + ($aRow['total_items'] * user('ynuv_points_when_add_video'))),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_ultimatevideo']) + $aRow['total_items']),
                    'activity_ultimatevideo' => $aRow['total_items']
                ), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }

        return null;
    }

    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('ultimate_videos') . ' (' . _p('Videos') . ')' => $aUser['activity_ultimatevideo_video'],
            _p('ultimate_videos') . ' (' . _p('Playlists') . ')' => $aUser['activity_ultimatevideo_playlist']
        );
    }

    public function getProfileMenu($aUser)
    {
        if (!setting('ynuv_app_enabled'))
            return false;

        $aUser['total_ultimatevideo'] = Phpfox::getService('ultimatevideo')->countVideoOfUserId($aUser['user_id']);

        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_ultimatevideo'])) {
                return false;
            }

            if (isset($aUser['total_ultimatevideo']) && (int)$aUser['total_ultimatevideo'] === 0) {
                return false;
            }
        }

        $aSubMenu = array();

        $aMenus[] = array(
            'phrase' => _p('ultimate_videos'),
            'url' => 'profile.ultimatevideo',
            'total' => (int)(isset($aUser['total_ultimatevideo']) ? $aUser['total_ultimatevideo'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/video.png'
        );

        return $aMenus;
    }

    public function getAjaxProfileController()
    {
        return 'ultimatevideo.index';
    }

    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = 'v.is_approved = 1 AND v.privacy = 1 AND v.status = 1';
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (v.title LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
        }

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = v.video_id AND tag.category_id = \'ynultimatevideo\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getService('ynultimatevideo_videos'), 'v')
            ->where($sCondition)
            ->execute('getSlaveField');

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = v.video_id AND tag.category_id = \'ynultimatevideo\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('v.video_id');
        }

        $aRows = $this->database()->select('v.title, v.time_stamp, ' . Phpfox::getUserField())
            ->from(Phpfox::getService('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('search_videos');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div><input type="button" value="' . _p('view_more_videos') . '" class="search_button" onclick="window.location.href = \'' . Phpfox_Url::instance()->makeUrl('ultimatevideo', array('tag', $sQuery)) . '\';" /></div>';
            } else {
                $aResults['form'] = '<form method="post" action="' . Phpfox_Url::instance()->makeUrl('ultimatevideo') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('view_more_videos') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array('ultimatevideo', $aRow['title_url'])),
                    'image' => Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aRow['server_id'],
                            'title' => $aRow['full_name'],
                            'path' => 'core.url_user',
                            'file' => $aRow['user_image'],
                            'suffix' => '_120',
                            'max_width' => 75,
                            'max_height' => 75
                        )
                    ),
                    'extra_info' => _p('ultimatevideo_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox_Url::instance()->makeUrl('ultimatevideo'),
                            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                            'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);

        return null;
    }

    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox_Url::instance()->permalink('ultimatevideo', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('ultimate_videos');
        if(!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'ultimatevideo.url_pic',
                    'suffix' => '_500',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        else {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => 'noimg_video.jpg',
                    'path' => 'ultimatevideo.url_pic_default',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }

        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('ultimate_videos')
        );
    }

    public function getTagSearch($aConds = array(), $sSort = '')
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
        $aRows = $this->database()->select("v.video_id AS id")
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = v.video_id")
            ->where($aConds)
            ->order($sSort)
            ->group('v.video_id')
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aRows as $aRow) {
            $aSearchIds[] = $aRow['id'];
        }
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
        return $aSearchIds;
    }

    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.video_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'ultimatevideo\' AS item_type_id, item.image_path AS item_photo, item.image_server_id AS item_photo_server')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'item')
            ->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 AND item.privacy = 0')
            ->union();
    }

    public function addLikePlaylist($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('playlist_id, title, user_id')
            ->from(Phpfox::getT('ynultimatevideo_playlists'))
            ->where('playlist_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ultimatevideo_playlist\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ynultimatevideo_playlists', 'playlist_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('ultimatevideo.Playlist', $aRow['playlist_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name') . _p(' liked your playlist.'))
                ->message(Phpfox::getUserBy('full_name') . _p(' liked your playlist ') . '"<a href="' . $sLink . '">' . $aRow['title'] . '</a>"' . _p(' To view this playlist follow the link below ') . '<a href="' . $sLink . '">' . $sLink . '</a>"')
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ultimatevideo_likeplaylist', $aRow['playlist_id'], $aRow['user_id']);
        }

        return null;
    }

    public function deleteLikePLaylist($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ultimatevideo_playlist\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ynultimatevideo_playlists', 'playlist_id = ' . (int)$iItemId);
    }

    public function getNotificationLikeplaylist($aNotification)
    {
        $aRow = $this->database()->select('e.playlist_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.playlist_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = $sUsers . _p(' liked ') . Phpfox::getService('user')->gender($aRow['gender']) . _p(' own playlist ') . '"' . $sTitle . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = $sUsers . _p(' liked your playlist ') . '"' . $sTitle . '"';
        } else {
            $sPhrase = $sUsers . _p(' liked ') . '<span class="drop_data_user">' . $aRow['full_name'] . '
        \'s</span> playlist "' . $sTitle . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getNotificationCommentplaylist($aNotification)
    {
        $aRow = $this->database()->select('e.playlist_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.playlist_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['playlist_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = $sUsers . _p(' commented ') . Phpfox::getService('user')->gender($aRow['gender']) . _p(' own playlist ') . '"' . $sTitle . '"';
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = $sUsers . _p(' commented your playlist ') . '"' . $sTitle . '"';
        } else {
            $sPhrase = $sUsers . _p(' commented ') . '<span class="drop_data_user">' . $aRow['full_name'] . '
        \'s</span> playlist "' . $sTitle . '"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getAjaxCommentVarPlaylist()
    {
        return 'ynuv_can_add_comment_on_playlist';
    }

    public function getCommentItemPlaylist($iId)
    {
        $aRow = $this->database()->select('playlist_id AS comment_item_id, user_id AS comment_user_id')
            ->from(Phpfox::getT('ynultimatevideo_playlists'))
            ->where('playlist_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0)) {
            \Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    public function addCommentPlaylist($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aVideo = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.title, v.playlist_id, v.privacy')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.playlist_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('ynultimatevideo_playlists', 'total_comment', 'playlist_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ultimatevideo', $aVideo['playlist_id'], $aVideo['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aVideo['user_id'],
                'item_id' => $aVideo['playlist_id'],
                'owner_subject' => Phpfox::getUserBy('full_name') . _p(' commented on your playlist ') . $aVideo['title'],
                'owner_message' => Phpfox::getUserBy('full_name') . _p(' commented on your playlist ') . '<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below: ') . '<a href="' . $sLink . '">' . $sLink . '</a>',
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ultimatevideo_commentplaylist',
                'mass_id' => 'ultimatevideo',
                'mass_subject' => (Phpfox::getUserId() == $aVideo['user_id']) ? (Phpfox::getUserBy('full_name') . _p(' commented on ') . Phpfox::getService('user')->gender($aVideo['gender']) . _p(' playlist.')) : Phpfox::getUserBy('full_name') . _p(' commented on ') . $aVideo['full_name'] . _p('\'s playlist.'),
                'mass_message' => (Phpfox::getUserId() == $aVideo['user_id']) ? (Phpfox::getUserBy('full_name') . _p(' commented on ') . Phpfox::getService('user')->gender($aVideo['gender'], 1) . _p(' playlist ') . '"<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below:') . '<a href="' . $sLink . '">' . $sLink . '</a>') : (Phpfox::getUserBy('full_name') . _p(' commented on ') . $aVideo['full_name'] . _p('\'s playlist ') . '"<a href="' . $sLink . '">' . $aVideo['title'] . '</a>"' . _p(' To see the comment thread, follow the link below:') . '<a href="' . $sLink . '">' . $sLink . '</a>'),
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    public function deleteCommentPlaylist($iId)
    {
        $this->database()->update(Phpfox::getT('ynultimatevideo_playlists'), array('total_comment' => array('= total_comment -', 1)), 'playlist_id = ' . (int)$iId);
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_ultimatevideo',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('ynultimatevideo_videos'))->where('user_id = ' . (int)$iUserId . ' AND is_approved = 1 AND item_id = 0')->execute('getSlaveField')
        );
    }

    public function getActivityFeedPlaylist($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = v.user_id');
        }
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ultimatevideo_playlist\' AND l.item_id = v.playlist_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select('v.playlist_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.image_path, v.user_id, v.image_server_id,v.description,v.total_video')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'v')
            ->where('v.playlist_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['playlist_id'])) {
            return false;
        }
        if (Phpfox::getParam('core.allow_html')) {
            $aRow['description'] = Phpfox::getLib('parse.input')->prepare(htmlspecialchars_decode($aRow['description']));
        }

        if ($bIsChildItem) {
            $aItem = array_merge($aRow, $aItem);
        }
        $aReturn = array(
            'feed_info' => _p('created_a_new_playlist'),
            'feed_title' => $aRow['title'],
            'feed_link' => Phpfox::permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']),
            'feed_content' => $aRow['description'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/video.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ultimatevideo_playlist',
            'like_type_id' => 'ultimatevideo_playlist',
            'total_video' => $aRow['total_video'],
            'image_path' => $aRow['image_path'],
            'image_server_id' => $aRow['image_server_id'],
        );
        $aReturn['load_block'] = 'ultimatevideo.feed_playlist';

        Phpfox_Component::setPublicParam('custom_param_ultimatevideo_playlist_' . $aItem['feed_id'], $aRow['playlist_id']);

        return array_merge($aReturn, $aItem);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getRedirectCommentVideo($iId)
    {
        return $this->getFeedRedirectVideo($iId);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getRedirectCommentPlaylist($iId)
    {
        return $this->getFeedRedirectPlaylist($iId);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirectVideo($iId)
    {
        return $this->getFeedRedirectVideo($iId);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirectPlaylist($iId)
    {
        return $this->getFeedRedirectPlaylist($iId);
    }

    /**
     * @param int $iId
     * @param int $iChild
     *
     * @return bool|string
     */
    public function getFeedRedirectVideo($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_getfeedredirectvideo__start')) ? eval($sPlugin) : false);

        $aVideo = $this->database()->select('v.video_id, v.title')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aVideo['video_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_getfeedredirectvideo__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('ultimatevideo', $aVideo['video_id'], $aVideo['title']);
    }

    /**
     * @param int $iId
     * @param int $iChild
     *
     * @return bool|string
     */
    public function getFeedRedirectPlaylist($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_getfeedredirectplaylist__start')) ? eval($sPlugin) : false);

        $aPlaylist = $this->database()->select('v.playlist_id, v.title')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.playlist_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPlaylist['playlist_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_callback_getfeedredirectplaylist__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('ultimatevideo.playlist', $aPlaylist['playlist_id'], $aPlaylist['title']);
    }

    public function getUploadParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('ultimatevideo.ynuv_max_file_size_photos_upload');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);

        return [
            'label' => _p('Select an image'),
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynultimatevideo' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynultimatevideo' . PHPFOX_DS,
            'thumbnail_sizes' => array(120, 250, 500, 1024),
            'remove_field_name' => 'remove_logo',
            'no_square' => true
        ];
    }

    public function getUploadParamsVideo()
    {
        return Phpfox::getService('ultimatevideo')->getUploadVideoParams();
    }

    public function onDeleteUser($iUser)
    {
        $aVideos = $this->database()
            ->select('video_id')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where('(user_id = ' . (int)$iUser . ')')
            ->execute('getSlaveRows');
        if (count($aVideos)) {
            foreach ($aVideos as $aVideo) {
                Phpfox::getService('ultimatevideo.process')->deleteVideo($aVideo['video_id'], true);
            }
        }

        $aPlaylists = $this->database()
            ->select('playlist_id')
            ->from(Phpfox::getT('ynultimatevideo_playlists'))
            ->where('(user_id = ' . (int)$iUser . ')')
            ->execute('getSlaveRows');
        if (count($aPlaylists)) {
            foreach ($aPlaylists as $aPlaylist) {
                Phpfox::getService('ultimatevideo.playlist.process')->delete($aPlaylist['playlist_id'], true);
            }
        }
    }
}