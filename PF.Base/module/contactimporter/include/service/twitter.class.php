<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php

if (!class_exists('Twitter'))
{
    require_once(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'Apiconnection' . PHPFOX_DS . 'twitter.php');
}
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'abstract.class.php');

class Contactimporter_Service_Twitter extends Contactimporter_Service_Abstract
{

    protected $_oTwitter;

    public function __construct()
    {
        $aSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('twitter');
        $aSetting['api_params'] = unserialize($aSetting['api_params']);
        $aConfig = array('consumer_key' => $aSetting['api_params']['appid'], 'consumer_secret' => $aSetting['api_params']['secret']);
        $this->_oTwitter = new Twitter($aSetting['api_params']['appid'], $aSetting['api_params']['secret']);
        $this->_oTwitter->setOAuthToken($_SESSION['twitter']['oauth_token']);
        $this->_oTwitter->setOAuthTokenSecret($_SESSION['twitter']['oauth_token_secret']);
    }

    public function getProfile()
    {
        $oTwitter = $this->_oTwitter;
        if ($oTwitter)
        {
            try
            {
                $me = $oTwitter->accountVerifyCredentials();
                $me['friend_count'] = isset($me['followers_count']) ? $me['followers_count'] : 0;
            }
            catch (Exception $e)
            {
                Phpfox_Error::set($e);
                return null;
            }
            return $me;
        }
        return null;
    }
    
    public function getFriends($iPage = 1, $iLimit = 50, $sCursor = -1)
    {
        $oTwitter = $this->_oTwitter;
        $iOffset = (($iPage - 1) * $iLimit) % 5000;
        if($iOffset > 5000)
            $iOffset = 0;
        $aInviteds = Phpfox::getService('contactimporter')->getInviteds();
        $aFriendIds = array();
        $iInvited = 0;
        do
        {
            list($sNextCursor, $aFriendIds) = $oTwitter->followersIds(null, $_SESSION['twitter']['user_id'], null, $sCursor);

            $aUninvited = array();
            foreach ($aFriendIds as $friend)
            {
                if (!in_array($friend, $aInviteds))
                {
                    $aUninvited[] = $friend;
                }
                else
                {
                  $iInvited++;
                }
            }

            $aFriendIds = $aUninvited;
            if (!count($aFriendIds))
            {
                $sCursor = $sNextCursor;
            }
        }
        while (count($aFriendIds) == 0 && $sNextCursor > 0);

        $sLinkNext = $sLinkPrev = '';
        if (!isset($_SESSION['twitter']['page']))
        {
            $_SESSION['twitter']['page'] = array();
        }
        if ($sNextCursor)
        {
            $_SESSION['twitter']['page'][] = Phpfox::getLib('url')->makeUrl('contactimporter.twitter', array('cursor' => $sNextCursor));
        }
        $aTwPage = $_SESSION['twitter']['page'];
        if (count($aTwPage) > 0)
        {
            $iPageCnt = count($aTwPage);
            $sLinkNext = $aTwPage[$iPageCnt - 1];
            if ($iPageCnt > 1 && isset($aTwPage[$iPageCnt - 2]))
            {
                $sLinkPrev = $aTwPage[$iPageCnt - 2];
            }
        }
        $aFriends = array();
        if ($iCount = count($aFriendIds))
        {
            $_SESSION['contactimporter']['twitter']['total_friends'] = $iCount;
            $aFriendIds = array_slice($aFriendIds, $iOffset, $iLimit);
            $aFriends = $oTwitter->usersLookup($aFriendIds, null);
            $aFriends = $this->processSocialRows($aFriends);
            $aJoineds = Phpfox::getService('contactimporter')->checkSocialJoined($aFriendIds);
            return array($aFriends, $sLinkPrev, $sLinkNext, $aJoineds,$iInvited);
        }
        return array($aFriends, $sLinkPrev, $sLinkNext, array(),$iInvited);
    }

    public function sendInvitation($iId, $sMessage, $sLink)
    {
        $oTwitter = $this->_oTwitter;
        if ($oTwitter)
        {
            if ($iId)
            {
                $sMessage = substr($sMessage, 0, 120 - strlen($sLink)) . ' ' . $sLink;
                try
                {
                    $iReturn = $oTwitter->directMessagesNew($sMessage, null, $iId);
                    return $iReturn;
                }
                catch (Exception $e)
                {
                    //Phpfox_Error::set($e);
                }
            }
        }
    }

}
?>