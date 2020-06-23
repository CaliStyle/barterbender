<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:10
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Gateway_Api_Skrill implements Phpfox_Gateway_Interface
{
    /**
     * Holds an ARRAY of settings to pass to the form
     *
     * @var array
     */
    private $_aParam = array();


    private $_aCurrency = array('USD', 'CAD', 'GBP', 'EUR', 'AUD', 'NZD');

    /**
     * Class constructor
     *
     */
    public function __construct() {

    }

    public function set($aSetting) {
        $this->_aParam = $aSetting;

        if (Phpfox::getLib('parse.format')->isSerialized($aSetting['setting'])) {
            $this->_aParam['setting'] = unserialize($aSetting['setting']);
        }
    }

    /**
     * Each gateway has a unique list of params that must be passed with the HTML form when posting it
     * to their site. This method creates that set of custom fields.
     *
     * @return array ARRAY of all the custom params
     */
    public function getEditForm() {
        return array(
            'merchant_email' => array(
                'phrase' => _p('merchant_email'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['merchant_email']) ? $this->_aParam['setting']['merchant_email'] : '')
            ),
            'secret_word' => array(
                'phrase' => _p('secret_word'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['secret_word']) ? $this->_aParam['setting']['secret_word'] : '')
            ),
        );
    }

    public function getForm() {
        $bCurrencySupported = true;

        if (!in_array($this->_aParam['currency_code'], $this->_aCurrency))
        {
            if (!empty($this->_aParam['alternative_cost']))
            {
                $aCosts = unserialize($this->_aParam['alternative_cost']);
                foreach ($aCosts as $aCost)
                {
                    $sCode = key($aCost);
                    $iPrice = $aCost[key($aCost)];

                    if (in_array($sCode, $this->_aCurrency))
                    {
                        $this->_aParam['amount'] = $iPrice;
                        $this->_aParam['currency_code'] = $sCode;
                        $bCurrencySupported = false;
                        break;
                    }
                }

                if ($bCurrencySupported === true)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        $aForm = array(
            'url' => 'https://www.moneybookers.com/app/payment.pl',
            'param' => array(
                'pay_to_email' => $this->_aParam['setting']['merchant_email'],
                'secret_word' => $this->_aParam['setting']['secret_word'],
                'is_test' => $this->_aParam['is_test'] ? 1 : 0,
                'detail1_text' => $this->_aParam['item_name'],
                'detail1_description' => '',
                'transaction_id' => $this->_aParam['item_number'],
                'currency' => $this->_aParam['currency_code'],
                'return_url' => $this->_aParam['return'],
                'status_url' => Phpfox::getLib('gateway')->url('skrill'),
                'language' => 'en',
                'return' => $this->_aParam['return'],
            )
        );

        if ($this->_aParam['recurring'] > 0)
        {
            switch ($this->_aParam['recurring'])
            {
                case '1':
                    $length = 30;
                    break;
                case '2':
                    $length = 3 * 30;
                    break;
                case '3':
                    $length = 6 * 30;
                    break;
                case '4':
                    $length = 12 * 30;
                    break;
            }
            $iPeriod = $length;

            if ((!isset($this->_aParam['recurring_cost']) || empty($this->_aParam['recurring_cost']))
                && !empty($this->_aParam['alternative_recurring_cost']))
            {
                $aCosts = unserialize($this->_aParam['alternative_recurring_cost']);
                $bPassed = false;
                foreach ($aCosts as $aCost)
                {
                    foreach($aCost as $sKey => $iCost)
                    {
                        if (in_array($sKey, $this->_aCurrency))
                        {
                            // Make all in the same currency
                            $this->_aParam['currency_code'] = $sKey;
                            $this->_aParam['amount'] = unserialize($this->_aParam['alternative_cost']);
                            $this->_aParam['amount'] = $this->_aParam['amount'][0][$sKey];

                            $this->_aParam['recurring_cost'] = $iCost;
                            if (is_array($this->_aParam['recurring_cost']))
                            {
                                $aRec = array_values($this->_aParam['recurring_cost']);
                                $this->_aParam['recurring_cost'] = array_shift($aRec);
                            }
                            $bPassed = true;
                            break;
                        }
                    }

                    if($bPassed)
                    {
                        break;
                    }
                }

                if ($bPassed === false)
                {
                    return false;
                }
            }

            // If recurring is not zero, set the recurring settings
            if($this->_aParam['recurring_cost'] > 0)
            {
                $aForm['param']['cmd'] = 'recurring';
                $aForm['param']['amount'] = $this->_aParam['amount'];
                $aForm['param']['rec_amount'] = $this->_aParam['amount'];
                $aForm['param']['rec_period'] = $iPeriod;
                $aForm['param']['recurrence_type'] = 'day';
                //$aForm['param']['return_url'] = Phpfox::getParam('core.path') . 'api/gateway/callback/skrill/type_recurring-callback/';
                $aForm['param']['recurring_cost'] = $this->_aParam['recurring_cost']; // $aCosts[$this->_aParam['currency_code']]; change made for 3.7.1
            }
            // if zero, why to set recurring?
            else
            {
                $aForm['param']['cmd'] = 'not_recurring';
                $aForm['param']['amount'] = $this->_aParam['amount'];
            }
        }
        else
        {
            $aForm['param']['cmd'] = 'not_recurring';
            $aForm['param']['amount'] = $this->_aParam['amount'];
        }

        return $aForm;
    }

    /**
     * Performs the callback routine when the 3rd party payment gateway sends back a request to the server,
     * which we must then back and verify that it is a valid request. This then connects to a specific module
     * based on the information passed when posting the form to the server.
     *
     */
    public function callback()
    {
        $params = $_REQUEST;
        $secret = $this->_aParam['setting']['secret_word'];
        Phpfox::getService('api.gateway.process')->addLog('skrill', $params);
        $bVerified = "true";
        $activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('skrill', false);
        if(!$activeGateway) {
            return false;
        }
        $status = $params['status'];
        if (isset($params["md5sig"])) {
            $sValidateCode = $_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper(md5($secret)) . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];
            if ($status == "2")
            {
                //processed
            }
            else {
                if ($status == "-2") {
                    //failed
                    $bVerified = "false";
                }
            }
            if (strtoupper(md5($sValidateCode)) != $params["md5sig"]) {
                $bVerified = "false";
            }
        }
        else {
            $bVerified = "false";
        }
        if ($bVerified === "true") {

            $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
            $resp['authorized'] = TRUE;
            $resp['transaction_id'] = $params['transaction_id'];
            $resp['amount'] = $params['amount'];
            $resp['currency'] = $sDefaultCurrency;
            try {
                // Process generic Silent Post Responce data ------------------------------------------------
                $aParts = explode('|',$resp['transaction_id']);

                if (Phpfox::isModule($aParts[0])) {
                    if (Phpfox::hasCallback($aParts[0], 'paymentApiCallback')) {
                        $sStatus = 'completed';
                        if ($sStatus !== null) {

                            Phpfox::callback($aParts[0] . '.paymentApiCallback', array(
                                    'gateway' => 'skrill',
                                    'status' => $sStatus,
                                    'item_number' => $aParts[1],
                                    'total_paid' => $resp['amount']
                                )
                            );
                            // Exit
                            echo 'OK';
                            Phpfox::getService('api.gateway.process')->addLog('skrill', Phpfox::endLog());
                            exit(0);

                        }
                        else {
                        }
                    }
                    else {
                    }
                }
                else {
                }
            }
            catch (Exception $e) {
                echo 'ERR';
                Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
                return;
            }
        }
        else {
            //Fail
            exit;
        }
    }
}