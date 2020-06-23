<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class AddCustomgroupController extends Phpfox_Component
{
    public function process()
    {
        phpfox::isUser(true);
        $bIsEdit = false;
        $sCategories = Phpfox::getService('advancedmarketplace.category')->get();

        if (($iEditId = $this->request()->getInt('id'))) {
            if (($aGroup = Phpfox::getService('advancedmarketplace.custom.group')->getGroupForEdit($iEditId)) && isset($aGroup['group_id'])) {
                $bIsEdit = true;
                $this->template()->assign(array(
                        'aForms' => $aGroup
                    )
                );
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {
            $aVals['module_id'] = 'advancedmarketplace';
            $aVals['product_id'] = 'younet_advmarketplace4';
            if ($bIsEdit === true) {
                if (Phpfox::getService('advancedmarketplace.custom.process')->updateGroup($aGroup['group_id'],
                    $aVals)) {
                    $this->url()->send('admincp.advancedmarketplace.addcustomgroup', array('id' => $aGroup['group_id']),
                        _p('custom.group_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('advancedmarketplace.custom.process')->addCustomGroup($aVals)) {
                    $this->url()->send('admincp.advancedmarketplace.addcustomgroup', null,
                        _p('custom.group_successfully_added'));
                }
            }
        }

        $this->template()->setHeader(array(
            'jscript/add.js' => 'app_p-advancedmarketplace'
        ))->assign(array(
            'sCategories' => $sCategories,
            'bIsEdit' => $bIsEdit
        ))->setBreadcrumb('Add Custom Group', $this->url()->makeUrl('admincp.advancedmarketplace.addcustomgroup'));
    }
}
