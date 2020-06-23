<?php
/**
 * User: huydnt
 * Date: 17/01/2017
 * Time: 10:46
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminProcessRestoreController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        if ($fileName = $this->request()->get('file')) {
            $this->template()
                ->setPhrase(array(
                    'FAILED! WRONG RESTORE FILE!' => _p('FAILED! WRONG RESTORE FILE!')
                ))
                ->assign([
                'file'       => $fileName,
                'sAssetsDir' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
            ]);
        }
    }
}