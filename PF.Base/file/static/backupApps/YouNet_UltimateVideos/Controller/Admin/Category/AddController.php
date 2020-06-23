<?php

/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Controller\Admin\Category;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class AddController extends \Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        if ($this->request()->get('req4') == 'edit' && $this->request()->get('req5')) 
        {
            $iEditId = $this->request()->get('req5');
           
            if ($aCategory = Phpfox::getService('ultimatevideo.category')->getForEdit($iEditId))
            {
                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript">$(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);});</script>')->assign('aForms', $aCategory);
            }
        }
        $aLanguages = \Language_Service_Language::instance()->getAll();
        if ($aVals = $this->request()->getArray('val'))
        {
            if ($bIsEdit || isset($aVals['category_id']))
            {
                if (Phpfox::getService('ultimatevideo.category.process')->update($aVals['category_id'], $aVals))
                {
                    $this->url()->send('admincp.app', [
                        'id' => 'YouNet_UltimateVideos'
                    ],_p('category_successfully_updated'));
                    //$this->url()->send('admincp.ultimatevideo.category.add', array('id' => $aCategory['category_id']), _p('category_successfully_updated'));
                }
            } else
            {
                if (Phpfox::getService('ultimatevideo.category.process')->add($aVals))
                {
                    $this->url()->send('admincp.app', [
                        'id' => 'YouNet_UltimateVideos'
                    ],_p('category_successfully_added'));
                    //$this->url()->send('admincp.ultimatevideo.category.add', null, _p('category_successfully_added'));
                }
            }
        }
        
        $selectBox = Phpfox::getService('ultimatevideo.multicat')->getSelectBox(array('id' => '', 'name' => 'val[parent_id]', 'class' => 'form-control'), null, null, null);
        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.ultimatevideo.category.add'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('ultimatevideo.category')->display('option')->get(),
                    'selectBox' => $selectBox,
                    'bIsEdit' => $bIsEdit,
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

?>