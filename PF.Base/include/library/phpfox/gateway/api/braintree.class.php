<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:10
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Gateway_Api_Braintree implements Phpfox_Gateway_Interface
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
            'merchant_id' => array(
                'phrase' => _p('merchant_id'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['merchant_id']) ? $this->_aParam['setting']['merchant_id'] : '')
            ),
            'public_key' => array(
                'phrase' => _p('public_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['public_key']) ? $this->_aParam['setting']['public_key'] : '')
            ),
            'private_key' => array(
                'phrase' => _p('private_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['private_key']) ? $this->_aParam['setting']['private_key'] : '')
            ),
            'cse_key' => array(
                'phrase' => _p('cse_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['cse_key']) ? $this->_aParam['setting']['cse_key'] : '')
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
            'url' => ($this->_aParam['is_test'] ?  Phpfox::getLib('url')->makeUrl('ynadvancedpayment.braintree', array('mode' => 0)) : Phpfox::getLib('url')->makeUrl('ynadvancedpayment.braintree', array('mode' => 1))),
            'param' => array(
                'merchant_id' => $this->_aParam['setting']['merchant_id'],
                'public_key' => $this->_aParam['setting']['public_key'],
                'private_key' => $this->_aParam['setting']['private_key'],
                'cse_key' => $this->_aParam['setting']['cse_key'],
                'is_test' => $this->_aParam['is_test'] ? 1 : 0,
                'item_name' => $this->_aParam['item_name'],
                'item_number' => $this->_aParam['item_number'],
                'currency_code' => $this->_aParam['currency_code'],
                'notify_url' => Phpfox::getLib('gateway')->url('braintree'),
                'return' => $this->_aParam['return'],
            )
        );

        if ($this->_aParam['recurring'] > 0)
        {
            switch ($this->_aParam['recurring'])
            {
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
                $aForm['param']['recurrence_type'] = $sPeriod;
                $aForm['param']['recurrence'] = $iEach;
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
        $input = @file_get_contents("php://input");
        $oParams = json_decode($input);
        if(empty($oParams) || count($oParams) == 0)
        {
            $oParams = $_REQUEST;
        }
        Phpfox::getService('api.gateway.process')->addLog('braintree',$oParams);
        return Phpfox::getService('ynadvancedpayment.braintree')->callback($oParams);
        http_response_code(200);
    }
}