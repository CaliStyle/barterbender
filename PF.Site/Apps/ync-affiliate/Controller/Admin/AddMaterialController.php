<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:43
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddMaterialController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('idMaterial'))) {
            $aRow = Phpfox::getService('yncaffiliate.materials')->getMaterialById($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms'  => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($this->_validate($aVals))
            {
                if ($bIsEdit) {
                    if (Phpfox::getService('yncaffiliate.materials.process')->update($aVals,$iEditId)) {
                        $this->url()->send('admincp.yncaffiliate.affiliate-materials', _p('successfully_updated_material'));
                    }
                } else {
                    if (Phpfox::getService('yncaffiliate.materials.process')->add($aVals)) {
                        $this->url()->send('admincp.yncaffiliate.affiliate-materials', _p('successfully_added_material'));
                    }
                }
            }
        }

        if ($bIsEdit)
        {
            $this->template()->setTitle(_p('edit_code'))
                ->setBreadCrumb(_p('edit_code'));
        }
        else
        {
            $this->template()->setTitle(_p('add_code'))
                ->setBreadCrumb(_p('add_code'));
        }
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-affiliate';
        $this->template()->assign([
                'bIsEdit' => $bIsEdit,
                'corePath' => $corePath,
            ]
        );
    }

    public function _validate($aVals)
    {
        $bIsFail = false;
        if (empty($aVals['material_width']) || !is_numeric($aVals['material_width'])) {
            Phpfox_Error::set(_p('please_enter_a_valid_number_to_width_field'));
            $bIsFail = true;
        }
        if (empty($aVals['material_height']) || !is_numeric($aVals['material_height'])) {
            Phpfox_Error::set(_p('please_enter_a_valid_number_to_height_field'));
            $bIsFail = true;
        }
        if (empty($aVals['material_name'])) {
            Phpfox_Error::set(_p('title_is_required'));
            $bIsFail = true;
        }
        if (empty($aVals['link'])) {
            Phpfox_Error::set(_p('link_is_required'));
            $bIsFail = true;
        }
        elseif(strpos($aVals['link'],Phpfox::getParam('core.path')) === false){
            Phpfox_Error::set(_p('please_enter_link_within_this_domain'));
            $bIsFail = true;
        }
        if($bIsFail)
        {
            $this->template()->assign([
                'aForms' => $aVals
            ]);
        }
        return Phpfox_Error::isPassed();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynafiliate.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}