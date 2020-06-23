<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package 		FoxFavorite_Module
 */
class Foxfavorite_Service_Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('foxfavorite');
    }

    public function getProfileLink()
    {
        return 'profile.foxfavorite';
    }

    public function canShareItemOnFeed()
    {

    }

    /**
     * Action to take when user cancelled their account
     *	Deletes: friends, friends lists and friends requests
     * @param int $iUser
     */
    public function onDeleteUser($iUser)
    {
        $aFavorites = $this->database()->select('favorite_id')->from($this->_sTable)->where('user_id = ' . (int)$iUser)->execute('getSlaveRows');

        foreach ($aFavorites as $aFavorite)
        {
            Phpfox::getService('foxfavorite.process')->delete($aFavorite['favorite_id']);
        }
    }

    public function tabHasItems($iUser)
    {
        $iCount = $this->database()->select('COUNT(user_id)')->from($this->_sTable)->where('user_id = ' . (int)$iUser)->execute('getSlaveField');
        return $iCount > 0;
    }

    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getParam('profile.show_empty_tabs'))
        {
            if (!isset($aUser['total_foxfavorite']))
            {
                return false;
            }

            if (isset($aUser['total_foxfavorite']) && (int)$aUser['total_foxfavorite'] === 0)
            {
                return false;
            }
        }

        $aSubMenu = array();
        $iTotal = 0;

        $aSettings = phpfox::getService('foxfavorite')->getSettings();
        $sUserName = $this->request()->get('req1');
        foreach ($aSettings as $iKey => $aSetting)
        {
            if ($aSetting['is_active'] != 1)
            {
                continue;
            }
            $iCnt = Phpfox::getService('foxfavorite')->getCount($aSetting['module_id'], $aUser['user_id'], '');
            if ($this->request()->get('req2') == 'foxfavorite') {
                $aSettings[$iKey]['url'] = phpfox::getLib('url')->makeUrl($sUserName . '.foxfavorite.view_' . $aSetting['title']);
                switch ($aSetting['module_id']) {
                    case 'advancedmarketplace':
                        break;
                    case 'advancedphoto':
                        $aSetting['title'] = 'photos';
                        break;
                    case 'contest':
                        break;
                    case 'directory':
                        $aSetting['title'] = 'business_directory';
                        break;
                    case 'auction':
                        $aSetting['title'] = 'auction';
                        break;
                    case 'fevent':
                        $aSetting['title'] = 'events';
                        break;
                    case 'foxfeedspro':
                        $aSetting['title'] = 'news';
                        break;
                    case 'jobposting':
                        $aSetting['title'] = 'jobs';
                        break;
                    case 'karaoke':
                        break;
                    case 'music':
                        break;
                    case 'musicsharing':
                        $aSetting['title'] = 'music_sharing';
                        break;
                    case 'marketplace':
                        break;
                    case 'pages':
                        break;
                    case 'profile':
                        break;
                    case 'quiz':
                        $aSetting['title'] = 'quizzes';
                        break;
                    case 'videochannel':
                        break;
                    default: //..., coupon, resume
                        $aSetting['title'] .= 's';
                        break;

                }
                $aSubMenu[] = [
                    'phrase' => _p(($aSetting['module_id'] == 'video' ? 'v' : $aSetting['module_id']) . "." . $aSetting['title']),
                    'url' => $aSettings[$iKey]['url'],
                    'module_name' => $aSetting['module_id'],
                    'total' => (int)$iCnt
                ];
            }
            $iTotal += $iCnt;
        }

        $aMenus[] = array(
            'phrase' => _p('foxfavorite.favorites'),
            'url' => 'profile.foxfavorite',
            'total' => ($iTotal != 0) ? $iTotal : (int)(isset($aUser['total_foxfavorite']) ? $aUser['total_foxfavorite'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'module/favorite.png',
            'icon_class' => 'ico ico-heart-o'
        );

        if ($iTotal != $aUser['total_foxfavorite'] && $iTotal != 0)
        {
            phpfox::getLib('database')->update(phpfox::getT('user_field'), array('total_foxfavorite' => $iTotal), 'user_id = ' . $aUser['user_id']);
        }
        return $aMenus;
    }

    public function getNotificationUpdateStatus($aNotification)
    {
        $aRow = $this->database()->select('us.status_id, u.user_id, u.gender, u.user_name, u.full_name, us.content')->from(Phpfox::getT('feed'), 'f')->join(phpfox::getT('user_status'), 'us', 'us.status_id = f.item_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = us.user_id')->where('f.feed_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');
        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sContent = Phpfox::getLib('parse.output')->shorten($aRow['content'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_updated_status_content', array('users' => $sUsers, 'content' => $sContent));


        return array(
            'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('status-id' => $aRow['status_id'])),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/application_add.png', 'return_url' => true)));
    }

    public function getNotificationAddlink($aNotification)
    {
        $aRow = $this->database()->select('l.link_id, l.title, l.link, ' . phpfox::getUserField())->from(Phpfox::getT('link'), 'l')->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')->where('l.link_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_shared_a_link_title', array('users' => $sUsers, 'link' => $aRow['link'], 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('link-id' => $aRow['link_id'])),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/link.png', 'return_url' => true)));
    }

    public function getNotificationAddphoto($aNotification)
    {
        if (!phpfox::isModule('photo'))
        {
            return false;
        }
        $aRow = $this->database()->select('p.photo_id, p.title,' . phpfox::getUserField())->from(Phpfox::getT('photo'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.photo_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_photo_title', array('users' => $sUsers, 'title' => $aRow['title']));

        return array(
            'link' => Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/photo.png', 'return_url' => true)));
    }

    public function getNotificationAddblog($aNotification)
    {
        if (!phpfox::isModule('blog'))
        {
            return false;
        }
        $aRow = $this->database()->select('b.blog_id, b.title,' . phpfox::getUserField())->from(Phpfox::getT('blog'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.blog_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_blog', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddpoll($aNotification)
    {
        if (!phpfox::isModule('poll'))
        {
            return false;
        }
        $aRow = $this->database()->select('b.poll_id, b.question as title,' . phpfox::getUserField())->from(Phpfox::getT('poll'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.poll_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_poll', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('poll', $aRow['poll_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddmusic($aNotification)
    {
        if (!phpfox::isModule('music'))
        {
            return false;
        }
        $aRow = $this->database()->select('b.song_id, b.title,' . phpfox::getUserField())->from(Phpfox::getT('music_song'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.song_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_song', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddlisting($aNotification)
    {
        if (!phpfox::isModule('marketplace'))
        {
            return false;
        }
        $aRow = $this->database()->select('m.listing_id, m.title,' . phpfox::getUserField())->from(Phpfox::getT('marketplace'), 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where('m.listing_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_listing', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('marketplace', $aRow['listing_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/marketplace.png', 'return_url' => true)));
    }

    public function getNotificationAddvideo($aNotification)
    {
        if (!phpfox::isModule('video'))
        {
            return false;
        }
        $aRow = $this->database()->select('v.video_id, v.title,' . phpfox::getUserField())->from(Phpfox::getT('video'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->where('v.video_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_video', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/video.png', 'return_url' => true)));
    }

    public function getNotificationAddquiz($aNotification)
    {
        if (!phpfox::isModule('quiz'))
        {
            return false;
        }
        $aRow = $this->database()->select('q.quiz_id, q.title,' . phpfox::getUserField())->from(Phpfox::getT('quiz'), 'q')->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')->where('q.quiz_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_quiz', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('quiz', $aRow['quiz_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/quiz.png', 'return_url' => true)));
    }

    public function getNotificationAddevent($aNotification)
    {
        if (!phpfox::isModule('event'))
        {
            return false;
        }
        $aRow = $this->database()->select('e.event_id, e.title,' . phpfox::getUserField())->from(Phpfox::getT('event'), 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where('e.event_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_an_event', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('event', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/event.png', 'return_url' => true)));
    }

    public function getNotificationAddflisting($aNotification)
    {
        if (!phpfox::isModule('advancedmarketplace'))
        {
            return false;
        }
        $aRow = $this->database()->select('m.listing_id, m.title,' . phpfox::getUserField())->from(Phpfox::getT('advancedmarketplace'), 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where('m.listing_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_listing', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aRow['listing_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/marketplace.png', 'return_url' => true)));
    }

    public function getNotificationAddfevent($aNotification)
    {
        if (!phpfox::isModule('fevent'))
        {
            return false;
        }
        $aRow = $this->database()->select('e.event_id, e.title,' . phpfox::getUserField())->from(Phpfox::getT('fevent'), 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where('e.event_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_an_event', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/event.png', 'return_url' => true)));
    }

    public function getNotificationAdddocument($aNotification)
    {
        if (!phpfox::isModule('document'))
        {
            return false;
        }
        $aRow = $this->database()->select('d.document_id, d.title,' . phpfox::getUserField())->from(Phpfox::getT('document'), 'd')->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')->where('d.document_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_document', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/document.jpg', 'return_url' => true)));
    }

    public function getNotificationAddkaraokesong($aNotification)
    {
        if (!phpfox::isModule('karaoke'))
        {
            return false;
        }
        $aRow = $this->database()->select('b.song_id, b.title,' . phpfox::getUserField())->from(Phpfox::getT('karaoke_song'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.song_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_karaoke_song', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('karaoke.songdetail', $aRow['song_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/music.png', 'return_url' => true)));
    }

    public function getNotificationAddkaraokerecording($aNotification)
    {
        if (!phpfox::isModule('karaoke'))
        {
            return false;
        }
        $aRow = $this->database()->select('b.recording_id, b.title,' . phpfox::getUserField())->from(Phpfox::getT('karaoke_recording'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.recording_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_added_a_karaoke_recording', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('karaoke.recordingdetail', $aRow['recording_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/music.png', 'return_url' => true)));
    }

    public function getNotificationAddchannelvideo($aNotification)
    {
        if (!phpfox::isModule('videochannel'))
        {
            return false;
        }
        $aRow = $this->database()->select('v.video_id, v.title,' . phpfox::getUserField())->from(Phpfox::getT('channel_video'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->where('v.video_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_video', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/video.png', 'return_url' => true)));
    }

    public function getNotificationAddadvancedphoto($aNotification)
    {
        if (!phpfox::isModule('advancedphoto'))
        {
            return false;
        }
        $aRow = $this->database()->select('p.photo_id, p.title,' . phpfox::getUserField())->from(Phpfox::getT('photo'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.photo_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';

        $sPhrase = _p('foxfavorite.user_has_already_posted_a_photo_title', array('users' => $sUsers, 'title' => $sTitle));


        return array(
            'link' => Phpfox::getLib('url')->permalink('advancedphoto', $aRow['photo_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/photo.png', 'return_url' => true)));
    }

    public function getNotificationAddbusiness($aNotification)
    {
        if (!phpfox::isModule('directory'))
        {
            return false;
        }

        $aRow = $this->database()->select('c.business_id, c.name as title,' . phpfox::getUserField())
            ->from(Phpfox::getT('directory_business'), 'c')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.business_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_has_already_added_a_business_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddauction($aNotification)
    {
        if (!phpfox::isModule('auction'))
        {
            return false;
        }

        $sWhere = '';
        $sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
        $aRow = $this->database()->select('e.product_id,e.name as title,'.Phpfox::getUserField())
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }


        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_has_already_added_a_product_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddcoupon($aNotification)
    {
        if (!phpfox::isModule('coupon'))
        {
            return false;
        }

        $aRow = $this->database()->select('c.coupon_id, c.title,' . phpfox::getUserField())->from(Phpfox::getT('coupon'), 'c')->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')->where('c.coupon_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');
        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.user_has_already_added_a_coupon_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddcontest($aNotification)
    {
        if (!Phpfox::isModule('contest'))
        {
            return false;
        }

        $aRow = $this->database()->select('c.contest_id, c.contest_name, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('contest'), 'c')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
        ->where('c.contest_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['contest_name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sMessage = _p('foxfavorite.users_has_already_posted_a_contest_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('contest', $aRow['contest_id'], $aRow['contest_name']),
            'message' => $sMessage,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddresume($aNotification)
    {
        if (!Phpfox::isModule('resume'))
        {
            return false;
        }

        $aRow = $this->database()->select('rbi.resume_id, rbi.headline, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('resume_basicinfo'), 'rbi')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')
        ->where('rbi.resume_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sHeadline = Phpfox::getLib('parse.output')->shorten($aRow['headline'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sMessage = _p('foxfavorite.users_has_already_added_a_resume_headline', array('users' => $sUsers, 'headline' => $sHeadline));

        return array(
            'link' => Phpfox::getLib('url')->permalink('resume.view', $aRow['resume_id'], $aRow['headline']),
            'message' => $sMessage,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddjob($aNotification)
    {
        if (!Phpfox::isModule('jobposting'))
        {
            return false;
        }

        $aRow = $this->database()->select('j.job_id, j.title, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('jobposting_job'), 'j')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = j.user_id')
        ->where('j.job_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sMessage = _p('foxfavorite.users_has_already_posted_a_job_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
            'message' => $sMessage,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddpetition($aNotification)
    {
        if (!Phpfox::isModule('petition'))
        {
            return false;
        }

        $aRow = $this->database()->select('p.petition_id, p.title, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('petition'), 'p')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
        ->where('p.petition_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sMessage = _p('foxfavorite.users_has_already_posted_a_petition_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('petition', $aRow['petition_id'], $aRow['title']),
            'message' => $sMessage,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationAddmusicsharing($aNotification)
    {
        if (!Phpfox::isModule('musicsharing'))
        {
            return false;
        }

        $aRow = $this->database()->select('s.song_id, s.title, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('m2bmusic_album_song'), 's')
        ->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')
        ->where('s.song_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sMessage = _p('foxfavorite.users_has_already_posted_a_song_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('musicsharing.listen', 'music_'.$aRow['song_id']),
            'message' => $sMessage,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'));
    }

    public function getNotificationFavorblog($aNotification)
    {
        if (!phpfox::isModule('blog'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('b.blog_id, b.title, b.user_id, u.gender, u.full_name')->from(Phpfox::getT('blog'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')->where('b.blog_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['blog_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorprofile($aNotification)
    {
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('u.user_id, u.gender, u.full_name, u.user_name')->from(Phpfox::getT('user'), 'u')->where('u.user_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['user_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        //$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_profile', array('users' => $sUsers, ));

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['user_name'], ''),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavormarketplace($aNotification)
    {
        if (!phpfox::isModule('marketplace'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('l.listing_id, l.title, l.user_id, u.gender, u.full_name')->from(Phpfox::getT('marketplace'), 'l')->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')->where('l.listing_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['listing_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_listing_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('marketplace', $aRow['listing_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorvideo($aNotification)
    {
        if (!phpfox::isModule('video'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')->from(Phpfox::getT('video'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->where('v.video_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_video_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavormusic($aNotification)
    {
        if (!phpfox::isModule('music'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('m.song_id, m.title, m.user_id, u.gender, u.full_name')->from(Phpfox::getT('music_song'), 'm')->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')->where('m.song_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['song_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_song_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorpages($aNotification)
    {
        if (!phpfox::isModule('pages'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('p.page_id, p.title, p.user_id, u.gender, u.full_name')->from(Phpfox::getT('pages'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.page_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['page_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_pages_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('pages', $aRow['page_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorphoto($aNotification)
    {
        if (!phpfox::isModule('photo'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('p.photo_id, p.title, p.user_id, u.gender, u.full_name')->from(Phpfox::getT('photo'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.photo_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['photo_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_photo_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorevent($aNotification)
    {
        if (!phpfox::isModule('event'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')->from(Phpfox::getT('event'), 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where('e.event_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['event_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_event_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('event', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorpoll($aNotification)
    {
        if (!phpfox::isModule('poll'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('p.poll_id, p.question as title, p.user_id, u.gender, u.full_name')->from(Phpfox::getT('poll'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.poll_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['poll_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_poll_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('poll', $aRow['poll_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorquiz($aNotification)
    {
        if (!phpfox::isModule('quiz'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('p.quiz_id, p.title, p.user_id, u.gender, u.full_name')->from(Phpfox::getT('quiz'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.quiz_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['quiz_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_quiz_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('quiz', $aRow['quiz_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorkaraoke($aNotification)
    {
        if (!phpfox::isModule('karaoke'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);

        $aFavor = $this->database()->select('kf.*')->from(Phpfox::getT('karaoke_favorite'), 'kf')->where('kf.favorite_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if(!count($aFavor))
        {
            return false;
        }

        if($aFavor['item_type']=='song')
        {
            $aRow = $this->database()->select('ks.song_id, ks.title, ks.user_id, u.gender, u.full_name, kf.*')->from(Phpfox::getT('karaoke_song'), 'ks')->join(Phpfox::getT('karaoke_favorite'), 'kf', 'kf.item_id = ks.song_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = ks.user_id')->where('kf.favorite_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

            if (!isset($aRow['song_id']))
            {
                return false;
            }

            $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
            $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

            $sPhrase = '';
            $sPhrase = _p('foxfavorite.user_favorited_your_karaoke_song_title', array('users' => $sUsers, 'title' => $sTitle));

            return array(
                'link' => Phpfox::getLib('url')->permalink('karaoke.songdetail', $aRow['song_id'], $aRow['title']),
                'message' => $sPhrase,
                'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
        }
        elseif($aFavor['item_type']=='recording')
        {
            $aRow = $this->database()->select('kr.recording_id, kr.title, kr.user_id, u.gender, u.full_name, kf.*')->from(Phpfox::getT('karaoke_recording'), 'kr')->join(Phpfox::getT('karaoke_favorite'), 'kf', 'kf.item_id = kr.recording_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = kr.user_id')->where('kf.favorite_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

            if (!isset($aRow['recording_id']))
            {
                return false;
            }

            $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
            $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

            $sPhrase = '';
            $sPhrase = _p('foxfavorite.user_favorited_your_karaoke_recording_title', array('users' => $sUsers, 'title' => $sTitle));

            return array(
                'link' => Phpfox::getLib('url')->permalink('karaoke.recordingdetail', $aRow['recording_id'], $aRow['title']),
                'message' => $sPhrase,
                'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
        }
        else
        {
            return false;
        }
    }

    public function getNotificationFavorchannelvideo($aNotification)
    {
        if (!phpfox::isModule('videochannel'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')->from(Phpfox::getT('channel_video'), 'v')->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')->where('v.video_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_video_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavordocument($aNotification)
    {
        if (!phpfox::isModule('document'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('d.document_id, d.title, d.user_id, u.gender, u.full_name')->from(Phpfox::getT('document'), 'd')->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')->where('d.document_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['document_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_document_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorfevent($aNotification)
    {
        if (!phpfox::isModule('fevent'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')->from(Phpfox::getT('fevent'), 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where('e.event_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['event_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_event_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('fevent', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavoradvancedmarketplace($aNotification)
    {
        if (!phpfox::isModule('advancedmarketplace'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('l.listing_id, l.title, l.user_id, u.gender, u.full_name')->from(Phpfox::getT('advancedmarketplace'), 'l')->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')->where('l.listing_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['listing_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_listing_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('advancedmarketplace/detail', $aRow['listing_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavoradvancedphoto($aNotification)
    {
        if (!phpfox::isModule('photo'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('p.photo_id, p.title, p.user_id, u.gender, u.full_name')->from(Phpfox::getT('photo'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.photo_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['photo_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        $sPhrase = _p('foxfavorite.user_favorited_your_photo_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('advancedphoto', $aRow['photo_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavordirectory($aNotification)
    {
        if (!phpfox::isModule('directory'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()
            ->select('c.business_id, c.name as title, c.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('directory_business'), 'c')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.business_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['business_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_favorited_your_business_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorauction($aNotification)
    {
        if (!phpfox::isModule('auction'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);

        $sWhere = '';
        $aRow = $this->database()->select('e.product_id,e.name as title,'.Phpfox::getUserField())
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_favorited_your_product_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorcoupon($aNotification)
    {
        if (!phpfox::isModule('coupon'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);
        $aRow = $this->database()->select('c.coupon_id, c.title, c.user_id, u.gender, u.full_name')->from(Phpfox::getT('coupon'), 'c')->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')->where('c.coupon_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');
        if (!isset($aRow['coupon_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.user_favorited_your_coupon_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorfoxfeedspro($aNotification)
    {
        if (!Phpfox::isModule('foxfeedspro'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);

        $aRow = $this->database()->select('ni.item_id, ni.item_title, ni.user_id, u.gender, u.full_name')
        ->from(Phpfox::getT('ynnews_items'), 'ni')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
        ->where('ni.item_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (!isset($aRow['item_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['item_title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_favorited_your_news_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('news.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavorpetition($aNotification)
    {
        if (!Phpfox::isModule('petition'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);

        $aRow = $this->database()->select('p.petition_id, p.title, p.user_id, u.gender, u.full_name')
        ->from(Phpfox::getT('petition'), 'p')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
        ->where('p.petition_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (!isset($aRow['petition_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_favorited_your_petition_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('petition', $aRow['petition_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getNotificationFavormusicsharing($aNotification)
    {
        if (!Phpfox::isModule('musicsharing'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aNotification);

        $aRow = $this->database()->select('s.song_id, s.title, a.user_id, u.gender, u.full_name')
        ->from(Phpfox::getT('m2bmusic_album_song'), 's')
        ->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')
        ->where('s.song_id = ' . (int)$aNotification['item_id'])
        ->execute('getSlaveRow');

        if (!isset($aRow['song_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('foxfavorite.users_favorited_your_song_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::permalink('musicsharing.listen', 'music_'.$aRow['song_id']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)));
    }

    public function getActivityFeedBlog($aRow)
    {
        if (!phpfox::isModule('blog'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aRow);

        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'blog\' AND l.item_id = b.blog_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aBlog = $this->database()->select('b.user_id, b.blog_id, b.title, b.time_stamp, b.total_comment, b.image_path, b.server_id, b.privacy, b.total_like, bt.text_parsed AS text, b.module_id, b.item_id, b.total_view, f.time_stamp AS favorite_time_stamp')
            ->from(Phpfox::getT('blog'), 'b')
            ->join(Phpfox::getT('blog_text'), 'bt', 'bt.blog_id = b.blog_id')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = b.blog_id AND f.type_id = "blog" AND f.user_id = ' . $aRow['favorite_user_id'])
            ->where('b.blog_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return false;
        }

        /**
         * Check active parent module
         */
        if (!empty($aBlog['module_id']) && !Phpfox::isModule($aBlog['module_id'])) {
            return false;
        }
        $sContent = Phpfox_Template::instance()->assign(['aItem' => $aBlog])->getTemplate('blog.block.feed', true);

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        $aBlog['group_id'] = $aBlog['item_id'];
        $aRow['item_id'] = $aBlog['blog_id'];
        $aReturn = array_merge(array(
            'feed_title' => $aBlog['title'],
            'privacy' => $aBlog['privacy'],
            'feed_info' => _p('foxfavorite.favorited_a_blog'),
            'feed_link' => Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']),
            'total_comment' => $aBlog['total_comment'],
            'feed_total_like' => $aBlog['total_like'],
            'feed_is_liked' => isset($aBlog['is_liked']) ? $aBlog['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/blog.png',
                'return_url' => true
            )),
            'enable_like' => true,
            'comment_type_id' => 'blog',
            'like_type_id' => 'blog',
            'custom_data_cache' => $aBlog,
            'load_block' => 'blog.feed',
            'feed_custom_html' => $sContent
        ), $aBlog);

        $aReturn['type_id'] = 'blog';

        // Strips all image in content
        list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aBlog['text'], 'img');
        $aReturn['feed_content'] = $sDescription;

        // Get image for feed
        if (!empty($aBlog['image_path'])) {
            $sImageSrc = Phpfox::getService('blog')->getImageUrl($aBlog['image_path'], $aBlog['server_id'], '_1024');
            $aReturn['feed_image'] = "<span style='background-image: url({$sImageSrc})'></span>";
        } else {
            $sImageSrc = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            if (!empty($sImageSrc)) {
                $aReturn['feed_image'] = "<span style='background-image: url({$sImageSrc})'></span>";
            }
        }

        $aCategories = Phpfox::getService('blog.category')->getCategoriesByBlogId($aBlog['blog_id']);
        $sHtmlCategories = '';

        if (!empty($aCategories)) {
            $sHtmlCategories = "<a href='" . $aCategories[0]['link'] . "'>" . $aCategories[0]['category_name'] . "</a>";
            unset($aCategories[0]);

            if (!empty($aCategories)) {
                $iCountCategories = count($aCategories);
                $sHtmlCategories .= sprintf(" %s <span class='dropup dropdown-tooltip' data-component='dropdown-tooltip'><a role='button' data-toggle=\"dropdown\" >%s %s</a>", _p('and'), $iCountCategories, $iCountCategories > 1 ? _p('others') : _p('other'));
                $sHtmlCategories .= '<ul class="dropdown-menu dropdown-center">';
                foreach ($aCategories as $aCategory) {
                    $sHtmlCategories .= sprintf("<li><a href='%s'>%s</a></li>", $aCategory['link'], $aCategory['category_name']);
                }
                $sHtmlCategories .= '</ul></span>';
            }
        }

        Phpfox_Component::setPublicParam('custom_param_blog_' . $aRow['feed_id'], ['aItem' => $aBlog,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']),
            'sCategory' => $sHtmlCategories
        ]);

        if (!empty($aBlog['image_path']))
        {
            $aReturn['feed_image'] = '<img src="' . Phpfox::getParam('core.url_pic'). 'blog/' . sprintf($aBlog['image_path'], '_1024') . '" />';
        }

        $aReturn['time_stamp'] = $aBlog['favorite_time_stamp'];

        return $aReturn;

    }

    public function getActivityFeedEvent($aItem)
    {
        if (!Phpfox::isAppActive('Core_Events'))
        {
            return false;
        }
        $this->getItemIdByFavoriteItemId($aItem);

        $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
            'u2.user_id = e.user_id');
        $sSelect = 'e.start_time, e.end_time, e.user_id, e.event_id, e.module_id, e.item_id, e.title, e.time_stamp, e.image_path, e.server_id, e.total_like, e.total_comment, e.location, e.privacy, e.privacy_comment, e.start_time, e.view_id, e.is_sponsor, et.description_parsed, f.time_stamp AS favorite_time_stamp';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', l.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'event\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isUser()) {
            $this->database()->select('ei.invite_id, ei.rsvp_id, ')->leftJoin(Phpfox::getT('event_invite'), 'ei',
                'ei.event_id = e.event_id AND ei.invited_user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select($sSelect)
            ->from(Phpfox::getT('event'), 'e')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = e.event_id AND f.type_id = "event" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('event_text'), 'et', 'et.event_id = e.event_id')
            ->where('e.event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $aRow['start_time_micro'] = Phpfox::getTime('M d, Y', $aRow['start_time'], true, true);
        $aRow['start_time_phrase_stamp'] = Phpfox::getTime('g:ia', $aRow['start_time']);
        $aRow['end_time_micro'] = Phpfox::getTime('M d, Y', $aRow['end_time'], true, true);
        $aRow['end_time_phrase_stamp'] = Phpfox::getTime('g:ia', $aRow['end_time']);
        $aRow['start_day'] = strftime('%d', $aRow['start_time']);
        $aRow['start_month'] = Phpfox::getTime('F', $aRow['start_time']);
        $aRow['event_date'] = Phpfox::getTime(Phpfox::getParam('event.event_basic_information_time'),
            $aRow['start_time']);
        $aRow['total_attending'] = Phpfox::getService('event')->getTotalRsvp($aRow['event_id'], 1);
        $aRow['url'] = Phpfox::permalink('event', $aRow['event_id'], $aRow['title']);

        $sContent = Phpfox_Template::instance()->assign(['aEvent' => $aRow])->getTemplate('event.block.feed-rows',
            true);
        if (!isset($aRow['event_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'event.view_browse_events')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'event.view_browse_events')))
        {
            return false;
        }

        $aReturn = [
            'feed_title' => '',
            'feed_info' => _p('foxfavorite.favorited_an_event'),
            'feed_link' => Phpfox::permalink('event', $aRow['event_id'], $aRow['title']),
            'feed_icon' => Phpfox::getLib('image.helper')->display([
                'theme' => 'module/event.png',
                'return_url' => true
            ]),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'enable_like' => true,
            'like_type_id' => 'event',
            'total_comment' => $aRow['total_comment'],
            'custom_data_cache' => $aRow,
            'feed_custom_html' => $sContent
        ];



        return $aReturn;
    }

    public function getActivityFeedMarketplace($aItem)
    {
        if (!Phpfox::isAppActive('Core_Marketplace'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = $this->database()->select('e.*, l.like_id AS is_liked, f.time_stamp AS favorite_time_stamp')
            ->from(Phpfox::getT('marketplace'), 'e')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = e.listing_id AND f.type_id = "marketplace" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('marketplace_text'), 'et', 'et.listing_id = e.listing_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'marketplace\' AND l.item_id = e.listing_id AND l.user_id = ' . Phpfox::getUserId())->where('e.listing_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['listing_id']))
        {
            return false;
        }

        $feedId = (int)$aItem['feed_id'];

        $sponsorId = \Phpfox::isAppActive('Core_BetterAds') && (!empty($aItem['sponsor_feed_id']) && ((int)$aItem['sponsor_feed_id'] === (int)$feedId )) ? Phpfox::getService('ad.get')->getFeedSponsors($feedId) : 0;

        $aRow['is_in_feed'] = true;
        $aRow['url'] = $sponsorId ? Phpfox::getLib('url')->makeUrl('ad.sponsor', ['view' => $sponsorId]) : Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);
        $aRow['categories'] = Phpfox::getService('marketplace.category')->getCategoriesById($aRow['listing_id']);
        $customContent = Phpfox::getLib('template')->assign('aListing', $aRow)->getTemplate('marketplace.block.feed',
            true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_listing'),
            'feed_link' => $aRow['url'],
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_custom_html' => $customContent,
        );

        return $aReturn;
    }

    public function getActivityFeedPoll($aItem)
    {
        if (!phpfox::isModule('poll'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = Phpfox::getService('poll')->getPollByUrl($aItem['item_id']);

        if (empty($aRow))
        {
            return false;
        }

        $aRow['time_stamp'] = $this->database()->select('time_stamp')
			->from(Phpfox::getT('foxfavorite'))
			->where('type_id = "poll" AND item_id = '.$aItem['item_id'] . ' AND user_id = ' . (int)$aItem['favorite_user_id'])
			->execute('getSlaveField');

        $aRow['poll_is_in_feed'] = true;
		$oTpl = Phpfox::getLib('template');
		$oTpl->assign(array('aPoll' => $aRow, 'iKey' => rand(2,900)));

        $sContent = Phpfox_Template::instance()->assign(['aPoll' => $aRow])->getTemplate('poll.block.feed-rows',
            true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_poll'),
            'feed_link' => Phpfox::permalink('poll', $aRow['poll_id'], $aRow['question']),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/poll.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'poll',
            'can_post_comment' => Phpfox::getUserParam('poll.can_post_comment_on_poll'),
            'like_type_id' => 'poll',
            'custom_data_cache' => $aRow,
            'feed_custom_html' => $sContent
        );

        return $aReturn;
    }

    public function getActivityFeedV($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (!phpfox::isAppActive('PHPfox_Videos'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = f.user_id');
        }

        $aRow = $this->database()->select('v.video_id, v.total_view AS video_total_view, v.module_id, v.item_id, v.title, f.time_stamp, v.total_comment, v.total_like, v.image_path, v.image_server_id, v.is_stream, l.like_id AS is_liked, vt.text_parsed, ve.embed_code, ve.video_url')
            ->from(phpfox::getT('foxfavorite'), 'f')
            ->join(Phpfox::getT('video'), 'v', 'v.video_id = f.item_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'video\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId())
            ->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->leftJoin(Phpfox::getT('video_embed'), 've', 've.video_id = v.video_id')
            ->where('f.type_id = "video" AND v.video_id = ' . (int)$aItem['item_id'] . ' AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'video.view_browse_videos')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'video.view_browse_videos')))
        {
            return false;
        }

        $aRow = Phpfox::getService('v.video')->compileVideo($aRow);

        $aReturn = [
            'feed_title' => $aRow['title'],
            'feed_info' => _p('foxfavorite.favorited_a_video'),
            'feed_link' => Phpfox::permalink('video.play', $aRow['video_id'], $aRow['title']),
            'feed_content' => $aRow['text_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'load_block' => 'v.feed_video',
            'embed_code' => isset($aRow['embed_code']) ? $aRow['embed_code'] : '',
            'is_stream' => isset($aRow['is_stream']) ? $aRow['is_stream'] : 0,
            'type_id' => 'v',
            'share_type_id' => 'foxfavorite_v',
            'video_total_view' => $aRow['video_total_view']
        ];

        if ($aRow['module_id'] == 'pages')
        {
            $aRow['parent_user_id'] = '';
            $aRow['parent_user_name'] = '';
        }

        if (empty($aRow['parent_user_id']))
        {
            $aReturn['feed_info'] = _p('foxfavorite.favorited_a_video');
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aRow);
        }

        return $aReturn;
    }

    public function getActivityFeedQuiz($aRow)
    {
        if (!phpfox::isModule('quiz'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aRow);

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'quiz\' AND l.item_id = q.quiz_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('q.*, ' . (Phpfox::getParam('core.allow_html') ? 'q.description_parsed' : 'q.description') . ' AS description,' . Phpfox::getUserField() . ', f.time_stamp AS favorite_time_stamp')
            ->from(Phpfox::getT('quiz'), 'q')
            ->leftjoin(phpfox::getT('foxfavorite'), 'f', 'f.item_id = q.quiz_id AND f.type_id = "quiz" AND f.user_id = ' . (int)$aRow['favorite_user_id'])
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
            ->where('q.quiz_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['quiz_id']))
        {
            return false;
        }

        $aQuestions = Phpfox::getService('quiz')->getQuestionsByQuizId($aRow['quiz_id']);
        $sContent = Phpfox_Template::instance()->assign(['aQuiz' => $aRow, 'aInitQuestions' => array_slice($aQuestions, 0, 2)])->getTemplate('quiz.block.feed-rows',
            true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_quiz'),
            'feed_link' => Phpfox::permalink('quiz', $aRow['quiz_id'], $aRow['title']),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/quiz.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'quiz',
            'like_type_id' => 'quiz',
            'feed_custom_html' =>  $sContent
        );

        return $aReturn;
    }


    public function getActivityFeedSong($aItem, $bIsAlbum = false)
    {
        if (!phpfox::isModule('music'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if ($bIsAlbum)
        {
            $this->database()->select('ma.name AS album_name, ma.album_id, u.gender, ')->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ma.user_id');
        }

        $this->database()->select('mp.play_id AS is_on_profile, ')->leftJoin(Phpfox::getT('music_profile'), 'mp', 'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId());

        $aRow = $this->database()->select('ms.song_id, ms.title, ms.module_id, ms.item_id, ms.description, ms.total_play, ms.privacy, f.time_stamp, ms.total_comment, ms.total_like, ms.user_id, l.like_id AS is_liked')
            ->from(Phpfox::getT('music_song'), 'ms')
            ->leftjoin(phpfox::getT('foxfavorite'), 'f', 'f.item_id = ms.song_id AND f.type_id = "music" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'music_song\' AND l.item_id = ms.song_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('ms.song_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['song_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'music.view_browse_music')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'music.view_browse_music')))
        {
            return false;
        }

        if ($bIsAlbum && empty($aRow['album_name']))
        {
            $bIsAlbum = false;
        }

        $iTitleLength = (Phpfox::isModule('notification') ? (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength) : 50);

        $sContent = Phpfox_Template::instance()->assign(['aSong' => $aRow])->getTemplate('music.block.mini-feed-entry',
            true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_song'), //($bIsAlbum ? _p('feed.shared_a_song_from_gender_album_a_href_album_link_album_name_a', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'album_link' => Phpfox::getLib('url')->permalink('music.album', $aRow['album_id'], $aRow['album_name']), 'album_name' => Phpfox::getLib('parse.output')->shorten($aRow['album_name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...'))) : _p('feed.shared_a_song')),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'custom_data_cache' => $aRow,
            'feed_custom_html' => $sContent
            );

        return $aReturn;
    }

    public function getActivityFeedPhoto($aItem, $aCallback = null)
    {
        if (!phpfox::isModule('photo'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = $this->database()->select('photo.*, f.time_stamp, l.like_id AS is_liked, pi.description, pfeed.photo_id AS extra_photo_id, pa.album_id, pa.name')
            ->from(phpfox::getT('photo'), 'photo')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = photo.photo_id')
            ->leftjoin(phpfox::getT('foxfavorite'), 'f', 'f.item_id = photo.photo_id AND f.type_id = "photo" AND f.user_id = ' . $aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'photo\' AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId())
            ->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'pfeed.feed_id = ' . (int)$aItem['feed_id'])
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id')
            ->where('photo.photo_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['photo_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'photo.view_browse_photos')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['group_id'], 'photo.view_browse_photos')))
        {
            return false;
        }

        $bIsPhotoAlbum = false;

        if ($aRow['album_id'])
        {
            $bIsPhotoAlbum = true;
        }

        $sLink = Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/';
        $sFeedImageOnClick = '';

        if (($aRow['mature'] == 0 || (($aRow['mature'] == 1 || $aRow['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aRow['user_id'] == Phpfox::getUserId())
        {
            $sCustomCss = 'photo_holder_image';
            $sImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['server_id'],
                'path' => 'photo.url_photo',
                'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aRow, array('full_name' => $aItem['full_name']))),
                'suffix' => '',
                'class' => 'photo_holder'));
        }
        else
        {
            $sImage = Phpfox::getLib('image.helper')->display(array('theme' => 'misc/no_access.png'));
            $sFeedImageOnClick = ' onclick="tb_show(\'' . _p('photo.warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&width=350&link=' . $sLink . '\')); return false;" ';
            $sCustomCss = 'no_ajax_link';
        }
        $aListPhotos = array();
        if ($aRow['extra_photo_id'] > 0)
        {
            $aPhotos = $this->database()->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')->from(Phpfox::getT('photo_feed'), 'pfeed')->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pfeed.photo_id')->where('pfeed.feed_id = ' . (int)$aItem['feed_id'])->limit(3)->order('p.time_stamp DESC')->execute('getSlaveRows');
            foreach ($aPhotos as $aPhoto)
            {
                if (($aPhoto['mature'] == 0 || (($aPhoto['mature'] == 1 || $aPhoto['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aPhoto['user_id'] == Phpfox::getUserId())
                {
                    $aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/" class="photo_holder_image" rel="' . $aPhoto['photo_id'] . '">' . Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aPhoto, array('full_name' => $aItem['full_name']))),
                        'suffix' => '_1024',
                        'max_width' => 100,
                        'max_height' => 100,
                        'class' => 'photo_holder',
                        'userid' => isset($aItem['user_id']) ? $aItem['user_id'] : '')) . '</a>';
                }
                else
                {
                    $aListPhotos[] = '<a href="#" class="no_ajax_link" onclick="tb_show(\'' . _p('photo.warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&width=350&link=' . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/\')); return false;">' . $sImage . '</a>';
                }
            }
            $aListPhotos = array_merge($aListPhotos, array('<a href="' . Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/" ' . (empty($sFeedImageOnClick) ? ' class="photo_holder_image" rel="' . $aRow['photo_id'] . '" ' : $sFeedImageOnClick . ' class="no_ajax_link"') . '>' . $sImage . '</a>'));
        }

        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('foxfavorite.favorited_a_photo'),
            'feed_image' => (count($aListPhotos) ? $aListPhotos : $sImage),
            'feed_status' => $aRow['description'],
            'feed_link' => $sLink,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'custom_css' => $sCustomCss,
            'custom_rel' => $aRow['photo_id'],
            'custom_js' => $sFeedImageOnClick,
            'type_id' => 'photo'
        );

        return $aReturn;
    }

    public function getActivityFeedProfile($aItem)
    {
        return false;
    }

    public function getActivityFeedPages($aItem, $callback = null, $isChildItem = false)
    {
        if (!Phpfox::isAppActive('Core_Pages'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'pages\' AND l.item_id = p.page_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('p.privacy, p.page_id, p.type_id, p.category_id, p.cover_photo_id, p.total_like, p.title, pu.vanity_url, p.image_path, p.image_server_id, p_type.name AS parent_category_name, pg.name AS category_name, f.time_stamp, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('pages'), 'p')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = p.page_id AND f.type_id = "pages" AND f.user_id = ' . $aItem['favorite_user_id'])
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
            ->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pg.type_id')
            ->where('p.page_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }


        $type = Phpfox::getService('pages.type')->getById($aRow['type_id']);
        if (empty($aRow['category_name'])) {
            $aRow['category_name'] = $type['name'];
            $aRow['category_link'] = Phpfox::permalink('pages.category', $aRow['type_id'], $type['name']);
        } else {
            $aRow['type_link'] = Phpfox::permalink('pages.category', $aRow['type_id'], $type['name']);
            $aRow['category_link'] = Phpfox::permalink('pages.sub-category', $aRow['category_id'],
                $aRow['category_name']);
        }

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
        $aRow['page_url'] = $sLink;

        $totalLikes = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('like'))
            ->where('item_id = ' . $aItem['feed_id'] . " AND type_id = 'pages_created'")
            ->execute('getSlaveField');
        $isLikedFeed = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('like'))
            ->where('item_id = ' . $aItem['feed_id'] . " AND type_id = 'pages_created'" . ' AND user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');

        $aRow['is_liked_page'] = Phpfox::getService('pages')->isMember($aItem['item_id']);

        $customContent = Phpfox::getLib('template')->assign(['aPage' => $aRow, 'aCoverPhoto' => ($aRow['cover_photo_id'] ? Phpfox::getService('photo')->getCoverPhoto($aRow['cover_photo_id']) : false)])->getTemplate('pages.block.page-feed', true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_page'),
            'feed_link' => phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'feed_content' => '',
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $totalLikes,
            'feed_is_liked' => $isLikedFeed,
            'feed_custom_html' => $customContent,
            'share_type_id' => 'foxfavorite_pages'
        );

        if($isChildItem) {
            $aReturn = array_merge($aReturn, $aRow);
        }

        return $aReturn;
    }

    public function getActivityFeedKaraoke($aItem)
    {
        if (!phpfox::isModule('karaoke'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aFavor = $this->database()->select('kf.*')->from(Phpfox::getT('karaoke_favorite'), 'kf')->where('kf.favorite_id = ' . (int)$aItem['item_id'])->execute('getSlaveRow');

        if(!count($aFavor))
        {
            return false;
        }

        if($aFavor['item_type']=='song')
        {
            $aRow = $this->database()->select('ks.*, kf.*, f.time_stamp')
                ->from(Phpfox::getT('karaoke_song'), 'ks')
                ->join(Phpfox::getT('karaoke_favorite'), 'kf', 'kf.item_id = ks.song_id')
                ->leftJoin(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = kf.favorite_id AND f.type_id = "karaoke" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
                ->where('kf.favorite_id = ' . (int)$aItem['item_id'])
                ->execute('getSlaveRow');

            if (!isset($aRow['song_id']))
            {
                return false;
            }

            $iTitleLength = (Phpfox::isModule('notification') ? (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength) : 50);
            $aReturn = array(
                'feed_title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], $iTitleLength, '...'),
                'feed_status' => $aRow['description'],
                'feed_info' => _p('foxfavorite.favorited_a_karaoke_song'),
                'feed_link' => Phpfox::permalink('karaoke.songdetail', $aRow['song_id'], $aRow['title']),
                'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
                'time_stamp' => $aRow['time_stamp'],
                );

            if (!empty($aRow['image_path']))
            {
                $aReturn['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => 0,
                    'path' => 'core.url_file',
                    'file' => 'karaoke/image'.$aRow['image_path'],
                    'suffix' => ''));
            }
            else
            {
                $aReturn['feed_image_banner'] = '<img src="'.Phpfox::getParam('core.path').'module/karaoke/static/image/kara-icon.jpg" width="100" />';
            }

            return $aReturn;
        }
        elseif($aFavor['item_type']=='recording')
        {
            $aRow = $this->database()->select('kr.*, kf.*, f.time_stamp')->from(Phpfox::getT('karaoke_recording'), 'kr')->join(Phpfox::getT('karaoke_favorite'), 'kf', 'kf.item_id = kr.recording_id')->leftJoin(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = kf.favorite_id')->where('f.type_id = "karaoke" AND kf.favorite_id = ' . (int)$aItem['item_id'])->execute('getSlaveRow');

            if (!isset($aRow['recording_id']))
            {
                return false;
            }

            $iTitleLength = (Phpfox::isModule('notification') ? (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength) : 50);
            $aReturn = array(
                'feed_title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], $iTitleLength, '...'),
                'feed_status' => $aRow['description'],
                'feed_info' => _p('foxfavorite.favorited_a_karaoke_recording'),
                'feed_link' => Phpfox::permalink('karaoke.recordingdetail', $aRow['recording_id'], $aRow['title']),
                'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
                'time_stamp' => $aRow['time_stamp'],
                );

            if (!empty($aRow['image_path']))
            {
                $aReturn['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => 0,
                    'path' => 'core.url_file',
                    'file' => 'karaoke/image'.$aRow['image_path'],
                    'suffix' => ''));
            }
            else
            {
                $aReturn['feed_image_banner'] = '<img src="'.Phpfox::getParam('core.path').'module/karaoke/static/image/kara-icon.jpg" width="100" />';
            }

            return $aReturn;
        }
        else
        {
            return false;
        }
    }

    public function getActivityFeedDocument($aRow)
    {
        if (!phpfox::isModule('document'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aRow);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'document\' AND l.item_id = d.document_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('d.*, f.time_stamp')
            ->from(Phpfox::getT('document'), 'd')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = d.document_id AND f.type_id = "document" AND f.user_id = ' . (int)$aRow['favorite_user_id'])
            ->where('d.document_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('foxfavorite.favorited_a_document'),
            'feed_link' => Phpfox::permalink('document', $aRow['document_id'], $aRow['title']),
            'feed_content' => $aRow['description'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            );

        return $aReturn;
    }

    public function convertToUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;

        $iTime = $iTime + $iTimeZoneOffsetInSecond;

        return $iTime;
    }

    public function getActivityFeedFevent($aItem)
    {
        if (!phpfox::isModule('fevent'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $sSelect = 'e.*, et.description_parsed, fi.rsvp_id, fi.user_id AS inviter_id, fi.invited_user_id AS invitee_id, f.time_stamp AS favorite_time_stamp';
        if (Phpfox::isModule('like'))
        {
            $sSelect .= ', l.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fevent\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select($sSelect)
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'et', 'et.event_id = e.event_id')
            ->leftJoin(Phpfox::getT('fevent_invite'), 'fi', 'fi.event_id = e.event_id AND fi.invited_user_id = '. Phpfox::getUserId())
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = e.event_id AND f.type_id = "fevent" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->where('e.event_id = ' . (int) $aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['event_id'])) {
            return false;
        }
        $aRow['d_type'] = Phpfox::getService('fevent.helper')->getTimeLineStatus($aRow['start_time'], $aRow['end_time']);
        $timeNeedToFormatted = in_array($aRow['d_type'], ['ongoing', 'past']) ? $aRow['end_time'] : $aRow['start_time'];
        $aRow['date_formatted'] = Phpfox::getService('fevent.helper')->formatTimeToDate($aRow['d_type'], $timeNeedToFormatted);
        $aRow['d_day'] = Phpfox::getTime('d', $timeNeedToFormatted);
        $aRow['d_month'] = Phpfox::getTime('F', $timeNeedToFormatted);
        $aRow['d_time'] = date('g:i a, d F', $timeNeedToFormatted);
        $aRow['is_invited'] = !empty($aRow['invitee_id']) && !empty($aRow['inviter_id']) ? ($aRow['inviter_id'] != $aRow['invitee_id'] ? true : ($aRow['user_id'] == $aRow['invitee_id'] ? true : false)) : false;

        $locationText = $aRow['location'];
        if (!empty($aRow['address'])) {
            $locationText .= ', ' . $aRow['address'];
        }
        if (!empty($aRow['city'])) {
            $locationText .= ', ' . $aRow['city'];
        }
        if (!empty($aRow['country_iso'])) {
            $locationText .= ', ' . Phpfox::getPhraseT(Phpfox::getService('core.country')->getCountry($aRow['event_country_iso']), 'country');
        }

        Phpfox::getService('fevent')->getMoreInfoForEventItem($aRow, true);

        $sContent = Phpfox_Template::instance()->assign([
            'link' => Phpfox::permalink(Phpfox::getLib('url')->doRewrite('fevent'), $aRow['event_id'], $aRow['title']),
            'aItem' => $aRow,
            'defaultImage' => Phpfox::getService('fevent')->getDefaultPhoto(),
            'location' => $locationText,
            'rsvpActionType' => 'list',
        ])->getTemplate('fevent.block.feed-event',
            true);

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'event.view_browse_events')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'event.view_browse_events')))
        {
            return false;
        }

        $aRow['is_on_feed'] = true;
        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('foxfavorite.favorited_an_event'),
            'feed_link' => Phpfox::permalink('fevent', $aRow['event_id'], $aRow['title']),
            'feed_content' => '',
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/advevent.png', 'return_url' => true)),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'enable_like' => true,
            'like_type_id' => 'fevent',
            'total_comment' => $aRow['total_comment'],
            'custom_data_cache' => $aRow,
            'feed_custom_html' => $sContent
        );

        return $aReturn;
    }

    public function getActivityFeedAdvancedmarketplace($aItem)
    {
        if (!phpfox::isModule('advancedmarketplace'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = $this->database()->select('e.*, f.time_stamp, et.short_description, et.short_description_parsed, et.description_parsed, l.like_id AS is_liked')
            ->from(Phpfox::getT('advancedmarketplace'), 'e')
            ->leftJoin(Phpfox::getT('advancedmarketplace_text'), 'et', 'et.listing_id = e.listing_id')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = e.listing_id AND f.type_id = "advancedmarketplace" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'advancedmarketplace\' AND l.item_id = e.listing_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('e.listing_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!empty($aRow['listing_id'])) {
            $aRow['categories'] = Phpfox::getService('advancedmarketplace.category')->getCategoriesById($aRow['listing_id']);
        }

        $sContent = Phpfox_Template::instance()->assign(['aListing' => $aRow])->getTemplate('advancedmarketplace.block.feed',
            true);

        if (!isset($aRow['listing_id']))
        {
            return false;
        }

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_listing'),
            'feed_link' => Phpfox::permalink('advancedmarketplace.detail', $aRow['listing_id'], $aRow['title']),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_custom_html' => $sContent

        );

        return $aReturn;
    }



    public function getActivityFeedChannelVideo($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (!phpfox::isModule('videochannel'))
        {
            return false;
        }

        $this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.parent_user_id');

        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = v.user_id');
        }

        if(Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'videochannel\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('v.video_id, v.module_id,v.item_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.image_path, v.user_id, v.image_server_id, v.duration, vt.text_parsed,ve.*')
            ->from(Phpfox::getT('channel_video'), 'v')
            ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = v.video_id AND f.type_id = "videochannel" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->join(Phpfox::getT('channel_video_embed'),'ve','v.video_id = ve.video_id')
            ->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'videochannel\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId())->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->where('v.video_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['video_id']))
        {
            return false;
        }

        if ($bIsChildItem)
        {
            $aItem = array_merge($aRow, $aItem);
        }

        $sContent = Phpfox_Template::instance()->assign(['aVideos' => $aRow])->getTemplate('videochannel.block.feed',
            true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_video'),
            'feed_title' => $aRow['title'],
            'feed_link' => Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'feed_content' => Phpfox::getLib('parse.output')-> shorten($aRow['text_parsed'],400,'...'),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/videochanel.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'videochannel',
            'like_type_id' => 'videochannel',
            'type_id' => 'videochannel',
            'load_block' => 'videochannel.feed',
            'feed_custom_html' => $sContent
        );

        if ($aCallback === null)
        {
            if (!empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST))
            {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
            }

            if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
            {
                $aReturn['feed_mini'] = true;
                $aReturn['feed_mini_content'] = _p('feed.full_name_posted_a_href_link_a_video_a_on_a_href_profile_parent_full_name_a_s_a_href_profile_link_wall_a', array('full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']), 'link' => Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']), 'profile' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']), 'parent_full_name' => $aRow['parent_full_name'], 'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name'])));
                $aReturn['feed_title'] = '';
                unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
            }
        }

        if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
        {

        }
        else
        {
            if (!empty($aRow['image_path']))
            {
                $sImage = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRow['image_server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aRow['image_path'],
                        'suffix' => '_120'
                    )
                );

            }
            $aReturn['feed_image_onclick'] = '$Core.box(\'videochannel.play\', 700, \'id=' . $aRow['video_id'] . '&amp;feed_id=' . (isset($aItem['feed_id']) ? $aItem['feed_id'] : 0) . '&amp;popup=true\', \'GET\'); return false;';
        }

        $aRow['is_one_video'] = true;
        $aRow['link'] = Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']);
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $aRow['video_url'], $matches);
        if(isset($matches[1]))
        {
            $aRow['video_code'] = $matches[1];
        }
        else{
            $aRow['video_code'] = '';
        }
        $aVideos = Phpfox::getService('videochannel')->getFeedVideos($aItem['feed_id']);
        $iVideos = count($aVideos);
        Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], $iVideos ? $aVideos : $aRow);
        Phpfox_Template::instance()->assign('aVideos', $iVideos ? $aVideos : $aRow);
        if (empty($aRow['parent_user_id']))
        {
            if($iVideos <= 1) {
                $aReturn['feed_info'] = _p('feed.shared_a_video');
                $aReturn['load_block'] = 'videochannel.feed';
            }
            else {
                $iChannelId = $this->database()->select('channel_id')->from(Phpfox::getT('channel_channel_data'))->where('video_id = ' . $aRow['video_id'])->execute('getField');
                $aChannel = Phpfox::getService('videochannel.channel.process')->getChannel($iChannelId);
                $aReturn['feed_info'] = _p('videochannel.added_x_videos_to_user_channel', array(
                    'total_videos' => $iVideos,
                    'gender' => Phpfox::getService('user')->gender($aRow['parent_gender'], 1),
                    'channel_title' => $aChannel['title'],
                    'channel_url' => Phpfox::permalink('videochannel.channel', $aChannel['channel_id'], $aChannel['title'])
                ));
                $aReturn['load_block'] = 'videochannel.feed';
            }
        }

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' .Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int) $aRow['item_id'])
                ->execute('getRow');

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            if ($aRow['user_id'] != $aPage['parent_user_id']){
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }
        return array_merge($aReturn, $aItem);
    }

    public function getActivityFeedAdvancedPhoto($aItem, $aCallback = null)
    {
        if (!phpfox::isModule('advancedphoto'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = $this->database()->select('photo.*, f.time_stamp, l.like_id AS is_liked, pi.description, pfeed.photo_id AS extra_photo_id, pa.album_id, pa.name')->from(phpfox::getT('photo'), 'photo')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = photo.photo_id')
            ->join(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = photo.photo_id AND f.type_id = "advancedphoto" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'photo\' AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId())
            ->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'pfeed.feed_id = ' . (int)$aItem['feed_id'])
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id')
            ->where('photo.photo_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['photo_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'photo.view_browse_photos')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['group_id'], 'photo.view_browse_photos')))
        {
            return false;
        }

        $bIsPhotoAlbum = false;

        if ($aRow['album_id'])
        {
            $bIsPhotoAlbum = true;
        }

        $sLink = Phpfox::permalink('advancedphoto', $aRow['photo_id'], $aRow['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/';
        $sFeedImageOnClick = '';

        if (($aRow['mature'] == 0 || (($aRow['mature'] == 1 || $aRow['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aRow['user_id'] == Phpfox::getUserId())
        {
            $sCustomCss = 'photo_holder_image';
            $sImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['server_id'],
                'path' => 'photo.url_photo',
                'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aRow, array('full_name' => $aItem['full_name']))),
                'suffix' => '',
                'class' => 'photo_holder'));
        }
        else
        {
            $sImage = Phpfox::getLib('image.helper')->display(array('theme' => 'misc/no_access.png'));
            $sFeedImageOnClick = ' onclick="tb_show(\'' . _p('photo.warning') . '\', $.ajaxBox(\'advancedphoto.warning\', \'height=300&width=350&link=' . $sLink . '\')); return false;" ';
        }

        $aListPhotos = array();
        if ($aRow['extra_photo_id'] > 0)
        {
            $aPhotos = $this->database()->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')->from(Phpfox::getT('photo_feed'), 'pfeed')->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pfeed.photo_id')->where('pfeed.feed_id = ' . (int)$aItem['feed_id'])->limit(3)->order('p.time_stamp DESC')->execute('getSlaveRows');
            foreach ($aPhotos as $aPhoto)
            {
                if (($aPhoto['mature'] == 0 || (($aPhoto['mature'] == 1 || $aPhoto['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aPhoto['user_id'] == Phpfox::getUserId())
                {
                    $aListPhotos[] = '<a href="' . Phpfox::permalink('advancedphoto', $aPhoto['photo_id'], $aPhoto['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/" class="photo_holder_image" rel="' . $aPhoto['photo_id'] . '">' . Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aPhoto, array('full_name' => $aItem['full_name']))),
                        'suffix' => '_100',
                        'max_width' => 100,
                        'max_height' => 100,
                        'class' => 'photo_holder',
                        'userid' => isset($aItem['user_id']) ? $aItem['user_id'] : '')) . '</a>';
                }
                else
                {
                    $aListPhotos[] = '<a href="#" class="no_ajax_link" onclick="tb_show(\'' . _p('photo.warning') . '\', $.ajaxBox(\'advancedphoto.warning\', \'height=300&width=350&link=' . Phpfox::permalink('advancedphoto', $aPhoto['photo_id'], $aPhoto['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/\')); return false;">' . $sImage . '</a>';
                }
            }

            $aListPhotos = array_merge($aListPhotos, array('<a href="' . Phpfox::permalink('advancedphoto', $aRow['photo_id'], $aRow['title']) . ($bIsPhotoAlbum ? 'albumid_' . $aRow['album_id'] : 'userid_' . $aRow['user_id']) . '/" ' . (empty($sFeedImageOnClick) ? ' class="photo_holder_image" rel="' . $aRow['photo_id'] . '" ' : $sFeedImageOnClick . ' class="no_ajax_link"') . '>' . $sImage . '</a>'));
        }

        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('foxfavorite.favorited_a_photo'),
            'feed_image' => (count($aListPhotos) ? $aListPhotos : $sImage),
            'feed_status' => $aRow['description'],
            'feed_link' => $sLink,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
        );

        return $aReturn;
    }

    public function getActivityFeedDirectory($aItem)
    {
        if (!phpfox::isModule('directory'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $sWhere = ' and e.business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';
        $aRow = $this->database()->select('u.user_id, e.business_id, e.package_data, e.module_id, e.item_id, e.business_id, e.name, e.time_stamp, e.logo_path as image_path, e.server_id as image_server_id, e.total_like, e.total_comment, e.short_description_parsed as description_parsed, l.like_id AS is_liked, f.time_stamp AS favorite_time_stamp')
            ->from(Phpfox::getT('directory_business'), 'e')
            ->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
            ->leftJoin(Phpfox::getT('directory_business_text'), 'et', 'et.business_id = e.business_id')
            ->join(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = e.business_id AND f.type_id = "directory" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'directory\' AND l.item_id = e.business_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('e.business_id = ' . (int) $aItem['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['business_id']))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('foxfavorite.favorited_a_business'),
            'feed_link' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['title']),
            'feed_content' => $aRow['description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'load_block' => 'directory.feed',
            );

        if (!empty($aRow['image_path']))
        {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aRow['image_path'],
                    'yndirectory_overridenoimage' => true,
                    'return_url' => true,
                    'suffix' => ''
                )
            );
        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/directory/static/image/default_ava.png';
        }
        // Strips all image in content
        list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['description_parsed'], 'img');
        $aReturn['feed_content'] = $sDescription;

        Phpfox_Component::setPublicParam('custom_param_directory_' . $aItem['feed_id'], ['aItem' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']),
            'aCategory' => Phpfox::getService('directory.category')->getMainCategoryByBusinessId($aRow['business_id'])
        ]);


        return $aReturn;
    }

    public function getActivityFeedAuction($aItem)
    {
        if (!phpfox::isModule('auction'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $sWhere = '';
        $sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
        $aRow = $this->database()->select('u.user_id, e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id, e.total_like, e.total_comment, et.description_parsed as description_parsed, l.like_id AS is_liked')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
            ->join(Phpfox::getT('foxfavorite'), 'f','f.item_id = e.product_id AND f.type_id = "auction" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'auction\' AND l.item_id = e.product_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('e.product_id = ' . (int) $aItem['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['name'],
            'feed_info' => _p('foxfavorite.favorited_a_product'),
            'feed_link' => Phpfox::permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'feed_content' => $aRow['description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/auction.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
        );

        if (NULL != $aRow['image_path'])
        {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aRow['image_path'],
                    'ynauction_overridenoimage' => true,
                    'suffix' => '',
                    'return_url' => true,
                )
            );

            $aReturn['feed_image_banner'] = $sImage;
        }

        return $aReturn;
    }

    public function getActivityFeedCoupon($aItem)
    {
        if (!phpfox::isModule('coupon'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        $aRow = $this->database()->select('c.coupon_id, c.title, c.time_stamp, c.module_id, c.item_id, c.server_id, c.image_path, c.total_comment, c.total_like, ct.description_parsed AS description, l.like_id AS is_liked, f.time_stamp AS favorite_time_stamp')
            ->from(Phpfox::getT('coupon'), 'c')
            ->leftJoin(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id')
            ->join(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = c.coupon_id AND f.type_id = "coupon" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'coupon\' AND l.item_id = c.coupon_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('c.coupon_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['coupon_id']))
        {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'coupon.view_browse_coupons')) || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'coupon.view_browse_coupons')))
        {
            return false;
        }

        $content = Phpfox::getLib('template')->assign([
            'sLink' => Phpfox::permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
            'aItem' => $aRow,
            'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
        ])->getTemplate('coupon.block.feed', true);

        $aReturn = [
            'feed_info' => _p('foxfavorite.favorited_a_coupon'),
            'feed_link' => Phpfox::permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
            'feed_content' => $aRow['description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_custom_html' => $content
        ];

        return $aReturn;
    }

    public function getActivityFeedContest($aItem)
    {
        if (!Phpfox::isModule('contest'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'contest\' AND l.item_id = c.contest_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('c.*, f.time_stamp AS favorite_time_stamp')
        ->from(Phpfox::getT('contest'), 'c')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = c.contest_id AND f.type_id = "contest" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $link = Phpfox::permalink('contest', $aRow['contest_id'], $aRow['contest_name']);

        $aRow = array_merge($aRow, [
            'feed_title' => $aRow['contest_name'],
            'feed_content' => Phpfox::getLib('parse.output')->shorten($aRow['short_description'], 200, '...'),
            'image_path' => !empty($aRow['image_path']) ? Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'contest/' . $aRow['image_path'],
                    'suffix' => '_400',
                    'class' => 'photo_holder',
                    'return_url' => true,
                )
            ) : '',
            'feed_link' => $link
        ]);

        $content = Phpfox::getLib('template')->assign('aItem', $aRow)->getTemplate('contest.block.contest.feed', true);

        $aReturn = [
            'feed_info' => _p('foxfavorite.favorited_a_contest'),
            'feed_link' => $link,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_custom_html' => $content
        ];

        return $aReturn;
    }

    public function getActivityFeedResume($aItem)
    {
        if (!Phpfox::isModule('resume'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'resume\' AND l.item_id = rbi.resume_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('rbi.*, rbi.server_id as item_server_id, f.time_stamp')
        ->from(Phpfox::getT('resume_basicinfo'), 'rbi')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = rbi.resume_id AND f.type_id = "resume" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['headline'],
            'feed_info' => _p('foxfavorite.favorited_a_resume'),
            'feed_link' => Phpfox::permalink('resume.view', $aRow['resume_id'], $aRow['headline']),
            'feed_content' => '',
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp']);

        $aReturn['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['item_server_id'],
            'path' => 'core.url_pic',
            'file' => 'resume/'.$aRow['image_path'],
            'suffix' => ''));

        return $aReturn;
    }

    public function getActivityFeedJobposting($aItem)
    {
        if (!Phpfox::isModule('jobposting'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'jobposting_job\' AND l.item_id = j.job_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('j.*, jt.description_parsed, c.image_path, c.server_id as item_server_id, f.time_stamp AS favorite_time_stamp')
        ->from(Phpfox::getT('jobposting_job'), 'j')
        ->join(Phpfox::getT('jobposting_job_text'), 'jt', 'jt.job_id = j.job_id')
        ->join(Phpfox::getT('jobposting_company'), 'c', 'c.company_id = j.company_id')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = j.job_id AND f.type_id = "jobposting" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $company = Phpfox::getService('jobposting.company')->getCompanyById($aRow['company_id']);
        if (!empty($company['image_path'])) {
            $imageSource = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $company['server_id'],
                'path' => 'core.url_pic',
                'file' => 'jobposting/' . $company['image_path'],
                'suffix' => '_500',
                'return_url' => true
            ));

        } else {
            $imageSource = Phpfox::getParam('core.path_file') . 'module/jobposting/static/image/default/default_ava.png';
        }

        $aRow['time_expire_micro'] = Phpfox::getTime('M d, Y', $aRow['time_expire'], true, true);

        $content = Phpfox::getLib('template')->assign([
            'aJob' => $aRow,
            'sImageSrc' => $imageSource,
            'sLink' => Phpfox::permalink('jobposting', $aRow['job_id'], $aRow['title']),
            'sCategories' => Phpfox::getService('jobposting.catjob')->getPhraseCategory($aRow['job_id'])
        ])->getTemplate('jobposting.block.job.feed', true);

        $aReturn = array(
            'feed_info' => _p('foxfavorite.favorited_a_job'),
            'feed_link' => Phpfox::permalink('jobposting', $aRow['job_id'], $aRow['title']),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['favorite_time_stamp'],
            'feed_custom_html' => $content,
        );
        
        return $aReturn;
    }

    public function getActivityFeedFoxfeedspro($aItem)
    {
        if (!Phpfox::isModule('foxfeedspro'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'foxfeedspro\' AND l.item_id = ni.item_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('ni.*, f.time_stamp')
        ->from(Phpfox::getT('ynnews_items'), 'ni')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = ni.item_id AND f.type_id = "foxfeedspro" AND f.user_id = '. (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['item_title'],
            'feed_info' => _p('foxfavorite.favorited_a_news'),
            'feed_link' => Phpfox::permalink('news.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']),
            'feed_content' => $aRow['item_description_parse'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp']);

        $aReturn['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['server_id'],
            'path' => 'core.url_pic',
            'file' => str_replace(Phpfox::getParam('core.url_pic'), '', $aRow['item_image']),
            'suffix' => ''));

        return $aReturn;
    }

    public function getActivityFeedPetition($aItem)
    {
        if (!Phpfox::isModule('petition'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'petition\' AND l.item_id = p.item_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('p.*, pt.short_description_parsed, f.time_stamp')
        ->from(Phpfox::getT('petition'), 'p')
		->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id = p.petition_id')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = p.petition_id AND f.type_id = "petition" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('foxfavorite.favorited_a_petition'),
            'feed_link' => Phpfox::permalink('petition', $aRow['petition_id'], $aRow['title']),
            'feed_content' => $aRow['short_description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp']);

        $aReturn['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['server_id'],
            'path' => 'core.url_pic',
            'file' => $aRow['image_path'],
            'suffix' => ''));

        return $aReturn;
    }

    public function getActivityFeedMusicsharing($aItem)
    {
        if (!Phpfox::isModule('musicsharing'))
        {
            return false;
        }

        $this->getItemIdByFavoriteItemId($aItem);

        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'musicsharing\' AND l.item_id = s.song_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('s.*, a.user_id, a.album_image,a.server_id, f.time_stamp')
        ->from(Phpfox::getT('m2bmusic_album_song'), 's')
        ->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')
        ->join(phpfox::getT('foxfavorite'), 'f', 'f.item_id = s.song_id AND f.type_id = "musicsharing" AND f.user_id = ' . (int)$aItem['favorite_user_id'])
        ->where('f.item_id = ' . (int)$aItem['item_id'])
        ->execute('getSlaveRow');

        if (empty($aRow))
        {
            return false;
        }
        $sLink = Phpfox::permalink('musicsharing.listen', 'music_'.$aRow['song_id']);
        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('foxfavorite.favorited_a_song'),
            'feed_link' => Phpfox::permalink('musicsharing.listen', 'music_'.$aRow['song_id']),
            'feed_content' => ($aRow['play_count'] > 1) ? $aRow['play_count'].' '._p('foxfavorite.plays') : $aRow['play_count'].' '._p('foxfavorite.play'),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp']
            );

        $aReturn['feed_image_banner'] = '<a href= "'.$sLink.'">'.phpFox::getLib('image.helper')->display(array('theme' => 'misc/play_button.png')).'</a>';
        if (!empty($aRow['album_image']))
        {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                                                                  'server_id' => $aRow['server_id'],
                                                                  'path' => 'core.url_pic',
                                                                  'file' => 'musicsharing/'.$aRow['album_image'],
                                                                  'suffix' => '',
                                                                  'return_url' => true,
                                                              )
            );
            $aReturn['feed_image_banner'] = '<a href= "'.$sLink.'"><img src="' . $sImage . '" style="max-width:200px; max-height:200px;" /></a>';

        }
        return $aReturn;
    }

    public function updateCounterList()
    {
        $aList = array();

        $aList[] = array('name' => 'User Favorite Count', 'id' => 'foxfavorite-total');

        return $aList;
    }

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);

        if ($iId == 'foxfavorite-total')
        {
            $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('user'))->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(b.favorite_id) AS total_items')->from(Phpfox::getT('user'), 'u')->leftJoin(Phpfox::getT('foxfavorite'), 'b', 'b.user_id = u.user_id')->limit($iPage, $iPageLimit, $iCnt)->group('u.user_id')->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_field'), array('total_foxfavorite' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }
        }

        return $iCnt;
    }

    public function getItemIdByFavoriteItemId(&$aItem)
    {
        $row = phpfox::getLib('database')->select('item_id, user_id AS favorite_user_id')->from(phpfox::getT('foxfavorite'))->where('favorite_id = ' . $aItem['item_id'])->execute('getSlaveRow');
        $aItem = array_merge($aItem, $row);
    }

    public function getNotificationSettings()
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getnotificationsettings__start')) ? eval($sPlugin) : false);
        return array(
            'foxfavorite.add_new_favorites' => array(
                'phrase' => _p('foxfavorite.follow_members_whose_profile_whose_i_favorited'),
                'default' => 1
            ),
            'foxfavorite.user_favorited_my_item' => array(
                'phrase' => _p('foxfavorite.notify_me_when_users_favorited_my_items'),
                'default' => 1
            )
        );
    }
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('favorite.service_callback__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __class__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>