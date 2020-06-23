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


class Socialad_Component_Controller_Admincp_Payment_Paylater extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		$aTransactions = Phpfox::getService("socialad.payment")->getPayLaterTransactions();

		$this->template()->assign(array( 
			"aTransactions" => $aTransactions,
            'isAdmin' => true
		))
			->setHeader(array(
				)
			)
			->setTitle(_p('pay_later_requests'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('pay_later_requests'));

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

