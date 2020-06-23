<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Service_Callback extends Phpfox_Service
{
	public function getNotificationApprove($aNotification)
	{
		$aRow = $this->database()->select('rbi.resume_id, rbi.headline, rbi.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_basicinfo'), 'rbi')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')
			->where('rbi.resume_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['headline'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_approved_gender_own_resume_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_approved_your_resume_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('resume.users_approved_span_class_drop_data_user_row_full_name_s_span_resume_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('resume.view', $aRow['resume_id'], $aRow['headline']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getNotificationDeny($aNotification)
	{
		$aRow = $this->database()->select('rbi.resume_id, rbi.headline, rbi.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_basicinfo'), 'rbi')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')
			->where('rbi.resume_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['headline'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_denied_gender_own_resume_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_denied_your_resume_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('resume.users_denied_span_class_drop_data_user_row_full_name_s_span_resume_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('resume.view', $aRow['resume_id'], $aRow['headline']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationFavorite($aNotification)
	{
		$aRow = $this->database()->select('rbi.resume_id, rbi.headline, rbi.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_basicinfo'), 'rbi')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')
			->where('rbi.resume_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['headline'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_added_gender_own_resume_title_to_the_favorite_list', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_added_your_resume_title_to_gender_favorite_list',
										 array(
										 	'users' => $sUsers, 
										 	'title' => $sTitle, 
										 	'gender' => Phpfox::getService('user')->gender($aNotification['gender'], 1)
										 ));
		}
		else 
		{
			$sPhrase = _p('resume.users_added_span_class_drop_data_user_row_full_name_s_span_resume_title_to_gender_favorite_list',
										  array(
									  		'users' => $sUsers, 
									  	    'row_full_name' => $aRow['full_name'], 
									  	    'title' => $sTitle, 
									  	    'gender' => Phpfox::getService('user')->gender($aNotification['gender'], 1)
										  ));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('resume.view', $aRow['resume_id'], $aRow['headline']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationView_Approve($aNotification)
	{
		$aRow = $this->database()->select('ra.account_id, u.full_name as name, ra.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_account'), 'ra')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ra.user_id')
			->where('ra.account_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_approved_gender_own_registration_to_have_a_full_view_on_resumes', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_approved_your_registration_to_have_a_full_view_on_resumes',
										 array(
										 	'users' => $sUsers
										 ));
		}
		else 
		{
			$sPhrase = _p('resume.users_approved_span_class_drop_data_user_row_full_name_s_span_registration_to_have_a_full_view_on_resumes',
										  array(
									  		'users' => $sUsers, 
									  	    'row_full_name' => $aRow['full_name'], 
									  	    'title' => $sTitle, 
									  	    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)
										  ));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('resume'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'registration.gif', 'resume')
		);	
	}
	
	public function getNotificationView_Unapprove($aNotification)
	{
		$aRow = $this->database()->select('ra.account_id, u.full_name as name, ra.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_account'), 'ra')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ra.user_id')
			->where('ra.account_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_unapproved_gender_own_registration_to_have_a_full_view_on_resumes', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_unapproved_your_registration_to_have_a_full_view_on_resumes',
										 array(
										 	'users' => $sUsers
										 ));
		}
		else 
		{
			$sPhrase = _p('resume.users_unapproved_span_class_drop_data_user_row_full_name_s_span_registration_to_get_resume_full_view',
										  array(
									  		'users' => $sUsers, 
									  	    'row_full_name' => $aRow['full_name'], 									  	    
										  ));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('resume'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'registration.gif', 'resume')
		);	
	}

	public function getNotificationWhoview_Approve($aNotification)
	{
		$aRow = $this->database()->select('ra.account_id, u.full_name as name, ra.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_account'), 'ra')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ra.user_id')
			->where('ra.account_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_approved_gender_own_registration_to_have_a_full_view_on_who_view_me_page', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_approved_your_registration_to_have_a_full_view_on_who_view_me_page',
										 array(
										 	'users' => $sUsers
										 ));
		}
		else 
		{
			$sPhrase = _p('resume.users_approved_span_class_drop_data_user_row_full_name_s_span_registration_on_who_view_me',
										  array(
									  		'users' => $sUsers, 
									  	    'row_full_name' => $aRow['full_name'], 									  	    
										  ));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('resume'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'registration.gif', 'resume')
		);	
	}
	
	public function getNotificationWhoview_Unapprove($aNotification)
	{
		$aRow = $this->database()->select('ra.account_id, u.full_name as name, ra.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('resume_account'), 'ra')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ra.user_id')
			->where('ra.account_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if(!$aRow)
		{
			return false;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('resume.users_unapproved_gender_own_registration_to_have_a_full_view_on_who_view_me_page', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('resume.users_unapproved_your_registration_to_have_a_full_view_on_who_view_me_page',
										 array(
										 	'users' => $sUsers
										 ));
		}
		else 
		{
			$sPhrase = _p('resume.users_unapproved_span_class_drop_data_user_row_full_name_s_span_registration_on_who_view_me',
										  array(
									  		'users' => $sUsers, 
									  	    'row_full_name' => $aRow['full_name'], 									  	    
										  ));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->makeUrl('resume'),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'registration.gif', 'resume')
		);
	}

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		// Delete all resumes of this user
		$aResumes = $this->database()
			->select('resume_id')
			->from(Phpfox::getT('resume_basicinfo'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
			
		foreach ($aResumes as $aResume)
		{
			Phpfox::getService('resume.process')->delete($aResume['resume_id']);
		}
		
		// Delete account of this user
		$aAccount = $this->database()->select('account_id')
						 ->from(Phpfox::getT('resume_account'))
						 ->where('user_id = ' . (int)$iUser)
						 ->execute('getSlaveRow');
		if($aAccount)
		{
			Phpfox::getService('resume.account.process')->deleteAccount($aAccount['account_id']);
		}
		
		//Delete all favorites of this user
		$aFavorites = $this->database()->select('favorite_id')
							->from(Phpfox::getT('resume_favorite'))
							->where('user_id = '.(int)$iUser)
							->execute('getSlaveRows');
		foreach ($aFavorites as $aFavorite)
		{
			Phpfox::getService('resume.process')->deleteFavorite($aFavorite['favorite_id']);
		}
		
		// Delete all view history of this user
		$this->database()->delete(Phpfox::getT('resume_viewme'), 'user_id = '. (int)$iUser);
	}

	/**
	 *  Generate menu for user recording profile page
	 * @param $aUser is the array of user information
	 * @return $aMenus or false
	 */
	 
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_resume']))
			{
				return false;
			}

			if (isset($aUser['total_resume']) && (int) $aUser['total_resume'] === 0)
			{
				//return false;
			}	
		}
		
		$iViewerId = Phpfox::getUserId();
		$iTotal = $aUser['total_resume'];
		
		$bIsFriend = Phpfox::isModule('friend') ? Phpfox::getService('friend')->isFriend($iViewerId, $aUser['user_id']) : 0;
		$bViewResumeRegistry = Phpfox::getService('resume.account')->checkViewResumeRegistration($iViewerId);
		$bHasPublishedResume = Phpfox::getService('resume')->hasPublishedResume($aUser['user_id']);
		
		if($iViewerId != $aUser['user_id'] && !$bIsFriend && !$bViewResumeRegistry)
		{
			return false;
		}
		
		// Show 1 published resume if member is viewing other profile if not do not show any thing
		if($iTotal > 0 && $aUser['user_id'] != $iViewerId)
		{
			if($bHasPublishedResume)
			{
				$iTotal = 1;
			}
			else
			{
				$iTotal = 0;
			}
		}
		
		$aMenus[] = array(
			'phrase' => _p('resume.resume'),
			'url' => 'profile.resume',
			'total' => $iTotal,
			'icon' => 'feed/resume.png',
            'icon_class' => 'ico ico-list-bullet-o'
		);	
		
		return $aMenus;
	}

	public function getProfileLink()
	{
			return 'profile.resume';
	}
    
    public function globalUnionSearch($sSearch)
	{
        //support Privacy
        //0: Everyone
        //1: Job Seeker Group
        //2: Friend
        //3: Only me
        $user_id_viewer = Phpfox::getUserId();

        $bViewResumeRegistration = 0;
        if(isset($_SESSION['bViewResumeRegistration'])){
            $bViewResumeRegistration = $_SESSION['bViewResumeRegistration'];
        }

        $privacyfriend = 'item.privacy=2 and 0<(select count("*") from '.Phpfox::getT('friend').' f where f.user_id = item.user_id AND f.friend_user_id = '.$user_id_viewer.')';
        $sCond = ' and( item.user_id='.$user_id_viewer.' or item.privacy=0 or (item.privacy=1 and 1="'.$bViewResumeRegistration.'") or (item.privacy=1 and item.user_id='.$user_id_viewer.') or ('.$privacyfriend.')  )';
              
		$this->database()->select('item.resume_id AS item_id, item.headline AS item_title, item.time_publish AS item_time_stamp, item.user_id AS item_user_id, \'resume\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('resume_basicinfo'), 'item')
			->where('item.is_published = 1 AND ' . $this->database()->searchKeywords('item.headline', $sSearch). $sCond)
			->union();
			  
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('resume.view', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('resume.resume');
		
		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => 'resume/'.$aRow['item_photo'],
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
			'name' => _p('resume.resume')
		);
	}
    /**
     * @return array
     */
    public function getUploadParams() {
        $iMaxFileSize = Phpfox::getUserParam('user.max_upload_size_profile_photo');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'resume' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic'). 'resume',
            'thumbnail_sizes' => [50,120,200],
            'no_square' => true,
            'update_space' => false
        ];
    }
    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':resume_basicinfo')
            ->where('user_id =' . (int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('resumes'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }
}

?>