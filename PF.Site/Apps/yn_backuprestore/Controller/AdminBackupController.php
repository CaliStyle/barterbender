<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 14:06
 */

namespace Apps\yn_backuprestore\Controller;


use Phpfox;

class AdminBackupController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $aDestinations = $oDestinationService->getDestinations();
        $oBackupService = Phpfox::getService('ynbackuprestore.backup');
        $aModules = $oBackupService->getModules();
        $aApps = $oBackupService->getApps();
        $aThemes = $oBackupService->getThemes();
        $aUploads = $oBackupService->getUploadFolders();
        $aDatabase = $oBackupService->getDatabaseTables();

        $aEmailsDest = array();
        $aDatabaseDest = array();
        foreach ($aDestinations as $aDestination) {
            if ($aDestination['type_id'] == 2) {
                $aEmailsDest[] = $aDestination['destination_id'];
            }
            if ($aDestination['type_id'] == 5) {
                $aDatabaseDest[] = $aDestination['destination_id'];
            }
        }
        $sEmailsDest = null;
        if (count($aEmailsDest)) {
            $sEmailsDest = '"' . implode('","', $aEmailsDest) . '"';
        }
        $sDatabaseDest = null;
        if (count($aDatabaseDest)) {
            $sDatabaseDest = '"' . implode('","', $aDatabaseDest) . '"';
        }

        $oRequest = $this->request();
        if ($aVal = $oRequest->getArray('val')) {
            $bIsValid = true;
            $aPlugins = $oRequest->getArray('plugin');
            $aThemes = $oRequest->getArray('theme');
            $aUploads = $oRequest->getArray('upload');
            $aDatabase = $oRequest->getArray('database');

            // prefix is required
            if (!(isset($aVal['prefix']) && $aVal['prefix'])) {
                $bIsValid = false;
            }
            $aVal['prefix'] = preg_replace('/[^A-Za-z0-9_.-]/', '_', $aVal['prefix']);
            // check include
            if (!count($aPlugins) && !count($aThemes) && !count($aUploads) && !count($aDatabase)) {
                $bIsValid = false;
            }

            // if params is valid, save backup
            if ($bIsValid) {
                $aVal['maintenance_mode'] = ($aVal['maintenance_mode'] == 'yes') ? 1 : 0;
                if ($iId = $oBackupService->addBackup($aVal, array_keys($aPlugins), array_keys($aThemes),
                    array_keys($aUploads), array_keys($aDatabase), 'manual')
                ) {
                    if (!$_SESSION) {
                        session_start();
                    }
                    $_SESSION['backup_id'] = $iId;
                    header('Location: ' . $this->url()->makeUrl('admincp.ynbackuprestore.authorize'));
                    exit();
                }
            }
        }
        $sOffline = _p('ynbackuprestore_change_to_offline',
            ['url' => $this->url()->makeUrl('admincp.setting.edit', ['group-id' => 'site_offline_online'])]);
        $this->template()
            ->setBreadCrumb(_p('Backup Now'), $this->url()->makeUrl('admincp.ynbackuprestore.backup'))
            ->assign([
            'sOffline'      => $sOffline,
            'aDestinations' => $aDestinations,
            'aModules'      => $aModules,
            'aApps'         => $aApps,
            'aThemes'       => $aThemes,
            'aUploads'      => $aUploads,
            'aDatabase'     => $aDatabase,
            'sEmailsDest'   => $sEmailsDest,
            'sDatabaseDest' => $sDatabaseDest,
            'sAssetsDir'    => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
        ]);
    }
}