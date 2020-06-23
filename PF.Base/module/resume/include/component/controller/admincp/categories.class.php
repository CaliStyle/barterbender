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
class Resume_Component_Controller_Admincp_Categories extends Phpfox_Component
{
    /*
     * Process method which is used to process this component
     */
    public function process()
    {
        $bSubCategory = false;

        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
        }


        if ($iDelete = $this->request()->getInt('delete')) {
            $bHasData = Phpfox::getService('resume.category')->hasData($iDelete);
            if (!$bHasData) {
                Phpfox::getService('resume.category.process')->delete($iDelete);
                $this->url()->send('admincp.resume.categories', null, _p('resume.category_successfully_deleted'));
            } else {
                Phpfox_Error::set(_p('resume.cannot_delete_category_that_currently_has_related_data'));
            }
        }

        $aCategories = ($bSubCategory ? Phpfox::getService('resume.category')->getForAdmin($iId) : Phpfox::getService('resume.category')->getForAdmin());
        $this->template()->setTitle(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setPhrase(array(
                    'resume.are_you_sure_this_will_remove_this_category_from_all_related_resumes_and_cannot_be_undone',
                    'resume.are_you_sure'
                )
            )
            ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                    'admin.js' => 'module_resume',
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'resume.categoryOrdering\'}); }</script>',
                    '<script type="text/javascript">$Behavior.setUrlResume = function(){$Core.resume.url(\'' . $this->url()->makeUrl('admincp.resume.categories') . '\');}</script>'
                )
            )
            ->assign(array(
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => $aCategories,
                    'iCount' => Phpfox::getService('resume.category')->getItemCount(array())
                )
            );
    }
}