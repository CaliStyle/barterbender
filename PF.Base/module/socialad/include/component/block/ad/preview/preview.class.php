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
class Socialad_Component_Block_Ad_Preview_Preview extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aAd = $this->getParam('aPreviewAd');
		$iAdTypeId = isset($aAd['ad_type']) ? $aAd['ad_type'] : $this->getParam('iAdTypeId');

		$sAdTypeName = Phpfox::getService('socialad.helper')->getNameById('ad.type', $iAdTypeId);

		$ynsa_ad_id = $this->getParam('ynsa_ad_id');
		if((int)$ynsa_ad_id > 0){
			$aAd = Phpfox::getService('socialad.ad')->getAdById((int)$ynsa_ad_id);
		}
		if($aAd) { // we are previewing a created ad
			if($sAdTypeName == 'html') {
				$this->setParam('aSaAd', $aAd);
			} else if($sAdTypeName == 'banner') {

			} else if($sAdTypeName == 'feed') {
				$aFeed = Phpfox::getService('socialad.ad')->mergeAdAndFeedData($aAd);
				$this->template()->assign(array( 
					'aFeed' => $aFeed
				));
			}

			$this->template()->assign(array( 
				'aPreviewAd' => $aAd
			));

		} else {
			$aFeed = Phpfox::getService('socialad.ad')->getTemplateDataForFeed();
			$this->template()->assign(array( 
				'aFeed' => $aFeed
			));
		}

		$this->template()->assign(array(
			'sAdTypeName' => $sAdTypeName,
		));

		return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

