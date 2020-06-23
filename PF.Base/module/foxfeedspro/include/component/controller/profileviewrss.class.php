<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class FoxFeedsPro_Component_Controller_Profileviewrss extends Phpfox_Component
{
	private function updateListNews($aDataList){
		foreach($aDataList as $key=>$aData)
		{
			foreach($aData['items'] as $ikey => $item){
				$content = $item['item_description_parse'];
				$length = 190;
				while($length < strlen($content)){
					if($content[$length] == ' ')
						break;
					else {
						$length ++;
					}
				}
				$item['item_description_parse'] = substr($content,0,$length);
				if($length<strlen($content))
				{
					$item['item_description_parse'].="...";
				}
				$aDataList[$key]['items'][$ikey] = $item;
			}
		}
		return $aDataList;
	 }

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		// check for processing from profile/pages
		$sYnFfFrom = $this->getParam('sYnFfFrom');

		if('profile' == $sYnFfFrom || 'pages' == $sYnFfFrom){
			$this->template() ->assign(array(
				'sYnFfFrom' => $sYnFfFrom, 
			));
					
			$bAddRssProvider = true;
			$bManageRssProvider = true;			
			$sAddRssProviderReplace = '';
			$sManageRssProviderReplace = '';			
			if('pages' == $sYnFfFrom){
				$sAddRssProviderReplace = 'profileviewrss/go_profileaddrssprovider';
				$sManageRssProviderReplace = 'profileviewrss/go_profilemanagerssprovider';
				$aParentModule = $this->getParam('aParentModule');
				$this->template() ->assign(array(
					'aParentModule' => $aParentModule, 
				));
				$aPage = Phpfox::getService('pages')->getPage((int)$aParentModule['item_id']);
				if(isset($aPage['page_id'])){
			        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'foxfeedspro.can_view_news'))
			        {
						$bAddRssProvider = false;
						$bManageRssProvider = false;								
			        }
					
					if($aPage['user_id'] == Phpfox::getUserId()
						&& (!Phpfox::getUserParam('foxfeedspro.can_add_rss_provider'))
					){
						$bAddRssProvider = false;
						$bManageRssProvider = false;								
					}
					
		        	if (Phpfox::getUserId() == $aPage['user_id'] 
		        		&& Phpfox::getUserParam('foxfeedspro.can_add_rss_provider')
		    		)
		        	{
						// do nothing
		        	} else {
		        		$bAddRssProvider = false;
		        	}														
				} else {
					$bAddRssProvider = false;
					$bManageRssProvider = false;								
				}
			} else {
				$sAddRssProviderReplace = 'profileaddrssprovider';
				$sManageRssProviderReplace = 'profilemanagerssprovider';			
				$aUser = $this->getParam('aUser');
				if($aUser['user_id'] == Phpfox::getUserId()
					&& (!Phpfox::getUserParam('foxfeedspro.can_add_rss_provider'))
				){
					$bAddRssProvider = false;
					$bManageRssProvider = false;								
				}
				if ($aUser['user_id'] == Phpfox::getUserId())
				{
					if(Phpfox::getUserParam('foxfeedspro.can_add_rss_provider_in_profile')
					){
						// do nothing
					} else {
						$bAddRssProvider = false;
						$bManageRssProvider = false;		
					}
				} else {
					$bAddRssProvider = false;
				}				
			}

            if ($bAddRssProvider && $sYnFfFrom == 'pages') {
			    $url = 'pages/' . $aPage['page_id'] . '/foxfeedspro/profileviewrss/go_profileaddrssprovider/profilemanagerssprovider';
                sectionMenu(_p('foxfeedspro.menu_foxfeedspro_add_new_rss_provider_e27fb9aa8592526fe0c5739b2030539d'), $url);
            }
            if ($bManageRssProvider && $sYnFfFrom == 'pages') {
			    $url = 'pages/' . $aPage['page_id'] . '/foxfeedspro/profileviewrss/go_profilemanagerssprovider/profilemanagerssprovider';
                sectionMenu(_p('foxfeedspro.manage_rss_provider'), $url);
            }
			
			$this->template() ->assign(array(
				'bAddRssProvider' => $bAddRssProvider, 
				'bManageRssProvider' => $bManageRssProvider, 
				'sAddRssProviderReplace' => $sAddRssProviderReplace, 
				'sManageRssProviderReplace' => $sManageRssProviderReplace, 
			));			
		}

		// Check view allow
		Phpfox::getUserParam('foxfeedspro.can_view_news',TRUE);

		// Get view mode
		$sView = $this->request()->get('view');

		// Set action url for searching
		$sActionUrl = $this->url()->makeUrl('foxfeedspro');

		if($sView)
		{
			$sActionUrl = $this->url()->makeUrl('foxfeedspro',array('view' => $sView));
		}

		$this->search()->set(
			array(
				'type' 			=> 'foxfeedspro',
				'field'			=> 'ni.item_id',
				'search' 		=> 'search',
				'search_tool'	=> array(
					'table_alias'   => 'ni',
					'search'		=> array(
						'action' 	   => $sActionUrl,
						'default_value'=> _p('foxfeedspro.search_news'),
						'name'		   => 'search',
						'field'		   => 'ni.item_title'
					),
					'sort'			=> array(
						'latest' 		 => array('ni.item_pubDate', _p('foxfeedspro.latest')),
						'most-viewed' 	 => array('ni.total_view', _p('foxfeedspro.most_viewed')),
						'most-comment' 	 => array('ni.total_comment', _p('foxfeedspro.most_discussed')),
						'most-favorited' => array('ni.total_favorite', _p('foxfeedspro.most_favorited')),
					),
					'show' 			=> array(10, 15, 20, 25),
					'when_field' 	=> 'item_pubDate'
				)
			)
		);

		// Set foxfeedspro service
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oNewsCategory = Phpfox::getService('foxfeedspro.category');

		// IsSearched mode
		$bIsSearched = FALSE;
		$sSearchId = $this->request()->get('search-id');
		$sSort = $this->request()->get('sort');
		$sShow = $this->request()->get('show');
		$sWhen = $this->request()->get('when');

		if($sSearchId || $sSort || $sShow || $sWhen)
		{
			$bIsSearched = TRUE;
		}
		// Generate headline
		$sHeadline = "";
		if(!$sSearchId && !$sWhen)
		{
			switch($sSort)
			{
				case 'most-viewed':
					$sHeadline = _p('foxfeedspro.most_viewed_news');
					break;
				case 'most-comment':
					$sHeadline = _p('foxfeedspro.most_commented_news');
					break;
				case 'most-favorited':
					$sHeadline = _p('foxfeedspro.most_favorited_news');
					break;
				default:
					if(!$sView)
					{
						$sHeadline = _p('foxfeedspro.latest_news');
					}
					break;
			}
		}
		else
		{
			$sHeadline = _p('foxfeedspro.search_result');
		}
		// Get Category Filter Conditions
		$sRelatedFeedIds = '';
		if($this->request()->get('req2') == 'category')
		{
			$iCatId = (int) $this->request()->get('req3');
			if($iCatId > 0)
			{
				$sRelatedFeedIds = $oNewsCategory->getRelatedFeedIdIntoString($iCatId);
			}
			
			if($sView)
			{
				$this->search()->setFormUrl($this->url()->makeUrl('foxfeedspro.category',array($iCatId, 'view' => $sView)));
			}
			else 
			{	
				$this->search()->setFormUrl($this->url()->makeUrl('foxfeedspro.category',array($iCatId)));
			}
			
		}

		// Generate data according to the search mode
		if($bIsSearched || $sView)
		{
			// Filter view mode
			switch ($sView)
			{
				case 'favorite':
					Phpfox::isUser(true);
					break;
				default:
					break;
			}
			
			$this->search()->setCondition("AND nf.is_active = 1 AND nf.is_approved = 1 AND nf.total_item > 0");
			// Get user current language id
			$sLanguageId = $oFoxFeedsPro->getUserUsingLanguage();
			if($sLanguageId != "any")
			{
				$this->search()->setCondition("AND (nf.feed_language = 'any' OR nf.feed_language = '{$sLanguageId}')");
			}
			else 
			{
				$this->search()->setCondition("AND nf.feed_language = '{$sLanguageId}'");
			}
			
			if($sRelatedFeedIds != '')
			{
				$this->search()->setCondition($aConds[] = "AND nf.feed_id IN ({$sRelatedFeedIds})");
			}
			
			// Setup search params
			$aBrowseParams = array(
				'module_id' => 'foxfeedspro',
				'alias' => 'ni',
				'field' => 'item_id',
				'table' => Phpfox::getT('ynnews_items')
				//'hide_view' => array('my')
			);
			$this->search()->browse()->params($aBrowseParams)->execute();
			
			$aNewsItems = $this->search()->browse()->getRows();
            foreach ($aNewsItems as $k => $aNews)
            {
                if (isset($aNews['item_image']) && Phpfox::getParam('core.allow_cdn') && $aNews['item_server_id'] > 0)
                {
                    $aNewsItems[$k]['item_image'] = Phpfox::getLib('cdn')->getUrl($aNews['item_image'], $aNews['item_server_id']);
                }
            }
			
			// Setup pager
			Phpfox::getLib('pager')->set(
				array(
					'page'  => $this->search()->getPage(), 
					'size'  => $this->search()->getDisplay(), 
					'count' => $this->search()->browse()->getCount()
				)
			);
			
			//print_r($aNewsItems);die;
			$iPage = $this->search()->getPage();
			$this->template()->assign(array(
				'aDataList' => Phpfox::getService('foxfeedspro')->updateNewsItems($aNewsItems),
			));	
		}
		else
		{
			// Generate Filter condition array
			$aConds = array();

			if('profile' == $sYnFfFrom){
				// for profile page
				$aUser = $this->getParam('aUser');	
				$aConds[] = " AND nf.user_id = " . (int)$aUser['user_id'];
			} else if('pages' == $sYnFfFrom){
				// for pages
				$aConds[] = " AND nf.page_id = " . (int)$aParentModule['item_id'];
			}

			$aConds[] = "AND nf.is_active = 1 AND nf.is_approved = 1 AND nf.total_item > 0";
			// Get user current language id
			$sLanguageId = $oFoxFeedsPro->getUserUsingLanguage();
			if($sLanguageId != "any")
			{
				$aConds[] = "AND (nf.feed_language = 'any' OR nf.feed_language = '{$sLanguageId}')";
			}
			else 
			{
				$aConds[] = "AND nf.feed_language = '{$sLanguageId}'";
			}
			
			if($sRelatedFeedIds != '')
			{
				$aConds[] = "AND nf.feed_id IN ({$sRelatedFeedIds})";
			}
			
			// Number feed per page
			$iFeedPerPage = Phpfox::getParam('foxfeedspro.number_feed_display');
			
			// Get current page
			$iPage = $this->request()->get('page');
			
			// Total items count
			$iCount = $oFoxFeedsPro->getItemCount('ynnews_feeds','nf', $aConds);
			// Order 
			$sOrder = "nf.order_display ASC, nf.feed_id DESC";
			
			$aFeeds = $oFoxFeedsPro->getFeeds($aConds, $iPage, $iFeedPerPage, $iCount, $sOrder, "browse");
			
			$aDataList = array();
			
			foreach($aFeeds as $aFeed)
			{
				$aNewsConds = array("ni.feed_id = {$aFeed['feed_id']} AND ni.is_active = 1");
				// $aNewsConds = array("ni.feed_id = {$aFeed['feed_id']}");
				$aRelatedNews = $oFoxFeedsPro->getNewsItems($aNewsConds, $aFeed['feed_item_display'], null, null, "ni.item_pubDate DESC, ni.item_id DESC");
				
				if(!empty($aRelatedNews))
				{
					$aDataList[] = array('feed' => $aFeed, 'items' => $aRelatedNews);
				}
			}
			
			// Setup pager
			Phpfox::getLib('pager')->set(
				array(
					'page'  => $iPage, 
					'size'  => $iFeedPerPage, 
					'count' => $iCount
				)
			);
			
			$aDataList = $this->updateListNews($aDataList);
			$this->template()->assign(array(
				'aDataList' => $aDataList, 
			));	
		}

		// Set header, breadcrumb  and variable
		$this->template()->setHeader(array(
            'front_end.js'	=> 'module_foxfeedspro',
		));


		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		$this-> template()->assign(array(
			'bIsSearched' 	   	=> $bIsSearched,
			'sView'				=> $sView,
			'bIsFriendlyUrl'   	=> Phpfox::getParam('foxfeedspro.is_friendly_url'),
			'sDefaultLogoLink' 	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/small.gif",
			'sDefaultImgLink'  	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
			'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
			'sHeadline'			=> $sHeadline,
			'iPage' 	 		=> $iPage,
		));
	}
}

?>