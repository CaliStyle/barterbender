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

if (file_exists(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'openinviter' . PHPFOX_DS . 'openinviter.php'))
{
    require_once(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'openinviter' . PHPFOX_DS . 'openinviter.php');
}
require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'abstract.class.php');

class Contactimporter_Service_OpenInviter extends Contactimporter_Service_Abstract
{
    public function get($sProvider, $iPage = 1, $iLimit = 50)
    {
        $iCnt = 0;
        $aErrors = $aRows = $aMails = $aInviteList = $aJoined = array();
        if (!isset($_SESSION['contactimporter'][$sProvider]) || 1)
        {
            $sEmail = isset($_POST['email_box']) ? $_POST['email_box'] : '';
            $sPassword = isset($_POST['password_box']) ? $_POST['password_box'] : '';
            $api = 'https://openid.younetid.com/phpfox-contact/open_inviter.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "provider_box={$sProvider}&email_box={$sEmail}&password_box={$sPassword}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $aContacts = json_decode($server_output);
            $_SESSION['contactimporter'][$sProvider] = $aContacts;
        }
        else
        {
            $aContacts = $_SESSION['contactimporter'][$sProvider];
        }
        if (count($aContacts) <= 0)
        {
            $aErrors['contacts'] = _p('there_is_not_contact_in_your_account');
        }
        if (!$aErrors)
        {
            $iOffset = ($iPage - 1) * $iLimit;
            $aRows = $aMails = $aInviteList = array();
            if (!$aContacts || count($aContacts) <= 0)
            {
                return array(0, null, null, null);
            }
            $aRows = array();
            $aInviteds = Phpfox::getService('contactimporter')->getInviteds();
            echoDebug($aInviteds ,true);
            foreach ($aContacts as $sEmail => $sFullName)
            {
                if (!in_array($sEmail, $aInviteds))
                {                                    
                    $aRow['name'] = $sFullName;
                    $aRow['email'] = $sEmail;
                    $aMails[] = $sEmail;
                    $aRows[] = $aRow;
                }
            }            
            list($aMails, $aInvalid, $aJoined) = Phpfox::getService('invite')->getValid($aMails, Phpfox::getUserId());
            $aInviteList = array();
            $iCountInvited = 0;
            foreach ($aRows as $aRow)
            {
                if (in_array($aRow['email'], $aMails))
                {
                    $aInviteList[] = $aRow;
                }
                else{
                    $iCountInvited++;
                }
            }
            $iCnt = count($aInviteList);
            if ($iCnt == 0)
            {
                if ($iCountInvited > 0)
                {
                    $aErrors['contacts'] = _p('you_have_sent_the_invitations_to_all_of_your_friends');
                }
                else
                {
                    $aErrors['contacts'] = _p('there_is_not_contact_in_your_account');
                }
            }
            $aInviteList = array_slice($aInviteList, $iOffset, $iLimit);
            $aInviteList = $this->processEmailRows($aInviteList);
        }
        return array($iCnt, $aInviteList, $aJoined, $aErrors);
    }

}

?>