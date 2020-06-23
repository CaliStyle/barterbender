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
class Socialad_Component_Block_Pay_Later extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->getParam('iAdId');
		$bNoButton = $this->getParam('bNoButton', false);

		$this->template()->assign(array(
			'sManualPaymentInstructions' => Phpfox::getService('socialad.custominfor')->getManualPaymentInstructions(),
			'aSaAd' => Phpfox::getService('socialad.ad')->getAdById($iAdId),
			'bNoButton' => $bNoButton
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

