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
class Resume_Service_Resume extends Phpfox_Service
{
	/**
	 * Get total item count from query
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	
	public function getGenderPhrase($gender){
		$aGender = array(
			'0' => _p('resume.none'),
			'1' => _p('resume.male'),
			'2' => _p('resume.female')
		);
		return $aGender[$gender];
	}
	
	public function getMaritalStatusPhrase($status){
		$aStatus = array(
			'single' => _p('resume.single'),
			'married' => _p('resume.married'),
			'other' => _p('resume.others')
		);
		return isset($aStatus[$status])?$aStatus[$status]:_p('resume.none');
	}
	
	public function getExtraInfo()
	{
		$aInfoUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		$aRelation = Phpfox::getService('custom.relation')->getLatestForUser(Phpfox::getUserId(), null, true);
		
		$aRelationStatus = array(
			'2' => 'single',
			'4' => 'married'
		);
	
		if(isset($aRelation['relation_id']) && isset($aRelationStatus[$aRelation['relation_id']]))
		{
			$aInfoUser['marital_status'] = $aRelationStatus[$aRelation['relation_id']];
		}	
		else
		{
			$aInfoUser['marital_status'] = 'other';
			
		}	
		
		$aInfoUser['gender_phrase'] = $this->getGenderPhrase($aInfoUser['gender']);
		$aInfoUser['marital_status_phrase'] = $this->getMaritalStatusPhrase($aInfoUser['marital_status']);
		$aInfoUser['month'] = substr($aInfoUser['birthday'], 0, 2);
		$aInfoUser['day'] = substr($aInfoUser['birthday'],2,2);
		$aInfoUser['year'] = substr($aInfoUser['birthday'],4);
		return $aInfoUser;
	} 
	
	public function getItemCount($aConds)
	{		
		// Generate query object	
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from(Phpfox::getT('resume_basicinfo'),'rbi')
						-> join(Phpfox::getT('user'),'u','u.user_id = rbi.user_id');
		// Filfer conditions
		if($aConds)
		{
			$oQuery-> where($aConds);
		}
								
		return $oQuery->execute('getSlaveField');
	}
	
	/**
	 * Get Resume items according to the data input (this only use for back-end browsing)
	 * @param array $aConditions is the array of filter conditions 
	 * @param string $sOrder is the listing order 
	 * @param int $iLimit is the limit of row's number output
	 * @return array of resume items data
	 */
 	public function getResumes($aConds, $sOrder, $iPage = 0, $iLimit = 0, $iCount = 0)
	{
		// Generate query object						
		$oSelect = $this -> database() 
						 -> select('rbi.resume_id, rbi.headline, rbi.is_completed, rbi.is_published, rbi.total_view, rbi.total_favorite, rbi.time_stamp, rbi.time_update, u.*, rbi.status')
						 -> from(Phpfox::getT('resume_basicinfo'), 'rbi')
						 -> join(Phpfox::getT('user'),'u','u.user_id = rbi.user_id');
		
		// Filter select condition
		if($aConds)
		{
			$oSelect->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oSelect->order($sOrder);
		}
		
		// Setup limit items getting
		$oSelect->limit($iPage, $iLimit, $iCount);

		$aResumes = $oSelect->execute('getRows');
		
	 	return $aResumes;
	}
	
    public function getResume($iResumeId)
	{
		// Generate query object						
		return $this->database()
                ->select('rbi.resume_id, rbi.headline, rbi.is_completed, rbi.is_published, rbi.total_view, rbi.total_favorite, rbi.time_stamp, rbi.time_update, u.*, rbi.status, rbi.is_synchronize')
                ->from(Phpfox::getT('resume_basicinfo'), 'rbi')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')
                ->where('rbi.resume_id = ' . (int) $iResumeId)
                ->execute('getRow');		
	}
    
