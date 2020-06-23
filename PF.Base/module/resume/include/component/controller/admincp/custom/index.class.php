<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Resume_Component_Controller_Admincp_Custom_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        
       if ($aOrder = $this->request()->getArray('order'))
		{
			if (Phpfox::getService('resume.custom.process')->updateOrder($aOrder))
			{
				$this->url()->send('admincp.resume.custom', null, _p('Custom fields successfully updated'));
			}
		}
		
		if ($iDelete = $this->request()->getInt('delete'))
		{
			$bHasData = Phpfox::getService('resume.category')->hasData($iDelete);
			if (!$bHasData)
			{
				Phpfox::getService('resume.category.process')->delete($iDelete);
				$this->url()->send('admincp.resume.categories', null, _p('resume.category_successfully_deleted'));
			}
			else
			{
				Phpfox_Error::set(_p('resume.cannot_delete_category_that_currently_has_related_data'));
			}
		}
		
		$aCategories = Phpfox::getService('resume.custom')->display();
	
		
		$this->template()->setTitle(_p('resume.manage_categories'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
			->setBreadcrumb(_p('admin_menu_manage_custom_fields'), $this->url()->makeUrl('admincp.resume.custom'))
			->setPhrase(array(
					'resume.are_you_sure_this_will_remove_this_category_froxm_all_related_resumes_and_cannot_be_undone',
					'resume.are_you_sure'
				)
			)
			->setHeader(array(
					'jquery/ui.js' => 'static_script',
					'admin.js' => 'module_custom',
                    'custom.js' => 'module_resume',
                    '<script type="text/javascript">$Behavior.resumeAdminCustomIndex = function() { $Core.custom.url(\'' . $this->url()->makeUrl('admincp.resume.custom') . '\'); $Core.custom.addSort(); }</script>',
				)
			)
			->assign(array(
					'sCategories' => $aCategories,
				)
			);
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}

?>