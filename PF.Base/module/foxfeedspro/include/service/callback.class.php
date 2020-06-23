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

class FoxFeedsPro_Service_Callback extends Phpfox_Service
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_items');
	}
	
	/*
	 * Callback function for adding a comment on news items
	 */
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
         $aNews = $this->database()->select('ni.*,u.full_name, u.user_id, u.gender, u.user_name')
			->from($this->_sTable, 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
		
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
		}
		
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
        
		$this->database()->updateCounter('ynnews_items','total_comment','item_id', $aVals['item_id']);

			/// Send the user an email
			$sLink = Phpfox::getLib('url')->makeUrl('foxfeedspro.newsdetails', array('item'=>$aNews['item_id']));
			
            Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aNews['user_id'],
                'item_id' => $aNews['item_id'],
                'owner_subject' => _p('foxfeedspro.full_name_commented_on_your_news_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aNews['item_title'])),
                'owner_message' => _p('foxfeedspro.full_name_commented_on_your_news_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aNews['item_title'])),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'comment_foxfeedspro',
                'mass_id' => 'news',
                'mass_subject' => (Phpfox::getUserId() == $aNews['user_id'] ? 
                				   _p('foxfeedspro.full_name_commented_on_gender_news', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aNews['gender'], 1))) : 
                				   _p('foxfeedspro.full_name_commented_on_news_full_name_s_news', array('full_name' => Phpfox::getUserBy('full_name'), 'news_full_name' => $aNews['full_name']))
								   ),
                'mass_message' => (Phpfox::getUserId() == $aNews['user_id'] ? 
                				   _p('foxfeedspro.full_name_commented_on_gender_news_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aNews['gender'], 1), 'link' => $sLink, 'title' => $aNews['item_title'])) : 
                				   _p('foxfeedspro.full_name_commented_on_news_full_name_s_news_message', array('full_name' => Phpfox::getUserBy('full_name'), 'news_full_name' => $aNews['full_name'], 'link' => $sLink, 'title' => $aNews['item_title'])))
           	)
        );
	}
	
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('ynnews_items', 'total_comment', 'item_id', $iId, TRUE);
	}
	
	public function getAjaxCommentVar()
	{
            return 'foxfeedspro.can_post_comments_on_news_items';
	}
   
    public function getCommentItemName()
	{
		return 'foxfeedspro';
	}
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('item_id AS comment_item_id, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('item_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0))
		{
			Phpfox_Error::set(_p('foxfeedspro.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
	
	public function getCommentNewsFeed($aRow, $iUserId = null)
	{
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{			
			$aRow['text'] = _p('foxfeedspro.user_added_a_new_comment_on_their_own_news', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']
				)
			);
		}
		elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
		{			
			$aRow['text'] = _p('foxfeedspro.user_added_a_new_comment_on_your_news', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link']	
				)
			);
		}
		else 
		{			
			$aRow['text'] = _p('foxfeedspro.user_name_added_a_new_comment_on_item_user_name_news', array(
					'user_name' => $aRow['owner_full_name'],
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'title_link' => $aRow['link'],
					'item_user_name' => $aRow['viewer_full_name'],
					'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
				)
			);
		}
		
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		return $aRow;
	}

	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}

	public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
	{
		if (!Phpfox::getUserParam('foxfeedspro.can_view_news'))
		{
			return false;
		}
        if (Phpfox::isModule('pages')) {
            $aFeedPage = $this->database()->select('f.feed_id')
                ->from(Phpfox::getT('feed'), 'f')
                ->join(Phpfox::getT('pages_feed'), 'pf', 'f.item_id = pf.item_id')
                ->where('f.type_id = \'foxfeedspro\' and f.item_id =' . (int)$aItem['item_id'])
                ->execute('getSlaveRow');
        }
        else {
            $aFeedPage = $this->database()->select('f.feed_id')
                ->from(Phpfox::getT('feed'),'f')
                ->where('f.type_id = \'foxfeedspro\' and f.item_id ='.(int)$aItem['item_id'])
                ->execute('getSlaveRow');
        }
		if(count($aFeedPage) && isset($aFeedPage['feed_id']) && $aFeedPage['feed_id'] != $aItem['feed_id']){
			$aItem['feed_id'] = $aFeedPage['feed_id'];
		}		
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')
				->leftJoin(Phpfox::getT('like'), 'l'
					, 'l.type_id = \'foxfeedspro\' AND l.item_id = b.item_id AND l.user_id = ' . Phpfox::getUserId());
		}
		$aRow = $this->database()->select('f.feed_name, f.feed_id as rss_id, b.item_id, b.item_title, b.item_alias, b.added_time, b.total_comment, b.total_like, b.item_description_parse AS text ')
			->from(Phpfox::getT('ynnews_items'), 'b')
			->join(Phpfox::getT('ynnews_feeds'),'f','f.feed_id = b.feed_id')
			->where('b.item_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');	
		$aRow['feed_id'] = $aItem['feed_id'];
		if (!isset($aRow['item_id']))
		{
			return false;
		}

		Phpfox::getParam('foxfeedspro.is_friendly_url');
		$aListNews = "";
		$aCountNews = $this->database()->select("COUNT(*)")
			->from(Phpfox::getT('ynnews_newfeeds'), 'nfeed')
			->join(Phpfox::getT('ynnews_items'), 'n', 'n.item_id = nfeed.item_id')
			->where('nfeed.feed_id = ' . (int) $aItem['feed_id'])
			->order('n.added_time DESC')
			->execute('getField');
		$aCountNews = $aCountNews + 1;
		$aNews = $this->database()
			->select('n.item_id, n.item_title, n.item_description_parse, n.item_alias')
			->from(Phpfox::getT('ynnews_newfeeds'), 'nfeed')
			->join(Phpfox::getT('ynnews_items'), 'n', 'n.item_id = nfeed.item_id')
			->where('nfeed.feed_id = ' . (int) $aItem['feed_id'])
			->limit(2)
			->order('n.added_time DESC')
			->execute('getSlaveRows');
		if(count($aNews) > 0){
			foreach ($aNews as $aNew){
				$aListNews .= '<a class="activity_feed_content_link_title" href="'.Phpfox::permalink('foxfeedspro.newsdetails', 'item_' . $aNew['item_id'], $aNew['item_alias']).'">'.Phpfox::getLib('parse.output')->shorten($aNew['item_title'],100,'...').'</a><p>'.Phpfox::getLib('parse.output')->shorten($aNew['item_description_parse'],200,'...').'</p>';
			}
			$aListNews = '<a class="activity_feed_content_link_title" href="'.Phpfox::permalink('foxfeedspro.newsdetails', 'item_' . $aRow['item_id'], $aRow['item_alias']).'">'.Phpfox::getLib('parse.output')->shorten($aRow['item_title'],100,'...').'</a><p>'.Phpfox::getLib('parse.output')->shorten($aRow['text'],200,'...').'</p>'.$aListNews;
			$aFeeds = array_merge(array(
				'feed_title' => "",
				'feed_info' => _p('foxfeedspro.posted_few_entries',array('total'=>$aCountNews,'rssLink' => Phpfox::permalink('foxfeedspro.feeddetails', 'feed_' . $aRow['rss_id']),'rssTitle' => $aRow['feed_name'])),
				'feed_link' => "",
				'feed_content' => "",
				'total_comment' => $aRow['total_comment'],
				'feed_total_like' => $aRow['total_like'],
				'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
				'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/blog.png', 'return_url' => true)),
				'time_stamp' => $aRow['added_time'],
				'enable_like' => true,
				'comment_type_id' => 'foxfeedspro',
				'like_type_id' => 'foxfeedspro',
				'feed_custom_html' => $aListNews,
				),$aRow);
		}
		else {
			$aFeeds = array_merge(array(
				'feed_title' => $aRow['item_title'],
				'feed_info' => _p('foxfeedspro.posted_a_new_entry'),
				'feed_link' => Phpfox::permalink('foxfeedspro.newsdetails', 'item_' . $aRow['item_id'], $aRow['item_alias']),
				'feed_content' => $aRow['text'],
				'total_comment' => $aRow['total_comment'],
				'feed_total_like' => $aRow['total_like'],
				'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
				'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/blog.png', 'return_url' => true)),
				'time_stamp' => $aRow['added_time'],
				'enable_like' => true,
				'comment_type_id' => 'foxfeedspro',
				'like_type_id' => 'foxfeedspro',
				'custom_data_cache' => $aRow
			), $aRow);
		}
		return $aFeeds;
	}

	public function getActivityFeedComment($aRow)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
		}		
		
		$aItem = $this->database()->select('ni.item_id, ni.item_title, ni.item_pubDate, ni.total_comment, ni.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('ynnews_items'), 'ni', 'c.type_id = \'foxfeedspro\' AND c.item_id = ni.item_id AND c.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('c.comment_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aItem['item_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::permalink('foxfeedspro.newsdetails','item_'.$aItem['item_id'], $aItem['item_title']);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aItem['item_title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
		$sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
		$sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);
		
		if ($aRow['user_id'] == $aItem['user_id'])
		{
			$sMessage = _p('foxfeedspro.posted_a_comment_on_gender_news_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
		}
		else
		{			
			$sMessage = _p('foxfeedspro.posted_a_comment_on_user_name_s_news_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
		}
		
		return array(
			'no_share' => true,
			'feed_info' => $sMessage,
			'feed_link' => $sLink,
			'feed_status' => $aItem['text'],
			'feed_total_like' => $aItem['total_like'],
			'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/blog.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],
			'like_type_id' => 'feed_mini'
		);		
	}

	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('ni.item_id, ni.item_title, ni.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_items'), 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['item_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['item_title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = _p('foxfeedspro.users_commented_on_gender_news_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('foxfeedspro.users_commented_on_your_news_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('foxfeedspro.users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('foxfeedspro.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getCommentNotificationFeed($aRow)
	{
		return array(
			'message' => _p('foxfeedspro.full_name_wrote_a_comment_on_your_news_news_title', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $aRow['full_name'],
					'news_link' => Phpfox::getLib('url')->makeUrl('foxfeedspro', array('redirect' => $aRow['item_id'])),
					'news_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')	
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('foxfeedspro', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}
	
	public function updateCommentText($aVals, $sText)
	{
		
	}	
	
	/*
	 * Callback functions for adding a like on news items
	 */
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('item_id, item_title, user_id')
			->from(Phpfox::getT('ynnews_items'))
			->where('item_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['item_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'foxfeedspro\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynnews_items', 'item_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('foxfeedspro.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('foxfeedspro.full_name_liked_your_news_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['item_title'])))
				->message(array('foxfeedspro.full_name_liked_your_news_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['item_title'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('foxfeedspro_like', $aRow['item_id'], $aRow['user_id']);
		}
	}

	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'foxfeedspro\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynnews_items', 'item_id = ' . (int) $iItemId);	
	}

	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
	
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = _p('foxfeedspro.a_href_user_link_full_name_a_likes_their_own_a_href_link_blog_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = _p('foxfeedspro.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_news_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';
		return $aRow;				
	}

	public function getNotificationFeednotifylike($aRow)
	{		
		return array(
			'message' => _p('foxfeedspro.a_href_user_link_full_name_a_likes_your_a_href_link_news_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('foxfeedspro', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('foxfeedspro', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('ni.item_id, ni.item_title, ni.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_items'), 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['item_title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('foxfeedspro.users_liked_gender_own_news_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('foxfeedspro.users_liked_your_news_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('foxfeedspro.users_liked_span_class_drop_data_user_row_full_name_s_span_news_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('foxfeedspro.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function getFeedRedirect($iId, $iChild = 0)
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
		
		$aNews = $this->database()->select('ni.item_id, ni.item_title')
			->from($this->_sTable, 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		if (!isset($aNews['item_id']))
		{
			return false;
		}					

		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
		
		return Phpfox::permalink('foxfeedspro.newsdetails', 'item_'.$aNews['item_id'], $aNews['item_title']);
	}
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getNotificationFeedapproved($aNotification)
	{
		$aRow = $this->database()->select('nf.feed_id, nf.feed_name, nf.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_feeds'), 'nf')
			->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = nf.user_id')
			->where('nf.feed_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_id'])) {
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['feed_name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('foxfeedspro.admin_approved_your_rss_link', array('feed_name' => $sTitle));
	
		return array(
			'link' => Phpfox::getLib('url') -> makeUrl('foxfeedspro.feeds'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}
	
	public function getNotificationFeeddeclined($aNotification)
	{
		$aRow = $this->database()->select('nf.feed_id, nf.feed_name, nf.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_feeds'), 'nf')
			->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = nf.user_id')
			->where('nf.feed_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_id'])) {
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['feed_name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('foxfeedspro.admin_declined_your_rss_link', array('feed_name' => $sTitle));
	
		return array(
			'link' => Phpfox::getLib('url') -> makeUrl('foxfeedspro.feeds'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}
	
	public function getNotificationFeedupdated($aNotification)
	{
		$aRow = $this->database()->select('nf.feed_id, nf.feed_name, nf.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_feeds'), 'nf')
			->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = nf.user_id')
			->where('nf.feed_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_id'])) {
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['feed_name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('foxfeedspro.some_latest_news_had_been_updated_on_the_feed_feed_name', array('feed_name' => $sTitle));
	
		return array(
			'link' => Phpfox::getLib('url') -> permalink('foxfeedspro.feeddetails', "feed_{$aRow['feed_id']}", $aRow['feed_name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function getNotificationNewsapproved($aNotification)
	{
		$aRow = $this->database()->select('ni.item_id, ni.item_title, ni.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_items'), 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['item_id'])) {
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['item_title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('foxfeedspro.admin_approved_your_news', array('news_title' => $sTitle));
	
		return array(
			'link' => Phpfox::getLib('url') -> makeUrl('foxfeedspro.news'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}
	
	public function getNotificationNewsdeclined($aNotification)
	{
		$aRow = $this->database()->select('ni.item_id, ni.item_title, ni.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('ynnews_items'), 'ni')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')
			->where('ni.item_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['item_id'])) {
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['item_title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('foxfeedspro.admin_declined_your_news', array('news_title' => $sTitle));
	
		return array(
			'link' => Phpfox::getLib('url') -> makeUrl('foxfeedspro.news'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function canShareItemOnFeed()
	{
		
	}

	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.item_id AS item_id, item.item_title AS item_title, item.item_pubDate AS item_time_stamp, item.user_id AS item_user_id, \'foxfeedspro\' AS item_type_id, item.item_image AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('ynnews_items'), 'item')
			->where('item.is_active = 1 AND item.is_approved = 1 AND ' . $this->database()->searchKeywords('item.item_title', $sSearch))
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('news.newsdetails', 'item_'.$aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('foxfeedspro.news');

        if ($aRow['item_photo']) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => str_replace(Phpfox::getParam('core.path') . 'file/pic/', '', $aRow['item_photo']),
                    'path' => 'core.url_pic',
                    'suffix' => '',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        else {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => 0,
                    'file' => 'foxfeedspro/static/image/default.png',
                    'path' => 'core.url_module',
                    'suffix' => '',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
		
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('foxfeedspro.news')
		);
	}

	// ----------------------------- Profile 
	public function getProfileMenu($aUser)
	{
		if($aUser['user_id'] == Phpfox::getUserId()
			&& (!Phpfox::getUserParam('foxfeedspro.can_add_rss_provider'))
		){
			return null;
		}
		$aSubMenu = array();
		
		if ($aUser['user_id'] == Phpfox::getUserId() && $this->request()->get('req2') == 'news')
		{
			if(Phpfox::getUserParam('foxfeedspro.can_add_rss_provider')
			){
				// show Add, Manage menu
				$aSubMenu[] = array(
					'phrase' => _p('foxfeedspro.add_rss_provider_uppercase'),
					'url' => Phpfox::getLib('url')->makeUrl('profile.foxfeedspro.profileaddrssprovider'),
					// 'total' => Phpfox::getService('blog')->getTotalDrafts($aUser['user_id'])
				);
				$aSubMenu[] = array(
					'phrase' => _p('foxfeedspro.manage_rss_provider'),
					'url' => Phpfox::getLib('url')->makeUrl('profile.foxfeedspro.profilemanagerssprovider'),
					// 'total' => Phpfox::getService('blog')->getTotalDrafts($aUser['user_id'])
				);
			}
		} else if($this->request()->get('req2') == 'news' || $this->request()->get('req2') == 'foxfeedspro') {
			// show Manage menu (not having action edit)
			$aSubMenu[] = array(
				'phrase' => _p('foxfeedspro.manage_rss_provider'),
				'url' => Phpfox::getLib('url')->makeUrl('profile.foxfeedspro.profilemanagerssprovider', array($aUser['user_name'])),
				// 'total' => Phpfox::getService('blog')->getTotalDrafts($aUser['user_id'])
			);
		}
		
		$aMenus[] = array(
			'phrase' => _p('foxfeedspro.rss'),
			'url' => 'profile.foxfeedspro.profileviewrss',
			// 'total' => (int) (isset($aUser['total_blog']) ? $aUser['total_blog'] : 0),
			'sub_menu' => $aSubMenu,
			'icon' => 'feed/foxfeedspro.png',
            'icon_class' => 'ico ico-newspaper-o'
		);	
		
		return $aMenus;	
	}

    public function getProfileLink()
    {
        return 'profile.foxfeedspro';
    }

    public function getAjaxProfileController()
    {
        return 'foxfeedspro.index';
    }

    public function hideBlockProfile($sType)
    {
        return array(
            'table' => ($sType == 'profile' ? 'user_design_order' : '')
        );
    }

    public function getBlockDetailsProfile()
    {
        return array(
            'title' => 'RSS'
        );
    }

	// ----------------------------- Pages
    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'foxfeedspro.can_view_news'))
        {
            return false;
        }

        return true;
    }

    public function getPagePerms()
    {
        $aPerms = array();

        // $aPerms['videochannel.share_videos'] = _p('videochannel.who_can_share_videos');
        // $aPerms['videochannel.view_browse_videos'] = _p('videochannel.who_can_view_browse_videos');
        //$aPerms['videochannel.add_channels'] = _p('videochannel.who_can_add_channels');

        return $aPerms;
    }

    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'foxfeedspro.can_view_news'))
        {
            return null;
        }
		if($aPage['user_id'] == Phpfox::getUserId()
			&& (!Phpfox::getUserParam('foxfeedspro.can_add_rss_provider'))
		){
			return null;
		}

        $aSubMenu = array();

        if ($this->request()->get('req3') == 'news' 
        	|| $this->request()->get('req2') == 'news'
        	|| $this->request()->get('req3') == 'foxfeedspro'
        	|| $this->request()->get('req2') == 'foxfeedspro'
    	)
        {
        	if (Phpfox::getUserId() == $aPage['user_id'] 
        		&& Phpfox::getUserParam('foxfeedspro.can_add_rss_provider')
    		)
        	{
        		// show Add, Manage menu
	            $aSubMenu[] = array(
	                'phrase' => _p('foxfeedspro.add_rss_provider_uppercase'),
	                'url' => 'go_profileaddrssprovider/'
	            );
	            $aSubMenu[] = array(
	                'phrase' => _p('foxfeedspro.manage_rss_provider'),
	                'url' => 'go_profilemanagerssprovider/'
	            );

        	} else {
        		// show Manage menu (not having action edit)
	            $aSubMenu[] = array(
	                'phrase' => _p('foxfeedspro.manage_rss_provider'),
	                'url' => 'go_profilemanagerssprovider/'
	            );
        	}
        }

        $aMenus[] = array(
            'phrase' => _p('foxfeedspro.rss'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'foxfeedspro/profileviewrss/',
            'icon' => 'feed/foxfeedspro.png',
            'landing' => 'foxfeedspro',
            'menu_icon' => 'ico ico-newspaper-o',
            'sub_menu' => $aSubMenu
        );

        return $aMenus;
    }    

	public function getPageSubMenu($aPage)
    {
        return array();
    }    

	public function getTagLinkNews()
	{
		$sExtra = '';
		if (defined('PHPFOX_TAG_PARENT_MODULE'))
		{
			$sExtra .= PHPFOX_TAG_PARENT_MODULE . '.' . PHPFOX_TAG_PARENT_ID . '.';
		}
		
		return Phpfox::getLib('url')->makeUrl($sExtra . 'foxfeedspro.tag');
	}

	public function getTagTypeNews()
	{
		return 'foxfeedspro_news';
	}

	public function getTagCloudNews()
	{
		return array(
			'link' => 'foxfeedspro',
			'category' => 'foxfeedspro_news'
		);
	}

    public function getUploadParams() {
        return [
            'label' => _p('upload_news_thumbnail_image'),
            'max_size' => null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'foxfeedspro' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'foxfeedspro' . PHPFOX_DS,
//            'thumbnail_sizes' => array(120, 250, 500, 1024),
            'remove_field_name' => 'remove_logo'
        ];
    }

    public function getUploadParamsLogo() {
        return [
            'label' => _p('or_upload_logo_from_your_computer'),
            'max_size' => null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'foxfeedspro' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'foxfeedspro' . PHPFOX_DS,
//            'thumbnail_sizes' => array(50, 100, 120, 200, 400),
            'remove_field_name' => 'remove_logo'
        ];
    }


}
?>