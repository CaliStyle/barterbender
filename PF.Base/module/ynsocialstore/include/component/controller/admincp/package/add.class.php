<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 9/30/16
 * Time: 5:56 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Admincp_Package_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;

        $aValidation = array(
            'name' => array(
                'def' => 'required',
                'title'=> _p('name_of_package_cannot_be_empty')
            ),
            'expire_number' => array(
                'def' => 'required',
                'title'=> _p('valid_period_cannot_be_empty')
            ),
            'fee' => array(
                'def' => 'required',
                'title'=> _p('package_fee_cannot_be_empty')
            ),
            'max_products' => array(
                'def' => 'required',
                'title'=> _p('maximum_number_of_product_cannot_be_empty')
            ),
        );

        $oValid = Phpfox::getLib('validator') -> set(array(
            'sFormName' => 'js_add_package_form',
            'aParams' => $aValidation,
        ));

        if ($iEditId = $this->request()->getInt('id')) {
            if ($aPackage = Phpfox::getService('ynsocialstore.package') -> getById($iEditId)) {
                $bIsEdit = true;
                $this->template()->assign(array(
                    'aForms' => $aPackage,
                ));
//                die(d($aPackage));
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($oValid->isValid($aVals)) {

                if ($bIsEdit) {
                    if (Phpfox::getService('ynsocialstore.package.process')->update($aPackage['package_id'],$aVals)) {
                        $this->url()->send('admincp.ynsocialstore.package.add', array('id' => $aPackage['package_id']), _p('package_successfully_updated'));
                    } else {
                        $aVals['package_id'] = 	$aPackage['package_id'];
                        $this->template()->assign(array(
                            'aForms' => $aVals,
                        ));
                    }
                } else {
                    if (Phpfox::getService('ynsocialstore.package.process')->add($aVals)) {
                        $this->url()->send('admincp.ynsocialstore.package.add', null, _p('package_successfully_added'));
                    } else {
                        $this->template()->assign(array(
                            'aForms' => $aVals,
                        ));
                    }
                }
//                die(d($aVals));
            } else {
                if ($bIsEdit) {
                    $aVals['package_id'] = $aPackage['package_id'];
                }
                $this->template()->assign(array(
                    'aForms' => $aVals,
                ));
//                die(d($aVals));
            }
        }

        $aThemes = Phpfox::getService('ynsocialstore')->getAllThemes();
        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sDefaultSymbol = Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency);
        $aCurrentSupportedModules = array('events', 'photos', 'videos');
        $aAssign = array(
            'bIsEdit' => $bIsEdit,
            'sCreateJs' => $oValid->createJS(),
            'aThemes' => $aThemes,
            'sDefaultSymbol' => $sDefaultSymbol,
            'core_path' => Phpfox::getParam('core.path_file'),
            'aCurrentSupportedModules' => $aCurrentSupportedModules,
        );

        $this->template()->setHeader(($bIsEdit ? _p('edit_a_package') : _p('create_a_new_package')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ynsocialstore'), $this->url()->makeUrl('admincp.app').'?id=__module_ynsocialstore')
            ->setBreadCrumb(($bIsEdit ? _p('edit_a_package') : _p('create_a_new_package')), $this->url()->makeUrl('admincp.ynsocialstore.package.add'))
            ->assign($aAssign);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }
}
?>