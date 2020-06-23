<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 30/12/2016
 * Time: 18:08
 */
namespace Apps\YNC_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Phpfox_Component
{
    public function process()
    {
        $bIsEdit = false;
        $bIsSub = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if (($iSubtEditId = $this->request()->getInt('sub'))) {
            $aRow = Phpfox::getService('ynblog.category')->getForEdit($iSubtEditId);
            $iEditId = $iSubtEditId;
            $bIsEdit = true;
            $bIsSub = true;
            $this->template()->assign([
                    'aForms'  => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }
        elseif (($iEditId = $this->request()->getInt('id'))) {
            $aRow = Phpfox::getService('ynblog.category')->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('ynblog.process')->updateCategory($iEditId, $aVals)) {
                        if ($bIsSub) {
                            $this->url()->send('admincp.app', ['id' => 'YNC_Blogs', 'val[sub]' => $aVals['parent_id']], _p('successfully_updated_a_new_category'));
                        } else {
                            $this->url()->send('admincp.app', ['id' => 'YNC_Blogs'], _p('successfully_updated_a_new_category'));
                        }
                    }
                } else {
                    if (Phpfox::getService('ynblog.process')->addCategory($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'YNC_Blogs'], _p('successfully_created_a_new_category'));
                    }
                }
            }
        }

        $this->template()->setTitle(_p('add_category'))
            ->setBreadCrumb(_p('add_category'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(0, 1, 0, $iEditId),
                    'aLanguages' => $aLanguages
                ]
            );
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

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}