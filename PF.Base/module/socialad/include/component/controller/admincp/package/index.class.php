<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */


class Socialad_Component_Controller_Admincp_Package_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('socialad.package.process')->delete($iId))
			{
				$this->url()->send('admincp.socialad.package', null, _p('package_successfully_deleted'));
			}
		}
		$aPackages = Phpfox::getService('socialad.package')->getAllPackages();

		$this->template()
            ->setTitle(_p('manage_packages'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('manage_packages'))
			->setHeader('cache', array(
				'jquery.tablednd.js' => 'module_socialad',
			))
			->assign(array(
			'aPackages' => $aPackages
		));
		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
		(($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Component_Controller_Admincp_Package_Index_clean')) ? eval($sPlugin) : false);
	}

}

