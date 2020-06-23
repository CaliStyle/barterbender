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


class Socialad_Component_Controller_Ad_Action extends Phpfox_Component 
{
	/**
	 * Class process metkod wnich is used to execute this component.
	 */
	public function process()
	{
		$sAction = $this->request()->get('actionname');

		$sMessage  = '';
		$sUrl = Phpfox::getLib('url')->makeUrl('socialad.ad');
		if($sAction == 'delete') {
			$iAdId = $this->request()->get('id');
			$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
			
			Phpfox::getService('socialad.ad.process')->deleteAd($iAdId);

			$sMessage = _p('delete_ad_title_successfully', array(
				'title' => $aAd['ad_title'],
			));

			$inadmin = $this->request()->get('inadmin');
			if($inadmin) {		
				//	notification for owner
				$iSenderUserId = 0;
				if((int)Phpfox::getUserId() > 0){
					$iSenderUserId = Phpfox::getUserId();
				} else {
					$iSenderUserId = (int)$aAd['ad_user_id'];
				}
				(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'socialad_ad_delete', $iItemId = $iAdId, $aAd['ad_user_id'], $iSenderUserId) : null);
				
				$sUrl = Phpfox::getLib('url')->makeUrl('admincp.socialad.ad');
			} else {
				$sUrl = Phpfox::getLib('url')->makeUrl('socialad.ad');	
			}

		} else if ($sAction == 'createsimilar') {
			$iAdId = $this->request()->get('id');
            $aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
            $aPackage = Phpfox::getService('socialad.package')->getPackageById($aAd['ad_package_id']);
            if($aPackage['package_is_deleted'])
            {
                Phpfox::getLib('url')->send('socialad.package.choose', array('createsimilar' => $iAdId));
            }
			$iNewAdId = Phpfox::getService('socialad.ad.process')->createSimilarAdFrom($iAdId);

			$sUrl = Phpfox::getLib('url')->makeUrl('socialad.ad.add', array('id' => $iNewAdId));
		} else if ($sAction == 'reedit') {
			$iNextStatus = Phpfox::getService('socialad.helper')->getConst('ad.status.draft');
			$iAdId = $this->request()->get('id');
			Phpfox::getService('socialad.ad.process')->updateStatus($iAdId, $iNextStatus);
			$sUrl = Phpfox::getLib('url')->makeUrl('socialad.ad.add', array('id' => $iAdId));
		}

		Phpfox::getLib('url')->send($sUrl, array(), $sMessage);

			
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

