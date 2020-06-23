<?php


defined('PHPFOX') or exit('NO DICE!');


class Coupon_Service_Callback extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('coupon');
	}

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, Phpfox::getTime('m',PHPFOX_TIME,false), Phpfox::getTime('d',PHPFOX_TIME,false), Phpfox::getTime('Y',PHPFOX_TIME,false));
		
		return array(
			'phrase' => _p('coupons'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('coupon'))
				->where('status != 7 AND status != 3 AND status != 8 AND (time_stamp IS NULL OR time_stamp >= ' . $iToday .' )')
				->execute('getSlaveField')
		);
	}	
        
	public function pendingApproval()
	{
		return array(
			'phrase' => _p('coupons'),
			'value' => Phpfox::getService('coupon')->getTotalPending(),
			'link' => Phpfox::getLib('url')->makeUrl('coupon', array('view' => 'pending'))
		);
	}
        
	public function getGlobalPrivacySettings()
	{
		return array(
			'coupon.default_privacy_setting' => array(
				'phrase' => _p('coupons')
			)
		);
	}
        
    public function paymentApiCallback($aParam)
    {

        $iTransactionId = $aParam['item_number'];
        if($aParam['status'] == Phpfox::getService('coupon.transaction')->getPaypalStatusCode('completed'))
        {
            if(!$aParam['total_paid'])
            {
                return false;
            }
            $aTransaction = Phpfox::getService('coupon.transaction')->getTransactionForCallback($iTransactionId);

            if($aTransaction['payment_type'] == 2)
                Phpfox::getService('coupon.process')->feature($aTransaction['coupon_id'], 1);
            else
            {
                $aCoupon = Phpfox::getService('coupon')->getCouponById($aTransaction['coupon_id']);
                if($aCoupon)
                {
                    if($aCoupon['status'] == Phpfox::getService('coupon')->getStatusCode('draft'))
                        Phpfox::getService('coupon.process')->publish($iTransactionId);
                }
            }
        	(($sPlugin = Phpfox_Plugin::get('coupon.service_callback_payment_coupon__end')) ? eval($sPlugin) : false);
        }
        Phpfox::getService('coupon.transaction.process')->updatePaypalTransaction($iTransactionId, $aParam);
    }
	
	/**
	 *  Return comment privacy value on coupon
	 * @author TienNPL
	 */
	public function getAjaxCommentVar()
	{
		return 'coupon.can_post_comment_on_coupon';
	}
	
	/**
	 * A callback method that called when adding a comment on coupon
	 * @author TienNPL
	 */
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('coupon_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('coupon'))
			->where('coupon_id = ' . (int) $iId)
			->execute('getSlaveRow');	
				
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'],  $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
	
	/**
	 * A callback method that is called when adding a comment on coupon
	 * @author TienNPL
	 */
	public function addComment($aVals, $iUserId = null, $sUserName = null) 
	{
        $aCoupon = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, c.title , c.coupon_id')
                ->from(Phpfox::getT('coupon'), 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where('c.coupon_id = ' . (int) $aVals['item_id'])
                ->execute('getSlaveRow');
				
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
		}
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('coupon', 'total_comment', 'coupon_id', $aVals['item_id']);
		}

		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
		
        // Send the user an email
		$sLink = Phpfox::getlib('url')->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aCoupon['user_id'],
				'item_id' => $aCoupon['coupon_id'],
				'owner_subject' => _p('full_name_commented_on_your_coupon_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aCoupon['title'])),
				'owner_message' => _p('full_name_commented_on_your_coupon_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aCoupon['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_coupon',
				'mass_id' => 'coupon',
				'mass_subject' => (Phpfox::getUserId() == $aCoupon['user_id'] ? _p('full_name_commented_on_gender_coupon_title', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aCoupon['gender'], 1), 'title' => $aCoupon['title'])):_p('full_name_commented_on_coupon_full_name_s_coupon', array('full_name' => Phpfox::getUserBy('full_name'), 'coupon_full_name' => $aCoupon['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aCoupon['user_id'] ? _p('full_name_commented_on_gender_coupon_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aCoupon['gender'], 1), 'link' => $sLink, 'title' => $aCoupon['title'])) : _p('full_name_commented_on_coupon_full_name_s_coupon_message', array('full_name' => Phpfox::getUserBy('full_name'), 'coupon_full_name' => $aCoupon['full_name'], 'link' => $sLink, 'title' => $aCoupon['title'])))
			)
		);
		
		/**
		 *Add notify for follower 
		 */
		$aFollowerIds = Phpfox::getService('coupon')->getFollowerIds($aCoupon['coupon_id']);		
		
		if($aFollowerIds)
		{
			foreach($aFollowerIds as $aFollowerId)
			{
				Phpfox::getService("notification.process")->add("comment_coupon", $aCoupon['coupon_id'], $aFollowerId['user_id'], Phpfox::getUserId());
			}
		} 
    }

	/**
	 * A callback method that called when delete a comment on coupon
	 * @author TienNPL
	 */
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('coupon', 'total_comment', 'coupon_id', $iId, TRUE);
	}
	
	/**
	 * A callback method that called when get a comment notification on coupon
	 * @author TienNPL
	 */
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, c.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['coupon_id']))
		{
			return FALSE;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = _p('users_commented_on_gender_coupon_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_coupon_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_coupon_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);
	}
	
	public function getCommentNotificationTag($aNotification)
	{
		$aRow = $this->database()->select('co.coupon_id, co.title, u.user_name, u.full_name')
					->from(Phpfox::getT('comment'), 'c')
					->join(Phpfox::getT('coupon'), 'co', 'co.coupon_id = c.item_id')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.comment_id = ' . (int)$aNotification['item_id'])
					->execute('getSlaveRow');
		
		$sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_coupon', array('user_name' => $aRow['full_name']));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']) . 'comment_' .$aNotification['item_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);
	}

	/**
	 * A callback method that is called when adding a like on coupon
	 * @author TienNPL
	 */
	public function addLike($iItemId, $bDoNotSendEmail = FALSE)
	{
		$aRow = $this->database()->select('coupon_id, title, user_id')
			->from(Phpfox::getT('coupon'))
			->where('coupon_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['coupon_id']))
		{
			return FALSE;
		}
		
		$this->database()->updateCount('like', 'type_id = \'coupon\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'coupon', 'coupon_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('coupon.full_name_liked_your_coupon_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(array('coupon.full_name_liked_your_coupon_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('coupon_like', $aRow['coupon_id'], $aRow['user_id']);
			
			/**
			 *Add notify for follower 
			 */
			$aFollowerIds = Phpfox::getService('coupon')->getFollowerIds($aRow['coupon_id']);		
			
			if($aFollowerIds)
			{
				foreach($aFollowerIds as $aFollowerId)
				{
					Phpfox::getService("notification.process")->add("coupon_like", $aRow['coupon_id'], $aFollowerId['user_id'], Phpfox::getUserId());
				}
			} 
		}
	}
	
	/**
	 * A callback method that called when unlike a coupon
	 * @author TienNPL
	 */
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'coupon\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'coupon', 'coupon_id = ' . (int) $iItemId);	
	}
	/**
	 * A callback method that called when get a like notification on coupon
	 * @author TienNPL
	 */
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if(!$aRow) return false;
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_coupon_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_coupon_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationFeature($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('admin_set_featured_gender_own_coupon_title', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('admin_set_featured_your_coupon_title', array('title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('admin_set_featured_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationDeny($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('admin_had_denied_gender_own_coupon_title', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('admin_had_denied_your_coupon_title', array('title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('admin_had_denied_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationApprove($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('admin_had_approved_gender_own_coupon_title', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('admin_had_approved_your_coupon_title', array('title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('admin_had_approved_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationPause($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('admin_had_paused_gender_own_coupon_title', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('admin_had_paused_your_coupon_title', array('title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('admin_had_paused_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationResume($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('admin_had_resumed_gender_own_coupon_title', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('admin_had_resumed_your_coupon_title', array('title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('admin_had_resumed_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationClaim($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_had_claimed_gender_own_coupon_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_had_claimed_your_coupon_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_had_claimed_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}

	public function getNotificationClose($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('the_coupon_title_had_been_closed_since_it_was_ended_or_reached_maximum_claims', array('title' => $sTitle));
						
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationRun($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('the_coupon_title_had_started_running', array('title' => $sTitle));
						
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}
	
	public function getNotificationInvited($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('users_invited_you_to_the_coupon_title', array('users' => $sUsers,'title' => $sTitle));
						
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}

	public function getNotificationFavorite($aNotification)
	{
		$aRow = $this->database()->select('c.coupon_id, c.title, u.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('coupon'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.coupon_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_had_set_favorite_gender_own_coupon_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_had_set_favorite_your_coupon_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_had_set_favorite_span_class_drop_data_user_row_full_name_s_span_coupon_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon')
		);	
	}

	public function canShareItemOnFeed(){}
	
	
	public function getFeedDetails($iItemId)
	{
		return array(
			'module' => 'coupon',
			'table_prefix' => 'coupon_',
			'item_id' => $iItemId
		);		
	}	

	/**
	 *  Call back methods for report and comment on coupon
	 */
	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aCoupon = $this->database()->select('c.coupon_id, c.title')
					->from(Phpfox::getT('coupon'), 'c')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.coupon_id = ' . (int) $iId)
					->execute('getSlaveRow');		
					
		if (!isset($aCoupon['coupon_id']))
		{
			return false;
		}				
		
		return Phpfox::permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']);
	}
	
	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	/**
	 * A callback method that called when rate a coupon
	 * @author TienNPL
	 */
	public function getRatingData()
	{
		return array(
			'field' => 'coupon_id',
			'table' => 'coupon',
			'table_rating' => 'coupon_rating'
		);
	}
	
    /**
     * get new feed to show
     * @by : datlv
     * @param $aRow
     * @param null $iUserId
     * @return mixed
     */
    public function getNewsFeed($aRow, $iUserId = null)
    {
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        $aRow['text'] = _p('user_full_name_added_a_new_coupon_a_href_title_link_title_a',
            array(
                'user_full_name' => $aRow['user_full_name'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['title']),
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link']
            )
        );

        $aRow['icon'] = Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon');
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'coupon';

        return $aRow;
    }

    /**
     * @by : datlv
     * @param $aRow
     * @param null $iUserId
     * @return mixed
     */
    public function getCommentNewsFeed($aRow, $iUserId = null)
    {
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        if ($aRow['user_id'] == $aRow['item_user_id'])
        {
            $aRow['text'] = _p('user_added_a_new_comment_on_their_own_coupon', array(
                    'user_name' => $aRow['user_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        }
        elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
        {
            $aRow['text'] = _p('user_added_a_new_comment_on_your_coupon', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        }
        else
        {
            $aRow['text'] = _p('user_name_added_a_new_comment_on_item_user_name_coupon', array(
                    'user_name' => $aRow['user_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'item_user_name' => $aRow['viewer_full_name'],
                    'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
                )
            );
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['title']);

        return $aRow;
    }

    /**
     * @by : datlv
     * @return array
     */
    public function updateCounterList()
    {
        $aList = array();

        $aList[] =	array(
            'name' => _p('users_coupon_count'),
            'id' => 'coupon-total'
        );

        $aList[] =	array(
            'name' => _p('update_users_activity_coupon_points'),
            'id' => 'coupon-activity'
        );

        return $aList;
    }

    /**
     * @by : datlv
     * @param $iId
     * @param $iPage
     * @param $iPageLimit
     * @return mixed
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'coupon-total')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(c.coupon_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('coupon'), 'c', 'c.user_id = u.user_id AND c.is_removed = 0')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_field'), array('total_coupon' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
        else
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('m.user_id, m.activity_coupon, m.activity_points, m.activity_total, COUNT(c.coupon_id) AS total_items')
                ->from(Phpfox::getT('user_activity'), 'm')
                ->leftJoin(Phpfox::getT('coupon'), 'c', 'c.user_id = m.user_id')
                ->group('m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_activity'), array(
                    'activity_points' => (($aRow['activity_total'] - ($aRow['activity_coupon'] * Phpfox::getUserParam('coupon.points_coupon'))) + ($aRow['total_items'] * Phpfox::getUserParam('coupon.points_coupon'))),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_coupon']) + $aRow['total_items']),
                    'activity_coupon' => $aRow['total_items']
                ), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
    }

    /**
     * auto update count item
     * @param $iUserId
     * @return array
     */
    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_coupon',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('coupon'))->where('user_id = ' . (int) $iUserId . ' AND is_approved = 1 AND is_draft = 0')->execute('getSlaveField')
        );
    }

    /**
     * @by : datlv
     * @param $aRow
     * @return array|bool
     */
    public function getActivityFeedComment($aRow)
    {
        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aItem = $this->database()->select('co.coupon_id, co.title, co.time_stamp, co.total_comment, co.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('coupon'), 'co', 'c.type_id = \'coupon\' AND c.item_id = co.coupon_id AND c.view_id = 0')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = co.user_id')
            ->where('c.comment_id = ' . (int) $aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aItem['coupon_id']))
        {
            return false;
        }

        $sLink = Phpfox::permalink('coupon.detail', $aItem['coupon_id'], $aItem['title']);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
        $sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
        $sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);

        if ($aRow['user_id'] == $aItem['user_id'])
        {
            $sMessage = _p('posted_a_comment_on_gender_coupon_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
        }
        else
        {
            $sMessage = _p('posted_a_comment_on_user_name_s_coupon_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
        }

        return array(
            'no_share' => true,
            'feed_info' => $sMessage,
            'feed_link' => $sLink,
            'feed_status' => $aItem['text'],
            'feed_total_like' => $aItem['total_like'],
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon'),
            'time_stamp' => $aRow['time_stamp'],
            'like_type_id' => 'feed_mini'
        );
    }

    /**
     * @by : datlv
     * @param $aItem
     * @param null $aCallback
     * @param bool $bIsChildItem
     * @return array|bool
     */
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (Phpfox::isUser())
        {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'coupon\' AND l.item_id = co.coupon_id AND l.user_id = ' . Phpfox::getUserId());
        }
        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = co.user_id');
        }

        $aRow = $this->database()->select('co.coupon_id, co.title, co.time_stamp, co.module_id, co.item_id, co.server_id, co.image_path, co.total_comment, co.total_like, ct.description_parsed as description')
            ->from(Phpfox::getT('coupon'), 'co')
            ->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = co.coupon_id')
            ->where('co.coupon_id = ' . (int) $aItem['item_id'])
            ->execute('getSlaveRow');

        if(!isset($aRow['coupon_id']))
        {
            return FALSE;
        }		
		
		if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'coupon.view_browse_coupons'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'coupon.view_browse_coupons'))			
		)
		{
			return false;
		}
		
        $aFeed = array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('posted_a_coupon'),
            'feed_link' => Phpfox::permalink('coupon.detail', $aRow['coupon_id'], $aRow['title']),
            'feed_content' => strip_tags(Phpfox::getLib('parse.bbcode')->parse($aRow['description'])),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'coupon'),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'coupon',
            'like_type_id' => 'coupon',
			'load_block' => 'coupon.feed'
        );


        if(!empty($aRow['image_path']))
        {
            $aRow['image_path'] = sprintf($aRow['image_path'], '');
        }
		
		if ($bIsChildItem)
		{
			$aFeed = array_merge($aFeed, $aItem);
		}

        Phpfox_Component::setPublicParam('custom_param_coupon_' . $aItem['feed_id'], $aRow);

        return $aFeed;
    }

    /**
     * @by : datlv
     * @return array
     */
    public function getDashboardLinks()
    {
        return array(
            'submit' => array(
                'phrase' => _p('create_a_coupon'),
                'link' => 'coupon.add',
                'image' => 'misc/page_white_add.png'
            ),
            'edit' => array(
                'phrase' => _p('manage_coupon'),
                'link' => 'profile.coupon',
                'image' => 'misc/page_white_edit.png'
            )
        );
    }

    /**
     * @by : datlv
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('coupons') => $aUser['activity_coupon']
        );
    }

    /**
     * @by : datlv
     * @return string
     */
    public function getProfileLink()
    {
        return 'profile.coupon';
    }

    /**
     * @by : datlv
     * @return string
     */
    public function getAjaxProfileController()
    {
        return 'coupon.index';
    }

    /**
     * @by : datlv
     * @param $aUser
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        $aUser['total_coupon'] = Phpfox::getService('coupon')->getNumberCouponByUser($aUser['user_id']);

        if (!Phpfox::getParam('profile.show_empty_tabs'))
        {
            if (!isset($aUser['total_coupon']))
            {
                return false;
            }

            if (isset($aUser['total_coupon']) && (int) $aUser['total_coupon'] === 0)
            {
                return false;
            }
        }

        $aSubMenu = array();

        $aMenus[] = array(
            'phrase' => _p('coupons'),
            'url' => 'profile.coupon',
            'total' => (int) (isset($aUser['total_coupon']) ? $aUser['total_coupon'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/coupon.png',
			'icon_class' => 'ico ico-cart-o'
        );

        return $aMenus;
    }

    /**
     * set menu in pages
     * @param $aPage
     * @return array|null
     */
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'coupon.view_browse_coupons'))
        {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('coupons'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'coupon/',
            'icon' => 'feed/coupon.png',
            'landing' => 'coupon',
            'menu_icon' => 'ico ico-cart-o'
        );

        return $aMenus;
    }

	public function getGroupMenu($aPage)
	{
		if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'coupon.view_browse_coupons'))
		{
			return null;
		}

		$aMenus[] = array(
			'phrase' => _p('coupons'),
			'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'coupon/',
			'icon' => 'feed/coupon.png',
			'landing' => 'coupon',
            'menu_icon' => 'ico ico-cart-o'
		);

		return $aMenus;
	}

    /**
     * allow add coupon in pages
     * @param $iId
     * @return array
     */
    public function addCoupon($iId)
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
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'coupon.share_coupons'))
        {
            return null;
        }

        return array(
            array(
                'phrase' => _p('create_a_coupon'),
                'url' => Phpfox::getLib('url')->makeUrl('coupon.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            )
        );
    }

	public function getGroupSubMenu($aPage)
	{
		if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'coupon.share_coupons'))
		{
			return null;
		}

		return array(
			array(
				'phrase' => _p('create_a_coupon'),
				'url' => Phpfox_Url::instance()->makeUrl('coupon.add', array('module' => 'groups', 'item' => $aPage['page_id']))
			)
		);
	}

    public function getPagePerms()
    {
        $aPerms = array();

        $aPerms['coupon.share_coupons'] = _p('who_can_share_coupons');
        $aPerms['coupon.view_browse_coupons'] = _p('who_can_view_browse_coupons');

        return $aPerms;
    }

	public function getGroupPerms()
	{
		$aPerms = array();

		$aPerms['coupon.share_coupons'] = _p('who_can_share_coupons');
		$aPerms['coupon.view_browse_coupons'] = _p('who_can_view_browse_coupons');

		return $aPerms;
	}

    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'coupon.view_browse_coupons'))
        {
            return false;
        }

        return true;
    }

    /**
     * check view detail coupon in pages
     * @param $aItem
     * @return array|bool
     */
    public function getCouponsDetails($aItem)
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
            'url_home_pages' => $sLink . 'coupon/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

	public function getCouponsGroupDetails($aItem)
	{
		Phpfox::getService('groups')->setIsInPage();

		$aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

		if (!isset($aRow['page_id']))
		{
			return false;
		}

		Phpfox::getService('groups')->setMode();

		$sLink = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

		return array(
			'breadcrumb_title' => _p('Groups'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('groups'),
			'module_id' => 'groups',
			'item_id' => $aRow['page_id'],
			'module' => 'groups',
			'item' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_pages' => $sLink . 'coupon/',
			'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}
		public function getActions()
	{
		return array(
			'dislike' => array(
				'enabled' => true,
				'action_type_id' => 2, // sort of redundant given the key 
				'phrase' => _p('dislike'),
				'phrase_in_past_tense' => _p('disliked'),
				'item_type_id' => 'coupon', // used internally to differentiate between photo albums and photos for example.
				'item_phrase' => _p('item_phrase'), // used to display to the user what kind of item is this
				'table' => 'coupon',
				'column_update' => 'total_dislike',
				'column_find' => 'coupon_id',
				'where_to_show' => array('', 'coupon')
				)
		);
	}
	
    public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.coupon_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'coupon\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('coupon'), 'item')
			->where($this->database()->searchKeywords('item.title', $sSearch) . ' AND item.is_approved = 1 AND item.privacy = 0 AND item.status IN(1,2,5)')
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('coupon.detail', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('coupon');

		if ($aRow['item_photo']) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'core.url_pic',
                    'suffix' => '_200',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        else {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => 'coupon/static/image/default/noimage.png',
                    'path' => 'core.url_module',
                    'suffix' => '_200',
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
			'name' => _p('coupon')
		);
	}

    public function getUploadParams() {
        return [
            'label' => _p('image'),
            'max_size' => Phpfox::getParam('coupon.max_upload_image_size') ? (Phpfox::getParam('coupon.max_upload_image_size')/1024) : null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'coupon' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'coupon' . PHPFOX_DS,
            'thumbnail_sizes' => array(100, 200, 400),
            'remove_field_name' => 'remove_logo'
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
		if ($sPlugin = Phpfox_Plugin::get('coupon.service_callback__call'))
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
