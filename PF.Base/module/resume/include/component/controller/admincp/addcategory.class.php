<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Controller_Admincp_Addcategory extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
         $bIsEdit = false;
         $aLanguages = Phpfox::getService('language')->getAll();

         if ($iEditId = $this->request()->getInt('id')) {
             if ($aCategory = Phpfox::getService('resume.category')->getForEdit($iEditId)) {
                 $bIsEdit = true;

                 $this->template()->setHeader('<script type="text/javascript"> $Behavior.onLoadParentCategory = function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);};</script>')->assign('aForms', $aCategory);
             }
         }

         if ($aVals = $this->request()->getArray('val')) {
             if ($aVals = $this->_validate($aVals)) {
                 if ($bIsEdit) {
                     if (Phpfox::getService('resume.category.process')->update($aCategory['category_id'], $aVals)) {
                         $this->url()->send('admincp.resume.addcategory', array('id' => $aCategory['category_id']),
                             _p('category_successfully_updated'));
                     }
                 } else {
                     if (Phpfox::getService('resume.category.process')->add($aVals)) {
                         $this->url()->send('admincp.resume.addcategory', null, _p('category_successfully_added'));
                     }
                 }
             }
         }

         $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
             ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
             ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
             ->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), '')
             ->assign(array(
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
