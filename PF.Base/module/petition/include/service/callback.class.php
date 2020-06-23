<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct()
	{            
		$this->_sTable = Phpfox::getT('petition');
            // if the notification module is disabled we cannot get the length to shorten, so we fallback to _iFallbackLength.
            $this->_iFallbackLength = 50;
	}

	public function mobileMenu()
	{            
		return array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'link' => Phpfox::getLib('url')->makeUrl('petition'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_custom-fields.png'))
		);
		}	
	
	/**
	 * Used for the function core.callback::getRedirection
	 * @return <type>
	 */
	public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
	{            
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
		$aPetitions = array();
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('petition'), 'petition')
			->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = petition.petition_id")
			->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id')
			->where($aConds)
			->execute('getSlaveField');	

		if ($iCnt)
		{
			$aRows = $this->database()->select("petition.*, " . (Phpfox::getParam('core.allow_html') ? "petition_text.description_parsed" : "petition_text.description") ." AS description, " . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'petition')
				->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = petition.petition_id")
				->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id')
				->join(Phpfox::getT('user'), 'u', 'petition.user_id = u.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $sLimit, $iCnt)				
				->execute('getSlaveRows');	
						
			if (count($aRows))
			{
				foreach ($aRows as $aRow)
				{
					$aPetitions[$aRow['petition_id']] = $aRow;
				}						
			}
		}		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
		return array($iCnt, $aPetitions);
	}	
	
	public function canShareItemOnFeed(){}
	
	public function getTagSearch($aConds = array(), $sSort)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
		$aRows = $this->database()->select("petition.petition_id AS id")
			->from(Phpfox::getT('petition'), 'petition')
			->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = petition.petition_id")
			->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id')
			->where($aConds)
			->order($sSort)	
			->group('petition.petition_id')
			->execute('getSlaveRows');							
		
		$aSearchIds = array();
		foreach ($aRows as $aRow)
		{
			$aSearchIds[] = $aRow['id'];
		}		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
		return $aSearchIds;		
	}	
	
	public function getTagCloud()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettagcloud__start')) ? eval($sPlugin) : false);
		return array(
			'link' => 'petition',
			'category' => 'petition'
		);
	}
	
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'petition.view_browse_petitions'))
		{
			return null;
		}		
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'petition/',
			'icon' => 'feed/petition.png',
			'landing' => 'petition'
		);
		
		return $aMenus;
	}
	
      public function addPetition($iId)
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
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'petition.share_petitions'))
		{
			return null;
		}		
		
		return array(
			array(
				'phrase' => Phpfox::getPhrase('petition.create_a_petition'),
				'url' => Phpfox::getLib('url')->makeUrl('petition.add', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}	
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['petition.share_petitions'] = Phpfox::getPhrase('petition.who_can_share_petitions');
		$aPerms['petition.view_browse_petitions'] = Phpfox::getPhrase('petition.who_can_view_browse_petitions');
		
		return $aPerms;
	}
	
	public function canViewPageSection($iPage)
	{		
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'petition.view_browse_petitions'))
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
		
		$aItem = $this->database()->select('p.petition_id, p.title, p.time_stamp, p.total_comment, p.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('petition'), 'p', 'c.type_id = \'petition\' AND c.item_id = p.petition_id AND c.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('c.comment_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aItem['petition_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::permalink('petition', $aItem['petition_id'], $aItem['title']);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
		$sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
		$sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);
		
		if ($aRow['user_id'] == $aItem['user_id'])
		{
			$sMessage = Phpfox::getPhrase('petition.posted_a_comment_on_gender_petition_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
		}
		else
		{			
			$sMessage = Phpfox::getPhrase('petition.posted_a_comment_on_user_name_s_petition_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
		}
		
		return array(
			'no_share' => true,
			'feed_info' => $sMessage,
			'feed_link' => $sLink,
			'feed_status' => $aItem['text'],
			'feed_total_like' => $aItem['total_like'],
			'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/petition.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],
			'like_type_id' => 'feed_mini'
		);		
	}	
	
	public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'petition\' AND l.item_id = p.petition_id AND l.user_id = ' . Phpfox::getUserId());
		}
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id');
		}	
		
		$aRow = $this->database()->select('p.petition_id, p.title, p.time_stamp,p.server_id, p.image_path, p.total_comment, p.total_like, pt.short_description_parsed AS description')
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id = p.petition_id')
			->where('p.petition_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');

		if(!isset($aRow['petition_id']))
		{
			return false;
		}
		
		if ($bIsChildItem)
		{
			$aItem = array_merge($aRow, $aItem);
		}	
		
		$aFeed = array(
			'feed_title' => $aRow['title'],                  
			'feed_info' => Phpfox::getPhrase('petition.posted_a_petition'),
			'feed_link' => Phpfox::permalink('petition', $aRow['petition_id'], $aRow['title']),
			'feed_content' => $aRow['description'],
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/petition.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'petition',
			'like_type_id' => 'petition'			
		);
            
            if(!empty($aRow['image_path']))
            {
               $aFeed['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['server_id'],
					'path' => 'core.url_pic',
					'file' => $aRow['image_path'],
					'suffix' => '',
					'class' => 'photo_holder'
				)
			);
            }
            
            return array_merge($aFeed, $aItem);
	}
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('petition_id, title, user_id')
			->from(Phpfox::getT('petition'))
			->where('petition_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['petition_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'petition\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'petition', 'petition_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('petition', $aRow['petition_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('petition.full_name_liked_your_petition_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('petition.full_name_liked_your_petition_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('petition_like', $aRow['petition_id'], $aRow['user_id']);
		}
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('p.petition_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getPhrase('petition.users_liked_gender_own_petition_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getPhrase('petition.users_liked_your_petition_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = Phpfox::getPhrase('petition.users_liked_span_class_drop_data_user_row_full_name_s_span_petition_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('petition', $aRow['petition_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'petition')
		);	
	}	
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'petition\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'petition', 'petition_id = ' . (int) $iItemId);	
	}
	
	public function spamCheck()
	{
		return array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'value' => 0,
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('view' => 'spam'))
		);		
	}
	
	public function getNewsFeed($aRow, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);
		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		 
		$aRow['text'] = Phpfox::getPhrase('petition.owner_full_name_added_a_new_petition_a_href_title_link_title_a',
			array(
				'owner_full_name' => $aRow['owner_full_name'], 
				'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']), 
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'title_link' => $aRow['link']				
			)
		);
		
		$aRow['icon'] = Phpfox::getLib('template')->getStyle('image', 'petition.png', 'petition');
		$aRow['enable_like'] = true;
		$aRow['comment_type_id'] = 'petition';

		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);
		
		return $aRow;
	}	
	
	public function getCommentNewsFeed($aRow, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{			
			$aRow['text'] = Phpfox::getPhrase('petition.user_added_a_new_comment_on_their_own_petition', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']
				)
			);
		}
		elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
		{			
			$aRow['text'] = Phpfox::getPhrase('petition.user_added_a_new_comment_on_your_petition', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']	
				)
			);
		}
		else 
		{			
			$aRow['text'] = Phpfox::getPhrase('petition.user_name_added_a_new_comment_on_item_user_name_petition', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link'],
					'item_user_name' => $aRow['viewer_full_name'],
					'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
				)
			);
		}
		
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);
		return $aRow;
	}	
	
	public function getTagLinkProfile($aUser)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettaglinkprofile__start')) ? eval($sPlugin) : false);
		return $this->getTagLink();
	}
	
	public function getTagLink()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
		return Phpfox::getLib('url')->makeUrl('petition.tag');
	}
	
	public function addTrack($iId, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);
		$this->database()->insert(Phpfox::getT('petition_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}	
	
	public function getLatestTrackUsers($iId, $iUserId)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getlatesttrackusers__start')) ? eval($sPlugin) : false);
		$aRows = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('petition_track'), 'track')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
			->where('track.item_id = ' . (int) $iId . ' AND track.user_id != ' . (int) $iUserId)
			->order('track.time_stamp DESC')
			->limit(0, 6)
			->execute('getSlaveRows');
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getlatesttrackusers__end')) ? eval($sPlugin) : false);
		return (count($aRows) ? $aRows : false);		
	}

	public function getTagTypeProfile()
	{
		return 'petition';
	}
	
	public function getTagType()
	{
		return 'petition';
	}
	
	public function getFeedRedirect($iId, $iChild = 0)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
		
		$aPetition = $this->database()->select('p.petition_id, p.title')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		if (!isset($aPetition['petition_id']))
		{
			return false;
		}					

		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
		
		return Phpfox::permalink('petition', $aPetition['petition_id'], $aPetition['title']);
	}
	
	public function getAjaxCommentVar()
	{
		return 'petition.can_post_comment_on_petition';
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);
		
		$aPetition = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, p.title, p.petition_id, p.privacy, p.privacy_comment')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id']) : null);
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('petition', 'total_comment', 'petition_id', $aVals['item_id']);
		}
		
		// Send the user an email
		$sLink = Phpfox::permalink('petition', $aPetition['petition_id'], $aPetition['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aPetition['user_id'],
				'item_id' => $aPetition['petition_id'],
				'owner_subject' => Phpfox::getPhrase('petition.full_name_commented_on_your_petition_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPetition['title'])),
				'owner_message' => Phpfox::getPhrase('petition.full_name_commented_on_your_petition_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aPetition['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_petition',
				'mass_id' => 'petition',
				'mass_subject' => (Phpfox::getUserId() == $aPetition['user_id'] ? Phpfox::getPhrase('petition.full_name_commented_on_gender_petition', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aPetition['gender'], 1))) : Phpfox::getPhrase('petition.full_name_commented_on_petition_full_name_s_petition', array('full_name' => Phpfox::getUserBy('full_name'), 'full_name' => $aPetition['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aPetition['user_id'] ? Phpfox::getPhrase('petition.full_name_commented_on_gender_petition_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aPetition['gender'], 1), 'link' => $sLink, 'title' => $aPetition['title'])) : Phpfox::getPhrase('petition.full_name_commented_on_petition_full_name_s_petition_message', array('full_name' => Phpfox::getUserBy('full_name'), 'petition_full_name' => $aPetition['full_name'], 'link' => $sLink, 'title' => $aPetition['title'])))
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
	}	
	
	public function updateCommentText($aVals, $sText)
	{
         
	}		
	
	public function getItemName($iId, $sName)
	{
		return Phpfox::getPhrase('petition.a_href_link_on_name_s_petition_a', array('link' => Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
	}	
	
	public function getAttachmentField()
	{
		return array('petition', 'petition_id');
	}
	
	public function getProfileLink()
	{
		return 'profile.petition';
	}
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('petition_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('petition_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(Phpfox::getPhrase('petition.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
	
	public function getRssTitle($iId)
	{
		$aRow = $this->database()->select('title')
			->from($this->_sTable)
			->where('petition_id = ' . (int) $iId)
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
		return 'petition';
	}
	
	public function processCommentModeration($sAction, $iId)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_processcommentmoderation__start')) ? eval($sPlugin) : false);
		// Is this comment approved?
		if ($sAction == 'approve')
		{			
			// Update the petition count
			Phpfox::getService('petition.process')->updateCounter($iId);
			
			// Get the petitions details so we can add it to our news feed
			$aPetition = $this->database()->select('p.petition_id, p.user_id, p.title, p.title_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')			
				->from($this->_sTable, 'p')								
				->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'petition\' AND c.item_id = p.petition_id')
				->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')				
				->where('p.petition_id = ' . (int) $iId)
				->execute('getSlaveRow');
				
			// Add to news feed			
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_petition', $aPetition['petition_id'], $aPetition['text_parsed'], $aPetition['comment_user_id'], $aPetition['user_id'], $aPetition['comment_id']) : null);
			
			// Send the user an email
			if (Phpfox::getParam('core.is_personal_site'))
			{
				$sLink = Phpfox::getLib('url')->makeUrl('petition', $aPetition['title_url']);
			}		
			else 
			{
				$sLink = Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'), array('petition', $aPetition['title_url']));
			}
			
			Phpfox::getLib('mail')->to($aPetition['comment_user_id'])
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
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_processcommentmoderation__end')) ? eval($sPlugin) : false);
	}
	
	public function getWhatsNew()
	{
		return array(
			'petition.petitions_title' => array(
				'ajax' => '#petition.getNew?id=js_new_item_holder',
				'id' => 'petition',
				'block' => 'petition.new'
			)
		);
	}

	public function globalSearch($sQuery, $bIsTagSearch = false)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
		$sCondition = 'p.is_approved = 1 AND p.privacy = 1 AND p.petition_status > 1';
		if ($bIsTagSearch == false)
		{
			$sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pt.description_parsed LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
		}		
		
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.petition_id AND tag.category_id = \'petition\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
		}				
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id = p.petition_id')
			->where($sCondition)
			->execute('getSlaveField');		
			
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.petition_id AND tag.category_id = \'petition\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.petition_id');
		}			
		
		$aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id = p.petition_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where($sCondition)
			->limit(10)
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');
			
		if (count($aRows))
		{
			$aResults = array();
			$aResults['total'] = $iCnt;
			$aResults['menu'] = Phpfox::getPhrase('petition.search_petitions');
			
			if ($bIsTagSearch == true)
			{
				$aResults['form'] = '<div><button type="button" value="" class="search_button btn btn-success btn-sm" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('petition', array('tag', $sQuery)) . '\';">' . Phpfox::getPhrase('petition.view_more_petitions') . '</button></div>';
			}
			else 
			{				
				$aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('petition') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . Phpfox::getPhrase('petition.view_more_petitions') . '" class="search_button" /></div></form>';
			}
			
			foreach ($aRows as $iKey => $aRow)
			{
				$aResults['results'][$iKey] = array(				
					'title' => $aRow['title'],	
					'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('petition', $aRow['title_url'])),
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
					'extra_info' => Phpfox::getPhrase('petition.petition_created_on_time_stamp_by_full_name', array(
							'link' => Phpfox::getLib('url')->makeUrl('petition'),
							'time_stamp' => Phpfox::getTime(Phpfox::getParam('petition.petition_time_stamp'), $aRow['time_stamp']),
							'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
							'full_name' => $aRow['full_name']	
						)
					)			
				);
			}
			(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
			return $aResults;
		}
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
	}

	public function deleteComment($iId)
	{
		$this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'petition_id = ' . (int) $iId);
	}
	
	public function verifyFavorite($iItemId)
	{
		$aItem = $this->database()->select('i.petition_id')
			->from($this->_sTable, 'i')
			->where('i.petition_id = ' . (int) $iItemId . ' AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.petition_status > 1')
			->execute('getSlaveRow');
			
		if (!isset($aItem['petition_id']))
		{
			return false;
		}

		return true;
	}

	public function getFavorite($aFavorites)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getfavorite__start')) ? eval($sPlugin) : false);
		$aItems = $this->database()->select('i.title, i.time_stamp, i.title_url, ' . Phpfox::getUserField())
			->from($this->_sTable, 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
			->where('i.petition_id IN(' . implode(',', $aFavorites) . ') AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.petition_status > 1')
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
				$aItems[$iKey]['link'] = Phpfox::getLib('url')->makeUrl('petition', $aItem['title_url']);
			}		
			else 
			{
				$aItems[$iKey]['link'] = Phpfox::getService('user')->getLink($aItem['user_id'], $aItem['user_name'], array('petition', $aItem['title_url']));
			}			
		}
	    
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getfavorite__return')) ? eval($sPlugin) : false);
		return array(
			'title' => Phpfox::getPhrase('petition.search_petitions'),
			'items' => $aItems
		);
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_getfavorite__end')) ? eval($sPlugin) : false);
	}
	
	public function getDashboardLinks()
	{
		return array(
			'submit' => array(
				'phrase' => Phpfox::getPhrase('petition.create_a_petition'),
				'link' => 'petition.add',
				'image' => 'misc/page_white_add.png'
			),
			'edit' => array(
				'phrase' => Phpfox::getPhrase('petition.manage_petitions'),
				'link' => 'profile.petition',
				'image' => 'misc/page_white_edit.png'
			)
		);
	}
	
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('petition.petitions') => $aUser['activity_petition']
		);
	}

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
		// get all the petitions by this user
		$aPetitions = $this->database()
			->select('petition_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aPetitions as $aPetition)
		{
			Phpfox::getService('petition.process')->delete($aPetition['petition_id']);
		}
		// delete this user's categories
		$aCats = $this->database()
			->select('category_id')
			->from(Phpfox::getT('petition_category'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		$sCats = '1=2';
		foreach ($aCats as $aCat)
		{
			$sCats .= ' OR category_id = ' . $aCat['category_id'];
		}
		$this->database()->delete(Phpfox::getT('petition_category'), $sCats);
		$this->database()->delete(Phpfox::getT('petition_category_data'), $sCats);

		// delete the tracks
		$this->database()->delete(Phpfox::getT('petition_track'), 'user_id = ' . $iUser );
		
		// delete petition
		$this->database()->delete(Phpfox::getT('petition'), 'user_id = ' . $iUser );
		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
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
			'message' => Phpfox::getPhrase('petition.your_petition_petition_title_has_been_approved', array('petition_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id']))		);		
	}
	
      public function getPetitionDetails($aItem)
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
			'breadcrumb_title' => Phpfox::getPhrase('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
                  'module' => 'pages',
                  'item' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_pages' => $sLink . 'petition/',
			'theater_mode' => Phpfox::getPhrase('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
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
								'table' => 'petition',
								'field' => 'upgrade_petition_id',
								'id' => $aRequest['id']
							)
						);
						
						if ($aItem !== false)
						{
							return array($aItem['user_name'], array('petition', $aItem['title_url']));
						}											
					}
					break;
				default:
					return 'petition';
					break;
			}
		}
		
		return false;
	}
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('p.petition_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['petition_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getPhrase('petition.users_commented_on_gender_petition_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getPhrase('petition.users_commented_on_your_petition_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = Phpfox::getPhrase('petition.users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('petition', $aRow['petition_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'petition')
		);
	}
	
	public function getCommentNotificationFeed($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('petition.full_name_wrote_a_comment_on_your_petition_petition_title', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $aRow['full_name'],
					'petition_link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id'])),
					'petition_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')	
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}
	
	public function getCommentNotificationTag($aNotification)
	{
		$aRow = $this->database()->select('p.petition_id, p.title, u.user_name, u.full_name')
					->from(Phpfox::getT('comment'), 'c')
					->join(Phpfox::getT('petition'), 'p', 'p.petition_id = c.item_id')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.comment_id = ' . (int)$aNotification['item_id'])
					->execute('getSlaveRow');
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = Phpfox::getPhrase('petition.full_name_tagged_you_in_a_comment_in_a_petition', array('full_name' => $sUsers));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('petition', $aRow['petition_id'], $aRow['title']) . 'comment_' .$aNotification['item_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function reparserList()
	{
		return array(
			'name' => Phpfox::getPhrase('petition.petitions_text'),
			'table' => 'petition_text',
			'original' => 'description',
			'parsed' => 'description_parsed',
			'item_field' => 'petition_id'
		);
	}
	
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('petition'))
				->where('petition_status > 1 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}	
	
	public function checkFeedShareLink()
	{
		if (!Phpfox::getUserParam('petition.add_new_petition'))
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
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('petition.a_href_user_link_full_name_a_likes_their_own_a_href_link_petition_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('petition.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_petition_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
		return $aRow;				
	}		

	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('petition.a_href_user_link_full_name_a_likes_your_a_href_link_petition_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function sendLikeEmail($iItemId, $aFeed)
	{		
		return Phpfox::getPhrase('petition.a_href_user_link_full_name_a_likes_your_a_href_link_petition_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $iItemId))
				)
			);
	}			
	
	public function updateCounterList()
	{
		$aList = array();	

		$aList[] =	array(
			'name' => Phpfox::getPhrase('petition.users_petition_count'),
			'id' => 'petition-total'
		);	
		
		$aList[] =	array(
			'name' => Phpfox::getPhrase('petition.update_tags_petitions'),
			'id' => 'petition-tag-update'
		);			

		$aList[] =	array(
			'name' => Phpfox::getPhrase('petition.update_users_activity_petition_points'),
			'id' => 'petition-activity'
		);			
		
		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);
		
		if ($iId == 'petition-total')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');		
			
			$aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(p.petition_id) AS total_items')
				->from(Phpfox::getT('user'), 'u')
				->leftJoin(Phpfox::getT('petition'), 'p', 'p.user_id = u.user_id AND p.is_approved = 1 AND p.petition_status > 0 AND p.module_id = \'petition\'')
				->limit($iPage, $iPageLimit, $iCnt)
				->group('u.user_id')
				->execute('getSlaveRows');		
				
			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_field'), array('total_petition' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
			}
		}
		elseif ($iId == 'petition-activity')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user_activity'))
				->execute('getSlaveField');			
					
			$aRows = $this->database()->select('m.user_id, m.activity_petition, m.activity_points, m.activity_total, COUNT(oc.petition_id) AS total_items')
				->from(Phpfox::getT('user_activity'), 'm')
				->leftJoin(Phpfox::getT('petition'), 'oc', 'oc.user_id = m.user_id')
				->group('m.user_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');				
			
			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_activity'), array(
					'activity_points' => (($aRow['activity_total'] - ($aRow['activity_points'] * Phpfox::getUserParam('petition.points_petition'))) + ($aRow['total_items'] * Phpfox::getUserParam('petition.points_petition'))),
					'activity_total' => (($aRow['activity_total'] - $aRow['activity_petition']) + $aRow['total_items']),
					'activity_petition' => $aRow['total_items']
				), 'user_id = ' . $aRow['user_id']);
			}
			
			return $iCnt;
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('tag'))
			->where('category_id = \'petition\'')
			->execute('getSlaveField');			
				
		$aRows = $this->database()->select('m.tag_id, oc.petition_id AS tag_item_id')
			->from(Phpfox::getT('tag'), 'm')
			->where('m.category_id = \'page_id\'')
			->leftJoin(Phpfox::getT('petition'), 'oc', 'oc.petition_id = m.item_id')
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
			Phpfox::getPhrase('petition.petitions') => 'activity_petition'
		);
	}	
	
	public function pendingApproval()
	{
		return array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'value' => Phpfox::getService('petition')->getPendingTotal(),
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('view' => 'pending'))
		);
	}

	public function getSqlTitleField()
	{
		return array(
			array(
				'table' => 'petition',
				'field' => 'title'
			),
			array(
				'table' => 'petition_category',
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
		return 'petition.index';
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{
			if (!isset($aUser['total_petition']))
			{
				return false;
			}

			if (isset($aUser['total_petition']) && (int) $aUser['total_petition'] === 0)
			{
				return false;
			}
		}
		
		$aSubMenu = array();
				
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('petition.petitions'),
			'url' => 'profile.petition',
			'total' => (int) (isset($aUser['total_petition']) ? $aUser['total_petition'] : 0),
			'sub_menu' => $aSubMenu,
			'icon' => 'feed/petition.png'
		);	
		
		return $aMenus;
	}
	
	public function getTotalItemCount($iUserId)
	{
		$kq =  array(
			'field' => 'total_petition',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('petition'))->where('user_id = ' . (int) $iUserId . ' AND is_approved = 1 AND module_id = "petition"')->execute('getSlaveField')
		);
		return $kq;
	}
	
	public function getNotificationApproved($aNotification)
	{
		$aRow = $this->database()->select('p.petition_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['petition_id']))
		{
			return false;
		}
		
		$sPhrase = Phpfox::getPhrase('petition.your_petition_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('petition', $aRow['petition_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'petition'),
			'no_profile_image' => true
		);			
	}	
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.petition_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'petition\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('petition'), 'item')
			->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 AND item.privacy = 0 AND item.petition_status > 0')
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('petition', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = Phpfox::getPhrase('petition.petition');
		
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
			'name' => Phpfox::getPhrase('petition.petition')
		);
	}
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'petition.default_privacy_setting' => array(
				'phrase' => Phpfox::getPhrase('petition.petitions')								
			)
		);
	}
	
	
	public function getUserCountFieldInvite()
	{
		return 'petition_invite';
	}	
	
	public function getNotificationFeedInvite($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('petition.full_name_invited_you_to_an_petition', array(
				'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
				'full_name' => $aRow['full_name']
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('petition', array('redirect' => $aRow['item_id']))
		);
	}
	
	public function getNotificationInvite($aNotification)
	{
		$aRow = $this->database()->select('p.petition_id, p.title, p.user_id, u.full_name')	
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.petition_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['petition_id']))
		{
			return false;
		}			
			
		$sPhrase = Phpfox::getPhrase('petition.users_invited_you_to_the_petition_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('petition', $aRow['petition_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
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
		if ($sPlugin = Phpfox_Plugin::get('petition.service_callback__call'))
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