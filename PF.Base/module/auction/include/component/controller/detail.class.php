<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Detail extends Phpfox_Component
{
	public function process()
	{
        Phpfox::getUserParam('auction.can_view_auction', true);
        
        // get callback for pages here
        $aCallback = $this->getParam('aCallback', false);
        
		$iViewerId = Phpfox::getUserId();
		$iProductId  = $this->request()->getInt('req3');
		if (!$iProductId) {
            $this->url()->send('auction');
        }

		$aAuction = Phpfox::getService('auction')->callback($aCallback)->getAuctionById($iProductId);

		if (empty($aAuction)) {
            $this->url()->send('auction', null, _p('auction_not_found'));
        }

        $aAuction['description'] = html_entity_decode($aAuction['description']);
        $aAuction['shipping'] = html_entity_decode($aAuction['shipping']);

    	Phpfox::getService('auction.process')->checkAndUpdateStatus($aAuction);

		if(!$aAuction || $aAuction['product_status'] == 'deleted')
		{
			$this->url()->send('auction', null, _p('auction_not_found'));
		}

		$sView = $this->request()->get('req5');
        if($sView == 'add-comment'){
            $sView = Phpfox::getService('auction')->changePageWhenAccessingAuctionDetail($sView);
        }
		
        $this->setParam('sView', $sView);
        $this->template()->assign(array(
            'sView' => $sView,
        ));

        if (Phpfox::isModule('privacy'))
		{
			Phpfox::getService('privacy')->check('auction', $aAuction['product_id'], $aAuction['user_id'], $aAuction['privacy'], $aAuction['is_friend']);
		}

        if (Phpfox::isModule('pages')) {
            if ($aAuction['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aAuction['item_id'],
                    'auction.view_browse_auction')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }
		
		if ($aAuction['user_id'] != $iViewerId)
        {
            Phpfox::getService('auction.process')->updateTotalView($aAuction['product_id']);
        }

        if ($aAuction['user_id'] != Phpfox::getUserId())
        {
            $sViewedAuctionId = Phpfox::getCookie('ynauction_viewed_auction_id');

            if (empty($sViewedAuctionId))
            {
                $sViewedAuctionId = $aAuction['product_id'];
            }
            else
            {
                $aViewedAuctionId = explode(',', $sViewedAuctionId);
                $aViewedAuctionId[] = $aAuction['product_id'];
                $aViewedAuctionId = array_unique(array_filter($aViewedAuctionId));
                $sViewedAuctionId = implode(',', $aViewedAuctionId);
            }
            
            Phpfox::setCookie('ynauction_viewed_auction_id', $sViewedAuctionId);
        }
        
        $bCanEdit = Phpfox::getService('auction.permission')->canEditAuction($aAuction['user_id'], $aAuction['product_id']);
        $bCanDelete = Phpfox::getService('auction.permission')->canDeleteAuction($aAuction['user_id']);
        
		$aAuction['canManageDashBoard'] = Phpfox::getService('auction.permission')->canManageAuctionDashBoard($aAuction['product_id']); 
        $aAuction['additioninfo'] = Phpfox::getService('auction')->getAuctionAdditionInfo($aAuction['product_id']);
        $aAuction['linkAuction'] = $this->url()->makeUrl('auction.detail', array($aAuction['product_id'], $aAuction['name'])); 
        $aAuction['bIsInWatchList'] = Phpfox::getService('auction.watch')->hasAddedInWatchList($aAuction['product_id']);
                
		// Draft/expire view permission
		switch ($aAuction['product_status']) {
			case 'draft':
				if ($aAuction['user_id'] != $iViewerId  && !Phpfox::isAdmin())
                {
                        $this->url()->send('subscribe');
                }
                break;
                
			case 'pending':
				if ($aAuction['user_id'] != $iViewerId && !Phpfox::isAdmin())
                {
                    $this->url()->send('subscribe');
                }
                break;
            case 'denied':
                if ($aAuction['user_id'] != $iViewerId && !Phpfox::isAdmin())
                {
                    $this->url()->send('subscribe');
                }
                break;

		}
        

		$bCanPostComment = true;
		if (isset($aAuction['privacy_comment']) && $aAuction['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('privacy.can_comment_on_all_items'))
		{
			switch ($aAuction['privacy_comment'])
			{
			    // Everyone is case 0. Skipped.
			    // Friends only
			    case 1:
			        if(!Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aAuction['user_id']))
			        {
			            $bCanPostComment = false;
			        }
			        break;
			    // Friend of friends
			    case 2:
			        if (!Phpfox::getService('friend')->isFriendOfFriend($aAuction['user_id']))
			        {
			            $bCanPostComment = false;    
			        }
			        break;
			    // Only me
			    case 3:
			        $bCanPostComment = false;
			        break;
			}
		}
				
		if (Phpfox::getUserId())
		{
			$bIsBlocked = Phpfox::getService('user.block')->isBlocked($aAuction['user_id'], Phpfox::getUserId());
			if ($bIsBlocked)
			{
				$bCanPostComment = false;
			}
		}
        $bCanPostComment = true;
		$sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

        $this->setParam('aFeedCallback', array(
                'module' => 'ecommerce',
                'table_prefix' => 'ecommerce_',
                'ajax_request' => 'ecommerce.addFeedComment',
                'item_id' => $aAuction['product_id'],
                'disable_share' => ($bCanPostComment ? false : true)
            )
        );

        $sFirstpage = 'overview';
        $this->template()->setTitle($aAuction['name']);

        if ($aAuction['module_id'] != 'auction') {
            if (Phpfox::isModule('pages')) {
                ($aCallback = Phpfox::callback('auction.getAuctionsDetails', array('item_id' => $aAuction['item_id'])));
                $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            }
            else {
                $aCallback = Phpfox::callback($aAuction['module_id'] . '.getAuctionsDetails', array('item_id' => $aAuction['item_id']));
            }
        }
        
		$auctionTitle = $aAuction['name'];
		if(strlen($auctionTitle) > 15)
		{
			$auctionTitle = substr($aAuction['name'], 0, 15).'...';
		}

        $sPhrase = (Phpfox::isPhrase($aAuction['category_title']) ? _p($aAuction['category_title']) : Phpfox::getLib('locale')->convert($aAuction['category_title']));

		$this->template()
                ->setBreadCrumb(_p('auction'), $aAuction['module_id'] == 'auction' ? $this->url()->makeUrl('auction') : $this->url()->permalink('pages', $aAuction['item_id'], 'auction') )
                ->setBreadCrumb($sPhrase, $this->url()->permalink('auction.category', $aAuction['category_id'], $sPhrase));

        $this->template()
                ->setMeta('description', $aAuction['name'] . '.')
                ->setMeta('keywords', $this->template()->getKeywords($aAuction['name']))
                ->setMeta('og:url', $sLink)
                ->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aAuction['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $aAuction['logo_path'],
                            'suffix' => '_200_square',
                            'return_url' => true
                )))
                ->setMeta('keywords', Phpfox::getParam('auction.auction_meta_keywords'))
                ->setMeta('description', Phpfox::getParam('auction.auction_meta_description'));

        $this->template()->setEditor(array(
                    'load' => 'simple'
                        )
                )
                ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'comment.css' => 'style_css',
                    'pager.css' => 'style_css',
                    'feed.js' => 'module_feed',
                    'jquery.rating.css' => 'style_css',
                    'jquery.plugin.min.js' => 'module_auction',
                    'jquery.countdown.min.js' => 'module_auction',
                    'jquery.flot.js' => 'module_auction',
                    'masterslide.min.js'=> 'module_auction',
            )
        );

        Phpfox::getService('auction.helper')->loadAuctionJsCss();

        $this->template()->setPhrase(array(
            'auction.manage_auction',
            'auction.actions',
            'auction.like',
            'auction.unlike',
            'auction.print',
            'auction.add_to_compare',
            'auction.remove_from_compare',
            )
        );

        $aModules = array();
        $aModuleView = array();
        $aPagesModule = array();
        
        $aModules = Phpfox::getService('auction')->getPageModuleForManage($aAuction['product_id']);
        
        foreach ($aModules[0] as $iModuleId => $sModule)
        {
            $aModuleView[$sModule] = array(
                'module_phrase' => _p('' . $sModule),
                'module_name' => $sModule,
                'is_show' => 1,
                'module_landing' => ($sModule == 'overview')
            );

            $sTitle = $aAuction['name'];
            if (!empty($sTitle))
            {
                if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1]))
                {
                    $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                    $sTitle = _p($sTitle);
                }

                $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
            }

            $aModuleView[$sModule]['link'] = $this->url()->makeUrl('auction.detail.' . $aAuction['product_id'] . '.' . $sTitle . '.' . $sModule);
            $aModuleView[$sModule]['active'] = false;
            
            if ($sView == '' && $aModuleView[$sModule]['module_landing'])
            {
                $aModuleView[$sModule]['active'] = true;
                $IsModuleActive = true;
            }
            elseif ($sView == $sModule)
            {
                $aModuleView[$sModule]['active'] = true;
                $IsModuleActive = true;
            }
        }
        
        if($sView == 'photos'){
            $this->template()->assign(array(
                        'iCountPhotos' => Phpfox::getService('ecommerce')->getNumberOfItemInEcommerce($aAuction['product_id'], 'photos'),
                    ));
        }
        else
        if($sView == 'videos'){
            $this->template()->assign(array(
                        'iCountVideos' => Phpfox::getService('ecommerce')->getNumberOfItemInEcommerce($aAuction['product_id'], 'videos'),
                    ));
        }
        $aYnAuctionDetail = array(
            'aAuction' => $aAuction,
            'bCanDelete' => $bCanDelete,
            'bCanEdit' => $bCanEdit,
            'firstpage' => $sFirstpage,
            'aModules' => $aModules,
            'aModuleView' => $aModuleView,
            'aPagesModule' => $aPagesModule,
            'sDetailUrl' => $this->url()->permalink('auction.detail', $aAuction['product_id'], $aAuction['name'])
        );
        $this->template()->assign(array(
            'aYnAuctionDetail' => $aYnAuctionDetail,
        ))
        ->setPhrase(array(
	        'auction.years',
	        'auction.months',
	        'auction.weeks',
	        'auction.days',
	        'auction.hours',
	        'auction.minutes',
	        'auction.seconds',
	        
	        'auction.year',
	        'auction.month',
	        'auction.week',
	        'auction.day',
	        'auction.hour',
	        'auction.minute',
	        'auction.second',
		)); // set phrase for countdown time
        $this->setParam('aYnAuctionDetail', $aYnAuctionDetail);
		Phpfox::getService('auction.helper')->buildMenu();
    }

}
?>