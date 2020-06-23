<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Fundraising_Component_Controller_Admincp_Gateway_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $this->template()->setTitle(_p('payment_gateways'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_fundraising'), $this->url()->makeUrl('admincp.app').'?id=__module_fundraising')
            ->setBreadcrumb(_p('payment_gateways'), $this->url()->makeUrl('admincp.fundraising'))
            ->assign(array(
                    'aGateways' => Phpfox::getService('fundraising.gateway')->getForAdmin()
                    )
            );

	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>