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

defined('YOUNET_NEWS_HOST') or define('YOUNET_NEWS_HOST', "http://newsservice.younetco.com/v1.1/parser.php");
 
class FoxFeedsPro_Service_FoxFeedsPro extends Phpfox_Service
{
	private $_defaultLanguage = 'en';
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_feeds');
	}
	
	/**
	 * Get language list in the system
	 * @return array of languages information 
	 */
	public function getLanguages()
	{
		$aLanguages = $this -> database()
							-> select('*')
							-> from(Phpfox::getT('language'))
							-> execute('getRows');
		return $aLanguages;
	}
	
	/**
	 * Get feed by name or url
	 * @param array $aVals information
	 * @return array of feed information
	 */
	public function getFeedsByNameOrURL($aVals = array())
	{
		$aConds = array();
		$sFilter = "";
		
		// Do not get the currently edit RSS Provider
		if (isset($aVals['feed_id']))
		{
			$aConds[] = "feed_id  <> ".$aVals['feed_id']; 
		}
		
		// Add Feed Name Filter Condition
		if ( isset($aVals['feed_name']))
		{
		$sFilter .= "feed_name='{$aVals['feed_name']}'";
		}
		
		// Add URL Filter Condition
		if ( isset($aVals['feed_url']))
		{
			if (!empty($sFilter))
			{
				$sFilter .= " OR ";
			}
			$sFilter .= "feed_url='{$aVals['feed_url']}'";
		}
		
		if($sFilter != "")
		{
			$aConds[] = "AND ({$sFilter})";
		}

		$aFeeds = $this->database()->select('*')
		->from(Phpfox::getT('ynnews_feeds'))
		->where($aConds)
		->execute('getRows');
		return $aFeeds;
	}
	
	/**
	 * Parse feed link to get logo and FavIcon
	 * @param string $sFeedUrl is the URL of the feed
	 * @return array(logo link, favicon link)
	 */
	public function getLogoAndFavIcon($sFeedUrl)
	{
		$aFeed = $this->getData($sFeedUrl);
		
		return array(
			'logo'	 => $aFeed['image_logo'],
			'favicon'=> $aFeed['logo_ico']
		);
	}
	
	/**
	 * Upload logo file to server
	 * @param string $sFile label
	 * @return string Logo image path
	 */
	public function uploadLogo($sFile)
	{
		//Check Folder Storage
		$sNewsPicStorage = Phpfox::getParam('core.dir_pic').'foxfeedspro';
		if(!is_dir($sNewsPicStorage))
		{
			@mkdir($sNewsPicStorage, 0777, 1);
			@chmod($sNewsPicStorage, 0777);
		}
		
		// Check image extention
		if(!$aImageFile = phpfox::getLib('file')->load($sFile, array('png', 'jpg', 'gif')))
		{
			 return false;
		}
		
		$sLogoUrl = phpfox::getLib('file')->upload($sFile, $sNewsPicStorage . PHPFOX_DS, $aImageFile['name']);
		$sLogoUrl = str_replace('%s','',$sLogoUrl);
		return $sLogoUrl;
	}

	public function uploadFavicon($sFile)
	{
		//Check Folder Storage
		$sNewsPicStorage = Phpfox::getParam('core.dir_pic').'foxfeedspro';
		if(!is_dir($sNewsPicStorage))
		{
			@mkdir($sNewsPicStorage, 0777, 1);
			@chmod($sNewsPicStorage, 0777);
		}
		
		// Check image extention
		if(!$aImageFile = phpfox::getLib('file')->load($sFile, array('png', 'jpg', 'gif','ico')))
		{
			 return false;
		}
		
		$sLogoUrl = phpfox::getLib('file')->upload($sFile, $sNewsPicStorage . PHPFOX_DS, $aImageFile['name']);
		$sLogoUrl = str_replace('%s','',$sLogoUrl);
		return $sLogoUrl;
	}

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
	
	/**
	 * Download logo file to server
	 * @param string $sImgUrl logo url
	 * @return string Logo image path
	 */


    public function downloadLogo($sImgUrl)
    {
        if (!$this->is_url_exist($sImgUrl)) {
            return false;
        }

        if (!$sImgUrl) {
            return '';
        }
        $pos = stripos($sImgUrl, ".bmp");
        if ($pos > 0) {
            return $sImgUrl;
        }
        //Check Folder Storage
        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'foxfeedspro';
        if (!is_dir($sNewsPicStorage)) {
            @mkdir($sNewsPicStorage, 0777, 1);
            @chmod($sNewsPicStorage, 0777);
        }

        // Generate Image object and store image to the temp file
        $iToken = rand();
        $oImage = \Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');

        if (empty($oImage) && (substr($sImgUrl, 0, 8) == 'https://')) {
            $sImgUrl = 'http://' . substr($sImgUrl, 8);
            $oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
        }
        $sTempImage = 'foxfeedspro_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME . '.jpg';
        \Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
        // Save image
        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '.jpg';
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage,sprintf($ThumbNail, ''), 1024, 1024);
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);

        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
        $sFileName = str_replace("\\", "/", $sFileName);

        // Return logo file
        return $sFileName;
    }
	/**
	 * Download favicon file to server
	 * @param string $sFavImageData favicon image data
	 * @return string favicon image path
	 */
	public function downloadFavIcon($sFavImageData)
	{
		if(!$sFavImageData)
		{
			return '';
		}
		//Check Folder Storage
		$sNewsPicStorage = Phpfox::getParam('core.dir_pic').'foxfeedspro';
		if(!is_dir($sNewsPicStorage))
		{
			@mkdir($sNewsPicStorage, 0777, 1);
			@chmod($sNewsPicStorage, 0777);
		}
		
		// Generate Image object and store favicon to the temp file
		$sTempImage = 'foxfeed_temp_favicon_'.PHPFOX_TIME;
        Phpfox::getLib('file')->writeToCache($sTempImage, $sFavImageData);
		
		// Save Favicon
		$sImageLocation = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS). md5('favicon_'.PHPFOX_TIME) . '.png';
		@copy(PHPFOX_DIR_CACHE . $sTempImage, sprintf($sImageLocation, ''));
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);
		// Return logo file
		$sFileName = str_replace(Phpfox::getParam('core.dir_pic')."foxfeedspro/", "", $sImageLocation);
		return $sFileName;	
	}
	
	/**
	 * Download image to server
	 * @param string $sImgUrl image url
	 * @return string Logo image path
	 */
	public function downloadImage($sImgUrl)
	{
		if(!$sImgUrl)
		{
			return '';
		}
		$pos = stripos($sImgUrl, ".bmp");
		if($pos>0)
		{
			return $sImgUrl;
		}
		//Check Folder Storage
		$sNewsPicStorage = Phpfox::getParam('core.dir_pic').'foxfeedspro';
		if(!is_dir($sNewsPicStorage))
		{
			@mkdir($sNewsPicStorage, 0777, 1);
			@chmod($sNewsPicStorage, 0777);
		}

		// Generate Image object and store image to the temp file
		$iToken = rand();
		$oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
		$sTempImage = 'foxfeedspro_temp_thumbnail_'.$iToken.'_'.PHPFOX_TIME;
                Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
		
		// Save image
		$ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_'.$iToken.'_'.PHPFOX_TIME) . '.jpg';
		Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, $ThumbNail, 250, 250);

		@unlink(PHPFOX_DIR_CACHE . $sTempImage);
     
		$sFileName = str_replace(Phpfox::getParam('core.dir_pic'),"", $ThumbNail);
                $sFileName = str_replace("\\", "/", $sFileName);
		// Return logo file
		return $sFileName;	
	}
	/**
	 * Get total item count from query
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	public function getItemCount($sTableName, $sTableAlias, $aConds = array())
	{
		// Generate query object		
		$oQuery = $this -> database() 
						-> select("COUNT(*)") 
						-> from(Phpfox::getT($sTableName), $sTableAlias);
		
		// Filter select condition
		if($aConds)
		{
			$oQuery -> where($aConds);
		}
		// print_r($oQuery->execute());die;
		return $oQuery -> execute('getSlaveField');				
	}
	
     public function getNewsItemsCount($aConds = array())
     {
          // Generate query object         
          $oQuery = $this -> database()
                              -> select("COUNT(*)")
                              -> from(Phpfox::getT('ynnews_items'), 'ni ')
                              -> join(Phpfox::getT('ynnews_feeds'),'nf', 'nf.feed_id = ni.feed_id');
         
          // Filter select condition
          if($aConds)
          {
               $oQuery -> where($aConds);
          }
          // print_r($oQuery->execute());die;
          return $oQuery -> execute('getSlaveField');                   
     }
	
	/**
	 * Get Feed items according to the data input (this only use for back-end browsing)
	 * @param array $aConditions is the array of filter conditions 
	 * @param string $sOrder is the listing order 
	 * @param int $iLimit is the limit of row's number output
	 * @return array of rss provider items data
	 */
	public function getFeeds($aConds = array(), $iPage = 0, $iPageSize = null, $iCount = null, $sOrder = null, $sMode = '')
	{
		if($sMode == 'browse')
		{
			$sSelectFields ="nf.feed_id, nf.feed_name, nf.feed_logo, nf.logo_mini_logo, nf.is_active_logo, nf.is_active_mini_logo, nf.feed_item_display, nf.feed_item_display_full, nf.server_id";
		} 
		else
		{
			$sSelectFields = "nf.feed_id, nf.feed_logo, nf.logo_mini_logo , nf.feed_name, nf.feed_url, nf.time_update, nf.is_active, nf.is_approved, nf.order_display, nf.feed_item_import, nf.server_id, nc.name as category_name";
		}
		// Generate query object		
		$oQuery = $this	-> database() 
						-> select($sSelectFields)
						-> from($this->_sTable, 'nf')
						-> leftjoin(Phpfox::getT('ynnews_categories'),'nc', 'nc.category_id = nf.category_id');
						
		// Filter select condition
		if($aConds)
		{
			$oQuery->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oQuery->order($sOrder);
		}				
		
		// Setup limit items getting
		$oQuery->limit($iPage, $iPageSize, $iCount);
		
		$aFeeds = $oQuery->execute('getRows');
		
		foreach($aFeeds as $key => &$aFeed)
		{
            if (Phpfox::getParam('core.allow_cdn') && $aFeed['server_id'] > 0) {
                $sStrReplace = Phpfox::getParam("core.cdn_pic") . "file/pic/foxfeedspro";
                $aFeed['feed_logo'] = str_replace($sStrReplace, "", $aFeed['feed_logo']);

                if (isset($aFeed['category_name'])) {
                    $aFeed['category_name'] = Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aFeed['category_name']) ? _p($aFeed['category_name']) : $aFeed['category_name']);
                }

                if (strpos($aFeeds[$key]['logo_mini_logo'], "http") !== false) {
                    $aFeeds[$key]['logo_mini_logo'] = $aFeeds[$key]['logo_mini_logo'];
                } else {
                    $aFeeds[$key]['logo_mini_logo'] = Phpfox::getLib('cdn')->getUrl('file/pic/foxfeedspro/' . $aFeeds[$key]['logo_mini_logo'], 2);
                }
            }
            elseif (!Phpfox::getParam('core.allow_cdn') && $aFeed['server_id'] > 0) {
                $sStrReplace = Phpfox::getParam("core.url_pic") . "foxfeedspro";
                $aFeed['feed_logo'] = str_replace($sStrReplace, "", $aFeed['feed_logo']);

                if (isset($aFeed['category_name'])) {
                    $aFeed['category_name'] = Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aFeed['category_name']) ? _p($aFeed['category_name']) : $aFeed['category_name']);
                }

                if (strpos($aFeeds[$key]['logo_mini_logo'], "http") !== false) {
                    $aFeeds[$key]['logo_mini_logo'] = $aFeeds[$key]['logo_mini_logo'];
                } else {
                    $aFeeds[$key]['logo_mini_logo'] = Phpfox::getLib('cdn')->getUrl('file/pic/foxfeedspro/' . $aFeeds[$key]['logo_mini_logo'], 2);
                }
            }
            else {
                $sStrReplace = Phpfox::getParam("core.url_pic") . "foxfeedspro";
                $aFeed['feed_logo'] = str_replace($sStrReplace, "", $aFeed['feed_logo']);
                if (isset($aFeed['category_name'])) {
                    $aFeed['category_name'] = Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aFeed['category_name']) ? _p($aFeed['category_name']) : $aFeed['category_name']);
                }

                if (strpos($aFeeds[$key]['logo_mini_logo'], "http") !== false) {
                    $aFeeds[$key]['logo_mini_logo'] = $aFeeds[$key]['logo_mini_logo'];
                } else {
                    $aFeeds[$key]['logo_mini_logo'] = Phpfox::getParam('core.url_pic') . "foxfeedspro/" . $aFeeds[$key]['logo_mini_logo'];
                }
            }

		}
	 	return $aFeeds;
	}

	/**
	 * Get news provider data information through it's Id
	 * @param int $iFeedId is the id of news provider need to get data
	 * @return array of selected news provider
	 */
	public function getFeedById($iFeedId,$sMode = "")
	{
	 	if($sMode == "quick")
		{
			$sSelectedField = "feed_id, user_id, feed_name";
		}
		else
		{
			$sSelectedField = "*";
		}
		$aFeed = $this -> database() 
				   -> select($sSelectedField) 
				   -> from($this->_sTable)
				   -> where("feed_id = {$iFeedId}")
				   -> execute('getRow');	
		$sStrReplace = Phpfox::getParam("core.url_pic")."foxfeedspro";
		if(isset($aFeed['feed_logo']))
			$aFeed['feed_logo'] = str_replace($sStrReplace,"", $aFeed['feed_logo']);
		else {
			$aFeed['feed_logo'] = '';
		}

		if(isset($aFeed['logo_mini_logo']) && strpos($aFeed['logo_mini_logo'], "http") !== false){
			$aFeed['logo_mini_logo'] = $aFeed['logo_mini_logo'];
		}
		else if(isset($aFeed['logo_mini_logo'])){
			$aFeed['logo_mini_logo'] = Phpfox::getParam('core.url_pic') . "foxfeedspro/" . $aFeed['logo_mini_logo'];
		}


		return $aFeed;
	}
	/**
	 * Get news provider data information through user 's Id
	 * @param int $iUserId is the id of the user
	 * @return array of selected news provider
	 */
	public function getFeedsByUserId($iUserId = 0)
	{
		$sConds = "";
		if($iUserId == 0)
		{
			$sConds = "1";	
		}
		else {
			$sConds = "user_id = {$iUserId}";
		}
		
		$aFeeds = $this->database()
				->select('feed_id, feed_name')
				->from($this->_sTable)
				->where($sConds)
				->execute('getRows');
		return $aFeeds;
	}
	
	/**
	 * Parse News Data And Save Them Into Database
	 * @param <array> $aFeed is RSS Provider data
	 * 
	 */
	 
	public function objectToArray($d) {
		if (is_object($d)) {
			$d = get_object_vars($d);
			
		}
		return $d;
	}

	public function getNews($aFeed,$sPage = 0)
	{
		// check RSS provider of page or not 
		$newsPageID = 0;
		if(isset($aFeed['page_id']) && (int)$aFeed['page_id'] > 0){

			$aPage = Phpfox::getService('pages')->getPage((int)$aFeed['page_id']);
			$newsUserID = $aPage['user_id'];
			$newsPageID = (int)$aFeed['page_id'];

			$aUserOfPage = $this->database()->select('u.user_id, p.view_id')
				->from(Phpfox::getT('user'), 'u')
				->join(Phpfox::getT('pages'), 'p', 'p.page_id = u.profile_page_id')
				->where('u.profile_page_id = ' . (int) $newsPageID)
				->execute('getSlaveRow');
		} else {
			$newsUserID = $aFeed['user_id'];
		}

		// Generate option variables
	 	$iImportNum = $aFeed['feed_item_import'];
		$bIsRandomFeatured = Phpfox::getParam('foxfeedspro.is_random_featured');
		$bIsImageDowloaded = Phpfox::getParam('foxfeedspro.is_image_downloaded');
		$oParseInput = Phpfox::getLib('parse.input');
		$iFeaturedNewsCounting = 0;
		 
		// Service Object
		$oNewsFeedProcess = Phpfox::getService('foxfeedspro.process');
		
		// Parse Feed to get News Data
		$aFeedOption = array('uri' => urlencode($aFeed['feed_url']));
		
		$sContent = "";
		
			// Parse Mode 
		$bParseDirectly = Phpfox::getParam('foxfeedspro.parse_news_directly');
		$sParserLink = YOUNET_NEWS_HOST;
		if($bParseDirectly)
		{
			$sParserLink = $this->getStaticPath()."module/foxfeedspro/static/parser/parser.php";			
		}
		//print_r($sParserLink . '?' . http_build_query($aFeedOption, NULL, "&"));die;
			// Using CURL to get news items information
		if (function_exists('curl_init')) {
			$cURL = curl_init($sParserLink . '?' . http_build_query($aFeedOption, NULL, "&"));
			curl_setopt($cURL, CURLOPT_TIMEOUT, 200);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			$sContent = curl_exec($cURL);
			curl_close($cURL);
		}
		else 
		{
			$sContent = file_get_contents($sParserLink . '?' . http_build_query($aFeedOption, NULL, "&"));
		}
		
		if (NULL != $sContent) 
		{
			if($bParseDirectly)
			{
				$sContent = strstr($sContent, '{');
				$iLastChar = strrpos($sContent,'}'); 
				if($iLastChar>0){
					if($iLastChar<strlen($sContent))
						$iLastChar+=1;
					$sContent = trim(substr($sContent,0,$iLastChar));
				}
				$aData = json_decode($sContent);
				$aData = $this->objectToArray($aData);
				// Prevent case: $aData['rows'] = 'No news data gotten'
				if($aData['total']>0 && is_array($aData['rows'])){
					foreach($aData['rows'] as $key=>$data){
						$data = $this->objectToArray($data);
						$aData['rows'][$key] = $data;
					}
				}
			}
			else
				$aData = json_decode($sContent, 1);
			
		}
		else 
		{
			return;
		}
		
		// Store news data
		if($aData['total'] && $aData['total'] > 0)
		{
			
			if(is_array($aData['rows']))
			{
				foreach($aData['rows'] as $iKey => $aItem)
				{
					// Break if reached allowed item number
				 	if($iKey == $iImportNum)
					{
						break;
					}
					// Get Item Author
					if(!isset($aItem['author']) || $aItem['author'] == "")
					{
						$aItem['author'] = $this->getDomainNameByLink($aItem['item_url_detail']);
					}
					// Get Image Thumbnail Path
					$sImageLink = $aItem['item_image'];
					
					if($bIsImageDowloaded && $sImageLink)
					{
						$sImageLink = $this->downloadImage($sImageLink);	
					}

					// News item
					$aNews = array(
						'feed_id'			 	=> $aFeed['feed_id'],
						'owner_type'		 	=> "user",
						'user_id'			 	=> $newsUserID,
						'page_id'			 	=> $newsPageID,
						'item_title'		 	=> $oParseInput->clean($aItem['item_title']),
						'item_alias'		 	=> $oParseInput->clean($aItem['item_title']),
						'item_description'   	=> $aItem['item_description'],
						'item_description_parse'=> $aItem['item_description'],
						'item_content'		 	=> $aItem['item_content'],
						'item_content_parse' 	=> $oParseInput->prepare($aItem['item_content']),
						'item_image'		 	=> $sImageLink,
						'item_url_detail'	 	=> $aItem['item_url_detail'],
						'item_author'		 	=> $aItem['author'],
						'item_pubDate'		 	=> ($aItem['item_pubDate'])?strtotime($aItem['item_pubDate']):PHPFOX_TIME,
						'item_pubDate_parse' 	=> ($aItem['item_pubDate'])?$aItem['item_pubDate']:date("D, d M Y h:i:s e", PHPFOX_TIME),
						'added_time'			=> PHPFOX_TIME,
						'is_active'			 	=> $aFeed['is_active'],
						'is_approved'			=> 1,
						'is_edited' 			=> 0,
                        'server_id'             => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                        'is_download_image'     => $bIsImageDowloaded
					);
					
					// Feature 3 first gotten news if random featured mode is enabled
					if($bIsRandomFeatured && $iFeaturedNewsCounting < 3 && $sImageLink)
					{
						$aNews['is_featured'] = 1;
						$iFeaturedNewsCounting ++;
					}
					// Get existed news
					$aExistedNews = $this->getExistedNews($aNews['item_url_detail'], $aNews['feed_id']);
					
					if(!$aExistedNews)
					{
						// Add new News
						
						$iNewsItemID = $oNewsFeedProcess->addNews($aNews,$sPage);


						$aTagsFromFeeds = Phpfox::getService('tag')->getTagsById('foxfeedspro_feeds', $aNews['feed_id']);
						$sTagsFeeds = '';					
						$sTagsNews = '';
						if (isset($aTagsFromFeeds[$aNews['feed_id']]))
						{
							foreach ($aTagsFromFeeds[$aNews['feed_id']] as $aTag)
							{
								$sTagsFeeds .= ' ' . $aTag['tag_text'] . ',';	
							}
							$sTagsFeeds = trim(trim($sTagsFeeds, ','));
						}

						if($sTagsNews == ''){
							$sTagsNews =  $sTagsFeeds;
						}
						else
						if($sTagsNews != '' && $sTagsFeeds != ''){
							$sTagsNews .= ','.$sTagsFeeds;
						}
					
						if (Phpfox::isModule('tag') && isset($sTagsNews) && ((is_array($sTagsNews) && count($sTagsNews)) || (!empty($sTagsNews))))
						{
							Phpfox::getService('tag.process')->add('foxfeedspro_news', $iNewsItemID, Phpfox::getUserId(), $sTagsNews);
						}
						
						$aSelUser = Phpfox::getService('user')->getUser((int)$newsUserID);

//                        if (isset($aSelUser['user_id']) && (int)$newsPageID > 0) {
//                            // add feed which creating by pages
//                            if (Phpfox::getUserGroupParam($aSelUser['user_group_id'],
//                                'foxfeedspro.can_add_rss_provider')) {
//                                (Phpfox::isModule('feed')
//                                    ? Phpfox::getService('feed.process')
//                                        ->callback(Phpfox::callback('pages' . '.getFeedDetails', $newsPageID))
//                                        ->add('foxfeedspro', $iNewsItemID, 0, 0, $newsPageID,
//                                            $aUserOfPage['user_id']) : null);
//                            }
//                        }
					}
					else
					{
						// Update news if it is not edited yet
						if(!$aExistedNews['is_edited'])
						{
							$sTempImageLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aExistedNews['item_image']);
							if(file_exists($sTempImageLink))
							{
								@unlink($sTempImageLink);	
							}
							$aNews['item_id'] = $aExistedNews['item_id'];
							if(isset($aNews['added_time'])){
								unset($aNews['added_time']);
							}
							$oNewsFeedProcess->editNews($aNews);
						}
						else 
						{
							// Remove image downloaded if there is no add/update process	
							if($bIsImageDowloaded && $sImageLink)
							{
								$sImageLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aExistedNews['item_image']);
								if(file_exists($sImageLink))
								{
									@unlink($sImageLink);
								}
							}
						}
					}
				}
			}
		}
	}

	 public function saveData($aData, $aFeed)
	 {	
	 	// Generate option variables
	 	 $iImportNum = $aFeed['feed_item_import'];
		 $bIsRandomFeatured = Phpfox::getParam('foxfeedspro.is_random_featured');
		 $bIsImageDowloaded = Phpfox::getParam('foxfeedspro.is_image_downloaded');
		 $oParseInput = Phpfox::getLib('parse.input');
		 $iFeaturedNewsCounting = 0;
		 
		 // Service Object
		 $oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		 
		 foreach ($aData['entries'] as $iKey => $aItem)
		 {
		 	// Break if reached allowed item number
		 	if($iKey == $iImportNum)
			{
				break;
			}
			
			// Get Item Author
			if($aItem['author'] == "")
			{
				if($aData['author'] != "")
				{
					$aItem['author'] == $aData['author'];
				}
				else
				{
					$aItem['author'] = $this->getDomainNameByLink($aData['link']);
				}
			}
			
			// Get Image Thumbnail Path
			$sImageLink = $this->parseDescription($aItem['description']);
			if($bIsImageDowloaded && $sImageLink)
			{
				$sImageLink = $this->downloadImage($sImageLink);	
			}

			$aNews = array(
				'feed_id'			 => $aFeed['feed_id'],
				'owner_type'		 => "user",
				'user_id'			 => $aFeed['user_id'],
				'item_title'		 => $oParseInput->clean($aItem['title']),
				'item_alias'		 => $oParseInput->clean($aItem['title']),
				'item_description'   => $oParseInput->clean($aItem['description']),
				'item_content'		 => $oParseInput->prepare($aItem['description']),
				'item_image'		 => $sImageLink,
				'item_url_detail'	 => $aItem['link_detail'],
				'item_author'		 => $aItem['author'],
				'item_pubDate'		 => $aItem['pubDate'],
				'item_pubDate_parse' => $aItem['pubDate_parse'],
				'added_time'		 =>	PHPFOX_TIME,
				'is_active'			 =>	$aFeed['is_active'],
				'is_approved'		 => 1,
				'is_edited' 		 =>	0,
                'server_id'          => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
			);
			// Feature 3 first gotten news if random featured mode is enabled
			if($bIsRandomFeatured && $iFeaturedNewsCounting < 3 && $sImageLink)
			{
				$aNews['is_featured'] = 1;
				$iFeaturedNewsCounting ++;
			}
			// Get existed news
			$aExistedNews = $this->getExistedNews($aNews['item_url_detail'], $aNews['feed_id']);
			
			if(!$aExistedNews)
			{
				// Add new News
				$oFoxFeedsProProcess->addNews($aNews);
			}
			else
			{
				// Update news if it is not edited yet
				if(!$aExistedNews['is_edited'])
				{
					$sTempImageLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aExistedNews['item_image']);
                    if (file_exists($sTempImageLink))
                    {
                        @unlink($sTempImageLink);
                    }
					$aNews['item_id'] = $aExistedNews['item_id'];
					$oFoxFeedsProProcess->editNews($aNews);
				}
				else 
				{
					// Remove image downloaded if there is no add/update process	
					if($bIsImageDowloaded && $sImageLink)
					{
						$sImageLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aExistedNews['item_image']);
						if (file_exists($sImageLink))
                        {
                            @unlink($sImageLink);
                        }
					}
				}
			}
		 }
	 }
	
	/**
	 * 
	 */
	public function getDomainNameByLink($sFeedUrl)
	{
		// Replace http,https 
		$sLink = str_replace(array("https://","http://"),"", $sFeedUrl);
		
		// Seperate link part into array
		$aHost = explode("/", $sLink);
		
		// Get Domain Name
		$sDomainName = $aHost[0];
		return $sDomainName;
	}
	
	/**
	 * Parse news description to get image thumbnail
	 * @param <text> $sDescription is the news description
	 * @return <string> $sResult is the image link
	 */
	private function parseDescription($sDescription){
		Phpfox_Error::skip(TRUE);
		$sResult= '';
		// Get img tags
		preg_match('/<img[^>]+>/i', $sDescription, $aResult);
		
		if ($aResult)
		{
			foreach( $aResult as $sImgTag)
			{
				// Get img source link
				preg_match('/(alt|title|src)=("[^"]*")/i', $sImgTag, $aImage);
				
				if( isset($aImage[2]) )
				{
					// Check size and get image link 
					$sImgLink = str_replace('"','', $aImage[2]);
					
					list($iWidth, $iHeight) = @getimagesize($sImgLink);
					
					if ($iWidth >= 40 && $iHeight >= 40)
					{
						$sResult = $sImgLink;
						break;
					}
				};
			}
		}
		Phpfox_Error::skip(FALSE);
		return $sResult;
	}
	
	/**
	 * Get existied news information according to news url and feed id
	 * @param <string> $sNewsUrl is the url of the news need to get
	 * @param <int> $iFeedId is the Id of the related Rss Provider
	 * @return <array> the information of existed news
	 */
	public function getExistedNews($sNewsUrl = '', $iFeedId = 0)
	{
		try{
			$aNews = $this->database()->select('item_id, is_edited, item_image, server_id as item_server_id')
					->from(Phpfox::getT('ynnews_items'))
                    ->where("feed_id = ".$iFeedId ." and item_url_detail like '".$this->database()->escape(trim($sNewsUrl))."'")
					->execute('getRow');
			
		}catch(Exception $ex){
			
		}
		return $aNews;
		
	}

	/**
	 * Generate an array of feed options to put in to select box
	 * @return <array> $aFeedOptions is array of feed options
	 */
	public function getFeedSelectOptions()
	{
		// Get RSS provider list from the system	
		$aFeeds = $this->database()->select('feed_id, feed_name')
						->from($this->_sTable)
						->order('feed_name ASC')
						->execute('getRows');
		
		// Generate Array of Feed Select Options 
		$aFeedOptions = array();
		foreach($aFeeds as $aFeed)
		{
			$aFeedName = substr($aFeed['feed_name'], 0, 100);
			$aFeedOptions[] = array(
					'feed_id' 	=> $aFeed['feed_id'], 
					'feed_name' => $aFeedName
			);
		}
						
		return $aFeedOptions;	
	}
	
	/**
	 * Get News items according to the data input (this only use for back-end browsing)
	 * @param array $aConditions is the array of filter conditions 
	 * @param string $sOrder is the listing order 
	 * @param int $iLimit is the limit of row's number output
	 * @return array of rss provider items data
	 */
	public function getNewsItems($aConds = array(), $iPage = 0, $iPageSize = null, $iCount = null, $sOrder = null, $sMode = '')
	{
		if($sMode == "admincp")
		{
			$sSelectFields = "ni.item_id, ni.item_title, ni.is_active, ni.item_image, ni.server_id as item_server_id, ni.item_pubDate, ni.item_pubDate_parse, ni.added_time, ni.is_approved, ni.is_featured, nf.feed_id, nf.feed_name, ni.is_download_image";
		} 
		else
		{
			$sSelectFields ="ni.item_id, ni.item_title, ni.item_alias, ni.item_image, ni.server_id as item_server_id, ni.item_author,  ni.item_description_parse, ni.item_content_parse, ni.item_pubDate, ni.item_pubDate_parse, ni.added_time, ni.total_view, ni.total_comment, ni.total_favorite,ni.is_download_image";
		}
		// Generate query object		
		$oQuery = $this	-> database() 
						-> select($sSelectFields)
						-> from(Phpfox::getT('ynnews_items'), 'ni ')
						-> join(Phpfox::getT('ynnews_feeds'),'nf', 'nf.feed_id = ni.feed_id');
						
		// Filter select condition
		if($aConds)
		{
			$oQuery->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oQuery->order($sOrder);
		}				
		
		// Setup limit items getting
		$oQuery->limit($iPage, $iPageSize, $iCount);
		$aNewsItems = $oQuery->execute('getRows');

        foreach ($aNewsItems as $k => $aNews) {
            if ($aNews['is_download_image'] == 1 && !empty($aNews['item_image']) && Phpfox::getParam('core.allow_cdn') && $aNews['item_server_id'] > 0) {
                $aNewsItems[$k]['item_image'] = Phpfox::getLib('cdn')->getUrl('file/pic/' . $aNews['item_image'],
                    $aNews['item_server_id']);
            } elseif ($aNews['is_download_image'] == 1 && !empty($aNews['item_image'])) {
                $aNewsItems[$k]['item_image'] = Phpfox::getParam('core.url_pic') . $aNews['item_image'];
            } elseif ($aNews['is_download_image'] == 0 && !empty($aNews['item_image']) && preg_match("/foxfeedspro/i",
                    $aNews['item_image'])) {
                $CorePath = Phpfox::getParam('core.url_pic') . 'foxfeedspro';
                $aNewsItems[$k]['item_image'] = preg_replace('/(.*)foxfeedspro/i', $CorePath, $aNews['item_image']);
            }
        }
        return $aNewsItems;
	}

	public function getNewsByNameOrURL($aVals)
	{
		$aConds = array();
		$sFilter = "";
		
		// Do not get the currently edit RSS Provider
		if (isset($aVals['item_id']))
		{
			$aConds[] = "item_id  <> {$aVals['item_id']}"; 
		}
		
		// Add Feed Name Filter Condition
		if ( isset($aVals['item_title']))
		{
		$sFilter .= "item_title = '{$aVals['item_title']}'";
		}
		
		// Add URL Filter Condition
		if ( isset($aVals['item_url_detail']))
		{
			if (!empty($sFilter))
			{
				$sFilter .= " OR ";
			}
			$sFilter .= "item_url_detail = '{$aVals['item_url_detail']}'";
		}
		
		if($sFilter != "")
		{
			$aConds[] = "AND ({$sFilter})";
		}

		$aNews = $this->database()->select('item_id, item_title, item_url_detail')
		->from(Phpfox::getT('ynnews_items'))
		->where($aConds)
		->execute('getRows');

		return $aNews;
	}
	
	/**
	 * Get All news items related to a feed
	 * @param <int> $iFeedId is the Id of the related feed
	 * @return <array> $aNewsItems is the array of news item gotten data
	 */
	public function getNewsByFeedId($iFeedId = 0)
	{
		$iFeedId = (int) $iFeedId;
		
		$aNewsItems = $this->database()->select('item_id, item_image,is_download_image, server_id as item_server_id')
							->from(Phpfox::getT('ynnews_items'))
							->where("feed_id = {$iFeedId}")
							->execute('getRows');
							
		return $aNewsItems;						
	}	
	
	/**
	 * Get news data through news id
	 * @param <int> $iNewsId is the Id of the related News
	 * @return <array> $aNews is the news gotten data
	 */
	public function getNewsById($iNewsId = 0, $bDeleteMode = true)
	{
		$iNewsId = (int) $iNewsId;
		
		$sSelectedFields = "";
		
		if(!$bDeleteMode)
		{
			$sSelectedFields = "ni.item_id, ni.item_title, ni.is_download_image, ni.item_url_detail, ni.feed_id, ni.user_id, ni.item_alias, ni.item_image, ni.server_id as item_server_id, ni.item_author, ni.item_description_parse, ni.item_description, ni.item_content, ni.item_content_parse, ni.is_approved, ni.is_active, ni.is_featured, ni.item_pubDate, ni.item_pubDate_parse, ni.added_time, ni.total_view, ni.total_like, ni.total_comment, ni.total_favorite,feed.rssparse, feed.lengthcontent";
		}
		else
		{
			$sSelectedFields = "ni.item_id, ni.item_image, ni.is_download_image, ni.server_id as item_server_id, ni.feed_id, ni.user_id, feed.rssparse, feed.lengthcontent";
		}
		
		if (Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'foxfeedspro\' AND lik.item_id = ni.item_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend'))
        {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = ni.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
			
		
		$aNews = $this -> database() -> select($sSelectedFields)
            -> from(Phpfox::getT('ynnews_items'),'ni')
            -> join(Phpfox::getT('ynnews_feeds'),'feed','feed.feed_id = ni.feed_id')
            -> where("ni.item_id = {$iNewsId}")
            -> execute('getRow');
		
        if (isset($aNews['item_image']) && Phpfox::getParam('core.allow_cdn') && $aNews['item_server_id'] > 0)
        {
            $aNews['item_image'] = Phpfox::getLib('cdn')->getUrl($aNews['item_image'], $aNews['item_server_id']);
        }
        
		return $aNews;
	}
	
	/**
	 * Get language id of current viewer
	 * @return <string> $sLanguageId is the language id gotten
	 */
	public function getUserUsingLanguage()
	{
		$sLanguageId = "";
		if (Phpfox::isUser()) {
            $sLanguageId = Phpfox::getUserBy('language_id');
            if (empty($sLanguageId)) {
                $sLanguageId = Phpfox::getParam('core.default_lang_id');
            }
        } else {
            if (($sLanguageId = Phpfox::getLib('session')->get('language_id'))) {
                
            } else {
                $sLanguageId = Phpfox::getParam('core.default_lang_id');
            }
        }
		
		if(!$sLanguageId)
		{
			$sLanguageId = $this->_defaultLanguage;
		}
		return $sLanguageId;
	}
	
	/**
	 * Upload image file to server
	 * @param string $sFile label
	 * @return string image path
	 */
	public function uploadImage($sFile)
	{
		//Check Folder Storage
		$sNewsPicStorage = Phpfox::getParam('core.dir_pic').'foxfeedspro/';
		$sNewsPicUrl 	 = Phpfox::getParam('core.url_pic').'foxfeedspro/';
		if(!is_dir($sNewsPicStorage))
		{
			@mkdir($sNewsPicStorage, 0777, 1);
			@chmod($sNewsPicStorage, 0777);
		}
		
		// Check image extention
		if(!$aImageFile = phpfox::getLib('file')->load($sFile, array('png', 'jpg', 'gif')))
		{
			 return false;
		}
		// Upload file and get image location
		$sTempImage = phpfox::getLib('file')->upload($sFile, $sNewsPicStorage, $aImageFile['name']);
		$sTempImage =  str_replace('%s','',$sTempImage);
		
		$sImageLocation = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage) . md5('thumbnail_'.PHPFOX_TIME) . '.jpg';
		
		// Create thumbnail and delete temp file
		Phpfox::getLib('image')->createThumbnail($sNewsPicStorage.$sTempImage, $sImageLocation, 250, 250);
		@unlink($sNewsPicStorage . $sTempImage);
		
		// Return the thumbnail url
		$sImageUrl = str_replace($sNewsPicStorage,"foxfeedspro/", $sImageLocation);
		
		return $sImageUrl;
	}

	public function checkNewsFavorite($iNewsId)
	{
		$iUserId = Phpfox::getUserId();
		
		$aRow = $this -> database()
					  -> select('*')
					  -> from(Phpfox::getT('ynnews_favorite'))
				  	  -> where("type_id = 'foxfeedspro' AND item_id = {$iNewsId} and user_id ={$iUserId}")
					  -> execute('getRow');
		
		if($aRow)
		{
			return TRUE;
		}
		else 
		{
			return FALSE;	
		}
	}
	
	public function addFavortitedNews($iNewsId, $iUserId)
	{
		$aNews = $this->getNewsById($iNewsId);
		// If news item is existed
		if($aNews)
		{
			$aInsert = array(
				'type_id' 	 => 'foxfeedspro',
				'item_id' 	 => $iNewsId,
				'user_id' 	 => $iUserId,
				'time_stamp' => PHPFOX_TIME
			);
			// Add news to favorite table
			$this->database()->insert(Phpfox::getT('ynnews_favorite'),$aInsert);
			
			// Update total favorite counter
			$this->database()->updateCounter('ynnews_items', 'total_favorite', 'item_id', $iNewsId);
			(($sPlugin = Phpfox_Plugin::get('foxfeedspro.service_foxfeedspro_addfavoritednews_end')) ? eval($sPlugin) : false);
		}
	}
	
	public function deleteFavortitedNews($iNewsId, $iUserId)
	{
		// Delete favorited news
		$this->database()->delete(Phpfox::getT('ynnews_favorite'),"item_id = {$iNewsId} AND user_id = {$iUserId}");
		
		// Update total favorite counter
		$this->database()->updateCounter('ynnews_items', 'total_favorite', 'item_id', $iNewsId, TRUE);
			(($sPlugin = Phpfox_Plugin::get('foxfeedspro.service_foxfeedspro_deletefavoritednews_end.php')) ? eval($sPlugin) : false);
	}
	
	public function checkFeedSubscribe($iFeedId)
	{
		$iUserId = Phpfox::getUserId();
		
		$aRow = $this -> database()
					  -> select('*')
					  -> from(Phpfox::getT('ynnews_subscribes'))
				  	  -> where("feed_id = {$iFeedId} and user_id ={$iUserId}")
					  -> execute('getRow');
		
		if($aRow)
		{
			return TRUE;
		}
		else 
		{
			return FALSE;	
		}
	}
	
	public function addSubscribedFeed($iFeedId, $iUserId)
	{
		$aFeed = $this->getFeedById($iFeedId, 'quick');
		// If news item is existed
		if($aFeed)
		{
			$aInsert = array(
				'feed_id' 	 => $iFeedId,
				'user_id' 	 => $iUserId,
				'time_stamp' => PHPFOX_TIME
			);
			// Add news to favorite table
			$this->database()->insert(Phpfox::getT('ynnews_subscribes'),$aInsert);
		}
	}
	
	public function deleteSubscribedFeed($iFeedId, $iUserId)
	{
		// Delete favorited news
		$this->database()->delete(Phpfox::getT('ynnews_subscribes'),"feed_id = {$iFeedId} AND user_id = {$iUserId}");
	}
	
	public function sendSubscribeNotification($aFeed)
	{
		if($aFeed)
		{
			$aSubscribes = $this -> database()-> select('yns.*,ynf.user_id as owner_user_id')
								 -> from(Phpfox::getT('ynnews_subscribes'),'yns')
								 ->leftJoin(Phpfox::getT('ynnews_feeds'),'ynf','ynf.feed_id = yns.feed_id ')
								 -> where("yns.feed_id = {$aFeed['feed_id']}")
								 -> execute('getRows');	
			if($aSubscribes)
			{
				foreach($aSubscribes as $aSubscribe)
				{
					Phpfox::getService('notification.process')->add('foxfeedspro_feedupdated', $aSubscribe['feed_id'], $aSubscribe['user_id'],$aSubscribe['owner_user_id']);
				}
				
				return TRUE;	

			}
		}
		
		return FALSE;
	}
	
	public function updateNewsItems($aNewsItems){
		foreach($aNewsItems as $key=>$aData){
			$content = $aData['item_description_parse'];
			$length = 220;
			while($length < strlen($content)){
				if($content[$length] == ' ')
					break;
				else {
					$length ++;
				}
			}
			$aData['item_description_parse'] = substr($content,0,$length);
			if($length<strlen($content))
			{
				$aData['item_description_parse'].="...";
			}
			$aNewsItems[$key] = $aData;
		}
		return $aNewsItems;
	}

	public function getProviderToDeleteNews(){
		return $this -> database()-> select('feed_id, time_delete_news')
							 -> from(Phpfox::getT('ynnews_feeds'))
							 -> where("1=1 AND time_delete_news > 0 AND time_delete_news_stamp < " . PHPFOX_TIME)
							 -> execute('getRows');	
	}

	public function getStaticPath(){
        $sCorePath = Phpfox::getParam('core.path');
        $sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
        return $sCorePath;
    }
	public function getLatestFeed(){
		return $this->database()->select("*")
					->from(Phpfox::getT('feed'))
					->order('feed_id DESC')
					->execute('getRow');
	}

    public function getTagCloud($mMaxDisplay)
    {
        return get_from_cache(['foxfeedspro','tags'],function() use ($mMaxDisplay){
            return array_map(function ($row){
                return [
                    'value' => $row['total'],
                    'key'=> $row['tag'],
                    'url' => $row['tag_url'],
                    'link'=> \Phpfox_Url::instance()->makeUrl('foxfeedspro.tag',$row['tag'])
                ];
            },$this->database()->select('category_id, tag_text AS tag, tag_url, COUNT(item_id) AS total')
                ->from(Phpfox::getT('tag'))
                ->where('category_id=\'foxfeedspro_news\'')
                ->group('tag_text, tag_url')
                ->order('total DESC')
                ->limit($mMaxDisplay)
                ->execute('getSlaveRows'));

        }, 1);

    }
}

?>