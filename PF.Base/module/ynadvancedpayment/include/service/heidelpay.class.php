<?php

/**
 *
 * @author MinhNC
 */
class Ynadvancedpayment_Service_HeidelPay extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_sender = '';
    protected $_login = '';
    protected $_pwd = '';
    protected $_channel = '';
    protected $_test_mode = '';

    public function initialize($gateway, $params)
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array)$gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_sender = $this->plugin_settings('sender');
        $this->_login = $this->plugin_settings('login');
        $this->_pwd = $this->plugin_settings('password');
        $this->_channel = $this->plugin_settings('channel');
        $this->_test_mode = $this->plugin_settings('test_mode');

    }

    public function process_payment_recurring($params, $package,$gateway)
    {
        $this->_gatewaySettings = unserialize($gateway['setting']);
        $this->_sender = $this->plugin_settings('sender');
        $this->_login = $this->plugin_settings('login');
        $this->_pwd = $this->plugin_settings('password');
        $this->_channel = $this->plugin_settings('channel');
        $this->_test_mode = $gateway['is_test'];
        //URL fuer Testsystem
        if ($this->_test_mode) {
            $url = "https://test-heidelpay.hpcgw.net/sgw/xml";
        } else {
            $url = "https://heidelpay.hpcgw.net/sgw/xml";
        }
        $parameters['SECURITY.SENDER'] = $this->_sender;
        $parameters['USER.LOGIN'] = $this->_login;
        $parameters['USER.PWD'] = $this->_pwd;
        $parameters['TRANSACTION.CHANNEL'] = $this->_channel;
        $parameters['ACCOUNT.HOLDER'] = $params['ACCOUNT_HOLDER'];
        $parameters['ACCOUNT.NUMBER'] = $params['ACCOUNT_NUMBER'];
        $parameters['ACCOUNT.BRAND'] = $params['ACCOUNT_BRAND'];
        $parameters['ACCOUNT.EXPIRY_MONTH'] = "";
        $parameters['ACCOUNT.EXPIRY_YEAR'] = "";
        $parameters['ACCOUNT.VERIFICATION'] = "";
        //Payment Code -- Auswahl Bezahlmethode und Typ
        $parameters['PAYMENT.CODE'] = "CC.SD";
        $parameters['PRESENTATION.CURRENCY'] = $package['default_currency_id'];
        //Response URL angeben
        $RESPONSE_URL = Phpfox::getParam('core.path') . 'api/gateway/callback/heidelpay/type_recurring-callback/';
        $parameters['FRONTEND.RESPONSE_URL'] = $RESPONSE_URL;
        //CSS- und/oder Jscript-Datei angeben
        $parameters['FRONTEND.CSS_PATH'] = Phpfox::getParam('core.path_file').'module/ynadvancedpayment/static/css/default/default/onlycarddetails_new.css';
        $parameters['PRESENTATION.AMOUNT'] =  $package['default_cost'];
        $parameters['IDENTIFICATION.TRANSACTIONID'] = $params['IDENTIFICATION_TRANSACTIONID'];
        $parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom ' . date("d.m.Y");

        $parameters['FRONTEND.MODE'] = "DEFAULT";

        // Modus ausw�hlen
        if ($this->_test_mode) {
            $parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
        } else {
            $parameters['TRANSACTION.MODE'] = "LIVE";
        }


        $parameters['FRONTEND.ENABLED'] = "true";
        $parameters['FRONTEND.POPUP'] = "false";
        //$parameters['FRONTEND.SHOP_NAME'] = '';
        $parameters['FRONTEND.REDIRECT_TIME'] = "0";


        $parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
        $parameters['FRONTEND.LANGUAGE'] = $params['FRONTEND_LANGUAGE'];

        $parameters['REQUEST.VERSION'] = $params['RESPONSE_VERSION'];


        $parameters['NAME.GIVEN'] = $params['NAME_GIVEN'];
        $parameters['NAME.FAMILY'] = $params['NAME_FAMILY'];
        $parameters['ADDRESS.STREET'] = $params['ADDRESS_STREET'];
        $parameters['ADDRESS.ZIP'] = $params['ADDRESS_ZIP'];
        $parameters['ADDRESS.CITY'] = $params['ADDRESS_CITY'];
        $parameters['ADDRESS.COUNTRY'] = $params['ADDRESS_COUNTRY'];
        $parameters['ADDRESS.STATE'] = $params['ADDRESS_STATE'];
        $parameters['CONTACT.EMAIL'] = $params['CONTACT_EMAIL'];

        $parameters['ACCOUNT.REGISTRATION'] = $params['IDENTIFICATION_UNIQUEID'];
        //building the postparameter string to send into the WPF

        $parameters['JOB.NAME'] = 'Standard Monthly Subscription';
        $parameters['ACTION.TYPE'] = 'DB';
        $parameters['DURATION.NUMBER'] = 1;
        $parameters['DURATION.UNIT'] = 'MONTH';
        $parameters['EXECUTION.DAYOFMONTH'] = date("m");
        $parameters['EXECUTION.MONTH'] = '*';
        $result = '';
        foreach ($parameters AS $key => $value)
            $result .= strtoupper($key) . '=' . urlencode($value) . '&';
        $strPOST = stripslashes($result);

        $current_day = date('d');
        switch ($current_day) {
            case '29':
            case '30':
            case '31':
                $current_day = 'L';
                break;
        }
        $current_month = date('m');
        $str_month = '*';
        $Execution = "<Execution>"
            . "<DayOfMonth>" . $current_day . "</DayOfMonth>"
            . "<Month>" . $str_month . "</Month>"
            . "</Execution>";

        $xml =
            "<Request version=\"" . $params['RESPONSE_VERSION'] . "\">"
            . "<Header>"
            . "<Security sender=\"" . $this->_sender . "\"/>"
            . "</Header>"
            . "<Transaction mode=\"" . $parameters['TRANSACTION.MODE'] . "\" response=\"SYNC\" channel=\"" . $this->_channel . "\">"
            . "<User login=\"" . $this->_login . "\" pwd=\"" . $this->_pwd . "\" />"
            . "<Identification>"
            . "<TransactionID>" . $params['IDENTIFICATION_TRANSACTIONID'] . "</TransactionID>"
            . "</Identification>"
            . "<Payment code=\"CC.SD\">"
            . "<Presentation>"
            . "<Amount>" . $package['default_recurring_cost'] . "</Amount>"
            . "<Currency>" . $package['default_recurring_currency_id'] . "</Currency>"
            . "<Usage>Order " . date("d.m.Y") . "</Usage>"
            . "</Presentation>"
            . "</Payment>"
            . "<Job name=\"Trial Subscripton\">"
            . "<Action type=\"DB\" />"
            . $Execution
            . "</Job>"
            . "<Account registration=\"" . $params['IDENTIFICATION_UNIQUEID'] . "\" />"
            . "</Transaction>"
            . "</Request>";

        //open the request url for the Web Payment Frontend
        $cpt = curl_init();
        curl_setopt($cpt, CURLOPT_URL, $url);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
        curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($cpt, CURLOPT_POST, 1);
        curl_setopt($cpt, CURLOPT_POSTFIELDS, "&load=" . urlencode($xml));
        $curlresultURL = curl_exec($cpt);
        $curlerror = curl_error($cpt);
        $curlinfo = curl_getinfo($cpt);
        curl_close($cpt);
        return $this->_parse_return($curlresultURL);
    }

    public function _parse_return($content)
    {
        $Result = $this->_substring_between($content, '<Result>', '</Result>');
        $UniqueID = $this->_substring_between($content, '<UniqueID>', '</UniqueID>');
        $Amount = $this->_substring_between($content, '<Amount>', '</Amount>');
        $Currency = $this->_substring_between($content, '<Currency>', '</Currency>');
        $TransactionID = $this->_substring_between($content, '<TransactionID>', '</TransactionID>');
        $returnvalue = array(
            'Result' => $Result,
            'UniqueID' => $UniqueID,
            'Amount' => $Amount,
            'Currency' => $Currency,
            'TransactionID' => $TransactionID,
        );
        return $returnvalue;
    }

    public function _substring_between($haystack, $start, $end)
    {
        if (strpos($haystack, $start) === false || strpos($haystack, $end) === false) {
            return false;
        } else {
            $start_position = strpos($haystack, $start) + strlen($start);
            $end_position = strpos($haystack, $end);
            return substr($haystack, $start_position, $end_position - $start_position);
        }
    }

    public function registration($gateway, $params, $order_id)
    {
        $this->initialize($gateway, $params);
        $price = $this->order('amount');
        //URL fuer Testsystem
        if ($this->_test_mode) {
            $url = "https://test-heidelpay.hpcgw.net/sgw/gtw";
        } else {
            $url = "https://heidelpay.hpcgw.net/sgw/gtw";
        }
        $parameters['SECURITY.SENDER'] = $this->_sender;
        $parameters['USER.LOGIN'] = $this->_login;
        $parameters['USER.PWD'] = $this->_pwd;
        // Channel f�r CC, OT Sofort, DC, DD, PayPal
        $parameters['TRANSACTION.CHANNEL'] = $this->_channel;

        $parameters['ACCOUNT.HOLDER'] = "";
        $parameters['ACCOUNT.NUMBER'] = "";
        $parameters['ACCOUNT.BRAND'] = "";
        $parameters['ACCOUNT.EXPIRY_MONTH'] = "";
        $parameters['ACCOUNT.EXPIRY_YEAR'] = "";
        $parameters['ACCOUNT.VERIFICATION'] = "";

        //Payment Code -- Auswahl Bezahlmethode und Typ
        $parameters['PAYMENT.CODE'] = "CC.RG";  // Registrierung Lastschrift
        $parameters['PRESENTATION.CURRENCY'] = $this->order('currency_code');
        //Response URL angeben
        $RESPONSE_URL = Phpfox::getParam('core.path') . 'api/gateway/callback/heidelpay/type_registration-callback/';
        $parameters['FRONTEND.RESPONSE_URL'] = $RESPONSE_URL;
        //CSS- und/oder Jscript-Datei angeben
        //$parameters['FRONTEND.CSS_PATH'] = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl() . '/application/modules/Ynpayment/externals/styles/onlycarddetails_new.css';
        //$parameters['FRONTEND.JSCRIPT_PATH'] = "http://127.0.0.1/wpf/wpfui.js";
        $parameters['PRESENTATION.AMOUNT'] = $price;
        $parameters['IDENTIFICATION.TRANSACTIONID'] = $order_id;
        $parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom ' . date("d.m.Y");

        $parameters['FRONTEND.MODE'] = "DEFAULT";

        if ($this->_test_mode) {
            $parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
        } else {
            $parameters['TRANSACTION.MODE'] = "LIVE";
        }

        $parameters['FRONTEND.ENABLED'] = "true";
        $parameters['FRONTEND.POPUP'] = "false";
        $parameters['FRONTEND.SHOP_NAME'] = Phpfox::getParam('core.site_title');
        $parameters['FRONTEND.REDIRECT_TIME'] = "0";

        $parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
        $parameters['FRONTEND.LANGUAGE'] = "en";
        $parameters['REQUEST.VERSION'] = "1.0";

        $parameters['NAME.GIVEN'] = "";
        $parameters['NAME.FAMILY'] = "";
        $parameters['ADDRESS.STREET'] = "";
        $parameters['ADDRESS.ZIP'] = "";
        $parameters['ADDRESS.CITY'] = "";
        $parameters['ADDRESS.COUNTRY'] = "";
        $parameters['ADDRESS.STATE'] = "";
        $parameters['CONTACT.EMAIL'] = "";

        if ($this->_test_mode) {
            $parameters['NAME.GIVEN'] = "Markus";
            $parameters['NAME.FAMILY'] = "Mustermann";
            $parameters['ADDRESS.STREET'] = "Musterstrasse 1";
            $parameters['ADDRESS.ZIP'] = "12345";
            $parameters['ADDRESS.CITY'] = "Musterstadt";
            $parameters['ADDRESS.COUNTRY'] = "DE";
            $parameters['ADDRESS.STATE'] = "";
            $parameters['CONTACT.EMAIL'] = "test@example.com";
        }
        //building the postparameter string to send into the WPF

        $result = '';
        foreach ($parameters AS $key => $value)
            $result .= strtoupper($key) . '=' . urlencode($value) . '&';
        $strPOST = stripslashes($result);

        //open the request url for the Web Payment Frontend

        $cpt = curl_init();
        curl_setopt($cpt, CURLOPT_URL, $url);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
        curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($cpt, CURLOPT_POST, 1);
        curl_setopt($cpt, CURLOPT_POSTFIELDS, $strPOST);
        $curlresultURL = curl_exec($cpt);
        $curlerror = curl_error($cpt);
        $curlinfo = curl_getinfo($cpt);
        curl_close($cpt);

        // parse results

        $r_arr = explode("&", $curlresultURL);
        foreach ($r_arr AS $buf) {
            $temp = urldecode($buf);
            $temp = explode("=", $temp, 2);
            $postatt = $temp[0];
            $postvar = $temp[1];
            $returnvalue[$postatt] = $postvar;
        }
        return $returnvalue;
    }

    public function process_payment($gateway, $params, $notificationURL = NULL, $response_url = NULL)
    {
        $this->initialize($gateway, $params);
        $price = $this->order('amount');
        //URL fuer Testsystem
        if ($this->_test_mode) {
            $url = "https://test-heidelpay.hpcgw.net/sgw/gtw";
        } else {
            $url = "https://heidelpay.hpcgw.net/sgw/gtw";
        }

        $parameters['SECURITY.SENDER'] = $this->_sender;
        $parameters['USER.LOGIN'] = $this->_login;
        $parameters['USER.PWD'] = $this->_pwd;
        $parameters['TRANSACTION.CHANNEL'] = $this->_channel;

        $parameters['ACCOUNT.HOLDER'] = "";
        $parameters['ACCOUNT.NUMBER'] = "";
        $parameters['ACCOUNT.BRAND'] = "";
        $parameters['ACCOUNT.EXPIRY_MONTH'] = "";
        $parameters['ACCOUNT.EXPIRY_YEAR'] = "";
        $parameters['ACCOUNT.VERIFICATION'] = "";

        //$parameters['PAYMENT.CODE'] = "CC.DB";  // Direkte Belastung
        $parameters['PRESENTATION.CURRENCY'] = $this->order('currency_code');
        //Response URL angeben
        if (empty($response_url)) {
            $response_url = Phpfox::getParam('core.path') . 'api/gateway/callback/heidelpay/type_payment-callback/';
        }
        $parameters['FRONTEND.RESPONSE_URL'] = $response_url;
        //CSS- und/oder Jscript-Datei angeben
        $parameters['PRESENTATION.AMOUNT'] = $price;
        $parameters['IDENTIFICATION.TRANSACTIONID'] = $this->order('item_number');
        $parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom ' . date("d.m.Y");


        $parameters['FRONTEND.MODE'] = "DEFAULT";

        if ($this->_test_mode) {
            $parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
        } else {
            $parameters['TRANSACTION.MODE'] = "LIVE";
        }

        $parameters['FRONTEND.ENABLED'] = "true";
        $parameters['FRONTEND.POPUP'] = "false";
        $parameters['FRONTEND.SHOP_NAME'] = Phpfox::getParam('core.site_title');
        $parameters['FRONTEND.REDIRECT_TIME'] = "0";

        $parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
        $parameters['FRONTEND.LANGUAGE'] = "en";
        $parameters['REQUEST.VERSION'] = "1.0";

        $parameters['NAME.GIVEN'] = "";
        $parameters['NAME.FAMILY'] = "";
        $parameters['ADDRESS.STREET'] = "";
        $parameters['ADDRESS.ZIP'] = "";
        $parameters['ADDRESS.CITY'] = "";
        $parameters['ADDRESS.COUNTRY'] = "";
        $parameters['ADDRESS.STATE'] = "";
        $parameters['CONTACT.EMAIL'] = "";

        $parameters['NAME.GIVEN'] = "Markus";
        $parameters['NAME.FAMILY'] = "Mustermann";
        $parameters['ADDRESS.STREET'] = "Musterstrasse 1";
        $parameters['ADDRESS.ZIP'] = "12345";
        $parameters['ADDRESS.CITY'] = "Musterstadt";
        $parameters['ADDRESS.COUNTRY'] = "DE";
        $parameters['ADDRESS.STATE'] = "";
        $parameters['CONTACT.EMAIL'] = "test@example.com";

        //building the postparameter string to send into the WPF

        $result = '';
        foreach ($parameters AS $key => $value)
            $result .= strtoupper($key) . '=' . urlencode($value) . '&';
        $strPOST = stripslashes($result);
        //open the request url for the Web Payment Frontend
        $cpt = curl_init();
        curl_setopt($cpt, CURLOPT_URL, $url);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
        curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($cpt, CURLOPT_POST, 1);
        curl_setopt($cpt, CURLOPT_POSTFIELDS, $strPOST);
        $curlresultURL = curl_exec($cpt);
        $curlerror = curl_error($cpt);
        $curlinfo = curl_getinfo($cpt);
        curl_close($cpt);

        // parse results
        $returnvalue = array();
        $r_arr = explode("&", $curlresultURL);
        foreach ($r_arr AS $buf) {
            $temp = urldecode($buf);
            $temp = explode("=", $temp, 2);
            $postatt = $temp[0];
            $postvar = $temp[1];
            $returnvalue[$postatt] = $postvar;
        }
        return $returnvalue;
    }
}

?>