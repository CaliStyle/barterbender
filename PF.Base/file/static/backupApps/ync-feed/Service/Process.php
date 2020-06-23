<?php

namespace Apps\YNC_Feed\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Module_Feed
 */
class Process extends \Feed_Service_Process
{
    /**
     * @var bool
     */
    private $_bAllowGuest = false;

    /**
     * @var int
     */
    private $_iLastId = 0;

    /**
     * @var array
     */
    private $_aCallback = [];

    /**
     * @var bool
     */
    private $_bIsCallback = false;

    /**
     * @var bool
     */
    private $_bIsNewLoop = false;

    /**
     * @var
     */
    private $_content;

    /**
     * @var int
     */
    private $_iNewLoopFeedId = 0;

    /**
     * @param string $sType
     * @param int $iItemId
     * @param int $iPrivacy
     * @param int $iPrivacyComment
     * @param int $iParentUserId
     * @param null|int $iOwnerUserId
     * @param bool|int $bIsTag
     * @param int $iParentFeedId
     * @param null|string $sParentModuleName
     *
     * @return int
     */
    public function add($sType, $iItemId = 0, $iPrivacy = 0, $iPrivacyComment = 0, $iParentUserId = 0, $iOwnerUserId = null, $bIsTag = 0, $iParentFeedId = 0, $sParentModuleName = null)
    {
        if (defined('NO_TWO_FEEDS_THIS_ACTION')) {
            if (defined('NO_TWO_FEEDS_THIS_ACTION_RAN')) {
                return true;
            } else {
                define('NO_TWO_FEEDS_THIS_ACTION_RAN', true);
            }
        }
        $isApp = false;
        $content = null;
        if (is_array($sType)) {
            $app = $sType;
            $sType = $app['type_id'];
            $isApp = true;
            $content = $app['content'];
            if (isset($app['privacy'])) {
                $iPrivacy = $app['privacy'];
            }

            if (isset($app['parent_user_id'])) {
                $iParentUserId = $app['item_id'];
            }
        }
        if (!empty($this->_content)) {
            $content = $this->_content;
        }
        //Plugin call
        if (($sPlugin = Phpfox_Plugin::get('feed.service_process_add__start'))) {
            eval($sPlugin);
        }

        if (!defined('PHPFOX_FEED_NO_CHECK')) {
            if (!$isApp && ((!Phpfox::isUser() && $this->_bAllowGuest === false) || (defined('PHPFOX_SKIP_FEED') && PHPFOX_SKIP_FEED))) {
                return false;
            }
        }

        if ($iParentUserId === null) {
            $iParentUserId = 0;
        }

        $iNewTimeStamp = PHPFOX_TIME;
        $iNewTimeStampCheck = Phpfox::getLib('date')->mktime(0, 0, 0, date('n', PHPFOX_TIME), date('j', PHPFOX_TIME), date('Y', PHPFOX_TIME));
        $aParentModuleName = explode('_', $sParentModuleName);
        $post_user_id = (defined('FEED_FORCE_USER_ID') ? FEED_FORCE_USER_ID : ($iOwnerUserId === null ? Phpfox::getUserId() : (int)$iOwnerUserId));
        $aInsert = array(
            'privacy' => (int)$iPrivacy,
            'privacy_comment' => (int)$iPrivacyComment,
            'type_id' => $sType,
            'user_id' => $post_user_id,
            'parent_user_id' => $iParentUserId,
            'item_id' => $iItemId,
            'time_stamp' => $iNewTimeStamp,
            'parent_feed_id' => (int)$iParentFeedId,
            'parent_module_id' => ((Phpfox::isModule($aParentModuleName[0]) || Phpfox::isApps($sParentModuleName)) ? $this->database()->escape($sParentModuleName) : null),
            'time_update' => $iNewTimeStamp,
            'content' => $content
        );

        if ($this->_bIsCallback && !isset($this->_aCallback['has_content'])) {
            unset($aInsert['content']);
        }

        if (!defined('PHPFOX_INSTALLER') && !$this->_bIsCallback && !Phpfox::getParam('feed.add_feed_for_comments') && preg_match('/^(.*)_comment$/i', $sType)) {
            $aInsert['feed_reference'] = true;
        }

        if (empty($aInsert['parent_module_id'])) {
            unset($aInsert['parent_module_id']);
        }
        if (defined('PHPFOX_APP_ID')) {
            $aInsert['app_id'] = PHPFOX_APP_ID;
        }

        //Plugin call
        if (($sPlugin = Phpfox_Plugin::get('feed.service_process_add__end'))) {
            eval($sPlugin);
        }

        if ($this->_bIsNewLoop) {
            $aInsert['feed_reference'] = (int)$bIsTag;
            $this->_iNewLoopFeedId = $this->database()->insert(Phpfox::getT('feed'), $aInsert);
        } else {
            $this->_iLastId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed'), $aInsert);
            if ($this->_bIsCallback) {
                storage()->set('feed_callback_' . $this->_iLastId, $this->_aCallback);
            }
            //Loop Feed for main of pages/groups items
            if ($this->_bIsCallback && ($this->_aCallback['module'] == 'pages' || (isset($this->_aCallback['add_to_main_feed']) && $this->_aCallback['add_to_main_feed'])) && !$this->_bIsNewLoop && $iParentUserId > 0) {
                $aUser = $this->database()->select('u.user_id, p.view_id')
                    ->from(Phpfox::getT('user'), 'u')
                    ->join(Phpfox::getT('pages'), 'p', 'p.page_id = u.profile_page_id')
                    ->where('u.profile_page_id = ' . (int)$iParentUserId)
                    ->execute('getSlaveRow');

                if (!$iParentFeedId && defined('PHPFOX_PAGES_IS_PARENT_FEED')) {
                    $iParentFeedId = $this->_iLastId;
                }

                if (!$aUser['view_id']) {
                    $this->_content = $content;
                    if (isset($aUser['user_id']) && Phpfox::getUserId() == $aUser['user_id']) {
                        $this->_bIsNewLoop = true;
                        $this->_bIsCallback = false;
                        $this->_aCallback = array();
                        $iNewLoopId = $this->add($sType, $iItemId, $iPrivacy, $iPrivacyComment, 0, null, 0, $iParentFeedId);
                    } else {
                        $this->_bIsNewLoop = true;
                        $this->_bIsCallback = false;
                        $this->_aCallback = array();
                        $iNewLoopId = $this->add($sType, $iItemId, $iPrivacy, $iPrivacyComment, 0, $iOwnerUserId === null ? Phpfox::getUserId() : $iOwnerUserId, 0, $iParentFeedId);
                    }
                    $this->_content = '';
                    defined('PHPFOX_NEW_FEED_LOOP_ID') || define('PHPFOX_NEW_FEED_LOOP_ID', $this->_iNewLoopFeedId);
                }
            }
            //End loop feed
        }

        if ($sPlugin = Phpfox_Plugin::get('feed.service_process_add__end2')) {
            eval($sPlugin);
        }

        return $this->_iLastId;
    }

