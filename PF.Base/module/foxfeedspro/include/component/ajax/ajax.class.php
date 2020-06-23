<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02p5
 *
 */
 class FoxFeedsPro_Component_Ajax_Ajax extends Phpfox_Ajax
{
	/**
	 *  Get News Items Data
	 */
	public function getData()
	{
		// Get params
		$iFeedId   = (int) $this->get('iFeedId');
		$iIsAdminPanel = (int) $this->get('iIsAdminPanel');
		$sMode = $this->get('sMode');
		$sPage = $this->get('sPage');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		// Get Selected Feed and News Items Data
		if ($iFeedId)
		{
			$aFeed = $oFoxFeedsPro->getFeedById($iFeedId);
			if($aFeed)
			{
					try{
						$oFoxFeedsPro->getNews($aFeed,$sPage);
						$oFoxFeedsProProcess->updateTimeOfFeed($iFeedId);
						$oFoxFeedsPro->sendSubscribeNotification($aFeed);
					}
					catch(exception $e)
					{
						
					}
			}
		}
		
		// Return ajax
		if($iIsAdminPanel)
		{
			$this->html('#feed_getdata_'.$iFeedId, '<a href="javascript:void(0);" onclick="foxfeedspro.getData('.$iFeedId.','.$iIsAdminPanel.',\'normal\')" >'._p('foxfeedspro.get_data').'</a><script type="text/javascript">foxfeedsprostack.pop(); foxfeedspro.getDataReport(\''.$sMode.'\');</script>');
		}
		else
		{
			$this->html('#feed_getdata_'.$iFeedId, '<a href="javascript:void(0);" onclick="foxfeedspro.getData('.$iFeedId.')" >'._p('foxfeedspro.get_data').'</a>');
			$message = _p('foxfeedspro.get_data_of_feed_successfully', array('feed' => $aFeed['feed_name']));
			$this->alert($message,_p('user.notification'),300,150,false);
		}
	}
	
	/**
	 * Update Feed Status (Active|Inactive)
	 */
	public function updateFeedStatus()
	{
		// Get Params
		$iFeedId = (int) $this->get('feed_id');
		$iIsActive = (int) $this->get('is_active');
		$iIsActive = (int) !$iIsActive;
		$sMode = $this->get('mode');
	
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		if($iFeedId)
		{
			$oFoxFeedsProProcess->updateFeedStatus($iFeedId, $iIsActive);
		}
		
		if($iIsActive)
		{
			$sLabel = _p('foxfeedspro.active');
		}
		else
		{
			$sLabel = _p('foxfeedspro.inactive');
		}
		
		$this->html('#feed_update_status_' . $iFeedId, '<a href="javascript:void(0);" onclick="foxfeedspro.updateFeedStatus('.$iFeedId.','.$iIsActive.',\'normal\')" >'.$sLabel.'</a><script type="text/javascript">foxfeedsprostack.pop();foxfeedspro.getDataReport(\''.$sMode.'\');</script>');
	}
	
	/**
	 * Set featured|unfeatured a news
	 */
	public function updateFeatured()
	{
		// Get Params
		$iNewsId 	 = (int) $this->get('iNewsId');
		$iIsFeatured = (int) $this->get('iIsFeatured');
		$iIsFeatured = (int)!$iIsFeatured;
		
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		if ($iNewsId)
		{
				$oFoxFeedsProProcess->updateFeaturedNews($iNewsId, $iIsFeatured);
		}
		
		if($iIsFeatured)
		{
			$sLabel = _p('core.yes');
		}
		else
		{
			$sLabel = _p('core.no');
		}
		
		$this->html('#item_update_featured_' . $iNewsId, '<a href="javascript:void(0);" onclick="foxfeedspro.updateFeatured('.$iNewsId.','.$iIsFeatured.');">'.$sLabel.'</a>');
	}
	
