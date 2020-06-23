<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Customfield_Index extends Phpfox_Component
{
	public function process()
	{
		$bOrderUpdated = false;
		
		if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('ecommerce.custom.group')->deleteGroup($iDeleteId))
		{
			$this->url()->send('admincp.ecommerce.customfield', null, _p('custom_group_successfully_deleted'));
		}
		
		if (($aFieldOrders = $this->request()->getArray('field')) && Phpfox::getService('ecommerce.custom.process')->updateOrder($aFieldOrders))
		{
			$bOrderUpdated = true;
		}
		
		if (($aGroupOrders = $this->request()->getArray('group')) && Phpfox::getService('ecommerce.custom.group')->updateOrder($aGroupOrders))
		{			
			$bOrderUpdated = true;
		}		
		
		if ($bOrderUpdated === true)
		{
			$this->url()->send('admincp.ecommerce.customfield', null, _p('custom.custom_fields_successfully_updated'));
		}
		
		$aGroups = Phpfox::getService('ecommerce.custom.group')->getForListing();

		$this->template()->setTitle(_p('manage_custom_field_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ecommerce'), $this->url()->makeUrl('admincp.app').'?id=__module_ecommerce')
			->setBreadcrumb(_p('manage_custom_field_groups'))
			->setPhrase(array(
					'custom.are_you_sure_you_want_to_delete_this_custom_option',
                    'ecommerce.are_you_sure',
                    'ecommerce.yes',
                    'ecommerce.no'
				)
			)			
			->setHeader(array(
					'admin.js' => 'module_ecommerce',
					'<script type="text/javascript">$Behavior.custom_set_url = function() { $Core.ecommerce_customgroup.url(\'' . $this->url()->makeUrl('admincp.ecommerce.customfield') . '\'); };</script>',
					'jquery/ui.js' => 'static_script',
					'<script type="text/javascript">$Behavior.ecommerce_custom_admin_addSort = function(){setTimeout(function(){$Core.ecommerce_customgroup.addSort();},100);};</script>'
				)
			)
			->assign(array(
					'aGroups' => $aGroups
				)
			);
	}
}

?>
