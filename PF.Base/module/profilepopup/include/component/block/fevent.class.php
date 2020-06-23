<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Component_Block_FEvent extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sM = $this->request()->get('m');
        $sModule = $this->request()->get('module');
        $sName = $this->request()->get('name');
        $sMatchType = $this->request()->get('match_type');
        $sMatchID = trim($this->request()->get('match_id'), '/');
        $sMatchName = $this->request()->get('match_name');

        //      init
        $oProfilePopup = Phpfox::getService('profilepopup');

        //      check event exist
        $aEvent = Phpfox::getService('fevent')->getEvent($sMatchID);
        $iIsEvent = 1;
        if (!($aEvent) || isset($aEvent['event_id']) === false) {
            $this->template()->assign(array(
                    'iIsEvent' => $iIsEvent
                )
            );

            return;
        }

        //      check can view event
        $iIsCanView = 1;
        $bCanViewEvent = true;
        if (Phpfox::isModule('privacy')) {
            $bCanViewEvent = Phpfox::getService('privacy')->check('fevent', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'], $aEvent['is_friend'], true);
            if (!$bCanViewEvent) {
                $iIsCanView = 0;
            }
        } else {
            $iIsCanView = 0;
        }
        if ($iIsCanView == 0) {
            $this->template()->assign(array(
                    'iIsEvent' => $iIsEvent,
                    'iIsCanView' => $iIsCanView,
                    'aEvent' => $aEvent
                )
            );

            return;
        }

        //      get permission
        $sShowJoinedFriend = Phpfox::getParam('profilepopup.show_joined_friend_in_event') ? '1' : '0';
        $iNumberOfJoinedFriend = intval(Phpfox::getParam('profilepopup.number_of_joined_friend_in_event'));

        //      get popup item
        $aAllItems = array();
        $aAllItems = $oProfilePopup->getAllItems(null, 'event');
        $iLen = count($aAllItems);
        for ($idx = 0; $idx < $iLen; $idx++) {
            //      language name
            $aAllItems[$idx]['lang_name'] = _p('profilepopup.' . $aAllItems[$idx]['phrase_var_name']);
        }

        //      get total attending member
        $iPageSize = 1;
        $iTotalOfMember = 0;
        $aInvites = array();
        list($iTotalOfMember, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 1, 1, $iPageSize);

        $iJoinedFriendTotal = 0;
        $aJoinedFriend = array();
        if ($sShowJoinedFriend === '1') {
            list($iJoinedFriendTotal, $aJoinedFriend) = $oProfilePopup->getJoinedFriendInFEvent($aEvent['event_id'], $iNumberOfJoinedFriend);
        }

        //      generate event url
        $sEventUrl = Phpfox::permalink('fevent', $aEvent['event_id'], $aEvent['title']);

        //      get repeat infor
        $content_repeat = "";
        $until = "";
        if ($aEvent['isrepeat'] == 0) {
            $content_repeat = _p('fevent.daily');
        } else if ($aEvent['isrepeat'] == 1) {
            $content_repeat = _p('fevent.weekly');
        } else if ($aEvent['isrepeat'] == 2) {
            $content_repeat = _p('fevent.monthly');
        }
        if ($content_repeat != "") {
            if ($aEvent['timerepeat'] != 0) {
                $sDefault = null;
                if (phpfox::getLib("date")->getTimeZone() < 0)
                    $until = Phpfox::getTime("m/d/Y", $aEvent['timerepeat'] - 3600 * phpfox::getLib("date")->getTimeZone());
                else
                    $until = Phpfox::getTime("m/d/Y", $aEvent['timerepeat']);

                $content_repeat .= ", " . _p('fevent.until') . " " . $until;
            }
        }

        $iShorten = intval(Phpfox::getParam('profilepopup.profilepopup_length_in_index'));

        //      integrate with Fox Favorite
        if (Phpfox::isModule('foxfavorite') && Phpfox::isUser()) {
            $sFFModule = 'fevent';
            $iFFItemId = $aEvent['event_id'];
            $iFFViewId = phpfox::getUserBy('view_id');

            $bFFPass = true;
            if (!Phpfox::getService('foxfavorite')->isAvailModule($sFFModule) || empty($aEvent) || $iFFViewId != 0) {
                $bFFPass = false;
            }

            if ($bFFPass === true) {
                $bFFIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sFFModule, $aEvent['event_id']);
                $this->template()->assign(array(
                        'bFFIsAlreadyFavorite' => $bFFIsAlreadyFavorite,
                        'sFFModule' => $sFFModule,
                        'iFFItemId' => $iFFItemId
                    )
                );
            }
        }

        $type = Phpfox::getService('fevent.helper')->getTimeLineStatus($aEvent['start_time'],$aEvent['end_time']);
        $timeNeedToFormatted = in_array($type, ['ongoing', 'past']) ? $aEvent['end_time'] : $aEvent['start_time'];
        $aEvent['date_formatted'] = Phpfox::getService('fevent.helper')->formatTimeToDate($type, $timeNeedToFormatted);

        $this->template()->assign(array(
                'iIsEvent' => $iIsEvent,
                'iIsCanView' => $iIsCanView,
                'aEvent' => $aEvent,
                'aAllItems' => $aAllItems,
                'sShowJoinedFriend' => $sShowJoinedFriend,
                'iNumberOfJoinedFriend' => $iNumberOfJoinedFriend,
                'iJoinedFriendTotal' => $iJoinedFriendTotal,
                'aJoinedFriend' => $aJoinedFriend,
                'iTotalOfMember' => $iTotalOfMember,
                'sEventUrl' => $sEventUrl,
                'content_repeat' => $content_repeat,
                'bEnableCachePopup' => Phpfox::getParam('profilepopup.enable_cache_popup'),
                'iShorten' => $iShorten,
                'sCorePath' => Phpfox::getParam('core.path_file')
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_fevent_clean')) ? eval($sPlugin) : false);
    }

}

