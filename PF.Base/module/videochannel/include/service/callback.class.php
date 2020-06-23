<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Service_Callback extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('channel_video');
    }

    public function canShareItemOnFeed()
    {
        
    }

    public function mobileMenu()
    {
        return array(
            'phrase' => _p('videochannel.videochannel'),
            'link' => Phpfox::getLib('url')->makeUrl('videochannel'),
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_videos.png'))
        );
    }

    public function getProfileMenu($aUser)
    {

        $aSubMenus = array();
        $iTotal = Phpfox::getService('videochannel')->getUserTotalVideos($aUser['user_id']);
        $aMenus[] = array(
            'phrase' => _p('videochannel.videochannel'),
            'url' => 'profile.videochannel',
            'total' => (int) $iTotal,
            'icon' => 'feed/videochanel.png',
            'icon_class' => 'ico ico-file-movie-o'
        );


        return $aMenus;
    }

    public function enableSponsor($aParams)
    {
        return Phpfox::getService('videochannel.process')->sponsor((int) $aParams['item_id'], 1);
    }

    public function getProfileLink()
    {
        return 'profile.videochannel';
    }

    public function getAjaxCommentVar()
    {
        return 'videochannel.can_add_comment_on_video';
    }

    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('video_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
                ->from(Phpfox::getT('channel_video'))
                ->where('video_id = ' . (int) $iId)
                ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
        {
            Phpfox_Error::set(_p('videochannel.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    public function getTagCloud()
    {
        return array(
            'link' => 'videochannel',
            'category' => 'videochannel'
        );
    }

    /**
     * Returns information related to a video for sponsoring purposes
     * @param int $iId video_id
     * @return array in the format:
     * array(
     * 	'title' => 'item title',		    <-- required
     * 	'link'  => 'makeUrl()'ed link',		    <-- required
     * 	'paypal_msg' => 'message for paypal'	    <-- required
     * 	'item_id' => int			    <-- required
     * 	'error' => 'phrase if item doesnt exit'	    <-- optional
     * 	'extra' => 'description'		    <-- optional
     * 	'image' => 'path to an image',		    <-- optional
     * 	'image_dir' => 'photo.url_photo|...	    <-- optional (required if image)
     * 	'server_id' => 'value from DB'		    <-- optional (required if image)
     * );
     */
    public function getToSponsorInfo($iId)
    {
        $aVideo = $this->database()->select('v.user_id, v.title, v.video_id as item_id, vt.text_parsed as extra,
               v.image_path as image, v.image_server_id as server_id')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->leftjoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
                ->where('v.video_id = ' . (int) $iId)
                ->execute('getSlaveRow');

        if (empty($aVideo))
        {
            return array('error' => _p('videochannel.sponsor_error_not_found'));
        }

        //$aVideo['link'] = Phpfox::getLib('url')->makeUrl('profile.videochannel.'.$aVideo['title_url']);
        $aVideo['link'] = Phpfox::permalink('ad.sponsor', $aVideo['item_id'], $aVideo['title']);
        $aVideo['paypal_msg'] = _p('videochannel.sponsor_paypal_message', array('sVideoTitle' => $aVideo['title'])); //'Video Sponsor ' . $aVideo['title'];
        $aVideo['title'] = _p('videochannel.sponsor_title', array('sVideoTitle' => $aVideo['title']));
        $aVideo['image_dir'] = 'core.url_pic';
        $aVideo['image'] = sprintf($aVideo['image'], '_120');

        return $aVideo;
    }

    public function getLink($aParams)
    {
        $aVideo = $this->database()->select('u.user_name, v.title_url')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.video_id = ' . (int) $aParams['item_id'])
                ->execute('getSlaveRow');

        if (empty($aVideo))
        {
            return false;
        }

        return Phpfox::getLib('url')->makeUrl($aVideo['user_name'] . '.videochannel.' . $aVideo['title_url']);
    }

    public function getActivityFeedComment($aRow)
    {
        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aItem = $this->database()->select('b.video_id, b.image_server_id, b.image_path, vt.text_parsed AS video_description, b.title, b.time_stamp, b.privacy, b.total_comment, b.total_like, c.total_like, ct.text_parsed AS text, f.friend_id AS is_friend, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('comment'), 'c')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->join(Phpfox::getT('channel_video'), 'b', 'c.type_id = \'videochannel\' AND c.item_id = b.video_id AND c.view_id = 0')
                ->join(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = b.video_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
                ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = b.user_id AND f.friend_user_id = " . Phpfox::getUserId())
                ->where('c.comment_id = ' . (int) $aRow['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aItem['video_id']))
        {
            return false;
        }

        $sLink = Phpfox::permalink('videochannel', $aItem['video_id'], $aItem['title']);
        $sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
        $sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);

        if ($aRow['user_id'] == $aItem['user_id'])
        {
            $sMessage = _p('videochannel.posted_a_comment_on_gender_video', array('gender' => $sGender));
        }
        else
        {
            $sMessage = _p('videochannel.posted_a_comment_on_user_name_s_video', array('user_name' => $sUser));
        }

        $aFeed = array(
            'no_share' => true,
            'feed_title' => $aItem['title'],
            'feed_info' => $sMessage,
            'feed_link' => $sLink,
            'feed_status' => $aItem['text'],
            'feed_content' => $aItem['video_description'],
            'feed_total_like' => $aItem['total_like'],
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/videochanel.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'like_type_id' => 'feed_mini'
        );

        $bCanViewItem = true;
        if ($aItem['privacy'] > 0)
        {
            $bCanViewItem = Phpfox::getService('privacy')->check('videochannel', $aItem['video_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend'], true);
        }

        if ($bCanViewItem)
        {
            if (!empty($aItem['image_path']))
            {
                $aFeed['feed_image'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aItem['image_path'],
                    'suffix' => '_120',
                    'max_width' => 120,
                    'max_height' => 120
                        )
                );
            }

            $aFeed['feed_image_onclick'] = '$Core.box(\'videochannel.play\', 700, \'id=' . $aItem['video_id'] . '&amp;feed_id=' . $aRow['feed_id'] . '&amp;popup=true\', \'GET\'); return false;';
        }

        return $aFeed;
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('m.video_id, m.item_id, m.title, u.full_name, u.gender, u.user_id, u.user_name')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->where('m.video_id = ' . (int) $aVals['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return Phpfox_Error::trigger('Invalid callback on video.');
        }

        if (empty($aRow['item_id']))
        {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id']) : null);
        }

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('channel_video', 'total_comment', 'video_id', $aRow['video_id']);
        }

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']);

        Phpfox::getService('comment.process')->notify(array(
            'user_id' => $aRow['user_id'],
            'item_id' => $aRow['video_id'],
            'owner_subject' => _p('videochannel.full_name_commented_on_your_video_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $this->preParse()->clean($aRow['title'], 100))),
            'owner_message' =>
            _p('videochannel.full_name_commented_on_your_video_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])),
            'owner_notification' => 'comment.add_new_comment',
            'notify_id' => 'videochannel',
            'mass_id' => 'videochannel',
            'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('videochannel.full_name_commented_on_gender_video', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) :
                    _p('videochannel.full_name_commented_on_other_full_name_s_video', array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $aRow['full_name']))),
            'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ?
                    _p('videochannel.full_name_commented_on_gender_video_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $aRow['title'], 'link' => $sLink)) : _p('videochannel.full_name_commented_on_other_full_name_s_video_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $aRow['full_name'], 'link' => $sLink, 'title' => $aRow['title'])))
                )
        );
    }

    public function updateCommentText($aVals, $sText)
    {
        
    }

    public function getItemName($iId, $sName)
    {
        return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . _p('videochannel.on_name_s_video', array('name' => $sName)) . '</a>';
    }

    public function deleteComment($iId)
    {
        $this->database()->updateCounter('channel_video', 'total_comment', 'video_id', $iId, true);
    }

    public function getCommentNewsFeed($aRow)
    {
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        if ($aRow['owner_user_id'] == $aRow['item_user_id'])
        {
            $aRow['text'] = _p('videochannel.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_video_a', array(
                        'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                        'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                        'title_link' => $aRow['link']
                            )
            );
        }
        else
        {
            if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
            {
                $aRow['text'] = _p('videochannel.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_video_a', array(
                            'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                            'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                            'title_link' => $aRow['link']
                                )
                );
            }
            else
            {
                $aRow['text'] = _p('videochannel.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_name_s_a_a_href_title_link_video_a', array(
                            'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                            'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                            'title_link' => $aRow['link'],
                            'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
                            'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name'])
                                )
                );
            }
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);

        return $aRow;
    }

    public function getFeedRedirect($iId, $iChild = 0)
    {
        $aRow = $this->database()->select('m.video_id, m.title')
                ->from($this->_sTable, 'm')
                ->where('m.video_id = ' . (int) $iId)
                ->execute('getSlaveRow');
        ;

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        return Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']);
    }

    public function getFeedRedirectUnFavourite($iId, $iChild = 0)
    {
        return false;
    }

    public function getFeedRedirectFavourite($iId, $iChild = 0)
    {
        return false;
    }

    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getNewsFeed($aRow)
    {
        if ($sPlugin = Phpfox_Plugin::get('videochannelservice_callback_getnewsfeed_start'))
        {
            eval($sPlugin);
        }
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        $aRow['text'] = _p('videochannel.a_href_user_link_owner_full_name_a_added_a_new_video_a_href_title_link_title_a', array(
                    'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                    'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                        )
        );

        $aRow['icon'] = 'module/videochanel.png';
        $aRow['enable_like'] = true;

        return $aRow;
    }

    public function groupMenu($sGroupUrl, $iGroupId)
    {
        if (!Phpfox::getService('group')->hasAccess($iGroupId, 'can_use_video'))
        {
            return false;
        }

        return array(
            _p('videochannel.videochannel') => array(
                'active' => 'videochannel',
                'url' => Phpfox::getLib('url')->makeUrl('group', array($sGroupUrl, 'videochannel')
                )
            )
        );
    }

    public function getWhatsNew()
    {
        return array(
            'videochannel.videochannel' => array(
                'ajax' => '#videochannel.getNew?id=js_new_item_holder',
                'id' => 'videochannel',
                'block' => 'videochannel.new'
            )
        );
    }

    public function getRatingData($iId)
    {
        return array(
            'field' => 'video_id',
            'table' => 'channel_video',
            'table_rating' => 'channel_video_rating'
        );
    }

    public function hideBlockProfile($sType)
    {
        return array(
            'table' => ($sType == 'profile' ? 'user_design_order' : '')
        );
    }

    public function getBlockDetailsProfile()
    {
        return array(
            'title' => _p('videochannel.videochannel')
        );
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     */
    public function onDeleteUser($iUser)
    {
        $aVideos = $this->database()
                ->select('video_id')
                ->from($this->_sTable)
                ->where('user_id = ' . (int) $iUser)
                ->execute('getSlaveRows');

        if (empty($aVideos))
        {
            return false;
        }
        foreach ($aVideos as $aVideo)
        {
            Phpfox::getService('videochannel.process')->delete($aVideo['video_id']);
        }
    }

    public function getGroupPosting()
    {
        return array(
            _p('videochannel.upload_videos') => 'can_upload_video'
        );
    }

    public function getGroupAccess()
    {
        return array(
            _p('videochannel.view_videos') => 'can_use_video'
        );
    }

    public function getItemView()
    {
        if (Phpfox::getLib('request')->get('req3') != '')
        {
            return true;
        }
    }

    public function addTrack($iId, $iUserId = null)
    {
        $this->database()->insert(Phpfox::getT('channel_video_track'), array(
            'item_id' => (int) $iId,
            'user_id' => Phpfox::getUserBy('user_id'),
            'ip_address' => Phpfox::getIp(true),
            'time_stamp' => PHPFOX_TIME
                )
        );

        $this->database()->updateCounter('channel_video', 'total_view', 'video_id', $iId);
    }

    public function getNotificationFeedApproved($aRow)
    {
        return array(
            'message' => _p('videochannel.your_video_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
            'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aRow['item_id'])),
            'path' => 'core.url_pic',
            'suffix' => '_120'
        );
    }

    public function getGlobalPrivacySettings()
    {
        return array(
            'videochannel.display_on_profile' => array(
                'phrase' => _p('videochannel.videochannel'),
                'default' => '0'
            )
        );
    }

    public function pendingApproval()
    {
        return array(
            'phrase' => _p('videochannel.videochannel'),
            'value' => Phpfox::getService('videochannel')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('view' => 'pending'))
        );
    }

    public function legacyRedirect($aRequest)
    {
        if (isset($aRequest['listid']))
        {
            $aItem = Phpfox::getService('core')->getLegacyUrl(array(
                'url_field' => 'name_url',
                'table' => 'channel_category',
                'field' => 'upgrade_item_id',
                'id' => $aRequest['listid'],
                'user_id' => false
                    )
            );

            if ($aItem !== false)
            {
                return array('videochannel', array('category', $aItem['name_url']));
            }
        }

        if (isset($aRequest['id']))
        {
            $aItem = Phpfox::getService('core')->getLegacyUrl(array(
                'url_field' => 'title_url',
                'table' => 'channel_video',
                'field' => 'upgrade_item_id',
                'id' => $aRequest['id']
                    )
            );

            if ($aItem !== false)
            {
                return array($aItem['user_name'], array('videochannel', $aItem['title_url']));
            }
        }

        return 'videochannel';
    }

    public function verifyFavorite($iItemId)
    {
        $aItem = $this->database()->select('i.video_id')
                ->from(Phpfox::getT('channel_video'), 'i')
                ->where('i.video_id = ' . (int) $iItemId . ' AND i.view_id = 0')
                ->execute('getSlaveRow');

        if (!isset($aItem['video_id']))
        {
            return false;
        }

        return true;
    }

    public function getFavorite($aFavorites)
    {
        return array('title' => null, 'items' => array());

        $oServiceVideoBrowse = Phpfox::getService('videochannel.browse');

        $oServiceVideoBrowse->condition('m.video_id IN(' . implode(',', $aFavorites) . ') AND m.in_process = 0 AND m.view_id = 0')
                ->execute();

        $aVideos = $oServiceVideoBrowse->get();

        foreach ($aVideos as $iKey => $aVideo)
        {
            $aVideos[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aVideo['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aVideo['image_path'],
                'suffix' => '_120',
                'max_width' => 75,
                'max_height' => 75
                    )
            );
        }

        return array(
            'title' => _p('videochannel.videochannel'),
            'items' => $aVideos
        );
    }

    public function getDashboardLinks()
    {
        $aLinks = array(
            'edit' => array(
                'phrase' => _p('videochannel.manage_videos'),
                'link' => 'profile.videochannel',
                'image' => 'module/video_edit.png'
            )
        );
        $aUpload = array();
        if (Phpfox::getParam('videochannel.show_share_and_upload_video_on_dashboard') != 'upload')
        {
            $aUpload[] = array(
                'phrase' => _p('videochannel.share_a_video'),
                'link' => 'videochannel.share',
                'image' => 'module/video_add.png'
            );
        }

        if (false && (Phpfox::getParam('videochannel.show_share_and_upload_video_on_dashboard') != 'share'))
        {
            $aUpload[] = array(
                'phrase' => _p('videochannel.upload_a_video'),
                'link' => 'videochannel.upload',
                'image' => 'module/video_add.png'
            );
        }

        $aLinks['submit'] = $aUpload;
        return $aLinks;
    }

    public function reparserList()
    {
        return array(
            'name' => _p('videochannel.video_text'),
            'table' => 'channel_video_text',
            'original' => 'text',
            'parsed' => 'text_parsed',
            'item_field' => 'video_id'
        );
    }

    public function getCommentNotificationFeed($aRow)
    {
        return array(
            'message' => _p('videochannel.full_name_wrote_a_comment_on_your_video', array(
                'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                'full_name' => $aRow['full_name'],
                'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aRow['item_id'])),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')
                    )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aRow['item_id'])),
            'path' => 'core.url_user',
            'suffix' => '_50'
        );
    }

    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('videochannel.videos_in_channels') => $aUser['activity_videochannel']
        );
    }

    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        $sCondition = 'p.in_process = 0 AND p.view_id = 0 AND p.item_id = 0 AND p.privacy = 0';
        if ($bIsTagSearch === false)
        {
            $sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pi.text_parsed LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
        }

        if ($bIsTagSearch == true)
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.video_id AND tag.category_id = \'videochannel\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
        }

        $iCnt = $this->database()->select('COUNT(*)')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('channel_video_text'), 'pi', 'pi.video_id = p.video_id')
                ->where($sCondition)
                ->execute('getSlaveField');

        if ($bIsTagSearch == true)
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.video_id AND tag.category_id = \'videochannel\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.video_id');
        }

        $aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, p.image_path, p.image_server_id, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('channel_video_text'), 'pi', 'pi.video_id = p.video_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->where($sCondition)
                ->limit(10)
                ->order('p.time_stamp DESC')
                ->execute('getSlaveRows');

        if (count($aRows))
        {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('videochannel.videochannel');

            if ($bIsTagSearch == true)
            {
                $aResults['form'] = '<div><input type="button" value="' . _p('videochannel.view_more_videos') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('videochannel', array('tag', $sQuery)) . '\';" /></div>';
            }
            else
            {
                $aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('videochannel') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[keyword]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="submit" value="' . _p('videochannel.view_more_videos') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow)
            {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('videochannel', $aRow['title_url'])),
                    'image' => Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRow['image_server_id'],
                        'title' => $aRow['title'],
                        'path' => 'core.url_pic',
                        'file' => $aRow['image_path'],
                        'suffix' => '_120',
                        'max_width' => 75,
                        'max_height' => 75
                            )
                    ),
                    'extra_info' => _p('videochannel.video_added_on_time_stamp_by_full_name', array(
                        'link' => Phpfox::getLib('url')->makeUrl('videochannel'),
                        'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                        'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                        'full_name' => $this->preParse()->clean($aRow['full_name'])
                            )
                    )
                );
            }

            return $aResults;
        }
    }

    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'view_id = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . db()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . db()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'video_channel',
            'total' => $iCnt
        ];
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('videochannel.videochannel'),
            'value' => $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('channel_video'))
                    ->where('view_id = 0 AND in_process = 0 AND time_stamp >= ' . $iToday)
                    ->execute('getSlaveField')
        );
    }

    public function updateCounterList()
    {
        $aList = array();

        $aList[] = array(
            'name' => _p('videochannel.update_video_view_count'),
            'id' => 'video-view-count'
        );

        $aList[] = array(
            'name' => _p('videochannel.update_tags_videos'),
            'id' => 'video-tag-update'
        );

        $aList[] = array(
            'name' => _p('videochannel.update_user_video_count'),
            'id' => 'video-count'
        );

        return $aList;
    }

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'video-count')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('user'))
                    ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id')
                    ->from(Phpfox::getT('user'), 'u')
                    ->limit($iPage, $iPageLimit, $iCnt)
                    ->group('u.user_id')
                    ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $iTotalPhotos = $this->database()->select('COUNT(m.video_id)')
                        ->from(Phpfox::getT('channel_video'), 'm')
                        ->where('m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.user_id = ' . $aRow['user_id'])
                        ->execute('getSlaveField');

                $this->database()->update(Phpfox::getT('user_field'), array('total_videochannel' => $iTotalPhotos), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }

        if ($iId == 'video-tag-update')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('tag'))
                    ->where('category_id = \'videochannel\'')
                    ->execute('getSlaveField');

            $aRows = $this->database()->select('m.tag_id, oc.video_id AS tag_item_id')
                    ->from(Phpfox::getT('tag'), 'm')
                    ->where('m.category_id = \'videochannel\'')
                    ->leftJoin(Phpfox::getT('channel_video'), 'oc', 'oc.video_id = m.item_id')
                    ->limit($iPage, $iPageLimit, $iCnt)
                    ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                if (empty($aRow['tag_item_id']))
                {
                    $this->database()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
                }
            }

            return $iCnt;
        }

        if (!file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php'))
        {
            return Phpfox_Error::set(_p('comment.your_old_v1_6_21_setting_file_must_exist', array('file' => 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')));
        }

        require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php');

        $sTable = (isset($_CONF['db']['prefix']) ? $_CONF['db']['prefix'] : '') . 'videos';

        if (!$this->database()->tableExists($sTable))
        {
            return Phpfox_Error::set(_p('comment.the_database_table_table_does_not_exist', array('table' => $sTable)));
        }

        $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('channel_video'))
                ->execute('getSlaveField');

        $aRows = $this->database()->select('m.video_id, m.total_view, oc.vid_total')
                ->from(Phpfox::getT('channel_video'), 'm')
                ->join($sTable, 'oc', 'oc.vid_id = m.upgrade_item_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

        foreach ($aRows as $aRow)
        {
            $this->database()->update(Phpfox::getT('channel_video'), array('total_view' => ($aRow['total_view'] + $aRow['vid_total'])), 'video_id = ' . (int) $aRow['video_id']);
        }

        return $iCnt;
    }

    public function deleteGroup($iId)
    {
        $aRows = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('module_id = \'group\' AND item_id = ' . (int) $iId)
                ->execute('getRows');

        foreach ($aRows as $aRow)
        {
            Phpfox::getService('videochannel.process')->delete($aRow['video_id'], $aRow);
        }

        return true;
    }

    public function getFeedRedirectFeedLike($iId, $iChildId = 0)
    {
        return $this->getFeedRedirect($iChildId);
    }

    public function getNewsFeedFeedLike($aRow)
    {
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
        {
            $aRow['text'] = _p('videochannel.a_href_user_link_full_name_a_likes_their_own_a_href_link_video_a', array(
                        'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                        'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                        'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
                        'link' => $aRow['link']
                            )
            );
        }
        else
        {
            $aRow['text'] = _p('videochannel.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_video_a', array(
                        'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                        'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                        'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
                        'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
                        'link' => $aRow['link']
                            )
            );
        }

        $aRow['icon'] = 'misc/thumb_up.png';

        return $aRow;
    }

    public function getNotificationFeedNotifyLike($aRow)
    {
        return array(
            'message' => _p('videochannel.a_href_user_link_full_name_a_likes_your_a_href_link_video_a', array(
                'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aRow['item_id']))
                    )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aRow['item_id']))
        );
    }

    public function sendLikeEmail($iItemId)
    {
        return _p('videochannel.a_href_user_link_full_name_a_likes_your_a_href_link_video_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
                    'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
                    'link' => Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $iItemId))
                        )
        );
    }

    public function getActivityPointField()
    {
        return array(
            _p('videochannel.videos_in_channels') => 'activity_videochannel'
        );
    }

    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getSqlTitleField()
    {
        return array(
            array(
                'table' => 'channel_video',
                'field' => 'title'
            ),
            array(
                'table' => 'channel_category',
                'field' => 'name'
            )
        );
    }

    public function tabHasItems($iUser)
    {
        $iCount = $this->database()->select('COUNT(user_id)')
                ->from($this->_sTable)
                ->where('user_id = ' . (int) $iUser)
                ->execute('getSlaveField');
        return $iCount > 0;
    }

    public function getActivityFeedFavourite($aItem, $aCallback = null)
    {
        if ($aCallback === null)
        {
            $this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.parent_user_id');
        }

        $aRow = $this->database()->select('v.video_id, v.module_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.image_path, v.image_server_id, l.like_id AS is_liked, vt.text_parsed,ve.*')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'videochannel\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId())
                ->join(Phpfox::getT('channel_video_embed'),'ve','v.video_id = ve.video_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
                ->where('v.video_id = ' . (int) $aItem['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }
        /*
          if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'videochannel.view_browse_videos'))
          {
          return false;
          }
         */

        $aReturn = array(
            'feed_title' => $aRow['title'],
            // 'feed_info' => 'shared a video.',
            'feed_link' => Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']),
//            'feed_content' => Phpfox::getLib('parse.output')-> shorten($aRow['text_parsed'],400,'...'),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/videochanel.png', 'return_url' => true)),
            'time_stamp' => $aItem['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'videochannel',
            'like_type_id' => 'videochannel',
        );
        /*
          if ($aRow['module_id'] == 'pages')
          {
          $aRow['parent_user_id'] = '';
          $aRow['parent_user_name'] = '';
          }
         */
        if (empty($aRow['parent_user_id']))
        {
            $aReturn['feed_info'] = _p('videochannel.favourited_a_video');
        }

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
                    'suffix' => '_120',
                    'max_width' => 120,
                    'max_height' => 120
                        )
                );

//                $aReturn['feed_image'] = $sImage;
            }

            $aReturn['feed_image_onclick'] = '$Core.box(\'videochannel.play\', 700, \'id=' . $aRow['video_id'] . '&amp;feed_id=' . $aItem['feed_id'] . '&amp;popup=true\', \'GET\'); return false;';
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
                $aReturn['feed_info'] = _p('videochannel.favourited_a_video');
                //$aReturn['feed_image_banner'] = $sImage;
                $aReturn['load_block'] = 'videochannel.feed';
            }
            else {
                $iChannelId = $this->database()->select('channel_id')->from(Phpfox::getT('channel_channel_data'))->where('video_id = ' . $aRow['video_id'])->execute('getField');
                $aChannel = Phpfox::getService('videochannel.channel.process')->getChannel($iChannelId);
                $aReturn['feed_info'] = _p('videochannel.favourited_a_video', array(
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

    public function getActivityFeedUnfavourite($aItem, $aCallback = null)
    {
        if ($aCallback === null)
        {
            $this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.parent_user_id');
        }

        $aRow = $this->database()->select('v.video_id, v.module_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.image_path, v.image_server_id, l.like_id AS is_liked, vt.text_parsed')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'videochannel\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId())
                ->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
                ->where('v.video_id = ' . (int) $aItem['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }
        /*
          if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'videochannel.view_browse_videos'))
          {
          return false;
          }
         */
        $aReturn = array(
            'feed_title' => $aRow['title'],
            // 'feed_info' => 'shared a video.',
            'feed_link' => Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'feed_content' => $aRow['text_parsed'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/videochanel.png', 'return_url' => true)),
            'time_stamp' => $aItem['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'videochannel',
            'like_type_id' => 'videochannel'
        );
        /*
          if ($aRow['module_id'] == 'pages')
          {
          $aRow['parent_user_id'] = '';
          $aRow['parent_user_name'] = '';
          }
         */
        if (empty($aRow['parent_user_id']))
        {
            $aReturn['feed_info'] = _p('videochannel.nolonger_favourited_a_video');
        }

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
                    'suffix' => '_120',
                    'max_width' => 120,
                    'max_height' => 120
                        )
                );

                $aReturn['feed_image'] = $sImage;
            }

            $aReturn['feed_image_onclick'] = '$Core.box(\'videochannel.play\', 700, \'id=' . $aRow['video_id'] . '&amp;feed_id=' . $aItem['feed_id'] . '&amp;popup=true\', \'GET\'); return false;';
        }

        return $aReturn;
    }

    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {

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
                ->join(Phpfox::getT('channel_video_embed'),'ve','v.video_id = ve.video_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
                ->where('v.video_id = ' . (int) $aItem['item_id'])
                ->execute('getSlaveRow');
        if (!isset($aRow['video_id']))
        {
            return false;
        }

        if ($bIsChildItem)
        {
            $aItem = array_merge($aRow, $aItem);
        }
        $aReturn = array(
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

//                $aReturn['feed_image_banner'] = $sImage;
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
                //$aReturn['feed_image_banner'] = $sImage;
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

    public function getNotificationFavourite($aNotification)
    {
        $aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.video_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = _p('videochannel.user_name_favourited_gender_own_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('videochannel.user_name_favourited_your_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        else
        {
            $sPhrase = _p('videochannel.user_name_favourited_span_class_drop_data_user_full_name_s_span_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('video_id, title, user_id')
                ->from(Phpfox::getT('channel_video'))
                ->where('video_id = ' . (int) $iItemId)
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'videochannel\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'channel_video', 'video_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                    ->subject(_p('videochannel.full_name_liked_your_video_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
                    ->message(_p('videochannel.full_name_liked_your_video_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
                    ->send();

            Phpfox::getService('notification.process')->add('videochannel_like', $aRow['video_id'], $aRow['user_id']);
        }
    }

    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'videochannel\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'channel_video', 'video_id = ' . (int) $iItemId);
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.video_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = _p('videochannel.user_name_liked_gender_own_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('videochannel.user_name_liked_your_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        else
        {
            $sPhrase = _p('videochannel.user_name_liked_span_class_drop_data_user_full_name_s_span_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()->select('l.video_id, l.title, u.user_id, u.gender, u.user_name, u.full_name')
                ->from(Phpfox::getT('channel_video'), 'l')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
                ->where('l.video_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
        {
            $sPhrase = _p('videochannel.user_name_commented_on_gender_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('videochannel.user_name_commented_on_your_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        else
        {
            $sPhrase = _p('videochannel.user_name_commented_on_span_class_drop_data_user_full_name_s_span_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotification($aNotification)
    {
        $aRow = $this->database()->select('l.video_id, l.title, u.user_id, u.gender, u.user_name, u.full_name')
                ->from(Phpfox::getT('channel_video'), 'l')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
                ->where('l.video_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
        {
            $sPhrase = _p('videochannel.user_name_commented_on_gender_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('videochannel.user_name_commented_on_your_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }
        else
        {
            $sPhrase = _p('videochannel.user_name_commented_on_span_class_drop_data_user_full_name_s_span_video_title', array('user_name' => Phpfox::getService('notification')->getUsers($aNotification), 'full_name' => $aRow['full_name'], 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.video_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['video_id']))
        {
            return false;
        }

        $sPhrase = _p('videochannel.your_video_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
            'no_profile_image' => true
        );
    }

    public function getAjaxProfileController()
    {
        return 'videochannel.index';
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_videochannel',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('channel_video'))->where('in_process = 0 AND view_id = 0 AND item_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
        );
    }

    public function checkFeedShareLink()
    {
        // Do not show upload video on wall.
        return false;
    }

    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.video_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'videochannel\' AS item_type_id, item.image_path AS item_photo, item.image_server_id AS item_photo_server')
                ->from(Phpfox::getT('channel_video'), 'item')
                ->where('item.in_process = 0 AND item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
                ->union();
    }

    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('videochannel', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('video_channel');

        $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['item_photo_server'],
            'file' => $aRow['item_photo'],
            'path' => 'core.url_pic',
            'suffix' => '_120',
            'max_width' => '120',
            'max_height' => '120'
                // 'return_url' => true
                )
        );

        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('video_channel')
        );
    }

    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()->select('v.video_id, v.title, u.user_name, u.full_name')
                ->from(Phpfox::getT('comment'), 'c')
                ->join(Phpfox::getT('channel_video'), 'v', 'v.video_id = c.item_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where('c.comment_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');


        $sPhrase = _p('videochannel.user_name_tagged_you_in_a_comment_in_a_video', array('user_name' => $aRow['full_name']));

        return array(
            'link' => Phpfox::getLib('url')->permalink('videochannel', $aRow['video_id'], $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/videochanel.png', 'return_url' => true))
        );
    }

    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'videochannel.view_browse_videos'))
        {
            return null;
        }


        $aMenus[] = array(
            'phrase' => _p('videochannel.videochannel'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'videochannel/',
            'icon' => 'feed/videochanel.png',
            'landing' => 'videochannel',
            'menu_icon' => 'ico ico-file-movie-o'
        );
        
        return $aMenus;
    }

    public function getPageSubMenu($aPage)
    {
        // Fix bug update Phpfox 350.
        //return array();
        
        $aSubMenu = array();

        if (Phpfox::isAdmin())
        {
            $aSubMenu[] = array(
                'phrase' => _p('videochannel.upload_share_a_video'),
                'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            );

            $aSubMenu[] = array(
                'phrase' => _p('videochannel.add_a_channel'),
                'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            );
        }
        else
        {
            if (Phpfox::getUserParam('videochannel.can_upload_video_on_page'))
            {
                if (Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'videochannel.share_videos'))
                {
                    $aSubMenu[] = array(
                        'phrase' => _p('videochannel.upload_share_a_video'),
                        'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'pages', 'item' => $aPage['page_id']))
                    );
                }
            }
            if (((Phpfox::getUserId() == $aPage['user_id']) || (Phpfox::getUserBy('profile_page_id') == $aPage['page_id'])) && Phpfox::getUserParam('videochannel.can_add_channel_on_page'))
            {
                $aSubMenu[] = array(
                    'phrase' => _p('videochannel.add_a_channel'),
                    'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'pages', 'item' => $aPage['page_id']))
                );
            }
        }
        return $aSubMenu;
    }

    public function getPagePerms()
    {
        $aPerms = array();

        $aPerms['videochannel.share_videos'] = _p('videochannel.who_can_share_videos');
        $aPerms['videochannel.view_browse_videos'] = _p('videochannel.who_can_view_browse_videos');
        //$aPerms['videochannel.add_channels'] = _p('videochannel.who_can_add_channels');

        return $aPerms;
    }

    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'videochannel.view_browse_videos'))
        {
            return false;
        }

        return true;
    }

    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'videochannel.view_browse_videos'))
        {
            return null;
        }


        $aMenus[] = array(
            'phrase' => _p('videochannel.videochannel'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'videochannel/',
            'icon' => 'feed/videochanel.png',
            'landing' => 'videochannel',
            'menu_icon' => 'ico ico-file-movie-o'
        );

        return $aMenus;
    }

    public function getGroupSubMenu($aPage)
    {
        // Fix bug update Phpfox 350.
        //return array();

        $aSubMenu = array();

        if (Phpfox::isAdmin())
        {
            $aSubMenu[] = array(
                'phrase' => _p('videochannel.upload_share_a_video'),
                'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'groups', 'item' => $aPage['page_id']))
            );

            $aSubMenu[] = array(
                'phrase' => _p('videochannel.add_a_channel'),
                'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'groups', 'item' => $aPage['page_id']))
            );
        }
        else
        {
            if (Phpfox::getUserParam('videochannel.can_upload_video_on_page'))
            {
                if (Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'videochannel.share_videos'))
                {
                    $aSubMenu[] = array(
                        'phrase' => _p('videochannel.upload_share_a_video'),
                        'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'groups', 'item' => $aPage['page_id']))
                    );
                }
            }

            if (Phpfox::getUserId() == $aPage['user_id'] && Phpfox::getUserParam('videochannel.can_add_channel_on_page'))
            {
                $aSubMenu[] = array(
                    'phrase' => _p('videochannel.add_a_channel'),
                    'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'groups', 'item' => $aPage['page_id']))
                );
            }
        }
        return $aSubMenu;
    }

    public function getGroupPerms()
    {
        $aPerms = array();

        $aPerms['videochannel.share_videos'] = _p('videochannel.who_can_share_videos');
        $aPerms['videochannel.view_browse_videos'] = _p('videochannel.who_can_view_browse_videos');

        return $aPerms;
    }

    public function canViewGroupSection($iPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($iPage, 'videochannel.view_browse_videos'))
        {
            return false;
        }

        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('videochannel.service_callback__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function getUploadParams() {
        return [
            'label' => _p('select an image'),
            'max_size' => (Phpfox::getUserParam('videochannel.max_size_for_video_photos') === 0 ? null : (Phpfox::getUserParam('videochannel.max_size_for_video_photos') / 1024)),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic'),
            'upload_path' => Phpfox::getParam('core.url_pic'),
            'thumbnail_sizes' => array(120, 480),
            'remove_field_name' => 'remove_logo'
        ];
    }

}

?>
