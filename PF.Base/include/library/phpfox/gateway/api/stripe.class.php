<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:10
 */

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(dirname(dirname(dirname(dirname(__file__)))))) . '/module/ynadvancedpayment/include/service/Stripe/Stripe.php';
class Phpfox_Gateway_Api_Stripe implements Phpfox_Gateway_Interface
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
            'stripe_secret_key' => array(
                'phrase' => _p('secret_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['stripe_secret_key']) ? $this->_aParam['setting']['stripe_secret_key'] : '')
            ),
            'stripe_public_key' => array(
                'phrase' => _p('public_key'),
                'phrase_info' => '',
                'value' => (isset($this->_aParam['setting']['stripe_public_key']) ? $this->_aParam['setting']['stripe_public_key'] : '')
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
            'url' => ($this->_aParam['is_test'] ?  Phpfox::getLib('url')->makeUrl('ynadvancedpayment.stripe', array('mode' => 0)) : Phpfox::getLib('url')->makeUrl('ynadvancedpayment.stripe', array('mode' => 1))),
            'param' => array(
                'stripe_secret_key' => $this->_aParam['setting']['stripe_secret_key'],
                'stripe_public_key' => $this->_aParam['setting']['stripe_public_key'],
                'is_test' => $this->_aParam['is_test'] ? 1 : 0,
                'item_name' => $this->_aParam['item_name'],
                'item_number' => $this->_aParam['item_number'],
                'currency_code' => $this->_aParam['currency_code'],
                'notify_url' => Phpfox::getLib('gateway')->url('stripe'),
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
        Phpfox::getService('ynadvancedpayment.stripe')->setApiPublicKey($this->_aParam['setting']['secret_key']);
        $input = @file_get_contents("php://input");
        $oParams = json_decode($input);
        if($oParams->type == "charge.succeeded")
        {
            Phpfox::log('Callback OK');
            $cus_id = $oParams -> data -> object -> customer;
            $customer_info = Stripe_Customer::retrieve($cus_id);
            $order_id = $customer_info -> metadata['order_id'];

            $activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('stripe', false);
            if(!isset($activeGateway['gateway_id'])){
                // Gateway detection failed
                Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
                return false;
            }
            try
            {
                // Process generic Silent Post Responce data ------------------------------------------------
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
                                    'price' => (float)($oParams -> data -> object -> amount / 100),
                                    'status' => $sStatus,
                                ));

                                if($iPurchaseId)
                                {
                                    Phpfox::callback($module . '.paymentApiCallback', array(
                                            'gateway' => 'stripe',
                                            'status' => $sStatus,
                                            'item_number' => $iPurchaseId,
                                            'total_paid' => (float)($oParams -> data -> object -> amount / 100)
                                        )
                                    );
                                    // Exit
                                    echo 'OK';
                                    Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
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
            catch (Exception $e)
            {
                echo 'ERR';
                Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
                return;
            }
        }
        http_response_code(200); // PHP 5.4 or greater
    }
}