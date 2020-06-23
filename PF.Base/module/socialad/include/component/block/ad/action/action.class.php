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
class Socialad_Component_Block_Ad_Action_Action extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iItemId = $this->getParam('iSaItemId');
		$iItemTypeId = $this->getParam('iSaItemTypeId');

		if(!$iItemId || ! $iItemTypeId || $iItemTypeId == Phpfox::getService('socialad.helper')->getConst('ad.itemtype.external_url')) { 
			return false;
		}

		$aAction = Phpfox::getService('socialad.ad.item')->getActionData($iItemId, $iItemTypeId, Phpfox::getUserId());


		$this->template()->assign(array(
			'aSaData' => $aAction['data'],
			'sSaTemplate' => $aAction['template']
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

