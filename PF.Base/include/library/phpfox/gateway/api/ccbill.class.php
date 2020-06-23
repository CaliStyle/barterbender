<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 */
class Phpfox_Gateway_Api_CCBill implements Phpfox_Gateway_Interface {

    /**
     * Holds an ARRAY of settings to pass to the form
     *
     * @var array
     */
    private $_aParam = array();

    /**
     * Holds an ARRAY of supported currencies for this payment gateway
     *
     * https://www.ccbill.com/cs/manuals/CCBill_Dynamic_Pricing.pdf
     * @var array
     */
    private $_aCurrency = array('USD' => "840", 'EUR'=>'978',"AUD"=>"036","CAD"=>"124","GBP"=>"826","JPY" =>"392" );

    /**
     * Class constructor
     *
     */
    public function __construct() {
        
    }

    /**
     * Set the settings to be used with this class and prepare them so they are in an array
     *
     * @param array $aSetting ARRAY of settings to prepare
     */
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
            'ccbill_accnum' => array(
                'phrase' => Phpfox::getPhrase('ynadvancedpayment.client_account_number'),
                'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.an_integer_value_representing_the_6_digit_client_account_number'),
                'value' => (isset($this->_aParam['setting']['ccbill_accnum']) ? $this->_aParam['setting']['ccbill_accnum'] : '')
            ),
            'ccbill_subaccnum' => array(
                'phrase' => Phpfox::getPhrase('ynadvancedpayment.client_sub_account_number'),
                'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.an_integer_value_representing_the_4_digit_client_subaccount_number'),
                'value' => (isset($this->_aParam['setting']['ccbill_subaccnum']) ? $this->_aParam['setting']['ccbill_subaccnum'] : '')
            ),
            'ccbill_salt' => array(
                'phrase' => Phpfox::getPhrase('ynadvancedpayment.salt_key'),
                'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.the_salt_value_is_used_by_ccbill_to_verify_the_hash_and_can_be_obtained_in_one_of_the_two_ways_br_1_contact_client_support_and_receive_the_salt_value_br_2_create_your_own_salt_value_up_to_32_alphanumeric_characters_and_provide_it_to_client_support'),
                'value' => (isset($this->_aParam['setting']['ccbill_salt']) ? $this->_aParam['setting']['ccbill_salt'] : '')
            ),
            'ccbill_form_id' => array(
                'phrase' => Phpfox::getPhrase('ynadvancedpayment.form_name'),
                'phrase_info' => Phpfox::getPhrase('ynadvancedpayment.the_name_of_the_form'),
                'value' => (isset($this->_aParam['setting']['ccbill_form_id']) ? $this->_aParam['setting']['ccbill_form_id'] : '')
            )
        );
    }

    /**
     * Returns the actual HTML <form> used to post information to the 3rd party gateway when purchasing
     * an item using this specific payment gateway
     *
     * @return bool FALSE if we can't use this payment gateway to purchase this item or ARRAY if we have successfully created a form
     */
    public function getForm() {
        if (!isset($this->_aCurrency[$this->_aParam['currency_code']])) {
            if (isset($this->_aParam['alternative_cost'])) {
                $aCosts = unserialize($this->_aParam['alternative_cost']);
                $bPassed = false;
                foreach ($aCosts as $sCode => $iPrice) {
                    if (isset($this->_aCurrency[$sCode])) {
                        $this->_aParam['amount'] = $iPrice;
                        $this->_aParam['currency_code'] = $this->_aCurrency[$sCode];
                        $bPassed = true;
                        break;
                    }
                }

                if ($bPassed === false) {
                    return false;
                }
            } else {
                return false;
            }
        }
        else{
            $this->_aParam['currency_code'] = $this->_aCurrency[$this->_aParam['currency_code']];
        }
        $aPayemtItem = explode("|", $this->_aParam['item_number']);
        $aForm = array(
            'url' => ('https://bill.ccbill.com/jpost/signup.cgi'),
            'param' => array(
                'clientAccnum' => $this->_aParam['setting']['ccbill_accnum'],
                'clientSubacc' => $this->_aParam['setting']['ccbill_subaccnum'],
                'formName' => $this->_aParam['setting']['ccbill_form_id'],
                'paymentItemId' => $aPayemtItem[1],
                'paymentTypeId' => $aPayemtItem[0],
                'itemName' => $this->_aParam['item_name'],
                'formPrice' => number_format($this->_aParam['amount'], 2, '.', ''),
                'formPeriod' => 365,
                'currencyCode' => $this->_aParam['currency_code']         
            )
        );
        $sSecurityCode = $this->_aParam['setting']["ccbill_salt"];

        if ($this->_aParam['recurring'] > 0) {
            switch ($this->_aParam['recurring'])
	        {
	            case '1':
	                $iDayRebill = 30;
	                break;
	            case '2':
	                $iDayRebill = 91;
	                break;
	            case '3':
	                $iDayRebill = 182;
	                break;
	            case '4':
	                $iDayRebill = 365;
	                break;              
	        }			
			
	        $aCosts = unserialize($this->_aParam['alternative_recurring_cost']);	
			$aForm['param']['formPeriod'] = $iDayRebill;
			$aForm['param']['formRecurringPrice'] = number_format($this->_aParam['recurring_cost'], 2, '.', ''); 
            // $aForm['param']['formRecurringPrice'] = number_format($aCosts[Phpfox::getService('core.currency')->getDefault()], 2, '.', '');
            //$aForm['param']['formRecurringPrice'] = number_format($this->_aParam['amount'], 2, '.', '');
			$aForm['param']['formRebills'] = 99;
			$aForm['param']['formRecurringPeriod'] = $iDayRebill;	

            $sHash = md5($aForm['param']["formPrice"].$aForm['param']["formPeriod"].$aForm['param']["formRecurringPrice"].$aForm['param']["formRecurringPeriod"].$aForm['param']["formRebills"].$aForm['param']["currencyCode"].$sSecurityCode);
        }
        else{
            $aForm['param']['formPeriod'] = 365;
            $sHash = md5($aForm['param']["formPrice"].$aForm['param']["formPeriod"].$aForm['param']["currencyCode"].$sSecurityCode);
        }
        $aForm['param']['formDigest'] = $sHash;

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
        $status_param = Phpfox::getLib("request")->get("status", 'not_existing_status_param');
        if($status_param == 'not_existing_status_param'){
            $sStatus = Phpfox::getLib("request")->get("req5");    
        } else {
            if ($status_param == "ccbill-success") {
                $sStatus = 'approved';    
            } else if ($status_param == "ccbill-fail") {
                $sStatus = 'denied';    
            }
        }
        // validate
        $bVerified = true;
        if(isset($this->_aParam["responseDigest"]))
        {
            $sValidateCode = "";
            if($sStatus == "approved"){
                $sValidateCode = md5($this->_aParam["subscription_id"]."1". $this->_aParam['setting']["ccbill_salt"]);
            }
            else if ($sStatus == "denied"){
                $sValidateCode = md5($this->_aParam["denialId"]."0". $this->_aParam['setting']["ccbill_salt"]);
            }
            if($sValidateCode != $this->_aParam["responseDigest"]){
                $bVerified = false;
            }
        }
        else{
            $bVerified = false;
        }

        if ($bVerified === true) 
        {
            $aParts = array();
            $aParts[0] = $this->_aParam['paymentTypeId'];
            $aParts[1] = $this->_aParam['paymentItemId'];
            if (Phpfox::isModule($aParts[0])) {
                if (Phpfox::hasCallback($aParts[0], 'paymentApiCallback')) {
                    if ($sStatus) {
                        switch ($sStatus) {
                            case 'approved':
                                $sStatus = 'completed';
                                break;
                            case 'denied':
                                $sStatus = 'cancel';
                                break;
                            default :
                                $sStatus = null;
                                break;
                        }
                    }
                    if ($sStatus !== null) 
                    {
                        Phpfox::callback($aParts[0] . '.paymentApiCallback', array(
                            'gateway' => 'ccbill',
                            'ref' => $this->_aParam['subscription_id'],
                            'status' => $sStatus,
                            'item_number' => $aParts[1],
                            'total_paid' => (isset($this->_aParam['initialPrice']) ? $this->_aParam['initialPrice'] : null)
                                )
                        );
                        header('HTTP/1.1 200 OK');
                    } else {
                    }
                } else {
                }
            } else {
            }
        } else {
        }
    }
}

?>