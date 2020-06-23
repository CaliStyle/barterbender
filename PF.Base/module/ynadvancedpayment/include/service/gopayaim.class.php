<?php

defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';
class Ynadvancedpayment_Service_GoPayAim extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_x_type = '';
    protected $_host = '';
    protected $_goid = '';
    protected $_secure_key = '';
    protected $_gatewaySettings;

    public function initialize($gateway, $params) 
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_goid = $this->_gatewaySettings['goid'];
        $this->_secure_key = $this->_gatewaySettings['secure_key'];
    }

    public function createToken(){
        
        $aGoPaySetting = $this->getGatewayById('gopay', false);

        /*if isset to session,use it or request to server gopoay*/
        if ($aGoPaySetting['is_test']) 
        {
            $this->_host = 'https://testgw.gopay.cz/api/oauth2/token';  
        } 
        else 
        {
            $this->_host = "https://gate.gopay.cz/api/oauth2/token";
        }

        $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
        $ch = curl_init($this->_host);

        $data = '';
        $post_array = array(
            "grant_type" => "client_credentials",
            "scope" => "payment-create",
        );

        while (list ($key, $val) = array_shift($post_array))
        {
            $data .= $key . "=" . urlencode($val) . "&";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        ));

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "".$aGoPaySetting['client_id'].":".$aGoPaySetting['secure_key'].""); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $aToken = curl_exec($ch);

        $sToken = '';

        if (!$aToken) 
        {
            $aToken['error_message'] = curl_error($ch);
            return $aToken;
        }

        if(preg_match_all('/{+(.*?)}/', $aToken, $matches)) {
            if(isset($matches[1][0])){
                $aToken = json_decode( "{".$matches[1][0]."}" ,true) ;
            }
        }
        curl_close($ch);
        if(empty($aToken['access_token'])){
            return Phpfox::getLib('url')->send('',array(),_p('ynadvancedpayment.cannot_connect_to_gopay_com_gateway'));
        }
        return $aToken;

    }
    public function process_payment($aTransactionItem) 
    {

            $aGoPaySetting = $this->getGatewayById('gopay', false);
            $aToken = Phpfox::getService('ynadvancedpayment.gopayaim')->createToken();

            if ($aGoPaySetting['is_test']) 
            {
                $this->_host = 'https://testgw.gopay.cz/api/payments/payment';  
            } 
            else 
            {
                $this->_host = "https://gate.gopay.cz/api/payments/payment";
            }

            $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
            
            $ch = curl_init($this->_host);

            $data = '';

            $payment_method = 'BANK_ACCOUNT';
            switch ($aTransactionItem['payment_method']) {
                case 'bank_transfer':
                     $payment_method = 'BANK_ACCOUNT';
                    break;
                case 'credit_card':
                     $payment_method = 'PAYMENT_CARD';
                    break;
            }

            $contact_info = array();

            if(isset($aTransactionItem['first_name']) && $aTransactionItem['first_name'] !='' ){
                $contact_info['first_name'] = $aTransactionItem['first_name'];
            }

            if(isset($aTransactionItem['last_name']) && $aTransactionItem['last_name'] !=''){
                $contact_info['last_name'] = $aTransactionItem['last_name'];
            }

            if(isset($aTransactionItem['email_address']) && $aTransactionItem['email_address'] !=''){
                $contact_info['email'] = $aTransactionItem['email_address'];
            }

            if(isset($aTransactionItem['phone']) && $aTransactionItem['phone'] !=''){
                $contact_info['phone_number'] = $aTransactionItem['phone'];
            }

            if(isset($aTransactionItem['city']) && $aTransactionItem['city'] !=''){
                $contact_info['city'] = $aTransactionItem['city'];
            }
            if(isset($aTransactionItem['address']) && $aTransactionItem['address'] !=''){
                $contact_info['street'] = $aTransactionItem['address'];
            }

            if(isset($aTransactionItem['zip']) && $aTransactionItem['zip'] !=''){
                $contact_info['postal_code'] = $aTransactionItem['zip'];
            }

            if(isset($aTransactionItem['country_code']) && $aTransactionItem['country_code'] !=''){
                $contact_info['country_code'] = $aTransactionItem['country_code'];
            }


            $post_array = array(
                'target' => array(
                         "type" => "ACCOUNT",
                         "goid" => $aGoPaySetting['goid']
                ),
                'amount' => round($aTransactionItem['amount']*100),
                'currency' => $aTransactionItem['currency_code'],
                'order_number' => $aTransactionItem['item_number'],
                'order_description' => $aTransactionItem['item_name'],
                'items' => array(
                        array("name" => $aTransactionItem['item_name'],"amount" => round($aTransactionItem['amount'] * 100))
                ),
                'additional_params' => array(
                                            array('name'=> $aTransactionItem['item_name'],'value'=> $aTransactionItem['item_number'])
                                        ),
                'callback' => array(
                     "return_url" => $aTransactionItem['return'],
                     "notification_url" => $aTransactionItem['notify_url']
                    ),
            );
            
            if(!empty($contact_info)){
                $post_array['payer']['contact'] = $contact_info;
            }
    
            if(!empty($aTransactionItem['recurring_cost']) && !empty($aTransactionItem['recurrence']) && !empty($aTransactionItem['recurrence_type'])){
                switch ($aTransactionItem['recurrence_type']) {
                    case 'month':
                        $aTransactionItem['recurrence_type'] = 'MONTH';
                        break;
                    case 'day':
                        $aTransactionItem['recurrence_type'] = 'DAY';
                        break;
                    case 'week':
                        $aTransactionItem['recurrence_type'] = 'WEEK';
                        break;
                }

                $post_array['recurrence'] = array(
                        "recurrence_cycle" => $aTransactionItem['recurrence_type'],
                        "recurrence_period" => $aTransactionItem['recurrence'],
                        "recurrence_date_to" => "2020-12-31",
                        "recurrence_state" => "REQUESTED"
                    );
            }
        
            $data = json_encode($post_array);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type:  application/json',
                'Authorization: '.$aToken['token_type'].' '.$aToken['access_token']
            ));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            $aCreatedPayment = curl_exec($ch);

            $aCreatedPayment = json_decode($aCreatedPayment,true);
            if (isset($aCreatedPayment['errors'])) 
            {
                return Phpfox::getLib('url')->send('',array(),_p('ynadvancedpayment.error_from_gopay_gateway_please_try_again'));
            }
            curl_close($ch);

            if(isset($aCreatedPayment['gw_url'])){
                header("Location: ".$aCreatedPayment['gw_url']);
                die();
            }

            return $aCreatedPayment;

    }

    public function process_callback($iPayMentId) 
    {

            $aGoPaySetting = $this->getGatewayById('gopay', false);
            $aToken = Phpfox::getService('ynadvancedpayment.gopayaim')->createToken();
            
            if ($aGoPaySetting['is_test']) 
            {
                $this->_host = 'https://testgw.gopay.cz/api/payments/payment/'.$iPayMentId;  
            } 
            else 
            {
                $this->_host = "https://gate.gopay.cz/api/payments/payment/".$iPayMentId;
            }


            $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
            $ch = curl_init($this->_host);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'ContentType:application/x-www-form-urlencoded',
                'Authorization: '.$aToken['token_type'].' '.$aToken['access_token']
            ));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $aCallBack = curl_exec($ch);


            if (!$aCallBack) 
            {
                $aCallBack['error_message'] = curl_error($ch);
                return $aCallBack;
            }
            curl_close($ch);

            return $aCallBack;

    }

}