	/*
	 *	Update news approval 
	 */
	public function updateApprovalNews()
	{
		// Get Params
		$iNewsId = (int) $this->get('iNewsId');
		$iIsApproved = (int) $this->get('iIsApproved');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		$sMessage = "";
		
		$aNews = $oFoxFeedsPro->getNewsById($iNewsId);
		
		if($aNews)
		{
			$oFoxFeedsProProcess->updateApprovalNews($iNewsId, $iIsApproved);
		}
		
		if($iIsApproved == 1)
		{
            $aRssFeed = $oFoxFeedsPro->getFeedById($aNews['feed_id']);
            $aFeed = Phpfox::getService('foxfeedspro')->getLatestFeed();
            if ($aFeed['type_id'] == "foxfeedspro") {
                $aItemNews = Phpfox::getService('foxfeedspro')->getNewsById($aFeed['item_id']);
            }
            if ($aFeed['type_id'] == "foxfeedspro" && $aFeed['user_id'] == $aNews['user_id'] && isset($aItemNews['feed_id']) && $aNews['feed_id'] == $aItemNews['feed_id']) {
                Phpfox::getLib('database')->insert(Phpfox::getT('ynnews_newfeeds'), array('feed_id' => $aFeed['feed_id'], 'item_id' => $iNewsId));
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('foxfeedspro', $iNewsId, 0, 0, 0, (int)$aNews['user_id']) : null);
                if ($aRssFeed['page_id'] != 0) {
                    Phpfox::getLib('database')->insert(Phpfox::getT('pages_feed'), array(
                            'type_id' => 'foxfeedspro',
                            'user_id' => Phpfox::getUserId(),
                            'parent_user_id' => $aRssFeed['page_id'],
                            'item_id' => $iNewsId,
                            'time_stamp' => PHPFOX_TIME,
                            'time_update' => PHPFOX_TIME,
                        )
                    );
                }
            }
			$sMessage = _p('foxfeedspro.the_news_had_been_approved');
			Phpfox::getService('notification.process')->add('foxfeedspro_newsapproved', $iNewsId, $aNews['user_id']);
		}
		else
		{
			$sMessage = _p('foxfeedspro.the_news_had_been_declined');
			Phpfox::getService('notification.process')->add('foxfeedspro_newsdeclined', $iNewsId, $aNews['user_id']);
		}
		
