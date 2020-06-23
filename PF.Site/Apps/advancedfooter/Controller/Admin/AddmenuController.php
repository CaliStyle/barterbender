<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddmenuController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $bIsEdit = false;
        $bIsSub = false;
        $aLanguages = Phpfox::getService('language')->getAll(true);
        $iEditId = $this->request()->getInt('edit');
        $iSubEditId = $this->request()->getInt('sub');
        if ($iSubEditId) {
            $bIsSub = true;
            $iEditId = $iSubEditId;
        }

        if ($iEditId) {
            $aRow = Phpfox::getService('advancedfooter.menu')->getForEdit($iEditId);

            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($bIsEdit) {
                if (Phpfox::getService('advancedfooter.menu')->updateMenu($iEditId, $aVals)) {
                    if ($bIsSub) {
                        $this->url()->send('admincp.advancedfooter.index',false ,
                            _p('Successfully updated a new menu icon'));
                    } else {
                        $this->url()->send('admincp.advancedfooter.index',false ,
                            _p('Successfully updated a new menu'));
                    }
                }
            } else {
                if (Phpfox::getService('advancedfooter.menu')->addMenu($aVals)) {
                    $this->url()->send('admincp.advancedfooter.index',false ,
                        _p('Successfully created a new menu'));
                }
            }
        }

        $this->template()
            ->setTitle($bIsEdit ? _p('Edit Menu') : _p('Add Menu'))
            ->setBreadCrumb($bIsEdit ? _p('Edit Menu') : _p('Add Menu'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('advancedfooter.menu')->getForAdmin(0, 0),
                    'aLanguages' => $aLanguages
                ]
            );
    }
}
