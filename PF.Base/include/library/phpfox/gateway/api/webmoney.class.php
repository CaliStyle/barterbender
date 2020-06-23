<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:10
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Gateway_Api_WebMoney implements Phpfox_Gateway_Interface
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
            'merchant_purse' => array(
                'phrase' => _p('merchant_purse'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['merchant_purse']) ? $this->_aParam['setting']['merchant_purse'] : '')
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
            'url' => 'https://merchant.wmtransfer.com/lmi/payment.asp',
            'param' => array(
                'LMI_PAYEE_PURSE' => $this->_aParam['setting']['merchant_purse'],
                'is_test' => $this->_aParam['is_test'] ? 1 : 0,
                'LMI_PAYMENT_DESC' => $this->_aParam['item_name'],
                'LMI_PAYMENT_NO' => $this->_aParam['item_number'],
                'currency_code' => $this->_aParam['currency_code'],
                'LMI_FAIL_URL' => Phpfox::getLib('gateway')->url('webmoney'),
                'LMI_FAIL_METHOD' => '2',
                'LMI_SUCCESS_URL' => Phpfox::getLib('gateway')->url('webmoney'),
                'LMI_SUCCESS_METHOD' => '2',
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
                $aForm['param']['LMI_PAYMENT_AMOUNT'] = $this->_aParam['amount'];
            }
        }
        else
        {
            $aForm['param']['cmd'] = 'not_recurring';
            $aForm['param']['LMI_PAYMENT_AMOUNT'] = $this->_aParam['amount'];
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
        Phpfox::log('Starting Webmoney callback');

        Phpfox::getService('api.gateway.process')->addLog('webmoney',$_POST);
        Phpfox::getService('api.gateway.process')->addLog('webmoney',$this->_aParam);
        $bVerified = true;
        Phpfox::log('Attempting callback');
        if(isset($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST'] == 1)
        {
            if(isset($_POST['LMI_PAYMENT_NO']))
            {
                if (!empty($_POST['LMI_SYS_TRANS_NO']))
                {
                    //success
                    $paymentStatus = 'okay';
                }
                else
                {
                    //fail
                    $bVerified = false;
                }
            }
            else
            {
                $bVerified = false;
            }
            if ($bVerified === true)
            {
                Phpfox::log('Callback OK');
                $isApp = false;
                $aParts = explode('|', $_POST['LMI_PAYMENT_NO']);
                if (substr($aParts[0], 0, 5) == '@App/') {
                    $isApp = true;
                    Phpfox::log('Is an APP.');
                } else {
                    $isApp = Phpfox::isAppAlias($aParts[0]);
                }
                Phpfox::log('Attempting to load module: ' . $aParts[0]);
                if ($isApp || Phpfox::isModule($aParts[0]))
                {
                    Phpfox::log('Module is valid.');
                    Phpfox::log('Checking module callback for method: paymentApiCallback');
                    if ($isApp || (Phpfox::isModule($aParts[0]) && Phpfox::hasCallback($aParts[0], 'paymentApiCallback')))
                    {
                        Phpfox::log('Module callback is valid.');
                        Phpfox::log('Building payment status: ' . (isset($this->_aParam['payment_status']) ? $this->_aParam['payment_status'] : '') . ' (' . (isset($this->_aParam['txn_type']) ? $this->_aParam['txn_type'] : '') . ')');

                        $sStatus = null;
                        if (isset($this->_aParam['payment_status']))
                        {
                            switch ($this->_aParam['payment_status'])
                            {
                                case 'Completed':
                                    $sStatus = 'completed';
                                    break;
                                case 'Pending':
                                    $sStatus = 'pending';
                                    break;
                                case 'Refunded':
                                case 'Reversed':
                                    $sStatus = 'cancel';
                                    break;
                            }
                        }

                        if (isset($this->_aParam['txn_type']))
                        {
                            switch ($this->_aParam['txn_type'])
                            {
                                case 'subscr_cancel':
                                case 'subscr_failed':
                                    $sStatus = 'cancel';
                                    break;
                            }
                        }

                        Phpfox::log('Status built: ' . $sStatus);

                        if ($sStatus !== null)
                        {
                            Phpfox::log('Executing module callback');

                            $params = array(
                                'gateway' => 'webmoney',
                                'ref' => $this->_aParam['subscription_id'],
                                'status' => $sStatus,
                                'item_number' => $aParts[1],
                                'total_paid' => (isset($this->_aParam['initialPrice']) ? $this->_aParam['initialPrice'] : null)
                            );

                            if ($isApp && !Phpfox::isAppAlias($aParts[0])) {
                                $callback = str_replace('@App/', '', $aParts[0]);
                                Phpfox::log('Running app callback on: ' . $callback);
                                \Core\Payment\Trigger::event($callback, $params);
                            }
                            else {
                                Phpfox::callback($aParts[0] . '.paymentApiCallback', $params);
                            }

                            header('HTTP/1.1 200 OK');
                        }
                        else
                        {
                            Phpfox::log('Status is NULL. Nothing to do');
                        }
                    }
                    else
                    {
                        Phpfox::log('Module callback is not valid.');
                    }
                }
            }
        }

    }
}