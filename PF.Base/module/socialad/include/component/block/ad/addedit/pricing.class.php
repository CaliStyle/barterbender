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
class Socialad_Component_Block_Ad_Addedit_Pricing extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPackageId = $this->getParam('iSaPackageId');
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($iPackageId);
		if($aPackage['package_is_free']) {
			return false;
		}
		$this->template()->assign(array( 
			'aPricingPackage' => $aPackage,
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

