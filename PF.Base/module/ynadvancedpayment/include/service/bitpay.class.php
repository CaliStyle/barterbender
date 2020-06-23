<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:19
 */
defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';
require_once dirname(dirname(__file__)) . '/service/BitPay/BitPayLib.php';

class Ynadvancedpayment_Service_BitPay extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_apiKey = '';
    protected $_test_mode;

    public function initialize($gateway,$params)
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_apiKey = $this->plugin_settings('merchant_api_key');
        if ($this->plugin_settings('test_mode'))
        {
            $this->_test_mode = "TRUE";
        }
        else
        {
            $this->_test_mode = "FALSE";
        }
    }
    public function process_payment($gateway, $params,$package = null, $notificationURL = NULL)
    {
        $this->initialize($gateway, $params);
        $order_id = explode('|', $this->_order['item_number']);
        $BitPayLib = new Ynadvancedpayment_Api_BitPay_BitPayLib();
        $price = $this->order('amount');
        $posData = '{"orderId":"'.$this->_order['item_number'].'"}';
        $currency = $this->_order['currency_code'];
        if(!$notificationURL)
        {
            $notificationURL = Phpfox::getLib('gateway')->url('bitpay');
        }
        $redirectURL = Phpfox::getLib('url')->makeUrl($order_id[0]);
        $bpOptions = array(
            'apiKey' => $this->_apiKey,
            'verifyPos' => true,
            'notificationURL' => $notificationURL,
            'currency' => $currency,
            'redirectURL' =>  $redirectURL,
        );
        if($this->plugin_settings('test_mode'))
        {
            $apiUrl = 'https://test.bitpay.com/api/invoice/';
        }
        else{
            $apiUrl = 'https://bitpay.com/api/invoice/';
        }
        $response = $BitPayLib -> bpCreateInvoice($this->_order['item_number'], $price, $posData,$bpOptions, $options = array(),$apiUrl);
        return $response;
    }
}