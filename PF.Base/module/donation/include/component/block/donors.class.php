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
class Donation_Component_Block_Donors extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iPageId = $this->request()->getInt('iPageId');
        $iTotalDonors = $this->request()->getInt('iTotalDonors');
        $iOffset = (int) $this->getParam('iOffset');
        $iLimit = Phpfox::getParam('donation.number_of_fetched_donors');
        $oDonation = Phpfox::getService('donation');
        $iUserPageId = $oDonation->getUserIdOfPage($iPageId);
        if (Phpfox::getUserId() == $iUserPageId)
        {
            $bModerator = true;
        }
        else
        {
            $bModerator = false;
        }
        if (Phpfox::isAdmin() && $iPageId == -1)
        {
            $bModerator = true;
        }
        $aResult = Phpfox::getService('donation.cache')->get('skey' . $iPageId . '.page.' . $iOffset . '.' . $iLimit);
        if ($aResult === FALSE)
        {
            $aFriends = $oDonation->getDonorList($iOffset, $iLimit, $iPageId);
            Phpfox::getService('donation.cache')->set('skey' . $iPageId . '.page.' . $iOffset . '.' . $iLimit, $aFriends, 3600);
        }
        else
        {
            list($iCnt, $aFriends) = array(count($aResult), $aResult);
        }
        if (!empty($aFriends))
        {
            foreach ($aFriends as &$f)
            {
                if ($f['is_guest'])
                {
                    // Do nothing.
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
        // Set variable for template.
        $this->template()->assign(array(
            'bLoadMore' => $iTotalDonors > $iLimit,
            'iTotalDonors' => $iTotalDonors,
            'iCurrentLimit' => $iOffset + $iLimit,
            'aFriends' => $aFriends,
            'bModerator' => $bModerator,
            'iPageId' => $iPageId,
            'sNoProfileImagePath' => Phpfox::getParam('core.path') . 'module/donation/static/image/no_avatar.jpg'
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