<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:10
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Gateway_Api_BitPay implements Phpfox_Gateway_Interface
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
    public function __construct()
    {

    }

    public function set($aSetting)
    {
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
    public function getEditForm()
    {
        return array(
            'merchant_api_key' => array(
                'phrase' => _p('merchant_api_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['merchant_api_key']) ? $this->_aParam['setting']['merchant_api_key'] : '')
            ),

        );
    }

    public function getForm()
    {
        $bCurrencySupported = true;

        if (!in_array($this->_aParam['currency_code'], $this->_aCurrency)) {
            if (!empty($this->_aParam['alternative_cost'])) {
                $aCosts = unserialize($this->_aParam['alternative_cost']);
                foreach ($aCosts as $aCost) {
                    $sCode = key($aCost);
                    $iPrice = $aCost[key($aCost)];

                    if (in_array($sCode, $this->_aCurrency)) {
                        $this->_aParam['amount'] = $iPrice;
                        $this->_aParam['currency_code'] = $sCode;
                        $bCurrencySupported = false;
                        break;
                    }
                }

                if ($bCurrencySupported === true) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $aForm = array(
            'url' => ($this->_aParam['is_test'] ? Phpfox::getLib('url')->makeUrl('ynadvancedpayment.bitpay', array('mode' => 0)) : Phpfox::getLib('url')->makeUrl('ynadvancedpayment.bitpay', array('mode' => 1))),
            'param' => array(
                'merchant_api_key' => $this->_aParam['setting']['merchant_api_key'],
                'is_test' => $this->_aParam['is_test'] ? 1 : 0,
                'item_name' => $this->_aParam['item_name'],
                'item_number' => $this->_aParam['item_number'],
                'currency_code' => $this->_aParam['currency_code'],
                'notify_url' => Phpfox::getLib('gateway')->url('bitpay'),
                'return' => $this->_aParam['return'],
            )
        );

        if ($this->_aParam['recurring'] > 0) {
            switch ($this->_aParam['recurring']) {
                case '1':
                    $sPeriod = 'month';
                    $iEach = 1;
                    break;
                case '2':
                    $sPeriod = 'month';
                    $iEach = 3;
                    break;
                case '3':
                    $sPeriod = 'month';
                    $iEach = 6;
                    break;
                case '4':
                    $sPeriod = 'year';
                    $iEach = 1;
                    break;
            }

            if ((!isset($this->_aParam['recurring_cost']) || empty($this->_aParam['recurring_cost']))
                && !empty($this->_aParam['alternative_recurring_cost'])
            ) {
                $aCosts = unserialize($this->_aParam['alternative_recurring_cost']);
                $bPassed = false;
                foreach ($aCosts as $aCost) {
                    foreach ($aCost as $sKey => $iCost) {
                        if (in_array($sKey, $this->_aCurrency)) {
                            // Make all in the same currency
                            $this->_aParam['currency_code'] = $sKey;
                            $this->_aParam['amount'] = unserialize($this->_aParam['alternative_cost']);
                            $this->_aParam['amount'] = $this->_aParam['amount'][0][$sKey];

                            $this->_aParam['recurring_cost'] = $iCost;
                            if (is_array($this->_aParam['recurring_cost'])) {
                                $aRec = array_values($this->_aParam['recurring_cost']);
                                $this->_aParam['recurring_cost'] = array_shift($aRec);
                            }
                            $bPassed = true;
                            break;
                        }
                    }

                    if ($bPassed) {
                        break;
                    }
                }

                if ($bPassed === false) {
                    return false;
                }
            }

            // If recurring is not zero, set the recurring settings
            if ($this->_aParam['recurring_cost'] > 0) {
                $aForm['param']['cmd'] = 'recurring';
                $aForm['param']['amount'] = $this->_aParam['amount'];
                $aForm['param']['recurrence_type'] = $sPeriod;
                $aForm['param']['recurrence'] = $iEach;
                $aForm['param']['recurring_cost'] = $this->_aParam['recurring_cost']; // $aCosts[$this->_aParam['currency_code']]; change made for 3.7.1
            } // if zero, why to set recurring?
            else {
                $aForm['param']['cmd'] = 'not_recurring';
                $aForm['param']['amount'] = $this->_aParam['amount'];
            }
        } else {
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
        $input = @file_get_contents("php://input");
        $params = json_decode($input,true);
        Phpfox::getService('api.gateway.process')->addLog('bitpay', $input);
        if ($params['status'] == "complete" || $params['status'] == "confirmed") {
            $order_id = json_decode(json_decode($params['posData'])->posData)->orderId;
            $order_id = explode('|', $order_id);
            if (Phpfox::isModule($order_id[0])) {
                if (Phpfox::hasCallback($order_id[0], 'paymentApiCallback')) {
                    $sStatus = 'completed';
                    if ($sStatus !== null) {
                        Phpfox::callback($order_id[0] . '.paymentApiCallback', array(
                                'gateway' => 'bitpay',
                                'status' => $sStatus,
                                'item_number' => $order_id[1],
                                'total_paid' => $params['price']
                            )
                        );
                        Phpfox::getService('api.gateway.process')->addLog('bitpay', Phpfox::endLog());
                        return $this->url()->send($order_id[0], null, null);
                    } else {
                    }
                } else {
                }
            } else {
            }
        }
        http_response_code(200);
        return false;
    }
}