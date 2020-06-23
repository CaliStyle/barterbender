<?php
namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Cache;

class AddController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if ($iEditId = $this->request()->getInt('id'))
        {
            if ($aCategory = Phpfox::getService('fevent.category')->getForEdit($iEditId))
            {

                $bIsEdit = true;

                $this->template()->setHeader('<script type="text/javascript"> $Behavior.onLoadParentCategory = function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);};</script>')->assign('aForms', $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val'))
        {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    $aVals['edit_id'] = $aCategory['category_id'];
                    if (Phpfox::getService('fevent.category.process')->update($aVals)) {
                        $this->url()->send('admincp.fevent.add', array('id' => $aCategory['category_id']),
                            _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('fevent.category.process')->add($aVals)) {
                        Phpfox_Cache::instance()->remove('fevent_category_display_option');
                        $this->url()->send('admincp.fevent.add', null, _p('category_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.fevent'))
            ->assign(array(
                    'sOptions' => Phpfox::getService('fevent.category')->display('option')->get(),
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages
                )
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
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}