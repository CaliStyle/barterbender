<?php
defined('PHPFOX') or exit('NO DICE!');
class SocialPublishers_Component_Controller_Admincp_Modules extends Phpfox_Component
{
	public function process()
	{
        $oService = phpfox::getService('socialpublishers.modules');
        if($this->request()->get('submit'))
        {
            $oService->updateSettings($this->request()->get('val'));
            $this->url()->send("admincp.socialpublishers.modules",null,_p('socialpublishers.update_successfully'));
        }
		$aModules = $oService->getModules(false);
		$this->template()->setTitle(_p('socialpublishers.mange_modules'))
			->setBreadcrumb(_p('socialpublishers.mange_modules'), $this->url()->makeUrl('admincp.socialpublishers.providers'))
			->assign(array(
					'aModules' => $aModules,
                    'sCoreUrl' =>phpfox::getParam('core.path')
				)
			);			
	}
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_admincp_providers_clean')) ? eval($sPlugin) : false);
	}
}

?>