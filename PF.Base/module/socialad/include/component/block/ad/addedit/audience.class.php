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
class Socialad_Component_Block_Ad_Addedit_Audience extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPackageId = $this->getParam('iSaPackageId');
		$aAllCountries = Phpfox::getService('core.country')->getCountriesAndChildren();
		$aGenders = Phpfox::getService('core')->getGenders();

		$aAge = array();
		$iAgeEnd = date('Y')-Phpfox::getParam('user.date_of_birth_start');
		$iAgeStart = date('Y')-Phpfox::getParam('user.date_of_birth_end');
		for ($iAgeStart; $iAgeStart <= $iAgeEnd; $iAgeStart++)
		{
			$aAge[$iAgeStart] = $iAgeStart;
		}	
		$this->template()->assign(array( 
			'aAllCountries' => $aAllCountries,
			'aGenders' => $aGenders,
			'aAge' => $aAge
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

