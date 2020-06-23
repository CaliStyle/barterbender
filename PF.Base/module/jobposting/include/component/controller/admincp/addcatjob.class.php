<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class Jobposting_Component_Controller_Admincp_Addcatjob extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */

    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll();

        if ($iEditId = $this->request()->getInt('id')) {
            if ($aCategory = Phpfox::getService('jobposting.catjob')->getForEdit($iEditId)) {
                $bIsEdit = true;
                $this->template()->assign(array(
                    'aForms' => $aCategory
                ));

                $this->template()->setHeader('<script type="text/javascript"> $Behavior.ynjpEditIndustry = function(){ $(function(){$(\'#js_mp_catjob_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);}); };</script>')->assign('aForms', $aCategory);
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    $aVals['edit_id'] = $iEditId;
                    if (Phpfox::getService('jobposting.catjob.process')->update($aVals)) {
                        $this->url()->send('admincp.jobposting.addcatjob', array('id' => $iEditId), _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('jobposting.catjob.process')->add($aVals)) {
                        $this->url()->send('admincp.jobposting.managecatjob', null, _p('category_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
            ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.jobposting.catjob.addcatjob'))
            ->assign(array(
                    'aCategories' =>Phpfox::getService('jobposting.catjob')->getForBrowse(),
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


}
