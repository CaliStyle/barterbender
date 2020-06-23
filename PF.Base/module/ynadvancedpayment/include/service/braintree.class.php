<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:19
 */
defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';
require_once dirname(dirname(__file__)) . '/service/Braintree/Braintree.php';

class Ynadvancedpayment_Service_Braintree extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_test_mode = '';
    protected $_host = '';
    protected $_gateway_id = '';
    protected $_api_key = '';
    protected $_username = '';

    public function initialize($gateway, $params)
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_merchant_id = $this->plugin_settings('merchant_id');
        $this->_public_key = $this->plugin_settings('public_key');
        $this->_private_key = $this->plugin_settings('private_key');
        $this->_cse_key = $this->plugin_settings('cse_key');
        if ($this->plugin_settings('test_mode'))
        {
            $this->_environment = "sandbox";
        }
        else
        {
            $this->_environment = "production";
        }
    }
    public function process_payment($gateway, $params,$package = null)
    {
        $this->initialize($gateway, $params);
        $Braintree = new Ynadvancedpayment_Service_Braintree_Braintree();
        $Braintree -> includeFiles();

        Braintree_Configuration::environment($this->_environment);
        Braintree_Configuration::merchantId($this->_merchant_id);
        Braintree_Configuration::publicKey($this->_public_key);
        Braintree_Configuration::privateKey($this->_private_key);
        if($package != null){
            $order_id = explode('|', $this->order('item_number'));
            $package_id = $this->getPackageId($order_id[1]);
            $result_customer = Braintree_Customer::create(array(
                "id" => $order_id[1], //use order_id represent for customer_id (for getting order_id later, in recurring payment)
                "firstName" => $this->order("first_name"),
                "lastName" => $this->order("last_name"),
                "creditCard" => array(
                    "number" => $this->order("credit_card_number"),
                    "cvv" => $this->order("CVV2"),
                    "expirationMonth" => $this->order("expiration_month"),
                    "expirationYear" => $this->order("expiration_year"),
                    "billingAddress" => array(
                        "postalCode" => $this->order("postal_code")
                    )
                )
            ));
            if ($result_customer->success) {

                //create customer successfully
                try {

                    //pay recurring payment
                    $customer_id = $result_customer->customer->id;
                    $customer = Braintree_Customer::find($customer_id);
                    $payment_method_token = $customer->creditCards[0]->token;

                    $result_recurring = Braintree_Subscription::create(array(
                        'paymentMethodToken' => $payment_method_token,
                        'planId' => $package_id
                    ));

                    if ($result_recurring->success) {
                        if($result_recurring->subscription->status == 'Active'){
                            $resp['active'] = true;
                            $resp['order_id'] = $order_id[1];
                        }
                        else {
                            $resp['failed'] = true;
                        }
                        return $resp;
                    } else {
                        $resp['failed'] = true;
                        $resp['error_message'] = _p('validation_errors');
                    }

                } catch (Braintree_Exception_NotFound $e) {
                    $resp['failed'] = true;
                    $resp['error_message'] = _p('failure_no_customer_found_with_id') . $customer_id;
                    return $resp;
                }

            } else if ($result_customer->transaction) {

                $resp['failed'] = true;
                $resp['error_message'] = $result_customer->message.', '.$result_customer->transaction->processorResponseCode;
                return $resp;

            } else {
                $resp['failed'] = true;
                $resp['error_message'] = _p('validation_errors');
                return $resp;
            }
        }
        else{
            $result = Braintree_Transaction::sale(array(
                "amount" => $this->order('amount'),
                "orderId" => $this->order('item_number'),
                "creditCard" => array(
                    "number" => $this->order('credit_card_number'),
                    "cvv" => $this->order('CVV2'),
                    "expirationMonth" => $this->order('expiration_month'),
                    "expirationYear" => $this->order('expiration_year')
                ),
                "billing" => array(
                    "firstName" => $this->order('first_name'),
                    "lastName" => $this->order('last_name'),
                    "postalCode" => $this->order('postal_code'),
                ),
                "options" => array(
                    "submitForSettlement" => true
                )
            ));
            $resp = [];
            if ($result->success) {
                $resp['authorized'] = TRUE;
                $resp['transaction_id'] = $result->transaction->id;
                $resp['amount'] = $result->transaction->amount;
                $resp['currency'] = strtoupper($result->transaction->currencyIsoCode);

            } else if ($result->transaction) {
                $resp['failed'] = true;
                $resp['error_message'] = $result->message.', '.$result->transaction->processorResponseCode;

            } else {
                $resp['failed'] = true;
                $resp['error_message'] = _p('validation_errors');
            }
            return $resp;
        }
    }
    public function callback($params)
    {
		// Disable layout and viewrenderer

        $activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('braintree', false);

		if (!$activeGateway) {
            return false;
        }

		$settings = unserialize($activeGateway['setting']);

        $Braintree = new Ynadvancedpayment_Service_Braintree_Braintree();
        $Braintree -> includeFiles();

		$braintree_merchant_id = $settings['merchant_id'];
		$braintree_public_key = $settings['public_key'];
		$braintree_private_key = $settings['private_key'];

		//check test_mode
		$test_mode = $activeGateway['is_test'];
		if($test_mode == 1){
            $environment = "sandbox";
        }
        else {
            $environment = "production";
        }

		Braintree_Configuration::environment($environment);
		Braintree_Configuration::merchantId($braintree_merchant_id);
		Braintree_Configuration::publicKey($braintree_public_key);
		Braintree_Configuration::privateKey($braintree_private_key);


		if(isset($params["bt_challenge"])) {
            //verify webhook url
            echo(Braintree_WebhookNotification::verify($params["bt_challenge"]));
        }

		if(isset($params["bt_signature"]) && isset($params["bt_payload"]))
        {
            $webhookNotification = Braintree_WebhookNotification::parse(
                $params["bt_signature"], $params["bt_payload"]
            );

            // Process
            $order_id = $webhookNotification -> subscription -> transactions[0] -> customer['id']; //order_id belong to this customer
            $module = 'subscribe';

            if (Phpfox::isModule($module))
            {
                if (Phpfox::hasCallback($module, 'paymentApiCallback'))
                {
                    $sStatus = 'completed';
                    if ($sStatus !== null)
                    {
                        // insert subscribe_purchase table
                        $oldPurchase = Phpfox::getService('subscribe.purchase')->getPurchase((int)$order_id);
                        if(isset($oldPurchase['purchase_id'])){
                            $ynsubscription = Phpfox::getService('ynadvancedpayment.paymentgateway')->getYNSubscriptionByGatewayIdAndGetawaySubscriptionId(
                                $activeGateway['gateway_id']
                                , $order_id
                            );
                            $iPurchaseId = Phpfox::getService('ynadvancedpayment.process')->addSubscribePurchase(array(
                                'package_id' => $ynsubscription['package_id'],
                                'user_id' => $ynsubscription['user_id'],
                                'currency_id' => $oldPurchase['currency_id'] ,
                                'price' => $webhookNotification -> subscription -> transactions[0] -> amount,
                                'status' => $sStatus,
                            ));

                            if($iPurchaseId)
                            {
                                Phpfox::callback($module . '.paymentApiCallback', array(
                                        'gateway' => 'braintree',
                                        'status' => $sStatus,
                                        'item_number' => $iPurchaseId,
                                        'total_paid' => $webhookNotification -> subscription -> transactions[0] -> amount,
                                    )
                                );
                                // Exit
                                echo 'OK';
                                Phpfox::getService('api.gateway.process')->addLog('braintree', Phpfox::endLog());
                                exit(0);
                            }
                        }
                    }
                    else
                    {
                    }
                }
                else
                {
                }
            }
            else
            {
            }
        }
    }
}
