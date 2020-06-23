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

$sLibPath = PHPFOX_DIR . 'module' . PHPFOX_DS . 'foxfeedspro' . PHPFOX_DS . 'static' . PHPFOX_DS;
require_once($sLibPath . 'cutstring.php');

class FoxFeedsPro_Component_Controller_NewsDetails extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 
	private static $entities = array(
        "&trade;",
        "&#039;"
    );
	
	private function keepEntities($string,$length)
   	{
       // ==== Getting $to limit ==== //
	   $string = preg_replace('/\s+/', ' ', $string);
	   $from = 0;
	   $maxlength = $length;
	   $string =  htmlspecialchars_decode($string);
	  
	   do{
	   		$strTmp = trim(substr($string, 0,$length));
			
	   		$lengthTags = mb_strlen(trim(strip_tags($strTmp)),'UTF-8');
			if($lengthTags>=$maxlength)
			{
				break;
			}
	   		$length += ($maxlength - $lengthTags);

	   }while($length < mb_strlen($string,'UTF-8'));
		if(isset($string[$length-1]) && $string[$length-1]!=' ')
	   {
		while($length < mb_strlen($string,'UTF-8')){
				if($string[$length] == ' ')
					break;
				else {
					$length ++;
				}
			}
	}
       $to = $from + $length;
	
			
       // ==== Going through the text and checking if there are any entities that get cut ==== //
       foreach (self::$entities as $entity)
       {
       		
           // ==== Getting entity size ==== //
           $esize = mb_strlen($entity,'UTF-8');

           // ==== Getting start position of entity ==== //
           $epos_start = strpos($string, $entity);

           // ==== Getting end position of entity ==== //
           $epos_end = $epos_start + $esize;

           // ==== Checking if $from will cut the $entity ==== //
           if($from > $epos_start && $from <= $epos_end)
           {
               $from = $epos_start;
           }

           // ==== Checking if $to will cut the $entity ==== //
           if($to >= $epos_start && $to < $epos_end)
           {
               $to = $epos_start;
           }
       }

       // ==== Getting $current_length ==== //
       $new_length = $to - $from;

       // ==== Cutting the text to the proper length ==== //
       if($new_length <= $length)
       {
           // ==== Cutting string to size ==== //
           $string = substr($string, $from, $new_length);

           return $string;
       }
       elseif($new_length > $length) // If the text has shifted go through the function again
       {
           return self::keepEntities($string, $from, $length);
       }

       return false;
   }

	private function cut_html_string($string, $limit){
	  $output = new HtmlCutString($string, $limit);
	  return $output->cut();
	}

	 private function updateNews($aNews){
	
		if($aNews['rssparse']==0 && $aNews['lengthcontent']>0)
		{
			$length = $aNews['lengthcontent'];
			
			$aNews['item_content'] = $this->cut_html_string( html_entity_decode($aNews['item_content']),$length);
			$aNews['item_content_parse'] = $this->cut_html_string(html_entity_decode($aNews['item_content_parse']),$length);
		}
		
		return $aNews;
	 }
	 
	public function process()
	{
		Phpfox::getUserParam('foxfeedspro.can_view_news', TRUE);

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
		
		// Generate params
		$bIsPageNotFound = FALSE;
		$iNewsId = (int) $this->request()->get('item');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		// Get selected news
		$aNews = $oFoxFeedsPro->getNewsById($iNewsId, FALSE);
		$bIsFavorited = $oFoxFeedsPro->checkNewsFavorite($iNewsId);
		$aRelatedFeed = array();
		
		// Page not found if cannot get the related news
		$bIsAdmin = Phpfox::isAdmin();
		if(!$aNews)
		{
			$bIsPageNotFound = TRUE;
		}
		elseif(!$bIsAdmin && (!$aNews['is_approved'] || !$aNews['is_active']))
		{
			$this->url()->send('subscribe');
		}
		else
		{
			// Update news view number
			$oFoxFeedsProProcess->updateNewsViewCount($iNewsId);
			
			// Get related feed
			$aRelatedFeed = $oFoxFeedsPro->getFeedById($aNews['feed_id']);
			
			// Get related news in the same feed
			$iRelatedNewsNumber = Phpfox::getParam('foxfeedspro.number_related_news');
			$iLimit = (int) ($iRelatedNewsNumber / 2);
			
			// Get newer news
			$aNewerConds = "";
			if($aNews['item_pubDate'])
			{
				$aNewerConds = "ni.item_id <> {$aNews['item_id']} AND ni.feed_id = {$aNews['feed_id']} AND ni.is_approved = 1 AND ni. is_active = 1 AND ni.item_pubDate > {$aNews['item_pubDate']}";
			}
			else 
			{
				$aNewerConds = "ni.item_id <> {$aNews['item_id']} AND ni.feed_id = {$aNews['feed_id']} AND ni.is_approved = 1 AND ni. is_active = 1 AND ni.added_time > {$aNews['added_time']}";
			}
			$aNewerItems = $oFoxFeedsPro->getNewsItems($aNewerConds, $iLimit, NULL, NULL, "ni.item_pubDate DESC, ni.item_id DESC");
			
			// Get Older news
			$iRemaining = $iRelatedNewsNumber-count($aNewerItems);
			$aOlderConds = "";
			if($aNews['item_pubDate'])
			{
				$aOlderConds = "ni.item_id <> {$aNews['item_id']} AND ni.feed_id = {$aNews['feed_id']} AND ni.is_approved = 1 AND ni. is_active = 1 AND ni.item_pubDate <= {$aNews['item_pubDate']}";
			}
			else 
			{
				$aOlderConds = "ni.item_id <> {$aNews['item_id']} AND ni.feed_id = {$aNews['feed_id']} AND ni.is_approved = 1 AND ni. is_active = 1 AND ni.added_time <= {$aNews['added_time']}";
			}
			
			$aOlderItems = $oFoxFeedsPro->getNewsItems($aOlderConds, $iRemaining, NULL, NULL, "ni.item_pubDate DESC, ni.item_id DESC");
			
			$this->template()->assign(array(
				'aNewerItems' => $aNewerItems,
				'aOlderItems' => $aOlderItems
			));
            $aNews['bookmark_url'] = Phpfox::permalink('foxfeedspro.newsdetails', 'item_'.$aNews['item_id'], $aNews['item_title']);
			// Setup feed box for comment
			$this->setParam('aFeed', array(				
				'comment_type_id' => 'foxfeedspro',
				'privacy' => 0,
				'comment_privacy' => 0,
				'like_type_id' => 'foxfeedspro',
				'feed_is_liked' => isset($aNews['is_liked']) ? $aNews['is_liked'] : false,
				'feed_is_friend' => isset($aNews['is_friend']) ? $aNews['is_friend'] : false,
				'item_id' => $aNews['item_id'],
				'user_id' => $aNews['user_id'],
				'total_comment' => $aNews['total_comment'],
				'total_like' => $aNews['total_like'],
				'feed_link' => Phpfox::permalink('foxfeedspro.newsdetails', 'item_'.$aNews['item_id'], $aNews['item_title']),
				'feed_title' => $aNews['item_title'],
				'feed_display' => 'view',
				'feed_total_like' => $aNews['total_like'],
				'feed_image' => $aNews['item_image'] ? sprintf("<img src=\"%s\" />", $aNews['item_image']):'',
			    'feed_content' => $aNews['item_content'],
				'report_module' => 'foxfeedspro',
			    'report_phrase' => _p('foxfeedspro.report_this_news'),
				'time_stamp' => $aNews['item_pubDate']
			));		
			
			// add script for comment
			$this->template()->setHeader('cache', array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'jquery/plugin/jquery.scrollTo.js' => 'static_script',
				'quick_edit.js' => 'static_script',
				'pager.css' => 'style_css',
				'feed.js' => 'module_feed',
				'autoload.js' => 'module_foxfeedspro',
			));
		}
		
		// Set header, breadcrumb and variables
		$this->template()->setHeader(array(
			'front_end.js' 		=> 'module_foxfeedspro'
		));
		
		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		
		if($aNews)
		{
			$this->template()->setBreadcrumb($aRelatedFeed['feed_name'],$this->url()->permalink('foxfeedspro.feeddetails','feed_'.$aRelatedFeed['feed_id'], $aRelatedFeed['feed_name']));
//			$this->template()->setBreadcrumb($aNews['item_title'], $this->url()->permalink('foxfeedspro.newsdetails','item_'.$aNews['item_id'], $aNews['item_title']));
			$this->template()->setBreadcrumb("", "", TRUE);
		}
		
		$aNewsUpdate = $this->updateNews($aNews);
		if(isset($aNewsUpdate['item_content_parse'])){
			$aNewsUpdate['item_content_parse'] = str_replace('id="text"','',$aNewsUpdate['item_content_parse']);
			$aNewsUpdate['item_content'] = str_replace('id="text"','',$aNewsUpdate['item_content']);
		}

		$bIsDisplayPopUp = Phpfox::getParam('foxfeedspro.is_display_popup');
		$isViewMoreToForward = $this->__isViewMoreToForward($aNewsUpdate);
		if($isViewMoreToForward == true){
			$bIsDisplayPopUp = false;
		}

		$core_url = Phpfox::getParam('core.path');


		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('foxfeedspro_news', $aNewsUpdate['item_id']);	
			if (isset($aTags[$aNewsUpdate['item_id']]))
			{
				$aNewsUpdate['tag_list'] = $aTags[$aNewsUpdate['item_id']];
			}
		}
        $aNewsUpdate['url_item'] = Phpfox::permalink('foxfeedspro.newsdetails', 'item_' . $aNews['item_id'], $aNews['item_title']);

		$this->template()->assign(array(
			'bIsPageNotFound'	=> $bIsPageNotFound,
			'aFeed'				=> $aRelatedFeed,
			'core_url'				=> $core_url,
			'aNews'				=> $aNewsUpdate,
			'isViewMoreToForward'				=> $isViewMoreToForward,
			'bIsFavorited'		=> $bIsFavorited,
			'bIsFullContent'	=> $aNews['rssparse']>0?0:1,
			'bIsDisplayPopUp'	=> $bIsDisplayPopUp,
			'bIsFriendlyUrl'   	=> Phpfox::getParam('foxfeedspro.is_friendly_url'),
			'sDefaultLogoLink' 	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/small.gif",
			'sDefaultImgLink'  	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
			'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
			'sTagType'			=> 'foxfeedspro_news',
		));
		if($aNews['item_image'])
		{
			$sImageUrl = $aNews['item_image'];
            $this->template()->setHeader(array('<meta property="og:image" content="'. $sImageUrl . '" />'));
            $this->template()->setHeader(array('<link rel="image_src" href="'. $sImageUrl . '" />'));
		}

		 $this->template()->setHeader(array('<meta property="og:title" content="'. $aNews['item_title'] . '" />'));
		
		$this->template() -> setMeta('description', Phpfox::getParam('foxfeedspro.foxfeedsprofoxfeedspro_meta_description'))
						  -> setMeta('description', $aNews['item_title'] . '.')
						  -> setMeta('keywords', $this->template()->getKeywords($aNews['item_title']))
						  -> setMeta('keywords', Phpfox::getParam('foxfeedspro.foxfeedspro_meta_keywords'));
		$this->template()->setTitle($aNews['item_title']);

	}

	private function getImageFromContent($content) {
		preg_match('#(<img.*?>)#', $content, $results);
		var_dump($results);
		return $results[1];
	}

	private function __isViewMoreToForward($news){
		if(isset($news['item_url_detail']) && 
			(strpos($news['item_url_detail'], 'yahoo.com'))
				)
		{
			return true;
		}

		return false;
	}
	/*
	 * Clean method used to generate the top menu of the plugin according to the privacy settings in user group setting
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
