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
class Resume_Service_Process extends Phpfox_Service
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_basicinfo');
	}
	
	 public function typesesstion($iId)
	 {
	 	$value = 1;
	 	switch(Phpfox::getLib("module")->getFullControllerName())
		{
			case 'resume.add':
				$value = 1;
				break;
			default: 
				$value = 10;
		}
		$aListShowMore = array("resume.add","resume.summary","resume.experience","resume.education","resume.skill");
		$_SESSION['showmenu'] = 0;
		if(!in_array(Phpfox::getLib("module")->getFullControllerName(), $aListShowMore))
		{
			$_SESSION['showmenu'] = 1;
		}
		return Phpfox::getService("resume.basic.process")->updatePositionSection($iId,$value);
	
	 }
	 
	 /**
	  * Delete resume
	  * @param int $iId - the Id of the resume need to be deleted
	  * @return true
	  */
	 public function delete($iId)
	 {
	 	// Delete Favorite
	 	$this->removeAllFavorite($iId);
		
	 	// Delete Addtional
	 	Phpfox::getService('resume.addition.process')->delete($iId);
		
	 	// Delete Publications
	 	Phpfox::getService('resume.publication.process')->delete($iId);
		
	 	// Delete Languages
	 	Phpfox::getService('resume.language.process')->delete($iId);
		
	 	// Delete Certifications
	 	Phpfox::getService('resume.certification.process')->delete($iId);
		
	 	// Delete Skills
	 	
	 	// Delete Education
	 	Phpfox::getService('resume.education.process')->delete($iId);
		
	 	// Delete Experience
	 	Phpfox::getService('resume.experience.process')->delete($iId);
		
	 	// Delete Basicinfo and Summary 
	 	Phpfox::getService('resume.basic.process')->delete($iId);
		// Delete SCategory Data
		$aCategoriesData = Phpfox::getService('resume.category')->getCategoriesData($iId);
		foreach($aCategoriesData as $category)
		{
			Phpfox::getService('resume.category.process')->updateUsedCategory($category['category_id'],-1);
		}
		
		Phpfox::getService("resume.category.process")->deleteAllCategorydata($iId);
			
		// Delete ViewMe
	 	Phpfox::getService('resume.viewme.process')->delete($iId);
		return true;
	 }
	 
	 /**
	  * Approve resume
	  * @param int $iId - the Id of the resume need to be approved
	  * @return true
	  */
	 public function approve($iId)
	 {
	 	$aResume = Phpfox::getService('resume.basic')->getQuick($iId);
		if($aResume)
		{
	 		$this->database()->update($this->_sTable,array('status'=> 'approved'), 'resume_id = ' . $iId);
			
			// Add notification
			Phpfox::getService('notification.process')->add('resume_approve', $aResume['resume_id'], $aResume['user_id']);
			(($sPlugin = Phpfox_Plugin::get('resume.service_process_approve_end')) ? eval($sPlugin) : false);
		}
	 }
	 
	  /**
	  * Deny resume
	  * @param int $iId - the Id of the resume will be denied
	  * @return true
	  */
	 public function deny($iId)
	 {
	 	$aResume = Phpfox::getService('resume.basic')->getQuick($iId);
		if($aResume)
		{
	 		$this->database()->update($this->_sTable,array('status'=> 'denied'), 'resume_id = ' . $iId);
		// Add notification
			Phpfox::getService('notification.process')->add('resume_deny', $aResume['resume_id'], $aResume['user_id']);
		}	
	 }
	 
	 /**
	  * Remove all favorite to the related resume
	  * @param int $iId - the Id of the resume need to be removed
	  * @return true 
	  */
	 public function removeAllFavorite($iId)
	 {
	 	$this->database()->delete(Phpfox::getT('resume_favorite'), 'resume_id = ' . (int) $iId);
	 	return true;
	 }
	 
	 /**
	  * Publish a resume on the site
	  * @param array $aVals - array of input information
	  * @return true
	  */
	 public function setPublish($aResume = array())
	 {
	 	if ($aResume)
		{
			// Set publish and update flag of selected resume
			$aUpdate = array(
					'is_published'	=> '1', 
					'time_publish' 	=> PHPFOX_TIME
			);
			(($sPlugin = Phpfox_Plugin::get('resume.service_process_setpublish')) ? eval($sPlugin) : false);
			if($aResume['status'] == 'none' || $aResume['status'] == 'denied')
			{
				if(Phpfox::getUserParam('resume.checking_approval_mode'))
				{
					$aUpdate['status'] = 'approving';
				}
				else
				{
                    $iTotalPublishedResume = Phpfox::getService('resume.basic')->getTotalPublishedResumes($aResume['user_id']);
                    $aResumeUser = Phpfox::getService('user')->getUser($aResume['user_id']);
                    $iLimitResume = Phpfox::getUserGroupParam($aResumeUser['user_group_id'], 'resume.limit_maximum_resume_active');
                    
                    if ($iLimitResume > 0 && $iTotalPublishedResume >= $iLimitResume)
                    {
                        return false;
                    }
                    
					$aUpdate['status'] = 'approved';
				}
			}
			$this->database()->update($this->_sTable, $aUpdate, "resume_id= {$aResume['resume_id']}");
			(($sPlugin = Phpfox_Plugin::get('resume.service_process_setpublish_end')) ? eval($sPlugin) : false);
			return true;
		}
		
		return false;
	 }
	 
	 /**
	  * Set private a resume on the site
	  * @param int $iId - the Id of the resume need to set private
	  * @return true
	  */
	 public function setPrivate($iId)
	 {
	 	$this->database()->update($this->_sTable, array('is_published'=> '0', 'time_publish' => '0'),'resume_id = '.$iId);
		return true;
	 }
	 
	 /**
	  * Check email address exist
	  */
	  public function check_email($email) {  
            if (strlen($email) == 0) return false;
            if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4})$/", $email)) return true;
            return false;
      }
	  
	  public function check_number($number)
	  {
	  	    $number = preg_replace("/[0-9]+/", "", $number);
			if(strlen($number)==0) 
	  			return true;
            return false;
	  }
	  
	  /**
	   * Import from LinedIn
	   * 
	   */
	   
	   public function Import($ynrm_linkedin_data)
	   {

	   		$aVals = array();
	   		if(isset($ynrm_linkedin_data->dateOfBirth))
			{
				$aVals['birthday'] = (array)$ynrm_linkedin_data->dateOfBirth;
				$aVals['day'] = $aVals['birthday']['day'];
				$aVals['month'] = $aVals['birthday']['month'];
				$aVals['year'] = $aVals['birthday']['year'];
			}
			else {
				$aVals['day'] = $aVals['month'] = $aVals['year'] = null; 
			}
			$aVals['full_name'] = $ynrm_linkedin_data->firstName . " ". $ynrm_linkedin_data->lastName;
			$aVals['gender'] = 0;
			$aVals['marital_status'] = 'other';
			$aVals['emailaddress'] = array();
			if(isset($ynrm_linkedin_data->emailAddress))
			{
				$aVals['emailaddress'][] = $ynrm_linkedin_data->emailAddress;
			}
			$aVals['homepage'] = array();
			$aVals['phone'] = array();
			if(isset($ynrm_linkedin_data->phoneNumbers))
			{
				$length = $ynrm_linkedin_data->phoneNumbers->_total;
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->phoneNumbers->values[$i];
					$aVals['phone'][] = $row->phoneNumber;
					$aVals['phonestyle'][]  = $row->phoneType;
				}
			}
			if(isset($ynrm_linkedin_data->imAccounts))
			{
				$length = $ynrm_linkedin_data->imAccounts->_total;
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->imAccounts->values[$i];
					$aVals['homepage'][] = $row->imAccountName;
					$aVals['homepagestyle'][]  = $row->imAccountType;
				}
			}
			
			if(isset($ynrm_linkedin_data->pictureUrl))
			{
				$aVals['picture_url'] = $ynrm_linkedin_data->pictureUrl;
				
			}
			$aVals['headline'] = $ynrm_linkedin_data->headline;
			$aVals['summary'] =isset($ynrm_linkedin_data->summary)?$ynrm_linkedin_data->summary:"";
			return $aVals;
	   }

	   
	   public function importEducation($ynrm_linkedin_data,$resume_id)
	   {
	   		if(isset($ynrm_linkedin_data->educations))
			{
				$length = $ynrm_linkedin_data->educations->_total;
				
				for($i=0;$i<$length;$i++)
				{
					$aVals = array();
					$aVals['resume_id'] = $resume_id;
					$row = $ynrm_linkedin_data->educations->values[$i];
					if(isset($row->schoolName))
						$aVals['school_name']=$row->schoolName;
					if(isset($row->activities))
						$aVals['activity']=$row->activities;
					if(isset($row->degree))
						$aVals['degree']=$row->degree;
					if(isset($row->notes))
						$aVals['note']=$row->notes;
					if(isset($row->fieldOfStudy))
						$aVals['field']=$row->fieldOfStudy;
					if(isset($row->startDate->year))
						$aVals['start_year']=$row->startDate->year;
					if(isset($row->endDate->year))
						$aVals['end_year']=$row->endDate->year;
					Phpfox::getService("resume.education.process")->add($aVals);
				}
			}
	   }
	   
	   public function importskills($ynrm_linkedin_data,$resume_id)
	   {
	   		if(isset($ynrm_linkedin_data->skills))
			{
				$length = $ynrm_linkedin_data->skills->_total;
				$aVals = array();
				$aVals['resume_id'] = $resume_id;
				$aVals['kill_list'] = "";
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->skills->values[$i];
					
					if(isset($row->skill->name))
					{
						$aVals['kill_list'].=$row->skill->name;
						if($i<$length-1)
							$aVals['kill_list'].=",";
					}
				}
			
				if($aVals['kill_list']!="")
					Phpfox::getService("resume.skill.process")->updateBasicSkill($aVals);
			}			
	   }
	   
	   public function importExperience($ynrm_linkedin_data,$resume_id)
	   {
	   		if(isset($ynrm_linkedin_data->threeCurrentPositions))
			{
				$length = $ynrm_linkedin_data->threeCurrentPositions->_total;
			
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->threeCurrentPositions->values[$i];
					$aVals = array();
					$aVals['resume_id'] = $resume_id;
					if(isset($row->company->name))
					{
						$aVals['company_name']=$row->company->name;
					}
					if(isset($row->startDate->month))
					{
						$aVals['start_month']=$row->startDate->month;
					}
					if(isset($row->startDate->year))
					{
						$aVals['start_year']=$row->startDate->year;
					}
					if(isset($row->title))
					{
						$aVals['title']=$row->title;
					}
					if(isset($row->summary))
					{
						$aVals['description']=$row->summary;
					}
					if(isset($row->isCurrent))
					{
						if($row->isCurrent==1)
						{
							$aVals['is_working_here']="on";
						}
						else {
							if(isset($row->endDate->month))
							{
								$aVals['end_month']=$row->endDate->month;
							}
							if(isset($row->endDate->year))
							{
								$aVals['end_year']=$row->endDate->year;
							}
						}
					}
					Phpfox::getService("resume.experience.process")->add($aVals);
				}
			}

			if(isset($ynrm_linkedin_data->threePastPositions))
			{
				$length = $ynrm_linkedin_data->threePastPositions->_total;
			
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->threePastPositions->values[$i];
					$aVals = array();
					$aVals['resume_id'] = $resume_id;
					if(isset($row->company->name))
					{
						$aVals['company_name']=$row->company->name;
					}
					if(isset($row->startDate->month))
					{
						$aVals['start_month']=$row->startDate->month;
					}
					if(isset($row->startDate->year))
					{
						$aVals['start_year']=$row->startDate->year;
					}
					if(isset($row->title))
					{
						$aVals['title']=$row->title;
					}
					if(isset($row->summary))
					{
						$aVals['description']=$row->summary;
					}
					if(isset($row->isCurrent))
					{
						if($row->isCurrent==1)
						{
							$aVals['is_working_here']="on";
						}
						else {
							if(isset($row->endDate->month))
							{
								$aVals['end_month']=$row->endDate->month;
							}
							if(isset($row->endDate->year))
							{
								$aVals['end_year']=$row->endDate->year;
							}
						}
					}
					Phpfox::getService("resume.experience.process")->add($aVals);
				}
			}			
			
	   }

		public function importpublications($ynrm_linkedin_data,$resume_id)
		{
			if(isset($ynrm_linkedin_data->publications))
			{
				$length = $ynrm_linkedin_data->publications->_total;
				for($i=0;$i<$length;$i++)
				{	
					$row = $ynrm_linkedin_data->publications->values[$i];
					$aVals = array();
					$aVals['resume_id'] = $resume_id;		
					if(isset($row->title))
					{
						$aVals['title']=$row->title;
					}
					if(isset($row->date->month))
					{
						$aVals['published_month']=$row->date->month;
					}
					if(isset($row->date->year))
					{
						$aVals['published_year']=$row->date->year;
					}
					Phpfox::getService("resume.publication.process")->add($aVals);
				}
			}			
		}

		public function importlanguages($ynrm_linkedin_data,$resume_id)
		{
			if(isset($ynrm_linkedin_data->languages))
			{
				$length = $ynrm_linkedin_data->languages->_total;
				for($i=0;$i<$length;$i++)
				{	
					$row = $ynrm_linkedin_data->languages->values[$i];
					$aVals = array();
					$aVals['resume_id'] = $resume_id;		
					if(isset($row->language->name))
					{
						$aVals['name']=$row->language->name;
					}
					Phpfox::getService("resume.language.process")->add($aVals);
				}
			}			
		}
		
		public function Importcertifications($ynrm_linkedin_data,$resume_id)
		{
			if(isset($ynrm_linkedin_data->certifications))
			{
				$length = $ynrm_linkedin_data->certifications->_total;
				for($i=0;$i<$length;$i++)
				{	
					$row = $ynrm_linkedin_data->certifications->values[$i];
					$aVals = array();
					$aVals['resume_id'] = $resume_id;		
					if(isset($row->name))
					{
						$aVals['certification_name']=$row->name;
					}
					Phpfox::getService("resume.certification.process")->add($aVals);
				}
			}	
		}
		
		public function ImportAddition($ynrm_linkedin_data,$resume_id)
		{
			$aVals = array();
			$aVals['resume_id'] = $resume_id;		
			if(isset($ynrm_linkedin_data->interests))
			{
				$aVals['interests']=$ynrm_linkedin_data->interests;
			}
			if(isset($ynrm_linkedin_data->memberUrlResources))
			{
				$length = $ynrm_linkedin_data->memberUrlResources->_total;
				for($i=0;$i<$length;$i++)
				{
					$row = $ynrm_linkedin_data->memberUrlResources->values[$i];
					$aVals['emailaddress'][] = $row->url;
				}
			}
			if(count($aVals)>0)
				Phpfox::getService("resume.addition.process")->add($aVals);
		}
		
	/**
	 * Add favorite
	 */
	public function addFavorite($iItemId)
	{
		if(isset($iItemId) && $iItemId)
		{
			$iCount = $this->database()->select('COUNT(*)')
					->from(phpfox::getT('resume_favorite'))
					->where('resume_id = ' . (int) $iItemId . ' AND user_id = ' . Phpfox::getUserId())
					->execute('getSlaveField');
			if($iCount)
			{
				return false;
			}
			$iId = $this->database()->insert(phpfox::getT('resume_favorite'), array(
					'resume_id' => (int) $iItemId,
					'user_id' => Phpfox::getUserId(),
					'time_stamp' => PHPFOX_TIME,
				)
			);
			
			$this->database()->updateCounter('resume_basicinfo', 'total_favorite', 'resume_id', $iItemId);
			
			(($sPlugin = Phpfox_Plugin::get('resume.service_process_addfavorite_end')) ? eval($sPlugin) : false);
			// Add notification
			return $iId;
		}
		return false;
	}
	
	/*
	 * Delete favorite
	 */
	public function deleteFavorite($iItemId)
	{
		$aFavorite = phpfox::getLib('database')->select('rf.*')
					->from(phpfox::getT('resume_favorite'), 'rf')
					->where('favorite_id = '.$iItemId.' and user_id = '.phpfox::getUserId())
					->execute('getRow');
		if(empty($aFavorite))
		{
			return false;
		}
		$this->database()->updateCounter('resume_basicinfo', 'total_favorite', 'resume_id', $aFavorite['resume_id'], true);
		$this->database()->delete(phpfox::getT('resume_favorite'), 'favorite_id = ' . (int) $iItemId . ' AND user_id = ' . Phpfox::getUserId());
		(($sPlugin = Phpfox_Plugin::get('resume.service_process_deletefavorite_end')) ? eval($sPlugin) : false);
	}
	
	//send Message
	public function SendMessage($aVals)
	{
		$oFilter = phpfox::getLib('parse.input');
        $user_id = $aVals['user_id'];
        $aUser = Phpfox::getService('user')->get($aVals['user_id']);
        $email = $user_id;
        $sLink = Phpfox::getLib("url")->makeUrl('resume.view').$aVals['resume_id']."/";

        if($aVals['type']==1)
        {
            $aAccount = Phpfox::getService('resume.account')->getAccountByUserId($user_id);
            $aAccount['name'] = $aUser['full_name'];
            $aAccount['email'] = $aUser['email'];
        }
        else {
            $aAccount = Phpfox::getService('resume.account')->getAccountByUserId(Phpfox::getUserId());
            $aAccount['name'] = Phpfox::getUserBy('full_name');
            $aAccount['email'] = Phpfox::getUserBy('email');
        }

        $aBasic = Phpfox::getService("resume.basic")->getBasicInfo($aVals['resume_id']);

        if(isset($aAccount['user_id']))
        {
            $full_name = $aAccount['name'];
        }
        else
        {
            $full_name = Phpfox::getUserBy('full_name');
        }

        if($aVals['type'] == 1)
        {

            $email = $aAccount['email'];
            if(isset($aBasic['full_name']))
            {
                $sFullName = $aBasic['full_name'];
                Phpfox::getLib("mail")->fromName($sFullName);
            }
            if(isset($aBasic['email'][0]))
            {
                $sEmail = $aBasic['email'][0];
                Phpfox::getLib("mail")->fromEmail($sEmail);
            }
        }
        else {

            if(isset($aBasic['email']) && count($aBasic['email'])>0)
            {
                $email = $aBasic['email'];

                if(isset($aAccount['email']))
                {
                    $sEmail = $aAccount['email'];
                    Phpfox::getLib("mail")->fromEmail($sEmail);
                }
                if(isset($aAccount['name']))
                {
                    $sFullName = $aAccount['name'];
                    Phpfox::getLib("mail")->fromName($sFullName);
                }
            }
        }

        if(empty($email)) {
            return false;
        }

        Phpfox::getLib('mail')
            ->to($email)
            ->subject($aVals['title'])
            ->message(array('resume.contact_mail_to_who_view', array(
                'full_name' => $full_name,
                'message' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message'])))),
                'link' => $sLink
                )
            )
        )
        ->notification('mail.new_message')
        ->send();
	}
}

?>