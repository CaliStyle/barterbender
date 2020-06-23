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

class Contactimporter_Service_Yahoo extends Contactimporter_Service_Abstract
{

    private function _prepareRows($aRows = array())
    {
        
    }

    public function get($iPage = 1, $iLimit = 50)
    {
        $iCnt = 0;
        $aErrors = $aRows = $aMails = $aInviteList = array();
        if (isset($_SESSION['contactimporter']['yahoo']) && $_SESSION['contactimporter']['yahoo'])
        {
            $aContacts = $_SESSION['contactimporter']['yahoo'];
        }
        else
        {
            $aContacts = isset($_REQUEST['contact']) ? json_decode(urldecode($_REQUEST['contact'])) : null;
            $_SESSION['contactimporter']['yahoo'] = $aContacts;
        }
        $iOffset = ($iPage - 1) * $iLimit;
        $aRows = $aMails = $aInviteList = array();
        if (!$aContacts || count($aContacts) <= 0)
        {
            return array(0, null, null, null);
        }
        foreach ($aContacts as $i => $aContact)
        {
            if (!in_array($aContact->email, $aMails))
            {
                $aRows[$i]['name'] = $aContact->name;
                $aRows[$i]['email'] = $aContact->email;
                $aMails[] = $aRows[$i]['email'];
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
            else
            {
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
        return array($iCnt, $aInviteList, $aJoined, $aErrors, $iCountInvited);
    }

}

?>