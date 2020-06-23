<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailcover extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

        $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];
        $aBusiness = Phpfox::getService('directory')->retrieveMoreInfoFromBusiness($aBusiness,'');
		//get cover photos
        $aCoverPhotos = Phpfox::getService('directory')->getImages($aBusiness['business_id']);
		//rating

		//member
		$iFollower =  Phpfox::getService('directory')->getCountFollowerOfBusiness($aBusiness['business_id']);
		//followers
		$iMember =  Phpfox::getService('directory')->getCountMemberOfBusiness($aBusiness['business_id']);
		//reviews
		$iReview =  Phpfox::getService('directory')->getCountReviewOfBusiness($aBusiness['business_id']);

		$sCurrentTheme = Phpfox::getService('directory.helper')->getThemeFolder();
		
		$this->template()->assign(array(
				'aBusiness'	=> $aBusiness,
				'sHeader' => '',
				'iFollower' => $iFollower,
				'iMember' => $iMember,
				'iReview' => $iReview,
				'sCurrentTheme' => $sCurrentTheme,
				'aCoverPhotos' => $aCoverPhotos,
                'sCustomClassName' => 'ync-block'
 			)
		);
		return false;
	}

}

?>
