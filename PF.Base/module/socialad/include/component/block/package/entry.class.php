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
class Socialad_Component_Block_Package_Entry extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aPackage = $this->getParam('aSaPackage');
		$bNoCreateBtn = $this->getParam('bNoCreateBtn');

		$this->template()->assign(array( 
			'aSaPackage' => $aPackage,
			'bNoCreateBtn' => $bNoCreateBtn
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

