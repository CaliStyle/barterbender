<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct()
	{            
		$this->_sTable = Phpfox::getT('fundraising_campaign');
            // if the notification module is disabled we cannot get the length to shorten, so we fallback to _iFallbackLength.
            $this->_iFallbackLength = 50;
	}


	public function getRatingData($iCampaignId)
	{

		//because we delegated rating resposibility to Rating module, we have to turn on need updating flag for noticing
		//campaign_owner_profile table to know when to update data
		//status = 1 mean we want it to be updated
		//mmm
		Phpfox::getService('fundraising.user.process')->updateNeedUpdatingStatusOfOwnerProfile($iCampaignId, $iStatus = 1);

		return array(
			'field' => 'campaign_id',
			'table' => 'fundraising_campaign',
			'table_rating' => 'fundraising_rating'
		);
	}
	
	public function paymentApiCallback($aParam)
	{
		$iDonorId = 0;
		$iTransactionId = $aParam['custom'];
		if($aParam['status'] == Phpfox::getService('fundraising.transaction')->getPaypalStatusCode('completed'))
		{
			if(!$aParam['total_paid'])
			{
				return false;
			}
			$iDonorId = Phpfox::getService('fundraising.user.process')->addDonor($iTransactionId, $aParam['total_paid'], $aParam);
            (($sPlugin = Phpfox_Plugin::get('fundraising.service_callback_payment__end')) ? eval($sPlugin) : false);
		}

		Phpfox::getService('fundraising.transaction.process')->updatePaypalTransaction($iTransactionId, $aParam, $iDonorId);
	}

	public function mobileMenu()
	{            
		return array(
			'phrase' => _p('fundraisings'),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_custom-fields.png')),
            'icon_class' => 'ico ico-money-bag-o'
		);
		}	
	
	/**
	 * Used for the function core.callback::getRedirection
	 * @return <type>
	 */
        /*
	public function getRedirectionTable()
	{
		return Phpfox::getT('blog_redirect');
	}
        */
	public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
	{            
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
		$aFundraisings = array();
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('fundraising_campaign'), 'fundraising')
			->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = fundraising.campaign_id")
			->join(Phpfox::getT('fundraising_text'), 'fundraising_text', 'fundraising_text.campaign_id = fundraising.campaign_id')
			->where($aConds)
			->execute('getSlaveField');	

		if ($iCnt)
		{
			$aRows = $this->database()->select("fundraising.*, " . (Phpfox::getParam('core.allow_html') ? "fundraising_text.description_parsed" : "fundraising_text.description") ." AS description, " . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_campaign'), 'fundraising')
				->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = fundraising.campaign_id")
				->join(Phpfox::getT('fundraising_text'), 'fundraising_text', 'fundraising_text.campaign_id = fundraising.campaign_id')
				->join(Phpfox::getT('user'), 'u', 'fundraising.user_id = u.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $sLimit, $iCnt)				
				->execute('getSlaveRows');	
						
			if (count($aRows))
			{
				foreach ($aRows as $aRow)
				{
					$aFundraisings[$aRow['campaign_id']] = $aRow;
				}						
			}
		}		
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
		return array($iCnt, $aFundraisings);
	}	
	
	public function canShareItemOnFeed(){}
	
	public function getTagSearch($aConds = array(), $sSort)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
		$aRows = $this->database()->select("fundraising.campaign_id AS id")
			->from(Phpfox::getT('fundraising_campaign'), 'fundraising')
			->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = fundraising.campaign_id")
			->join(Phpfox::getT('fundraising_text'), 'fundraising_text', 'fundraising_text.campaign_id = fundraising.campaign_id')
			->where($aConds)
			->order($sSort)	
			->group('fundraising.campaign_id')
			->execute('getSlaveRows');							
		
		$aSearchIds = array();
		foreach ($aRows as $aRow)
		{
			$aSearchIds[] = $aRow['id'];
		}		
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
		return $aSearchIds;		
	}	
	
	public function getTagCloud()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettagcloud__start')) ? eval($sPlugin) : false);
		return array(
			'link' => 'fundraising',
			'category' => 'fundraising'
		);
	}
	
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'fundraising.view_browse_fundraisings'))
		{
			return null;
		}		
		
		$aMenus[] = array(
			'phrase' => _p('fundraisings'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'fundraising/',
			'icon' => 'feed/fundraising.png',
			'landing' => 'fundraising',
            'menu_icon' => 'ico ico-money-bag-o'
		);
		
		return $aMenus;
	}
	
      public function addCampaign($iId)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'pages',
			'item_id' => $iId,
			'table_prefix' => 'pages_'
		);
	}
      
	public function getPageSubMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'fundraising.share_campaigns'))
		{
			return null;
		}		
		
		return array(
			array(
				'phrase' => _p('create_a_fundraising'),
				'url' => Phpfox::getLib('url')->makeUrl('fundraising.add', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}	
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['fundraising.share_campaigns'] = _p('who_can_share_campaigns');
		$aPerms['fundraising.view_browse_campaigns'] = _p('who_can_view_browse_campaigns');
		
		return $aPerms;
	}
	
	public function canViewPageSection($iPage)
	{		
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'fundraising.view_browse_fundraisings'))
		{
			return false;
		}
		
		return true;
	}	
	
	public function getActivityFeedComment($aRow)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
		}		
		
		$aItem = $this->database()->select('p.campaign_id, p.title, p.time_stamp, p.total_comment, p.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('fundraising_campaign'), 'p', 'c.type_id = \'fundraising\' AND c.item_id = p.campaign_id AND c.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('c.comment_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aItem['campaign_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::permalink('fundraising', $aItem['campaign_id'], $aItem['title']);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
		$sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
		$sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);
		
		if ($aRow['user_id'] == $aItem['user_id'])
		{
			$sMessage = _p('posted_a_comment_on_gender_fundraising_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
		}
		else
		{			
			$sMessage = _p('posted_a_comment_on_user_name_s_fundraising_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
		}
		
		return array(
			'no_share' => true,
			'feed_info' => $sMessage,
			'feed_link' => $sLink,
			'feed_status' => $aItem['text'],
			'feed_total_like' => $aItem['total_like'],
			'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/fundraising.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],
			'like_type_id' => 'feed_mini'
		);		
	}	
	
	public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'fundraising\' AND l.item_id = p.campaign_id AND l.user_id = ' . Phpfox::getUserId());
		}
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id');
		}	
		
		$aRow = $this->database()->select('p.campaign_id, p.title, p.time_stamp,p.server_id, p.image_path, p.total_comment, p.total_like, p.short_description_parsed AS description, p.image_path')
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->where('p.campaign_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');

		if(!isset($aRow['campaign_id']))
		{
			return false;
		}

        $sLink = Phpfox::permalink('fundraising', $aRow['campaign_id'], $aRow['title']);

        $aReturn = array_merge(array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('posted_a_fundraising'),
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'feed_icon' => Phpfox::getLib('template')->getStyle('image', 'fundraising.png', 'fundraising'),
            'comment_type_id' => 'fundraising',
            'like_type_id' => 'fundraising',
            'custom_data_cache' => $aRow,
            'load_block' => 'fundraising.feed',
            'type_id' => 'fundraising'
        ), $aItem);


        if (!empty($aRow['image_path'])) {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aRow['image_path'],
                'suffix' => '_600',
                'return_url' => true
            ));

        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/fundraising/static/image/noimage_big.png';
        }

        Phpfox_Component::setPublicParam('custom_param_fundraising_' . $aItem['feed_id'], ['aCampaign' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => $sLink,
            'aCategories' => Phpfox::getService('fundraising.category')->getCategoriesByCampaignId($aRow['campaign_id'])
        ]);

        return $aReturn;
	}

    public function getActivityFeedDonate($aItem, $aCallback = null, $bIsChildItem = false)
    {

        $aDonor = Phpfox::getService('fundraising.user')->getDonorbyId($aItem['item_id']);
        if (!$aDonor) {
            return false;
        }

        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'fundraising\' AND l.item_id = p.campaign_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = p.user_id');
        }

        $aRow = $this->database()->select('p.campaign_id, p.title, p.time_stamp,p.server_id, p.image_path, p.total_comment, p.total_like, p.short_description_parsed AS description, p.image_path')->from(Phpfox::getT('fundraising_campaign'),
                'p')->where('p.campaign_id = ' . (int)$aDonor['campaign_id'])->execute('getSlaveRow');

        if (!isset($aRow['campaign_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink('fundraising', $aRow['campaign_id'], $aRow['title']);

        $aReturn = array_merge(array(
            'feed_title' => $aRow['title'],
            'feed_info' => Phpfox::getLib('parse.output')->clean(_p('donated_to_campaign_title', array('title' => $aRow['title']))),
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'feed_icon' => Phpfox::getLib('template')->getStyle('image', 'fundraising.png', 'fundraising'),
            'comment_type_id' => 'fundraising',
            'like_type_id' => 'fundraising',
            'custom_data_cache' => $aRow,
            'load_block' => 'fundraising.feed',
            'type_id' => 'fundraising'
        ), $aItem);


        if (!empty($aRow['image_path'])) {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aRow['image_path'],
                'suffix' => '_600',
                'return_url' => true
            ));

        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/fundraising/static/image/noimage_big.png';
        }

        Phpfox_Component::setPublicParam('custom_param_fundraising_' . $aItem['feed_id'], ['aCampaign' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => $sLink,
            'aCategories' => Phpfox::getService('fundraising.category')->getCategoriesByCampaignId($aRow['campaign_id'])
        ]);

        return $aReturn;
    }
	
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('campaign_id, title, user_id')
			->from(Phpfox::getT('fundraising_campaign'))
			->where('campaign_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['campaign_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'fundraising\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'fundraising_campaign', 'campaign_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('fundraising', $aRow['campaign_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(_p('full_name_liked_your_fundraising_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(_p('full_name_liked_your_fundraising_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('fundraising_like', $aRow['campaign_id'], $aRow['user_id']);
		}
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		if (empty($aRow['campaign_id'])) {
		    return false;
        }

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_fundraising_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_fundraising_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_fundraising_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'fundraising')
		);	
	}	
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'fundraising\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'fundraising_campaign', 'campaign_id = ' . (int) $iItemId);	
	}
	
	public function spamCheck()
	{
		return array(
			'phrase' => _p('fundraisings'),
			'value' => 0,
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('view' => 'spam'))
		);		
	}
	
	public function getNewsFeed($aRow, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);
		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		 
		$aRow['text'] = _p('owner_full_name_added_a_new_fundraising_a_href_title_link_title_a',
			array(
				'owner_full_name' => $aRow['owner_full_name'], 
				'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']), 
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'title_link' => $aRow['link']				
			)
		);
		
		$aRow['icon'] = Phpfox::getLib('template')->getStyle('image', 'fundraising.png', 'fundraising');
		$aRow['enable_like'] = true;
		$aRow['comment_type_id'] = 'fundraising';

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);
		
		return $aRow;
	}	
	
	public function getCommentNewsFeed($aRow, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{			
			$aRow['text'] = _p('user_added_a_new_comment_on_their_own_fundraising', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']
				)
			);
		}
		elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
		{			
			$aRow['text'] = _p('user_added_a_new_comment_on_your_fundraising', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']	
				)
			);
		}
		else 
		{			
			$aRow['text'] = _p('user_name_added_a_new_comment_on_item_user_name_fundraising', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link'],
					'item_user_name' => $aRow['viewer_full_name'],
					'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
				)
			);
		}
		
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);
		return $aRow;
	}	
	
	public function getTagLinkProfile($aUser)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettaglinkprofile__start')) ? eval($sPlugin) : false);
		return $this->getTagLink();
	}
	
	public function getTagLink()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
		return Phpfox::getLib('url')->makeUrl('fundraising.tag');
	}
	
	public function addTrack($iId, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);
		$this->database()->insert(Phpfox::getT('fundraising_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}	
	
	public function getLatestTrackUsers($iId, $iUserId)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getlatesttrackusers__start')) ? eval($sPlugin) : false);
		$aRows = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('fundraising_track'), 'track')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
			->where('track.item_id = ' . (int) $iId . ' AND track.user_id != ' . (int) $iUserId)
			->order('track.time_stamp DESC')
			->limit(0, 6)
			->execute('getSlaveRows');
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getlatesttrackusers__end')) ? eval($sPlugin) : false);
		return (count($aRows) ? $aRows : false);		
	}

	public function getTagTypeProfile()
	{
		return 'fundraising';
	}
	
	public function getTagType()
	{
		return 'fundraising';
	}
	
	public function getFeedRedirect($iId, $iChild = 0)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
		
		$aFundraising = $this->database()->select('p.campaign_id, p.title')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		if (!isset($aFundraising['campaign_id']))
		{
			return false;
		}					

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
		
		return Phpfox::permalink('fundraising', $aFundraising['campaign_id'], $aFundraising['title']);
	}
	
	public function getAjaxCommentVar()
	{
		return 'fundraising.can_post_comment_on_campaign';
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);
		
		$aFundraising = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, p.title, p.campaign_id, p.privacy, p.privacy_comment')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id']) : null);
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('fundraising_campaign', 'total_comment', 'campaign_id', $aVals['item_id']);
		}
		
		// Send the user an email
		$sLink = Phpfox::permalink('fundraising', $aFundraising['campaign_id'], $aFundraising['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aFundraising['user_id'],
				'item_id' => $aFundraising['campaign_id'],
				'owner_subject' => _p('full_name_commented_on_your_fundraising_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aFundraising['title'])),
				'owner_message' => _p('full_name_commented_on_your_fundraising_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aFundraising['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_fundraising',
				'mass_id' => 'fundraising',
				'mass_subject' => (Phpfox::getUserId() == $aFundraising['user_id'] ? _p('full_name_commented_on_gender_fundraising', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aFundraising['gender'], 1))) : _p('full_name_commented_on_fundraising_full_name_s_fundraising', array('full_name' => Phpfox::getUserBy('full_name'), 'full_name' => $aFundraising['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aFundraising['user_id'] ? _p('full_name_commented_on_gender_fundraising_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aFundraising['gender'], 1), 'link' => $sLink, 'title' => $aFundraising['title'])) : _p('full_name_commented_on_fundraising_full_name_s_fundraising_message', array('full_name' => Phpfox::getUserBy('full_name'), 'fundraising_full_name' => $aFundraising['full_name'], 'link' => $sLink, 'title' => $aFundraising['title'])))
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
	}	
	
	public function updateCommentText($aVals, $sText)
	{
         
	}		
	
	public function getItemName($iId, $sName)
	{
		return _p('a_href_link_on_name_s_fundraising_a', array('link' => Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
	}	
	
	public function getAttachmentField()
	{
		return array('fundraising', 'campaign_id');
	}
	
	public function getProfileLink()
	{
		return 'profile.fundraising';
	}
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('campaign_id AS comment_item_id, privacy_comment, user_id AS comment_user_id, module_id AS parent_module_id')
			->from($this->_sTable)
			->where('campaign_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
	
	public function getRssTitle($iId)
	{
		$aRow = $this->database()->select('title')
			->from($this->_sTable)
			->where('campaign_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		return 'Comments on: ' . $aRow['title'];
	}	
	
	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getCommentItemName()
	{
		return 'fundraising';
	}
	
	public function processCommentModeration($sAction, $iId)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_processcommentmoderation__start')) ? eval($sPlugin) : false);
		// Is this comment approved?
		if ($sAction == 'approve')
		{			
			// Update the fundraising count
			Phpfox::getService('fundraising.process')->updateCounter($iId);
			
			// Get the fundraisings details so we can add it to our news feed
			$aFundraising = $this->database()->select('p.campaign_id, p.user_id, p.title, p.title_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')			
				->from($this->_sTable, 'p')								
				->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'fundraising\' AND c.item_id = p.campaign_id')
				->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')				
				->where('p.campaign_id = ' . (int) $iId)
				->execute('getSlaveRow');
				
			// Add to news feed			
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_fundraising', $aFundraising['campaign_id'], $aFundraising['text_parsed'], $aFundraising['comment_user_id'], $aFundraising['user_id'], $aFundraising['comment_id']) : null);
			
			// Send the user an email
			if (Phpfox::getParam('core.is_personal_site'))
			{
				$sLink = Phpfox::getLib('url')->makeUrl('fundraising', $aFundraising['title_url']);
			}		
			else 
			{
				$sLink = Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'), array('fundraising', $aFundraising['title_url']));
			}
			
			Phpfox::getLib('mail')->to($aFundraising['comment_user_id'])
				->subject(array('comment.full_name_approved_your_comment_on_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'))))
				->message(array('comment.full_name_approved_your_comment_on_site_title_message', array(
							'full_name' => Phpfox::getUserBy('full_name'),
							'site_title' => Phpfox::getParam('core.site_title'),
							'link' => $sLink
						)
					)
				)
				->notification('comment.approve_new_comment')
				->send();							
		}
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_processcommentmoderation__end')) ? eval($sPlugin) : false);
	}
	
	public function getWhatsNew()
	{
		return array(
			'fundraising.fundraisings_title' => array(
				'ajax' => '#fundraising.getNew?id=js_new_item_holder',
				'id' => 'fundraising',
				'block' => 'fundraising.new'
			)
		);
	}

	public function globalSearch($sQuery, $bIsTagSearch = false)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
		$sCondition = 'p.is_approved = 1 AND p.privacy = 1 AND p.status > 1';
		if ($bIsTagSearch == false)
		{
			$sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pt.description_parsed LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
		}		
		
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.campaign_id AND tag.category_id = \'fundraising\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
		}				
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('fundraising_text'), 'pt', 'pt.campaign_id = p.campaign_id')
			->where($sCondition)
			->execute('getSlaveField');		
			
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.campaign_id AND tag.category_id = \'fundraising\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.campaign_id');
		}			
		
		$aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('fundraising_text'), 'pt', 'pt.campaign_id = p.campaign_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where($sCondition)
			->limit(10)
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');
			
		if (count($aRows))
		{
			$aResults = array();
			$aResults['total'] = $iCnt;
			$aResults['menu'] = _p('search_fundraisings');
			
			if ($bIsTagSearch == true)
			{
				$aResults['form'] = '<div><input type="button" value="' . _p('view_more_fundraisings') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('fundraising', array('tag', $sQuery)) . '\';" /></div>';
			}
			else 
			{				
				$aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('fundraising') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('view_more_fundraisings') . '" class="search_button" /></div></form>';
			}
			
			foreach ($aRows as $iKey => $aRow)
			{
				$aResults['results'][$iKey] = array(				
					'title' => $aRow['title'],	
					'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('fundraising', $aRow['title_url'])),
					'image' => Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aRow['server_id'],
							'title' => $aRow['full_name'],
							'path' => 'core.url_user',
							'file' => $aRow['user_image'],
							'suffix' => '_75',
							'max_width' => 75,
							'max_height' => 75
						)
					),
					'extra_info' => _p('fundraising_created_on_time_stamp_by_full_name', array(
							'link' => Phpfox::getLib('url')->makeUrl('fundraising'),
							'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
							'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
							'full_name' => $aRow['full_name']	
						)
					)			
				);
			}
			(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
			return $aResults;
		}
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
	}

	public function deleteComment($iId)
	{
		$this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'campaign_id = ' . (int) $iId);
	}
	
	public function verifyFavorite($iItemId)
	{
		$aItem = $this->database()->select('i.campaign_id')
			->from($this->_sTable, 'i')
			->where('i.campaign_id = ' . (int) $iItemId . ' AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.status > 1')
			->execute('getSlaveRow');
			
		if (!isset($aItem['campaign_id']))
		{
			return false;
		}

		return true;
	}

	public function getFavorite($aFavorites)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getfavorite__start')) ? eval($sPlugin) : false);
		$aItems = $this->database()->select('i.title, i.time_stamp, i.title_url, ' . Phpfox::getUserField())
			->from($this->_sTable, 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
			->where('i.campaign_id IN(' . implode(',', $aFavorites) . ') AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.status > 1')
			->execute('getSlaveRows');
			
		foreach ($aItems as $iKey => $aItem)
		{
			$aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aItem['server_id'],
					'path' => 'core.url_user',
					'file' => $aItem['user_image'],
					'suffix' => '_75',
					'max_width' => 75,
					'max_height' => 75
				)
			);		
			
			if (Phpfox::getParam('core.is_personal_site'))
			{
				$aItems[$iKey]['link'] = Phpfox::getLib('url')->makeUrl('fundraising', $aItem['title_url']);
			}		
			else 
			{
				$aItems[$iKey]['link'] = Phpfox::getService('user')->getLink($aItem['user_id'], $aItem['user_name'], array('fundraising', $aItem['title_url']));
			}			
		}
	    
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getfavorite__return')) ? eval($sPlugin) : false);
		return array(
			'title' => _p('search_fundraisings'),
			'items' => $aItems
		);
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_getfavorite__end')) ? eval($sPlugin) : false);
	}
	
	public function getDashboardLinks()
	{
		return array(
			'submit' => array(
				'phrase' => _p('create_a_fundraising'),
				'link' => 'fundraising.add',
				'image' => 'misc/page_white_add.png'
			),
			'edit' => array(
				'phrase' => _p('manage_fundraisings'),
				'link' => 'profile.fundraising',
				'image' => 'misc/page_white_edit.png'
			)
		);
	}
	
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			_p('fundraisings') => $aUser['activity_fundraising']
		);
	}

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
		// get all the fundraisings by this user
		$aFundraisings = $this->database()
			->select('campaign_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
               
		foreach ($aFundraisings as $aFundraising)
		{
                   
                    $this->database()->delete(Phpfox::getT('fundraising_campaign_category'), 'campaign_id = ' . $aFundraising['campaign_id'] );
                     
			Phpfox::getService('fundraising.process')->delete($aFundraising['campaign_id']);
                       
		}
		// delete this user's categories
		/*$aCats = $this->database()
			->select('category_id')
			->from(Phpfox::getT('fundraising_category'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		$sCats = '1=2';
		foreach ($aCats as $aCat)
		{
			$sCats .= ' OR category_id = ' . $aCat['category_id'];
		}*/
		//$this->database()->delete(Phpfox::getT('fundraising_category'), $sCats);
		//$this->database()->delete(Phpfox::getT('fundraising_category_data'), $sCats);

		// delete the tracks
		//$this->database()->delete(Phpfox::getT('fundraising_track'), 'user_id = ' . $iUser );
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
	}
	
	public function getItemView()
	{
		if (Phpfox::getLib('request')->get('req3') != '')
		{
			return true;
		}
	}	
	
	public function getNotificationFeedApproved($aRow)
	{
		return array(
			'message' => _p('your_fundraising_fundraising_title_has_been_approved', array('fundraising_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id']))		);		
	}
	
      public function getFundraisingDetails($aItem)
	{		
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'breadcrumb_title' => _p('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
                  'module' => 'pages',
                  'item' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_pages' => $sLink . 'fundraising/',
			'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}
      
	public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['req2']))
		{
			switch ($aRequest['req2'])
			{
				case 'view':
					if (isset($aRequest['id']))
					{				
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
							'url_field' => 'title_url',
								'table' => 'fundraising',
								'field' => 'upgrade_campaign_id',
								'id' => $aRequest['id']
							)
						);
						
						if ($aItem !== false)
						{
							return array($aItem['user_name'], array('fundraising', $aItem['title_url']));
						}											
					}
					break;
				default:
					return 'fundraising';
					break;
			}
		}
		
		return false;
	}
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['campaign_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = _p('users_commented_on_gender_fundraising_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_fundraising_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'fundraising')
		);
	}
	
	public function getCommentNotificationFeed($aRow)
	{
		return array(
			'message' => _p('full_name_wrote_a_comment_on_your_fundraising_fundraising_title', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $aRow['full_name'],
					'fundraising_link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id'])),
					'fundraising_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')	
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}
	
	public function getCommentNotificationTag($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, u.user_name, u.full_name')
					->from(Phpfox::getT('comment'), 'c')
					->join(Phpfox::getT('fundraising_campaign'), 'p', 'p.campaign_id = c.item_id')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.comment_id = ' . (int)$aNotification['item_id'])
					->execute('getSlaveRow');
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('full_name_tagged_you_in_a_comment_in_a_fundraising', array('full_name' => $sUsers));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']) . 'comment_' .$aNotification['item_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function reparserList()
	{
		return array(
			'name' => _p('fundraisings_text'),
			'table' => 'fundraising_text',
			'original' => 'description',
			'parsed' => 'description_parsed',
			'item_field' => 'campaign_id'
		);
	}
	
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => _p('statistic_campaigns'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('fundraising_campaign'))
				->where('status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') . ' AND (time_stamp IS NULL OR time_stamp >= ' . $iToday .' )')
				->execute('getSlaveField')
		);
	}	
	
	public function checkFeedShareLink()
	{
		if (!Phpfox::getUserParam('fundraising.add_new_campaign'))
		{
			return false;
		}
	}
	
	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
	
	public function getNewsFeedFeedLike($aRow)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_a_href_link_fundraising_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_fundraising_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
		return $aRow;				
	}		

	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => _p('a_href_user_link_full_name_a_likes_your_a_href_link_fundraising_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function sendLikeEmail($iItemId, $aFeed)
	{		
		return _p('a_href_user_link_full_name_a_likes_your_a_href_link_fundraising_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $iItemId))
				)
			);
	}			
	
	public function updateCounterList()
	{
		$aList = array();	

		$aList[] =	array(
			'name' => _p('users_fundraising_count'),
			'id' => 'fundraising-total'
		);	
		
		$aList[] =	array(
			'name' => _p('update_tags_fundraisings'),
			'id' => 'fundraising-tag-update'
		);			

		$aList[] =	array(
			'name' => _p('update_users_activity_fundraising_points'),
			'id' => 'fundraising-activity'
		);			
		
		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);
		
		if ($iId == 'fundraising-total')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');		
			
			$aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(p.campaign_id) AS total_items')
				->from(Phpfox::getT('user'), 'u')
				->leftJoin(Phpfox::getT('fundraising_campaign'), 'p', 'p.user_id = u.user_id AND p.is_approved = 1 AND p.status > 0 AND p.module_id = \'fundraising\'')
				->limit($iPage, $iPageLimit, $iCnt)
				->group('u.user_id')
				->execute('getSlaveRows');		
				
			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_field'), array('total_fundraising' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
			}
		}
		elseif ($iId == 'fundraising-activity')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user_activity'))
				->execute('getSlaveField');			
					
			$aRows = $this->database()->select('m.user_id, m.activity_fundraising, m.activity_points, m.activity_total, COUNT(oc.campaign_id) AS total_items')
				->from(Phpfox::getT('user_activity'), 'm')
				->leftJoin(Phpfox::getT('fundraising_campaign'), 'oc', 'oc.user_id = m.user_id')
				->group('m.user_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');				
			
			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_activity'), array(
					'activity_points' => (($aRow['activity_total'] - ($aRow['activity_points'] * Phpfox::getUserParam('fundraising.points_fundraising'))) + ($aRow['total_items'] * Phpfox::getUserParam('fundraising.points_fundraising'))),
					'activity_total' => (($aRow['activity_total'] - $aRow['activity_fundraising']) + $aRow['total_items']),
					'activity_fundraising' => $aRow['total_items']
				), 'user_id = ' . $aRow['user_id']);
			}
			
			return $iCnt;
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('tag'))
			->where('category_id = \'fundraising\'')
			->execute('getSlaveField');			
				
		$aRows = $this->database()->select('m.tag_id, oc.campaign_id AS tag_item_id')
			->from(Phpfox::getT('tag'), 'm')
			->where('m.category_id = \'page_id\'')
			->leftJoin(Phpfox::getT('fundraising_campaign'), 'oc', 'oc.campaign_id = m.item_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');			
			
		foreach ($aRows as $aRow)
		{
			if (empty($aRow['tag_item_id']))
			{
				$this->database()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
			}
		}
		
		return $iCnt;	
	}

	public function getActivityPointField()
	{
		return array(
			_p('fundraisings') => 'activity_fundraising'
		);
	}	
	
	public function pendingApproval()
	{
		return array(
			'phrase' => _p('statistic_campaigns'),
			'value' => Phpfox::getService('fundraising.campaign')->getTotalPendings(),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('view' => 'pending'))
		);
	}

	public function getSqlTitleField()
	{
		return array(
			array(
				'table' => 'fundraising',
				'field' => 'title'
			),
			array(
				'table' => 'fundraising_category',
				'field' => 'name'
			)
		);
	}

	public function tabHasItems($iUser)
	{
		$iCount = $this->database()->select('COUNT(user_id)')
				->from($this->_sTable)
				->where('user_id = ' . (int)$iUser)
				->execute('getSlaveField');
		return $iCount > 0;
	}

	public function getAjaxProfileController()
	{
		return 'fundraising.index';
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{
			if (!isset($aUser['total_fundraising']))
			{
				return false;
			}

			if (isset($aUser['total_fundraising']) && (int) $aUser['total_fundraising'] === 0)
			{
				return false;
			}
		}
		
		$aSubMenu = array();
				
		$aMenus[] = array(
			'phrase' => _p('fundraisings'),
			'url' => 'profile.fundraising',
			'total' => (int) (isset($aUser['total_fundraising']) ? $aUser['total_fundraising'] : 0),
			'sub_menu' => $aSubMenu,
			'icon' => 'feed/fundraising.png',
			'icon_class' => 'ico ico-money-bag-o'
		);
		
		return $aMenus;
	}
	
	public function getTotalItemCount($iUserId)
	{
		$kq =  array(
			'field' => 'total_fundraising',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('fundraising_campaign'))->where('user_id = ' . (int) $iUserId . ' AND is_approved = 1 AND module_id = "fundraising"')->execute('getSlaveField')
		);
		return $kq;
	}
	
	public function getNotificationApproved($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['campaign_id']))
		{
			return false;
		}
		
		$sPhrase = _p('your_fundraising_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'fundraising'),
			'no_profile_image' => true
		);			
	}	

	public function getNotificationSetactive($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['campaign_id']))
		{
			return false;
		}
		
		$sPhrase = _p('your_fundraising_title_has_been_setactive', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'fundraising'),
			// 'no_profile_image' => true
		);			
	}	

	public function getNotificationSetinactive($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['campaign_id']))
		{
			return false;
		}
		
		$sPhrase = _p('your_fundraising_title_has_been_setinactive', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'fundraising'),
			// 'no_profile_image' => true
		);			
	}	
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.campaign_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'fundraising\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('fundraising_campaign'), 'item')
			->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 AND item.privacy = 0 AND item.is_active = 1 AND item.status > 0')
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('fundraising', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('fundraising');
		
        $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => $aRow['item_photo'],
				'path' => 'core.url_pic',
				'suffix' => '_120',
				'max_width' => '120',
				'max_height' => '120'				
			)
		);
        
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('fundraising')
		);
	}
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'fundraising.default_privacy_setting' => array(
				'phrase' => _p('fundraisings')
			)
		);
	}
	
	
	public function getUserCountFieldInvite()
	{
		return 'fundraising_invite';
	}	
	
	
	public function getNotificationNotice_Follower($aNotification, $sItemType = '')
	{

		$aDonorUser = array();

		// notification when a user donate
		if($sItemType == 'donated')
		{
			$aDonorUser =  $this->database()->select('fd.campaign_id, fd.user_id, ' . Phpfox::getUserField())	
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
				->where('fd.donor_id =  ' . (int) $aNotification['item_id'])
				->execute('getSlaveRow');

			$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.full_name')	
				->from(Phpfox::getT('fundraising_campaign'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
				->where('p.campaign_id = ' . (int) $aDonorUser['campaign_id'])
				->execute('getSlaveRow');
		}
		else
		{
			$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.full_name')	
				->from(Phpfox::getT('fundraising_campaign'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
				->where('p.campaign_id = ' . (int) $aNotification['item_id'])
				->execute('getSlaveRow');
		}

			
		if (!isset($aRow['campaign_id']))
		{
			return false;
		}			
			
		$aMessage = Phpfox::getService('fundraising.campaign')->getMessageNotifyingFollowers($sItemType, $aRow, $aDonorUser);
		$sPhrase = $aMessage['subject'];
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}
	public function getNotificationNotice_News($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'news');
	}

	public function getNotificationNotice_Video($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'video');
	}
	
	public function getNotificationNotice_Image($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'image');
	}

	public function getNotificationNotice_Reached($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'reached');
	}

	public function getNotificationNotice_Expired($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'expired');
	}

	public function getNotificationNotice_Closed($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'closed');
	}


	// item id of this notification is donor id
	public function getNotificationNotice_Donated($aNotification)
	{
		return $this->getNotificationNotice_Follower($aNotification, $sItemType = 'donated');
	}
	
	
	public function getNotificationFeedInvite($aRow)
	{		
		return array(
			'message' => _p('full_name_invited_you_to_an_fundraising', array(
				'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
				'full_name' => $aRow['full_name']
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('fundraising', array('redirect' => $aRow['item_id']))
		);
	}
	
	public function getNotificationInvited($aNotification)
	{
		$aRow = $this->database()->select('p.campaign_id, p.title, p.user_id, u.full_name')	
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['campaign_id']))
		{
			return false;
		}			
			
		$sPhrase = _p('users_invited_you_to_the_fundraising_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('fundraising', $aRow['campaign_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

    public function getUploadParams($aParams = null)
    {
        $iRemainImage = $aParams['remain_upload'];
        $iMaxFileSize = Phpfox::getUserParam('fundraising.max_upload_size_fundraising');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.fundraising.dropzoneOnSending',
            'success' => '$Core.fundraising.dropzoneOnSuccess',
            'queuecomplete' => '$Core.fundraising.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('fundraising.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'fundraising' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'fundraising' . PHPFOX_DS,
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'on_remove' => 'fundraising.deleteImage',
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(120, 300, 600)
        ];
    }

    /**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_callback__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>