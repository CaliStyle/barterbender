<?php

namespace Apps\YNC_StatusBg\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Error;
use Phpfox_Mail;
use Phpfox_Parse_Output;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

class Process extends Phpfox_Service
{
    private $_sBTable;
    private $_sSBTable;
    private $_aLanguages;
    private $_iStatusId = 0;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncstatusbg_collections');
        $this->_sBTable = Phpfox::getT('yncstatusbg_backgrounds');
        $this->_sSBTable = Phpfox::getT('yncstatusbg_status_background');
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }

    public function add($aVals, $sName = 'title', $bIsEdit = false)
    {
        if (isset($aVals[$sName]) && \Core\Lib::phrase()->isPhrase($aVals[$sName])) {
            $finalPhrase = $aVals[$sName];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, $sName);
        }
        if (!$finalPhrase || (!$aVals['time_stamp'] && !$bIsEdit)) {
            return false;
        }
        if ($bIsEdit) {
            $aCollection = Phpfox::getService('yncstatusbg')->getForEdit($aVals['id']);
            if (!$aCollection) {
                return Phpfox_Error::set(_p('collection_you_are_looking_for_does_not_exists'));
            }
        }

        if ($aVals['is_default']) {
            db()->update($this->_sTable, ['is_default' => 0, 'is_active' => 0], 'is_default = 1 AND is_active = 1');
        } elseif ($aVals['is_active']) {
            db()->update($this->_sTable, ['is_active' => 0], 'is_default = 0 AND is_active = 1');
        }
        $aInsert = [
            'title' => $finalPhrase,
            'is_active' => $aVals['is_active'],
            'is_default' => $aVals['is_default'],

        ];

        if ($bIsEdit) {
            $iId = $aVals['id'];
            db()->update($this->_sTable, $aInsert, 'collection_id = ' . (int)$iId);
        } else {
            //Add all image of this collection
            $iTotalBg = db()->select('COUNT(*)')
                ->from($this->_sBTable)
                ->where('time_stamp = ' . (int)$aVals['time_stamp'])
                ->execute('getField');
            $aInsert['total_background'] = $iTotalBg;
            $aInsert['view_id'] = 0;
            $aInsert['time_stamp'] = PHPFOX_TIME;
            $iId = db()->insert($this->_sTable, $aInsert);
            if ($iId && $iTotalBg) {
                db()->update($this->_sBTable, ['collection_id' => $iId], 'time_stamp = ' . (int)$aVals['time_stamp']);
                $iFirstBgId = db()->select('background_id')->from($this->_sBTable)->where('collection_id =' . $iId)->order('ordering ASC, background_id ASC')->execute('getField');
                db()->update($this->_sTable, ['main_image_id' => $iFirstBgId], 'collection_id = ' . $iId);
            }
        }
        $this->cache()->removeGroup('yncstatusbg');
        return $iId;
    }

    /**
     * Update phrase when edit a category
     *
     * @param array $aVals
     * @param string $sName
     */
    protected function updatePhrase($aVals, $sName = 'title')
    {
        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $name = $aVals[$sName . '_' . $aLanguage['language_id']];
                Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals[$sName],
                    $name);
            }
        }
    }

    /**
     * Add a new phrase for category
     *
     * @param array $aVals
     * @param string $sName
     * @param bool $bVerify
     *
     * @return null|string
     */
    protected function addPhrase($aVals, $sName = 'title', $bVerify = true)
    {
        $aFirstLang = end($this->_aLanguages);
        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            } elseif ($bVerify) {
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" ' . $sName . '.',
                    ['language_name' => $aLanguage['title']])));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull) {
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = 'yncstatusbg_collection_title_' . md5('yncstatusbg_collection_title' . $name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

    public function deleteCollection($iId)
    {
        Phpfox::isAdmin(true);

        $aCollection = Phpfox::getService('yncstatusbg')->getForEdit($iId);
        if (!$aCollection || $aCollection['view_id'] == 1 || $aCollection['is_default'] == 1) {
            return Phpfox_Error::set(_p('collection_can_not_found_or_you_can_not_delete_it'));
        }
        db()->update($this->_sTable, ['is_deleted' => 1], 'collection_id =' . (int)$iId);
        db()->update($this->_sBTable, ['is_deleted' => 1], 'collection_id =' . (int)$iId);

        $this->cache()->removeGroup('yncstatusbg');
        return true;
    }

    public function deleteBackground($iBackgroundId, $aCollection = null, $bCheckMain = true)
    {
        Phpfox::isAdmin(true);
        $aBackground = db()->select('*')
            ->from($this->_sBTable)
            ->where('background_id =' . (int)$iBackgroundId)
            ->execute('getRow');
        if (!$aBackground || $aBackground['view_id']) {
            return false;
        }

        if ($aCollection == null) {
            $aCollection = db()->select('c.*')
                ->from($this->_sTable, 'c')
                ->join($this->_sBTable, 'b', 'c.collection_id = b.collection_id')
                ->where('b.background_id =' . (int)$iBackgroundId)
                ->execute('getRow');
        }
        if (!$aCollection) {
            return false;
        }
        //Update main image for collection
        if ($aCollection['main_image_id'] == $iBackgroundId && $bCheckMain) {
            $iOtherBackground = db()->select('background_id')
                ->from($this->_sBTable)
                ->where('is_deleted = 0 AND collection_id = ' . $aCollection['collection_id'] . ' AND background_id <>' . (int)$iBackgroundId)
                ->order('ordering ASC, background_id ASC')
                ->execute('getField');
            db()->update($this->_sTable, ['main_image_id' => $iOtherBackground ? $iOtherBackground : 0],
                'collection_id =' . $aCollection['collection_id']);
            $this->cache()->removeGroup('yncstatusbg');
        }
        //Mark sticker is deleted
        db()->update($this->_sBTable, ['is_deleted' => 1], 'background_id = ' . $aBackground['background_id']);

        db()->updateCounter('yncstatusbg_collections', 'total_background', 'collection_id',
            $aCollection['collection_id'], true);
        return true;
    }

    public function toggleActiveCollection($iId, $iActive)
    {
        Phpfox::isAdmin(true);
        $aCollection = Phpfox::getService('yncstatusbg')->getForEdit($iId);
        if (!$aCollection) {
            return Phpfox_Error::set(_p('collection_you_are_looking_for_does_not_exists'));
        }
        $iActive = (int)$iActive;
        if ($iActive == 1 && Phpfox::getService('yncstatusbg')->countTotalActiveCollection() >= 2) {
            return Phpfox_Error::set(_p('you_cannot_activate_this_collection_because_the_maximum_number_of_active_collections_is_2'));
        }
        if ($iActive == 0 && $aCollection['is_default'] == 1) {
            return Phpfox_Error::set(_p('you_cannot_deactive_default_collection'));
        }
        $this->database()->update($this->_sTable, [
            'is_active' => ($iActive == 1 ? 1 : 0)
        ], 'collection_id = ' . (int)$iId);

        $this->cache()->removeGroup('yncstatusbg');
        return true;
    }

    public function setDefault($iId)
    {
        Phpfox::isAdmin(true);
        $aCollection = Phpfox::getService('yncstatusbg')->getForEdit($iId);
        if (!$aCollection) {
            return Phpfox_Error::set(_p('collection_you_are_looking_for_does_not_exists'));
        }
        if ($aCollection['is_default']) {
            return true;
        }
        db()->update($this->_sTable, ['is_default' => 0, 'is_active' => 0], 'is_default = 1');

        //Set default
        db()->update($this->_sTable, ['is_default' => 1, 'is_active' => 1], 'collection_id =' . (int)$iId);
        $this->cache()->removeGroup('yncstatusbg');
        return true;
    }

    /**
     * @param $aParams
     * @param $iCollectionId
     * @return bool
     */
    public function updateImagesOrdering($aParams, $iCollectionId)
    {
        $iCnt = 0;
        foreach ($aParams['values'] as $mKey => $mOrdering) {
            if ($iCnt == 0) {
                db()->update($this->_sTable, ['main_image_id' => $mKey], 'collection_id =' . $iCollectionId);
            }
            $iCnt++;
            db()->update($this->_sBTable, array('ordering' => $iCnt),
                'background_id =' . $mKey . ' AND collection_id =' . $iCollectionId);
        }
        $this->cache()->removeGroup('yncstatusbg');
        return true;
    }

    public function editUserStatusCheck($iItemId, $sType, $iUserId, $iActive)
    {
        return db()->update($this->_sSBTable, ['is_active' => $iActive],
            'item_id = ' . (int)$iItemId . ' AND type_id = \'' . $sType . '\' AND user_id = ' . (int)$iUserId);
    }

    public function updateStatus($aVals)
    {
        if (isset($aVals['feed_id']) && $aVals['feed_id']) {
            return Phpfox::getService('user.process')->editStatus($aVals['feed_id'], $aVals);
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
        $bIsTagged = false;
        if (Phpfox::getParam('feed.enable_tag_friends') && !empty($aVals['tagged_friends'])) {
            $aTagged = explode(',', $aVals['tagged_friends']);
            foreach ($aTagged as $iKey => $iTagUserId) {
                if (!Phpfox::getService('user.privacy')->hasAccess($iTagUserId, 'user.can_i_be_tagged')) {
                    unset($aTagged[$iKey]);
                }
            }
            if (count($aTagged)) {
                Phpfox::getService('feed.process')->addTaggedUsers($iStatusId, $aTagged, 'user_status');
                $bIsTagged = true;
            }
        } else {
            $aTagged = [];
        }

        if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('user_status', $iStatusId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        if (isset($aVals['status_background_id'])) {
            $iBackgroundId = $aVals['status_background_id'];
        } else {
            $iBackgroundId = 0;
        }
        $aCurrentUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
        $sTagger = (isset($aCurrentUser['full_name']) && $aCurrentUser['full_name']) ? $aCurrentUser['full_name'] : $aCurrentUser['user_name'];
        $link = Phpfox_Url::instance()->makeUrl($aCurrentUser['user_name'], ['status-id' => $iStatusId]);
        $aSentMail = array();
        if ($bIsTagged) {
            if (empty($aInsert['location_latlng'])) {
                $this->notifyTagged($sStatus, $iStatusId, 'status', 0, null, $aTagged, [], $iBackgroundId);
            } else {
                $this->notifyTagged($sStatus, $iStatusId, 'status', 0, null, $aTagged, [
                    'location_latlng' => $aInsert['location_latlng'],
                    'location_name' => $aInsert['location_name']
                ], $iBackgroundId);
            }

            //Send Mail
            foreach ($aTagged as $iUserId) {
                $aSentMail[] = $iUserId;
                Phpfox_Mail::instance()->to($iUserId)
                    ->subject(_p('user_name_tagged_you_in_a_status_update', ['user_name' => $sTagger]))
                    ->message(_p('user_name_tagged_you_in_a_status_update',
                            ['user_name' => $sTagger]) . '. <a href="' . $link . '">' . _p('check_it_out') . '</a>')
                    ->send();
            }
        }

        $this->notifyTagged($sStatus, $iStatusId, 'status', 0, null, null, [], $iBackgroundId);
        // Send mail to mentioned user
        $mentions = Phpfox_Parse_Output::instance()->mentionsRegex($aVals['user_status']);
        foreach ($mentions as $user) {
            if (!Phpfox::getService('user.privacy')->hasAccess($user->id, 'user.can_i_be_tagged')) {
                continue;
            }
            if (!in_array($user->id, $aSentMail)) {
                Phpfox_Mail::instance()->to($user->id)
                    ->subject(_p('user_name_tagged_you_in_a_status_update', ['user_name' => $sTagger]))
                    ->message(_p('user_name_tagged_you_in_a_status_update',
                            ['user_name' => $sTagger]) . '. <a href="' . $link . '">' . _p('check_it_out') . '</a>')
                    ->send();
            }
        }

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('user_status', $iStatusId, Phpfox::getUserId(), $sStatus, true);
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus')) ? eval($sPlugin) : false);

        $iReturnId = Phpfox::getService('feed.process')->add('user_status', $iStatusId, $aVals['privacy'],
            $aVals['privacy_comment'], 0, null, 0, (isset($aVals['parent_feed_id']) ? $aVals['parent_feed_id'] : 0),
            (isset($aVals['parent_module_id']) ? $aVals['parent_module_id'] : null));

        if (Phpfox::isAppActive('Core_Activity_Points')) {
            Phpfox::getService('activitypoint.process')->updatePoints(Phpfox::getUserId(), !empty($aVals['no_check_empty_user_status']) ? 'share_item' : 'feed_postonwall');
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_updatestatus_end')) ? eval($sPlugin) : false);

        return $iReturnId;
    }

    public function notifyTagged(
        $sContent,
        $iItemId,
        $sType,
        $iPrivacy = 0,
        $iUpdateFeedId = null,
        $aMatches = null,
        $aLocation = [],
        $iBackgroundId = 0
    ) {
        $bIsMention = false;

        //use aMatches for storage user who tag in `with friend`
        if (!$aMatches) {
            $bIsMention = true;
            $aMatches = Phpfox::getService('user.process')->getIdFromMentions($sContent);
        }
        $aChecked = array();
        foreach ($aMatches as $iKey => $iUserId) {
            if (in_array($iUserId, $aChecked) || empty($iUserId)) {
                continue;
            }
            $aChecked[] = $iUserId;
        }
        $aMatches = $aChecked;

        if (empty($aMatches)) {
            return;
        }
        $sUsers = implode(',', $aMatches);
        $aPerms = $this->database()->select('user_id, user_value')->from(Phpfox::getT('user_privacy'))->where('user_id in (' . $sUsers . ' ) AND user_privacy = \'user.can_i_be_tagged\'')->execute('getSlaveRows');
        foreach ($aPerms as $aRow) {
            foreach ($aMatches as $iIndex => $iUserId) {
                if ($iUserId == $aRow['user_id'] && $aRow['user_value'] == 4) {
                    unset($aMatches[$iIndex]);
                }
            }
        }

        if ($sType == 'status') {
            foreach ($aMatches as $iIndex => $iUserId) {
                $bIsExist = Phpfox::getService('notification')->checkExisted('feed_comment_profile', $iItemId,
                    $iUserId);
                // Copy the status update as if it were a comment on that user's profile
                if ($iUpdateFeedId !== null) {
                    Phpfox::getService('feed.process')->updateFeedComment($iUpdateFeedId, $sContent);
                } else {
                    if (!$bIsExist) {
                        $aInsert = [
                            'privacy_comment' => 0,
                            'parent_user_id' => $iUserId,
                            'user_id' => Phpfox::getUserId(),
                            'user_status' => $sContent,
                            'privacy' => $iPrivacy,
                            'time_stamp' => PHPFOX_TIME,
                            'feed_reference' => $iItemId,
                        ];
                        if (!$bIsMention) {
                            $aInsert['tagged_friends'] = implode(',', $aMatches);
                            $aInsert['no_notification'] = true;
                        }
                        $iFeedId = Phpfox::getService('feed.process')->addComment(empty($aLocation) ? $aInsert : array_merge($aInsert,
                            $aLocation));
                        if ($iBackgroundId && $iFeedId) {
                            $iStatusId = db()->select('item_id')->from(':feed')->where('feed_id = ' . (int)$iFeedId)->execute('getField');
                            if ($iStatusId) {
                                Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('feed_comment',
                                    $iStatusId, $iBackgroundId, Phpfox::getUserId(), 'feed');
                            }
                        }
                    }
                }
                if (!$bIsExist) {
                    if (Phpfox::isModule('notification')) {
                        Phpfox::getService('notification.process')->add('feed_comment_profile', $iItemId, $iUserId);
                    }
                }
            }
        } else {
            /*
                Implemented for comments in:
                    photo
                    blog
                    Comments in pages are funky, remove from there?
                    Video - phrase
                    Quiz
                    Poll
                    Song
                    Music Album

            */

            if (Phpfox::isModule('notification')) {
                $sName = 'comment_';
                if ($sType == 'photo_album' || (strpos($sType, 'music') !== false) || ($sType == 'user_status')) {
                    $sName .= $sType . 'tag';
                } else {
                    $sName .= $sType . '_tag';
                }

                foreach ($aMatches as $iIndex => $iUserId) {
                    if (!Phpfox::getService('notification')->checkExisted($sName, $iItemId, $iUserId)) {
                        Phpfox::getService('notification.process')->add($sName, $iItemId, $iUserId);
                    }
                }
            }
        }
    }

    public function addBackgroundForStatus($sType, $iItemId, $iBackgroundId, $iUserId = null, $sModule = null)
    {
        if (!$iBackgroundId || !$iItemId) {
            return false;
        }
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        $aInsert = [
            'type_id' => $sType,
            'item_id' => $iItemId,
            'background_id' => $iBackgroundId,
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME,
            'module_id' => $sModule
        ];
        $iId = db()->insert($this->_sSBTable, $aInsert);

        return $iId;
    }
}