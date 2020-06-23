<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_Globalsettings extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		if ($aVals = $this->request()->getArray('val'))
        {
        	Phpfox::getService('directory.process')->deleteGlobalSetting();
        	Phpfox::getService('directory.process')->addGlobalSetting($aVals['theme'], $aVals['feature_fee']);
        }

		$aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();

		$aThemes = Phpfox::getService('directory')->getAllThemes();
		$this->template()->setTitle(_p('directory.global_settings'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.global_settings'))
			->assign(array(
				'aCurrentCurrencies' => Phpfox::getService('directory.helper')->getCurrentCurrencies(),
				'aThemes'   => $aThemes,
				'aGlobalSetting'   => $aGlobalSetting[0],
				'core_path' => Phpfox::getParam('core.path_file'),
						)
		);
	 
	}
	
}

?>