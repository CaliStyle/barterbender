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


class Socialad_Component_Controller_Ad_Click extends Phpfox_Component 
{
	/**
	 * Class process metkod wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->request()->get('id');

		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);

		$sUrl = '';
		if($aAd['ad_item_type_name'] == 'external_url') {
			$sUrl = $aAd['ad_external_url'];
		} else {
			$sUrl = Phpfox::getService('socialad.ad.item')->getItemUrl( $aAd['ad_item_id'], $aAd['ad_item_type']);
		}

		Phpfox::getService('socialad.ad.process')->click($iAdId);
		Phpfox::getLib('url')->send($sUrl);

			
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

