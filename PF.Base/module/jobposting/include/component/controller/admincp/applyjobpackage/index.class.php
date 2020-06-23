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

class Jobposting_Component_Controller_Admincp_Applyjobpackage_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPage 		= $this->request()->getInt('page');
		$iPageSize 	= 10;
		
		$iCount = Phpfox::getService('jobposting.applyjobpackage')->getItemCount();
		$aPackages = Phpfox::getService('jobposting.applyjobpackage')->getPackages($iPage, $iPageSize, $iCount);
		
		phpFox::getLib('pager')->set(array(
				'page'  => $iPage, 
				'size'  => $iPageSize, 
				'count' => $iCount
		));
		
		
		$this->template()->setTitle(_p('manage_aj_package'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
			->setBreadcrumb(_p('manage_aj_package'))
			->assign(array(
					'aPackages' => $aPackages,
				)
		);
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