		$this->call("$('#foxfeedspro_item_".$iNewsId."').remove(); $('#core_js_messages').message('".$sMessage."', 'valid').fadeOut(5000);foxfeedsprostack.pop();foxfeedspro.getDataReport('normal');");
	}
	
	/**
	 * Update Feed approval
	 */
	public function updateApprovalFeeds()
	{
		// Get Params
		$iFeedId = (int) $this->get('iFeedId');
		$iIsApproved = (int) $this->get('iIsApproved');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		$sMessage = "";
		
		$aFeed = $oFoxFeedsPro->getFeedById($iFeedId,"quick");
		if($aFeed)
		{
			$oFoxFeedsProProcess->updateApprovalFeed($iFeedId, $iIsApproved);
		}
		
		if($iIsApproved == 1)
		{
			$sMessage = _p('foxfeedspro.the_rss_provider_had_been_approved');
			Phpfox::getService('notification.process')->add('foxfeedspro_feedapproved', $iFeedId, $aFeed['user_id']);
		}
		else
		{
			$sMessage = _p('foxfeedspro.the_rss_provider_had_been_declined');
			Phpfox::getService('notification.process')->add('foxfeedspro_feeddeclined', $iFeedId, $aFeed['user_id']);
		}
		
		$this->call("$('#foxfeedspro_item_".$iFeedId."').remove(); $('#core_js_messages').message('".$sMessage."', 'valid').fadeOut(5000);foxfeedsprostack.pop();foxfeedspro.getDataReport('normal');");
	}

	/**
	 * Update News Favorite List
	 */
	public function updateFavorite()
	{
		$iNewsId = (int) $this->get('id');
		$iStatus = (int) $this->get('status');
		$iUserId = (int) Phpfox::getUserId(); 
		
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		if($iStatus)
		{
            $oFoxFeedsPro->addFavortitedNews($iNewsId, $iUserId);
            $this->alert(_p('foxfeedspro.the_news_had_been_added_to_your_favorite_list'), _p('Notice'), 450, 150, true);
            $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.js_box_close .fa-close\').trigger(\'click\')},2000);');
        }
		else 
		{
			$oFoxFeedsPro->deleteFavortitedNews($iNewsId, $iUserId);
            $this->call("$('#news_{$iNewsId}').remove();$('#news_{$iNewsId}').remove();");
            $this->alert(_p('foxfeedspro.the_news_had_been_removed_from_your_favorite_list'), _p('Notice'), 450, 150, true);
            $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.js_box_close .fa-close\').trigger(\'click\')},2000);');


        }
	}
	
	/**
	 * Update Feeds Subscribe List
	 */
	public function updateSubscribe()
	{
		$iFeedId = (int) $this->get('id');
		$iStatus = (int) $this->get('status');
		$iUserId = (int) Phpfox::getUserId(); 
		
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		if($iStatus)
		{
			$oFoxFeedsPro->addSubscribedFeed($iFeedId, $iUserId);
            $this->alert(_p('foxfeedspro.you_had_subscribed_this_feed'), _p('Notice'), 450, 150, true);
            Phpfox::addMessage(_p('foxfeedspro.you_had_subscribed_this_feed'));
            $this->call('$Core.reloadPage();');
		}
		else 
		{
			$oFoxFeedsPro->deleteSubscribedFeed($iFeedId, $iUserId);
            $this->alert(_p('foxfeedspro.you_had_unsubscribed_this_feed'), _p('Notice'), 450, 150, true);
            Phpfox::addMessage(_p('foxfeedspro.you_had_unsubscribed_this_feed'));
            $this->call('$Core.reloadPage();');
		}
	}
	public function viewPopup()
	{
		Phpfox::getBlock('foxfeedspro.viewpopup', array(
                'id' => $this->get('id')
		));
	}

	public function popup()
	{
		$iItemId = $this->get('item_id');
		Phpfox::getBlock('foxfeedspro.mycategories', array('iItemId' => $iItemId));
	}

	public function addToCategories()
	{
		$aVals = $this->get('val');
		$aCategories = $aVals['category'];
        if(!isset($aCategories) || empty($aCategories[0])){
            return false;
        }
		$iItemId = $aVals['item_id'];
		if(phpfox::getService('foxfeedspro.process')->addToCategories($aCategories, $iItemId)){
			$this->alert(_p('foxfeedspro.add_provider_to_category_successfully'));
			$this->call("setTimeout(function(){location.reload();},1000);");
		}
	}

	public function deleteMyCatFeed()
	{
		$iFeedId = (int) $this -> get('feed_id');
		$iUserId = (int) Phpfox::getUserId();
		
		Phpfox::getLib('database')->delete(Phpfox::getT('ynnews_category_data'), 'feed_id = ' . $iFeedId. ' and user_id = '.$iUserId);
		$this->call("$('#my_cat_feed_" . $iFeedId . "').hide('slow');");
        Phpfox::addMessage(_p('foxfeedspro.category_successfully_deleted'));
        $this->call('$Core.reloadPage();');
	}

	public function moderation() {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'delete':
                foreach ((array )$this->get('item_moderate') as $iId) {
                    Phpfox::getService('foxfeedspro.process')->deleteFeed($iId);
					$this->call("$('#js_feed_entry" . $iId . "').prev('._moderator').hide();");
					$this->slideUp('#js_feed_entry' . $iId);
                }
                $sMessage = _p('foxfeedspro.selected_feeds_were_deleted_successfully');
                break;
        }
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');	
    }
	
	public function moderationnew() {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'delete':
                foreach ((array )$this->get('item_moderate') as $iId) {
                    Phpfox::getService('foxfeedspro.process')->deleteNews($iId);
					$this->call("$('#js_new_entry" . $iId . "').prev('._moderator').hide();");
					$this->slideUp('#js_new_entry' . $iId);
                }
                $sMessage = _p('foxfeedspro.selected_news_items_were_deleted_successfully');
                break;
        }
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');	
    }
	 public function deleteMyCategory()
	 {
		 Phpfox::isUser(true);
		 $iCategory = $this->get('category_id');
		 if (Phpfox::getService('foxfeedspro.process')->deleteMyCategory($iCategory))
		 {
             Phpfox::addMessage(_p('foxfeedspro.category_successfully_deleted'));
             $this->call('$Core.reloadPage();');
		 }
	 }
	 public function categoryOrdering()
	 {
		 Phpfox::isAdmin(true);
		 $aVals = $this->get('val');
		 Phpfox::getService('core.process')->updateOrdering(array(
										  'table' => 'ynnews_categories',
										  'key' => 'category_id',
										  'values' => $aVals['ordering']
														  )
		 );

		 Phpfox::getLib('cache')->remove('foxfeedspro', 'substr');
	 }

	 public function updateActivity()
	 {
		 if (Phpfox::getService('foxfeedspro.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub')))
		 {

		 }
	 }
}