    protected function _getPageIdFromMentions($sContent, $iType = 0)
    {
        if ($iType)
            $iCount = preg_match_all('/\[group=(\d+)\].+?\[\/group\]/i', $sContent, $aMatches);
        else $iCount = preg_match_all('/\[page=(\d+)\].+?\[\/page\]/i', $sContent, $aMatches);
        if ($iCount < 1) {
            return array();
        }
        if (is_array($aMatches[1])) {
            return array_filter(array_unique($aMatches[1]),
                function ($arrayEntry) {
                    return is_numeric($arrayEntry);
                }
            );
        }
        return array();
    }

    protected function _getBusinessIdFromMentions($sContent)
    {
        $iCount = preg_match_all('/\[car=(\d+)\].+?\[\/car\]/i', $sContent, $aMatches);
        if ($iCount < 1) {
            return array();
        }
        if (is_array($aMatches[1])) {
            return array_filter(array_unique($aMatches[1]),
                function ($arrayEntry) {
                    return is_numeric($arrayEntry);
                }
            );
        }
        return array();
    }

    protected function _getUserIdFromMentions($sContent)
    {
        $aUserIds = Phpfox::getService('ynfeed.user.process')->getIdFromMentions($sContent);
        if (!empty($aUserIds)) {
            $sUsers = implode(',', $aUserIds);
            $aPerms = $this->database()->select('user_id, user_value')->from(Phpfox::getT('user_privacy'))->where('user_id in (' . $sUsers . ' ) AND user_privacy = \'user.can_i_be_tagged\'')->execute('getSlaveRows');

            $aUserIds = array_filter(array_unique($aUserIds),
                function ($arrayEntry) {
                    return is_numeric($arrayEntry);
                }
            );

            foreach ($aPerms as $aRow) {
                foreach ($aUserIds as $iIndex => $iUserId) {
                    if ($iUserId == $aRow['user_id'] && $aRow['user_value'] == 4) {
                        unset($aUserIds[$iIndex]);
                    }
                }
            }
        }
        return $aUserIds;
    }

