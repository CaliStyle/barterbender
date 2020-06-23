    <?php
defined('PHPFOX') or exit('NO DICE!');
define("YNAP_START_YEAR",2017);
define("YNAP_END_YEAR",2040);

class Ynadvancedpayment_Component_Controller_GoPay extends Phpfox_Component
{
	public function process()
	{

		// get data for GoPay
		$aTransactionItem = Phpfox::getLib('session')->get('item_info');

		if($this->request()->get('item_name')){
			$aTransactionItem['item_name'] = $this->request()->get('item_name');
			$aTransactionItem['item_number'] = $this->request()->get('item_number');
			$aTransactionItem['currency_code'] = $this->request()->get('currency_code');
			$aTransactionItem['notify_url'] = $this->request()->get('notify_url');
			$aTransactionItem['return'] = $this->request()->get('return');
			$aTransactionItem['cmd'] = $this->request()->get('cmd');
			$aTransactionItem['amount'] = round($this->request()->get('amount'));
			$aTransactionItem['recurring_cost'] = $this->request()->get('recurring_cost');
			$aTransactionItem['recurrence'] = $this->request()->get('recurrence');
			$aTransactionItem['recurrence_type'] = $this->request()->get('recurrence_type');

			Phpfox::getLib('session')->set('item_info',$aTransactionItem);

		}

		if(empty($aTransactionItem)){
                return $this->url()->send('',array(),_p('ynadvancedpayment.transaction_is_expired'));
		}

		if($aTransactionItem['currency_code'] != 'EUR' && $aTransactionItem['currency_code'] != 'CZK'){
                return $this->url()->send('',array(),_p('ynadvancedpayment.gopay_getway_only_support_eur_czk'));
		}

		$aValidationParam = $this->_getValidationParams();
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynap_gopay_form',
                'aParams' => $aValidationParam
            )
        );

        if ($this->_checkIfSubmittingAForm()) {
            $aVals = $this->request()->getArray('val');
            
            $aValidationParam = $this->_getValidationParams($aVals);

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ynap_gopay_form',
                    'aParams' => $aValidationParam
                )
            );

            $check2 = $oValid->isValid($aVals);
            $check1 = $this->_verifyCustomForm($aVals);

            if ($check1 && $check2)
            {
				Phpfox::getService('ynadvancedpayment.gopayaim')->process_payment($aVals);

            }
        }

		$months = array(
			"01"	=> _p('core.january'),
			"02"	=> _p('core.february'),
			"03"	=> _p('core.march'),
			"04"	=> _p('core.april'),
			"05"	=> _p('core.may'),
			"06"	=> _p('core.june'),
			"07"	=> _p('core.july'),
			"08"	=> _p('core.august'),
			"09"	=> _p('core.september'),
			"10"	=> _p('core.october'),
			"11"	=> _p('core.november'),
			"12"	=> _p('core.december')
			); 
		$years = array();
		for($i = YNAP_START_YEAR; $i < YNAP_END_YEAR; $i++){
			$years[$i] = $i;
		}

        $this->template()->setTitle((_p('ynadvancedpayment.gopay_billing_info')))
            ->setPhrase(array(
            ))
            ->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
            ))
            ->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'months' => $months,
                'years' => $years,
                'aData' => $aTransactionItem,
            ));		

	}

    private function _verifyCustomForm($aVals)
    {
    	$result = true;
        if(isset($aVals['email_address'])) {
        }

        return $result;
    }	

    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }	

    private function _getValidationParams($aVals = array()) {

        $aParam = array();

        return $aParam;
    }


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ynadvancedpayment.component_controller_authorizenet_clean')) ? eval($sPlugin) : false);
	}

}
