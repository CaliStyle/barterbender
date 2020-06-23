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

class AddsocialController extends Admincp_Component_Controller_App_Index
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
            $aRow = Phpfox::getService('advancedfooter.social')->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($bIsEdit) {
                if (Phpfox::getService('advancedfooter.social')->updateSocial($iEditId, $aVals)) {
                    if ($bIsSub) {
                        $this->url()->send('admincp.advancedfooter.social',false ,
                            _p('Successfully updated a new social icon'));
                    } else {
                        $this->url()->send('admincp.advancedfooter.social',false ,
                            _p('Successfully updated a new social'));
                    }
                }
            } else {
                if (Phpfox::getService('advancedfooter.social')->addSocial($aVals)) {
                    $this->url()->send('admincp.advancedfooter.social',false ,
                        _p('Successfully created a new social'));
                }
            }
        }

        $this->template()
            ->setTitle($bIsEdit ? _p('Edit Social Icon') : _p('Add Social Icon'))
            ->setBreadCrumb($bIsEdit ? _p('Edit Social Icon') : _p('Add Social Icon'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('advancedfooter.social')->getForAdmin(0, 0),
                    'aSocial' => Phpfox::getService('advancedfooter.social')->getSocial(),
                    'aLanguages' => $aLanguages
                ]
            );
    }
}