	public function getPublishedResumeByUserId($iUserId = 0)
	{
			if(!$iUserId)
		return;
		$aPublishedResume = $this -> database() -> select('*')
								  -> from(Phpfox::getT('resume_basicinfo'),'bi')
								  -> where("bi.is_completed = 1 AND bi.is_published = 1 AND bi.status = 'approved' AND bi.user_id = {$iUserId}")
								  -> execute('getRows');
		return $aPublishedResume;
	}
	/**
	 * Genetate array of search conditions from input
	 */
	public function setAdvSearchConditions($aVals)
	{
	
		// Filter keywords			
		if(!empty($aVals['keywords']))
		{
			$this->search()->setCondition("AND rbi.headline LIKE '%" . $aVals['keywords'] . "%'");
		}

		//Filter country
		if(!empty($aVals['country_iso']))
		{
			$this->search()->setCondition('AND rbi.country_iso = \'' . $aVals['country_iso'] . '\'');
		}
		
		//Filter state / province
		if(!empty($aVals['country_child_id']))
		{
			$this->search()->setCondition('AND rbi.country_child_id = ' . $aVals['country_child_id']);
		}
		
		//Filter city
		if(!empty($aVals['city']))
		{
			$this->search()->setCondition('AND rbi.city LIKE \'%' . $aVals['city'] . '%\'');
		}
		
		//Filter company
		if(!empty($aVals['company']))
		{
			$this->search()->setCondition('AND rex.company_name LIKE \'%' . $aVals['company'] . '%\'');
		}
		
		//Filter school and degree

		if(!empty($aVals['school']))
		{
			$this->search()->setCondition('AND red.school_name LIKE \'%' . $aVals['school'] . '%\'');
		}
		
		if(!empty($aVals['degree']))
		{
			$this->search()->setCondition('AND red.degree LIKE \'%' . $aVals['degree'] . '%\'');
		}
		
		//Filter level
		if(!empty($aVals['level_id']))
		{
				$this->search()->setCondition('AND rbi.level_id = ' . $aVals['level_id']);
		}
		
		//Filter Year of experience
		if($aVals['year_exp_from'] > 0)
		{
				$this->search()->setCondition('AND rbi.year_exp >= ' . $aVals['year_exp_from']);
		}
		
		//Filter Year of experience
		if($aVals['year_exp_to'] > 0)
		{
				$this->search()->setCondition('AND rbi.year_exp <= ' . $aVals['year_exp_to']);
		}
		//Filter Gender
		if($aVals['gender'] > 0)
		{
				$this->search()->setCondition('AND rbi.gender = ' . $aVals['gender']);
		}
		//Filter Gender
		if(!empty($aVals['skill']))
		{
				$aSkills = explode(',', trim($aVals['skill'], ','));
				if(is_array($aSkills) && !empty($aSkills))
				{ 
					$sSkillConds = "";
					foreach($aSkills as $sSkill)
					{
						if(!$sSkillConds)
						{
							$sSkillConds .= "AND (rbi.skills like '%" . $sSkill . "%' ";
						}
						else
						{
							$sSkillConds .= "OR rbi.skills like '%" . $sSkill . "%' "; 
						}
					}
					if($sSkillConds)
					{
						$sSkillConds .=")";
						$this->search()->setCondition($sSkillConds);
					}	
				}
		}
	}
	
	public function getAdvSearchFields()
	{
		$aVals = array();
		
		if($this->search()->get('submit') == _p('resume.reset'))
		{
			$aVals = array(
				'form_flag' => 1,
				'gender' => '',
				'level_id' => 0,
				'year_exp_from' => 0,
				'year_exp_to' => 0
			);
		}
		else{
			$aVals['form_flag'] 		= $this->search()->get('form_flag');
			$aVals['keywords'] 			= $this->search()->get('keywords');
			$aVals['country_iso'] 		= $this->search()->get('country_iso');
			$aVals['country_child_id'] 	= $this->search()->get('country_child_id');
			$aVals['city'] 				= $this->search()->get('city');
			$aVals['company'] 			= $this->search()->get('company');
			$aVals['school'] 			= $this->search()->get('school');		
			$aVals['degree'] 			= $this->search()->get('degree');
			$aVals['level_id'] 			= $this->search()->get('level_id');
			$aVals['year_exp_from'] 	= $this->search()->get('year_exp_from');
			$aVals['year_exp_to'] 		= $this->search()->get('year_exp_to');
			$aVals['gender'] 			= $this->search()->get('gender');
			$aVals['skill'] 			= $this->search()->get('skill');
			$aVals['submit'] 			= $this->search()->get('submit');
			$aVals['category'] 			= $this->search()->get('category');
			if(is_array($aVals['category']))
			{
				$aVals['category'] 	= array_unique($aVals['category']);
			}
		}
		return $aVals;
	}
	
	public function isFavorite($iItemId)
	{
		$iFavoriteId = phpfox::getLib('database')->select('favorite_id')
					->from(phpfox::getT('resume_favorite'))
					->where('resume_id = '.$iItemId.' and user_id ='.phpfox::getUserId())
					->execute('getSlaveField');
		if($iFavoriteId)
		{
			return true;
		}
		return false;
	}
	
	public function addNote($aNote)
	{
		$aResumeId= $aNote['resume_id'];
		$sNote = trim($aNote['text']);
		$sNote = Phpfox::getLib('parse.input')->clean($sNote);
		$sNote = substr($sNote, 0, 500);
		
		$aView = $this->database()
					  ->select('*')
					  ->from(Phpfox::getT('resume_viewme'))
					  ->where('resume_id = ' . $aResumeId . " AND user_id = " . Phpfox::getUserId())
					  ->execute('getRow');
		if($aView && !empty($sNote))
		{					  
			$this->database()->update(Phpfox::getT('resume_viewme'),array('note' => $sNote),'view_id = ' . $aView['view_id']);
		}
	}
	
