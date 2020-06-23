<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detailmenu extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        $sView = $this->getParam('sView');
        $aAuction = $aYnAuctionDetail['aAuction'];
        $aModules = $aYnAuctionDetail['aModules'];
        $aModuleView = $aYnAuctionDetail['aModuleView'];
        $aPagesModule = $aYnAuctionDetail['aPagesModule'];
        
        if (!count($aModuleView) && !count($aPagesModule))
        {
            return false;
        }
		
		//get auth photo & video
		$photoEnable = true;
		$videoEnable = true;
		if (Phpfox::isModule('privacy'))
		{
			$photoEnable = Phpfox::getService('privacy')->check('auction', $aAuction['product_id'], $aAuction['user_id'], $aAuction['privacy_photo'], $aAuction['is_friend'], true);
			$videoEnable = Phpfox::getService('privacy')->check('auction', $aAuction['product_id'], $aAuction['user_id'], $aAuction['privacy_video'], $aAuction['is_friend'], true);
		}
		
        //hide tab menu with member role setting
        $oPermission = Phpfox::getService('auction.permission');
        foreach ($aModuleView as $key => $module)
        {
            switch ($module['module_name']) {
                case 'photos':
                    if (!$oPermission->canViewPhotoInAuction($aAuction['product_id']))
                    {
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                case 'videos':
                    if (!$oPermission->canViewVideoInAuction($aAuction['product_id']))
                    {
                        $aModuleView[$key]['is_show'] = false;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }


        // get number of items in business 
        $aNumberOfItem = array(
            'photos' => Phpfox::getService('ecommerce')->getNumberOfItemInEcommerce($aAuction['product_id'], 'photos'),
            'videos' => Phpfox::getService('ecommerce')->getNumberOfItemInEcommerce($aAuction['product_id'], 'videos')
        );
        
        $isLiked = Phpfox::getService('like')->didILike('auction', $aAuction['product_id']);
        
        $this->template()->assign(array(
            'aAuction' => $aAuction,
            'sView' => $sView,
            'aModuleView' => $aModuleView,
            'aPagesModule' => $aPagesModule,
            'aNumberOfItem' => $aNumberOfItem,
            'videoEnable' => $videoEnable,
            'photoEnable' => $photoEnable,
            'core_path' => Phpfox::getParam('core.path'),
            'isLiked' => $isLiked
                )
        );
        
        return 'block';
    }

}

?>
