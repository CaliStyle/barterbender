<?php
defined('PHPFOX') or exit('NO DICE!');
class SocialPublishers_Component_Controller_Admincp_Providers extends Phpfox_Component
{
	public function process()
	{
        $oService = phpfox::getService('socialpublishers.providers');
        if($aFacebook = $this->request()->get('facebook'))
        {
            $iStatus = $aFacebook['is_active'];
            unset($aFacebook['is_active']);
            $oService->addSetting('facebook',serialize($aFacebook),$iStatus);
            $this->url()->send("admincp.socialpublishers.providers",null,_p('socialpublishers.update_successfully'));
        }
        if($aTwitter = $this->request()->get('twitter'))
        {
            $iStatus = $aTwitter['is_active'];
            unset($aTwitter['is_active']);
            $oService->addSetting('twitter',serialize($aTwitter),$iStatus);
            $this->url()->send("admincp.socialpublishers.providers",null,_p('socialpublishers.update_successfully'));
        }
        if($aLinkedIn = $this->request()->get('linkedin'))
        {
            $iStatus = $aLinkedIn['is_active'];
            unset($aLinkedIn['is_active']);
            $oService->addSetting('linkedin',serialize($aLinkedIn),$iStatus);
            $this->url()->send("admincp.socialpublishers.providers",null,_p('socialpublishers.update_successfully'));
        }
		$aPublisherProviders = $oService->getProviders(false);
		$this->template()->setTitle(_p('socialpublishers.mange_social_providers'))
			->setBreadcrumb(_p('socialpublishers.mange_social_providers'), $this->url()->makeUrl('admincp.socialpublishers.providers'))
			->setHeader('cache', array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">Core_drag.init({table: \'#js_drag_drop\', ajax: \'socialpublishers.ordering\'});</script>'		
				)
			)
			->setPhrase(array(
				'socialpublishers.view',
				'socialpublishers.hide',
				)
			)
			->assign(array(
					'aPublisherProviders' => $aPublisherProviders,
                    'sCoreUrl' => phpfox::getParam('core.path'),
                    'sCallBackUrl' => phpfox::getParam('core.path').'module/socialpublishers/static/php/static.php',
				)
			);			
	}
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_admincp_providers_clean')) ? eval($sPlugin) : false);
	}
}

?>