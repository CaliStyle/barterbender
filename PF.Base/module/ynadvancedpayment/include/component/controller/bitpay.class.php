<?php
defined('PHPFOX') or exit('NO DICE!');
define("YNAP_START_YEAR", 2017);
define("YNAP_END_YEAR", 2040);

class Ynadvancedpayment_Component_Controller_BitPay extends Phpfox_Component
{
    public function process()
    {
        $activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('bitpay', false);

        // get data for Authorize.Net
        $aData = array();
        $aData['merchant_api_key'] = $this->request()->get('merchant_api_key');
        $aData['is_test'] = $this->request()->get('is_test');
        $aData['item_name'] = $this->request()->get('item_name');
        $aData['item_number'] = $this->request()->get('item_number');
        $aData['currency_code'] = $this->request()->get('currency_code');
        $aData['notify_url'] = $this->request()->get('notify_url');
        $aData['return'] = $this->request()->get('return');
        $aData['cmd'] = $this->request()->get('cmd');
        $aData['amount'] = $this->request()->get('amount');
        $aData['recurring_cost'] = $this->request()->get('recurring_cost');
        $aData['recurrence'] = $this->request()->get('recurrence');
        $aData['recurrence_type'] = $this->request()->get('recurrence_type');
        $aParts = explode('|', $aData['item_number']);
        $sReturn = ($aParts[0] == 'betterads') ? 'ads' : $aParts[0];

        if (!isset( $aData['merchant_api_key']) || empty($aData['merchant_api_key'])) {
            return $this->url()->send($sReturn, null, null);
        }
        elseif (preg_match("/(http|https):\/\//i", $aData['return'])) {
            $sReturn = $aData['return'];
        }
        $aVals = $aData;


        if (true) {
            $aVals = array_merge($aVals, $aData);
            $aVals['total'] = $aData['amount'];
            if ($aData['cmd'] == 'recurring') {
                // process recurring
                //Create Ynpayment Subscription
                $resp = Phpfox::getService('ynadvancedpayment.bitpay')->process_payment(
                    array(
                        'config' => array(
                            'merchant_api_key' => $aData['merchant_api_key'],
                        ),
                        'test_mode' => $aData['is_test'],
                    )
                    , $aVals
                    , array(
                        'recurrence' => $aData['recurrence'],
                        'recurrence_type' => $aData['recurrence_type'],
                    )
                );
            } else {

                $resp = Phpfox::getService('ynadvancedpayment.bitpay')->process_payment(
                    array(
                        'config' => array(
                            'merchant_api_key' => $aData['merchant_api_key'],
                        ),
                        'test_mode' => $aData['is_test'],
                    ),
                    $aVals
                );
            }
            if (!empty(isset($resp['error']) && $resp['error'])) {
                if (!empty($resp['error'])) {
                    Phpfox::getService('api.gateway.process')->addLog('bitpay', Phpfox::endLog());
                    return $this->url()->send($sReturn, null, (isset($resp['error']['message']) ? $resp['error']['message'] : _p('there_has_been_a_problem_with_your_transaction_please_verify_your_payment_details_and_try_again')));
                } else {
                    Phpfox::getService('api.gateway.process')->addLog('bitpay', Phpfox::endLog());
                    return $this->url()->send($sReturn, null, _p('there_has_been_a_problem_with_your_transaction_please_verify_your_payment_details_and_try_again'));
                }
            } else {
                if($resp['status'] == 'new')
                {
                    $this->url()->send($resp['url']);
                }
                if ($aData['cmd'] == 'recurring') {
                    $aPurchase = Phpfox::getService('subscribe.purchase')->getPurchase((int)$aParts[1]);

                    Phpfox::getService('ynadvancedpayment.process')->addSubscriptions(array(
                        'user_id' => Phpfox::getUserId(),
                        'getaway_subscription_id' => $resp,
                        'gateway_id' => $activeGateway['gateway_id'],
                        'package_id' => (int)$aPurchase['package_id'],
                        'purchase_id' => (int)$aPurchase['purchase_id'],
                    ));
                }

                // process callback
                if (Phpfox::isModule($aParts[0])) {
                    if (Phpfox::hasCallback($aParts[0], 'paymentApiCallback')) {
                        $sStatus = 'completed';
                        if ($sStatus !== null) {
                            Phpfox::callback($aParts[0] . '.paymentApiCallback', array(
                                    'gateway' => 'bitpay',
                                    'status' => $sStatus,
                                    'item_number' => $aParts[1],
                                    'total_paid' => $aData['amount']
                                )
                            );
                            Phpfox::getService('api.gateway.process')->addLog('bitpay', Phpfox::endLog());
                            return $this->url()->send($sReturn, null, _p('your_purchase_has_just_been_made_successfully'));
                        } else {
                        }
                    } else {
                    }
                } else {
                }
            }
        }
    }


    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynadvancedpayment.component_controller_bitpay_clean')) ? eval($sPlugin) : false);
    }

}
