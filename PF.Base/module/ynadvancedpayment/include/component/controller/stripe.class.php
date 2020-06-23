<?php
defined('PHPFOX') or exit('NO DICE!');
define("YNAP_START_YEAR",2017);
define("YNAP_END_YEAR",2040);

class Ynadvancedpayment_Component_Controller_Stripe extends Phpfox_Component
{
	public function process()
	{
		$activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('stripe', false);

		// get data for Authorize.Net
		$aData = array();
		$aData['stripe_secret_key'] = $this->request()->get('stripe_secret_key');
		$aData['stripe_public_key'] = $this->request()->get('stripe_public_key');
		$aData['is_test'] = $this->request()->get('is_test');
		$aData['item_name'] = $this->request()->get('item_name');
		$aData['item_number'] = $this->request()->get('item_number');
		$aData['currency_code'] = $this->request()->get('currency_code');
		$aData['notify_url'] = $this->request()->get('notify_url');
		$aData['return'] = $this->request()->get('return');
		$aData['cmd'] = $this->request()->get('cmd');
		$aData['amount'] = $this->request()->get('amount');
		$aData['recurring_cost'] = $this->request()->get('recurring_cost');
		$aData['recurrence'] = $this->request()->get('recurrence');
		$aData['recurrence_type'] = $this->request()->get('recurrence_type');
		$aParts = explode('|', $aData['item_number']);
        $sReturn = ($aParts[0] == 'betterads') ? 'ads' : $aParts[0];
		if(!isset($aData['stripe_secret_key']) || empty($aData['stripe_secret_key'])){
			return $this->url()->send($sReturn, null, null);
		}
        elseif (preg_match("/(http|https):\/\//i", $aData['return'])) {
            $sReturn = $aData['return'];
        }
		$aValidationParam = $this->_getValidationParams();
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynap_stripe_form',
                'aParams' => $aValidationParam
            )
        );
        if ($this->_checkIfSubmittingAForm()) {
            $aVals = $this->request()->getArray('val');
            
            $aValidationParam = $this->_getValidationParams($aVals);

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ynap_stripe_form',
                    'aParams' => $aValidationParam
                )
            );

            $check2 = $oValid->isValid($aVals);

            if ($check2)
            {
            	$aVals = array_merge($aVals, $aData);
            	$aVals['total'] = $aData['amount'];
                if($aData['cmd'] == 'recurring')
                {
                    // process recurring
                    $resp = Phpfox::getService('ynadvancedpayment.stripe')->process_payment(
                        array(
                            'config' => array(
                                'stripe_public_key' => $aData['stripe_public_key'],
                                'stripe_secret_key' => $aData['stripe_secret_key'],
                            ),
                            'test_mode' => $aData['is_test'],
                        )
                        , $aVals
                        , array(
                            'recurrence' => $aData['recurrence'],
                            'recurrence_type' => $aData['recurrence_type'],
                        )
                    );
                }
                else{

                    $resp = Phpfox::getService('ynadvancedpayment.stripe')->process_payment(
                        array(
                            'config' => array(
                                'stripe_public_key' => $aData['stripe_public_key'],
                                'stripe_secret_key' => $aData['stripe_secret_key'],
                            ),
                            'test_mode' => $aData['is_test'],
                        ),
                        $aVals
                    );
                }
				if (!empty($resp['failed']) || !empty($resp['error_message']))
				{
					if(!empty($resp['error_message']))
					{
						Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
	                    return $this->url()->send($sReturn, null, $resp['error_message']);
		            }
		            else
		            {
		            	Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());
	                    return $this->url()->send($sReturn, null, _p('there_has_been_a_problem_with_your_transaction_please_verify_your_payment_details_and_try_again'));
		            }
				} 
				else 
				{
					if($aData['cmd'] == 'recurring')
					{
							$aPurchase = Phpfox::getService('subscribe.purchase')->getPurchase((int)$aParts[1]);

							Phpfox::getService('ynadvancedpayment.process')->addSubscriptions(array(
					            'user_id' => Phpfox::getUserId(),
					            'getaway_subscription_id' => $resp,
					            'gateway_id' => $activeGateway['gateway_id'],
					            'package_id' => (int)$aPurchase['package_id'],
					            'purchase_id' => (int)$aPurchase['purchase_id'],
							));
					} 

					// process callback 
					if (Phpfox::isModule($aParts[0]))
					{
						if (Phpfox::hasCallback($aParts[0], 'paymentApiCallback'))
						{
							$sStatus = 'completed';		
							if ($sStatus !== null)
							{
								Phpfox::callback($aParts[0] . '.paymentApiCallback', array(
										'gateway' => 'stripe',
										'status' => $sStatus,
										'item_number' => $aParts[1],
										'total_paid' => $aData['amount']
									)
								);
								Phpfox::getService('api.gateway.process')->addLog('stripe', Phpfox::endLog());

								return $this->url()->send($sReturn, null, _p('your_purchase_has_just_been_made_successfully'));
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

        $this->template()->setTitle((_p('stripe_billing_info')))
            ->setPhrase(array(
            ))
            ->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                '<script src="https://js.stripe.com/v2/"></script>',
                'buy.js' => 'module_ynadvancedpayment'
            ))
            ->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'months' => $months,
                'years' => $years,
                'aData' => $aData,
            ));		


	}


    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }	

    private function _getValidationParams($aVals = array()) {

        $aParam = array(
            'credit_card_number' => array(
                'def' => 'required',
                'title' => _p('please_fill_credit_card_number'),
            ),
            'CVV2' => array(
                'def' => 'required',
                'title' => _p('please_fill_card_security_code'),
            ),
        );

        return $aParam;
    }


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ynadvancedpayment.component_controller_stripe_clean')) ? eval($sPlugin) : false);
	}

}
