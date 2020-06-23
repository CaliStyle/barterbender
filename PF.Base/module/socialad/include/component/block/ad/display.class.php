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
class Socialad_Component_Block_Ad_Display extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aAd = $this->getParam('aSaAd');
		if($bIsDisplayForUser = $this->getParam('bIsDisplayForUser')) {
		}

		if(!isset($aAd['ad_title']) || !$aAd['ad_title']) {
			$aAd['ad_title'] = _p('example_ad_title');
		}
		if(!isset($aAd['image_path']) || !$aAd['image_path']) {
			$aAd['ad_image'] = 'no image';
			$aAd['image_full_url'] =  Phpfox::getService('socialad.ad.image')->getNoImageUrlOfHtml();
		}
		if(!isset($aAd['ad_text']) || !$aAd['ad_text']) {
			$aAd['ad_text'] = _p('example_ad_text');
		}
		$this->template()->assign(array(
			'aSaDisplayAd' => $aAd,
			'bIsDisplayForUser' => $bIsDisplayForUser
		));	

		// return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

