<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:40
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;

class AffiliateMaterialsController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $this->template()->setTitle(_p('Manage Codes'))
            ->setBreadCrumb(_p('Manage Codes'));
        $aMaterials = Phpfox::getService('yncaffiliate.materials')->getMaterials([]);
        if($iId = $this->request()->getInt('delete'))
        {
            if(Phpfox::getService('yncaffiliate.materials.process')->delete($iId))
            {
                $this->url()->send('admincp.yncaffiliate.affiliate-materials',_p('material_deleted_successfully'));
            }
        }
        $this->template()->assign([
            'aMaterials' => $aMaterials
        ]);
    }
}