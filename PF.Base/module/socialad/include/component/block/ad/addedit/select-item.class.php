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
class Socialad_Component_Block_Ad_Addedit_Select_Item extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPackageId = $this->getParam('iSaPackageId');
		$iItemTypeId = $this->getParam('iSaItemTypeId');
		$name = '';

		if(!$iItemTypeId) {
			return false;
		}

		if($iItemTypeId == Phpfox::getService('socialad.ad.item')->getTypeId('external_url')) {
			return false;
		}

		list($iCount, $aItems) = Phpfox::getService('socialad.ad.item')->getAllItems($iItemTypeId, $iUserId = Phpfox::getUserId(), $name);

		$this->template()->assign(array(
			'aSaItems' => $aItems,
			'aSaItemTypeId' => $iItemTypeId
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

