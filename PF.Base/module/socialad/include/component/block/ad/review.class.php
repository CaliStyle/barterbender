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
class Socialad_Component_Block_Ad_Review extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->getParam('iReviewAdId');

		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);

		$this->template()->assign(array(
			'aSaAd' => $aAd,
			'aSaPackage' => Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id'])
		));

		Phpfox::getService('socialad.helper')->loadSocialAdJsCss();

	}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

