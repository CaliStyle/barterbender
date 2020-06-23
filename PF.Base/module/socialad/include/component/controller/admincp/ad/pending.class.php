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


class Socialad_Component_Controller_Admincp_Ad_Pending extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
        $this->template()->assign(array('status'=>Phpfox::getService('socialad.helper')->getConst('ad.status.pending', 'id')));
		$this->setParam('aQueryParam' , array(
			'ad_status' => Phpfox::getService('socialad.helper')->getConst('ad.status.pending', 'id'),
		));

		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('pending_ads'));
        $this->setParam('bIsAdminManage', true);
	}

}


