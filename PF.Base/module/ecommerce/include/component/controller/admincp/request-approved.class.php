<?php

defined('PHPFOX') or exit('NO DICE!');


class Ecommerce_Component_Controller_Admincp_Request_Approved extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $iRequestId = $this->request()->get('id');
        $request = Phpfox::getService('ecommerce.request')->get($iRequestId);
        $message = '';
        if ($this->request()->get('process')) {
            $aVal = $this->request()->getArray('val');
            $message = $aVal['text'];
            Phpfox::getService('ecommerce.request.process')->updateRequest($iRequestId, $message);
            $sUrl = $sUrl = $this->url()->makeUrl('admincp.ecommerce.requests', array('payment' => 'done'));
            $aUserGateways = Phpfox::getService('api.gateway')->getUserGateways($request['user_id']);
            $aActiveGateways = Phpfox::getService('api.gateway')->getActive(); // http://www.phpfox.com/tracker/view/15060/
            $aPurchaseDetails = array(
                'item_number' => 'ecommerce|request_' . $iRequestId,
                'currency_code' => Phpfox::getService('core.currency')->getDefault(),
                'amount' => $request['creditmoneyrequest_amount'],
                'item_name' => 'Money Request',
                'return' => Phpfox::getService('ecommerce')->getStaticPath() . 'module/ecommerce/static/php/thankyou.php?sLocation=' . $sUrl,
                'recurring' => '',
                'recurring_cost' => '',
                'alternative_cost' => '',
                'alternative_recurring_cost' => ''
            );

            if (is_array($aUserGateways) && count($aUserGateways)) {
                foreach ($aUserGateways as $sGateway => $aData) {
                    if (is_array($aData['gateway'])) {
                        foreach ($aData['gateway'] as $sKey => $mValue) {
                            $aPurchaseDetails['setting'][$sKey] = $mValue;
                        }
                    } else {
                        $aPurchaseDetails['fail_' . $sGateway] = true;
                    }

                    if (empty($aActiveGateways)) {
                        continue;
                    }
                    $bActive = false;
                    foreach ($aActiveGateways as $aActiveGateway) {
                        if ($sGateway == $aActiveGateway['gateway_id']) {
                            $bActive = true;
                        }
                    }
                    if (!$bActive) {
                        $aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
                    }
                }
            }
            $this->setParam('gateway_data', $aPurchaseDetails);
            $this->template()->setTitle(_p('review_and_confirm_payment'))
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('module_ecommerce'), $this->url()->makeUrl('admincp.app').'?id=__module_ecommerce')
                ->setBreadcrumb(_p('review_and_confirm_payment'), null, true)
                ->assign(array(
                        'confirm' => false,
                    )
                );
            return;
        }
        $request['text'] = $request['creditmoneyrequest_response'];
        $this->template()->setTitle(_p('review_and_confirm_payment'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ecommerce'), $this->url()->makeUrl('admincp.app').'?id=__module_ecommerce')
            ->setBreadcrumb(_p('review_and_confirm_payment'), null, true)
            ->assign(array(
                    'request' => $request,
                    'confirm' => true,
                    'message' => $message
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ecommerce.component_controller_admincp_request_approved_clean')) ? eval($sPlugin) : false);
    }
}

?>