<?php
/**
 * User: huydnt
 * Date: 11/01/2017
 * Time: 09:01
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Apps\yn_backuprestore\Adapter\Onedrive;
use Phpfox;

class AdminGetOnedriveAccessTokenController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        if (!isset($_SESSION)) {
            session_start();
        }

        $aDestination = Phpfox::getService('ynbackuprestore.destination')->getDestinationById($_SESSION['onedrive_destination_id']);
        $aParams = json_decode($aDestination['params'], true);
        $oOneDrive = new Onedrive($aParams['onedrive_id'], $_SESSION['onedrive_client_state']);
        if (isset($_GET['code'])) {
            $aParams['access_token'] = $oOneDrive->getAccessToken($aParams['onedrive_secret'], $_GET['code']);
            // save access token
            Phpfox::getService('ynbackuprestore.destination')->updateParams($_SESSION['onedrive_destination_id'], $aParams);
        }
        unset($_SESSION['onedrive_client_state']);
        header('Location: ' . $_SESSION['onedrive_callback']);
    }
}