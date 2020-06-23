<?php

defined('PHPFOX') or exit('NO DICE!');

class Jobposting_Service_Permission extends Phpfox_service
{
    public function canViewJob($iJob, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iJob);
        
        if (!$aJob)
        {
            return false;
        }
		
	if($aJob['is_deleted']==1)
            return false;
        
        if (!Phpfox::getUserParam('jobposting.can_approve_job'))
		{
			if ($aJob['is_approved'] != '1' && $aJob['user_id'] != $iUserId)
			{
				return false;
			}
		}
		
		if(PHpfox::isAdmin())
		{
			return true;
		}
		
		if ($aJob['post_status'] != '1' && $iUserId != $aJob['user_id'])
		{
			return false;
		}
        
        return true;
    }

    public function canEditJob($job_id, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);

        if (!$aJob)
        {
            return false;
        }

		if($aJob['time_expire']<=PHPFOX_TIME){
			return false;
		}
		
		// check permission ADMIN of company
        $edit_job = Phpfox::getService('jobposting.company')->isAdminPermission($aJob['company_id'], $iUserId, 'edit_job');	
		
        if (($iUserId == $aJob['user_id'] && Phpfox::getUserParam('jobposting.can_edit_own_job')) || Phpfox::getUserParam('jobposting.can_edit_job_created_by_other_users') || $edit_job)
        {
			return true;				
        }

        return false;
    }

    public function canDeleteJob($job_id, $iUserId = 0)
    {
        if (!$iUserId)
        {
        	$iUserId = Phpfox::getUserId();
        }

        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);

        if (!$aJob)
        {
            return false;
        }
		
		// check permission ADMIN of company
        $delete_job = Phpfox::getService('jobposting.company')->isAdminPermission($aJob['company_id'], $iUserId, 'delete_job');
		
        if ($aJob['user_id'] == $iUserId || Phpfox::getUserParam('jobposting.can_delete_job_other_user') || $delete_job)
        {
			return true;				
           
        }

        return false;
    }

    public function canApproveJob($job_id, $iUserId = 0)
    {
        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($job_id);

        if (!$aJob)
        {
            return false;
        }

        if ($aJob['is_approved'] != 1 && Phpfox::getUserParam('jobposting.can_approve_job'))
		{
            return true;
		}

        return false;
    }

    public function canFeatureJob($job_id, $iUserId = 0)
    {
        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);

        if (!$aJob)
        {
            return false;
        }
		
        if ($aJob['post_status'] == 1 && $aJob['is_approved'] == 1 && $aJob['is_featured'] != 1)
        {
        	if($aJob['user_id']==Phpfox::getUserId() && Phpfox::getUserParam('jobposting.can_feature_jobs'))
            	return true;
			if(PHpfox::isAdmin())
				return true;
        }

        return false;
    }
	
	public function canunFeatureJob($job_id, $iUserId = 0)
    {
        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);

        if (!$aJob)
        {
            return false;
        }

        if ($aJob['is_featured']==1 && Phpfox::isAdmin())
        {
            return true;
        }

        return false;
    }
    
    public function canFeaturePublishedJob($iJob = 0, $iUserId = 0)
    {
        if (empty($iJob)) //add
		{
	        if (!Phpfox::getUserParam('jobposting.approved_job_before_displayed') && Phpfox::getUserParam('jobposting.can_feature_jobs'))
			{
	            return true;
			}
		}
		else //edit
		{
			$aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iJob);
	        if (!$aJob)
	        {
	            return false;
	        }
			
	        if ($aJob['is_approved'] && !$aJob['is_featured'] && Phpfox::getUserParam('jobposting.can_feature_jobs'))
	        {
	            return true;
	        }
		}
		
        return false;
    }
    
    public function canViewCompany($iCompany, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }
        
		if(PHpfox::isAdmin())
		{
			return true;
		}
		
        if (!Phpfox::getUserParam('jobposting.can_approve_company'))
		{
			if (($aCompany['is_approved'] != '1' || !$aCompany['is_activated'])&& $aCompany['user_id'] != $iUserId)
			{
				return false;
			}
		}
		
		if ($aCompany['post_status'] != '1' && $iUserId != $aCompany['user_id'])
		{
			return false;
		}
        
        return true;
    }

    public function canEditCompany($iCompany, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);
        if (!$aCompany)
        {
            return false;
        }

        if ((($iUserId == $aCompany['user_id'] || Phpfox::getService('jobposting.company')->isAdmin($iCompany, $iUserId)) && Phpfox::getUserParam('jobposting.can_edit_own_company')) || Phpfox::getUserParam('jobposting.can_edit_user_company'))
        {
            return true;
        }

        return false;
    }
	
	public function canActivatedCompany($iCompany, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }
				
		
        if (($iUserId == $aCompany['user_id'] || Phpfox::getService('user')->isAdminUser($iUserId)) && $aCompany['is_approved'] && !$aCompany['is_activated'])
        {
            return true;
        }

        return false;
    }
	
	public function canBuyPackages($iCompany, $iUserId = 0)
    {
        $buy_packages= Phpfox::getService('jobposting.company')->isAdminPermission($iCompany, $iUserId, 'buy_packages');
        return $buy_packages;
    }

    public function canDectivatedCompany($iCompany, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }


        if (($iUserId == $aCompany['user_id'] || Phpfox::getService('user')->isAdminUser($iUserId)) && $aCompany['is_approved'] && $aCompany['is_activated'])
        {
            return true;
        }

        return false;
    }

    public function canDeleteCompany($company_id, $iUserId = 0)
    {
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }

        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($company_id);
		
        if (!$aCompany)
        {
            return false;
        }

        if (($iUserId == $aCompany['user_id'] && Phpfox::getUserParam('jobposting.can_delete_own_company')) || Phpfox::getUserParam('jobposting.can_delete_company_other_user'))
        {
            return true;
        }

        return false;
    }

    public function canApproveCompany($iCompany, $iUserId = 0)
    {
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }

        if ($aCompany['is_approved'] != 1 && Phpfox::getUserParam('jobposting.can_approve_company'))
		{
            return true;
		}

        return false;
    }

    public function canSponsorCompany($iCompany, $iUserId = 0)
    {
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }

        if ($aCompany['post_status'] == 1 && $aCompany['is_approved'] == 1 && $aCompany['is_sponsor'] != 1)
		{
			if($aCompany['user_id']==PHpfox::getUserId() && Phpfox::getUserParam('jobposting.can_sponsor_company'))
            	return true;
			if(PHpfox::isAdmin())
				return true;
		}

        return false;
    }

    public function canunSponsorCompany($iCompany, $iUserId = 0)
    {
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);

        if (!$aCompany)
        {
            return false;
        }

        if ($aCompany['is_sponsor'] == 1 && PHpfox::isAdmin())
		{
            return true;
		}

        return false;
    }
	
	public function canSponsorPublishedCompany($iCompany = 0, $iUserId = 0)
	{
		if (empty($iCompany)) //add
		{
	        if (!Phpfox::getUserParam('jobposting.approve_company_before_displayed') && Phpfox::getUserParam('jobposting.can_sponsor_company'))
			{
	            return true;
			}
		}
		else //edit
		{
			$aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);
	        if (!$aCompany)
	        {
	            return false;
	        }
			
	        if ($aCompany['is_approved'] && !$aCompany['is_sponsor'] && Phpfox::getUserParam('jobposting.can_sponsor_company'))
	        {
	            return true;
	        }
		}
		
        return false;
	}

    public function allPermissionForJob($aJob, $user_id)
    {
		if (!$user_id)
		{
			$user_id = Phpfox::getUserId();
		}
		
        $aJob['canEditJob'] = $this->canEditJob($aJob['job_id'], $user_id);
        $aJob['canDeleteJob'] = $this->canDeleteJob($aJob['job_id'], $user_id);
        $aJob['canApproveJob'] = $this->canApproveJob($aJob['job_id'], $user_id);
        $aJob['canFeatureJob'] = $this->canFeatureJob($aJob['job_id'], $user_id);
		$aJob['canunFeatureJob'] = $this->canunFeatureJob($aJob['job_id'], $user_id);
		
        $aJob['action'] = 0;
        
        if ($aJob['canEditJob'] || $aJob['canDeleteJob'] || $aJob['canApproveJob'] || $aJob['canFeatureJob'] || $aJob['canunFeatureJob'])
        {
            $aJob['action'] = 1;
        }

        return $aJob;
    }
	
    public function allPermissionForCompany($aCompany, $user_id)
    {
    	if (!$user_id)
		{
			$user_id = Phpfox::getUserId();
		}
		
        $aCompany['canEditCompany'] = $this->canEditCompany($aCompany['company_id'], $user_id);
        $aCompany['canDeleteCompany'] = $this->canDeleteCompany($aCompany['company_id'], $user_id);
        $aCompany['canApproveCompany'] = $this->canApproveCompany($aCompany['company_id'], $user_id);
        $aCompany['canSponsorCompany'] = $this->canSponsorCompany($aCompany['company_id'], $user_id);
		$aCompany['canunSponsorCompany'] = $this->canunSponsorCompany($aCompany['company_id'], $user_id);
		$aCompany['canActivatedCompany'] = $this->canActivatedCompany($aCompany['company_id'], $user_id);
		$aCompany['canDectivatedCompany'] = $this->canDectivatedCompany($aCompany['company_id'], $user_id);

        $aCompany['canBuyPackages'] =  $this->canBuyPackages($aCompany['company_id'], $user_id);
        $aCompany['canSubmissionForm'] =Phpfox::getService('jobposting.company')->isAdminPermission($aCompany['company_id'], $user_id, 'edit_submission_form');
        
        $aCompany['action'] = 0;

        if ($aCompany['canEditCompany'] || $aCompany['canDeleteCompany'] || $aCompany['canApproveCompany'] || $aCompany['canSponsorCompany'] || $aCompany['canunSponsorCompany'])
        {
            $aCompany['action'] = 1;
        }

        return $aCompany;
    }

    public function isCompanyOwnerOrAdmin($companyID = null){
        if($companyID == null){
            return false;
        }

        if(Phpfox::isAdmin()){
            return true;
        }

        $ownerID = Phpfox::getService('jobposting')->getOwner('company', $companyID);
        if(Phpfox::getUserId() == $ownerID){
            return true;
        }

        if(Phpfox::getService('jobposting.company')->isAdmin($companyID, Phpfox::getUserId())){
            return true;
        }

        return false;
    }
}
