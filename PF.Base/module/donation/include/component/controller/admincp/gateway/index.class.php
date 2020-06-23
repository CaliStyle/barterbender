<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Donation_Component_Controller_Admincp_Gateway_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
            $this->template()->setTitle(_p('donation.payment_gateways'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('donation'), $this->url()->makeUrl('admincp.app',['id' => '__module_donation']))
                ->setBreadcrumb(_p('donation.payment_gateways'), $this->url()->makeUrl('admincp.donation'))
                ->assign(array(
                                'aGateways' => Phpfox::getService('donation.gateway')->getForAdmin()
                        )
                );

	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('donation.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>