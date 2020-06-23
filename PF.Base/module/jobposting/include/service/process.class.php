<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class JobPosting_Service_Process extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        
    }
    
	public function sendInvitations($sType, $iId, $aVals)
	{
		$aItem = array();
		$sTitle = '';
		$sLink = '';
		
		if ($sType == 'job')
        {
            $aItem = Phpfox::getService('jobposting.job')->getGeneralInfo($iId);
            $sTitle = $aItem['title'];
            $sLink = Phpfox::getLib('url')->permalink('jobposting', $aItem['job_id'], $aItem['title']);
        }
        elseif ($sType == 'company')
        {
            $aItem = Phpfox::getService('jobposting.company')->getGeneralInfo($iId);
            $sTitle = $aItem['name'];
            $sLink = Phpfox::getLib('url')->permalink('jobposting.company', $aItem['company_id'], $aItem['name']);
        }
		
		if (isset($aVals['emails']) || isset($aVals['invite']))
		{
			$aInvites = $this->database()->select('invited_user_id, invited_email')
				->from(Phpfox::getT('jobposting_invite'))
				->where('item_id = ' . (int) $iId . ' AND item_type = "'.$sType.'"')
				->execute('getRows');
			$aInvited = array();
			foreach ($aInvites as $aInvite)
			{
				$aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
			}			
		}
		
		$iSendCnt = 0;
		
		if (isset($aVals['emails']))
		{
			// if (strpos($aVals['emails'], ','))
			{
				$sSubject = _p('full_name_invited_you_to_the_type_title', array(
					'full_name' => Phpfox::getUserBy('full_name'),
		            'type' => $sType,
					'title' => $sTitle,
				));
		        
		        $sMessage = _p('full_name_invited_you_to_the_type_title_link', array(
		            'full_name' => Phpfox::getUserBy('full_name'),
		            'type' => $sType,
		            'title' => $sTitle,
		            'link' => $sLink
				));
				
				$aEmails = explode(',', $aVals['emails']);
				$aCachedEmails = array();
				
				foreach ($aEmails as $sEmail)
				{
					$sEmail = trim($sEmail);
					if (!Phpfox::getLib('mail')->checkEmail($sEmail))
					{
						continue;
					}
					
					if (isset($aInvited['email'][$sEmail]))
					{
						continue;
					}
					
					if (!empty($aVals['subject']))
					{
						$sSubject = $aVals['subject'];
					}
					
					if (!empty($aVals['personal_message']))
					{
						$sMessage = $aVals['personal_message'];
					}
					
					$oMail = Phpfox::getLib('mail');
					if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1)
					{
						$oMail->fromEmail(Phpfox::getUserBy('email'))->fromName(Phpfox::getUserBy('full_name'));
					}
					$bSent = $oMail->to($sEmail)->subject($sSubject)->message($sMessage)->send();
                    
					if ($bSent)
					{
						$this->_aInvited[] = array('email' => $sEmail);
						
						$aCachedEmails[$sEmail] = true;
						
						$this->database()->insert(Phpfox::getT('jobposting_invite'), array(
								'item_type' => $sType,
								'item_id' => $iId,
								'type_id' => 1,
								'user_id' => Phpfox::getUserId(),
								'invited_email' => $sEmail,
								'time_stamp' => PHPFOX_TIME
							)
						);
						
						$iSendCnt++;
					}
				}
			}
		}
		
		if (isset($aVals['invite']) && is_array($aVals['invite']))
		{
			$sUserIds = '';
			foreach ($aVals['invite'] as $iUserId)
			{
				if (!is_numeric($iUserId))
				{
					continue;
				}
				$sUserIds .= $iUserId . ',';
			}
			$sUserIds = rtrim($sUserIds, ',');
			
			$aUsers = $this->database()->select('user_id, email, language_id, full_name')
				->from(Phpfox::getT('user'))
				->where('user_id IN(' . $sUserIds . ')')
				->execute('getSlaveRows');
				
			foreach ($aUsers as $aUser)
			{
				if (isset($aCachedEmails[$aUser['email']]))
				{
					continue;
				}	
				
				if (isset($aInvited['user'][$aUser['user_id']]))
				{
					continue;
				}
				
				$sSubject = _p('full_name_invited_you_to_the_type_title', array(
					'full_name' => Phpfox::getUserBy('full_name'),
		            'type' => $sType,
					'title' => $sTitle,
				), false, null, $aUser['language_id']);
		        
		        $sMessage = _p('full_name_invited_you_to_the_type_title_link', array(
		            'full_name' => Phpfox::getUserBy('full_name'),
		            'type' => $sType,
		            'title' => $sTitle,
		            'link' => $sLink
				), false, null, $aUser['language_id']);
				
				$bSent = Phpfox::getLib('mail')->to($aUser['user_id'])						
					->subject($sSubject)
					->message($sMessage)
					->notification('jobposting.invite'.$sType)
					->send();
						
				if ($bSent)
				{
					$this->_aInvited[] = array('user' => $aUser['full_name']);	
					
					$iInviteId = $this->database()->insert(Phpfox::getT('jobposting_invite'), array(
							'item_type' => $sType,
							'item_id' => $iId,								
							'user_id' => Phpfox::getUserId(),
							'invited_user_id' => $aUser['user_id'],
							'time_stamp' => PHPFOX_TIME
						)
					);
					
					$iSendCnt++;
					
					(Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('jobposting_invite_'.$sType, $iId, $aUser['user_id']) : null);
				}
			}
		}

		$sMsg = ($iSendCnt > 0) ? _p('invitations_have_been_sent_successfully') : '';
		Phpfox::getLib('url')->send($sLink, null, $sMsg);
	}
    
    public function favorite($sType, $iId, $iUserId)
    {
        $aSql = array(
            'item_type' => $sType,
            'item_id' => $iId,
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        );
        
        $iInsertId = $this->database()->insert(Phpfox::getT('jobposting_favorite'), $aSql);
        
        if(!$iInsertId)
        {
            return false;
        }
        
        $this->updateTotalFavorite($sType, $iId);
        
        $this->addNotification('favorite', $sType, $iId, $iUserId, true, true, false);
        
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_process_favorite_end')) ? eval($sPlugin) : false);
       
        return $iInsertId;
    }
    
    public function unfavorite($sType, $iId, $iUserId)
    {
        $this->database()->delete(Phpfox::getT('jobposting_favorite'), 'item_type = "'.$sType.'" AND item_id = '.(int)$iId.' AND user_id = '.(int)$iUserId);
        
        $this->updateTotalFavorite($sType, $iId);
        
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_process_unfavorite_end')) ? eval($sPlugin) : false);
        
        return true;
    }
    
    public function updateTotalFavorite($sType, $iId)
    {
        $iTotal = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('jobposting_favorite'))
            ->where('item_type = "'.$sType.'" AND item_id = '.(int)$iId)
            ->execute('getSlaveField');
        
        $this->database()->update(Phpfox::getT('jobposting_'.$sType), array('total_favorite' => $iTotal), $sType.'_id = '.$iId);
        
        return true;
    }

    public function follow($sType, $iId, $iUserId)
    {
        $aSql = array(
            'item_type' => $sType,
            'item_id' => $iId,
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        );
        
        $iInsertId = $this->database()->insert(Phpfox::getT('jobposting_follow'), $aSql);
        
        if(!$iInsertId)
        {
            return false;
        }
        
        $this->addNotification('follow', $sType, $iId, $iUserId, true, false, false);
         PHpfox::getService("jobposting.company.process")->updateTotalJob($iId,$sType);
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_process_follow_end')) ? eval($sPlugin) : false);
        
        return $iInsertId;
    }

    public function unfollow($sType, $iId, $iUserId)
    {
        $this->database()->delete(Phpfox::getT('jobposting_follow'), 'item_type = "'.$sType.'" AND item_id = '.(int)$iId.' AND user_id = '.(int)$iUserId);
		
        PHpfox::getService("jobposting.company.process")->updateTotalJob($iId,$sType);
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_process_unfollow_end')) ? eval($sPlugin) : false);
        
        return true;
    }

    public function addNotification($sAction, $sItemType, $iItemId, $iUserId, $bToOwner = false, $bToFollower = false, $bToApplicant = false)
    {
        if (!Phpfox::isModule('notification'))
        {
            return false;
        }
        
        if (empty($iUserId))
        {
            $iUserId = Phpfox::getUserId();
        }
        
        if ($bToOwner)
        {
            $iOwner = Phpfox::getService('jobposting')->getOwner($sItemType, $iItemId);
			
            if ($iOwner)
            {
 		if((int)$iUserId <= 0){
        		$iUserId = $iOwner;
    		}
                Phpfox::getService('notification.process')->add('jobposting_'.$sAction.$sItemType, $iItemId, $iOwner, $iUserId);
            }
            if ($sItemType == 'company')
            {
                $aAdmin = Phpfox::getService('jobposting.company')->getAdmins($iItemId);
                if (is_array($aAdmin) && count($aAdmin))
                {
                    foreach ($aAdmin as $iAdmin)
                    {
			if((int)$iUserId <= 0){
                 		$iUserId = $iAdmin;
             		}
                        Phpfox::getService('notification.process')->add('jobposting_'.$sAction.$sItemType, $iItemId, $iAdmin, $iUserId);
                    }
                }
            }
        }
        
        if ($bToFollower)
        {
            $aFollower = Phpfox::getService('jobposting')->getFollowers($sItemType, $iItemId);
            if (is_array($aFollower) && count($aFollower))
            {
                foreach ($aFollower as $iFollower)
                {
			if((int)$iUserId <= 0){
             			$iUserId = $iFollower;
        		}
                    Phpfox::getService('notification.process')->add('jobposting_'.$sAction.'followed'.$sItemType, $iItemId, $iFollower, $iUserId);
                }
            }
        }
        
        if ($bToApplicant)
        {
            $aApplicant = Phpfox::getService('jobposting')->getApplicants($sItemType, $iItemId);
            if (is_array($aApplicant) && count($aApplicant))
            {
                foreach ($aApplicant as $iApplicant)
                {
			if((int)$iUserId <= 0){
             			$iUserId = $iApplicant;
         		}
                    Phpfox::getService('notification.process')->add('jobposting_'.$sAction.'applied'.$sItemType, $iItemId, $iApplicant, $iUserId);
                }
            }
        }
        
        return true;
    }
    
    public function sendEmail($sAction, $sItemType, $iItemId, $iOwner)
    {
        $sTitle = Phpfox::getService('jobposting')->getItemTitle($sItemType, $iItemId);
        $sLink = Phpfox::getLib('url')->permalink(($sItemType == 'company') ? 'jobposting.company' : 'jobposting', $iItemId, $sTitle);

        $sSubject = _p(''.$sAction.'_'.$sItemType.'_email_subject');
        $sMessage = _p(''.$sAction.'_'.$sItemType.'_email_message', array(
            'title' => $sTitle,
            'link' => $sLink
        ));

        Phpfox::getLib('mail')->to($iOwner)->subject($sSubject)->message($sMessage)->send();

        if ($sItemType == 'company')
        {
            $aAdmin = Phpfox::getService('jobposting.company')->getAdmins($iItemId);
            if (is_array($aAdmin) && count($aAdmin))
            {
                foreach ($aAdmin as $iAdmin)
                {
                    Phpfox::getLib('mail')->to($iAdmin)->subject($sSubject)->message($sMessage)->send();
                }
            }
        }

        return true;

    }

    public function addWorkingCompanyRequest($userID = null, $companyID = null){
    	if($companyID == null){
    		return false;
    	}
    	if($userID == null){
    		$userID = Phpfox::getUserId();
    	}

		$id = $this->database()->insert(Phpfox::getT('jobposting_company_working_request'), array(
			'user_id' => $userID,
			'company_id' => $companyID,
		));

		return $id;
    }

    public function deleteWorkingCompanyRequest($userID = null, $companyID = null){
    	if($companyID == null){
    		return false;
    	}
    	if($userID == null){
    		return false;
    	}

    	$this->database()->delete(Phpfox::getT('jobposting_company_working_request')
    		, ' 1=1 AND company_id = '.(int)$companyID.' AND user_id = '.(int)$userID);

    	return true;
    }
    
}

?>