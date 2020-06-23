<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author           VuDP, AnNT
 * @package          Module_jobposting
 */

class JobPosting_Service_Callback extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_job');
    }

    public function getNotificationAdmin_Assignment($aNotification)
    {
        if(empty($aNotification['item_id']))
        {
            return false;
        }

        $aRow = Phpfox::getService('jobposting.company')->getCompanyById($aNotification['item_id']);

        if(empty($aRow))
        {
            return false;
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
            'message' => _p('jobposting_to_be_admin_of_company',[
                'name' => $aRow['name']
            ]),
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

	public function getReportRedirectJob($aParam)
	{
		$sUrl = 	Phpfox_Url::instance()->makeUrl("jobposting").$aParam."/";
		Phpfox_Url::instance()->send($sUrl);
	}

    public function paymentApiCallback($aParam)
    {
        if(!$aParam['total_paid'])
        {
            return false;
        }
        
        $iTransactionId = $aParam['item_number'];
        $aTransaction = Phpfox::getService('jobposting.transaction')->get($iTransactionId);
        if(!$aTransaction)
        {
            return false;
        }
        switch($aTransaction['payment_type'])
        {
            case 1: //sponsor
                if($aParam['status'] == 'completed')
                {
                    Phpfox::getService('jobposting.company.process')->sponsor($aTransaction['item_id']);
                }
                break;
            case 2: //package
                Phpfox::getService('jobposting.package.process')->updatePayStatus($aTransaction['invoice'], $aParam['status']);
                break;
            case 3: //package + publish
                Phpfox::getService('jobposting.package.process')->updatePayStatus($aTransaction['invoice'], $aParam['status']);
                if($aParam['status'] == 'completed')
				{
				    Phpfox::getService('user.auth')->setUserId($aTransaction['user_id']);
					Phpfox::getService("jobposting.job.process")->publish($aTransaction['invoice']['publish']);
                    Phpfox::getService('jobposting.package.process')->updateRemainingPost($aTransaction['invoice']['package_data'][0]);
				}
                break;
            case 4: //package + publish + feature
            	Phpfox::getService('jobposting.package.process')->updatePayStatus($aTransaction['invoice'], $aParam['status']);
				if($aParam['status'] == 'completed')
				{
				    Phpfox::getService('user.auth')->setUserId($aTransaction['user_id']);
					Phpfox::getService("jobposting.job.process")->publish($aTransaction['invoice']['publish']);
                    Phpfox::getService('jobposting.package.process')->updateRemainingPost($aTransaction['invoice']['package_data'][0]);
					Phpfox::getService("jobposting.job.process")->featureJobs($aTransaction['invoice']['feature'],1);
				}
                break;
            case 5: //feature
            	if($aParam['status'] == 'completed')
				{
					Phpfox::getService("jobposting.job.process")->featureJobs($aTransaction['invoice']['feature'],1);
				}
                break;
            case 6: 
                break;
            case 7: // pay fee for apply job
            	Phpfox::getService('jobposting.applyjobpackage.process')->updatePayStatusOnePackage($aTransaction['invoice'], $aParam['status']);
            	if($aParam['status'] == 'completed')
				{
				    Phpfox::getService('user.auth')->setUserId($aTransaction['user_id']);
                    Phpfox::getService('jobposting.applyjobpackage.process')->updateRemainingApply($aTransaction['invoice']['package_data']);					
				}
            	break;
            default:
                #do nothing
        }
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_callback_payment_callback__end')) ? eval($sPlugin) : false);
        Phpfox::getService('jobposting.transaction.process')->update($iTransactionId, $aParam);
    }
	
	public function getFeedDisplay($company_id)
	{
		return array(
			'module' => 'jobposting',
			'table_prefix' => 'jobposting_',
			'ajax_request' => 'jobposting.addFeedComment',
			'item_id' => $company_id
		);
	}

	public function getAjaxCommentVar()
	{
		return ;
	}	
	
	public function getActivityFeedComment($aItem)
	{

		$aRow = $this->database()->select('fc.*, l.like_id AS is_liked, e.company_id, e.name')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'jobposting_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId())			
			->where('fc.feed_comment_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		

		if (!isset($aRow['company_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::getLib('url')->permalink(array('jobposting.company', 'comment-id' => $aRow['feed_comment_id']), $aRow['company_id'], $aRow['name']);
		
		$aReturn = array(
			'no_share' => true,
			'feed_status' => $aRow['content'],
			'feed_link' => $sLink,
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'jobposting',
			'like_type_id' => 'jobposting_comment',
			'parent_user_id' => 0
		);
		return $aReturn;		
	}	
	
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('jobposting_company', 'total_comment', 'company_id', $iId, true);
		
	}
	
	public function addLikeComment($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.company_id, e.name')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->where('fc.feed_comment_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'jobposting_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::getLib('url')->permalink(array('jobposting.company', 'comment-id' => $aRow['feed_comment_id']), $aRow['company_id'], $aRow['name']);
			$sItemLink = Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('jobposting.full_name_liked_a_comment_you_posted_on_the_jobposting_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
				->message(array('jobposting.full_name_liked_your_comment_message_jobposting', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'), 'item_link' => $sItemLink, 'title' => $aRow['name'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('jobposting_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
		}
	}		
	
	public function deleteLikeComment($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'jobposting_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
	}

	public function addPhoto($iId)
	{
		return array(
			'module' => 'jobposting',
			'item_id' => $iId,
			'table_prefix' => 'jobposting_'
		);
	}	

	public function addLink($aVals)
	{
		return array(
			'module' => 'jobposting',
			'item_id' => $aVals['callback_item_id'],
			'table_prefix' => 'jobposting_'
		);		
	}
	
	public function addLikeCompany($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('company_id, name, user_id')
			->from(Phpfox::getT('jobposting_company'))
			->where('company_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['company_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'jobposting_company\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_company', 'company_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('jobposting.company', $aRow['company_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('jobposting.full_name_liked_your_company_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
				->message(array('jobposting.full_name_liked_your_company_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['name'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('jobposting_like', $aRow['company_id'], $aRow['user_id']);				
		}		
	}
	
	public function deleteLikeCompany($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'jobposting_company\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_company', 'company_id = ' . (int) $iItemId);
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('e.company_id, e.name, e.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('jobposting_company'), 'e')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('e.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['company_id']))
		{
			return false;
		}			
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_company_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_company_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_company_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function canShareItemOnFeed(){}

	public function getFeedDetails($iItemId)
	{
		return array(
			'module' => 'jobposting',
			'table_prefix' => 'jobposting_',
			'item_id' => $iItemId
		);		
	}	
	
	public function getCommentItem($iId)
	{		
		$aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('jobposting_feed_comment'))
			->where('feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');		
		
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}		
		
		$aRow['parent_module_id'] = 'jobposting';
			
		return $aRow;
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.company_id, e.name, u.full_name, u.gender')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->where('fc.feed_comment_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('jobposting_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);		
		}
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink(array('jobposting.company', 'comment-id' => $aRow['feed_comment_id']), $aRow['company_id'], $aRow['name']);
		$sItemLink = Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['feed_comment_id'],
				'owner_subject' => _p('full_name_commented_on_a_comment_posted_on_the_company_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])),
				'owner_message' => _p('full_name_commented_on_one_of_your_comments_you_posted_on_the_company', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'jobposting_comment_feed',
				'mass_id' => 'jobposting',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_company_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('full_name_commented_on_one_of_row_full_name_s_company_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_own_comments_on_the_company', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)) : _p('full_name_commented_on_one_of_row_full_name_s', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)))
			)
		);
	}
	
	public function getNotificationComment_Feed($aNotification)
	{
		return $this->getCommentNotification($aNotification);	
	}
	
	public function uploadVideo($aVals)
	{
		return array(
			'module' => 'jobposting',
			'item_id' => $aVals['callback_item_id']
		);
	}
	
	public function convertVideo($aVideo)
	{
		return array(
			'module' => 'jobposting',
			'item_id' => $aVideo['item_id'],
			'table_prefix' => 'jobposing_'
		);			
	}
	
	public function getCommentNotification($aNotification)
	{
	
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.company_id, e.name')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_comment_on_the_company_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = _p('users_commented_on_gender_own_comment_on_the_company_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_one_of_your_comments_on_the_company_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_comments_on_the_company_tit', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink(array('jobposting.company', 'comment-id' => $aRow['feed_comment_id']), $aRow['company_id']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}		
	
	public function getNotificationComment($aNotification)
	{
		
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.company_id, e.name')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')			
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_company_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' =>  $sTitle));
			}
			else 
			{
				$sPhrase = _p('users_commented_on_gender_own_company_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_company_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_company_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getNotificationComment_Like($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.company_id, e.name')
			->from(Phpfox::getT('jobposting_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('jobposting_company'), 'e', 'e.company_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_company_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = _p('users_liked_gender_own_comment_on_the_company_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_one_of_your_comments_on_the_company_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_company_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink(array('jobposting_company', 'comment-id' => $aRow['feed_comment_id']), $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getActions()
	{
		return array(
			'dislike' => array(
				'enabled' => true,
				'action_type_id' => 2, // 2 = dislike
				'phrase' => _p('like.dislike'),
				'phrase_in_past_tense' => 'disliked',
				'item_type_id' => 'jobposting', // used to differentiate between photo albums and photos for example.
				'table' => 'jobposting_company',
				'item_phrase' => _p('item_phrase'),
				'column_update' => 'total_dislike',
				'column_find' => 'company_id'				
				)
		);
	}	

	//for commen box of job
	public function getActivityFeedCommentJob($aRow)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
		}		
		
		$aItem = $this->database()->select('b.job_id, b.title, b.time_stamp, b.total_comment, b.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('jobposting.job'), 'b', 'c.type_id = \'blog\' AND c.item_id = b.job_id AND c.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('c.comment_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aItem['job_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::permalink('jobposting', $aItem['job_id'], $aItem['title']);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
		$sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
		$sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);
		
		if ($aRow['user_id'] == $aItem['user_id'])
		{
			$sMessage = _p('posted_a_comment_on_gender_job_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
		}
		else
		{			
			$sMessage = _p('posted_a_comment_on_user_name_s_job_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
		}
		(($sPlugin = Phpfox_Plugin::get('job.component_service_callback_getactivityfeedcomment__1')) ? eval($sPlugin) : false);
		
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

	public function canShareItemOnFeedJob(){}
	
	public function getActivityFeedJob($aItem, $aCallback = null, $bIsChildItem = false)
	{
        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'jobposting_job\' AND l.item_id = b.job_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = b.user_id');
        }
		
		$aRow = $this->database()->select('b.post_status, b.job_id, b.company_id, b.title, b.working_place, b.time_stamp, b.time_expire, b.total_comment, b.total_like, bt.description_parsed')
			->from(Phpfox::getT('jobposting_job'), 'b')
			->join(Phpfox::getT('jobposting_job_text'), 'bt', 'bt.job_id = b.job_id')
			->where('b.job_id = ' . (int) $aItem['item_id'].' and is_deleted = 0')
			->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }
                
        $aReturn = array_merge(array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('posted_a_job'),
            'feed_link' => Phpfox::permalink('jobposting', $aRow['job_id'], $aRow['title']),
            'feed_content' => $aRow['description_parsed'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/job.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'jobposting_job',
            'like_type_id' => 'jobposting_job',
            'custom_data_cache' => $aRow,
            'load_block' => 'jobposting.job.feed',
            'type_id' => 'jobposting_job'
        ), $aItem);

        $aCompany = Phpfox::getService('jobposting.company')->getCompanyById($aRow['company_id']);
        if (!empty($aCompany['image_path'])) {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCompany['server_id'],
                'path' => 'core.url_pic',
                'file' => 'jobposting/' . $aCompany['image_path'],
                'suffix' => '_500',
                'return_url' => true
            ));

        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/jobposting/static/image/default/default_ava.png';
        }

        $aRow['time_expire_micro'] = Phpfox::getTime('M d, Y', $aRow['time_expire'], true, true);
        Phpfox_Component::setPublicParam('custom_param_jobposting_job_' . $aItem['feed_id'], ['aJob' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('jobposting', $aRow['job_id'], $aRow['title']),
            'sCategories' => Phpfox::getService('jobposting.catjob')->getPhraseCategory($aRow['job_id'])
        ]);

        (($sPlugin = Phpfox_Plugin::get('jobposting.component_service_callback_getactivityjobfeed__1')) ? eval($sPlugin) : false);

        return $aReturn;
	}

	public function addLikeJob($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('job_id, title, user_id')
			->from(Phpfox::getT('jobposting_job'))
			->where('job_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['job_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'jobposting_job\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_job', 'job_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('jobposting', $aRow['job_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('jobposting.full_name_liked_your_job_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(array('jobposting.full_name_liked_your_job_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('jobposting_job_like', $aRow['job_id'], $aRow['user_id']);
		}
	}

	public function getNotificationJob_Like($aNotification)
	{
		$aRow = $this->database()->select('b.job_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('jobposting_job'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_event_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_event_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_job_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function deleteLikeJob($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'jobposting_job\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'jobposting_job', 'job_id = ' . (int) $iItemId);	
	}
	
	public function getAjaxCommentVarJob()
	{
		return ;
	}
	
	public function addCommentJob($aVals, $iUserId = null, $sUserName = null)
	{
	
		$aJob = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.job_id, b.privacy, b.privacy_comment')
			->from($this->_sTable, 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.job_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
		
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
		}
		
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			
			$this->database()->updateCounter('jobposting_job', 'total_comment', 'job_id', $aVals['item_id']);
		}
		
		// Send the user an email
		$sLink = Phpfox::permalink('jobposting', $aJob['job_id'], $aJob['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aJob['user_id'],
				'item_id' => $aJob['job_id'],
				'owner_subject' => _p('full_name_commented_on_your_job_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aJob['title'])),
				'owner_message' => _p('full_name_commented_on_your_job_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aJob['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_jobposting_job',
				'mass_id' => 'jobposting_job',
				'mass_subject' => (Phpfox::getUserId() == $aJob['user_id'] ? _p('full_name_commented_on_gender_job', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aJob['gender'], 1))) : _p('full_name_commented_on_job_full_name_s_job', array('full_name' => Phpfox::getUserBy('full_name'), 'blog_full_name' => $aJob['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aJob['user_id'] ? _p('full_name_commented_on_gender_job_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aJob['gender'], 1), 'link' => $sLink, 'title' => $aJob['title'])) : _p('full_name_commented_on_job_full_name_s_job_message', array('full_name' => Phpfox::getUserBy('full_name'), 'blog_full_name' => $aJob['full_name'], 'link' => $sLink, 'title' => $aJob['title'])))
			)
		);
	
	}	

	public function updateCommentText($aVals, $sText)
	{
		
	}
	
	public function updateCommentTextJob($aVals, $sText)
	{
		
	}
	
	public function getCommentItemJob($iId)
	{
		$aRow = $this->database()->select('job_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('job_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
	
	public function getCommentItemNameJob()
	{
		return 'jobposting_job';
	}

	public function deleteCommentJob($iId)
	{
		$this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'job_id = ' . (int) $iId);
	}
	
	public function getCommentNotificationJob($aNotification)
	{
		$aRow = $this->database()->select('b.job_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('jobposting_job'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['job_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = _p('users_commented_on_gender_job_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_job_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getCommentNotificationFeedJob($aRow)
	{
		return array(
			'message' => _p('full_name_wrote_a_comment_on_your_job_job_title', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $aRow['full_name'],
					'job_link' => Phpfox::getLib('url')->makeUrl('jobposting', array('redirect' => $aRow['item_id'])),
					'job_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')	
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('jobposting', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}
		
	public function getCommentNotificationTagJob($aNotification)
	{
		$aRow = $this->database()->select('b.job_id, b.title, u.user_name, u.full_name')
					->from(Phpfox::getT('comment'), 'c')
					->join(Phpfox::getT('jobposting_job'), 'b', 'b.job_id = c.item_id')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.comment_id = ' . (int)$aNotification['item_id'])
					->execute('getSlaveRow');
		
		
		$sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_job', array('user_name' => $aRow['full_name']));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']) . 'comment_' .$aNotification['item_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getSiteStatsForAdmin($iStartTime, $iEndTime)
	{
		$aCond = array();
		$aCond[] = 'is_approved = 1 AND post_status = 1';
		if ($iStartTime > 0)
		{
			$aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
		}	
		if ($iEndTime > 0)
		{
			$aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
		}			
		
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('jobposting_job'))
			->where($aCond)
			->execute('getSlaveField');
		
		return array(
			'phrase' => 'jobposting.jobs',
			'total' => $iCnt,
			'icon' => 'fa fa-briefcase'
		);
	}	
	
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => _p('jobs'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('jobposting_job'))
				->where('post_status = 1 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}
	
	public function pendingApproval()
	{
		return array(
			'phrase' => _p('jobs'),
			'value' => Phpfox::getService('jobposting.job')->getPendingTotal(),
			'link' => Phpfox::getLib('url')->makeUrl('jobposting', array('view' => 'pending_jobs'))
		);
	}

    public function getActivityFeedCompany($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'jobposting_company\' AND l.item_id = b.company_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = b.user_id');
        }

        $aRow = $this->database()
            ->select('b.company_id, b.name, b.image_path, b.server_id as image_server_id, b.time_stamp, b.total_comment, b.total_like, b.module_id, b.item_id, bt.description_parsed')
            ->from(Phpfox::getT('jobposting_company'), 'b')
            ->join(Phpfox::getT('jobposting_company_text'), 'bt', 'bt.company_id = b.company_id')
            ->where('b.company_id = ' . (int)$aItem['item_id'] . ' and b.is_deleted = 0 and b.is_approved = 1')
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $aRow['group_id'] = $aRow['item_id'];
        $aItem['item_id'] = $aRow['company_id'];
        $aReturn = array_merge(array(
            'feed_title' => $aRow['name'],
            'feed_info' => _p('created_a_company'),
            'feed_link' => Phpfox::permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
            'feed_content' => $aRow['description_parsed'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/blog.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'jobposting_company',
            'like_type_id' => 'jobposting_company',
            'custom_data_cache' => $aRow,
            'load_block' => 'jobposting.company.feed',
            'type_id' => 'jobposting_company'
        ), $aItem);

        if (!empty($aRow['image_path'])) {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['image_server_id'],
                'path' => 'core.url_pic',
                'file' => 'jobposting/' . $aRow['image_path'],
                'suffix' => '_1024',
                'return_url' => true
            ));

        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/jobposting/static/image/default/default_ava.png';
        }

        Phpfox_Component::setPublicParam('custom_param_jobposting_company_' . $aItem['feed_id'], ['aCompany' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
            'sCategories' => Phpfox::getService('jobposting.category')->getPhraseCategory($aRow['company_id'], true)
        ]);

        return $aReturn;
    }
	
	public function getNotificationInvite_Job($aNotification)
	{
		$aRow = $this->database()->select('j.job_id, j.title')	
			->from(Phpfox::getT('jobposting_job'), 'j')
			->where('j.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['job_id']))
		{
			return false;
		}
        
        $sPhrase = _p('full_name_invited_you_to_the_type_title', array(
            'full_name' => Phpfox::getService('notification')->getUsers($aNotification),
            'type' => 'job',
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationInvite_Company($aNotification)
	{
		$aRow = $this->database()->select('c.company_id, c.name')	
			->from(Phpfox::getT('jobposting_company'), 'c')
			->where('c.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['company_id']))
		{
			return false;
		}
        
        $sPhrase = _p('full_name_invited_you_to_the_type_title', array(
            'full_name' => Phpfox::getService('notification')->getUsers($aNotification),
            'type' => 'company',
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationFavoritejob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_favorited_your_job_title', array(
            'users' => $sUsers,
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFavoritefollowedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_favorited_your_followed_job_title', array(
            'users' => $sUsers,
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFavoritecompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_favorited_your_company_name', array(
            'users' => $sUsers,
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFavoritefollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_favorited_your_followed_company_name', array(
            'users' => $sUsers,
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFollowjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_followed_your_job_title', array(
            'users' => $sUsers,
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFollowcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_followed_your_company_name', array(
            'users' => $sUsers,
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeactivatefollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('notification_when_company_deactivated', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);					
	}

	public function getNotificationActivatefollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('notification_when_company_activated', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);				
	}
	public function getNotificationApplyjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_applied_your_job_title', array(
            'users' => $sUsers,
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company.manage', 'job_'.$aRow['job_id']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	public function getNotificationDeletedapplication($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		if (!isset($aRow['job_id']))
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_application_for_the_job_title_was_status', array(
			'title' => $aRow['title'],
			'status' => strtolower(_p('deleted'))
		));

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getNotificationPassedapplication($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		if (!isset($aRow['job_id']))
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_application_for_the_job_title_was_status', array(
			'title' => $aRow['title'],
			'status' => strtolower(_p('passed'))
		));

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getNotificationRejectedapplication($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		if (!isset($aRow['job_id']))
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_application_for_the_job_title_was_status', array(
			'title' => $aRow['title'],
			'status' => strtolower(_p('rejected'))
		));

		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getNotificationAcceptworking($aNotification){
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('no_accept_working', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...'), 
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']) . '#tabs-4',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationRemoveworking($aNotification){
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('no_remove_working', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...'), 
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']) . '#tabs-4',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationRejectworking($aNotification){
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('no_reject_working', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...'), 
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']) . '#tabs-4',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	
	public function getNotificationJoincompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_joined_your_company_name', array(
            'users' => $sUsers,
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']) . '#tabs-4',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationJoinfollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('users_joined_your_followed_company_name', array(
            'users' => $sUsers,
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationFeaturefollowedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_job_title_has_been_featured', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationSponsorfollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_company_name_has_been_sponsored', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationAddjobfollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title, c.company_id, c.name')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->join(Phpfox::getT('jobposting_company'), 'c', 'c.company_id = i.company_id')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_company_name_posted_a_job_title', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], 55, '...'),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], 55, '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeletefollowedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_job_title_has_been_deleted', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeleteappliedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_applied_job_title_has_been_deleted', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeletefollowedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_company_name_has_been_deleted', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeleteappliedcompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_applied_company_name_has_been_deleted', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationDeletecompanyfollowedjob($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('the_company_of_your_followed_job_name_has_been_deleted', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationExpirejob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_job_title_is_expired', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationExpirefollowedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_followed_job_title_is_expired', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationExpireappliedjob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_applied_job_title_is_expired', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationApprovejob($aNotification)
	{
		$aRow = $this->database()->select('i.job_id, i.title')	
			->from(Phpfox::getT('jobposting_job'), 'i')
			->where('i.job_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['job_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_job_title_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}	
	
	public function getNotificationApprovecompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_company_name_has_been_approved', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getNotificationChangeowner($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);		
		
		$sPhrase = _p('owner_has_just_transferred_company_name_to_you', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...'),
            'owner' => $sUsers,            
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.job_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'jobposting\' AS item_type_id, jc.image_path AS item_photo, jc.server_id AS item_photo_server')
			->from(Phpfox::getT('jobposting_job'), 'item')
            ->join(Phpfox::getT('jobposting_company'), 'jc', 'jc.company_id = item.company_id')
			->where('item.post_status = 1 AND item.privacy = 0 AND item.is_approved = 1 AND item.is_deleted = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
			->union();

		$this->database()->select('item.company_id AS item_id, item.name AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'jobposting_company\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('jobposting_company'), 'item')
			->where('item.post_status = 1 AND item.privacy = 0 AND item.is_approved = 1 AND item.is_deleted = 0 AND ' . $this->database()->searchKeywords('item.name', $sSearch))
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('jobposting', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('job');
		
		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => 'jobposting/'.$aRow['item_photo'],
				'path' => 'core.url_pic',
				'suffix' => '_120',
				'max_width' => '120',
				'max_height' => '120'				
			)
		);		
		
		return $aInfo;
	}

	public function getSearchInfoCompany($aRow)
	{
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('jobposting.company', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('Company');

		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => 'jobposting/'.$aRow['item_photo'],
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
			'name' => _p('job')
		);
	}

	public function getSearchTitleInfoCompany()
	{
		return array(
			'name' => _p('company')
		);
	}
	
	public function getNotificationActivatecompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_company_name_has_been_activated', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	public function getNotificationDeactivatecompany($aNotification)
	{
		$aRow = $this->database()->select('i.company_id, i.name')	
			->from(Phpfox::getT('jobposting_company'), 'i')
			->where('i.company_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
        if (!isset($aRow['company_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_company_name_has_been_deactivated', array(
            'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('jobposting.company', $aRow['company_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	public function getGroupPerms()
	{
		$aPerms = array();
		$aPerms['jobposting.share_company'] = _p('Who can share a company?');
		$aPerms['jobposting.view_browse_companies'] = _p('Who can view companies?');
		return $aPerms;
	}

	public function getGroupSubMenu($aPage)
	{
		if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'jobposting.share_company'))
		{
			return null;
		}

		return array(
			array(
				'phrase' => _p('create_your_company'),
				'url' => Phpfox_Url::instance()->makeUrl('jobposting.company.add', array('module' => 'groups', 'item' => $aPage['page_id']))
			)
		);
	}
	public function getGroupMenu($aPage)
	{
		if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'jobposting.view_browse_companies'))
		{
			return null;
		}

		$aMenus[] = array(
			'phrase' => _p('job_posting'),
			'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'jobposting/',
			'icon' => 'feed/jobposting.png',
			'landing' => 'jobposting.company'
		);

		return $aMenus;
	}

    public function getUploadParamsCompany($aParams = null)
    {
        $iRemainImage = $aParams['iRemain'];
        $iMaxFileSize = Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.jobposting.dropzoneOnSending',
            'success' => '$Core.jobposting.dropzoneOnSuccess',
            'queuecomplete' => '$Core.jobposting.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('jobposting.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'jobposting' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'jobposting' . PHPFOX_DS,
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(50, 120, 150, 200, 240, 500, 1024)
        ];
    }
}