	public function isNote($iItemId)
	{
		$sNote = $this->database()->select('note')
					->from(Phpfox::getT('resume_viewme'))
					->where('resume_id = '.$iItemId.' and user_id ='.phpfox::getUserId())
					->execute('getSlaveField');
		if(!empty($sNote))
		{
			return $sNote;
		}
		return false;
	}

	public function updateStatus($iResumeId)
	{		
		list($score,$aListUncomplete,$total_marks) = Phpfox::getService("resume.completeness")->calculate($iResumeId);
		
		$aResume = Phpfox::getService('resume.basic')->getQuick($iResumeId);
		
		if(!$aResume)
		{
			return false;
		}
		
		
		// Incompleted Resume
		if($score < $total_marks)
		{
			$aUpdate = array(
				'is_completed' => 0,
				'status'	   => 'none',
				'is_published' => 0,
				'time_update'  => PHPFOX_TIME
			);
		}
		// Completed Resume
		else 
		{
			$aUpdate = array(
				'is_completed' => 1,
				'status'	   => 'none',
				'is_published' => 0,
				'time_update'  => PHPFOX_TIME
			);
		}
		$this->database()->update(Phpfox::getT('resume_basicinfo'), $aUpdate, 'resume_id = ' . $iResumeId);
		return true;
	}
	
	//Check if user has at least on resume in "Approving" status
	public function checkApprovingStatus($iUserId)
	{
		$aApprovings = $this->database()->select("COUNT(*)")
							->from(Phpfox::getT('resume_basicinfo'),'rbi')
							->where('user_id = ' . $iUserId ." AND status = 'approving'")
							->execute('getSlaveField');
							
		if($aApprovings > 0)
		{
			return true;
		}
		
		return false;
	}
	
	public function hasPublishedResume($iUserId)
	{
		$aResume = $this->database()->select("resume_id")
					->from (Phpfox::getT('resume_basicinfo'))
					->where("user_id = {$iUserId} AND is_published = 1 and status ='approved'")
					->execute('getRow');
		if($aResume)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	public function getPendingResumeTotal()
    {
        if(!Phpfox::getUserId())
            return 0;

        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('resume_basicinfo'),'resume')
            ->where('resume.status=\'approving\'')
            ->execute('getSlaveField'));
    }
    public function buildSectionMenu()
    {
        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE'))
        {
            $iTotalMy = $this->getItemCount(['rbi.user_id = '.Phpfox::getUserId()]);
            $aFilterMenu = array(
                _p('resume.all_resumes') => '',
                _p('resume.my_resumes').($iTotalMy > 0 ? '<span class="count-item">'.($iTotalMy > 99 ? '99+' : $iTotalMy).'</span>' : '') => 'my',
                _p('resume.my_noted_resumes') => 'noted',
                _p('resume.my_favorite_resumes') => 'favorite',
                _p('resume.who_viewed_me') => 'resume.whoviewedme'
            );
            $iPendingTotal = Phpfox::getService('resume')->getPendingResumeTotal();
            if($iPendingTotal > 0 && Phpfox::getUserParam('resume.can_approve_resumes'))
            {
                $aFilterMenu[_p('resume.pending_resumes'). '<span class="count-item">'.($iPendingTotal > 99 ? '99+' : $iPendingTotal).'</span>'] =  'pending';
            }
        }
        Phpfox::getLib('template')->buildSectionMenu('resume', $aFilterMenu);
    }

    public function setPageSectionMenu($aResume)
    {
        $oUrl = Phpfox::getLib('url');
        $aMenus = array(
            $oUrl->makeUrl('resume.add',['id' => $aResume['resume_id']]) => _p('basic_information'),
            $oUrl->makeUrl('resume.summary',['id' => $aResume['resume_id']]) => _p('summary'),
            $oUrl->makeUrl('resume.experience',['id' => $aResume['resume_id']]) => _p('experience'),
            $oUrl->makeUrl('resume.education',['id' => $aResume['resume_id']]) => _p('education'),
            $oUrl->makeUrl('resume.skill',['id' => $aResume['resume_id']]) => _p('add_skill_expertise'),
            $oUrl->makeUrl('resume.certification',['id' => $aResume['resume_id']]) => _p('certifications'),
            $oUrl->makeUrl('resume.language',['id' => $aResume['resume_id']]) => _p('languages'),
            $oUrl->makeUrl('resume.publication',['id' => $aResume['resume_id']]) => _p('publications'),
            $oUrl->makeUrl('resume.addition',['id' => $aResume['resume_id']]) => _p('additional_information'),
        );
        $sViewDetail = array(
            'link' => $oUrl->permalink('resume.view',$aResume['resume_id'],$aResume['headline']),
            'phrase' => _p('view_my_resume')
        );
        Phpfox::getLib('template')->buildPageMenu('yresume_add_form', $aMenus, $sViewDetail, true);
    }
}

?>