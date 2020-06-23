<?php

defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';

class Ynadvancedpayment_Service_Authorizenetaim extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_x_type = '';
    protected $_x_test_request = '';
    protected $_host = '';
    protected $_api_login = '';
    protected $_transaction_key = '';

    public function initialize($gateway, $params) 
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_x_type = ($this->plugin_settings('transaction_settings') ? $this->plugin_settings('transaction_settings') : 'AUTH_CAPTURE' );
        $this->_api_login = $this->plugin_settings('api_login');
        $this->_transaction_key = $this->plugin_settings('transaction_key');
        if ($this->plugin_settings('test_mode')) 
        {
            $this->_x_test_request = "TRUE";    
			$this->_host = 'https://test.authorize.net/gateway/transact.dll';  
		} 
        else 
        {
            $this->_x_test_request = "FALSE";
			$this->_host = "https://secure.authorize.net/gateway/transact.dll";
        }
    }

    public function process_payment($gateway, $params) 
    {
    	$this->initialize($gateway, $params); 

        $post_array = array(
            "x_Login " => $this->_api_login,
            "x_Tran_Key" => $this->_transaction_key,
            "x_Version" => "3.1",
            "x_test_request" => $this->_x_test_request,
            "x_Delim_Data" => "TRUE",
            "x_Relay_Response" => "FALSE",
            "x_first_name" => $this->order('first_name'),
            "x_last_name" => $this->order('last_name'),
            "x_address" => $this->order('address'),
            "x_city" => $this->order('city'),
            "x_state" => $this->order('state'),
            "x_description" => $this->order('description'),
            "x_zip" => $this->order('zip'),
            "x_country" => ($this->order('country_code') ? $this->order('country_code') : 'USA'),
            'x_ship_to_first_name' => ($this->order('shipping_first_name')) ? $this->order('shipping_first_name') : $this->order('first_name'),
            'x_ship_to_last_name' => ($this->order('shipping_last_name')) ? $this->order('shipping_last_name') : $this->order('last_name'),
            'x_ship_to_company' => ($this->order('shipping_company')) ? $this->order('shipping_company') : $this->order('company'),
            'x_ship_to_country' => ($this->order('shipping_country')) ? $this->order('shipping_country') : $this->order('country_code'),
            'x_ship_to_address' => ($this->order('shipping_address')) ? $this->order('shipping_address') . ' ' . $this->order('shipping_address2') : $this->order('address'),
            'x_ship_to_city' => ($this->order('shipping_city')) ? $this->order('shipping_city') : $this->order('city'),
            'x_ship_to_state' => ($this->order('shipping_state')) ? $this->order('shipping_state') : $this->order('state'),
            'x_ship_to_zip' => ($this->order('shipping_zip')) ? $this->order('shipping_zip') : $this->order('zip'),
            "x_phone" => $this->order('phone'),
            "x_fax" => $this->order('fax'),
            "x_email" => $this->order('email_address'),
            "x_cust_id" => $this->order('member_id'),
            "x_invoice_num" => time() . strtoupper(substr($this->order('last_name'), 0, 3)),
            "x_company" => $this->order('company'),
            "x_email_customer" => "FALSE",
            "x_Amount" => number_format($this->order('total'), 2, '.', ''),
            "x_Method" => "CC", // MODULE_PAYMENT_QUICKCOMMERCE_METHOD == 'Credit Card' ? 'CC' : 'ECHECK',
            "x_Type" => $this->_x_type, // 'Authorize Only' ? 'AUTH_ONLY' : 'AUTH_CAPTURE', // set to AUTH_CAPTURE for money capturing transactions
            "x_card_num" => $this -> order('credit_card_number'),
            "x_card_code" => $this->order('CVV2'),
            "x_exp_date" => str_pad($this->order('expiration_month'), 2, '0', STR_PAD_LEFT) . '/' . $this->year_2($this->order('expiration_year')),
            "x_tax" => $this->order('tax'),
            "x_freight" => $this->order('shipping')
        );
        reset($post_array);
        $data = '';

        foreach($post_array as $key => $value)
        {
            $data .= $key . "=" . urlencode($value) . "&";
        }

        // SENDING ORDER DATA TO AUTHORIZE.NET
        $line_item = array();
        if ($this->order('items')) 
        {
            foreach ($this->order('items') as $row_id => $item) 
            {
                $basket = "";
                if (!isset($count)) {
                    $count = 1;
                }
                $count++;
                if ($count > 30) 
                {
                    continue;
                }
                $title = $this->strip_punctuation($item['title']);
                $title = substr($title, 0, 30);

                while (strlen(urlencode(htmlspecialchars($title))) > 30) {
                    $title = substr($title, 0, -1);
                }
                if (empty($item['entry_id'])) {
                    $item['entry_id'] = "000";
                }
                $basket .= $item['entry_id'] . "<|>";
                $basket .= urlencode(htmlspecialchars($title)) . "<|>";
                $basket .= $item['entry_id'] . "<|>";
                $basket .= abs($item['quantity']) . "<|>";
                $basket .= number_format(abs($item['price']), 2, '.', '') . "<|>";
                $basket .="Y";
                $line_item[] = $basket;
            }
        }
        // ADDING TO EXISTING DATA STRING.
        foreach($line_item as $key => $value)
        {
            $data .= 'x_line_item=' . $value . '&';
        }

        $data .= 'x_duty=0';
        $auth['authorized'] = FALSE;
        $auth['declined'] = FALSE;
        $auth['transaction_id'] = NULL;
        $auth['failed'] = TRUE;
        $auth['error_message'] = "";
        $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
        $ch = curl_init($this->_host);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $connect = curl_exec($ch);

        if (!$connect) 
        {
            $auth['error_message'] = curl_error($ch);
            return $auth;
        }
        curl_close($ch);
        $response = explode(",", $connect);
        switch ($response[0]) 
        {
            case 1:
                $auth['authorized'] = TRUE;
                $auth['failed'] = FALSE;
				if($this->plugin_settings('test_mode'))
					$auth['transaction_id'] = @$response[7];
				else
                	$auth['transaction_id'] = @$response[6];
                $auth['amount'] = @$response[9];
				$auth['currency'] = $params['currency_code'];
                break;
            case 2:
                $auth['authorized'] = FALSE;
                $auth['declined'] = TRUE;
                $auth['transaction_id'] = NULL;
                $auth['failed'] = FALSE;
                $auth['error_message'] = @$response[3];
                break;
            case 3:
                $auth['authorized'] = FALSE;
                $auth['declined'] = FALSE;
                $auth['transaction_id'] = NULL;
                $auth['failed'] = TRUE;
                $auth['error_message'] = @$response[3];
                break;
            case 4:
                if ($response[1] == "252" || $response[1] == "253") {
                    $auth['authorized'] = TRUE;
                    $auth['failed'] = FALSE;
                    if($this->plugin_settings('test_mode'))
						$auth['transaction_id'] = @$response[7];
					else
	                	$auth['transaction_id'] = @$response[6];
                } else {
                    $auth['authorized'] = FALSE;
                    $auth['declined'] = FALSE;
                    $auth['transaction_id'] = NULL;
                    $auth['failed'] = TRUE;
                    $auth['error_message'] = @$response[3];
                }
                break;
            default:
                $auth['failed'] = TRUE;
                $auth['error_message'] = $response[0];
        }
        return $auth;
    }
}
