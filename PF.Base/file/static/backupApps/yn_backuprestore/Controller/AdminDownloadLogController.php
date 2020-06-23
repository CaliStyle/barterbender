<?php
/**
 * User: huydnt
 * Date: 09/01/2017
 * Time: 17:25
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminDownloadLogController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $aBackup = Phpfox::getService('ynbackuprestore.backup')->getBackup($this->request()->get('id'));
        if (!$aBackup) {
            return;
        }
        $sFilePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . 'logfile.txt';
        header("Content-Disposition: attachment; filename=" . basename($sFilePath));
        header("Content-type: text/plain");
        ob_clean();
        readfile("$sFilePath");

        exit;
    }
}