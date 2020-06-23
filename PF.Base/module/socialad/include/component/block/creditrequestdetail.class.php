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

class Socialad_Component_Block_Creditrequestdetail extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bNoButton = $this->getParam('bNoButton', false);
		$id = $this->getParam('id');
		$creditMoneyRequest = Phpfox::getService('socialad.ad')->getCreditMoneyRequestById($id);
		$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

		$this->template()->assign(array(
			'aCurrentCurrency' => isset($aCurrentCurrencies[0]) ? $aCurrentCurrencies[0] : null, 
			'bNoButton' => $bNoButton, 
			'creditMoneyRequest' => $creditMoneyRequest, 
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

