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

class Socialad_Component_Block_Addrequestinadmin extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$userid = $this->request()->get('userid');
		$bNoButton = $this->getParam('bNoButton', false);
		$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

		$this->template()->assign(array(
			'aCurrentCurrency' => isset($aCurrentCurrencies[0]) ? $aCurrentCurrencies[0] : null, 
			'bNoButton' => $bNoButton, 
			'userid' => $userid, 
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