    public function getMentions($sContent)
    {
        /*Users*/
        $aUserIds = $this->_getUserIdFromMentions($sContent);

        /*Groups*/
        $aGroupIds = $this->_getPageIdFromMentions($sContent, 1);

        /*Pages*/
        $aPagesIds = $this->_getPageIdFromMentions($sContent, 0);

        $aBusinessIds = $this->_getBusinessIdFromMentions($sContent);

        return [
            'user' => $aUserIds,
            'group' => $aGroupIds,
            'page' => $aPagesIds,
            'car' => $aBusinessIds
        ];
    }

    public function getUsersForMention()
    {
        /*Get friends*/
        $aFriends = Phpfox::getService('friend')->getFromCache();

        /*Get groups & pages*/
        $aProcessedPages = $aProcessedCars = [];
        if (Phpfox::isModule('pages')) {
            $aPages = $this->database()->select(Phpfox::getUserField() . ', p.item_type as page_type')
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('pages'), 'p', 'u.profile_page_id = p.page_id')
                ->where('u.profile_page_id > 0')
                ->order('u.last_activity DESC')
                ->execute('getSlaveRows');

            $aProcessedPages = [];
            foreach ($aPages as $iKey => $aPage) {
                if (!((($aPage['page_type'] == 0) && Phpfox::getService('pages')->hasPerm($aPage['profile_page_id'],
                            'ynfeed.tag_in_feed')) ||
                    (($aPage['page_type'] == 1) && Phpfox::getService('groups')->hasPerm($aPage['profile_page_id'],
                            'ynfeed.tag_in_feed')))) {
                    continue;
                }
                $aPage['full_name'] = html_entity_decode($aPage['full_name'], null, 'UTF-8');
                $aPage['user_profile'] = Phpfox::getService('pages')->getUrl($aPage['profile_page_id'], '');
                $aPage['is_page'] = ($aPage['profile_page_id'] ? true : false);
                $aPage['user_image'] = Phpfox::getLib('image.helper')->display(array(
                        'user' => $aPage,
                        'suffix' => '_50_square',
                        'max_height' => 32,
                        'max_width' => 32,
                        'return_url' => true
                    )
                );
                $aPage['user_image_actual'] = Phpfox::getLib('image.helper')->display(array(
                        'user' => $aPage,
                        'suffix' => '_50_square',
                        'max_height' => 32,
                        'max_width' => 32
                    )
                );
                $aProcessedPages[] = $aPage;
            }
        }
        if (Phpfox::isModule('ynclistingcar')) {
            $aProcessedCars = $this->getCarCache();
        }
        // Current user
        $aUser = Phpfox::getUserBy();
        if (is_array($aUser) && !empty($aUser)) {
            $aUser = Phpfox::getService('ynfeed.user.process')->addMoreInfo($aUser);
            array_push($aFriends, $aUser);
        }

