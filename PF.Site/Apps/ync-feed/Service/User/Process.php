<?php

namespace Apps\YNC_Feed\Service\User;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Process extends \User_Service_Process
{
    private $_iStatusId = 0;

    public function updateStatus($aVals)
    {
        if (isset($aVals['feed_id']) && $aVals['feed_id']) {
            return $this->editStatus($aVals['feed_id'], $aVals);
        }
        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            if (!isset($aVals['no_check_empty_user_status']) || empty($aVals['no_check_empty_user_status'])) {
                return Phpfox_Error::set(_p('add_some_text_to_share'));
            }
        }

        if (!Phpfox::getService('ban')->checkAutomaticBan($aVals['user_status'])) {
            return false;
        }

        $sStatus = $this->preParse()->prepare($aVals['user_status']);
        //Don't check spam if share item
        if (!defined('PHPFOX_INSTALLER') && (!isset($aVals['no_check_empty_user_status']) || empty($aVals['no_check_empty_user_status']))) {
            $aUpdates = $this->database()->select('content')
                ->from(Phpfox::getT('user_status'))
                ->where('user_id = ' . (int)Phpfox::getUserId())
                ->limit(Phpfox::getParam('user.check_status_updates'))
                ->order('time_stamp DESC')
                ->execute('getSlaveRows');

            $iReplications = 0;
            foreach ($aUpdates as $aUpdate) {
                if ($aUpdate['content'] == $sStatus) {
                    $iReplications++;
                }
            }
            if ($iReplications > 0) {
                return Phpfox_Error::set(_p('you_have_already_added_this_recently_try_adding_something_else'));
            }
        }

        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $aInsert = array(
            'user_id' => (int)Phpfox::getUserId(),
            'privacy' => $aVals['privacy'],
            'privacy_comment' => $aVals['privacy_comment'],
            'content' => $sStatus,
            'time_stamp' => PHPFOX_TIME
        );

        if (isset($aVals['location']) && isset($aVals['location']['latlng']) && !empty($aVals['location']['latlng'])) {
            $aMatch = explode(',', $aVals['location']['latlng']);
            $aMatch['latitude'] = floatval($aMatch[0]);
            $aMatch['longitude'] = floatval($aMatch[1]);
            $aInsert['location_latlng'] = json_encode(array(
                'latitude' => $aMatch['latitude'],
                'longitude' => $aMatch['longitude']
            ));
        }

        if (isset($aInsert['location_latlng']) && !empty($aInsert['location_latlng']) && isset($aVals['location']) && isset($aVals['location']['name']) && !empty($aVals['location']['name'])) {
            $aInsert['location_name'] = Phpfox::getLib('parse.input')->clean($aVals['location']['name']);
        }

        $iStatusId = $this->database()->insert(Phpfox::getT('user_status'), $aInsert);
        $this->_iStatusId = $iStatusId;

        if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('user_status', $iStatusId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        /* Old notification call */
        /* Phpfox::getService('ynfeed.user.process')->notifyTagged($sStatus, $iStatusId, 'status'); */

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('user_status', $iStatusId, Phpfox::getUserId(), strip_tags($sStatus), true);
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus')) ? eval($sPlugin) : false);
        $iReturnId = Phpfox::getService('ynfeed.process')->add('user_status', $iStatusId, $aVals['privacy'],
            $aVals['privacy_comment'], 0, null, 0, (isset($aVals['parent_feed_id']) ? $aVals['parent_feed_id'] : 0),
            (isset($aVals['parent_module_id']) ? $aVals['parent_module_id'] : null));

        if (Phpfox::isAppActive('Core_Activity_Points')) {
            Phpfox::getService('activitypoint.process')->updatePoints(Phpfox::getUserId(), !empty($aVals['no_check_empty_user_status']) ? 'share_item' : 'feed_postonwall');
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus_end')) ? eval($sPlugin) : false);

        return $iReturnId;
    }

    public function getUserType($iUserId)
    {
        if(db()->tableExists(Phpfox::getT('pages'))) {
            $aUser = db()->select('p.item_type as page_type,' . Phpfox::getUserField('u'))
                ->from(Phpfox::getT('user'), 'u')
                ->join(Phpfox::getT('pages'), 'p', 'p.page_id = u.profile_page_id')
                ->where('u.profile_page_id > 0 AND u.user_id = ' . (int)$iUserId)
                ->execute('getSlaveRow');
            if ($aUser && isset($aUser['page_type'])) {
                if ($aUser['page_type']) {
                    return 'group';
                }
                return 'page';
            }
        }
        return 'user';
    }

    public function getUsersById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }

        $aUsers = db()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u')
            ->where("u.user_id IN (" . $ids . ")")
            ->execute('getSlaveRows');

