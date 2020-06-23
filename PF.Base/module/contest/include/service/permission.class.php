<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Permission extends Phpfox_service {
	/**
	 * this function will check whether in this context, user can perform edit action
	 * @by minhta
	 * @param int $iUserId is 0 if this user is a guest
	 * @return
	 */		
	public function canEditContest($iContestId, $iUserId = 0)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}


		// no way to edit a Contest in uneditable status
		if(in_array($aContest['contest_status'], Phpfox::getService('contest.constant')->getUneditableStatus()))
		{
			return false;
		}

		// if he is the owner and he has permission to edit his own Contest
		if(($iUserId == $aContest['user_id'] && Phpfox::getUserParam('contest.edit_own_contest')) || Phpfox::getUserParam('contest.edit_user_contest')) 
		{

			return true;
		}

		return false;

		// // page owner is a big BOSS
		// if ($aContest['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aContest['item_id']))
		// {		
		// 	return true;
		// }	

		// return false;

	}

	public function canFeatureContest($iContestId)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')
			)))
		{
			return false;
		}

		if(Phpfox::getUserParam('contest.can_feature_contest')) 
		{
			return true;
		}
		else 
		{
			return false;
		}

		return true;
	}

	public function canPremiumContest($iContestId)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')
			)))
		{
			return false;
		}

		if((Phpfox::getUserParam('contest.can_premium_contest') || 
			($aContest['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aContest['item_id']))))
		{
			return true;
		}
		else 
		{
			return false;
		}

		return true;
	}

	public function canEndingSoonContest($iContestId)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')
			)))
		{
			return false;
		}

		if((Phpfox::getUserParam('contest.can_ending_soon_contest') || 
			($aContest['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aContest['item_id']))))
		{
			return true;
		}
		else 
		{
			return false;
		}

		return true;
	}

        public function canActiveContest($iContestId)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!Phpfox::isAdmin()){
                    return false;
                }

		return true;
	}

	public function canCloseContest($iContestId, $iUserId)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')
			)))
		{
			return false;
		}


		if((Phpfox::getUserParam('contest.can_close_contest_added_by_other_users') || 
				($aContest['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('contest.can_close_own_contest')))) 
		{
			return true;
		}

		return false;
	}


	public function canDeleteContest($iContestId, $iUserId = 0)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}
		
		if(!in_array($aContest['contest_status'], Phpfox::getService('contest.constant')->getDeletableStatus()))
		{
			return false;
		}

		if($aContest['user_id']  == Phpfox::getUserId() && Phpfox::getUserParam('contest.can_delete_own_contest'))
		{
			return true;
		}

		if($aContest['user_id']  != Phpfox::getUserId() && Phpfox::getUserParam('contest.can_delete_other_contests'))
		{
			return true;
		}


		return false;

	}


	public function canPublishContest($iContestId, $iUserId = 0)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if($aContest['is_published'])
		{
			return false;
		}

		if($aContest['contest_status'] == Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'))
		{
			return false;
		}

		if(!Phpfox::getUserParam('contest.can_publish_contest'))
		{
			return false;
		}
		return true;

		// // no way to edit a Contest in uneditable status
		// if(in_array($aContest['status'], Phpfox::getService('fundraising.Contest')->getUneditableStatus()))

	}

	public function canCreateContest()
	{

		if(Phpfox::getUserParam('contest.add_new_contest')) 
		{
			return true;
		}
		else 
		{
			return false;
		}

		return true;


	}

	public function canApproveDenyContest($iContestId, $iUserId = 0)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending')
			)))
		{
			return false;
		}

		if(Phpfox::getUserParam('contest.can_approve_contest')) 
		{
			return true;
		}
		else 
		{
			return false;
		}

		return false;
	}

	public function canRegisterService($iContestId, $iUserId)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		// only owner can register services
		if(Phpfox::getUserId() != $aContest['user_id'])
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('draft'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')

			)))
		{
			return false;
		}

		if($aContest['is_published'] && $aContest['is_premium'] && $aContest['is_feature'] && $aContest['is_ending_soon'] )
		{
			return false;
		}

		$aFees = Phpfox::getService('contest.contest')->getAllFees();
		if($aContest['is_published'] && $aFees['publish'] <= 0 && $aFees['premium'] <= 0 && $aFees['ending_soon'] <= 0 )
		{
			return false;
		}



		return true;
	}


	public function canSubmitEntry($iContestId, $iUserId)
	{
		if(!$iUserId)
		{
			return false;
		}

		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!Phpfox::getUserParam('contest.can_submit_entry_for_contest'))
		{
			return false;
		}

		if(!Phpfox::getService('contest.participant')->isJoinedContest($iUserId, $iContestId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if($aContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'))
		{
			return false;
		}
        
        if (PHPFOX_TIME < $aContest['start_time'] || PHPFOX_TIME > $aContest['stop_time'])
        {
            return false;
        }

		if(isset($aContest['can_submit_entry']) && !$aContest['can_submit_entry'])
		{
			return false;
		}

		$iTotalEntries = Phpfox::getService('contest.entry')->getNumberOfSumittedEntryInAContestOfUser($iContestId, $iUserId);

		if($aContest['number_entry_max'] != 0 && $aContest['number_entry_max'] <= $iTotalEntries)
		{
			return false;
		}

		return true;
	}


	public function canJoinContest($iContestId, $iUserId)
	{
		if(!$iUserId)
		{
			return false;
		}

		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!Phpfox::getUserParam('contest.can_join_contest'))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if($aContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'))
		{
			return false;
		}
        
        if (PHPFOX_TIME < $aContest['begin_time'])
        {
            return false;
        }

		return true;
	}

	public function canFollowContest($iContestId, $iUserId)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed')
			)))
		{
			return false;
		}


		return true;
	}

	public function canFavoriteContest($iContestId, $iUserId)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed')
			)))
		{
			return false;
		}

		return true;
	}

	public function canInviteFriend($iContestId, $iUserId)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')
			)))
		{
			return false;
		}


		return true;
	}

	public function canHideAction($aContest){
		 	
		$is_hidden_action = 1;
		
		if($aContest['user_id']==PHpfox::getUserId() || PHpfox::isAdmin()){
			$is_hidden_action = 0;
		}
		
		return $is_hidden_action;
	}

	public function canViewBrowseContest($iContestId, $iUserId = 0)
	{
		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);
                
		if(!$this->canViewBrowseContestModule())
		{
			return false;
		}
                
                if(!$aContest['is_active'] && PHpfox::getUserId()!=$aContest['user_id'] && !PHpfox::isAdmin()){
                    return false;
                }
                
		if (Phpfox::isModule('privacy'))
		{
			if(!Phpfox::getService('privacy')->check('contest', $aContest['contest_id'], $aContest['user_id'], $aContest['privacy'], $aContest['is_friend'], true))		
			{
				return false;
			}
		}


		// if not owner, not admin, contes must be closed or on-going to watch
		if(	$aContest['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin() &&
			!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed'),
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going')

			)))
		{
			return false;
		}


		
		return true;
	}

	public function canViewBrowseContestModule()
	{
		if(!Phpfox::getUserParam('contest.view_contests'))
		{
			return false;
		}
                
		return true;
	}

	public function canViewWinningEntriesActionLink($iContestId, $iUserId)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

		if(!$aContest)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed') )) || 
			$aContest['user_id'] != Phpfox::getUserId()
			)
		{
			return false;
		}


		return true;
	}

	public function canViewEntryDetail($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(!in_array($aEntry['status_entry'], array(
				Phpfox::getService('contest.constant')->getEntryStatusIdByStatusName('approved') )) && 
				$iUserId != $aEntry['user_id'] &&
				$iUserId != $aContest['user_id'] && 
				!Phpfox::isAdmin()
			)
		{
			return false;
		}


		return true;

	}

	public function canVoteEntry($iEntryId, $iUserId)
	{
		if(!Phpfox::getUserParam('contest.can_vote_entry'))
		{
			return false;
		}

		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if($aEntry['status_entry'] != Phpfox::getService('contest.constant')->getEntryStatusIdByStatusName('approved'))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);
		
        if (!$aContest)
        {
            return false;
        }
        
		if($aContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'))
		{
			return false;
		}

        if (PHPFOX_TIME < $aContest['start_vote'] || PHPFOX_TIME > $aContest['stop_vote'])
        {
            return false;
        }

		if(!$aContest['vote_without_join'] && !Phpfox::getService('contest.participant')->isJoinedContest($iUserId, $aEntry['contest_id']))
		{
			return false;
		}

		return true;
	}

	public function canApproveEntry($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(	$iUserId != $aContest['user_id'] &&
				!Phpfox::isAdmin()
			)
		{
			return false;
		}

		if(in_array($aEntry['status_entry'], array(
				Phpfox::getService('contest.constant')->getEntryStatusIdByStatusName('approved') ))
			)
		{
			return false;
		}

		return true;

	}

	public function canDenyEntry($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(	$iUserId != $aContest['user_id'] &&
				!Phpfox::isAdmin()
			)
		{
			return false;
		}


		if(in_array($aEntry['status_entry'], array(
				Phpfox::getService('contest.constant')->getEntryStatusIdByStatusName('denied') ))
			)
		{
			return false;
		}


		return true;

	}

	public function canSetWinningEntry($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(	$iUserId != $aContest['user_id'] &&
				!Phpfox::isAdmin()
			)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed') )) 
			)
		{
			return false;
		}

		if(in_array($aEntry['status_entry'], array(
				Phpfox::getService('contest.constant')->getEntryStatusIdByStatusName('denied') ))
			)
		{
			return false;
		}
		
		if(Phpfox::getService("contest.entry")->CheckExistEntryWinning($iEntryId))
		{
			return false;
		}	

		return true;	
	}

	public function canDeleteEntry($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(	$iUserId != $aContest['user_id'] &&
				!Phpfox::isAdmin()
			)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed') )) 
			)
		{
			return false;
		}

		if(Phpfox::getService("contest.entry")->CheckExistEntryWinning($iEntryId))
		{
			return false;
		}	

		return true;	
	}

	public function canRemoveEntryFromWinningList($iEntryId, $iUserId)
	{
		$aEntry = Phpfox::getService('contest.entry')->getEntryForCheckingPermission($iEntryId);

		if(!$aEntry)
		{
			return false;
		}

		if(!$this->canViewBrowseContest($aEntry['contest_id'], $iUserId))
		{
			return false;
		}

		$aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($aEntry['contest_id']);

		if(	$iUserId != $aContest['user_id'] &&
				!Phpfox::isAdmin()
			)
		{
			return false;
		}

		if(!in_array($aContest['contest_status'], array(
				Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed') )) 
			)
		{
			return false;
		}

		if(!Phpfox::getService("contest.entry")->CheckExistEntryWinning($iEntryId))
		{
			return false;
		}	

		return true;	
	}


}