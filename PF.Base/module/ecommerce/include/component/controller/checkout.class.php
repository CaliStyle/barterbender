<?php

defined('PHPFOX') or exit('NO DICE!');
require_once PHPFOX_DIR . 'module/ecommerce/static/libs/paypal/samples/PPBootStrap.php';

class ecommerce_Component_Controller_checkout extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $sModule = $this->request()->get('req1');

        $aMyCart = Phpfox::getService('ecommerce')->getMyCartData();
        $iCartId = Phpfox::getService('ecommerce')->getMyCartId();
        if ($iSellerId = $this->request()->get('sellerid')) {
            $aCheckout = array($aMyCart[$iSellerId]);
        } else {
            $aCheckout = $aMyCart;
        }
        $iTotalItem = 0;

        if (!is_array($aCheckout)) {
            $aCheckout = array();
        }
        if (count($aCheckout)) {
            foreach ($aCheckout as $key => $aSeller) {
                $iTotalItem += count($aSeller);
            }
        }

        /*testing with adaptive paypal*/
        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();
        $bUsingAdaptive = isset($aGlobalSetting['actual_setting']['payment_settings']) ? ($aGlobalSetting['actual_setting']['payment_settings']) : 0;
        $aGatewaySettingPaypal = Phpfox::getService('ecommerce.helper')->getGatewaySetting('paypal');

        /*testing with adaptive paypal*/
        if ($aVals = $this->request()->getArray('val')) {
            $aShipping = array();
            $aOrders = array();
            $sModuleId = $this->request()->get('module_id');
            if (!Phpfox::isModule($sModuleId))
            {
                $sModuleId = 'ecommerce';
            }
            if (isset($aVals['selected_address']) && (int)$aVals['selected_address'] > 0) {
                $aShipping = Phpfox::getService('ecommerce')->getAddressById((int)$aVals['selected_address']);
            } else {
                Phpfox_Error::set(_p('please_input_address_for_shipping'));
            }
            /*remove shipping info*/
            unset($aVals['selected_address']);

            $sCurrency = 'USD';
            if (isset($aVals['ynecommerce_currency'])) {
                $sCurrency = $aVals['ynecommerce_currency'];
            }
            unset($aVals['ynecommerce_currency']);

            if (count($aVals) && Phpfox_Error::isPassed()) {
                /*one seller for one order*/
                foreach ($aVals as $iSellerId => $aSeller) {
                    $aOrder = array();
                    if (count($aSeller['ynecommerce_checkout_productid'])) {
                        $iCount = count($aSeller['ynecommerce_checkout_productid']);
                        $iTotalPrice = 0;
                        $aProducts = array();
                        for ($i = 0; $i < $iCount; $i++) {
                            $key_product_id = $aSeller['ynecommerce_checkout_productid'][$i];
                            if (!isset($aProducts[$key_product_id])) {
                                $aProducts[$key_product_id] = array(
                                    'product_id' => $key_product_id,
                                    'product_quantity' => $aSeller['ynecommerce_checkout_quantity'][$i],
                                    'product_price' => $aSeller['ynecommerce_checkout_price'][$i],
                                );
                            } else {
                                $aProducts[$key_product_id]['product_quantity'] += $aSeller['ynecommerce_checkout_quantity'][$i];
                                $aProducts[$key_product_id]['product_price'] += $aSeller['ynecommerce_checkout_price'][$i];
                            }

                            $iTotalPrice += $aSeller['ynecommerce_checkout_quantity'][$i] * $aSeller['ynecommerce_checkout_price'][$i];
                        }
                        $aOrder['total_price'] = $iTotalPrice;
                        $aOrder['product'] = $aProducts;
                    }

                    $aOrder['checkout_message'] = $aSeller['ynecommerce_checkout_message'];
                    $aOrder['seller_id'] = $iSellerId;
                    $aGatewayValues = Phpfox::getService('api.gateway')->getUserGateways($iSellerId);
                    if ($bUsingAdaptive) {
                        if (!isset($aGatewayValues['paypal']['gateway']['paypal_email']) || ($aGatewayValues['paypal']['gateway']['paypal_email'] == '')) {
                            $aUser = Phpfox::getService('user')->get($iSellerId);
                            $sError = _p('paypal_email_of_seller_full_name_is_not_set', array('full_name' => $aUser['full_name']));
                            Phpfox_Error::set($sError);
                        }
                    }

                    $aOrders[] = $aOrder;

                }

                if (Phpfox_Error::isPassed()) {

                    list($aIdOrder, $aParamsOrder) = Phpfox::getService('ecommerce.order.process')->saveOrder($aOrders, $aShipping, $bUsingAdaptive, $sCurrency, $sModuleId);
                }
                /*save order*/
                if (!empty($aIdOrder) && $aGatewaySettingPaypal['is_active']) {
                    /*redirect to payment*/

                    if ($bUsingAdaptive) {
                        /*handle data and redirect to paypal*/
                        $payRequest = new PayRequest();

                        $receiver = array();
                        $i = 0;
                        if (isset($aParamsOrder['admin'])) {

                            if ($aParamsOrder['admin']['amount'] == 0) {

                            } else {
                                $receiver[0] = new Receiver();
                                $receiver[0]->amount = $aParamsOrder['admin']['amount'];
                                $receiver[0]->email = $aParamsOrder['admin']['email'];
                                if (count($aParamsOrder['admin']['invoice_id']) > 2) {
                                    $receiver[0]->invoiceId = implode("-", $aParamsOrder['admin']['invoice_id']);
                                } else
                                    if (count($aParamsOrder['admin']['invoice_id']) == 1) {
                                        $receiver[0]->invoiceId = $aParamsOrder['admin']['invoice_id'];
                                    }
                                $i++;
                            }
                        }

                        if (count($aParamsOrder['seller'])) {
                            foreach ($aParamsOrder['seller'] as $aSeller) {
                                if ($aSeller['amount'] == 0) {
                                    continue;
                                }
                                $receiver[$i] = new Receiver();
                                $receiver[$i]->amount = $aSeller['amount'];
                                $receiver[$i]->email = $aSeller['email'];
                                if (count($aSeller['invoice_id']) >= 2) {
                                    $receiver[$i]->invoiceId = implode("-", $aSeller['invoice_id']);
                                } else
                                    if (count($aSeller['invoice_id']) == 1) {
                                        $receiver[$i]->invoiceId = $aSeller['invoice_id'];
                                    }
                                $i++;
                            }
                        }


                        $receiverList = new ReceiverList($receiver);

                        $payRequest->receiverList = $receiverList;

                        $requestEnvelope = new RequestEnvelope("en_US");
                        $payRequest->requestEnvelope = $requestEnvelope;
                        $payRequest->actionType = "PAY";
                        $payRequest->cancelUrl = Phpfox::getLib('url')->makeUrl('');
                        $payRequest->returnUrl = Phpfox::permalink($sModule . '.my-orders', false, false) . 'payment_done/';
                        $payRequest->currencyCode = $sCurrency;
                        $payRequest->ipnNotificationUrl = Phpfox::getService('ecommerce')->getStaticPath() . 'module/ecommerce/static/php/thankyou.php';

                        $sdkConfig = array(
                            "acct1.UserName" => $aGlobalSetting['actual_setting']['username_paypal'],
                            "acct1.Password" => $aGlobalSetting['actual_setting']['password_paypal'],
                            "acct1.Signature" => $aGlobalSetting['actual_setting']['signature_paypal'],
                            "acct1.AppId" => $aGlobalSetting['actual_setting']['application_id_paypal']
                        );
                        if ($aGatewaySettingPaypal['is_test']) {
                            $sdkConfig["mode"] = "sandbox";
                        } else {
                            $sdkConfig["mode"] = "live";
                        }

                        $adaptivePaymentsService = new AdaptivePaymentsService($sdkConfig);
                        $payResponse = $adaptivePaymentsService->Pay($payRequest);

                        $sRedirectPaypal = 'https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=';
                        if (!$aGatewaySettingPaypal['is_test']) {
                            $sRedirectPaypal = 'https://www.paypal.com/webscr?cmd=_ap-payment&paykey=';
                        }
                        $this->url()->send($sRedirectPaypal . $payResponse->payKey);
                    } else {

                        $sUrl = Phpfox::permalink($sModule . '.my-orders', false, false) . 'payment_done/';
                        $this->setParam('gateway_data', array(
                                'item_number' => 'ecommerce|' . implode("_", $aIdOrder),
                                'currency_code' => ($sCurrency != '') ? $sCurrency : 'USD',
                                'amount' => $aParamsOrder['amount'],
                                'item_name' => preg_replace('#[^\w()/.%\-&]#', "", $aParamsOrder['item_name']),
                                'return' => Phpfox::getService('ecommerce')->getStaticPath() . 'module/ecommerce/static/php/thankyou.php?sLocation=' . $sUrl,
                                'recurring' => '',
                                'recurring_cost' => '',
                                'alternative_cost' => '',
                                'alternative_recurring_cost' => ''
                            )
                        );

                    }

                    $this->template()->setTitle(_p('review_and_confirm_purchase'))
                        ->setBreadcrumb(_p('module_ecommerce'), $this->url()->makeUrl('ecommerce.checkout'))
                        ->setBreadcrumb(_p('review_and_confirm_purchase'), null, false);
                    $this->template()->assign(array(
                        'bPlaceOrder' => true,
                    ));
                }
            }
        }
        else
        {
            $this->template()->setTitle(_p('check_out'))
                ->setBreadcrumb(_p('' . $sModule), $this->url()->makeUrl($sModule));

            if ($sModule == 'ecommerce') {
                $this->template()->setBreadcrumb(_p('checkout'), $this->url()->makeUrl('ecommerce.checkout'));
            }
        }

        $this->template()->setHeader(
            'cache', array(
                'ynecommerce.js' => 'module_ecommerce'
            )
        )
            ->assign(array(
                'aCheckout' => $aCheckout,
                'iCartId' => $iCartId,
                'iTotalItem' => $iTotalItem,
            ))
            ->setPhrase(
                array(
                    'auction.this_field_is_required'
                )
            );

        $iUserId = Phpfox::getUserId();
        $aAddresses = Phpfox::getService('ecommerce')->getAddressUserId($iUserId);
        $this->template()->assign(array(
            'aAddresses' => $aAddresses,
            'sModule' => $sModule,
            'sDefaultImage' => Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png'
        ));
    }
}

?>