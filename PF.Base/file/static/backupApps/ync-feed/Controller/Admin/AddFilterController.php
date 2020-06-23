<?php

namespace Apps\YNC_Feed\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddFilterController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();
        if (($iEditId = $this->request()->getInt('edit_id'))) {
            $aRow = Phpfox::getService('ynfeed.filter')->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms'  => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($bIsEdit) {
                if (Phpfox::getService('ynfeed.filter')->updateFilter($iEditId, $aVals)) {
                    $this->url()->send('admincp.ynfeed.manage-filter', null, _p('successfully_updated_a_filter'));
                }
            } else {
                if (Phpfox::getService('ynfeed.filter')->addFilter($aVals)) {
                    $this->url()->send('admincp.ynfeed.manage-filter', null, _p('successfully_created_a_new_filter'));
                }
            }
        }

        $aModules = Phpfox::getService('ynfeed.filter')->getModulesForAddFilter();
        $this->template()->setTitle($iEditId?_p('edit_filter'):_p('add_filter'))
            ->setBreadCrumb($iEditId?_p('edit_filter'):_p('add_filter'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages,
                    'aModules' => $aModules,
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}