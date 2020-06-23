<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/17/16
 * Time: 10:09 AM
 *
 * Note: This class can be used for display all user with some conditional query
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_User extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        // We're in page detail of store. So if we don't have store detail params will not show this block
        // $iItemId just mean item id
        $iItemId = $this->request()->getInt('iStoreId', $this->request()->getInt('iProductId', $this->request()->getInt('iItemId', 0)));

        if ($iItemId <= 0) {
            return false;
        }

        $aTypeUser = array('favorite', 'following', 'friend-bought-this');
        $sTypeUser = $this->request()->get('sType');

        $iLimit = 10;

        $iPage = $this->request()->get('page', 0) + 1;

        if (!in_array($sTypeUser, $aTypeUser)) {
            return false;
        }

        switch ($sTypeUser) {
            case 'favorite':
                list($iCount, $aItem) = Phpfox::getService('ynsocialstore')->getAllFavorite($iItemId, $iLimit, $iPage);
                break;
            case 'following':
                list($iCount, $aItem) = Phpfox::getService('ynsocialstore')->getAllFollowing($iItemId, $iLimit, $iPage);
                break;
            case 'friend-bought-this':
                list($iCount, $aItem) = Phpfox::getService('ynsocialstore.product')->getFriendBoughtThisProduct($iItemId, $iLimit, $iPage);
                break;
        }

        $this->template()->assign(array(
                'aItem'	=> isset($aItem) ? $aItem : array(),
                'iPage' => isset($iPage) ? $iPage : 0,
                'sType' => $sTypeUser,
                'iCount' => $iCount,
                'iItemId' => $iItemId,
            )
        );

    }

}