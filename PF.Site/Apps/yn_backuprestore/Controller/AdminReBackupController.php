<?php
/**
 * User: huydnt
 * Date: 16/01/2017
 * Time: 14:43
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;

class AdminReBackupController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        if (!$iId = $this->request()->get('backup_id', 0)) {
            return;
        }
        $oBackupService = \Phpfox::getService('ynbackuprestore.backup');
        $oDestinationService = \Phpfox::getService('ynbackuprestore.destination');
        $destination_ids = $oDestinationService->getDestinationsByBackupId($iId);
        $ids = array();
        foreach ($destination_ids as $destination_id) {
            $ids[] = $destination_id['destination_id'];
        }

        $aBackup = $oBackupService->getBackup($iId);
        $aVal = [
            'prefix'           => $aBackup['prefix'],
            'maintenance_mode' => $aBackup['maintenance_mode'],
            'archive_format'   => $aBackup['archive_format'],
            'destination_ids'  => $ids
        ];
        $iId = $oBackupService->addBackup($aVal, json_decode($aBackup['plugins_included'], true),
            json_decode($aBackup['themes_included'], true), json_decode($aBackup['uploads_included'], true),
            json_decode($aBackup['database_included'], true), 'manual');
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['backup_id'] = $iId;
        header('Location: ' . $this->url()->makeUrl('admincp.ynbackuprestore.authorize'));
    }
}