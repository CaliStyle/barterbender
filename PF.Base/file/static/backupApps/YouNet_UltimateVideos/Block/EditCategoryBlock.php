<?php

/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class EditCategoryBlock extends \Phpfox_Component
{

    public function process()
    {
        $bIsEdit = false;
        $iEditId = 0;
        $selectBox = '';
        if ($this->getParam('iCategoryId')) {
            $iEditId = $this->getParam('iCategoryId');

            if ($aCategory = Phpfox::getService('ultimatevideo.category')->getForEdit($iEditId)) {
                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript">$(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);});</script>')->assign('aForms', $aCategory);
                $selectBox = Phpfox::getService('ultimatevideo.multicat')->getSelectBox(array('id' => '', 'name' => 'val[parent_id]', 'class' => 'form-control'), $aCategory['parent_id'], $iEditId, null);
            }
        }
        $aLanguages = Phpfox::getService('language')->getAll();
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $this->template()->setTitle(_p('edit_a_category'))
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.ultimatevideo.category.add'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('ultimatevideo.category')->display('option')->get($iEditId),
                    'bIsEdit' => $bIsEdit,
                    'corePath' => $corePath,
                    'selectBox' => !empty($selectBox) ? $selectBox : "",
                    'aLanguages' => $aLanguages
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_category_add_clean')) ? eval($sPlugin) : false);
    }
}