        return array_merge($aProcessedCars, $aProcessedPages, $aFriends);
    }

    public function getCarCache()
    {
        return get_from_cache(['ynfeed.cars'], function () {
            list($iCnt, $aBusinesses) = Phpfox::getService('ynclistingcar')->getBusiness('');
            return array_map(function ($row) {
                if ($row['logo_path'])
                    $row['business_image'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $row['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $row['logo_path'],
                            'ynclistingcar_overridenoimage' => true,
                            'suffix' => '_100',
                            'return_url' => true
                        )
                    );
                else $row['business_image'] = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-listing-car/assets/images/default_ava.png';
                // process for mention
                $row['user_image'] = $row['business_image'];
                $row['full_name'] = $row['name'];
                $row['is_car'] = 1;
                return $row;
            }, $aBusinesses);
        }, 1);
    }

    /**
     * @param array $aVals
     *
     * @return bool|int
     */
    public function addComment($aVals)
    {
        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (empty($aVals['parent_user_id'])) {
            $aVals['parent_user_id'] = 0;
        }

        if (!Phpfox::getService('ban')->checkAutomaticBan($aVals['user_status'])) {
            return false;
        }

        $sStatus = $this->preParse()->prepare($aVals['user_status']);

        $iStatusId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed_comment'), array(
                'user_id' => (int)Phpfox::getUserId(),
                'parent_user_id' => (int)$aVals['parent_user_id'],
                'privacy' => $aVals['privacy'],
                'privacy_comment' => $aVals['privacy_comment'],
                'content' => $sStatus,
                'time_stamp' => PHPFOX_TIME
            )
        );

        if (!defined('PHPFOX_NEW_USER_STATUS_ID')) {
            define('PHPFOX_NEW_USER_STATUS_ID', $iStatusId);
        }

        if ($this->_bIsCallback) {
            if ($sPlugin = Phpfox_Plugin::get('feed.service_process_addcomment__1')) {
                eval($sPlugin);
            }
            $sLink = $this->_aCallback['link'] . 'comment-id_' . $iStatusId . '/';

            if (!empty($this->_aCallback['notification']) && !Phpfox::getUserBy('profile_page_id')) {
                Phpfox::getLib('mail')->to($this->_aCallback['email_user_id'])
                    ->translated(isset($this->_aCallback['mail_translated']) ? $this->_aCallback['mail_translated'] : false)
                    ->subject($this->_aCallback['subject'])
                    ->message(sprintf($this->_aCallback['message'], $sLink))
                    ->notification(($this->_aCallback['notification'] == 'pages_comment' ? 'comment.add_new_comment' : $this->_aCallback['notification']))
                    ->send();

            }

            if (isset($this->_aCallback['add_tag']) && $this->_aCallback['add_tag']) {
                if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                    Phpfox::getService('tag.process')->add($this->_aCallback['feed_id'], $iStatusId, Phpfox::getUserId(), strip_tags($aVals['user_status']), true);
                }
            }

            return Phpfox::getService('ynfeed.process')->add($this->_aCallback['feed_id'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int)$aVals['parent_user_id']);
        }

        $aUser = $this->database()->select('user_name')
            ->from(Phpfox::getT('user'))
            ->where('user_id = ' . (int)$aVals['parent_user_id'])
            ->execute('getSlaveRow');

        $sLink = Phpfox_Url::instance()->makeUrl($aUser['user_name'], array('comment-id' => $iStatusId));

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add((isset($aVals['feed_type']) ? $aVals['feed_type'] : 'feed_comment'), $iStatusId, Phpfox::getUserId(), strip_tags($aVals['user_status']), true);
        }

        /* When a user is tagged it needs to add a special feed */
        if (!isset($aVals['feed_reference']) || empty($aVals['feed_reference'])) {
            Phpfox::getLib('mail')->to($aVals['parent_user_id'])
                ->subject(array('feed.full_name_wrote_a_comment_on_your_wall', array('full_name' => Phpfox::getUserBy('full_name'))))
                ->message(array('feed.full_name_wrote_a_comment_on_your_wall_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink)))
                ->notification('comment.add_new_comment')
                ->send();

            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('feed_comment_profile', $iStatusId, $aVals['parent_user_id']);
            }
            if (isset($aVals['feed_type'])) {
                return Phpfox::getService('ynfeed.process')->add($aVals['feed_type'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int)$aVals['parent_user_id']);
            }
        } else { // This is a special feed
            // Send mail
            return Phpfox::getService('ynfeed.process')->add('feed_comment', $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int)$aVals['parent_user_id'], null, $aVals['feed_reference']);
        }

        if ($sPlugin = Phpfox_Plugin::get('ynfeed.service_process_addcomment__2')) {
            eval($sPlugin);
        }

        if(Phpfox::isAppActive('Core_Activity_Points')) {
            if(!empty($aVals['parent_feed_id'])) {
                Phpfox::getService('activitypoint.process')->updatePoints(Phpfox::getUserId(), 'share_item');
            }
            elseif (!empty($aVals['parent_user_id'])) {
                Phpfox::getService('activitypoint.process')->updatePoints(Phpfox::getUserId(), 'feed_postonotherprofile');
            }
        }

        return Phpfox::getService('ynfeed.process')->add('feed_comment', $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int)$aVals['parent_user_id'], null, 0, (isset($aVals['parent_feed_id']) ? $aVals['parent_feed_id'] : 0), (isset($aVals['parent_module_id']) ? $aVals['parent_module_id'] : null));
    }

    public function deleteFeed($iId, $sModule = null, $iItem = 0)
    {
        $aCallback = null;
        if (!empty($sModule)) {
            if (Phpfox::hasCallback($sModule, 'getFeedDetails')) {
                $aCallback = Phpfox::callback($sModule . '.getFeedDetails', $iItem);
            }
        }

        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->getFeed($iId);
        $sType = '';

        if (!$aFeed && ($cache = storage()->get('feed_callback_' . $iId))) {
            if (in_array($cache->value->module, ['pages', 'groups'])) {
                $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->getFeed($iId, 'pages_');
                $sType = 'v_pages';
            }
        }

        if (!isset($aFeed['feed_id'])) {
            return false;
        }

        if (empty($sType)) {
            $sType = $aFeed['type_id'];
        }

        $iItemId = $aFeed['item_id'];
        if (!$iItemId) {
            $iItemId = $aFeed['feed_id'];
        }

        //Delete all shared items from this item
        $aSharedItems = $this->database()->select('feed_id')
            ->from(':feed')
            ->where('parent_module_id="' . $sType . '" AND parent_feed_id =' . (int)$iItemId)
            ->execute('getSlaveRows');

        if (is_array($aSharedItems) && count($aSharedItems)) {
            foreach ($aSharedItems as $aSharedItem) {
                if (isset($aSharedItem['feed_id'])) {
                    $this->deleteFeed($aSharedItem['feed_id']);
                }
            }
        }

        if ($aFeed['type_id'] == 'photo') {
            Phpfox::callback($aFeed['type_id'] . '.deleteFeedItem', $aFeed['item_id']);
        }

        if ($sPlugin = Phpfox_Plugin::get('feed.service_process_deletefeed')) {
            eval($sPlugin);
        }

        $bCanDelete = false;
        if (Phpfox::getUserParam('feed.can_delete_own_feed') && ($aFeed['user_id'] == Phpfox::getUserId()) || ($aFeed['parent_user_id'] == Phpfox::getUserId())) {
            $bCanDelete = true;
        }

        if (defined('PHPFOX_FEED_CAN_DELETE')) {
            $bCanDelete = true;
        }

        if (Phpfox::getUserParam('feed.can_delete_other_feeds')) {
            $bCanDelete = true;
        }

        if ($bCanDelete === true) {

            if (isset($aCallback['table_prefix'])) {
                $this->database()->delete(Phpfox::getT($aCallback['table_prefix'] . 'feed'), 'feed_id = ' . (int)$iId);
            }

            if ($aFeed['type_id'] == 'feed_comment') {
                $aCore = Phpfox_Request::instance()->getArray('core');
                if (isset($aCore['is_user_profile']) && $aCore['profile_user_id'] != Phpfox::getUserId()) {

                    $this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] . ' AND time_stamp = ' . $aFeed['time_stamp'] . ' AND parent_user_id = ' . $aCore['profile_user_id']);
                } elseif (isset($aCore['is_user_profile']) && $aCore['profile_user_id'] == Phpfox::getUserId()) {
                    $this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . (int)$aFeed['feed_id']);
                }
                $this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] . ' AND time_stamp = ' . $aFeed['time_stamp'] . ' AND parent_user_id = ' . Phpfox::getUserId());
            } else {
                $this->database()->delete(Phpfox::getT('feed'), 'user_id = ' . $aFeed['user_id'] . ' AND time_stamp = ' . $aFeed['time_stamp']);
            }
            if (!(Phpfox::hasCallback($aFeed['type_id'], 'ignoreDeleteLikesAndTagsWithFeed') && Phpfox::callback($aFeed['type_id'] . '.ignoreDeleteLikesAndTagsWithFeed'))) {
                // Delete likes that belonged to this feed
                $this->database()->delete(Phpfox::getT('like'), 'type_id = "' . $aFeed['type_id'] . '" AND item_id = ' . $aFeed['item_id']);

                // Delete tags that belonged to this feed
                $this->database()->delete(Phpfox::getT('tag'), 'category_id = "' . $aFeed['type_id'] . '" AND item_id = ' . $aFeed['item_id']);
            }

            if (in_array($sType, ['photo', 'user_status'])) {
                if ($aFeed['feed_reference'] == 0 && Phpfox::hasCallback($sType, 'deleteFeedItem')) {
                    Phpfox::callback($sType . '.deleteFeedItem', $iItemId, ($aCallback != null ? $aCallback['table_prefix'] : ''));
                }
            } elseif (!empty($sModule) && Phpfox::hasCallback($sModule, 'deleteFeedItem')) {
                Phpfox::callback($sModule . '.deleteFeedItem', [
                    'type_id' => $sType,
                    'item_id' => $iItemId,
                ]);
            }
            
            return true;
        }

        return false;
    }

    /**
     * @param array $aCallback
     *
     * @return $this
     */
    public function callback($aCallback)
    {
        if (isset($aCallback['module'])) {
            $this->_bIsCallback = true;
            $this->_aCallback = $aCallback;
        }

        return $this;
    }

    /**
     * @param $iItemId
     * @param $sItemType
     * @param $iUserId
     * @return bool
     */
    public function removeTag($iItemId, $sItemType, $iUserId)
    {
        db()->delete(Phpfox::getT('ynfeed_feed_map'), "item_id = " . (int)$iItemId . " AND item_type = '" . $sItemType . "' AND user_id = " . $iUserId . " AND parent_user_id = " . Phpfox::getUserId());
        Phpfox::getService('feed.process')->updateTaggedUsers($iItemId, $sItemType);
        return true;
    }
}