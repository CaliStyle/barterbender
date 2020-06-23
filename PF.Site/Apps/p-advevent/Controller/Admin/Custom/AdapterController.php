<?php
namespace Apps\P_AdvEvent\Controller\Admin\Custom;

use Phpfox;
use Phpfox_Component;

class AdapterController extends Phpfox_Component
{
    public function process()
    {
        $request = $this->request();
        if($request->get('req3') == 'custom') {
            $req4 = $request->get('req4');
            if(empty($req4) || $req4 == 'index') {
                return Phpfox::getLib('module')->setController('fevent.admincp.custom.index');
            }
            elseif ($req4 == 'add') {
                return Phpfox::getLib('module')->setController('fevent.admincp.custom.add');
            }
        }

        return false;
    }
}