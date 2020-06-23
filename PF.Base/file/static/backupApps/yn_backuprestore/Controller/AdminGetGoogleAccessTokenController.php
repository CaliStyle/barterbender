<?php
/**
 * User: huydnt
 * Date: 11/01/2017
 * Time: 09:01
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Apps\yn_backuprestore\Adapter\Googledrive;
use Phpfox;

class AdminGetGoogleAccessTokenController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        if (!isset($_SESSION)) {
            session_start();
        }

        $aDestination = Phpfox::getService('ynbackuprestore.destination')->getDestinationById($_SESSION['google_destination_id']);
        $aParams = json_decode($aDestination['params'], true);
        $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
            $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'));
        if (isset($_GET['code'])) {
            // save to session
            $aParams['access_token'] = $oGoogle->getAccessToken($_GET['code']);
            Phpfox::getService('ynbackuprestore.destination')->updateParams($_SESSION['google_destination_id'], $aParams);
        }

        header('Location: ' . $_SESSION['google_callback']);
    }
}