<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_Customfield_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bOrderUpdated = false;
		
		if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('directory.custom.group')->deleteGroup($iDeleteId))
		{
			$this->url()->send('admincp.directory.customfield', null, _p('directory.custom_group_successfully_deleted'));
		}

		if (($aFieldOrders = $this->request()->getArray('field')) && Phpfox::getService('directory.custom.process')->updateOrder($aFieldOrders))
		{			
			$bOrderUpdated = true;
		}
		
		if (($aGroupOrders = $this->request()->getArray('group')) && Phpfox::getService('directory.custom.group')->updateOrder($aGroupOrders))
		{			
			$bOrderUpdated = true;
		}		
		
		if ($bOrderUpdated === true)
		{
			$this->url()->send('admincp.directory.customfield', null, _p('custom.custom_fields_successfully_updated'));
		}
		
		$aGroups = Phpfox::getService('directory.custom.group')->getForListing();

		$this->template()->setTitle(_p('directory.manage_custom_field_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.manage_custom_field_groups'))
			->setPhrase(array(
					'custom.are_you_sure_you_want_to_delete_this_custom_option'
				)
			)			
			->setHeader(array(
					'admin.js' => 'module_directory',
					'<script type="text/javascript">$Behavior.custom_set_url = function() { $Core.customgroup.url(\'' . $this->url()->makeUrl('admincp.directory.customfield') . '\'); };</script>',
					'jquery/ui.js' => 'static_script',
					'<script type="text/javascript">$Behavior.custom_admin_addSort = function(){
							$Core.customgroup.addSort();
					};</script>'
				)
			)
			->assign(array(
					'aGroups' => $aGroups
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
