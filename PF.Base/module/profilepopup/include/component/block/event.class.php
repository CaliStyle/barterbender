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
class ProfilePopup_Component_Block_Event extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
                //      get parameters
                $sM = $this->request()->get('m');
                $sModule = $this->request()->get('module');
                $sName = $this->request()->get('name');
                $sMatchType = $this->request()->get('match_type');
                $sMatchID = trim($this->request()->get('match_id'), '/');
                $sMatchName = $this->request()->get('match_name');

                //      init
                $oProfilePopup = Phpfox::getService('profilepopup');

                //      check event exist
                $aEvent = Phpfox::getService('event')->getEvent($sMatchID);
                $iIsEvent = 1;
                if (!($aEvent) || isset($aEvent['event_id']) === false)
                {
                        $this->template()->assign(array(
                                'iIsEvent' => $iIsEvent
                                )
                        );

                        return;
                }

                $aEvent['start_time_short_month'] = date('M', $aEvent['start_time']);
                $aEvent['start_time_short_day'] = date('j', $aEvent['start_time']);

                //      check can view event
                $iIsCanView = 1;
                $bCanViewEvent = true;
                if (Phpfox::isModule('privacy'))
                {
                        $bCanViewEvent = Phpfox::getService('privacy')->check('event', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'], $aEvent['is_friend'], true);
                        if (!$bCanViewEvent)
                        {
                                $iIsCanView = 0;
                        }
                } else
                {
                        $iIsCanView = 0;
                }
                if ($iIsCanView == 0)
                {
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
                $showCoverPhoto = false;

                for ($idx = 0; $idx < $iLen; $idx++)
                {
                    // check show cover photo
                    if($aAllItems[$idx]['name'] == 'cover_photo' && $aAllItems[$idx]['is_display'] == 1){
                        $showCoverPhoto = true;
                    }

                    //      language name
                    $aAllItems[$idx]['lang_name'] = _p('profilepopup.' . $aAllItems[$idx]['phrase_var_name']);
                }

                if (!$showCoverPhoto)
                {
                    $aEvent['image_path'] = '';
                }

                //      get total attending member
                $iPageSize = 1;
                $iTotalOfMember = 0;
                $aInvites = array();
                list($iTotalOfMember, $aInvites) = Phpfox::getService('event')->getInvites($aEvent['event_id'], 1, 1, $iPageSize);

                $iJoinedFriendTotal = 0;
                $aJoinedFriend = array();
                if ($sShowJoinedFriend === '1')
                {
                        list($iJoinedFriendTotal, $aJoinedFriend) = $oProfilePopup->getJoinedFriendInEvent($aEvent['event_id'], $iNumberOfJoinedFriend);
                }

                //      generate event url
                $sEventUrl = Phpfox::permalink('event', $aEvent['event_id'], $aEvent['title']);

                $iShorten = intval(Phpfox::getParam('profilepopup.profilepopup_length_in_index'));

                //      integrate with Fox Favorite
                if (Phpfox::isModule('foxfavorite') && Phpfox::isUser())
                {
                        $sFFModule = 'event';
                        $iFFItemId = $aEvent['event_id'];
                        $iFFViewId = phpfox::getUserBy('view_id');

                        $bFFPass = true;
                        if (!Phpfox::getService('foxfavorite')->isAvailModule($sFFModule) || empty($aEvent) || $iFFViewId != 0)
                        {
                                $bFFPass = false;
                        }

                        if ($bFFPass === true)
                        {
                                $bFFIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sFFModule, $aEvent['event_id']);
                                $this->template()->assign(array(
                                        'bFFIsAlreadyFavorite' => $bFFIsAlreadyFavorite,
                                        'sFFModule' => $sFFModule,
                                        'iFFItemId' => $iFFItemId
                                        )
                                );
                        }
                }
                $event_date = explode('-', $aEvent['event_date']);
                $aEvent['start_date'] = $event_date[0];
                $aEvent['end_date'] = $event_date[1];
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
                        'bEnableCachePopup' => Phpfox::getParam('profilepopup.enable_cache_popup'),
                        'iShorten' => $iShorten
                        )
                );
                //      end 
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_event_clean')) ? eval($sPlugin) : false);
        }

}

