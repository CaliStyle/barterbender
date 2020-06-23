<?php
/**
 * User: huydnt
 * Date: 09/01/2017
 * Time: 08:52
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminProcessBackupController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iId = $this->request()->getInt('backup_id');
        $this->template()->assign([
            'id'         => $iId,
            'sUrl'       => \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.download-log') . $iId,
            'sAssetsDir' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
        ]);
    }
}