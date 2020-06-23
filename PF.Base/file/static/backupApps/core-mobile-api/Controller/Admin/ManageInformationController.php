<?php

namespace Apps\Core_MobileApi\Controller\Admin;

use Phpfox;
use Phpfox_Component;

class ManageInformationController extends Phpfox_Component
{
    public function process()
    {

        list($sLogo, $bIsDefault) = Phpfox::getService('mobile.admincp.setting')->getAppLogo();

        if ($aVals = $this->request()->getArray('val')) {
            //Update logo
            if (Phpfox::getService('mobile.admincp.setting')->updateLogo()) {
                $this->url()->send('admincp.mobile.manage-information', _p('logo_updated_successfully'));
            }
        }
        if ($this->request()->get('delete')) {
            if (Phpfox::getService('mobile.admincp.setting')->deleteLogo()) {
                $this->url()->send('admincp.mobile.manage-information', _p('logo_removed_successfully'));
            }
        }
        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Mobile Api"), $this->url()->makeUrl('admincp.app', ['id' => 'Core_MobileApi']))
            ->setBreadCrumb(_p('manage_information'), $this->url()->makeUrl('admincp.mobile.manage-information'))
            ->setTitle(_p('manage_information'))
            ->assign([
                'sLogo'      => $sLogo,
                'bIsDefault' => $bIsDefault
            ]);
    }
}