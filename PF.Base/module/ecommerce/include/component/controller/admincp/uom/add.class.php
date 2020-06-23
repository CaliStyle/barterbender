<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Uom_Add extends Phpfox_Component
{
    public function process()
    {
        $bIsEdit = false;
        $iMaxLength = 16;
        $aLanguages = Phpfox::getService('language')->getAll();
        if ($iEditId = $this->request()->getInt('id')) {
            if ($aUom = Phpfox::getService('ecommerce.uom')->getForEdit($iEditId)) {
                $bIsEdit = true;

                $this->template()->assign('aForms', $aUom);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('ecommerce.uom.process')->update($aVals)) {
                        $this->url()->send('admincp.ecommerce.uom.add', array('id' => $aUom['uom_id']),
                            _p('uom_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('ecommerce.uom.process')->add($aVals)) {
                        $this->url()->send('admincp.ecommerce.uom.add', null, _p('uom_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('edit_an_uom') : _p('create_a_new_uom')))->setBreadCrumb(_p("Apps"),
            $this->url()->makeUrl('admincp.apps'))->setBreadCrumb(_p('module_ecommerce'),
            $this->url()->makeUrl('admincp.app') . '?id=__module_ecommerce')->setBreadcrumb(($bIsEdit ? _p('edit_an_uom') : _p('create_a_new_uom')),
            $this->url()->makeUrl('admincp.ecommerce.uom.add'))->assign(array(
            'bIsEdit' => $bIsEdit,
            'iMaxLength' => $iMaxLength,
            'aLanguages' => $aLanguages
        ));
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }
}
