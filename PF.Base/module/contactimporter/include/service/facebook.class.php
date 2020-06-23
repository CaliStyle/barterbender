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

if (!class_exists('YNCFacebook'))
{
    require_once(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'Apiconnection' . PHPFOX_DS . 'facebook.php');
}
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'abstract.class.php');

class Contactimporter_Service_Facebook extends Contactimporter_Service_Abstract
{

    protected $_oFacebook;

    public function __construct()
    {
        $aSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('facebook');
        $aSetting['api_params'] = unserialize($aSetting['api_params']);
        $this->_oFacebook = $oFacebook = new YNCFacebook(array(
                    'appId' => $aSetting['api_params']['appid'],
                    'secret' => $aSetting['api_params']['secret'],
                    'cookie' => true,
                ));
    }

    public function getProfile()
    {
        $oFacebook = $this->_oFacebook;
        if ($oFacebook)
        {
            $uid = $oFacebook->getUser();
            try
            {
                $me = $oFacebook->api('/me');
                $aFriendCount = $oFacebook->api(array(
                    'method' => 'fql.query',
                    'query' => "SELECT friend_count FROM user WHERE uid = $uid"//me()"
                        ));
                $me['friend_count'] = isset($aFriendCount[0]['friend_count']) ? $aFriendCount[0]['friend_count'] : 0;
            }
            catch (FacebookApiException $e)
            {
                //echoDebug($e,true);
                //Phpfox_Error::set($e);
                return null;
            }
            return $me;
        }
        return null;
    }

    public function getFriends($iPage = 1, $iLimit = 50)
    {

        $oFacebook = $this->_oFacebook;
        $iOffset = ($iPage - 1) * $iLimit;

        if ($oFacebook)
        {
            try
            {
                $iCountInvited =  0;
                if (!isset($_SESSION['contactimporter']['facebook']))
                {
                    $session['access_token'] = $oFacebook->getAccessToken();

                    //$friends = $oFacebook->api('/me/friends?token=' . $session['access_token'] . '&limit=' . $iLimit . '&offset=' . $iOffset);
                    $friends = $oFacebook->api('/me/friends?token=' . $session['access_token']);
                    $imgLink = "http://graph.facebook.com/%s/picture";
                    $nextpage = $friends['paging']['next'];
                    $friends = $friends['data'];
                    $aUninvited = array();
                    $aInviteds = Phpfox::getService('contactimporter')->getInviteds();

                    $sServerId = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
                    $aConds[] = 'AND status = "pending"';
                    $aConds[] = 'AND (server_id IS NULL OR server_id = "' . $sServerId . '")';
                    $aConds[] = 'AND provider = "facebook"';
                    $aConds[] = 'AND user_id = "' . Phpfox::getUserId() . '"';

                    $aRow = Phpfox::getLib('phpfox.database')->select('*')
                            ->from(Phpfox::getT('contactimporter_queue'))
                            ->where($aConds)
                            ->limit(1)
                            ->order('queue_id ASC')
                            ->execute('getRow');

                    //d($aRow); die();
                    $iCount = 0;

                    foreach ($friends as $friend)
                    {
                        if (!in_array($friend['id'], $aInviteds))
                        {
                            $aUninvited[] = $friend;
                        }
                        else
                        {
                            $iCountInvited++;
                        }
                    }

                    $friends = $aUninvited;

                    foreach ($friends as $i => $value)
                    {
                        $iCount++;
                        $friends[$i]['pic'] = sprintf($imgLink, $value['id']);
                    }
                    $_SESSION['contactimporter']['facebook'] = $friends;
                    $_SESSION['contactimporter']['facebook']['total_friends'] = $iCount;
                }
                else
                {
                    $friends = $_SESSION['contactimporter']['facebook'];
                }
                $friends = array_slice($friends, $iOffset, $iLimit);
                $aIds = array();
                foreach ($friends as $friend)
                {
                    $aIds[] = $friend['id'];
                }
                $friends = $this->processSocialRows($friends);
                $aJoineds = Phpfox::getService('contactimporter')->checkSocialJoined($aIds);
                return array($friends, $aJoineds,$iCountInvited);
            }
            catch (FacebookApiException $e)
            {
                Phpfox_Error::set($e);
            }
        }
        return array(array(),array(),0);
    }

    public function sendInvitation($iId, $sMessage, $sLink)
    {
        $aSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('facebook');
        $aSetting['api_params'] = unserialize($aSetting['api_params']);
        $this->_oFacebook = $oFacebook = new YNCFacebook(array(
                    'appId' => $aSetting['api_params']['appid'],
                    'secret' => $aSetting['api_params']['secret'],
                    'cookie' => true,
                ));
        if ($this->_oFacebook)
        {
            if ($iId)
            {
                //$sName = _p('you_are_invited_to_message_from_host', array('host' => phpfox::getParam('core.path'), 'link' => $sLink));
                $sName = _p('you_are_invited_to_message_from_host', array('host' => phpfox::getParam('core.path'), 'link' => phpfox::getParam('core.path')));
                $sPicture = Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/item.png';
                if (isset($_SESSION['fbConfig']['pic']) && $_SESSION['fbConfig']['pic'])
                {
                    $sPicture = phpfox::getParam('photo.url_photo') . $_SESSION['fbConfig']['pic'];
                }
                $param = array(
                    'picture' => $sPicture,
                    'message' => $sMessage,
                    'link' => $sLink,
                    'name' => $sName,
                    'caption' => Phpfox::getParam('core.path'),
                    'description' => Phpfox::getParam('core.global_site_title')
                );
                try
                {
                    $iReturn = $this->_oFacebook->api('/' . $iId . '/feed', 'POST', $param);
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