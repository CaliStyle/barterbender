<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Globalsettings extends Phpfox_Component
{
    public function process()
    {
        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals['payment_settings']) {
                unset($aVals['payment_gateway_settings']);
                if ($aVals['username_paypal'] == '' || $aVals['password_paypal'] == '' || $aVals['signature_paypal'] == '' || $aVals['application_id_paypal'] == '') {
                    Phpfox_Error::set(_p('required_field_is_empty'));
                }
            } else {
                unset($aVals['username_paypal']);
                unset($aVals['password_paypal']);
                unset($aVals['signature_paypal']);
                unset($aVals['application_id_paypal']);
            }


            $aDefaultVals = array(
                'payment_settings' => 0,
                'payment_gateway_settings' => array('2checkout', 'paypal')
            );
            if (Phpfox_Error::isPassed()) {
                Phpfox::getService('ecommerce.process')->deleteGlobalSetting();
                Phpfox::getService('ecommerce.process')->addGlobalSetting($aDefaultVals, $aVals);
            }
        }

        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();

        $aGateways = Phpfox::getService('api.gateway')->getForAdmin();

        $aData = array();
        if (isset($aGlobalSetting['actual_setting'])) {
            $aData = $aGlobalSetting['actual_setting'];
        } elseif (isset($aGlobalSetting['default_setting'])) {
            $aData = $aGlobalSetting['default_setting'];
        }

        $aTemp = array();
        if (isset($aData['payment_gateway_settings'])) {
            foreach ($aData['payment_gateway_settings'] as $sPayment) {
                $aTemp[$sPayment] = 1;
            }
        }

        foreach ($aGateways as $iKey => $aItem) {
            if (isset($aTemp[$aItem['gateway_id']])) {
                $aGateways[$iKey]['checked'] = true;
            }
        }
        if (!Phpfox_Error::isPassed()) {
            $aData['payment_settings'] = 1;
        }
        $this->template()->setTitle(_p('global_settings'))->setBreadCrumb(_p("Apps"),
            $this->url()->makeUrl('admincp.apps'))->setBreadCrumb(_p('module_ecommerce'),
            $this->url()->makeUrl('admincp.app') . '?id=__module_ecommerce')->setBreadcrumb(_p('global_settings'))->assign(array(
            'aForms' => $aData,
            'aGateways' => $aGateways
        ));
    }

}
