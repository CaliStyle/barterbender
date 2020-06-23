<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		LuanND
 * @package  		Module_jobposting
 */

class Jobposting_Component_Controller_Admincp_Managecustomfield_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$objtype= $this->request()->get('objtype');
		if(empty($objtype) || $objtype == 'undefined')
		{
			$objtype = 1;
		}
		
		$aFields = Phpfox::getService('jobposting.custom')->getByCompanyId(0, $objtype);
		
		if ($aOrder = $this->request()->getArray('order'))
		{
			if (Phpfox::getService('jobposting.custom')->updateOrder($aOrder))
			{
				$this->url()->send('admincp.jobposting.managecustomfield', array('objtype' => $objtype), _p('category_order_successfully_updated'));
			}
		}	
		
		if ($iDelete = $this->request()->getInt('delete'))
		{
			if (Phpfox::getService('jobposting.custom.process')->delete($iDelete))
			{
				$this->url()->send('admincp.jobposting.managecustomfield', array('objtype' => $objtype), _p('custom_field_successful_deleted'));
			}
		}
		
		$this->template()->setTitle(_p('manage_aj_package'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
			->setBreadcrumb(_p('manage_custom_field'))
			->assign(array(
					'aFields' => $aFields,
					'objtype' => $objtype,
					'sCustomField' => !empty($aFields) ? Phpfox::getService('jobposting.custom')->buildHtmlForReview($aFields) : '',
				)
		)->setHeader(array(
			'jquery/ui.js' => 'static_script',
			'ynjobposting_custom_field_admin.js' => 'module_jobposting',
			'<script type="text/javascript">$Behavior.setURLJobposting = function() { $Core.jobposting.url(\'' . $this->url()->makeUrl('admincp.jobposting.managecustomfield') . '\'); } </script>'
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