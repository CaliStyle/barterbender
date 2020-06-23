<?php

defined('PHPFOX') or exit('NO DICE!');


class FoxFavorite_Component_Controller_Admincp_Settings extends Phpfox_Component 
{
	public function process()
	{
		$aSettings = phpfox::getService('foxfavorite')->getSettings();

        $aFunctionedModule = array(
            'coupon',
            'contest',
            'foxfeedspro',
            'jobposting',
            'karaoke',
            'resume',
            'videochannel',
			'directory'
        );

		$this->template()->setHeader('cache', array(
			'template.css' => 'style_css',
			'drag.js' => 'static_script',
			'jquery/plugin/jquery.scrollTo.js' => 'static_script'
		))
		->assign(array(
            'aSettings' => $aSettings,
            'aFunctionedModule' => $aFunctionedModule
		));
					
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_foxfavorite'), $this->url()->makeUrl('admincp.app').'?id=__module_foxfavorite')
            ->setBreadcrumb(_p('foxfavorite.settings'), $this->url()->makeUrl('admincp.foxfavorite.settings'));
	}
}
?>