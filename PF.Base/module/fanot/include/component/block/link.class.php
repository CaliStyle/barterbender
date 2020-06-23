<?php

defined('PHPFOX') or exit('NO DICE!');

class Fanot_Component_Block_Link extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = (int) Phpfox::getParam('fanot.how_many_notifications_to_show');

        if ($iLimit < 0)
        {
            $iLimit = 5;
        }
        $aNotifications = Phpfox::getService('fanot')->getNotifications($iLimit);

        $aFriendRequests = array();

        if (Phpfox::getParam('fanot.enable_advanced_feed_notification_for_friend_request'))
        {
            $aFriendRequests = Phpfox::getService('fanot')->getFriendRequests(0, $iLimit);
        }
        if (!count($aNotifications) && !count($aFriendRequests))
        {
            return false;
        }

        $this->template()->assign(array(
            'aNotifications' => $aNotifications,
            'aFriendRequests' => $aFriendRequests,
            'bIsActiveSoundAlert' => Phpfox::getService('fanot')->isActiveSoundAlert()
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fanot.component_block_link_clean')) ? eval($sPlugin) : false);
    }

}
?>

