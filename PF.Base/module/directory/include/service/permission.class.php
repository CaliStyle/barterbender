<?php 
defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Permission extends Phpfox_Service
{

	public function getSettingSupportInBusiness($iBusinessId, $aBusiness = null){
		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getBusinessById($iBusinessId);		
		}

		$setting = array(
			'allow_business_owner_to_add_new_content_pages' => false, 
			'allow_users_to_confirm_working_at_the_business' => false, 
			'allow_users_to_share_business' => false, 
			'allow_users_to_invite_friends_to_business' => false, 
			'allow_business_owner_to_edit_contact_form' => false, 
			'allow_business_owner_to_add_more_custom_fields_to_his_business' => false, 
		);
		$key_setting = array_keys($setting);

		$aPackage = json_decode($aBusiness['package_data'], true);
		$aSettingId = array();
		if(isset($aPackage['settings'])){
			foreach ($aPackage['settings'] as $keysettings => $valuesettings) {
				$aSettingId[] = $valuesettings['setting_id'];
			}	
		}
		

		$aSetting = Phpfox::getService('directory')->getSettingByIds($aSettingId);
		foreach ($aSetting as $key => $value) {
			if(in_array($value['setting_name'], $key_setting)){
				$setting[$value['setting_name']] = true;
			}
		}		

		return $setting;
	}

	public function doShowSettingInBusiness($setting_name, $iBusinessId, $aBusiness = null, $aModuleView = null){
		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getBusinessById($iBusinessId);		
		}
		if(null == $aModuleView){
			$aModuleView = Phpfox::getService('directory')->getModuleViewInBusiness($iBusinessId, $aBusiness);		
		}

		switch ($setting_name) {
			case 'share_a_photo':
			case 'view_browse_photos':
				if(Phpfox::getService('directory.helper')->isPhoto()){
					if(isset($aModuleView['photos']) && $aModuleView['photos']['is_show']){
						return true;
					}
				}
				break;
			
			case 'share_an_event':
			case 'view_browse_events':
				if(Phpfox::getService('directory.helper')->isEvent()){
					if(isset($aModuleView['events']) && $aModuleView['events']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_poll':
			case 'view_browse_polls':
				if(Phpfox::getService('directory.helper')->isPoll()){
					if(isset($aModuleView['polls']) && $aModuleView['polls']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_video':
			case 'view_browse_videos':
				if(Phpfox::getService('directory.helper')->isVideoChannel()){
					if(isset($aModuleView['videos']) && $aModuleView['videos']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_music':
			case 'view_browse_musics':
				if(Phpfox::getService('directory.helper')->isMusic()){
					if(isset($aModuleView['musics']) && $aModuleView['musics']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_marketplace_item':
			case 'view_browse_marketplace_items':
				if(Phpfox::getService('directory.helper')->isMarketplace()){
					if(isset($aModuleView['marketplace']) && $aModuleView['marketplace']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_blog':
			case 'view_browse_blogs':
				if(Phpfox::getService('directory.helper')->isBlog()){
					if(isset($aModuleView['blogs']) && $aModuleView['blogs']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_job':
			case 'view_browse_jobs':
				if(Phpfox::getService('directory.helper')->isJob()){
					if(isset($aModuleView['jobs']) && $aModuleView['jobs']['is_show']){
					return true;
					}
				}
				break;
			
			case 'share_a_coupon':
			case 'view_browse_coupons':
				if(Phpfox::getService('directory.helper')->isCoupon()){
					if(isset($aModuleView['coupons']) && $aModuleView['coupons']['is_show']){
					return true;
					}
				}
				break;
			
			default:
				return true;
				break;
		}
		return false;
	}

	public function canCheckinhere($iBusinessId, $bRedirect = false){
		if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
			$aCheckinhere = Phpfox::getService('directory')->getCheckinhere($iBusinessId, Phpfox::getUserId());
			if(isset($aCheckinhere['checkinhere_id']) == false){
				return true;
			}
		}
			
		return false;				
	}

	public function canApproveBusiness($bRedirect = false){
		if(Phpfox::getUserParam('directory.can_approve_business', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;		
	}
	
	public function canClaimBusiness($bRedirect = false){
		if(Phpfox::getUserParam('directory.can_claim_business', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;		
	}
	
	public function canCreateBusiness($bRedirect = false){
		if(Phpfox::getUserParam('directory.can_create_business', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;		
	}

	public function canCreateBusinessForClaiming($bRedirect = false){
		$aCreator = Phpfox::getService('directory')->getCreatorByUserId(Phpfox::getUserId());
		if(isset($aCreator['user_id'])){
			return true;
		}

		if(Phpfox::getUserParam('directory.can_create_business_for_claiming', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;		
	}

	public function canCreateBusinessWithLimit(){
		$countBusiness = Phpfox::getService('directory')->countBusinessOfUserId(Phpfox::getUserId());
		$limit = (int)Phpfox::getUserParam('directory.number_business_user_can_create');
		if($countBusiness < $limit){
			return true;
		}

		return false;
	}

	public function canDeleteBusiness($iUserId, $bRedirect = false){
		if((($iUserId == Phpfox::getUserId() && Phpfox::getUserParam('directory.can_delete_own_business', $bRedirect)) || Phpfox::getUserParam('directory.can_delete_others_business', $bRedirect))) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;				
	}

	public function canEditBusiness($iUserId,$iBusinessId,$bRedirect = false){
	    if (!Phpfox::getUserParam('directory.can_edit_business')) {
	        return false;
        }
		if(Phpfox::isUser() == false) {
			return false;
		}

		if((($iUserId == Phpfox::getUserId() && Phpfox::getUserParam('directory.can_edit_business', $bRedirect)))) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'view_dashboard'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canViewBusiness($bRedirect = false){
		if(Phpfox::getUserParam('directory.can_view_business', $bRedirect)) {
			// if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			// }
		}
			
		return false;				
	}

	public function canAddPhotoInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isPhoto()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_photo'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewPhotoInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isPhoto()){
			return false;
		}
		// check business show this menu
		list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('photos' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_photos'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canViewVideoInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null,$isUlt = false){
		if($isUlt){
            if(!Phpfox::getService('directory.helper')->isUltVideo()){
                return false;
            }
        }
        else{
            if(!(Phpfox::getService('directory.helper')->isVideo() || Phpfox::getService('directory.helper')->isVideoChannel())){
                return false;
            }
        }
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('v' == $value){
				$hasMenu = true;
				break;
			}
		}

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_videos'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddVideoInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null,$isUlt = false){
		if($isUlt){
            if(!Phpfox::getService('directory.helper')->isUltVideo()){
                return false;
            }
        }
        else{
            if(!Phpfox::getService('directory.helper')->isVideo() && !Phpfox::getService('directory.helper')->isVideoChannel()){
                return false;
            }

        }
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_video'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewMusicInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isMusic()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('musics' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_musics'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddMusicInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isMusic()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_music'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewBlogInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isBlog()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('blogs' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_blogs'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddBlogInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isBlog()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_blog'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

    public function canViewAdvBlogInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
        if(!Phpfox::getService('directory.helper')->isAdvBlog()){
            return false;
        }
        // check business show this menu
        if(null == $listPageMenu || null == $keyLandingPage){
            list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
        }
        $hasMenu = false;
        foreach ($listPageMenu as $key => $value) {
            if('advanced-blog' == $value){
                $hasMenu = true;
                break;
            }
        }

        // check user can do action on business
        if($hasMenu){
            if($roleOfUser == null){
                $roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
            }
            foreach ($roleOfUser as $keyRole => $valueRole) {
                if($valueRole['setting_name'] == 'view_browse_ynblogs'){
                    $status = $valueRole['status'];
                    if($status == 'no'){
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function canAddAdvBlogInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
        if(!Phpfox::getService('directory.helper')->isAdvBlog()){
            return false;
        }
        if(Phpfox::isUser() == false) {
            return false;
        }

        // check business show this menu - NOT check, view permission has to do this
        // check user can do action on business
        if($roleOfUser == null){
            $roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
        }
        foreach ($roleOfUser as $keyRole => $valueRole) {
            if($valueRole['setting_name'] == 'share_a_ynblog'){
                $status = $valueRole['status'];
                if($status == 'no'){
                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

	public function canViewDiscussionInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('discussion' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_discussions'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddDiscussionInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_discussion'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewPollsInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isPoll()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('polls' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_polls'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddPollsInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isPoll()){
			return false;
		}

		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_poll'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewEventInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isEvent()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('events' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_events'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddEventInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isEvent()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_an_event'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewJobInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isJob()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('jobs' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_jobs'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}	

	public function canAddJobInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isJob()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_job'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewMarketplaceInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isMarketplace()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('marketplace' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_marketplace_items'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddMarketplaceInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isMarketplace()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_marketplace_item'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canViewCouponInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null, $listPageMenu = null, $keyLandingPage = null){
		if(!Phpfox::getService('directory.helper')->isCoupon()){
			return false;
		}
		// check business show this menu
		if(null == $listPageMenu || null == $keyLandingPage){
			list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($iBusinessId, null);
		}
		$hasMenu = false;
		foreach ($listPageMenu as $key => $value) {
			if('coupons' == $value){
				$hasMenu = true;
				break;
			}
		}				

		// check user can do action on business 
		if($hasMenu){
			if($roleOfUser == null){
				$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
			}
			foreach ($roleOfUser as $keyRole => $valueRole) {
				if($valueRole['setting_name'] == 'view_browse_coupons'){
					$status = $valueRole['status'];
					if($status == 'no'){
						return false;
					} else {
						return true;
					}
				}
			}
		}

		return false;						
	}

	public function canAddCouponInBusiness($iBusinessId, $bRedirect = false, $roleOfUser = null){
		if(!Phpfox::getService('directory.helper')->isCoupon()){
			return false;
		}
		if(Phpfox::isUser() == false) {
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		if($roleOfUser == null){
			$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);
		}
		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'share_a_coupon'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;						
	}

	public function canManageBusinessDashBoard($iBusinessId, $aBusiness = null){


		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}
		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'view_dashboard'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	
	}

	public function canViewPageInsightDashBoard($iBusinessId, $aBusiness = null){

		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}
		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'view_page_insight_dashboard'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}
	public function canManageEditInfoDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'edit_business_information'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canManageCoverPhotosDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'manage_cover_photos'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canManagePagesDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'manage_pages'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canAddMemberRoleDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'add_member_roles'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canChangeMemberRoleDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'change_member_roles'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canConfigureSettingRoleDashBoard($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}
		
		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}
		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'configure_setting_member_roles'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canManageModule($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'manage_modules'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}
	public function canManageAnnouncement($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'manage_announcements'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}
	public function canChangeBusinessTheme($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}

		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}

		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'change_business_theme'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canUpdatePackage($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}


		if($aBusiness['user_id'] == Phpfox::getUserId()){
			return true;
		}
		
		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'update_package'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canInviteMember($iBusinessId, $aBusiness = null){
		if(Phpfox::isUser() == false) {
			return false;
		}

		if(null == $aBusiness){
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		}

		// if((int)$aBusiness['package_id'] <= 0){
		if($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft')){
			return false;
		}
		// check business show this menu - NOT check, view permission has to do this 
		// check user can do action on business 
		$roleOfUser = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $iBusinessId);

		foreach ($roleOfUser as $keyRole => $valueRole) {
			if($valueRole['setting_name'] == 'invite_member'){
				$status = $valueRole['status'];
				if($status == 'no'){
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function canReviewBusiness($iOwnerBusinessId,$iBusinessId){

		if(Phpfox::isUser() == false) {
			return false;
		}

		if(Phpfox::getUserId() == $iOwnerBusinessId) {
			return false;
		}

		//check user is already review
		$aReview = Phpfox::getService('directory')->getExistingReview($iBusinessId,Phpfox::getUserId());
		
		if(count($aReview)){
			return false;
		}

		if(Phpfox::getUserParam('directory.can_rate_business')){
			return true;
		}

		return false;

	}


}