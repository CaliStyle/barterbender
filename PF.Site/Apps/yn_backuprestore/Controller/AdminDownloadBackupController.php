<?php
/**
 * User: huydnt
 * Date: 09/01/2017
 * Time: 17:25
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminDownloadBackupController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iId = $this->request()->get('id', $this->request()->get('req4'));
        $aBackup = Phpfox::getService('ynbackuprestore.backup')->getBackup($iId);

        if (!$aBackup) {
            return;
        }
        Phpfox::getService('ynbackuprestore.backup')->addDownloadDestination($iId);
        $sFilePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . $aBackup['title'] . "." . $aBackup['archive_format'];
        if ($aBackup['archive_format'] == 'zip') {
            $type = 'zip';
        } else {
            $type = 'octet-stream';
        }
        header("Content-Disposition: attachment; filename=" . basename($sFilePath));
        header("Content-type: application/$type");
        ob_clean();
        readfile("$sFilePath");

        exit;
    }
}