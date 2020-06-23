<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Creditmoney_Filter extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCore = $this->request()->get('core');
		$yncm_user_id = $this->getParam('yncm_user_id');

		if(!isset($aCore['is_admincp'])){
			if(!Phpfox::isAdminPanel()) {		
				// in front end
				$yncm_user_id = Phpfox::getUserId();
			} else {

			}
		} else if($aCore['is_admincp'] !=  1){
			// in front end
			// check for ajax request 
			$yncm_user_id = Phpfox::getUserId();
		}		

		$aCreditMoney = Phpfox::getService('socialad.ad')->getCreditMoneyByUserId((int)$yncm_user_id);
		$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

		$this->template()->assign(array( 
			'aCreditMoney' => $aCreditMoney, 
			'yncm_user_id' => $yncm_user_id, 
			'aCurrentCurrency' => isset($aCurrentCurrencies[0]) ? $aCurrentCurrencies[0] : null, 
			'aCreditMoneyRequestStatus' => Phpfox::getService('socialad.ad')->getAllCreditMoneyRequestStatuses(),
            'bIsAdminManage' => $this->getParam('bIsAdminManage',false)
		));

	}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

