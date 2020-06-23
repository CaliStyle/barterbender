<?php
/**
 * User: huydnt
 * Date: 10/01/2017
 * Time: 17:26
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Apps\yn_backuprestore\Adapter\Googledrive;
use Apps\yn_backuprestore\Adapter\Onedrive;
use Phpfox;

class AdminAuthorizeController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        if (!isset($_SESSION)) {
            session_start();
        }

        $oBackupService = Phpfox::getService('ynbackuprestore.backup');
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $iId = $_SESSION['backup_id'];

        $aOneDriveDest = $oBackupService->getDestinations($iId, [
            'type_id' => 8
        ]);
        $aGoogleDest = $oBackupService->getDestinations($iId, [
            'type_id' => 9
        ]);

        // authorize google if needed
        foreach ($aGoogleDest as $aItem) {
            $aParams = json_decode($aItem['params'], true);
            $_SESSION['google_callback'] = $this->url()->makeUrl('admincp.ynbackuprestore.authorize');
            // if not authorize yet, need authorize
            if (!isset($aParams['access_token'])) {
                $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                    $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'));
                $oGoogle->authorize();
                exit();
            } else {
                $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                    $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'),
                    $aParams['access_token']);
                if ($oGoogle->isAccessTokenExpired()) {
                    // Case 2.2
                    if (!isset($aParams['access_token']['refresh_token'])) {
                        $oGoogle->authorize();
                        exit();
                    } else {
                        $aAccessToken = $oGoogle->refreshToken();
                        if ($aAccessToken === false) {
                            $oGoogle->authorize();
                            exit();
                        }
                        // update access token
                        $aParams['access_token'] = $aAccessToken;
                        $oDestinationService->updateParams($aItem['destination_id'], $aParams);
                    }
                }
            }
        }

        // authorize onedrive if needed
        foreach ($aOneDriveDest as $aItem) {
            $aParams = json_decode($aItem['params'], true);
            $_SESSION['onedrive_callback'] = $this->url()->makeUrl('admincp.ynbackuprestore.authorize');
            // if not authorize yet, need authorize
            if (!isset($aParams['access_token'])) {
                $oOneDrive = new Onedrive($aParams['onedrive_id']);
                $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                exit();
            } else {
                $oState = json_decode($aItem['params'])->access_token;
                $oOneDrive = new Onedrive($aParams['onedrive_id'], $oState);
                if (isset($oState->token->data->refresh_token)) {
                    $oOneDrive->renewAccessToken($aParams['onedrive_secret']);
                    $access_token = $oOneDrive->getState();
                    if (!isset($access_token->token->data->error)) {
                        $aParams['access_token'] = $access_token;
                        $oDestinationService->updateParams($aItem['destination_id'], $aParams);
                    } else {
                        // if refresh fail
                        $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                        exit();
                    }
                } else {
                    $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                    exit();
                }
            }
        }

        // complete authorize, go to backup process
        header('Location: ' . \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.process-backup',
                ['backup_id' => $iId]));
    }
}