<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class AddCustomfieldController extends Phpfox_Component
{
    public function process()
    {
        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('id'))) {

            $aField = Phpfox::getService('advancedmarketplace.custom.group')->getFieldForCustomEdit($iEditId);


            if (isset($aField['field_id'])) {
                $bIsEdit = true;
                $this->template()->assign(array(
                        'aForms' => $aField
                    )
                );

                if (isset($aField['option']) && $aField['var_type'] == 'select') {
                    $bHideOptions = false;
                }
            }
        } else {
            $this->template()->assign(array('aForms' => array()));
        }

        if (($aVals = $this->request()->getArray('val'))) {

            $aVals['module_id'] = 'advancedmarketplace';
            $aVals['product_id'] = 'younet_advmarketplace4';
            if ($bIsEdit) {

                if (Phpfox::getService('advancedmarketplace.custom.process')->updateField($aField['field_id'],
                    $aVals)) {
                    $this->url()->send('admincp.advancedmarketplace.addcustomfield', array('id' => $aField['field_id']),
                        _p('custom.field_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('advancedmarketplace.custom.process')->addCustomField($aVals)) {
                    $this->url()->send('admincp.advancedmarketplace.addcustomfield', null,
                        _p('custom.field_successfully_added'));
                }
            }

            if (isset($aVals['var_type']) && $aVals['var_type'] == 'select') {
                $bHideOptions = false;
                $iCnt = 0;
                $sOptionPostJs = '';
                foreach ($aVals['option'] as $iKey => $aOptions) {
                    if (!$iKey) {
                        continue;
                    }

                    $aValues = array_values($aOptions);
                    if (!empty($aValues[0])) {
                        $iCnt++;
                    }

                    foreach ($aOptions as $sLang => $mValue) {
                        $sOptionPostJs .= 'option_' . $iKey . '_' . $sLang . ': \'' . str_replace("'", "\'",
                                $mValue) . '\',';
                    }
                }
                $sOptionPostJs = rtrim($sOptionPostJs, ',');
                $iDefaultSelect = $iCnt;
            }
        }
        $sCategories = Phpfox::getService('advancedmarketplace.category')->get();
        $this->template()->setHeader(array(
            'jscript/admin.js' => 'app_p-advmarketplace',
            'custom.js' => 'module_advancedmarketplace',
            '<script type="text/javascript"> var bIsEdit = ' . ($bIsEdit ? 'true' : 'false') . '</script>',
            '<script type="text/javascript">$(function(){$Core.init(' . ($bIsEdit == true ? 1 : $iDefaultSelect) . '' . (isset($sOptionPostJs) ? ', {' . $sOptionPostJs . '}' : '') . ');});</script>'
        ))
            ->assign(array(
                'sCategories' => $sCategories,
                'bHideOptions' => $bHideOptions,
                'aLanguages' => Phpfox::getService('language')->getAll(),
                'bIsEdit' => $bIsEdit,
                'aGroups' => Phpfox::getService('advancedmarketplace.custom.group')->get(),
            ))
            ->setBreadcrumb('Add Custom Field', $this->url()->makeUrl('admincp.marketplace.addcustomfield'));
    }
}
