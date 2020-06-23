<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		TriLM
 * @package  		Module_Coupon
 */

class Coupon_Component_Controller_Admincp_Customfield_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aFields = Phpfox::getService('coupon.custom')->getCustomField();
		
		
		if ($aOrder = $this->request()->getArray('order'))
		{
			if (Phpfox::getService('coupon.custom')->updateOrder($aOrder))
			{
				$this->url()->send('admincp.coupon.customfield', null, _p('category_order_successfully_updated'));
			}
		}	
		


		if ($iDelete = $this->request()->getInt('delete'))
		{

			if (Phpfox::getService('coupon.custom.process')->delete($iDelete))
			{
				$this->url()->send('admincp.coupon.customfield', array(), _p('custom_field_successfully_deleted'));
			}
		}
		
		$this->template()->setTitle(_p('manage_custom_field'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
			->setBreadcrumb(_p('manage_custom_field'))
			->assign(array(
					'sCorePath' => Phpfox::getParam('core.path'),
					'aFields' => $aFields			
						)
		)->setHeader(array(
			'coupon_backend.css' => 'module_coupon',
			'jquery/ui.js' => 'static_script',
			'yncoupon_custom_field_admin.js' => 'module_coupon',
			'<script type="text/javascript">$Behavior.setURLCoupon = function() { $Core.coupon.url(\'' . $this->url()->makeUrl('admincp.coupon.customfield') . '\'); } </script>'
		));
	 
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		
	}
}

?>