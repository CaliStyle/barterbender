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
class Socialad_Component_Block_Ad_Addedit_Placement extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPackageId = $this->getParam('iSaPackageId');
		$aModules = Phpfox::getService('socialad.package')->getModulesOfPackage($iPackageId);
		$newModules = array();
		foreach($aModules as $key => $val){
			$newModules[$val] = $val;
		}
		$aBlocks = Phpfox::getService('socialad.package')->getBlocksOfPackage($iPackageId);

		$aModules = Phpfox::getService('socialad.helper')->convertModuleToFriendlyName($newModules);
        unset($aModules['socialad']);
		$this->template()->assign(array( 
			'aSaBlocks' => $aBlocks,
			'aSaModules' => $aModules
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

