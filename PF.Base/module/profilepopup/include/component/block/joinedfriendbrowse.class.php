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
class ProfilePopup_Component_Block_JoinedFriendBrowse extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
                $iPage = $this->request()->getInt('page');
                $sItemType = $this->request()->get('item_type');
                $iItemID = $this->request()->getInt('item_id');

                $iPageSize = 5;
                $iCnt = 0;
                $aFriends = array();
                if ($sItemType == 'pages')
                {
                        list($iCnt, $aFriends) = Phpfox::getService('profilepopup')->getJoinedFriendInPagesWithPaging((int) $iItemID, $iPage, $iPageSize, true);
                } else if ($sItemType == 'groups')
                {
                    list($iCnt, $aFriends) = Phpfox::getService('profilepopup')->getJoinedFriendInGroupsWithPaging((int) $iItemID, $iPage, $iPageSize, true);
                } else if ($sItemType == 'event')
                {
                        list($iCnt, $aFriends) = Phpfox::getService('profilepopup')->getJoinedFriendInEventWithPaging((int) $iItemID, $iPage, $iPageSize, true);
                } else if ($sItemType == 'fevent')
                {
                        list($iCnt, $aFriends) = Phpfox::getService('profilepopup')->getJoinedFriendInFEventWithPaging((int) $iItemID, $iPage, $iPageSize, true);
                }

                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'ajax' => 'profilepopup.getJoinedFriends'));

                $this->template()->assign(array(
                        'aFriends' => $aFriends,
                        'iPage' => $iPage
                        )
                );
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_joinedfriendbrowse_clean')) ? eval($sPlugin) : false);
        }

}

