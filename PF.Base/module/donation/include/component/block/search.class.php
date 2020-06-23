<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.class.php 3639 2011-12-02 05:59:22Z Raymond_Benc $
 */
class Donation_Component_Block_Search extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $oDb = Phpfox::getLib('database');
        $oDonation = Phpfox::getService('donation');
        // Variables.
        $iPage = $this->getParam('page', 0);
        $iPageId = (int) $this->getParam('iPageId');
        $iUserId = (int) $this->getParam('iUserId');
        $iPageSize = 20;
        $bIsOnline = false;
        $aParams = array();
        $aConditions = array();
        $iListId = 0;
        $iUserPageId = $oDonation->getUserIdOfPage($iPageId);
        $aUsersId = $oDonation->getDonatedUser($iPageId);
        // Not a user.
        if (!empty($aUsersId))
        {
            foreach ($aUsersId as $user)
            {
                $ret[] = $user['user_id'];
            }
            $aUsersId = implode(',', $ret);
            $aConditions[] = 'u.user_id IN (' . $aUsersId . ')';
        }
        // Add find condition.
        if (($sFind = $this->getParam('find')))
        {
            $aConditions[] = 'AND (u.full_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))';
        }
        // Letters
        $aLetters = array(
            'All', '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        );
        // Check the letter.
        if (($sLetter = $this->getParam('letter')) && in_array($sLetter, $aLetters) && strtolower($sLetter) != 'all')
        {
            if ($sLetter == '#')
            {
                $sSubCondition = '';
                for ($i = 0; $i <= 9; $i++)
                {
                    $sSubCondition .= "OR u.full_name LIKE '" . $oDb->escape($i) . "%' ";
                }
                $sSubCondition = ltrim($sSubCondition, 'OR ');
                $aConditions[] = 'AND (' . $sSubCondition . ')';
            }
            else
            {
                $aConditions[] = "AND u.full_name LIKE '" . $oDb->escape($sLetter) . "%'";
            }
            $aParams['letter'] = $sLetter;
        }
        // View case.
        if ($sView = $this->getParam('view'))
        {
            switch ($sView) {
                case 'top':
                    $aConditions[] = 'AND is_top_friend = 1';
                    break;
                case 'online':
                    $bIsOnline = true;
                    break;
                case 'all':
                    break;
                default:
                    if ((int) $sView > 0 && ($aList = Phpfox::getService('friend.list')->getList($sView, Phpfox::getUserId())) && isset($aList['list_id']))
                    {
                        $iListId = (int) $aList['list_id'];
                    }
                    break;
            }
        }
        // Check type.
        if ($this->getParam('type') == 'mail')
        {
            $aConditions[] = 'AND u.user_id != ' . Phpfox::getUserId();
            list($iCnt, $aFriends) = Phpfox::getService('user.browse')
                    ->conditions($aConditions)
                    ->sort('u.full_name ASC')
                    ->page($iPage)
                    ->get();
            if (Phpfox::getParam('mail.disallow_select_of_recipients'))
            {
                $oMail = Phpfox::getService('mail');
                foreach ($aFriends as $iKey => $aFriend)
                {
                    if (!$oMail->canMessageUser($aFriend['user_id']))
                    {
                        $aFriends[$iKey]['canMessageUser'] = false;
                    }
                }
            }
        }
        else
        {
            if (empty($aConditions))
            {
                $aConditions[] = 'friend.user_id IN (0)';
            }
            $aResult = Phpfox::getService('donation.cache')->get('skey' . $iPageId . '.page.' . $iPage . '.' . $iPageSize);

            if ($aResult === FALSE)
            {
                list($iCnt, $aFriends) = $oDonation->getDonatedUserBlock($aConditions, 'u.full_name ASC', $iPage, $iPageSize, true, true, $bIsOnline, null, false, $iListId, $iPageId);
                Phpfox::getService('donation.cache')->set('skey' . $iPageId . '.page.' . $iPage . '.' . $iPageSize, $aFriends);
            }
            else
            {
                list($iCnt, $aFriends) = array(count($aResult), $aResult);
            }
        }
        if (!empty($aFriends))
        {
            foreach ($aFriends as &$f)
            {
                if ($f['is_guest'])
                {
                    if (strlen($f['temp_id']) > 20)
                    {
                        $f['temp_id'] = substr($f['temp_id'], 0, 20) . "...";
                    }
                }
                else
                {
                    if (strlen($f['full_name']) > 20)
                    {
                        $f['full_name'] = substr($f['full_name'], 0, 20) . "...";
                    }
                    $aUser = $oDonation->getUser($f['user_id']);
                    $aUser['suffix'] = '_50_square';
                    $aUser['max_width'] = '50';
                    $aUser['max_height'] = '50';
                    $aUser['user'] = $aUser;
                    $f['img'] = Phpfox::getLib('image.helper')->display($aUser);
                }
            }
        }
        $aParams['input'] = $this->getParam('input');
        $aParams['friend_item_id'] = $this->getParam('friend_item_id');
        $aParams['friend_module_id'] = $this->getParam('friend_module_id');
        $aParams['type'] = $this->getParam('type');
        $sFriendModuleId = $this->getParam('friend_module_id', '');
        $this->template()->assign(array(
            'iTotalDonors' => $iCnt,
            'aFriends' => $aFriends,
            'bModerator' => ($iUserId == $iUserPageId),
            'iPageId' => $iPageId,
            'aLetters' => $aLetters,
            'sView' => $sView,
            'sActualLetter' => $sLetter,
            'sPrivacyInputName' => $this->getParam('input'),
            'aLists' => Phpfox::getService('friend.list')->get(),
            'bSearch' => $this->getParam('search'),
            'bIsForShare' => $this->getParam('friend_share', false),
            'sFriendItemId' => (int) $this->getParam('friend_item_id', '0'),
            'sFriendModuleId' => $sFriendModuleId,
            'sFriendType' => $this->getParam('type'),
            'sNoProfileImagePath' => Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/profile_50.png'
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('friend.component_block_search_clean')) ? eval($sPlugin) : false);
    }

}

?>