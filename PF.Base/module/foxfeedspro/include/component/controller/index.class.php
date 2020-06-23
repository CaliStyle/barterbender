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
 * @version        3.02
 * 
 */

class FoxFeedsPro_Component_Controller_Index extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	private function _setMetaAndKeywordsOfPage($aDataList,$IsSearched = true) {
        $this->template()->setMeta('keywords', Phpfox::getParam('foxfeedspro.foxfeedspro_meta_keywords'));
        $this->template()->setMeta('description', Phpfox::getParam('foxfeedspro.foxfeedsprofoxfeedspro_meta_description'));

        if($IsSearched){
            foreach ($aDataList as $iKey => $aData) {
	            		
	            		$this->template()->setMeta('keywords', $this->template()->getKeywords($aData['item_title']));
	        		
	        	}
	   
        }
        else{
	        foreach ($aDataList as $iKey => $aData) {
	        	if($aData['items'] > 0){
	        		foreach ($aData['items'] as $aItem) {
	            		$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['item_title']));
	        		}
	        	}
	        }
    	}
    }

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
		 
	public function process ()
	{
        $aParentModule = $this->getParam('aParentModule');
        $iItem = (isset($aParentModule['item_id']) ? $aParentModule['item_id'] : 0);
        $sModule = (isset($aParentModule['module_id']) ? $aParentModule['module_id'] : 'videochannel');
       	$bIsValidTag = false;
        if (defined('PHPFOX_IS_PAGES_VIEW'))
        {
        	$req4 = $this->request()->get('req4');	
			// check for Vanity URL pages
			$req3 = $this->request()->get('req3');	
			$sCheckParam = $req3;
			if('profileviewrss' == $req4){
				$sCheckParam = $req4;
			}

        	$go = $this->request()->get('go');	
			$aAssign = array('bNoTemplate' => true, 'sYnFfFrom' => 'pages');
			$this->setParam('sYnFfFrom', 'pages');		

			switch ($sCheckParam) {
				case 'profileviewrss':
					switch ($go) {
						case 'profileaddrssprovider':
							Phpfox::getComponent('foxfeedspro.addfeed', $aAssign, 'controller', true);
							break;
						
						case 'profilemanagerssprovider':
							Phpfox::getComponent('foxfeedspro.feeds', $aAssign, 'controller', true);
							break;

						default:
							Phpfox::getComponent('foxfeedspro.profileviewrss', $aAssign, 'controller', true);
							break;
					}
					
					break;

				default:
					$this->url()->send('', null, null);
					break;
			}
			
			return true;
        }

        // ------------------------------------------------------------

		// Check view allow
		Phpfox::getUserParam('foxfeedspro.can_view_news',TRUE);
		
		// Build filter section menu on left side
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE')) 
		{
			$aFilterMenu = array(
				_p('foxfeedspro.browse_all') => '',
				TRUE,
				_p('foxfeedspro.my_rss_providers') 	 => 'foxfeedspro.feeds',
				_p('foxfeedspro.my_news') 			 => 'foxfeedspro.news',
				_p('foxfeedspro.my_favorited_news') 	 => 'favorite',
			);
		}
		$this -> template() -> buildSectionMenu('foxfeedspro', $aFilterMenu);
		
		// Get view mode
		$sView = $this->request()->get('view');
		
		// Set action url for searching
		$sActionUrl = $this->url()->makeUrl('foxfeedspro');
		
		if($sView)
		{
			$sActionUrl = $this->url()->makeUrl('foxfeedspro',array('view' => $sView));
		}
		
		// Setup search fields
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
		$sSearchId = $this->request()->get('search');
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
		$iCatId = null;
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
		if($bIsSearched || $sView || $this->request()->get('req2') == 'tag')
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
			$this->search()->setCondition("AND ni.is_active = 1 AND ni.is_approved = 1");
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
			
			if ($aTag = Phpfox::getService('tag')->getTagInfo('foxfeedspro_news', $this->request()->get('req3')))
			{
					$this->setParam('sTagType', 'foxfeedspro_news');
					$this->template()->setBreadCrumb(_p('tag.topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);				
					$this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\'');
					$bIsValidTag = true;
					$sHeadline = "";
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
                if($aNews['is_download_image'] == 1 && !empty($aNews['item_image'])){
                    $aNewsItems[$k]['item_image'] = Phpfox::getParam('core.url_pic').$aNews['item_image'];
                }
                else if($aNews['is_download_image'] == 0 && !empty($aNews['item_image']) && preg_match("/foxfeedspro/i", $aNews['item_image'])){
                    $CorePath = Phpfox::getParam('core.url_pic').'foxfeedspro';
                    $aNewsItems[$k]['item_image'] = preg_replace('/(.*)foxfeedspro/i',$CorePath,$aNews['item_image']);
                }               
            }
			
			$this->_setMetaAndKeywordsOfPage($aNewsItems);

			// Setup pager
			Phpfox::getLib('pager')->set(
				array(
					'page'  => $this->search()->getPage(), 
					'size'  => $this->search()->getDisplay(), 
					'count' => $this->search()->browse()->getCount()
				)
			);
			
			
			$this->template()->assign(array(
				'aDataList' => Phpfox::getService('foxfeedspro')->updateNewsItems($aNewsItems)
			));	
		}
		else
		{
			// Generate Filter condition array
			$aConds = array();
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
			    //$aNewsConds = array("ni.feed_id = {$aFeed['feed_id']}");
				$aNewsConds = array("ni.feed_id = {$aFeed['feed_id']} AND ni.is_active = 1 AND ni.is_approved = 1");
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
			$this->_setMetaAndKeywordsOfPage($aDataList,false);
			
			$this->template()->assign(array(
				'aDataList' => $this->updateListNews($aDataList)
			));	
		}
		
		// Set header, breadcrumb  and variable
		$this->template()->setHeader(array(
            'front_end.js'	=> 'module_foxfeedspro',
            'owl.carousel.min.js' => 'module_foxfeedspro',
            'owl.carousel.css' => 'module_foxfeedspro'
		));


		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
        if ($iCatId !== null)
        {
            $aCategories = Phpfox::getService('foxfeedspro.category')->getParentBreadcrumb($iCatId);
            $iCnt = 0;
            foreach ($aCategories as $aCategory)
            {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);

                $this->template()->setBreadcrumb($aCategory[0], $aCategory[1], ($iCnt === count($aCategories) ? true : false));
            }
        }

		$this-> template()->assign(array(
			'bIsSearched' 	   	=> $bIsSearched,
			'sView'				=> $sView,
			'bIsValidTag'		=> $bIsValidTag,
			'bIsFriendlyUrl'   	=> Phpfox::getParam('foxfeedspro.is_friendly_url'),
			'sDefaultLogoLink' 	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/small.gif",
			'sDefaultImgLink'  	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
			'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
			'sHeadline'			=> $sHeadline,
			'iPage'				=> ($bIsSearched || $sView || $this->request()->get('req2') == 'tag') ? $this->search()->getPage() : $iPage,
		));

		

	}
	
	/*
	 * Clean method used to generate the top menu of the plugin according to the privacy settings in user group setting
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}


?>