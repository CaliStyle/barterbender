<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: sample.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Component_Controller_Admincp_Managecurrencies extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $oPaypal = Phpfox::getService('donation.gateways')->load('paypal');
        if ($oPaypal === false) {
            Phpfox_Error::display(_p('donation.no_gateway_active'));
        } else {
            $aCurrencies = $oPaypal->getSupportedCurrencies();
            $aVals = $this->request()->getArray('aVals');
            if ($aVals)
            {
                if (Phpfox::getService('donation.process')->updateCurrencies('paypal'))
                {
                    $this->url()->send('admincp.donation.managecurrencies', null, _p('donation.currencies_are_updated_successfully'));
                }
            }
            $aCurrentCurrencies = Phpfox::getService('donation')->getCurrentCurrencies('paypal');
            $this->template()->assign(array(
                    'aCurrencies' => $aCurrencies,
                    'aCurrentCurrencies' => $aCurrentCurrencies
                )
            );
        }
        $this->template()
                ->setTitle(_p('donation.manage_currencies'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('donation'), $this->url()->makeUrl('admincp.app',['id' => '__module_donation']))
                ->setBreadcrumb(_p('donation.manage_currencies'), $this->url()->makeUrl('admincp.donation.managecurrencies'))
                ->setHeader(array('general.css' => 'module_donation'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('donation.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}

?>
