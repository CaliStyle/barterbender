<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Newsfeed
 * @version        3.02p5
 * 
 */

class FoxFeedsPro_Service_Process extends Phpfox_Service
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_feeds');
	}
	
	public function addToCategories($aVals, $iItemId, $bIsUser = true)
	{
		if(!empty($aVals))
		{
			$iUserId = 0;
			if($bIsUser)
			{
				$iUserId = phpfox::getUserId();
			}
			$this->database()->delete(Phpfox::getT('ynnews_category_data'), 'feed_id = ' . $iItemId. ' and user_id = '.$iUserId);
			foreach($aVals as $iKey => $iCategoryId)
			{
				if(isset($iCategoryId) && $iCategoryId)
				{
                    $this->database()->delete(Phpfox::getT('ynnews_category_data'), 'feed_id = ' . $iItemId. ' and user_id = '.$iUserId);
				    Phpfox::getLib('phpfox.database') ->insert(Phpfox::getT('ynnews_category_data'), array('feed_id' => $iItemId, 'category_id' => $iCategoryId, 'user_id'=>($bIsUser)?phpfox::getUserId():0));
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * Add feed information into the system
	 * @param array $aVals is the input data
	 * @return int $iId is the id of the added feed
	 */
	public function addFeed($aVals = array())
	{
		// Get feed logo process
		$sFeedLogo = ""; 
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		// Generate logo url from uploaded file
		if(isset($aVals['logo_file']) && !empty($aVals['logo_file']['name']))
		{
			$sFeedLogo = 'foxfeedspro/'.$oFoxFeedsPro-> uploadLogo('logo_file');
			if(!$sFeedLogo)
			{
				return FALSE;
			}
		}
		// Get logo url from input form
		elseif ($aVals['feed_logo'] != "") 
		{
			$sFeedLogo = $oFoxFeedsPro -> downloadLogo($aVals['feed_logo']);
		}

		//$sFeedFavIcon = $oFoxFeedsPro -> downloadFavIcon($aVals['favicon']);
		// Update the way to download favicon later
		$sFeedFavIcon = $aVals['favicon'];
		
		$oParseInput = Phpfox::getLib('parse.input');
		
		$iAutoApproved = (int) Phpfox::getUserParam('foxfeedspro.auto_approve_posted_rss_providers');
		
		$aInsert = array(
			'feed_name' 		=> $oParseInput->clean($aVals['feed_name']),
			'category_id' 		=> 0,
			'feed_url'			=> trim($aVals['feed_url']),
			'time_stamp'		=> PHPFOX_TIME,
			'time_update'		=> PHPFOX_TIME,
			'feed_logo' 		=> $sFeedLogo,
			'logo_mini_logo'	=> $sFeedFavIcon,
			'rssparse'			=> $aVals['rssparse'],
			'lengthcontent'		=> $aVals['lengthcontent'],
			'feed_item_import'  => $aVals['feed_item_import'],
			'feed_language' 	=> $aVals['feed_language'],
			'feed_alias'		=> $oParseInput->cleanTitle($aVals['feed_name']),
			'is_approved'		=> (Phpfox::isAdminPanel() ? 1 : $iAutoApproved),
            'server_id'         => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
		);

		if(isset($aVals['sYnFfFrom']) && 
			('profile' == $aVals['sYnFfFrom'] || 'pages' == $aVals['sYnFfFrom'])
		)
		{
			if('profile' == $aVals['sYnFfFrom']){
				$aInsert['user_id'] = Phpfox::getUserId();
			} else {
				$aInsert['user_id'] = 0;
				$aInsert['page_id'] = (int)$aVals['pageID'];
			}
		} else {
			$aInsert['user_id'] = Phpfox::getUserId();
		}

		if(isset($aVals['time_delete_news']) && (int)$aVals['time_delete_news'] > 0)
		{
			$aInsert['time_delete_news_stamp'] = PHPFOX_TIME +  ((int)$aVals['time_delete_news'] * 24 * 60 * 60);
			$aInsert['time_delete_news'] = (int)$aVals['time_delete_news'];
		}

		// Get Category selected list
		$aCategories = array_filter($aVals['category']);
		
		// Get the category id to add to the feed
		$iCategoryId = (int) array_pop($aCategories);
		
		if($iCategoryId)
		{
			// Push CatId back to the array
			$aCategories[] = $iCategoryId;
			$aInsert['category_id'] = $iCategoryId;
		}
		
		// Process Add RSS Provider
		$iId = $this -> database() -> insert($this->_sTable, $aInsert);

		// Remove cache total feed
        if ($aInsert['is_approved']) {
            storage()->del('foxfeedspro/feed/total');
        }

        $aItemImage = $this->database()->select('feed_logo, server_id')
            ->from($this->_sTable)
            ->where('feed_id = '.$iId)
            ->execute('getSlaveRow');


        $aItemImage['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aItemImage['feed_logo'] = 'foxfeedspro/'. sprintf($aFile['path'], '');
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update($this->_sTable,array('feed_logo'=> $aItemImage['feed_logo'], 'server_id' => $aItemImage['server_id']),'feed_id='.$iId);
            }
        }

		
		if($aCategories)
		{
			$oNewsCatProcess = Phpfox::getService('foxfeedspro.category.process');
			foreach($aCategories as $iCatId)
			{
				$oNewsCatProcess->addCategoryData($iId, $iCatId);
			}
		}
		return $iId;
	}

	/**
	 * Update feed information into the system
	 * @param array $aVals is the input data
	 * @return int $iId is the id of the updated feed
	 */
	public function editFeed($aVals = array())
	{
		// Generate variables	
		$aLogoUrl = array();
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		// Get related RSS Provider
		$aFeed = $oFoxFeedsPro->getFeedById( (int) $aVals['feed_id']); 
		if(!$aFeed) 
		{
			return FALSE;
		}
		$sFeedLogo = $aFeed['feed_logo']; 
		$sFeedFavIcon = $aFeed['logo_mini_logo'];
		
		$aVals['feed_logo'] = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/","", $aVals['feed_logo']);
		
		// Get feed logo process
		
		// Generate logo url from uploaded file
		if(isset($aVals['logo_file']) && !empty($aVals['logo_file']['name']))
		{
			$sFeedLogo = 'foxfeedspro/'.$oFoxFeedsPro -> uploadLogo('logo_file');
			if(!$sFeedLogo)
			{
				return FALSE;
			}
		}
		elseif ($aVals['feed_logo'] != "") 
		{
			$sFeedLogo = $oFoxFeedsPro-> downloadLogo($aVals['feed_logo']);
		}
			// Get logo from parsed feed if the feed url is changed
		elseif($aVals['feed_logo'] == "")
		{
			$sFeedLogo	= $oFoxFeedsPro -> downloadLogo($aVals['logo']);
		}
			// Get new FavIcon if the feed url is changed
		if($aFeed['feed_url'] != $aVals['feed_url'])
		{
			//$sFeedFavIcon =$oFoxFeedsPro -> downloadFavIcon($aVals['favicon']);
			// Update the way to download favicon later
			$sFeedFavIcon = $aVals['favicon'];
		}

		if(isset($aVals['logo_mini_logo']) && !empty($aVals['logo_mini_logo']['name']))
		{
			$sFeedFavIcon = $oFoxFeedsPro -> uploadFavicon('logo_mini_logo');
			if(!$sFeedFavIcon)
			{
				return FALSE;
			}
		}


		// Process Update
		$oParseInput = Phpfox::getLib('parse.input');
		
		$aUpdate = array(
			'feed_name' 			=> $oParseInput->clean($aVals['feed_name']),
			'category_id' 			=> 0,
			'feed_url'				=> $aVals['feed_url'],
			'time_update'			=> PHPFOX_TIME,
			'feed_logo' 			=> $sFeedLogo,
			'logo_mini_logo'		=> $sFeedFavIcon,
			'is_active_logo'		=> $aVals['is_active_logo'],
			'is_active_mini_logo' 	=> $aVals['is_active_mini_logo'],
			'order_display'			=> $aVals['order_display'],
			'is_active'				=> $aVals['is_active'],
			'rssparse'				=> $aVals['rssparse'],
			'lengthcontent'			=> $aVals['lengthcontent'],
			'feed_item_display'		=> $aVals['feed_item_display'],
			'feed_item_display_full'=> $aVals['feed_item_display_full'],
			'feed_item_import'  	=> $aVals['feed_item_import'],
			'feed_language' 		=> $aVals['feed_language'],
			'feed_alias'			=> $oParseInput->cleanTitle($aVals['feed_name']),
            'server_id'         => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
		);

		if(isset($aVals['time_delete_news']) && (int)$aVals['time_delete_news'] > 0)
		{
			$aUpdate['time_delete_news_stamp'] = PHPFOX_TIME +  ((int)$aVals['time_delete_news'] * 24 * 60 * 60);
			$aUpdate['time_delete_news'] = (int)$aVals['time_delete_news'];
		}		
		
		// Remove old logo and mini logo image
		if($aFeed['feed_logo'] != $sFeedLogo)
		{
			@unlink(Phpfox::getParam('core.dir_pic')."foxfeedspro/".$aFeed['feed_logo']);
		}
		
		if($aFeed['logo_mini_logo'] != $sFeedFavIcon)
		{
			//@unlink(Phpfox::getParam('core.dir_pic')."foxfeedspro/".$aFeed['logo_mini_logo']);
			//Update when download favicon and store on site
		}

//        $aItemImage = $this->database()->select('feed_logo')
//            ->from($this->_sTable)
//            ->where('feed_id = '.$aVals['feed_id'])
//            ->execute('getSlaveRow');
//
//        if (!empty($aVals['temp_file'])) {
//            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
//            if (!empty($aFile)) {
//                $aItemImage['feed_logo'] = "foxfeedspro/" . sprintf($aFile['path'], '');
//                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
//                $this->database()->update($this->_sTable,array('feed_logo'=> $aItemImage['feed_logo']),'feed_id='.(int)$aVals['feed_id']);
//            }
//        }

        $aItemImage = $this->database()->select('feed_logo, server_id')
            ->from($this->_sTable)
            ->where('feed_id = '.$aVals['feed_id'])
            ->execute('getSlaveRow');


        $aItemImage['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aItemImage['feed_logo'] = sprintf($aFile['path'], '');
                $aUpdate['feed_logo'] = 'foxfeedspro/'.$aItemImage['feed_logo'];
                $aUpdate['server_id'] = $aItemImage['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update(Phpfox::getT('ynnews_feeds'),array('feed_logo'=> $aItemImage['feed_logo'], 'server_id' => $aItemImage['server_id']),'feed_id='.$aVals['feed_id']);
            }
        }

		//Process Active/Inactive Related News on Feed when Active mode is changed
		if($aFeed['is_active'] != $aVals['is_active'])
		{
			// Update the related news status
			 $this->database()->update(Phpfox::getT('ynnews_items'),array('is_active' => $aVals['is_active']), "feed_id = {$aFeed['feed_id']}");
		}
		
		// Update Category List
		// Get Category selected list
		$aCategories = array_filter($aVals['category']);
		
		// Get the category id to add to the feed
		$iCategoryId = (int) array_pop($aCategories);
		
		if($iCategoryId)
		{
			// Push CatId back to the array
			$aCategories[] = $iCategoryId;
			$aUpdate['category_id'] = $iCategoryId;
		}
		
        $oNewsCatProcess = Phpfox::getService('foxfeedspro.category.process');
        $oNewsCat = Phpfox::getService('foxfeedspro.category');
        $aFeedCats = $oNewsCat->getFeedCategoryList($aFeed['feed_id']);
        // Delete the old category list
        foreach($aFeedCats as $iOldId)
        {
            $oNewsCatProcess->deleteCategoryData($aFeed['feed_id'],$iOldId);
        }
		if($aCategories)
		{
			// Add new category list
			foreach($aCategories as $iNewId)
			{
				$oNewsCatProcess->addCategoryData($aFeed['feed_id'], $iNewId);
			}
		}

		// Update database
		$iId = $this -> database() -> update($this->_sTable, $aUpdate,'feed_id = '. (int) $aVals['feed_id']);
		
		return $iId;
	}

	/**
	 * Delete selected RSS Provider from the system
	 * @param <int> $iFeedId is the selected Rss Provider Id
	 * @return TRUE|FALSE
	 */
	public function deleteFeed($iFeedId)
	{
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oNewsCat  = Phpfox::getService('foxfeedspro.category');
		$oNewsCatProcess = Phpfox::getService('foxfeedspro.category.process');
		
		$aFeed = $oFoxFeedsPro->getFeedById($iFeedId);
		
		if(!$aFeed)
		{
			return FALSE;
		}
		
		// Delete log image related to the RSS Provider
		if($aFeed['feed_logo'])
		{
			@unlink(Phpfox::getParam('core.dir_pic')."foxfeedspro/".$aFeed['feed_logo']);
		}
		
		if($aFeed['logo_mini_logo'])
		{
			//@unlink(Phpfox::getParam('core.dir_pic')."foxfeedspro/".$aFeed['logo_mini_logo']);
			// Update when download favicon and store on site
		}

		// Delete the old category list
		$aFeedCats = $oNewsCat->getFeedCategoryList($aFeed['feed_id']);
		foreach($aFeedCats as $iCatId)
		{
				$oNewsCatProcess->deleteCategoryData($aFeed['feed_id'], $iCatId);
		}
		
		// Process Delete
		
		// Delete all related news
		$this->deleteNewsByFeedId($iFeedId);
		
		// Delete Feed
		$this -> database() -> delete($this->_sTable, "feed_id = {$iFeedId}");
		return TRUE; 
	}
	
	public function deleteNews($iNewsId)
	{
		// Get Related News
		$aNews = Phpfox::getService('foxfeedspro')->getNewsById($iNewsId);
		if(!$aNews)
		{
			return FALSE;
		}
		
		//  Remove news image
		if($aNews['item_image'] && $aNews['item_server_id'] == '0')
		{
			$sTempLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aNews['item_image']);
			if (file_exists($sTempLink))
            {
                @unlink($sTempLink);
            }
		}
		
		// Delete news
		$this -> database() -> delete(Phpfox::getT('ynnews_items'),"item_id = {$iNewsId}");
		
		//delete feed
        if(Phpfox::isModule('feed')){
			Phpfox::getService('feed.process')->delete('foxfeedspro', $iNewsId);
		}
		// Update Feed Item Count
		$this->database()->updateCounter('ynnews_feeds','total_item','feed_id', $aNews['feed_id'], TRUE);
		

		//delete tag
		$this->database()->delete(Phpfox::getT('tag'), "category_id = 'foxfeedspro_news' AND item_id = " . (int) $iNewsId);
        
        if (Phpfox::isModule('tag'))
        {
            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . (int) $iNewsId . ' AND category_id = "foxfeedspro_news"', 1);      
            $this->cache()->remove('tag', 'substr');
        }


		return TRUE;
	}
	/**
	 * Update Rss Provider Order Display
	 * @param <int> $iFeedId is the Rss Provider Id need to update order
	 * @param <int> $iOrder is the order number
	 * @return TRUE
	 */
	 
	public function deleteNewsByFeedId($iFeedId)
	{
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$aNewsItems = $oFoxFeedsPro->getNewsByFeedId($iFeedId);
		
		if($aNewsItems)
		{
			foreach($aNewsItems as $aNews)
			{
				//  Remove news image
				if($aNews['item_image'])
				{
					$sTempLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aNews['item_image']);
					if (file_exists($sTempLink))
                    {
                        @unlink($sTempLink);
                    }
				}
				
				// Delete news
				$this -> database() -> delete(Phpfox::getT('ynnews_items'),"item_id = {$aNews['item_id']}");
					//delete tag
		        if (Phpfox::isModule('tag'))
		        {
		            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . (int) $aNews['item_id'] . ' AND category_id = "foxfeedspro_news"', 1);      
		            $this->cache()->remove('tag', 'substr');
		        }

				//delete feed
		        if(Phpfox::isModule('feed')){
					Phpfox::getService('feed.process')->delete('foxfeedspro', $aNews['item_id']);
				}
			}
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function updateFeedOrder($iFeedId, $iOrder)
	{
		$aUpdate = array(
			'order_display' => (int) $iOrder
		);
		
		$this->database()->update($this->_sTable, $aUpdate,'feed_id = '.(int) $iFeedId);
		return TRUE;
	}
	
	/**
	 * Add news information into the system
	 * @param array $aVals is the input data
	 * @return int $iId is the id of the added news
	 */
	public function addNews($aVals = array(),$aVal, $sPage = 0)
	{
		$iId = $this->database()->insert(Phpfox::getT('ynnews_items'), $aVals);

		$aItemImage = $this->database()->select('item_image, server_id')
                ->from(Phpfox::getT('ynnews_items'))
                ->where('item_id = '.$iId)
                ->execute('getSlaveRow');

        if (!empty($aVal)) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVal);
            if (!empty($aFile)) {
                $aItemImage['item_image'] = 'foxfeedspro'. PHPFOX_DS . sprintf($aFile['path'], '');
                Phpfox::getService('core.temp-file')->delete($aVal);
                $this->database()->update(Phpfox::getT('ynnews_items'),array('item_image'=> $aItemImage['item_image']),'item_id='.$iId);
            }
        }

		$aFeed = Phpfox::getService('foxfeedspro')->getLatestFeed();
		$aItemNews = array();
		if($aVals['is_approved'] == 1) {
            if ($aFeed['type_id'] == "foxfeedspro") {
                $aItemNews = Phpfox::getService('foxfeedspro')->getNewsById($aFeed['item_id']);
            }
            if ($aFeed['type_id'] == "foxfeedspro" && $aFeed['user_id'] == Phpfox::getUserId() && isset($aItemNews['feed_id']) && $aVals['feed_id'] == $aItemNews['feed_id']) {
                $this->database()->insert(Phpfox::getT('ynnews_newfeeds'), array('feed_id' => $aFeed['feed_id'], 'item_id' => $iId));
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('foxfeedspro', $iId, 0, 0, 0, (int)$aVals['user_id']) : null);
                if ($sPage != 0) {
                    $this->database()->insert(Phpfox::getT('pages_feed'), array(
                            'type_id' => 'foxfeedspro',
                            'user_id' => Phpfox::getUserId(),
                            'parent_user_id' => $sPage,
                            'item_id' => $iId,
                            'time_stamp' => PHPFOX_TIME,
                            'time_update' => PHPFOX_TIME,
                        )
                    );
                }
            }
        }
		// Update Feed Item Count
		$this->database()->updateCounter('ynnews_feeds','total_item','feed_id',$aVals['feed_id']);
		
		return $iId;
	}

	/**
	 * Update news information into the system
	 * @param array $aVals is the input data
	 * @return int $iId is the id of the updated news
	 */
	public function editNews($aVals = array())
	{
		$iId = $this->database()->update(Phpfox::getT('ynnews_items'), $aVals, 'item_id = '. $aVals['item_id']);
		
		return $iId;
	}
	
	/**
	 * Update time udpate for a selected feed
	 * @param <int> $iFeedId is the Id of the selected feed
	 */
	public function updateTimeOfFeed($iFeedId)
	{
		$this->database()->update($this->_sTable, array('time_update' => PHPFOX_TIME), "feed_id = {$iFeedId}");
	}
	
	/**
	 * Update feed status (Active/Inactive)
	 * @param <int> $iFeedId is the Id of the feed
	 * @param <int> $iIsActive is the status of the feed
	 */
	public function updateFeedStatus($iFeedId, $iIsActive)
	{
		// Update related news status
		$this->database()->update(Phpfox::getT('ynnews_items'),array('is_active' => $iIsActive), "feed_id = {$iFeedId}");
		// Update feed status
	 	$this->database()->update($this->_sTable,array('is_active' => $iIsActive), "feed_id = {$iFeedId}");
        // Remove cache total feed
        storage()->del('foxfeedspro/feed/total');
	}
	
	/**
	 * Update news featured status
	 * @param <int> $iNewsId is the Id of the news
	 * @param <int> $iIsFeatured is the feature status of the news
	 */
	public function updateFeaturedNews($iNewsId, $iIsFeatured)
	{
		$this->database()->update(Phpfox::getT('ynnews_items'),array('is_featured' => $iIsFeatured ),"item_id = {$iNewsId}");
	}
	
	/**
	 * Update news approvement status
	 * @param <int> $iNewsId is the Id of the news
	 * @param <int> $iIsApproved is the approval status of the news
	 */
	public function updateApprovalNews($iNewsId, $iIsApproved)
	{
		$aUpdate = array(
			'is_approved' => $iIsApproved
		);
		
		if($iIsApproved == 1)
		{
			$aUpdate['is_active'] = 1;
		}
		else
		{
			$aUpdate['is_active'] = 0;
		}
		
		$this->database()->update(Phpfox::getT('ynnews_items'), $aUpdate, "item_id = {$iNewsId}");
	}
	
	/**
	 * Update feed approvement status
	 * @param <int> $iNewsId is the Id of the feed
	 * @param <int> $iIsApproved is the approval status of the feed
	 */
	public function updateApprovalFeed($iFeedId, $iIsApproved)
	{
		$aUpdate = array(
			'is_approved' => $iIsApproved
		);
		
		if($iIsApproved == 1)
		{
			$aUpdate['is_active'] = 1;
		}
		else
		{
			$aUpdate['is_active'] = 0;
		}
		
		$this->database()->update($this->_sTable, $aUpdate, "feed_id = {$iFeedId}");
	}
	
	/**
	 * Update news view count
	 * @param <int> $iNewsId is the Id of the viewed news
	 */
	 public function updateNewsViewCount($iNewsId)
	 {
	 	$this->database()->updateCounter('ynnews_items', 'total_view', 'item_id', $iNewsId);
		return TRUE;
	 }

	public function deleteNewsInProviderByScrubs(){
		$providers = Phpfox::getService('foxfeedspro')->getProviderToDeleteNews();

		if(is_array($providers) && count($providers) > 0){
			foreach ($providers as $key => $prv) {
				$iNumberDayToDelete = (int)$prv['time_delete_news'];
				$iTimePeriod = $iNumberDayToDelete * 24 * 60 * 60;

				$iTimePeriod = PHPFOX_TIME - $iTimePeriod;
				// Set query conditions
				$aConds = array("ni.item_pubDate < $iTimePeriod");

				$aNewsItems = Phpfox::getService('foxfeedspro')->getNewsItems($aConds, NULL, NULL, NULL);
				foreach($aNewsItems as $aNews)
				{
					$this->deleteNews($aNews['item_id']);
				}

				$this->database()
					->update(Phpfox::getT('ynnews_feeds')
						,array('time_delete_news_stamp' => PHPFOX_TIME +  ((int)$prv['time_delete_news'] * 24 * 60 * 60))
						, "feed_id = " . (int)$prv['feed_id']);
			}
		}
	}

	public function deleteMyCategory($iId)
	{
		// Get related category
		$aCategory = Phpfox::getService('foxfeedspro.category')->getCategory($iId);

		// Delete process
		if($aCategory)
		{
			// Check if category is free to delete
			if($aCategory['used'] == 0)
			{
                $aSubCategories = $this->database()->select('category_id')
                    ->from(Phpfox::getT('ynnews_categories'))
                    ->where('parent_id = ' . (int) $iId. ' AND user_id ='.(int)Phpfox::getUserId())
                    ->execute('getRows');
                if (!empty($aSubCategories))
                {
                    $aSubCategoryIds = array();

                    foreach ($aSubCategories as $aItem)
                    {
                        $aSubCategoryIds[] = array_shift($aItem);
                    }

                    $sSubCategories = implode(',', $aSubCategoryIds);

                    $this->database()->update(Phpfox::getT('ynnews_categories'), array('parent_id' => $aCategory['parent_id']), 'category_id IN ('.$sSubCategories.')');
                }

				$this->database()->delete(Phpfox::getT('ynnews_categories'), 'category_id = ' . (int) $iId);
				$this->database()->delete(Phpfox::getT('ynnews_category_data'), 'category_id = ' . (int) $iId);
				return true;
			}
			else
			{
				Phpfox_Error::set(_p('foxfeedspro.cannot_delete_category_that_currently_has_related_data'));

				return false;
			}
		}
		return false;
	}
}