<?php


defined('PHPFOX') or exit('NO DICE!');


class Directory_Service_Callback extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{

	}

	public function onDeleteUser($iUser)
	{
		$aItems = $this->database()
			->select('business_id')
			->from(Phpfox::getT('directory_business'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aItems as $aItem)
		{
			Phpfox::getService('directory.process')->delete($aItem['business_id']);
		}		
	}

	public function uploadSong($iItemId)
	{
	    if (Phpfox::isModule('pages') || Phpfox::isModule('groups')) {
            Phpfox::getService('pages')->setIsInPage();
        }

		return array(
			'module' => 'directory',
			'item_id' => $iItemId,
			'table_prefix' => 'directory_'
		);			
	}		

    public function getDashboardActivity()
    {
    	
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('directory.business_directory') => $aUser['activity_directory']
        );
    }

	public function updateCounterList()
    {
        $aList = array();

        $aList[] =	array(
            'name' => _p('directory.users_business_count'),
            'id' => 'directory-total'
        );

        $aList[] =	array(
            'name' => _p('directory.update_users_activity_business_points'),
            'id' => 'directory-activity'
        );

        return $aList;
    }

    public function getTotalItemCount($iUserId)
    {
        $sStatus = Phpfox::getService('directory.helper')->getConst('business.status.draft')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.unpaid')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.pending')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.denied')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.paused')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.deleted')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming');
        $result = array(
            'field' => 'total_directory',
            'total' => $this->database()->select('COUNT(*)')
	            ->from(Phpfox::getT('directory_business'))
	            ->where('user_id = ' . (int) $iUserId . ' AND business_status NOT IN '."(" .$sStatus. ")" )
	            ->execute('getSlaveField')
        );
		return $result;
    }

	public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'directory-total')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(c.business_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('directory_business'), 'c', 'c.user_id = u.user_id AND c.business_status NOT IN '."(" 
            			. Phpfox::getService('directory.helper')->getConst('business.status.draft') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.unpaid') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.pending') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.denied') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.paused') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.deleted') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming')  
            		. ")" )
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_field'), array('total_directory' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
        elseif ($iId == 'directory-activity')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('m.user_id, m.activity_directory, m.activity_points, m.activity_total, COUNT(c.business_id) AS total_items')
                ->from(Phpfox::getT('user_activity'), 'm')
                ->leftJoin(Phpfox::getT('directory_business'), 'c', 'c.user_id = m.user_id AND c.business_status NOT IN '."(" 
            			. Phpfox::getService('directory.helper')->getConst('business.status.draft') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.unpaid') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.pending') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.denied') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.paused') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.deleted') 
	            		. "," . Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming')  
            		. ")" )
                ->group('m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_activity'), array(
                    'activity_points' => (($aRow['activity_total'] - ($aRow['activity_directory'] * Phpfox::getUserParam('directory.points_directory'))) + ($aRow['total_items'] * Phpfox::getUserParam('directory.points_directory'))),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_directory']) + $aRow['total_items']),
                    'activity_directory' => $aRow['total_items']
                ), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
    }	

	public function getActivityFeedCheckinhere($aItem, $aCallback = null, $bIsChildItem = false)
	{
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = e.user_id');
		}			

		$sWhere = '';
		$sWhere .= ' and e.business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';
		$aRow = $this->database()->select('u.user_id, e.business_id, e.package_data, e.module_id, e.item_id, e.business_id, e.name, e.time_stamp, e.logo_path as image_path, e.server_id as image_server_id, e.total_like, e.total_comment, e.short_description_parsed as description_parsed, l.like_id AS is_liked')
			->from(Phpfox::getT('directory_business'), 'e')
			->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
			->leftJoin(Phpfox::getT('directory_business_text'), 'et', 'et.business_id = e.business_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'directory\' AND l.item_id = e.business_id AND l.user_id = ' . Phpfox::getUserId())
			->where('e.business_id = ' . (int) $aItem['item_id'] . $sWhere)
			->execute('getSlaveRow');
	
		if (!isset($aRow['business_id']))
		{
			return false;
		}
        $aRow['setting_support'] = Phpfox::getService('directory.permission')->getSettingSupportInBusiness($aRow['business_id'], $aRow);

		if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'directory.view_browse_business'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'directory.view_browse_business'))
		)
		{
			return false;
		}
		
		$aReturn = array(
			'feed_title' => $aRow['name'],
			'feed_info' => _p('directory.checked_in_this_business'),
			'feed_link' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']),
			'feed_content' => $aRow['description_parsed'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/directory.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],	
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'enable_like' => true,			
			'like_type_id' => 'directory_checkinhere',
			'total_comment' => $aRow['total_comment'],
            'custom_data_cache' => $aRow,
            'load_block' => 'directory.feed',
            'type_id' => 'directory'
		);
		if($aRow['setting_support']['allow_users_to_share_business'] == false){
			$aReturn['no_share'] = true;
		}

        if (!empty($aRow['image_path']))
        {
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aRow['image_path'],
                    'return_url' => true,
                    'suffix' => ''
                )
            );
        } else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/directory/static/image/default_ava.png';
        }

        // Strips all image in content
        list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['description_parsed'], 'img');
        $aReturn['feed_content'] = $sDescription;

        Phpfox_Component::setPublicParam('custom_param_directory_' . $aItem['feed_id'], ['aItem' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']),
            'aCategory' => Phpfox::getService('directory.category')->getMainCategoryByBusinessId($aRow['business_id'])
        ]);

		if ($bIsChildItem)
		{
			$aReturn = array_merge($aReturn, $aItem);
		}

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = db()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['group_id'])
                ->execute('getSlaveRow');

            if (empty($aPage)) {
                return false;
            }

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

		(($sPlugin = Phpfox_Plugin::get('directory.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

		return $aReturn;
	}

	public function getNotificationPostitem($aNotification)
	{
		$aRow = Phpfox::getService('directory')->getQuickBusinessById((int) $aNotification['item_id']);		
        if (!isset($aRow['business_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('directory.new_item_has_been_posted_on_title_business', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);        
		
		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationExpirenotify($aNotification)
	{
		$aRow = Phpfox::getService('directory')->getQuickBusinessById((int) $aNotification['item_id']);		
        if (!isset($aRow['business_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('directory.your_business_title_which_will_expire_very_soon_please_update_new_package', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);        
		
		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationUpdateinfobusiness($aNotification)
	{
		$aRow = Phpfox::getService('directory')->getQuickBusinessById((int) $aNotification['item_id']);		
        if (!isset($aRow['business_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('directory.business_title_which_you_are_following_it_has_been_updated_information', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);        
		
		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationApprove_Claimrequest($aNotification)
	{
		$aRow = Phpfox::getService('directory')->getQuickBusinessById((int) $aNotification['item_id']);		
        if (!isset($aRow['business_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('directory.your_claim_request_for_title_business_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);        
		
		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

	public function getNotificationApprove_Business($aNotification)
	{
		$aRow = Phpfox::getService('directory')->getQuickBusinessById((int) $aNotification['item_id']);		
        if (!isset($aRow['business_id']))
		{
			return false;
		}
		
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('directory.your_business_title_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);        
		
		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}

    // public function getNotificationSettings()
    // {
    //     return array(
    //     	'directory.notifications_for_new_created_businesses' =>array(
    //             'phrase' => _p('directory.notifications_for_new_created_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.notification_for_new_claimed_businesses' =>array(
    //             'phrase' => _p('directory.notification_for_new_claimed_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.approval_of_businesses' =>array(
    //             'phrase' => _p('directory.approval_of_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.denial_of_businesses' =>array(
    //             'phrase' => _p('directory.denial_of_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.new_activities_on_your_own_businesses' =>array(
    //             'phrase' => _p('directory.new_activities_on_your_own_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.transferring_owner_of_your_businesses' =>array(
    //             'phrase' => _p('directory.transferring_owner_of_your_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.confirming_of_work_here' =>array(
    //             'phrase' => _p('directory.confirming_of_work_here'),
    //             'default' => 1, 
    //         ),
    //     	'directory.expiration_of_your_businesses' =>array(
    //             'phrase' => _p('directory.expiration_of_your_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.changes_on_your_businesses' =>array(
    //             'phrase' => _p('directory.changes_on_your_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.feature_un_feature_your_businesses' =>array(
    //             'phrase' => _p('directory.feature_un_feature_your_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.new_activities_on_your_following_businesses' =>array(
    //             'phrase' => _p('directory.new_activities_on_your_following_businesses'),
    //             'default' => 1, 
    //         ),
    //     	'directory.approval_of_claim_requests' =>array(
    //             'phrase' => _p('directory.approval_of_claim_requests'),
    //             'default' => 1, 
    //         ),
    //     	'directory.denial_of_claim_requests' =>array(
    //             'phrase' => _p('directory.denial_of_claim_requests'),
    //             'default' => 1, 
    //         ),
    //     );
    // }

    public function getAjaxProfileController()
    {
        return 'directory.index';
    }

    public function getProfileLink()
    {
        return 'profile.directory';
    }

    public function getProfileMenu($aUser)
    {
        $aUser['total_business'] = Phpfox::getService('directory')->getNumberBusinessByUser($aUser['user_id']);

        if (!Phpfox::getParam('profile.show_empty_tabs'))
        {
            if (!isset($aUser['total_business']))
            {
                return false;
            }

            if (isset($aUser['total_business']) && (int) $aUser['total_business'] === 0)
            {
                return false;
            }
        }

        $aSubMenu = array();

        $aMenus[] = array(
            'phrase' => _p('directory.business_directory'),
            'url' => 'profile.directory',
            'total' => (int) (isset($aUser['total_business']) ? $aUser['total_business'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/directory.png',
            'icon_class' => 'ico ico-briefcase-o'
        );

        return $aMenus;
    }    

    public function getCouponsDetails($aItem)
    {
        // Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);

	    if (!isset($aRow['business_id']))
	    {
			return false;
	    }

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

        return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
            'module' => 'directory',
            'item' => $aRow['business_id'],
			'title' => $aRow['name'],
            'url_home' => $sLink,
            'url_home_pages' => $sLink . 'coupons/',
            'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
        );
    }

	public function getMarketplaceDetails($aItem)
	{
	    // Phpfox::getService('pages')->setIsInPage();
	    $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
	    if (!isset($aRow['business_id']))
	    {
			return false;
	    }

        // Check if login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($aRow['business_id']);
            if ($aBusiness['module_id'] == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

	    return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'marketplace/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
	    );
	}

	public function getJobDetails($aItem)
	{
	    // Phpfox::getService('pages')->setIsInPage();
	    $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
	    if (!isset($aRow['business_id']))
	    {
			return false;
	    }

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

	    return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'jobs/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
	    );
	}

	public function getPollDetails($aItem)
	{
	    // Phpfox::getService('pages')->setIsInPage();
	    $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
	    if (!isset($aRow['business_id']))
	    {
			return false;
	    }

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

	    return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'polls/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
	    );
	}

	public function viewEvent($iItem)
	{		
		$aRow = $this->addEvent($iItem);		
		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
		return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_pages' => $sLink . 'events/'
		);		
	}

	public function addEvent($iItem)
	{		
		// Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('directory')->getBusinessById($iItem);
		
		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		return $aRow;
	}

	public function addForum($iId)
	{
		// Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('directory')->getBusinessById($iId);
			
		if (!isset($aRow['business_id']))
		{
			return false;
		}			
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
		return array(
			'module' => 'directory',
			'item' => $aRow['business_id'],
			'group_id' => $aRow['business_id'],
			'url_home' => $sLink,
			'title' => $aRow['name'],
			'table_prefix' => 'directory_',
			'item_id' => $aRow['business_id']			
		);
	}

	public function getBlogDetails($aItem)
	{
	    // Phpfox::getService('pages')->setIsInPage();
	    $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
	    if (!isset($aRow['business_id']))
	    {
			return false;
	    }

	    $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

	    return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'blogs/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
	    );
	}

	public function getMusicDetails($aItem)
	{		
		// Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
			
		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		// Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
		return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'musics/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
		);
	}

    public function getVideoDetails($aItem)
    {
        // Phpfox::getService('pages')->setIsInPage();
        $aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);

        if (!isset($aRow['business_id'])) {
            return false;
        }

        // Check if login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($aRow['business_id']);
            if ($aBusiness['module_id'] == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }

        // Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);

        return array(
            'breadcrumb_title' => _p('directory.module_menu_business'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
            'module_id' => 'directory',
            'item_id' => $aRow['business_id'],
            'title' => $aRow['name'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'video',
            'theater_mode' => _p('directory.in_the_business_a_href_link_title_a',
                array('link' => $sLink, 'title' => $aRow['name']))
        );
    }

	public function uploadVideo($aVals)
	{
		// Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'directory',
			'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int) $aVals)
		);
	}	
	
	public function convertVideo($aVideo)
	{
		return array(
			'module' => 'directory',
			'item_id' => $aVideo['item_id'],
			'table_prefix' => 'directory_'
		);			
	}	

	public function getPhotoDetails($aPhoto)
	{
		// Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('directory')->getBusinessById($aPhoto['group_id']);

		if (!isset($aRow['business_id']))
		{
			return false;
		}

        // Check if login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($aRow['business_id']);
            if ($aBusiness['module_id'] == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }

		// Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
		return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'photos/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
		);
	}

	public function getPhotoCount($iBusinessId)
	{
		$iCnt = $this->database()->select('COUNT(*)')
					->from(Phpfox::getT('photo'))
					->where("module_id = 'directory' AND group_id = " . $iBusinessId)
					->execute('getSlaveField');
		
		return ($iCnt > 0) ? $iCnt : 0;
	}

    public function getDirectoryDetails($aItem)
    {
    	return array();
    }

	public function getTagTypeBusiness()
	{
		return 'business';
	}

	public function getTagCloud()
	{
		return array(
			'link' => 'directory',
			'category' => 'business'
		);
	}

	public function paymentApiCallback($aParams){
		Phpfox::log('Module callback recieved: ' . var_export($aParams, true));	
		Phpfox::log('Attempting to retrieve purchase from the database');		
		

		$aInvoice = Phpfox::getService('directory')->getInvoice($aParams['item_number']);
		

		if ($aInvoice === false)
		{
			Phpfox::log('Not a valid invoice');
			
			return false;
		}

		$aItem =  Phpfox::getService('directory')->getBusinessForEdit($aInvoice['item_id'], true);
		
		if ($aItem === false)
		{
			Phpfox::log('Not a valid listing.');
			
			return false;
		}
		
		Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));
		
		if ($aParams['status'] == 'completed')
		{
			if ($aParams['total_paid'] == $aInvoice['price'])
			{
				Phpfox::log('Paid correct price');
			}
			else 
			{
				Phpfox::log('Paid incorrect price');
				
				return false;
			}
		}
		else 
		{
			Phpfox::log('Payment is not marked as "completed".');
			
			return false;
		}
		
		$this->database()->update(Phpfox::getT('directory_invoice'), array(
				'status' => $aParams['status'],
				'param' => json_encode($aParams),
				'payment_method' => isset($aParams['gateway']) ? $aParams['gateway'] : '',
				'time_stamp_paid' => PHPFOX_TIME
			), 'invoice_id = ' . $aInvoice['invoice_id']
		);		
		// update data 
		switch ($aInvoice['type']) {
			case 'business':
					// create new business or submit 1 DRAFT 
					$aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);

					$pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);
					$start_time = PHPFOX_TIME;
					foreach($pay_type as $val){
						switch ($val) {
							case 'package':
								//update package info,if change

                                if(isset($aInvoice['invoice_data']['change_package_id'])){
                                		Phpfox::getService('directory.process')->updatePackageForBusiness($aInvoice['invoice_data']['change_package_id'],$aItem['business_id']);
                                }
								// update status 
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');
                                
                                if($aItem['creating_type'] == 'claiming'){
                                    $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                                } else if($aItem['package_start_time'] == 0 || $aItem['package_end_time'] == 0 ){
                                    //still not approved
                                    $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');
                                    if(Phpfox::getService('directory.helper')->getUserParam('directory.business_created_by_user_automatically_approved', (int)$aItem['user_id'])){
                                        $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                                    } else {
                                        $status = Phpfox::getService('directory.helper')->getConst('business.status.pending');
                                    }
                                }
                                else{
                                    //already approved
                                    $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                                }

								Phpfox::getService('directory.process')->updateBusinessStatus($aItem['business_id'], $status);

								if($status == Phpfox::getService('directory.helper')->getConst('business.status.approved')){
									// call approve function 
								Phpfox::getService('directory.process')->approveBusiness($aItem['business_id'], null);									
								}
                                (($sPlugin = Phpfox_Plugin::get('directory.service_callback_payment_publish__end')) ? eval($sPlugin) : false);
								break;

							case 'feature':
								$feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day'];
								Phpfox::getService('directory.process')->updateBusinessFeatureTime($aItem['business_id'], $aItem['feature_start_time'], $aItem['feature_end_time'], $feature_days);
								
								//case package free
								if(!in_array("package", $pay_type)){

									 $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');
                                    if(Phpfox::getService('directory.helper')->getUserParam('directory.business_created_by_user_automatically_approved', (int)$aItem['user_id'])){
                                        $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                                    } else {
                                        $status = Phpfox::getService('directory.helper')->getConst('business.status.pending');
                                    }

									Phpfox::getService('directory.process')->updateBusinessStatus($aItem['business_id'], $status);
									
									if($status == Phpfox::getService('directory.helper')->getConst('business.status.approved')){
									// call approve function 
									Phpfox::getService('directory.process')->approveBusiness($aItem['business_id'], null);									
									}
										
								}
                                (($sPlugin = Phpfox_Plugin::get('directory.service_callback_payment_feature__end')) ? eval($sPlugin) : false);
								break;
						}
					}

					break;
			case 'feature':
					// update featured time 
					$aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);
					$pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);

					foreach($pay_type as $val){
						switch ($val) {
							case 'feature':
								
								$feature_days = 0;
								/*not approved*/
								if($aItem['feature_start_time'] == 0 || $aItem['feature_end_time'] == 0 ){
									
									$start_time = 0;
									$end_time = 0;
									$feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day']; 
								}
								else{
								/*already approved*/
									if(PHPFOX_TIME < $aItem['feature_end_time']){	//still in feature,wanna expend featured time.								
									
										$start_time = $aItem['feature_start_time'];
										$end_time =   $aItem['feature_end_time'] + (int)$aInvoice['invoice_data']['feature_days']*86400 ;
										$feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day']; 	
									}
									else{

										$start_time = PHPFOX_TIME; 
										$feature_days = (int)$aInvoice['invoice_data']['feature_days']; 	
										$end_time =   $start_time + $feature_days*86400;
									}
								}
								
								if($end_time >= 4294967295){
									$end_time = 4294967295;
								}
							
								Phpfox::getService('directory.process')->updateBusinessFeatureTime($aItem['business_id'], $start_time, $end_time, $feature_days);
                                (($sPlugin = Phpfox_Plugin::get('directory.service_callback_payment_feature__end')) ? eval($sPlugin) : false);
								break;
						}
					}
					break;
		}
		
		// send email (refer Marketplace module)
		
		Phpfox::log('Handling complete');		
	}

	public function getFeedDisplay($business_id)
	{
		return array(
			'module' => 'directory',
			'table_prefix' => 'directory_',
			'ajax_request' => 'directory.addFeedComment',
			'item_id' => $business_id
		);
	}

	public function getAjaxCommentVar()
	{
		return 'directory.can_comment_on_business';
	}	

	public function getActivityFeedComment($aItem)
	{

		$aRow = $this->database()->select('fc.*, l.like_id AS is_liked, e.business_id, e.name')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'directory_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId())			
			->where('fc.feed_comment_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		

		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::getLib('url')->permalink(array('directory.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['business_id'], $aRow['name']);
		
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
			'comment_type_id' => 'directory',
			'like_type_id' => 'directory_comment',
			'parent_user_id' => 0
		);

        if (!empty($aRow['location_name'])) {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng'])) {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }

		return $aReturn;		
	}	

	public function deleteComment($iId)
	{
		$this->database()->updateCounter('directory_business', 'total_comment', 'business_id', $iId, true);
		
	}

	public function addLikeComment($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.business_id, e.name')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->where('fc.feed_comment_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'directory_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'directory_feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::getLib('url')->permalink(array('directory.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['business_id'], $aRow['name']);
			$sItemLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('directory.full_name_liked_a_comment_you_posted_on_the_business_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
				->message(array('directory.full_name_liked_your_comment_a_href_link_content_a_that_you_posted_on_the_business_a_href_item_link_title_a_to_view_this_business_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'), 'item_link' => $sItemLink, 'title' => $aRow['name'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('directory_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
		}
	}		

	public function deleteLikeComment($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'directory_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'directory_feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
	}

	public function addPhoto($iId)
	{
		return array(
			'module' => 'directory',
			'item_id' => $iId,
			'table_prefix' => 'directory_'
		);
	}	

	public function addLink($aVals)
	{
		return array(
			'module' => 'directory',
			'item_id' => $aVals['callback_item_id'],
			'table_prefix' => 'directory_'
		);		
	}

	public function addLikeCheckinhere($iItemId, $bDoNotSendEmail = false)
	{
		$this->addLike($iItemId);
	}

	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('business_id, name, user_id')
			->from(Phpfox::getT('directory_business'))
			->where('business_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'directory\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'directory_business', 'business_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('directory.full_name_liked_your_business_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
				->message(array('directory.full_name_liked_your_business_a_href_link_title_a_to_view_this_business_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['name'])))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('directory_like', $aRow['business_id'], $aRow['user_id']);				
		}		
	}

	public function deleteLikeCheckinhere($iItemId)
	{
		$this->deleteLike($iItemId);
	}

	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'directory\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'directory_business', 'business_id = ' . (int) $iItemId);
	}

	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('e.business_id, e.name, e.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('directory_business'), 'e')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('e.business_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['business_id']))
		{
			return false;
		}			
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('directory.users_liked_gender_own_business_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('directory.users_liked_your_business_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('directory.users_liked_span_class_drop_data_user_row_full_name_039_s_span_business_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	

	public function canShareItemOnFeed(){}	

	public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
	{
        if (Phpfox::isUser()) {
            db()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'directory\' AND l.item_id = e.business_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            db()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = e.user_id');
        }

		$sWhere = ' and e.business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';
		$aRow = $this->database()->select('u.user_id, e.business_id, e.package_data, e.module_id, e.item_id, e.business_id, e.name, e.time_stamp, e.logo_path as image_path, e.server_id as image_server_id, e.total_like, e.total_comment, e.short_description_parsed as description_parsed, l.like_id AS is_liked')
			->from(Phpfox::getT('directory_business'), 'e')
			->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
			->leftJoin(Phpfox::getT('directory_business_text'), 'et', 'et.business_id = e.business_id')
			->where('e.business_id = ' . (int) $aItem['item_id'] . $sWhere)
			->execute('getSlaveRow');

        if (!isset($aRow['business_id'])) {
            return false;
        }

        $aRow['setting_support'] = Phpfox::getService('directory.permission')->getSettingSupportInBusiness($aRow['business_id'], $aRow);

        /**
         * Check active parent module
         */
        if (!empty($aBlog['module_id']) && !Phpfox::isModule($aBlog['module_id'])) {
            return false;
        }

        // Check parent permission
		if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'directory.view_browse_business'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'directory.view_browse_business'))
		)
		{
			return false;
		}

        $aRow['group_id'] = $aRow['item_id'];
        $aItem['item_id'] = $aRow['business_id'];
        $aReturn = array_merge(
            array(
                'feed_title' => $aRow['name'],
                'feed_info' => _p('directory.created_an_business'),
                'feed_link' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']),
                'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/directory.png', 'return_url' => true)),
                'time_stamp' => $aRow['time_stamp'],
                'feed_total_like' => $aRow['total_like'],
                'feed_is_liked' => $aRow['is_liked'],
                'enable_like' => true,
                'like_type_id' => 'directory',
                'total_comment' => $aRow['total_comment'],
                'custom_data_cache' => $aRow,
                'load_block' => 'directory.feed',
                'type_id' => 'directory'
            ), $aItem
        );
		if($aRow['setting_support']['allow_users_to_share_business'] == false){
			$aReturn['no_share'] = true;
		}
		
		if (!empty($aRow['image_path']))
		{
            $sImageSrc = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['image_server_id'],
					'path' => 'core.url_pic',
					'file' => $aRow['image_path'],
					'return_url' => true,
					'suffix' => ''
				)
			);
		} else {
            $sImageSrc = Phpfox::getParam('core.path_file') . 'module/directory/static/image/default_ava.png';
        }

        // Strips all image in content
        list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['description_parsed'], 'img');
        $aReturn['feed_content'] = $sDescription;

        Phpfox_Component::setPublicParam('custom_param_directory_' . $aItem['feed_id'], ['aItem' => $aRow,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('directory.detail', $aRow['business_id'], $aRow['name']),
            'aCategory' => Phpfox::getService('directory.category')->getMainCategoryByBusinessId($aRow['business_id'])
        ]);
		
        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = db()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['group_id'])
                ->execute('getSlaveRow');

            if (empty($aPage)) {
                return false;
            }

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }
		
		(($sPlugin = Phpfox_Plugin::get('directory.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

		return $aReturn;
	}	

	public function getFeedDetails($iItemId)
	{
		return array(
			'module' => 'directory',
			'table_prefix' => 'directory_',
			'item_id' => $iItemId
		);		
	}	

	public function getCommentItem($iId)
	{		
		$aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('directory_feed_comment'))
			->where('feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');		
		
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('directory.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}		
		
		$aRow['parent_module_id'] = 'directory';
			
		return $aRow;
	}	

	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.business_id, e.name, u.full_name, u.gender')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->where('fc.feed_comment_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('directory_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);		
		}
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink(array('directory.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['business_id'], $aRow['name']);
		$sItemLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['feed_comment_id'],
				'owner_subject' => _p('directory.full_name_commented_on_a_comment_posted_on_the_business_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])),
				'owner_message' => _p('directory.full_name_commented_on_one_of_your_comments_you_posted_on_the_business_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'directory_comment_feed',
				'mass_id' => 'directory',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('directory.full_name_commented_on_one_of_gender_business_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('directory.full_name_commented_on_one_of_row_full_name_s_business_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('directory.full_name_commented_on_one_of_gender_own_comments_on_the_business_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)) : _p('directory.full_name_commented_on_one_of_row_full_name_s_comments_on_the_business_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)))
			)
		);
	}

	public function getNotificationComment_Feed($aNotification)
	{
		return $this->getCommentNotification($aNotification);	
	}

	public function getCommentNotification($aNotification)
	{
	
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.business_id, e.name')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
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
				$sPhrase = _p('directory.users_commented_on_span_class_drop_data_user_row_full_name_s_span_comment_on_the_business_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = _p('directory.users_commented_on_gender_own_comment_on_the_business_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('directory.users_commented_on_one_of_your_comments_on_the_business_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('directory.users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_comments_on_the_business_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink(array('directory.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['business_id']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}		

	public function getNotificationComment($aNotification)
	{
		
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.business_id, e.name')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')			
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if(!count($aRow)){
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = _p('directory.users_commented_on_span_class_drop_data_user_row_full_name_s_span_business_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' =>  $sTitle));
			}
			else 
			{
				$sPhrase = _p('directory.users_commented_on_gender_own_business_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('directory.users_commented_on_your_business_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('directory.users_commented_on_span_class_drop_data_user_row_full_name_s_span_business_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationComment_Like($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.business_id, e.name')
			->from(Phpfox::getT('directory_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('directory_business'), 'e', 'e.business_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if(!count($aRow)){
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = _p('directory.users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_business_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = _p('directory.users_liked_gender_own_comment_on_the_business_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('directory.users_liked_one_of_your_comments_on_the_business_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('directory.users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_business_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink(array('directly_detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['business_id'], $aRow['name']),
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
				'item_type_id' => 'directory', // used to differentiate between photo albums and photos for example.
				'table' => 'directory_business',
				'item_phrase' => _p('directory.item_phrase'),
				'column_update' => 'total_dislike',
				'column_find' => 'business_id'				
				)
		);
	}	

	public function updateCommentText($aVals, $sText)
	{
		
	}

	public function getSiteStatsForAdmin($iStartTime, $iEndTime)
	{
		$aCond = array();
		$aCond[] = ' business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';
		if ($iStartTime > 0)
		{
			$aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
		}	
		if ($iEndTime > 0)
		{
			$aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
		}			
		
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('directory_business'))
			->where($aCond)
			->execute('getSlaveField');
		
		return array(
			'phrase' => 'directory.businesses',
			'total' => $iCnt
		);
	}	

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$sWhere = '';
		$sWhere .= ' and business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';
		
		return array(
			'phrase' => _p('directory.businesses'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('directory_business'))
				->where('time_stamp >= ' . $iToday . $sWhere)
				->execute('getSlaveField')
		);
	}

	// public function pendingApproval()
	// {
	// }

	public function globalUnionSearch($sSearch)
	{
		$sWhere = '';
		$sWhere .= ' and item.business_status IN ( ' . Phpfox::getService('directory.helper')->getConst('business.status.running') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.completed') . ',' . Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' ) ';

		$this->database()->select('item.business_id AS item_id, item.name AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'directory\' AS item_type_id, item.logo_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('directory_business'), 'item')
			->where(' 1=1 ' . $sWhere . ' AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.name', $sSearch))
			->union();
	}

	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('directory.detail', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('directory.business');
		
        $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => $aRow['item_photo'],
				'path' => 'core.url_pic',
				'suffix' => '_200',
				'max_width' => '120',
				'max_height' => '120'				
			)
		);
        
		return $aInfo;
	}

	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('directory.businesses')
		);
	}

	/**
	 *  Call back methods for report and comment on directory business
	 */
	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aBusiness = $this->database()->select('dbus.business_id, dbus.name')
					->from(Phpfox::getT('directory_business'), 'dbus')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
					->where('dbus.business_id = ' . (int) $iId)
					->execute('getSlaveRow');		
					
		if (!isset($aBusiness['business_id']))
		{
			return false;
		}				
		
		return Phpfox::permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);
	}
	
	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}

	public function getNotificationInvited($aNotification)
	{
			
		$aRow = $this->database()->select('dbus.business_id, dbus.name')
		->from(Phpfox::getT('directory_business'), 'dbus')
		->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
		->where('dbus.business_id = ' . (int) $aNotification['item_id'])
		->execute('getSlaveRow');	

		if(!$aRow) return false;
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = _p('directory.users_invited_you_to_the_business_title', array('users' => $sUsers,'title' => $sTitle));
						
		return array(
			'link' => Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function uploadUltimateVideo($aVals)
	{
		
		return array(
			'module' => 'directory',
			'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int) $aVals)
		);
	}	
	public function getUltimateVideoDetails($aItem)
	{		
		// Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('directory')->getBusinessById($aItem['item_id']);
			
		if (!isset($aRow['business_id']))
		{
			return false;
		}
		
		// Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getLib('url')->permalink('directory.detail', $aRow['business_id'], $aRow['name']);
			
		return array(
			'breadcrumb_title' => _p('directory.module_menu_business'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('directory'),
			'module_id' => 'directory',
			'item_id' => $aRow['business_id'],
			'title' => $aRow['name'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'videos/',
			'theater_mode' => _p('directory.in_the_business_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
		);
	}

	public function getItem($iItemId)
    {
        if (!$iItemId) {
            return null;
        }
        $aRow = Phpfox::getService('directory')->getQuickBusinessById($iItemId);
        $aRow['module'] = 'directory';
        $aRow['title'] = $aRow['name'];
        $aRow['module_title'] = _p('module_title');

        return $aRow;
    }

    public function checkPermission($iItemId, $sPermission)
    {
        // Check if login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aBusiness = Phpfox::getService('directory')->getBusinessById($iItemId);
            if ($aBusiness['module_id'] == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }
        switch ($sPermission) {
            case 'ynblog.share_blogs':
                $bAllow = Phpfox::getService('directory.permission')->canAddAdvBlogInBusiness($iItemId, $bRedirect = false);
                break;
            case 'ynblog.view_browse_ynblogs':
                $bAllow = Phpfox::getService('directory.permission')->canViewAdvBlogInBusiness($iItemId, $bRedirect = false);
                break;
            default:
                $bAllow = true;
                break;
        }

        return $bAllow;
    }

    public function getUploadParamsBusiness($aParams = null)
    {
        $iRemainImage = $aParams['remain_upload'];
        $iMaxFileSize = Phpfox::getParam('directory.max_upload_size_photos');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.yndirectory.dropzoneOnSending',
            'success' => '$Core.yndirectory.dropzoneOnSuccess',
            'queuecomplete' => '$Core.yndirectory.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('directory.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'yndirectory' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'yndirectory' . PHPFOX_DS,
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(100, 120, 200, 400)
        ];
    }

    /**
     * @return array
     */
    public function getUploadParamsBusiness_Logo() {
        $iMaxFileSize = Phpfox::getParam('directory.max_upload_size_photos');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;

        return [
            'label' => _p('logo'),
            'max_size' => $iMaxFileSize,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'yndirectory' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'yndirectory' . PHPFOX_DS,
            'thumbnail_sizes' => array(100, 200, 400),
        ];
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('directory')->getPendingTotal();

        return [
            'message'=> _p('you_have_total_pending_directory', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => \Phpfox_Url::instance()->makeUrl('admincp.directory.managebusiness', array('search' => 'pending'))
        ];
    }
}