        foreach ($aUsers as $iKey => $aUser) {
            $aUsers[$iKey]['user_profile'] = ($aUser['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aUser['profile_page_id'],
                '', $aUser['user_name']) : Phpfox_Url::instance()->makeUrl($aUser['user_name']));
        }
        return $aUsers;
    }

    public function editStatus($iFeedId, $aVals)
    {
        //Get current user status information
        $aCallback = [];
        if (isset($aVals['callback_module'])) {
            $aCallback['module'] = $aVals['callback_module'];
            $aCallback['table_prefix'] = $aVals['callback_module'] . '_';
        }
        if (isset($aVals['callback_item_id'])) {
            $aCallback['item_id'] = $aVals['callback_item_id'];
        }
        $aStatusFeed = Phpfox::getService('ynfeed')->getUserStatusFeed($aCallback, $iFeedId);
        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            if (!isset($aVals['no_check_empty_user_status']) || empty($aVals['no_check_empty_user_status'])) {
                return Phpfox_Error::set(_p('add_some_text_to_share'));
            }
        }

        if (!Phpfox::getService('ban')->checkAutomaticBan($aVals['user_status'])) {
            return false;
        }

        $sStatus = $this->preParse()->prepare($aVals['user_status']);

        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $aUpdate = array(
            'privacy' => $aVals['privacy'],
            'privacy_comment' => $aVals['privacy_comment'],
            'content' => $sStatus,
        );
        if (isset($aVals['location']) && isset($aVals['location']['latlng']) && !empty($aVals['location']['latlng'])) {
            $aMatch = explode(',', $aVals['location']['latlng']);
            $aMatch['latitude'] = floatval($aMatch[0]);
            $aMatch['longitude'] = floatval($aMatch[1]);
            $aUpdate['location_latlng'] = json_encode(array(
                'latitude' => $aMatch['latitude'],
                'longitude' => $aMatch['longitude']
            ));
        } else {
            $aUpdate['location_latlng'] = '';
        }

        if (isset($aUpdate['location_latlng']) && !empty($aUpdate['location_latlng']) && isset($aVals['location']) && isset($aVals['location']['name']) && !empty($aVals['location']['name'])) {
            $aUpdate['location_name'] = Phpfox::getLib('parse.input')->clean($aVals['location']['name']);
        } else {
            $aUpdate['location_name'] = '';
        }
        $this->database()->update(Phpfox::getT('user_status'), $aUpdate, 'status_id=' . (int)$aStatusFeed['item_id']);
        $this->_iStatusId = $iStatusId = (int)$aStatusFeed['item_id'];

        if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('user_status', $iStatusId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []));
        }

        $oldMentions = Phpfox::getService('user.process')->getIdFromMentions($aStatusFeed['feed_status'], true, false);
        $oldTagged = Phpfox::getService('feed')->getTaggedUserIds($iStatusId, $aVals['type_id']);

        Phpfox::getService('user.process')->notifyTaggedInFeed($aVals['type_id'], $sStatus, $iStatusId, $aStatusFeed['user_id'], $iFeedId, $aVals['tagged'], $aVals['privacy'], (isset($aStatusFeed['parent_user_id']) ? (int)$aStatusFeed['parent_user_id'] : 0), $oldTagged, $oldMentions);

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->deleteForItem(Phpfox::getUserId(), $iStatusId, 'user_status');
            Phpfox::getService('tag.process')->add('user_status', $iStatusId, Phpfox::getUserId(), strip_tags($sStatus), true);
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus')) ? eval($sPlugin) : false);

        $iReturnId = Phpfox::getService('ynfeed.process')->update('user_status', $iStatusId, $aVals['privacy'],
            $aVals['privacy_comment']);

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus_end')) ? eval($sPlugin) : false);
        $this->cache()->remove('feed_status_' . $iFeedId);
        return $iReturnId;
    }

    public function addMoreInfo($aUser)
    {
        $aUser['user_profile'] = Phpfox_Url::instance()->makeUrl($aUser['user_name']);
        $aUser['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aUser['full_name'], 20), null,
            'UTF-8');
        $aUser['user_profile'] = ($aUser['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aUser['profile_page_id'],
            '', $aUser['user_name']) : Phpfox_Url::instance()->makeUrl($aUser['user_name']));
        $aUser['is_page'] = ($aUser['profile_page_id'] ? true : false);
        $aUser['user_image'] = Phpfox::getLib('image.helper')->display(array(
                'user' => $aUser,
                'suffix' => '_50_square',
                'max_height' => 32,
                'max_width' => 32,
                'return_url' => true
            )
        );
        $aUser['user_image_actual'] = Phpfox::getLib('image.helper')->display(array(
                'user' => $aUser,
                'suffix' => '_50_square',
                'max_height' => 32,
                'max_width' => 32
            )
        );
        $aUser['has_image'] = isset($aUser['user_image']) && $aUser['user_image'];
        return $aUser;
    }
}