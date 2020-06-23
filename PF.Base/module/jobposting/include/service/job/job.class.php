<?php

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Service_Job_Job extends Phpfox_service {
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('jobposting_job');
	}

    public function getPermission($companyId, &$job) {
        $job['canEdit'] = Phpfox::getService('jobposting.company')->isAdminPermission($companyId, Phpfox::getUserId(), 'edit_job');
        $job['canDelete'] = Phpfox::getService('jobposting.company')->isAdminPermission($companyId, Phpfox::getUserId(), 'delete_job');
        $job['canViewApplication'] = Phpfox::getService('jobposting.company')->isAdminPermission($companyId, Phpfox::getUserId(), 'view_application');
        $job['canDownloadResumes'] = Phpfox::getService('jobposting.company')->isAdminPermission($companyId, Phpfox::getUserId(), 'download_resumes');
    }

	public function getJobByJobId($job_id, $getMoreInfo = false)
	{
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = job.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}		
			
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'jobposting_job\' AND l.item_id = job.job_id AND l.user_id = ' . Phpfox::getUserId());
		}
			
		$aRow = $this->database()->select('job.*,t.description,t.description_parsed,t.skills, t.skills_parsed')
			->from($this->_sTable,'job')
			->leftJoin(Phpfox::getT('jobposting_job_text'),'t','t.job_id = job.job_id')
			->where('job.job_id = '.$job_id)
			->execute('getRow');
			
		if($aRow){
			$aRow['time_expire_month'] = Phpfox::getTime('n', $aRow['time_expire'], false);
			$aRow['time_expire_day'] = Phpfox::getTime('j', $aRow['time_expire'], false);
			$aRow['time_expire_year'] = Phpfox::getTime('Y', $aRow['time_expire'], false);
			$aRow['time_expire_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), Phpfox::getService('jobposting.helper')->convertToUserTimeZone($aRow['time_expire']));
            
            if (!isset($aRow['is_friend']))
    		{
    			$aRow['is_friend'] = 0;
    		}
		}

        #Industry
		$aRow['category'] = Phpfox::getService('jobposting.catjob')->getCategoryData($job_id);
		$aRow['category_phrase'] = Phpfox::getService('jobposting.catjob')->getPhraseCategory($job_id);		
		
		#working_place, city, country, longitude, latitude
		$aRow = $this->implementsCountry($aRow);
		if($getMoreInfo) {
            $aRow['posted_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp'], false);
            $aRow['expire_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_expire'], false);
            $aRow['total_application'] = Phpfox::getService('jobposting.application')->getTotalByJobId($aRow['job_id']);
            if($aRow['time_expire'] <= PHPFOX_TIME){
                $aRow['status_jobs'] = _p('expired');
                $aRow['is_expired'] = 1;
            }
            else {
                $aRow['is_expired'] = 0;
                if($aRow['post_status']==1)
                    $aRow['status_jobs'] = _p('published');
                else if ($aRow['post_status']==0){
                    $aRow['status_jobs'] = _p('draft');
                }
            }
            $this->getPermission($aRow['company_id'], $aRow);
        }
		return $aRow;
	}
	/*
	 * Implement Job country 
	*/
	private function implementsCountry($aRow){
		$aRow['country_iso_phrase']  = Phpfox::getService('core.country')->getCountry($aRow['country_iso']);
		$aRow['country_child_id_phrase']  = Phpfox::getService('core.country')->getCountry($aRow['country_child_id']);
		
		$aRow['country_phrase'] = "";
		$aRow['city_country_phrase'] = "";
		$aRow['location_city_country_phrase'] = "";
		
		if(strlen($aRow['country_child_id_phrase'])>0)
			$aRow['country_phrase'] .= $aRow['country_child_id_phrase']." ";
		if(strlen($aRow['country_iso_phrase'])>0)
			$aRow['country_phrase'] .= $aRow['country_iso_phrase'];	
		
		if(strlen($aRow['city'])>0){
			$aRow['city_country_phrase'].= $aRow['city'].", ";
		if(strlen($aRow['country_phrase'])>0)
			$aRow['city_country_phrase'] .= $aRow['country_phrase'];
		}
		if(strlen($aRow['working_place'])>0){
			$aRow['location_city_country_phrase'].= $aRow['working_place'].", ";
		}
		$aRow['location_city_country_phrase'].=$aRow['city_country_phrase'];
		
		$aRow['encode_location_city_country_phrase'] = htmlentities(urlencode($aRow['location_city_country_phrase']));
		
		#latitude, longitude		
		$gmap = unserialize($aRow['gmap']);
		$aRow['latitude'] = $gmap['latitude'];
		$aRow['longitude'] = $gmap['longitude'];
		
		return $aRow;
	}
	
	private function implementFields($aJobs){
		
		foreach($aJobs as $key=>$aJob){
			$aJob['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aJob['time_stamp']);
			$aJob['time_expire_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'),  Phpfox::getService('jobposting.helper')->convertToUserTimeZone($aJob['time_expire']));
			$aJob['industrial_phrase'] = Phpfox::getService('jobposting.category')->getPhraseCategory($aJob['company_id']);
			if(isset($aJob['description_parsed']))
				$aJob['description_parsed_phrase'] = strip_tags($aJob['description_parsed']);
			$aJobs[$key] = $aJob;
		}
		return $aJobs;
	}
	
	public function getBlockJob($Conds, $Order, $iLimit = 3){
		$oQuery = $this->database()->select('job.*,ca.name, ca.location, ca.image_path, ca.server_id as image_server_id, t.description,t.description_parsed,t.skills, t.skills_parsed')
			->from($this->_sTable,'job')
			->join(PHpfox::getT('jobposting_company'),'ca','ca.company_id = job.company_id and ca.is_approved = 1')
			->leftJoin(Phpfox::getT('jobposting_job_text'),'t','t.job_id = job.job_id');
		if($Conds){
			$Conds.=" and job.is_deleted=0 and job.is_hide=0 and ca.is_deleted = 0 and job.is_approved = 1 and job.post_status = 1 and job.time_expire>".PHPFOX_TIME;
			$oQuery->where($Conds);
		}
		else {
			$Conds.="job.is_deleted=0 and job.is_hide=0 and ca.is_deleted = 0  and job.is_approved = 1 and job.post_status = 1 and job.time_expire>".PHPFOX_TIME;
			$oQuery->where($Conds);
		}
		if($Order){
			$oQuery->order($Order);
		}
		if($iLimit>0)
		{
			$oQuery->limit($iLimit);
		}	
		$aRows = $oQuery->execute('getRows');
		
		return $this->implementFields($aRows);
	}
	
	public function searchJobs($aConds, $sSort = 'job.time_stamp DESC', $iPage = '', $iLimit = '')
	{
		if($aConds){
			if(!is_array($aConds))
				$aConds.=" and job.is_deleted=0 and ca.is_deleted = 0";
			else {
				$aConds[] =" and job.is_deleted=0 and ca.is_deleted = 0";
			}
		}
		else {
			$aConds="job.is_deleted=0 and ca.is_deleted = 0";
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'job')
			->join(Phpfox::getT('jobposting_company'),'ca','ca.company_id = job.company_id')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = job.user_id')
			->where($aConds)
			->execute('getSlaveField');	
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('job.*, ca.name, ca.image_path, ca.server_id as image_server_id, ' . Phpfox::getUserField())
				->from($this->_sTable, 'job')
				->join(Phpfox::getT('jobposting_company'),'ca','ca.company_id = job.company_id')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = job.user_id')
				->where($aConds)
				->limit($iPage, $iLimit, $iCnt)
				->order('job.time_stamp desc')
				->execute('getSlaveRows');
			
			foreach($aItems as $k=>$aItem)
			{
				$aItems[$k]['permission'] = Phpfox::getService('jobposting.permission')->allPermissionForJob($aItem, Phpfox::getUserId());
                $aItems[$k]['total_application'] = Phpfox::getService('jobposting.application')->getTotalByJobId($aItem['job_id']);
			}
		}
		
		return array($iCnt, $this->implementFields($aItems));
	}	
	
	public function iCountJobByCompanyId($ompany_id){
		$aConds="job.post_status = 1 and job.is_approved = 1 and job.is_deleted=0 and ca.is_deleted = 0 and ca.company_id=".$ompany_id." and job.time_expire>".PHPFOX_TIME;
		
		$iCnt = $this->database()->select('COUNT(job.job_id)')
			->from($this->_sTable, 'job')
			->join(Phpfox::getT('jobposting_company'),'ca','ca.company_id = job.company_id')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = job.user_id')
			->where($aConds)
			->execute('getSlaveField');
			
		return $iCnt;
	}
	
	public function setAdvSearchConditions($aVals)
	{
		// Filter keywords
		if(!empty($aVals['keywords']))
		{

			$this->search()->setCondition("AND job.title LIKE '%" . $aVals['keywords'] . "%'");
		}
		if(!empty($aVals['company']))
		{
			$this->search()->setCondition("AND jc.name LIKE '%" . $aVals['company'] . "%'");
		}
		if(!empty($aVals['location']))
		{
			$this->search()->setCondition("AND jc.location LIKE '%" . $aVals['location'] . "%'");
		}
		if(!empty($aVals['language_prefer']))
		{
			$this->search()->setCondition("AND job.language_prefer LIKE '%" . $aVals['language_prefer'] . "%'");
		}
		if(!empty($aVals['education_prefer']))
		{
			$this->search()->setCondition("AND job.education_prefer LIKE '%" . $aVals['education_prefer'] . "%'");
		}
		if(!empty($aVals['working_place']))
		{
			$this->search()->setCondition("AND job.working_place LIKE '%" . $aVals['working_place'] . "%'");
		}
		if($aVals['country_iso'] != -1)
		{
			$this->search()->setCondition("AND job.country_iso  LIKE '%" . $aVals['country_iso'] . "%'");
		}
		if(!empty($aVals['country_child_id']))
		{
			$this->search()->setCondition("AND job.country_child_id LIKE '%" . $aVals['country_child_id'] . "%'");
		}
		if(!empty($aVals['city']))
		{
			$this->search()->setCondition("AND job.city LIKE '%" . $aVals['city'] . "%'");
		}
		
		if(isset($aVals['end_day']) && $aVals['end_day']>0
			&& isset($aVals['end_month'])
			&& $aVals['end_month']> 0 && isset($aVals['end_year']) && $aVals['end_year']>0)
		{
			$time_expire = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
			$time_expire = Phpfox::getService('jobposting.helper')->convertToUserTimeZone($time_expire);
			$this->search()->setCondition("AND job.time_expire <=".$time_expire);	
		}
		if(!empty($aVals['categories'])){
			
			$listcategory = trim($aVals['categories'],",");
			$aList = explode(",", $listcategory);
			if(count($aList)>=2){
				if($aList[1]>0)
					$listcategory = $aList[1];
				else {
					$listcategory = $aList[0];
				}
			}
			$this->search()->setCondition("AND 0<(select(count(*)) from ".Phpfox::getT('jobposting_category_data')." data where data.company_id = job.company_id and data.category_id in (".$listcategory."))");
		}
	}

	public function implementConditions($aVals){
		$sConditions = '1';
		if(!empty($aVals['keywords']))
		{
			$sConditions .= " AND job.title LIKE '%" . $aVals['keywords'] . "%'";
		}
		if(!empty($aVals['company']))
		{
			$sConditions .= " AND ca.name LIKE '%" . $aVals['company'] . "%'";
		}
		if(!empty($aVals['location']))
		{
			$sConditions .=" AND ca.location LIKE '%" . $aVals['location'] . "%'";
		}
		if(!empty($aVals['language_prefer']))
		{
			$sConditions .= " AND job.language_prefer LIKE '%" . $aVals['language_prefer'] . "%'";
		}
		if(!empty($aVals['education_prefer']))
		{
			$sConditions .= " AND job.education_prefer LIKE '%" . $aVals['education_prefer'] . "%'";
		}
		if(!empty($aVals['working_place']))
		{
			$sConditions .= " AND job.working_place LIKE '%" . $aVals['working_place'] . "%'";
		}
		
		if(isset($aVals['end_day']) && $aVals['end_day']>0 && isset($aVals['end_month']) && $aVals['end_month']> 0 && isset($aVals['end_year']) && $aVals['end_year']>0)
		{
			$time_expire = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
			$time_expire = Phpfox::getService('jobposting.helper')->convertToUserTimeZone($time_expire);
			$sConditions .= " AND job.time_expire <=".$time_expire;	
		}
		if(!empty($aVals['categories'])){
			
			$listcategory = trim($aVals['categories'],",");
			$aList = explode(",", $listcategory);
			if(count($aList)>=2){
				if($aList[1]>0)
					$listcategory = $aList[1];
				else {
					$listcategory = $aList[0];
				}
			}
			$sConditions .= " AND 0<(select(count(*)) from ".Phpfox::getT('jobposting_category_data')." data where data.company_id = job.company_id and data.category_id in (".$listcategory."))";
		}
		return $sConditions;
	}
	
	public function getAdvSearchFields()
	{
		$aVals = array();	
		
		$aVals['keywords'] = $this->search()->get('keywords');
		$aVals['company'] = $this->search()->get('company');
		$aVals['location'] = $this->search()->get('location');
		$aVals['industry'] = $this->search()->get('industry');
		$aVals['language_prefer'] = $this->search()->get('language_prefer');
		$aVals['education_prefer'] = $this->search()->get('education_prefer');
		$aVals['working_place'] = $this->search()->get('working_place');
		$aVals['expired_before'] = $this->search()->get('expired_before');
		$aVals['searchdate'] = $this->search()->get('searchdate');
		
		$aVals['city'] = $this->search()->get('city');
		$aVals['country_iso'] = $this->search()->get('country_iso');
		$aVals['country_child_id'] = $this->search()->get('country_child_id');
		
		$aCategory = $this->search()->get('category');
		$aVals['categories']  = "";

		$js_end__datepicker = $this->search()->get('js_end__datepicker');
        if(isset($_SESSION['ynjobposting_end_year']) && !empty($js_end__datepicker))
        {
        	$aVals['end_year'] = $_SESSION['ynjobposting_end_year'];
        	$aVals['end_month'] = $_SESSION['ynjobposting_end_month'];
			$aVals['end_day'] = $_SESSION['ynjobposting_end_day'];
        }

		
		if(isset($aCategory['search_0']))
		{
			$aValue = str_replace("search_", "", $aCategory['search_0']);
		
			$aVals['categories'].= $aValue;
			if(isset($aCategory["search_".$aValue]))
			{
				$aValue = str_replace("search_", "", $aCategory["search_".$aValue]);
				$aVals['categories'].= ",".$aValue;
			}
		}
		
		return $aVals;
	}

	public function getSubscribe(){
		$aRow = $this->database()->select('sb.*')
			->from(Phpfox::getT('jobposting_subscribe'),'sb')
			->where('sb.user_id = '.Phpfox::getUserId())
			->execute('getSlaveRow');
		if($aRow){
			$aRow['end_month'] = $aRow['time_expire_month'] = Phpfox::getTime('n', $aRow['time_expire'], false);
			$aRow['end_day'] = $aRow['time_expire_day'] = Phpfox::getTime('j', $aRow['time_expire'], false);
			$aRow['end_year'] = $aRow['time_expire_year'] = Phpfox::getTime('Y', $aRow['time_expire'], false);
			$aRow['searchdate'] = $aRow['time_expire_month']."/".$aRow['time_expire_day']."/".$aRow['time_expire_year'];
			if($aRow['industry']>0)
			{
				$aRow['categories'] = $aRow['industry'];
				if($aRow['industry_child']>0)
				{
					$aRow['categories'].= ",".$aRow['industry_child'];
				}
			}	
			$aList = explode("/", $aRow['searchdate']);
			if(count($aList)==3){
				$aRow['end_day'] = $aList[1];
				$aVals['end_month'] = $aList[0];
				$aVals['end_year'] = $aList[2];
			}
			
		}

		return $aRow;
	}
	
	public function getResume($iUserId){
		if(PHpfox::isModule('resume')){
			$iLimit = 50;
			$aConds = array("rbi.user_id = {$iUserId}");
			$sOrder = "rbi.time_update DESC, rbi.resume_id DESC";
			
			$oResume = Phpfox::getService('resume');
			$oResumeCompleteness = Phpfox::getService('resume.completeness');
			$aResumes = $oResume ->getResumes($aConds, $sOrder, $iLimit);
			return $aResumes;
		}
		return array();
	}
    
    /**
     * Search jobs in edit company
     * @param int $iCompanyId
     * @param string $sCond: ' AND <conditions>'
     * @param string $sSort
     * @param int $iPage
     * @param int $iLimit
     * @return array
     */
    public function searchForEditCompany($iCompanyId, $sCond = '', $iPage = null, $iLimit = null)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('is_deleted = 0 and company_id = '.(int)$iCompanyId.$sCond)
			->execute('getField');
        
        $aItems = array();
		if ($iCnt)
		{
			$aItems = $this->database()->select('*')
    			->from($this->_sTable)
    			->where('is_deleted = 0 and company_id = '.(int)$iCompanyId.$sCond)
                ->order('time_stamp DESC')
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
            
            foreach($aItems as $k => $aItem)
            {
                $aItems[$k]['posted_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_stamp'], false);
                $aItems[$k]['expire_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_expire'], false);
                $aItems[$k]['total_application'] = Phpfox::getService('jobposting.application')->getTotalByJobId($aItem['job_id']);
                $this->getPermission($iCompanyId, $aItems[$k]);
            }
		}
		
		return array($iCnt, $aItems);
	}
    
    public function getGeneralInfo($iId)
    {
        return $this->database()->select('*')->from($this->_sTable)->where('job_id = '.$iId)->execute('getSlaveRow');
    }
	
	public function buildHtmlRow($iId)
	{
		$sHtml = '';
		
		$aItem = $this->database()->select('*')->from($this->_sTable)->where('job_id = '.(int)$iId)->execute('getSlaveRow');
		if($aItem)
		{    
	        $aItem['posted_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_stamp'], false);
	        $aItem['expire_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), Phpfox::getService('jobposting.helper')->convertToUserTimeZone($aItem['time_expire']), false);
            $aItem['total_application'] = Phpfox::getService('jobposting.application')->getTotalByJobId($iId);
			
			$sHtml .= '<td><a href="'.Phpfox::getLib('url')->permalink('jobposting', $aItem['job_id'], $aItem['title']).'">'.$aItem['title'].'</a></td>';
            $sHtml .= '<td class="t_center">'.$aItem['posted_text'].'</td>';
            $sHtml .= '<td class="t_center">'.$aItem['expire_text'].'</td>';
            $sHtml .= '<td class="t_center"><a href="#" onclick="$.ajaxCall(\'jobposting.changeJobHide\', \'id='.$aItem['job_id'].'\'); return false;">'.($aItem['is_hide'] ? _p('hide') : _p('show')).'</a></td>';
            $sHtml .= '<td class="t_center">'.($aItem['post_status'] ? _p('published') : _p('draft')).'</td>';
            $sHtml .= '<td>';
            $sHtml .= '<a href="'.Phpfox::getLib('url')->permalink('jobposting.add', $aItem['job_id']).'">'._p('edit').'</a> | ';
            $sHtml .= '<a href="#" onclick="if(confirm(\''._p('core.are_you_sure').'\')) $.ajaxCall(\'jobposting.deleteJob\', \'id='.$aItem['job_id'].'\'); return false;">'._p("jobposting.delete").'</a> | ';
			if ($aItem['post_status'] != 1)
			{
				$sHtml .= '<a href="javascript:void(0);" onclick="">'._p('publish').'</a>';
			}
			else
			{
				if ($aItem['total_application'] > 0)
				{
					$sHtml .= '<a href="'.Phpfox::getLib('url')->permalink('jobposting.company.manage', 'job_'.$aItem['job_id']).'">'._p('view_applications').' ('.$aItem['total_application'].')</a> | ';
					$sHtml .= '<a class="no_ajax_link" href="'.Phpfox::getParam('core.path_file').'module/jobposting/static/php/downloadzip.php?id='.$aItem['job_id'].'">'._p('download_all_resumes').'</a>';
				}
				else
				{
					$sHtml .= _p('view_applications').' (0) | '._p('download_all_resumes');
				}
			}
			$sHtml .= '</td>';
		}

		return $sHtml;
	}
    
    public function getPromoteCode($iId, $en_photo, $en_description)
    {
        $sFrameUrl = Phpfox::getParam('core.path_file').'module/jobposting/static/php/promotejob.php?id='.$iId.'&en_photo='.$en_photo.'&en_description='.$en_description;
        return '<iframe src="'.$sFrameUrl.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:180px; height:360px;" allowTransparency="true">;</iframe>';
    }

	public function getForEmbed($iId)
	{
		$aJob = $this->database()->select('j.job_id, j.title, jt.description_parsed as description, c.image_path, c.server_id as image_server_id')
			->from($this->_sTable, 'j')
			->leftJoin(Phpfox::getT('jobposting_job_text'), 'jt', 'jt.job_id = j.job_id')
			->join(Phpfox::getT('jobposting_company'), 'c', 'c.company_id = j.company_id')
			->where('j.job_id = '.(int)$iId)
			->execute('getSlaveRow');
		
		return $aJob;
	}
	
	public function getPendingTotal()
	{
		return (int)$this->database()->select('COUNT(*)')
			->from($this->_sTable,'job')
			->join(PHpfox::getT('jobposting_company'),'ca','ca.company_id = job.company_id and ca.is_deleted = 0 and ca.is_approved = 1')
			->where('job.is_approved = 0 and job.is_deleted = 0 and job.post_status = 1 and job.time_expire>'.PHPFOX_TIME)
			->execute('getSlaveField');
	}
}

?>