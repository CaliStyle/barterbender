<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

$sPathPPFunctions = PHPFOX_DIR . 'module' . PHPFOX_DS . 'profilepopup' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'extras' . PHPFOX_DS . 'functions.php';

if (file_exists($sPathPPFunctions)) {
    require_once($sPathPPFunctions);
}

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Component_Ajax_Ajax extends Phpfox_Ajax
{

    /**
     * Load profile popup content
     */
    public function loadProfilePopup()
    {
        $this->error(false);

        $sM = $this->get('m');
        $sModule = $this->get('module');
        $sName = $this->get('name');
        $sMatchType = $this->get('match_type');
        $sMatchID = $this->get('match_id');
        $sMatchName = $this->get('match_name');

        $bIsRight = false;
        $sMatchTypeUserConvertToPages = $sMatchType;
        if ($sMatchType == 'user') {
            $bIsRight = true;
            $aUser = Phpfox::getService('user')->getByUserName($sMatchName);
            if (isset($aUser['user_id']) === true) {
                //      check pages object
                if ((!isset($aUser['user_id'])) || (isset($aUser['user_id']) && $aUser['profile_page_id'] > 0)) {
                    if (Phpfox::isModule('pages') && Phpfox::getService('pages')->isPage($sMatchName)) {
                        $sMatchTypeUserConvertToPages = 'page';
                    } elseif (Phpfox::isModule('groups') && Phpfox::getService('groups')->isPage($sMatchName)) {
                        $sMatchTypeUserConvertToPages = 'group';
                    }
                } else {
                    Phpfox::getBlock('profilepopup.user');
                    echo json_encode(array(
                        'content' => $this->getContent(false),
                        'msg' => "success",
                        'match_type' => $sMatchType,
                        'match_id' => $sMatchID,
                        'match_name' => $sMatchName
                    ));
                }
            } else {
                $bIsRight = false;
                $sMatchTypeUserConvertToPages = 'page';
            }
        }
        if ($sMatchType == 'page' || $sMatchTypeUserConvertToPages == 'page') {
            $bIsRight = true;
            if (Phpfox::isModule('pages') && Phpfox::getService('pages')->isPage($sMatchName)) {
            }
            $aPage = Phpfox::getService('pages')->getForView($sMatchID);
            if (!$aPage) {
                $bIsRight = false;
            } else {
                Phpfox::getBlock('profilepopup.pages');
                echo json_encode(array(
                    'content' => $this->getContent(false),
                    'msg' => "success",
                    'match_type' => $sMatchType,
                    'match_id' => $sMatchID,
                    'match_name' => $sMatchName
                ));
            }
        }
        if ($sMatchType == 'group' || $sMatchTypeUserConvertToPages == 'group') {
            $bIsRight = true;
            if (Phpfox::isModule('groups') && Phpfox::getService('groups')->isPage($sMatchName)) {
            }
            $aGroup = Phpfox::getService('groups')->getForView($sMatchID);
            if (!$aGroup) {
                $bIsRight = false;
            } else {
                Phpfox::getBlock('profilepopup.groups');
                echo json_encode(array(
                    'content' => $this->getContent(false),
                    'msg' => "success",
                    'match_type' => $sMatchType,
                    'match_id' => $sMatchID,
                    'match_name' => $sMatchName
                ));
            }
        }
        if ($sMatchType == 'event') {
            $bIsRight = true;
            $aEvent = Phpfox::getService('event')->getEvent($sMatchID);
            if (!$aEvent) {
                $bIsRight = false;
            } else {
                Phpfox::getBlock('profilepopup.event');
                echo json_encode(array(
                    'content' => $this->getContent(false),
                    'msg' => "success",
                    'match_type' => $sMatchType,
                    'match_id' => $sMatchID,
                    'match_name' => $sMatchName
                ));
            }
        }

        if ($sMatchType == 'fevent') {
            $bIsRight = true;
            if (Phpfox::isModule('fevent')) {
                $aEvent = Phpfox::getService('fevent')->getEvent($sMatchID);
                if (!$aEvent) {
                    $bIsRight = false;
                } else {
                    Phpfox::getBlock('profilepopup.fevent');
                    echo json_encode(array(
                        'content' => $this->getContent(false),
                        'msg' => "success",
                        'match_type' => $sMatchType,
                        'match_id' => $sMatchID,
                        'match_name' => $sMatchName
                    ));
                }
            }
        }

        if ($bIsRight === false) {
            echo json_encode(array(
                'content' => '',
                'msg' => "failure",
                'match_type' => $sMatchType,
                'match_id' => $sMatchID,
                'match_name' => $sMatchName
            ));
        }
    }

    /**
     * Update Global Settings in AdminCP
     */
    public function updateGlobalSettings()
    {
        $oRequest = Phpfox::getLib('request');
        $aUpdateIds = $oRequest->getArray('id');
        $aOrdering = $oRequest->getArray('ordering');
        $aDisplay = $oRequest->getArray('display');
        $sItemType = $oRequest->get('item_type');

        $bIsWrong = false;
        foreach ($aOrdering as $iOrder) {
            if (is_numeric($iOrder) === false || intval($iOrder) <= 0) {
                $bIsWrong = true;
                break;
            }
        }

        if ($bIsWrong === true) {
            $this->call('$(\'#core_js_messages\').html(\'<div class="error_message">' . _p('profilepopup.global_setting_warning_ordering') . '</div>\');');
        } else {
            //      update ordering
            foreach ($aUpdateIds as $iKey => $iID) {
                if (isset($aOrdering[$iKey]) === true) {
                    Phpfox::getService('profilepopup.process')->updateOrderingItem(intval($iID),
                        intval($aOrdering[$iKey]), $sItemType);
                }
            }

            //      update display
            Phpfox::getService('profilepopup.process')->setDisplayStatusForAllItem(0, $sItemType);
            foreach ($aDisplay as $iKey => $iID) {
                Phpfox::getService('profilepopup.process')->updateDisplayItem(intval($iID), 1, $sItemType);
            }
        }

        // resume module
        $aUpdateIdsResume = $oRequest->getArray('id_resume');
        $aOrderingResume = $oRequest->getArray('ordering_resume');
        $aDisplayResume = $oRequest->getArray('display_resume');
        $bIsWrong = false;
        foreach ($aOrderingResume as $iOrder) {
            if (is_numeric($iOrder) === false || intval($iOrder) <= 0) {
                $bIsWrong = true;
                break;
            }
        }
        if ($bIsWrong === true) {
            $this->call('$(\'#core_js_messages\').html(\'<div class="error_message">' . _p('profilepopup.global_setting_warning_ordering') . '</div>\');');
        } else {
            //      update ordering
            foreach ($aUpdateIdsResume as $iKey => $iID) {
                if (isset($aOrderingResume[$iKey]) === true) {
                    Phpfox::getService('profilepopup.process')->updateOrderingItemByModule(intval($iID),
                        intval($aOrderingResume[$iKey]), $sItemType, 'resume');
                }
            }

            //      update display
            Phpfox::getService('profilepopup.process')->setDisplayStatusForAllItemByModule(0, $sItemType, 'resume');
            foreach ($aDisplayResume as $iKey => $iID) {
                Phpfox::getService('profilepopup.process')->updateDisplayItemByModule(intval($iID), 1, $sItemType,
                    'resume');
            }

            Phpfox::addMessage(_p('profilepopup.item_s_successfully_updated'));
            $this->call('window.location.href = \'' . Phpfox::permalink('admincp.profilepopup.' . strtolower($sItemType),
                    null) . '\';');
        }
    }

    /**
     * Remove friend
     */
    public function unfriend()
    {
        if (Phpfox::getService('friend.process')->delete($this->get('id'))) {
            $this->alert(_p('friend.friend_successfully_removed'), _p('friend.remove_friend'), 300, 150, true);
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 2000);');
            } else {
                $this->call('window.setTimeout(\'return false\', 2000);');
            }
        }
    }

    /**
     * Get joined friend in pages/event
     */
    public function getJoinedFriends()
    {
        Phpfox::isUser(true);
        $sItemType = $this->get('item_type');
        $iItemID = (int)$this->get('item_id');

        if ((int)$this->get('page') == 0) {
            $this->setTitle(_p('profilepopup.joined_friends_s'));
        }
        Phpfox::getBlock('profilepopup.joinedfriendbrowse');

        if ((int)$this->get('page') > 0) {
            $this->remove('#js_friend_mutual_browse_append_pager');
            $this->append('#js_friend_mutual_browse_append', $this->getContent(false));
        }
    }

    public function likePages()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('like.process')->add($this->get('type_id'), $this->get('item_id'))) {
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('ynfbpp.refreshPage(null);');
            } else {
                $this->call('window.stop();return false;');
            }
        }
    }

    public function signup()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('pages.process')->register($this->get('page_id'))) {
            $this->call('alert(\'' . _p('pages.successfully_registered_for_this_page') . '\'); window.setTimeout(\'ynfbpp.refreshPage(null)\', 500);');
        }
    }

    /**
     * Unlike pages
     */
    public function unlikePages()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('like.process')->delete($this->get('type_id'), $this->get('item_id'),
            (int)$this->get('force_user_id'))) {
            $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 500);');
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('ynfbpp.refreshPage(null);');
            } else {
                $this->call('window.stop();return false;');
            }
        }
    }

    /**
     * Join event, that mean attending
     */
    public function joinEvent()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('event.process')->addRsvp($this->get('id'), $this->get('rsvp'), Phpfox::getUserId())) {
            $this->alert(_p('profilepopup.event_join_successfully'), _p('profilepopup.event_join_title'), 300, 150,
                true);
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 2000);');
            } else {
                $this->call('window.setTimeout(\'return false\', 2000);');
            }
        }
    }

    /**
     * Leave event
     */
    public function leaveEvent()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('event.process')->removeInvite($this->get('id'))) {
            $this->alert(_p('profilepopup.event_inform_leave'), _p('profilepopup.event_leave_title'), 300, 150, true);
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 2000);');
            } else {
                $this->call('window.setTimeout(\'return false\', 2000);');
            }
        }
    }

    /**
     * Join event, that mean attending
     */
    public function joinFEvent()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('fevent.process')->addRsvp($this->get('id'), $this->get('rsvp'), Phpfox::getUserId())) {
            $this->alert(_p('profilepopup.event_join_successfully'), _p('profilepopup.event_join_title'), 300, 150,
                true);
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 2000);');
            } else {
                $this->call('window.setTimeout(\'return false\', 2000);');
            }
        }
    }

    /**
     * Leave event
     */
    public function leaveFEvent()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('fevent.process')->removeInvite($this->get('id'))) {
            $this->alert(_p('profilepopup.event_inform_leave'), _p('profilepopup.event_leave_title'), 300, 150, true);
            if (Phpfox::getParam('profilepopup.enable_cache_popup')) {
                $this->call('window.setTimeout(\'ynfbpp.refreshPage(null)\', 2000);');
            } else {
                $this->call('window.setTimeout(\'return false\', 2000);');
            }
        }
    }

    public function getMutualFriends()
    {
        Phpfox::isUser(true);
        if ((int)$this->get('page') == 0) {
            $this->setTitle(_p('friend.mutual_friends'));
        }
        Phpfox::getBlock('profilepopup.mutual-browse');

        if ((int)$this->get('page') > 0) {
            $this->remove('#js_friend_mutual_browse_append_pager');
            $this->append('#js_friend_mutual_browse_append', $this->getContent(false));
        }
    }

}
