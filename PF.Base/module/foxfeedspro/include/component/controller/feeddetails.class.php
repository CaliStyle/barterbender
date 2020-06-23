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
 
class FoxFeedsPro_Component_Controller_FeedDetails extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	public function process()
	{
		// Check view allow
		$bCanView = Phpfox::getUserParam('foxfeedspro.can_view_news');
		if(!$bCanView)
		{
			$this->url()->send("subscribe");
		}
		
		// Build filter section menu on left side
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE')) 
		{
			$aFilterMenu = array(
				_p('foxfeedspro.browse_all') => '',
				TRUE,
				_p('foxfeedspro.my_rss_providers') 	 => 'foxfeedspro.feeds',
				_p('foxfeedspro.my_news') 			 => 'foxfeedspro.news',
				_p('foxfeedspro.my_favorited_news') 	 => 'foxfeedspro.view_favorite',
			);
		}
		$this -> template() -> buildSectionMenu('foxfeedspro', $aFilterMenu);
		
		// Generate params
		$bIsPageNotFound = FALSE;
		$bCanSubcribe = TRUE;
		$bIsSubscribed = FALSE;
		$iFeedId = (int) $this->request()->get('feed');
		$iViewer = Phpfox::getUserId();
		
		$aFeed = array();
		$aNewsItems = array();
		
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');

		$sLanguageId = $oFoxFeedsPro->getUserUsingLanguage();
		
		// Get the selected feed
		$aFeed = $oFoxFeedsPro->getFeedById($iFeedId);
		if(!$aFeed)
		{
			$bIsPageNotFound = TRUE;
		}
		elseif(!$aFeed['is_approved'] || !$aFeed['is_active'])
		{
			$this->url()->send('subscribe');
		}
		else 
		{
			if(!$iViewer || $aFeed['user_id'] == $iViewer)
			{
				$bCanSubcribe = FALSE;
			}
			
			if($bCanSubcribe)
			{
				$bIsSubscribed = $oFoxFeedsPro->checkFeedSubscribe($aFeed['feed_id']);
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
							'action' 	   => $this->url()->makeUrl('foxfeedspro.feeddetails',array('feed' => $iFeedId)),
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
						'show' 			=> array(5, 10, 15, 20, 25),
						'when_field' 	=> 'item_pubDate'
					)
				)
			);				
			// Setup search params
			$aBrowseParams = array(
				'module_id' => 'foxfeedspro',
				'alias' => 'ni',
				'field' => 'item_id',
				'table' => Phpfox::getT('ynnews_items'),
				'hide_view' => array('my')
			);
			
			// Set Filter Conditions
			$this->search()->setCondition("AND ni.feed_id = {$iFeedId} AND ni.is_approved = 1 AND ni.is_active = 1");
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
            
			// Setup pager
			Phpfox::getLib('pager')->set(
				array(
					'page'  => $this->search()->getPage(), 
					'size'  => $this->search()->getDisplay(), 
					'count' => $this->search()->browse()->getCount()
				)
			);
		}

		// Set header, breadcrumb and variables
		$this->template()->setHeader(array(
			'front_end.js' 		=> 'module_foxfeedspro'
		));
		
		
		
		if($aFeed)
		{
			$this->template()->setBreadcrumb($aFeed['feed_name'],$this->url()->permalink('foxfeedspro.feeddetails','feed_'.$aFeed['feed_id'],$aFeed['feed_name']));
			//$this->template()->setBreadcrumb("", "", TRUE);
		}
        else {
            $this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
        }
		
		$myCategories = Phpfox::getLib('database')->select('name, category_id, parent_id, ordering, name_url')
			->from(Phpfox::getT('ynnews_categories'))
			->where('parent_id = ' . (int) 0 . ' AND is_active = ' . (int) 1 . ' AND user_id = '.(int)Phpfox::getUserId())
			->order('ordering ASC')
			->execute('getRows');
		
		

		$this->template()->assign(array(
			'bIsPageNotFound'	=> $bIsPageNotFound,
			'bCanSubcribe'		=> $bCanSubcribe,
			'bIsSubscribed'		=> $bIsSubscribed,
			'aFeed'				=> $aFeed,
			'aNewsItems'		=> $aNewsItems,
			'sView'				=> $this->request()->get('view'),
			'bIsFriendlyUrl'   	=> Phpfox::getParam('foxfeedspro.is_friendly_url'),
			'sDefaultLogoLink' 	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/small.gif",
			'sDefaultImgLink'  	=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
			'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
			'urlAddCate' 		=> (count($myCategories) == 0) ? Phpfox::getLib('url') -> makeUrl('foxfeedspro.add'): '',
			'iPage'  			=> $this->search()->getPage()
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