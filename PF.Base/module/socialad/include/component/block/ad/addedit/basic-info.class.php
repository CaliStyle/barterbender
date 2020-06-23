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
class Socialad_Component_Block_Ad_Addedit_Basic_Info extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPackageId = $this->getParam('iSaPackageId');
		$aAd = $this->getParam('aSaBasicInfoAd');
		
		$aItemTypes = Phpfox::getService('socialad.package')->getItemTypesOfPackage($iPackageId);
		$aAdTypes = Phpfox::getService('socialad.package')->getAdTypesOfPackage($iPackageId);

		if($aAd) {
			$iDefaultAdTypeId = $aAd['ad_type'];
			$iDefaultItemTypeId = $aAd['ad_item_type'];
		} else {
			$aDefaultAdType = current($aAdTypes);
			$iDefaultAdTypeId = $aDefaultAdType['id'];
			$aDefaultItemType = current($aItemTypes);
			$iDefaultItemTypeId = $aDefaultItemType['id'];
		}

		$aBlocks = Phpfox::getService('socialad.package')->getBlocksOfPackage($iPackageId);

		$this->template()->assign(array(
			'aSaItemTypes' => $aItemTypes,
			'aSaBlocks' => $aBlocks,
			'iDefaultAdTypeId' =>  $iDefaultAdTypeId,
			'iDefaultItemTypeId' =>  $iDefaultItemTypeId,
			'aSaAdTypes' => $aAdTypes,
			'iSaTitleLimitCharacter' => 25,
			'iSaTextLimitCharacter' => 90,
			'aSaPackage' => Phpfox::getService('socialad.package')->getPackageById($iPackageId),
			'aImageSizes' => Phpfox::getService('socialad.ad.image')->getImageSizes()
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

