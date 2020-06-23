<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:19
 */
defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';
require_once dirname(dirname(__file__)) . '/service/ITransact/cardauthrequest.class.php';

class Ynadvancedpayment_Service_ITransact extends Ynadvancedpayment_Service_Paymentgateway
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
        $this->_api_key = $this->plugin_settings('api_key');
        $this->_gateway_id = $this->plugin_settings('gateway_id');
        $this->_username = $this->plugin_settings('api_username');
        $this->_host = 'https://secure.itransact.com/cgi-bin/rc/xmltrans2.cgi';
        if ($this->plugin_settings('test_mode'))
        {
            $this->_test_mode = "TRUE";
        }
        else
        {
            $this->_test_mode = "FALSE";
        }
    }
    public function process_payment($gateway, $params,$package = null)
    {
        $this->initialize($gateway, $params);
        $post_array = array(
            "api_key" => $this->_api_key,
            "username" => $this->_username,
            "target_gateway" => $this->_gateway_id,
            "test_mode" => $this->_test_mode,
            "bill_first_name" => $this->order('first_name'),
            "bill_last_name" => $this->order('last_name'),
            "bill_address1" => $this->order('address'),
            "bill_city" => $this->order('city'),
            "bill_state" => $this->order('state'),
            "bill_zip" => $this->order('zip'),
            "bill_country" => ($this->order('country_code') ? $this->order('country_code') : 'USA'),
            "ship_country" => ($this->order('country_code') ? $this->order('country_code') : 'USA'),
            'ship_first_name' => ($this->order('shipping_first_name')) ? $this->order('shipping_first_name') : $this->order('first_name'),
            'ship_last_name' => ($this->order('shipping_last_name')) ? $this->order('shipping_last_name') : $this->order('last_name'),
            'ship_address1' => ($this->order('shipping_address')) ? $this->order('shipping_address') . ' ' . $this->order('shipping_address2') : $this->order('address'),
            'ship_city' => ($this->order('shipping_city')) ? $this->order('shipping_city') : $this->order('city'),
            'ship_state' => ($this->order('shipping_state')) ? $this->order('shipping_state') : $this->order('state'),
            'ship_zip' => ($this->order('shipping_zip')) ? $this->order('shipping_zip') : $this->order('zip'),
            "ship_phone" => $this->order('phone'),
            "bill_phone" => $this->order('phone'),
            "email" => $this->order('email_address'),
            "send_customer_email" => "TRUE",
            "send_merchant_email" => "FALSE",
            "total" => number_format($this->order('amount'), 2, '.', ''),
            "description" => $this->order('order_description')?$this->order('order_description'):"Pay Subscription Plan",
            "account_number" => $this -> order('credit_card_number'),
            "cvv_number" => $this->order('CVV2'),
            "expiration_month" => $this->order('expiration_month'),
            "expiration_year" => $this->order('expiration_year')
        );
        $post_array['order_items'] = array();
        $post_array['order_items'][0] = array();
        $post_array['order_items'][0]['description'] = "Pay Subscription Plan";
        $post_array['order_items'][0]['cost'] = number_format($this->order('amount'), 2, '.', '');
        $post_array['order_items'][0]['qty'] = "1";

        if($package != null && is_array($package)){
            $unit = '';
            $length = 0;
            $recurrence = $package['recurrence'];
            switch ($package['recurrence_type'])
            {
                case 'day':
                    $length = $recurrence;
                    $unit = 'days';
                    break;
                case 'week':
                    $length = $recurrence * 7;
                    $unit = 'days';
                    break;
                case 'month':
                    $length = $recurrence;
                    $unit = 'months';
                    break;
                case 'year':
                    $length = $recurrence * 12;
                    $unit = 'months';
                    break;

            }
            $post_array['recur_total'] = number_format($this->order('amount'), 2, '.', '');
            $post_array['recur_description'] = "Recurring Payment";
            $post_array['recur_recipe'] = $length.$unit;
            $post_array['recur_reps'] = "9999";
        }
        reset($post_array);
        $xml_request = new Ynadvancedpayment_Service_ITransact_CardAuthRequest($post_array);
        $xml = $xml_request->toXML();
        $response = $xml_request->submit($this -> _host, $xml);
        if(stristr($response->status(), 'fail'))
        {
            $auth['authorized'] = FALSE;
            $auth['declined'] = FALSE;
            $auth['transaction_id'] = NULL;
            $auth['failed'] = TRUE;
            $auth['error_message'] = $response->errorMessage();
        }
        else if ($response->status() == "ERROR")
        {
            $auth['authorized'] = FALSE;
            $auth['declined'] = TRUE;
            $auth['transaction_id'] = NULL;
            $auth['failed'] = TRUE;
            $auth['error_message'] = $response->errorMessage();
        }
        else if (stristr($response->status(), 'ok'))
        {
            $auth['authorized'] = TRUE;
            $auth['transaction_id'] = $response->xid();
            $auth['amount'] = $response->amount();
            $auth['currency'] = $params['currency_code'];
        }
        else
        {
            $auth['failed'] = TRUE;
            $auth['error_message'] = $response[0];
        }
        return $auth;
    }
}