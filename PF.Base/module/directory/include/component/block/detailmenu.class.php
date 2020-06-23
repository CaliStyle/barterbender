<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Detailmenu extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

	    $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
	    $sView = $this->getParam('sView');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];
        $aModuleView = $aYnDirectoryDetail['aModuleView'];
        $aPagesModule = $aYnDirectoryDetail['aPagesModule'];
		
        if(!count($aModuleView) && !count($aPagesModule)){
            return false;
        }

        // For advanced-blog
        if (isset($aModuleView['advanced-blog'])) $aModuleView['ynblog'] = $aModuleView['advanced-blog'];

        //hide tab menu with member role setting
        $oPermission = Phpfox::getService('directory.permission');
        foreach ($aModuleView as $key => $module) {

            //In phpFox 4.5. We not use prefix of module before phrase.
            $aModuleView[$key]['module_phrase'] = str_replace('directory.', '', $aModuleView[$key]['module_phrase']);
            switch ($module['module_name']) {
                case 'photos':
                    if(!$oPermission->canViewPhotoInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'musics':
                    if(!$oPermission->canViewMusicInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'blogs':
                    if(!$oPermission->canViewBlogInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'ynblog':
                    if(!$oPermission->canViewAdvBlogInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'polls':
                    if(!$oPermission->canViewPollsInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'coupons':
                    if(!$oPermission->canViewCouponInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'events':
                    if(!$oPermission->canViewEventInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'jobs':
                    if(!$oPermission->canViewJobInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'marketplace':
                    if(!$oPermission->canViewMarketplaceInBusiness($aBusiness['business_id'])){
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'videos':
                    $aModuleView[$key]['module_phrase'] = "{phrase var='video_channel'}";
                    break;
                case 'v':
                    $aModuleView[$key]['link'] = Phpfox::permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']) . 'v';
                    break;
                default:
                    # code...
                    break;
            }
        }
        
        // get number of items in business 
        $aNumberOfItem = array(
            'members' => Phpfox::getService('directory')->getCountMemberOfBusiness($aBusiness['business_id']),
            'followers' => Phpfox::getService('directory')->getCountFollowerOfBusiness($aBusiness['business_id']),
            'reviews' => Phpfox::getService('directory')->getCountReviewOfBusiness($aBusiness['business_id']),
            'photos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'photos'),
            'videos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'videos'),
            'musics' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'musics'),
            'blogs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'blogs'),
            'polls' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'polls'),
            'coupons' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'coupons'),
            'events' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'events'),
            'jobs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'jobs'),
            'marketplace' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'marketplace'),
            'ultimatevideo' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'ultimatevideo'),
            'ynblog' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'ynblog'),
            'v' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'v'),
        );
        if ($aBusiness['isDraft']) {
            return false;
        }
		$this->template()->assign(array(
				'aBusiness' => $aBusiness,
				'sView'	=> $sView,
				'aModuleView' => $aModuleView,
                'aPagesModule'  => $aPagesModule, 
				'aNumberOfItem'	=> $aNumberOfItem, 
                'core_path'     =>Phpfox::getParam('core.path'),
                'sHeader' => '',
                'sCustomClassName' => 'ync-block'
			)
		);
		return 'block';
	}

}

?>
