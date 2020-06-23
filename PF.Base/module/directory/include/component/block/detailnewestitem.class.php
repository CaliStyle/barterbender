<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailnewestitem extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

	    $iBusinessId = $this->request()->get('req3');
	    $sBusinessTitle = $this->request()->get('req4');
	    $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
        $aBusiness = $aYnDirectoryDetail['aBusiness'];
	    $sView = $this->getParam('sView');
        $aModuleView = $aYnDirectoryDetail['aModuleView'];

        $bShow = false;
        if (isset($aModuleView['advanced-blog'])) {
            if ($aModuleView['advanced-blog']['is_show'] == 1) {
                $bShow = true;
            }
        }

        // get newest items in business 
        $aItem = array(
            'photos' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'photos'),
            'videos' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'videos'),
            'v' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'v'),
            'ultimatevideo' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'ultimatevideo'),
            'musics' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'musics'),
            'blogs' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'blogs'),
            'ynblog' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'ynblog'),
            'polls' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'polls'),
            'coupons' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'coupons'),
            'events' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'events'), 
            'jobs' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'jobs'),
            'marketplace' => Phpfox::getService('directory')->getNewestItemInBusiness($aBusiness['business_id'], 'marketplace'),
        );

        $aNumberOfItem = array(
            'photos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'photos'),
            'videos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'videos'),
            'v' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'v'),
            'ultimatevideo' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'ultimatevideo'),
            'musics' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'musics'),
            'blogs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'blogs'),
            'ynblog' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'ynblog'),
            'polls' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'polls'),
            'coupons' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'coupons'),
            'events' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'events'),
            'jobs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'jobs'),
            'marketplace' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'marketplace'),
        );

        if (!empty($aItem['videos'])) {
            foreach ($aItem['videos'] as $iKey => $aRow) {
                $aItem['videos'][$iKey]['link'] = Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']);
            }
        }

        if (empty(array_filter($aItem))) {
            return false;
        }

        $iAdvEvent = Phpfox::getService('directory.helper')->isAdvEvent();
        $iAdvancedMarketplace = Phpfox::getService('directory.helper')->isAdvMarketplace();

		$this->template()->assign(array(
                'aItemphoto' => $aItem['photos'],
                'aItemBlog' => $aItem['blogs'],
                'aItemAdvBlog' => $aItem['ynblog'],
                'bShow' => $bShow,
                'aItemEvent' => $aItem['events'],
                'aItemMarketplace' => $aItem['marketplace'],
                'aItemMusic' => $aItem['musics'],
                'aItemPoll' => $aItem['polls'],
                'aItemVideoChannel' => $aItem['videos'],
                'aItemVideo' => $aItem['v'],
                'aItemUltimateVideo' => $aItem['ultimatevideo'],
                'aItemCoupon' => $aItem['coupons'],
                'aItemJobs' => $aItem['jobs'],
                'aItem' => $aItem,
                'aNumberOfItem' => $aNumberOfItem,
                'sMusicPath' => Phpfox::getParam('core.path_actual').'PF.Site/Apps/core-music/assets/image/nophoto_song.png',
                'sView' => $sView,
                'iBusinessId' => $iBusinessId,
                'sBusinessTitle' => $sBusinessTitle,
                'aModuleView' => $aModuleView,
                'iAdvEvent' => $iAdvEvent,
                'iAdvancedMarketplace' => $iAdvancedMarketplace,
                'coreUrlModule' => Phpfox::getParam('core.path_file') . 'module/',
                'sCustomClassName' => 'ync-block'
			)
		);
        $this->template()->clean(['sHeader']);
		return 'block';
	}

}

?>
