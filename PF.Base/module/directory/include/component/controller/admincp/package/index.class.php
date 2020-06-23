<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Admincp_Package_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPage 		= $this->request()->getInt('page');
		$iPageSize 	= 10;
		
		$iCount = Phpfox::getService('directory.package')->getItemCount();
		$aPackages = Phpfox::getService('directory.package')->getPackages($iPage, $iPageSize, $iCount);
		
		phpFox::getLib('pager')->set(array(
				'page'  => $iPage, 
				'size'  => $iPageSize, 
				'count' => $iCount
		));
		
		
		$this->template()->setTitle(_p('directory.manage_packages'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.manage_packages'))
			->assign(array(
					'aPackages' => $aPackages,
				)
		)->setHeader(array(
        	'package_index.js'			 => 'module_directory',
		));		
        $this->template()->setPhrase(array(
        	'directory.are_you_sure',
        	'directory.yes',
        	'directory.no',
        	'directory.transfer_owner',
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