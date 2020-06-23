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

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'abstract.class.php');

class Contactimporter_Service_LinkedIn extends Contactimporter_Service_Abstract
{

    public function getFriends($iPage = 1, $iLimit = 50)
    {
        $iCnt = 0;
        $iCountInvited = 0;
        $aErrors = $aRows = $aMails = $aInviteList = array();
        if (isset($_SESSION['contactimporter']['linkedin']) && $_SESSION['contactimporter']['linkedin'])
        {
            $aContacts = $_SESSION['contactimporter']['linkedin'];
        }
        else
        {
            $params = "lType=getconnections";
            $params .="&oauth_tok3n=" . $_SESSION['token'];
            $params .="&oauth_token_secret=" . $_SESSION['secret_token'];
            $ch = curl_init("https://openid.younetid.com/auth/contact.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);  // DO NOT RETURN HTTP HEADERS
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
            $return_Data = curl_exec($ch);
            $index = strpos($return_Data, 'returnConnnetions=');
            if ($index < 0)
            {
                return array();
            }
            else
            {
                $datastr = substr($return_Data, $index + strlen('returnConnnetions='));
                $array_data = explode(',', $datastr);

                $aInviteds = Phpfox::getService('contactimporter')->getInviteds();
                
                $count = count($array_data) - 1;
                $aContacts = array();
                for ($i = 0; $i < $count - 1; $i+=6)
                {
                    if (!in_array($array_data[$i + 1], $aInviteds))
                    {
                        if(!empty( $array_data[$i + 1]))
                        {
                            $aContacts[$array_data[$i + 1]] = array('id' => $array_data[$i + 1], 'name' => $array_data[$i + 3], 'pic' => $array_data[$i + 5]);
                        }
                    }
                    else
                    {
                        $iCountInvited++;
                    }
                }
            }

            $_SESSION['contactimporter']['linkedin'] = $aContacts;
        }
        $iCnt = count($aContacts);
        
        $iOffset = ($iPage - 1) * $iLimit;
        $aContacts = array_slice($aContacts, $iOffset, $iLimit);
        $aIds = array();
        $aInviteList = array();
        foreach ($aContacts as $aContact)
        {
            $aInviteList[] = $aContact;
            $aIds[] = $aContact['id'];
        }
        $aJoineds = Phpfox::getService('contactimporter')->checkSocialJoined($aIds);
        $aInviteList = $this->processSocialRows($aInviteList);
        return array($iCnt, $aInviteList, $aJoineds, $aErrors,$iCountInvited);
    }

    public function sendInvitation($iId, $sMessage, $sLink)
    {
        $iUserId = Phpfox::getUserId();
        $aUser = Phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('user'))
                ->where('user_id = ' . $iUserId)
                ->execute('getRow');
        $aMailSettings = array(
            'displayname' => $aUser['full_name'],
            'message' => $sMessage,
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . '/invite/id_' . $iUserId . '/',
            'header' => '',
            'footer' => '',
            'sender_email' => $aUser['email'],
            'host' => $_SERVER['HTTP_HOST']
        );
        $aMessage = array(
            'subject' => $aUser['full_name'] . ' ' . _p('is_inviting_you_to') . ' ' . $aMailSettings['host'],
            'body' => $aUser['full_name'] . ' ' . _p('is_inviting_you_to') . ' ' . $aMailSettings['host'] . "\n\r " . _p('to_join_please_follow_the_link') . "\n\r " . $sLink . "\n\r " . _p('message') . ": " . $aMailSettings['message']
        );
        $params = "lType=message";
        $params .= "&0=" . $iId;
        $params .="&message_body=" . $aMessage['body'];
        $params .="&message_subject=" . $aMessage['subject'];
        $params .="&oauth_tok3n=" . $_SESSION['token'];
        $params .="&oauth_token_secret=" . $_SESSION['secret_token'];
        $ch = curl_init("https://openid.younetid.com/auth/contact.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
        curl_exec($ch);
    }

}

?>