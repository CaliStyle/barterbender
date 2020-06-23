<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:34 PM
 */
defined('PHPFOX') or exit('NO DICE!');
require_once PHPFOX_DIR . 'module/ecommerce/static/libs/paypal/samples/PPBootStrap.php';

class Ynsocialstore_Component_Controller_Checkout extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $this->template()->clearBreadCrumb();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                        ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('ynsocialstore.my-cart'))
                        ->setBreadCrumb(_p('ecommerce.checkout'));
        $iCartId = Phpfox::getService('ecommerce')->getMyCartId();
        $iId = $this->request()->getInt('id');
        list($iCount,$aMaxOfElement,$aMyCart,$bIsOnlyDigital) = Phpfox::getService('ecommerce')->getMyCartData('ynsocialstore',$iId);
        $this->template()
            ->setHeader(
                'cache', array(
                           'ynecommerce.js' => 'module_ecommerce',
                           'jquery.dd.js' => 'module_ynsocialstore',
                           'dd.css' => 'module_ynsocialstore'
                       )
            )
            ->assign(array(
                         'iCartId'   => $iCartId,
                         'iCount'    => $iCount,
                         'aMaxElement' => json_encode($aMaxOfElement),
                         'sModule'   => 'ynsocialstore',
                         'sCorePath' => Phpfox::getParam('core.path_file'),
                     ));

        Phpfox::isUser(true);
        $sModule = 'ynsocialstore';

        if ($iSellerId = $this->request()->get('sellerid')) {
            if(isset($aMyCart[$iSellerId])){
                $aCheckout = array($aMyCart[$iSellerId]);
            }
            else{
                Phpfox_Error::display(_p('Can not find this cart. Please check your cart!'));
                return false;
            }
        } else {
            $aCheckout = $aMyCart;
        }
        $iTotalItem = 0;

        if (!is_array($aCheckout)) {
            $aCheckout = array();
        }
        /*testing with adaptive paypal*/
        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();
        $bUsingAdaptive = isset($aGlobalSetting['actual_setting']['payment_settings']) ? ($aGlobalSetting['actual_setting']['payment_settings']) : 0;
        $aGatewaySettingPaypal = Phpfox::getService('ecommerce.helper')->getGatewaySetting('paypal');
        $aChecked = [];
        /*testing with adaptive paypal*/
        if ($aVals = $this->request()->getArray('val')) {
            $aShipping = array();
            $aOrders = array();
            if (isset($aVals['selected_address']) && (int)$aVals['selected_address'] > 0) {
                $aShipping = Phpfox::getService('ecommerce')->getAddressById((int)$aVals['selected_address']);
            } elseif(!$aVals['only_digital']) {
                Phpfox_Error::set(_p('ecommerce.please_input_address_for_shipping'));
            }
            /*remove shipping info*/
            unset($aVals['selected_address']);
            unset($aVals['only_digital']);

            $sCurrency = 'USD';
            if (isset($aVals['ynecommerce_currency'])) {
                $sCurrency = $aVals['ynecommerce_currency'];
            }
            unset($aVals['ynecommerce_currency']);
            if($bUsingAdaptive) {
                $aSellerCommission = [];
                $iTotalMail = 0;

                foreach ($aVals as $key => $aSell) {
                    if (!isset($aVals[$key]['ynecommerce_select_to_checkout'])) {
                        unset($aVals[$key]);
                    }
                    else{
                        $aChecked[$key] = $key;
                    }
                }

            }
            if (count($aVals) && Phpfox_Error::isPassed()) {
                /*one seller for one order*/
                foreach ($aVals as $iSellerId => $aSeller) {
                    $aFinalProducts = [];
                    $aOrder = array();
                    if (count($aSeller['ynecommerce_checkout_productid'])) {
                        $iCount = count($aSeller['ynecommerce_checkout_productid']);
                        $iTotalPrice = 0;
                        $aProducts = array();
                        for ($i = 0; $i < $iCount; $i++) {
                            Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_cart_product'),['cartproduct_quantity' => $aSeller['ynecommerce_checkout_quantity'][$i]],'cartproduct_payment_status = \'init\' AND cartproduct_product_id ='.$aSeller['ynecommerce_checkout_productid'][$i].' AND cartproduct_attribute_id ='.$aSeller['ynecommerce_checkout_attributeid'][$i]);
                            $key_product_id = $aSeller['ynecommerce_checkout_productid'][$i];
                            $key_attribute_id = isset($aSeller['ynecommerce_checkout_attributeid'][$i]) ? $aSeller['ynecommerce_checkout_attributeid'][$i] : 0 ;
                            if (!isset($aProducts[$key_product_id][$key_attribute_id])) {
                                $aProducts[$key_product_id][$key_attribute_id] = array(
                                    'product_id' => $key_product_id,
                                    'product_quantity' => $aSeller['ynecommerce_checkout_quantity'][$i],
                                    'product_price' => $aSeller['ynecommerce_checkout_price'][$i],
                                    'attribute_id' => $key_attribute_id,
                                    'module'    => $sModule
                                );
                            } else {
                                $aProducts[$key_product_id][$key_attribute_id]['product_quantity'] += $aSeller['ynecommerce_checkout_quantity'][$i];
                                $aProducts[$key_product_id][$key_attribute_id]['product_price'] += $aSeller['ynecommerce_checkout_price'][$i];
                            }

                            $iTotalPrice += $aSeller['ynecommerce_checkout_quantity'][$i] * $aSeller['ynecommerce_checkout_price'][$i];
                        }

                        foreach( $aProducts as $productId => $aProduct)
                        {
                            foreach($aProduct as $attId => $aAttrProduct)
                            {
                                $aFinalProducts[] = $aAttrProduct;
                            }
                        }
                        $aOrder['total_price'] = $iTotalPrice;
                        $aOrder['product'] = $aFinalProducts;
                    }
                    $aOrder['checkout_message'] = $aSeller['ynecommerce_checkout_message'];
                    $aOrder['seller_id'] = $aSeller['ynecommerce_checkout_ownerid'];
                    $aOrder['order_buyfrom_id'] = $iSellerId;
                    $aOrder['order_buyfrom_type'] = 'store';
                    $aGatewayValues = Phpfox::getService('api.gateway')->getUserGateways($aSeller['ynecommerce_checkout_ownerid']);
                    if ($bUsingAdaptive) {
                        if (!isset($aGatewayValues['paypal']['gateway']['paypal_email']) || ($aGatewayValues['paypal']['gateway']['paypal_email'] == '')) {
                            $aUser = Phpfox::getService('ynsocialstore')->getFieldsStoreById('name',$iSellerId,'getRow');
                            $sError = _p('ecommerce.paypal_email_of_seller_full_name_is_not_set', array('full_name' => $aUser['name']));
                            Phpfox_Error::set($sError);
                        }
                        elseif(!array_key_exists($aGatewayValues['paypal']['gateway']['paypal_email'],$aSellerCommission)){
                            $aSellerCommission[$aGatewayValues['paypal']['gateway']['paypal_email']]['email'] = $aGatewayValues['paypal']['gateway']['paypal_email'];
                            $iTotalMail++ ;
                        }
                        if($iTotalMail > 4){
                            Phpfox_Error::set(_p('The number of receivers in this transaction must not exceed 4. Please reduce the amount of stores in your shopping cart to process this payment in one single pay request.'));
                        }
                    }

                    $aOrders[] = $aOrder;

                }
                if (Phpfox_Error::isPassed()) {

                    list($aIdOrder, $aParamsOrder) = Phpfox::getService('ecommerce.order.process')->saveOrder($aOrders, $aShipping, $bUsingAdaptive, $sCurrency, $sModule);
                }
                /*save order*/
                if (!empty($aIdOrder) ) {
                    /*redirect to payment*/

                    if ($bUsingAdaptive && $aGatewaySettingPaypal['is_active']) {
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
                        if ($aParamsOrder['amount'] == 0) {
                            $this->updateOrderWithNoPrice($aIdOrder);
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

                        $this->template()->setTitle(_p('ecommerce.review_and_confirm_purchase'))
                            ->setBreadcrumb(_p('ecommerce.module_ecommerce'), $this->url()->makeUrl('ecommerce.checkout'))
                            ->setBreadcrumb(_p('ecommerce.review_and_confirm_purchase'), null, false);
                        $this->template()->assign(array(
                                                      'bPlaceOrder' => true,
                                                  ));
                    }
                }
            }
        }
        else
        {
            $this->template()->setTitle(_p('ecommerce.check_out'))
                ->setBreadcrumb(_p('ecommerce.' . $sModule), $this->url()->makeUrl($sModule));

            if ($sModule == 'ecommerce') {
                $this->template()->setBreadcrumb(_p('ecommerce.checkout'), $this->url()->makeUrl('ecommerce.checkout'));
            }
        }
        $this->template()->assign(array(
                         'bIsOnlyDigital' => $bIsOnlyDigital,
                         'aCheckout' => $aCheckout,
                         'iCntSelect' => 0,
                         'bUsingAdaptive' => $bUsingAdaptive,
                         'aChecked' => $aChecked
                     ))
            ->setPhrase(
                array(
                    'auction.this_field_is_required',
                )
            );

        $iUserId = Phpfox::getUserId();
        $aAddresses = Phpfox::getService('ecommerce')->getAddressUserId($iUserId);
        $this->template()->assign(array(
                                      'aAddresses' => $aAddresses,
                                      'sModule' => $sModule,
                                  ));
    }
    private function updateOrderWithNoPrice($aIdOrder)
    {
        if (count($aIdOrder)) {
            foreach ($aIdOrder as $key => $iOrderId) {
                $aUpdate = array(
                    'order_payment_status' => 'completed',
                    'order_payment_method' => '',
                    'order_purchase_datetime' => PHPFOX_TIME,
                );
                Phpfox::getService('ecommerce.order.process')->updateOrder($iOrderId, $aUpdate);

                $aOrder = Phpfox::getService('ecommerce.order')->getOrderById($iOrderId);

                $iBuyerId = $aOrder['user_id'];

                /*update amount money for seller*/
                $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney($aOrder['seller_id']);

                $aMoneyRequestForSeller = array(
                    'creditmoney_total_amount' => $aCreditMoney['creditmoney_total_amount'] + $aOrder['order_total_price'] - $aOrder['order_commission_value'],
                    'creditmoney_remain_amount' => $aCreditMoney['creditmoney_remain_amount'] + $aOrder['order_total_price'] - $aOrder['order_commission_value'],
                    'creditmoney_creation_datetime' => PHPFOX_TIME,
                    'creditmoney_modification_datetime' => PHPFOX_TIME,
                    'creditmoney_description' => '',
                );

                Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_creditmoney'), $aMoneyRequestForSeller, 'creditmoney_user_id = ' . $aOrder['seller_id']);

                /*update amount money for seller*/

                /*prepare email*/
                $aUserSeller = Phpfox::getService('user')->get($aOrder['seller_id']);
                $aUserBuyer = Phpfox::getService('user')->get($aOrder['user_id']);

                $sMessageSellerItemSold = '';
                $sMessageBuyerItemSold = '';
                $sOrderModule = 'ynsocialstore';
                $aStoreUpdated = [];
                if (isset($aOrder['product']) && count($aOrder['product'])) {
                    foreach ($aOrder['product'] as $key => $aOrderProduct) {
                        $aProductItem = Phpfox::getService('ecommerce')->getQuickProductById($aOrderProduct['orderproduct_product_id']);
                        if (empty($aProductItem)) {
                            continue;
                        }

                        /*compose email*/
                        $sMessageSellerItemSold .= _p('ecommerce.product_name_title_sold_price_symbol_currency_amount_buyer_buyer',
                                                                     array(
                                                                         'title' => $aProductItem['name'],
                                                                         'buyer' => $aUserBuyer['full_name'],
                                                                         'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                                                         'amount' => $aOrderProduct['orderproduct_product_price'],
                                                                     )
                            ) . '<br><br>';

                        $sMessageBuyerItemSold .= _p('ecommerce.product_name_title_sold_price_symbol_currency_amount_by_seller',
                                                                    array(
                                                                        'title' => $aProductItem['name'],
                                                                        'seller' => $aUserSeller['full_name'],
                                                                        'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                                                        'amount' => $aOrderProduct['orderproduct_product_price'],
                                                                    )
                            ) . '<br><br>';

                        /*update quantity product*/
                        $sOrderModule = 'ynsocialstore';
                        $aProductStore = Phpfox::getService('ynsocialstore.product')->getProductSomeInfo($aOrderProduct['orderproduct_product_id']);
                        if((int)$aProductItem['product_quantity_main'] > 0 && $aProductStore['product_type'] == 'physical')
                        {
                            $iRestQuantity = $aProductItem['product_quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                            if($iRestQuantity < 0) $iRestQuantity = 0;
                            Phpfox::getService('ecommerce.process')->updateProductQuantity($aOrderProduct['orderproduct_product_id'], $iRestQuantity,'ynsocialstore_product');
                            if($iRestQuantity == 0 && $aProductStore['auto_close'])
                            {
                                Phpfox::getService('ynsocialstore.product.process')->closeProduct((int)$aOrderProduct['orderproduct_product_id']);
                            }
                        }
                        if((int)$aOrderProduct['orderproduct_attribute_id'] > 0)
                        {
                            $aAttribute = Phpfox::getService('ynsocialstore.product')->getElementAttribute($aOrderProduct['orderproduct_attribute_id']);
                            if($aAttribute['quantity'] > 0)
                            {
                                $iRestQuantity = $aAttribute['quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                                if($iRestQuantity < 0) $iRestQuantity = 0;
                                Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_product_attribute'), ['remain' => $iRestQuantity] ,'attribute_id = '.(int)$aOrderProduct['orderproduct_attribute_id']);
                            }
                        }
                        /*update my cart*/
                        $aCart = Phpfox::getService('ecommerce.cart')->get($iBuyerId);
                        if (!empty($aCart)) {
                            /*update status of product offer*/

                            Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_cart_product'), array(
                                'cartproduct_payment_status' => 'completed',
                            ),
                                                      'cartproduct_product_id = ' . (int)$aOrderProduct['orderproduct_product_id'] . ' AND cartproduct_cart_id = ' . (int)$aCart['cart_id'].' AND cartproduct_attribute_id = '.$aOrderProduct['orderproduct_attribute_id']);

                        }

                        /*update total order*/
                        Phpfox::getService('ecommerce.order.process')->updateTotalOrderProduct($aOrderProduct['orderproduct_product_id']);
                        if(!in_array($aProductItem['item_id'],$aStoreUpdated)){
                            Phpfox::getService('ynsocialstore.process')->updateTotalOrder($aProductItem['item_id']);
                            $aStoreUpdated[] = $aProductItem['item_id'];
                        }

                    }
                }

                /*send email and notification to buyer and seller*/
                $sLinkSeller = Phpfox::permalink('ecommerce.manage-orders', null, null);
                $sLinkBuyer = Phpfox::permalink('ecommerce.my-orders', null, null);

                /*send to seller*/
                $iReceiveId = $aOrder['seller_id'];
                $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                $email = $aUser['email'];
                $iProductId = 1;
                $aExtraData = array();
                $aExtraData['lists_item'] = $sMessageSellerItemSold;
                $aExtraData['url'] = $sLinkSeller;

                $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'congratulations_your_item_sold', $language_id, $iReceiveId, $iProductId, $aExtraData);
                Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

                Phpfox::getService('notification.process')->add('ecommerce_soldseller', $aOrder['order_id'], $aOrder['seller_id'], $aOrder['seller_id']);

                /*send to buyer*/
                $iReceiveId = $aOrder['user_id'];
                $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                $email = $aUser['email'];
                $iProductId = 1;
                $aExtraData = array();

                $aExtraData['lists_item'] = $sMessageBuyerItemSold;
                $aExtraData['url'] = $sLinkBuyer;

                $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'you_ve_bought_the_item', $language_id, $iReceiveId, $iProductId, $aExtraData);
                Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

                Phpfox::getService('notification.process')->add('ecommerce_soldbuyer', $aOrder['order_id'], $aOrder['user_id'], $aOrder['user_id']);
                $this->url()->send('ynsocialstore',null,_p('Your orders is successfully'));
            }
        }
        else{
            $this->url()->send('ynsocialstore',null,_p('Payment failed. Please check your cart'));
        }
    }
}