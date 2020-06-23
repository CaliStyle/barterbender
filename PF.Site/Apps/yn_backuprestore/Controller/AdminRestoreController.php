<?php
/**
 * User: huydnt
 * Date: 17/01/2017
 * Time: 10:15
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminRestoreController extends Admincp_Component_Controller_App_Index
{
    protected $_sRestoreDir;
    public function __construct(array $aParams)
    {
        $this->_sRestoreDir = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS;
    }

    public function process()
    {
        parent::process();

        if ($aVal = $this->request()->getArray('val')) {
            // upload file to server
            if (isset($_FILES['backup_file']['name']) && $_FILES['backup_file']['name'] != '') {
                /**
                 * Using pure PHP Upload because Phpfox_File have bug
                 * when upload file with extension tar.gz, tar.bz2 (only get last extension)
                 */
                if (!is_dir($this->_sRestoreDir)) {
                    mkdir($this->_sRestoreDir);
                }
                $fileName = basename($_FILES["backup_file"]["name"]);
                $filePath = $this->_sRestoreDir . $fileName;
                $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
                /**
                 * Check extension supported
                 * Zip, Tar, Tar.gz, Tar.bz2
                 */
                if ($fileType == 'zip' || $fileType == 'tar'
                    || ($fileType == 'gz' && substr($fileName, strlen($fileName) - 7, strlen($fileName)) == '.tar.gz')
                    || ($fileType == 'bz2' && $fileType = substr($fileName, strlen($fileName) - 8,
                                strlen($fileName)) == '.tar.bz2')
                ) {
                    if (move_uploaded_file($_FILES["backup_file"]["tmp_name"], $filePath)) {
                        if (isset($aVal['maintenance_mode']) && $aVal['maintenance_mode']) {
                            // turn to maintenance mode
                            Phpfox::getService('admincp.setting.process')->update([
                                'value' => [
                                    'site_is_offline' => 1
                                ]
                            ]);
                            \Phpfox_Cache::instance()->remove();
                        }
                        $this->url()->send('admincp.ynbackuprestore.process-restore', ['file' => $fileName]);
                    }
                } else {
                    \Phpfox_Error::display(_p('Backup file is invalid.'));
                }
            }
        }

        $sOffline = _p('ynbackuprestore_change_to_offline',
            ['url' => $this->url()->makeUrl('admincp.setting.edit', ['group-id' => 'site_offline_online'])]);
        $this->template()
            ->setBreadCrumb(_p('Restore Now'), $this->url()->makeUrl('admincp.ynbackuprestore.restore'))
            ->assign([
                'sMaxUploadFileSize' => ini_get('upload_max_filesize'),
                'sOffline'           => $sOffline
            ]);
    }
}