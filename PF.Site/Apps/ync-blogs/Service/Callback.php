<?php

namespace Apps\YNC_Blogs\Service;

use Phpfox;
use Phpfox_Plugin;
use Core;
use Phpfox_Component;
use Phpfox_Service;

class Callback extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynblog_blogs');
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationNewItem_Groups($aNotification)
    {
        if (!Phpfox::isAppActive('PHPfox_Groups')) {
            return false;
        }
        $aBlog = Phpfox::getService('ynblog.blog')->getBlog($aNotification['item_id']);
        if (empty($aBlog) || empty($aBlog['item_id']) || ($aBlog['module_id'] != 'groups')) {
            return false;
        }

        $aRow = Phpfox::getService('groups')->getPage($aBlog['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('{{ users }} add a new blog in the group "{{ title }}"', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox::getLib('url')->permalink(\Phpfox_Url::instance()->doRewrite('ynblog'), $aBlog['blog_id'], $aBlog['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $params
     */
    public function saveItem($params)
    {
        if($params['is_save']) {
            Phpfox::getService('ynblog.process')->addSavedBlog($params['user_id'], $params['item_id'], true);
        }
        else {
            Phpfox::getService('ynblog.process')->deleteSavedBlog($params['user_id'], $params['item_id'], true);
        }
    }

    /**
     * @param $params
     * @return bool|string
     */
    public function getLink($params)
    {
        $title = db()->select('title')
                    ->from($this->_sTable)
                    ->where('blog_id = ' . (int)$params['item_id'])
                    ->execute('getSlaveField');
        if(empty($title)) {
            return false;
        }

        return Phpfox::permalink('ynblog', (int)$params['item_id'], $title);
    }

    /**
     * @param $item
     * @return array|bool
     */
    public function getSavedInformation($item)
    {
        if(empty($item['item_id']) || !($blog = Phpfox::getService('ynblog.blog')->getBlog($item['item_id']))) {
            return false;
        }

        $extra = [
            'additional_information' => [
                'value' => $blog['text']
            ],
            'photo' => Phpfox::getService('ynblog.helper')->getImagePath($blog['image_path'], $blog['server_id'], '_240', $blog['is_old_suffix'], true)
        ];

        return $extra;
    }

    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'is_approved = 1 AND post_status = \'public\'';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(blog_id)')
            ->from($this->_sTable)
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'ynblog',
            'total' => $iCnt,
            'icon' => 'ico ico-compose-alt'
        ];
    }

    /**
     * @return array
     */
    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return [
            'phrase' => _p('ynblog'),
            'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('ynblog_blogs'))
                ->where('post_status = \'public\' AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }

    /**
     * @param string $sTag
     * @param array $aConds
     * @param string $sSort
     * @param string $iPage
     * @param string $sLimit
     *
     * @return array
     */
    public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
        $aBlogs = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'blog')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
            ->where($aConds)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aRows = $this->database()->select("blog.*, " . Phpfox::getUserField())
                ->from(Phpfox::getT('blog'), 'blog')
                ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
                ->join(Phpfox::getT('user'), 'u', 'blog.user_id = u.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)
                ->execute('getSlaveRows');

            if (count($aRows)) {
                foreach ($aRows as $aRow) {
                    $aBlogs[$aRow['blog_id']] = $aRow;
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
        return array($iCnt, $aBlogs);
    }

    /**
     * @param array $aUser
     *
     * @return string
     */
    public function getTagLinkProfile($aUser)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettaglinkprofile__start')) ? eval($sPlugin) : false);
        return $this->getTagLink();
    }

    /**
     * @return string
     */
    public function getTagLink()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
        $sExtra = '';
        if (defined('PHPFOX_TAG_PARENT_MODULE')) {
            $sExtra .= PHPFOX_TAG_PARENT_MODULE . '.' . PHPFOX_TAG_PARENT_ID . '.';
        }

        return Phpfox::getLib('url')->makeUrl($sExtra . 'ynblog.tag');
    }

    /**
     * @param array $aConds
     * @param $sSort
     * @return array
     */
    public function getTagSearch($aConds = array(), $sSort)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
        $aRows = $this->database()->select("blog.blog_id AS id")
            ->from($this->_sTable, 'blog')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
            ->where($aConds)
            ->group('blog.blog_id', true)
            ->order($sSort)
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aRows as $aRow) {
            $aSearchIds[] = $aRow['id'];
        }
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
        return $aSearchIds;
    }

    /**
     * @return array
     */
    public function getTagCloud()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_gettagcloud__start')) ? eval($sPlugin) : false);
        return array(
            'link' => 'ynblog',
            'category' => 'ynblog'
        );
    }

    /**
     * @return string
     */
    public function getTagType()
    {
        return 'ynblog';
    }

    /**
     * @param int $iId
     * @param int $iChild
     *
     * @return bool|string
     */
    public function getFeedRedirect($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);

        $aBlog = $this->database()->select('b.blog_id, b.title')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('ynblog', $aBlog['blog_id'], $aBlog['title']);
    }

    /**
     * @param $iId
     * @param null $iUserId
     */
    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);

        $this->database()->insert(Phpfox::getT('track'), [
            'type_id' => 'ynblog',
            'item_id' => (int)$iId,
            'ip_address' => Phpfox::getUserBy('last_ip_address'),
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME
        ]);
    }

    /**
     * @param int $iId
     * @param int $iUserId
     *
     * @return bool|array
     */
    public function getLatestTrackUsers($iId, $iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getlatesttrackusers__start')) ? eval($sPlugin) : false);

        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('track'), 'track')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
            ->where('track.item_id = ' . (int)$iId . ' AND track.user_id != ' . (int)$iUserId . ' AND track.type_id="ynblog"')
            ->order('track.time_stamp DESC')
            ->limit(0, 6)
            ->execute('getSlaveRows');

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getlatesttrackusers__end')) ? eval($sPlugin) : false);
        return (count($aRows) ? $aRows : false);
    }

    /**
     * @return string
     */
    public function getAjaxCommentVar()
    {
        return 'yn_advblog_comment';
    }

    /**
     * @param $aVals
     * @param null $iUserId
     * @param null $sUserName
     */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aBlog = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null || empty($aBlog['blog_id'])) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
        Phpfox::getService('ynblog.cache_remove')->advancedblog($aVals['item_id']);
        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('ynblog_blogs', 'total_comment', 'blog_id', $aVals['item_id']);
            $this->database()->update($this->_sTable, array('latest_comment' => PHPFOX_TIME), 'blog_id = ' . (int)$aVals['item_id']);

            $this->cache()->remove('ynblog_recent_comment', 'substr');
            $this->cache()->remove('ynblog_most_discussed', 'substr');
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ynblog', $aBlog['blog_id'], $aBlog['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aBlog['user_id'],
                'item_id' => $aBlog['blog_id'],
                'owner_subject' => _p('full_name_commented_on_your_blog_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aBlog['title'])),
                'owner_message' => _p('full_name_commented_on_your_blog_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aBlog['title'])),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'comment_ynblog',
                'mass_id' => 'ynblog',
                'mass_subject' => (Phpfox::getUserId() == $aBlog['user_id'] ? _p('full_name_commented_on_gender_blog', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aBlog['gender'], 1))) : _p('full_name_commented_on_blog_full_name_s_blog', array('full_name' => Phpfox::getUserBy('full_name'), 'blog_full_name' => $aBlog['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aBlog['user_id'] ? _p('full_name_commented_on_gender_blog_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aBlog['gender'], 1), 'link' => $sLink, 'title' => $aBlog['title'])) : _p('full_name_commented_on_blog_full_name_s_blog_message', array('full_name' => Phpfox::getUserBy('full_name'), 'blog_full_name' => $aBlog['full_name'], 'link' => $sLink, 'title' => $aBlog['title'])))
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    /**
     * @param $iId
     */
    public function deleteComment($iId)
    {
        $this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'blog_id = ' . (int)$iId);
        Phpfox::getService('ynblog.cache_remove')->advancedblog($iId);
    }

    /**
     * @return array
     */
    public function updateCounterList()
    {
        $aList = [];

        $aList[] = [
            'name' => _p('users_ynblog_count'),
            'id' => 'ynblog-total'
        ];

        $aList[] = [
            'name' => _p('update_tags_ynblogs'),
            'id' => 'ynblog-tag-update'
        ];

        $aList[] = [
            'name' => _p('update_users_activity_ynblog_points'),
            'id' => 'ynblog-activity'
        ];

        return $aList;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return int
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);

        if ($iId == 'ynblog-total') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(b.blog_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ynblog_blogs'), 'b', 'b.module_id = \'ynblog\' AND b.user_id = u.user_id AND b.is_approved = 1 AND b.post_status = \'public\'')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_field'), array('total_ynblog' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }
        } elseif ($iId == 'ynblog-activity') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $this->database()->select('u.user_id, u.user_group_id, COUNT(oc.blog_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ynblog_blogs'), 'oc', 'oc.user_id = u.user_id')
                ->group('u.user_id')
                ->union();

            $aRows = $this->database()->select('m.user_id, u.user_group_id, m.activity_ynblog, m.activity_points, m.activity_total, u.total_items')
                ->unionFrom('u')
                ->join(Phpfox::getT('user_activity'), 'm', 'u.user_id = m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $iPointsPerBlog = Phpfox::getService('user.group.setting')->getGroupParam($aRow['user_group_id'], 'ynblog.points_ynblog');

                $aUpdate = array(
                    'activity_points' => (($aRow['activity_points'] - ($aRow['activity_ynblog'] * $iPointsPerBlog)) + ($aRow['total_items'] * $iPointsPerBlog)),
                    'activity_total' => ($aRow['activity_total'] - $aRow['activity_ynblog'] + $aRow['total_items']),
                    'activity_ynblog' => $aRow['total_items']);
                $this->database()->update(Phpfox::getT('user_activity'), $aUpdate, 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('tag'))
            ->where('category_id = \'ynblog\'')
            ->execute('getSlaveField');

        $aRows = $this->database()->select('m.tag_id, oc.blog_id AS tag_item_id')
            ->from(Phpfox::getT('tag'), 'm')
            ->where('m.category_id = \'page_id\'')
            ->leftJoin(Phpfox::getT('ynblog_blogs'), 'oc', 'oc.blog_id = m.item_id')
            ->limit($iPage, $iPageLimit, $iCnt)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            if (empty($aRow['tag_item_id'])) {
                $this->database()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
            }
        }

        return $iCnt;
    }

    /**
     * @param $iId
     * @return array|int|string
     */
    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('blog_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            \Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    /**
     * @param int $iId
     *
     * @return string
     */
    public function getRssTitle($iId)
    {
        $aRow = $this->database()->select('title')
            ->from($this->_sTable)
            ->where('blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        return 'Comments on: ' . $aRow['title'];
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @return string
     */
    public function getCommentItemName()
    {
        return 'ynblog';
    }

    /**
     * @param string $sAction
     * @param int $iId
     */
    public function processCommentModeration($sAction, $iId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_processcommentmoderation__start')) ? eval($sPlugin) : false);
        // Is this comment approved?
        if ($sAction == 'approve') {
            // Update the blog count
            Phpfox::getService('ynblog.process')->updateCounter($iId);

            // Get the blogs details so we can add it to our news feed
            $aBlog = $this->database()->select('b.blog_id, b.user_id, b.title, b.title_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')
                ->from($this->_sTable, 'b')
                ->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'ynblog\' AND c.item_id = b.blog_id')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->where('b.blog_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            // Add to news feed
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_ynblog', $aBlog['blog_id'], $aBlog['text_parsed'], $aBlog['comment_user_id'], $aBlog['user_id'], $aBlog['comment_id']) : null);

            // Send the user an email
            $sLink = Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'), [
                'blog', $aBlog['title_url']
            ]);

            Phpfox::getLib('mail')->to($aBlog['comment_user_id'])
                ->subject(['comment.full_name_approved_your_comment_on_site_title', [
                    'full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title')
                ]
                ])->message([
                    'comment.full_name_approved_your_comment_on_site_title_message', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink
                    ]
                ])->notification('comment.approve_new_comment')->send();
        }
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_processcommentmoderation__end')) ? eval($sPlugin) : false);
    }

    /**
     * @param string $sQuery
     * @param bool $bIsTagSearch
     *
     * @return array|null
     */
    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = 'b.is_approved = 1 AND b.privacy = 1 AND b.post_status = \'public\'';
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (b.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR b.text LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
        }

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = b.blog_id AND tag.category_id = \'ynblog\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'b')
            ->where($sCondition)
            ->execute('getSlaveField');

        if ($bIsTagSearch == true) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = b.blog_id AND tag.category_id = \'ynblog\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('b.blog_id');
        }

        $aRows = $this->database()->select('b.title, b.title_url, b.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('b.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('search_blogs');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div><input type="button" value="' . _p('view_more_blogs') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('blog', array('tag', $sQuery)) . '\';" /></div>';
            } else {
                $aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('ynblog') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('view_more_blogs') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('ynblog', $aRow['title_url'])),
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
                    'extra_info' => _p('blog_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox::getLib('url')->makeUrl('ynblog'),
                            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
                            'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * @param string $sSearch
     */
    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.blog_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'ynblog\' AS item_type_id, item.image_path AS item_photo, 0 AS item_photo_server')
            ->from(Phpfox::getT('ynblog_blogs'), 'item')
            ->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 AND item.privacy = 0 AND item.post_status = \'public\'')
            ->union();
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('ynblog', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('ynblog');
        $aInfo['item_display_photo'] = '<img style="" src="' . Phpfox::getService('ynblog.helper')->getImagePath($aRow['item_photo'], $aRow['item_photo_server'], '_240', $aRow['is_old_suffix']) . '" class="_image_120 image_deferred  built has_image">';
        return $aInfo;
    }

    /**
     * @return array
     */
    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('ynblog')
        );
    }

    /**
     * @return array
     */
    public function getGlobalPrivacySettings()
    {
        return [
            'ynblog.default_privacy_setting' => [
                'phrase' => _p('advanced_blogs'),
                'icon_class' => 'ico ico-file-text-alt-o'
            ]
        ];
    }

    /**
     * @param $iItemId
     * @return array
     */
    public function getFeedDetails($iItemId)
    {
        return array();
    }

    /**
     *
     */
    public function canShareItemOnFeed()
    {
    }

    /**
     * @param $aItem
     * @param null $aCallback
     * @param bool $bIsChildItem
     * @return array
     */
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (!Phpfox::getUserParam('yn_advblog_view')) {
            return false;
        }

        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynblog\' AND l.item_id = b.blog_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = b.user_id');
        }

        $aRow = $this->database()->select('b.user_id, b.blog_id, b.title, b.time_stamp, b.total_comment, b.total_view, b.privacy, b.total_like, b.text, b.module_id, b.item_id, b.image_path, b.server_id')
            ->from(Phpfox::getT('ynblog_blogs'), 'b')
            ->where('b.blog_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        if ($bIsChildItem) {
            $aItem = array_merge($aRow, $aItem);
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'yn_advblog_view'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'yn_advblog_view'))
            || ($aRow['module_id'] && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'], 'canShareOnMainFeed') && !Phpfox::callback($aRow['module_id'] . '.canShareOnMainFeed', $aRow['item_id'], 'yn_advblog_view', $bIsChildItem))
        ) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        $aRow['group_id'] = $aRow['item_id'];
        $aRow['item_id'] = $aRow['blog_id'];

        $aRow['is_in_feed'] = true;

        $aRow['is_saved'] = Phpfox::getService('ynblog.blog')->findSavedBlogId(Phpfox::getUserId(), $aRow['blog_id']);

        if (!empty($aRow['image_path'])) {
            if(filter_var($aRow['image_path'], FILTER_VALIDATE_URL)) {
                $sImage = $aRow['image_path'];
            }
            else {
                $sImage = Phpfox::getLib('image.helper')->display(
                    [
                        'server_id' => $aRow['server_id'],
                        'path' => 'core.url_pic',
                        'file' => 'ynadvancedblog/' . $aRow['image_path'],
                        'suffix' => '_500',
                        'return_url' => true
                    ]
                );
            }
        } else {
            $sImage = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/assets/image/blog_photo_default.png';
        }

        $sLink = Phpfox::permalink('ynblog', $aRow['blog_id'], $aRow['title']);

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'privacy' => $aRow['privacy'],
            'feed_info' => _p('shared_a_blog').(' <a href="'.$sLink.'">'. _p('ynblog_a_blog').'</a>'),
            'feed_link' => Phpfox::permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/blog.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ynblog',
            'like_type_id' => 'ynblog',
            'custom_data_cache' => $aRow,
            'feed_content' => nl2br(strip_tags($aRow['text'])),
            'load_block' => 'ynblog.feed_item'
        );

        $aReturn['type_id'] = 'ynblog';

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        $aCategory = Phpfox::getService('ynblog.category')->getCategoryByBlogId($aRow['blog_id'], 'parent_id ASC');

        Phpfox::getLib('template')->assign('aBlog', $aRow);

        Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], [$aRow,
            'sLink' => $sLink,
            'aCategory' => $aCategory
        ]);

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['group_id'])
                ->execute('getSlaveRow');

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        return $aReturn;
    }

    /**
     * @param $iItemId
     * @param bool $bDoNotSendEmail
     * @return bool|null
     */
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('blog_id, title, user_id')
            ->from(Phpfox::getT('ynblog_blogs'))
            ->where('blog_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }
        Phpfox::getService('ynblog.cache_remove')->advancedblog($aRow['blog_id']);
        $this->database()->updateCount('like', 'type_id = \'ynblog\' AND item_id = ' . (int)$iItemId, 'total_like', 'ynblog_blogs', 'blog_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('ynblog', $aRow['blog_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array('full_name_liked_your_blog_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
                ->message(array('full_name_liked_your_blog_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ynblog_like', $aRow['blog_id'], $aRow['user_id']);
        }
        return null;
    }

    /**
     * @param int $iItemId
     */
    public function deleteLike($iItemId)
    {
        Phpfox::getService('ynblog.cache_remove')->advancedblog($iItemId);
        $this->database()->updateCount('like', 'type_id = \'ynblog\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ynblog_blogs', 'blog_id = ' . (int)$iItemId);
    }

    /**
     * @param array $aRow
     * @param null $iUserId
     *
     * @return array
     */
    public function getNewsFeed($aRow, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);

        $oUrl = Phpfox::getLib('url');

        $aRow['text'] = _p('owner_full_name_added_a_new_blog_a_href_title_link_title_a',
            array(
                'owner_full_name' => $aRow['owner_full_name'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link']
            )
        );

        $aRow['icon'] = 'module/blog.png';
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'ynblog';

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);

        return $aRow;
    }

    /**
     * @param array $aRow
     * @param null $iUserId
     *
     * @return array
     */
    public function getCommentNewsFeed($aRow, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');

        if ($aRow['owner_user_id'] == $aRow['item_user_id']) {
            $aRow['text'] = _p('user_added_a_new_comment_on_their_own_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id')) {
            $aRow['text'] = _p('user_added_a_new_comment_on_your_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('user_name_added_a_new_comment_on_item_user_name_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'item_user_name' => $aRow['viewer_full_name'],
                    'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
                )
            );
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getCommentNotification($aNotification)
    {
        $aRow = Phpfox::getService('ynblog.blog')->getBlog(intval($aNotification['item_id']));

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = _p('users_commented_on_gender_blog_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array
     */
    public function getCommentNotificationTag($aNotification)
    {
        $aRow = db()->select('b.blog_id, b.title, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join($this->_sTable, 'b', 'b.blog_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_blog', ['user_name' => $aRow['full_name']]);

        return [
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('users_liked_gender_own_blog_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_blog_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationNewblog($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sLink = Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('full_name_has_just_written_a_new_blog_blog_name', array('full_name' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_blog_name_is_approved_by_sender', array('blog_name' => $sTitle, 'sender' => $sUsers));


        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationDenied($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_blog_name_is_denied_by_sender', array('blog_name' => $sTitle, 'sender' => $sUsers));


        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationBlogfeature($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_blog_name_is_featured_by_sender', array('blog_name' => $sTitle, 'sender' => $sUsers));


        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationFavoriteblog($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_favorited_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_favorited_span_class_drop_data_user_row_full_name_s_span_blog_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynblog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param $aUser
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_ynblog'])) {
                return false;
            }

            if (isset($aUser['total_ynblog']) && (int)$aUser['total_ynblog'] === 0) {
                return false;
            }
        }

        $aSubMenu = [];

        if ($aUser['user_id'] == Phpfox::getUserId() && $this->request()->get('req2') == 'ynblog') {
            $aSubMenu[] = [
                'phrase' => _p('drafts'),
                'url' => Phpfox::getLib('url')->makeUrl('profile.blog.view_draft'),
                'total' => Phpfox::getService('ynblog.blog')->getTotalDrafts($aUser['user_id'])
            ];
        }

        $aMenus[] = [
            'phrase' => _p('ynblog'),
            'url' => 'profile.ynblog',
            'total' => (int)(isset($aUser['total_ynblog']) ? $aUser['total_ynblog'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/blog.png',
            'icon_class' => 'ico ico-file-text-alt-o'
        ];

        return $aMenus;
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        if(!Phpfox::getUserParam('ynblog.yn_advblog_view')) {
            return [];
        }

        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        return [
            _p('ynblog') => $aUser['activity_ynblog']
        ];
    }

    /**
     * @param $iUserId
     * @return array
     */
    public function getTotalItemCount($iUserId)
    {
        return [
            'field' => 'total_ynblog',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('ynblog_blogs'))
                ->where('user_id = ' . (int)$iUserId . ' AND is_approved = 1 AND post_status = \'public\' AND item_id = 0')
                ->execute('getSlaveField')
        ];
    }

    /**
     * @param int $iId
     * @param string $sName
     *
     * @return string
     */
    public function getItemName($iId, $sName)
    {
        return _p('a_href_link_on_name_s_blog_a', array('link' => Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
    }

    /**
     * @return string
     */
    public function getProfileLink()
    {
        return 'profile.ynblog';
    }

    /*
     *  Integrate with Pages
     */
    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [];

        $aPerms['ynblog.share_ynblogs'] = _p('who_can_share_ynblogs');
        $aPerms['ynblog.view_browse_ynblogs'] = _p('who_can_view_ynblogs');

        return $aPerms;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ynblog.view_browse_ynblogs') || !user('yn_advblog_add_blog')) {
            return null;
        }

        $sCustomUrl = Phpfox::getService('ynblog.helper')->getCustomURL();

        $aMenus[] = [
            'phrase' => _p('ynblog'),
            'url' => Phpfox::getService('pages')
                    ->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . $sCustomUrl . '/',
            'icon' => 'module/blog.png',
            'menu_icon' => 'ico ico-file-text-alt-o',
            'landing' => 'ynblog'
        ];

        return $aMenus;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ynblog.share_ynblogs')) {
            return null;
        }

        return [
            [
                'phrase' => _p('write_new'),
                'url' => Phpfox::getLib('url')->makeUrl('ynblog.add', [
                    'module' => 'pages',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /*
     *  Integrate with Pages
     */
    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = [];

        $aPerms['ynblog.share_ynblogs'] = _p('who_can_share_ynblogs');
        $aPerms['ynblog.view_browse_ynblogs'] = _p('who_can_view_ynblogs');

        return $aPerms;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'ynblog.view_browse_blogs')) {
            return null;
        }

        $sCustomUrl = Phpfox::getService('ynblog.helper')->getCustomURL();

        $aMenus[] = [
            'phrase' => _p('ynblog'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . $sCustomUrl . '/',
            'icon' => 'module/blog.png',
            'menu_icon' => 'ico ico-file-text-alt-o',
            'landing' => 'ynblog'
        ];

        return $aMenus;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getGroupSubMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'ynblog.share_ynblogs')) {
            return null;
        }

        return [
            [
                'phrase' => _p('write_new'),
                'url' => Phpfox::getLib('url')->makeUrl('ynblog.add', [
                    'module' => 'groups',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @return array
     */
    public function getActivityPointField()
    {
        return [
            _p('ynblog') => 'activity_ynblog'
        ];
    }

    /**
     * @return array
     */
    public function pendingApproval()
    {
        return [
            'phrase' => _p('ynblog'),
            'value' => Phpfox::getService('ynblog.blog')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('ynblog', ['view' => 'pending'])
        ];
    }

    /**
     * @return array
     */
    public function getAttachmentField()
    {
        return [
            'ynblog_blogs',
            'blog_id'
        ];
    }

    /**
     * @return array
     */
    public function getUploadParams() {
        $iMaxFileSize = setting('yn_advblog_max_file_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        return [
            'label' => _p('featured_image'),
            'type_description' => _p('recommend_file_size', ['file_size' => Phpfox::getParam('ynblog.yn_advblog_max_file_size')]),
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynadvancedblog' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynadvancedblog' . PHPFOX_DS,
            'thumbnail_sizes' => Phpfox::getParam('ynblog.thumbnail_sizes'),
            'remove_field_name' => 'remove_logo',
            'no_square' => true
        ];
    }
}
