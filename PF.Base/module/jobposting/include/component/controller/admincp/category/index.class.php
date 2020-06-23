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

class Jobposting_Component_Controller_Admincp_Category_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $bSubCategory = false;

        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
        }

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('jobposting.category.process')->delete($iDelete)) {
                $this->url()->send('admincp.jobposting.category', null, _p('category_successfully_deleted'));
            }

        }

        $aCategories = ($bSubCategory ? Phpfox::getService('jobposting.category')->getForAdmin($iId) : Phpfox::getService('jobposting.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
            ->setBreadCrumb(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setHeader(array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'jobposting.categoryOrdering\'}); }</script>'
                )
            )
            ->assign(array(
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => $aCategories
                )
            );
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('jobposting.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>