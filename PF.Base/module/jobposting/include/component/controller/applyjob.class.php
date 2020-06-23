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

class JobPosting_Component_Controller_ApplyJob extends Phpfox_Component 
{
	public function process(){
        PHpfox::isUser(true);
        Phpfox::getUserParam('jobposting.can_apply_job', true);
        $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'jobposting' . PHPFOX_DS;
        if (!is_dir($p)) {
            if (!@mkdir($p, 0777, 1)) {
            }
        }
        // check send from applyfee controller
        $fromComponent = $this->getParam('fromComponent');
        $job_id = null;
        if('applyfee' != $fromComponent){
            $fromComponent = $this->request()->get('fromComponent');
            $job_id = (int)$this->request()->get('jobID');
        }

        if('applyfee' != $fromComponent){
            // check transaction
            $iTransactionId = $this->request()->get('req3');
            if((int)$iTransactionId <=0){
                $iTransactionId = (int)$this->request()->get('iTransactionId');
            }
            $aTransaction = Phpfox::getService('jobposting.transaction')->get((int)$iTransactionId);
            if(!$aTransaction
                || isset($aTransaction['invoice']) == false
                || isset($aTransaction['invoice']['jobID']) == false
                )
            {
                $sUrl = Phpfox::getLib('url')->permalink('jobposting', null);
                $this->url()->send($sUrl, null, _p('unable_to_find_the_job_you_want_to_apply'));
                return;                
            }

            $job_id = (int)$aTransaction['invoice']['jobID'];
            $this->template()->assign(array(
                'iTransactionId' => $iTransactionId,
            ));             
        } else {
            if(null == $job_id){
                $job_id = $this->getParam('jobID');
            }
            $this->template()->assign(array(
                'fromComponent' => $fromComponent,
                'jobID' => $job_id,
            ));             
        }

		$aResumes = array();
		$oServiceJobs = Phpfox::getService('jobposting.job');
		$aJob = $oServiceJobs->getJobByJobId($job_id);
		
		if(!$aJob){
			Phpfox::getLib('url')->send('subscribe');
		}
        
        if (Phpfox::getService('jobposting.application')->isApplied($job_id, Phpfox::getUserId()))
        {
            $this->url()->permalink('jobposting', $job_id, null, true);
        }
        
        if ($aVals = $this->request()->getArray('val'))
		{
            if($this->_verifyApplyForm($aVals, $aJob['company_id']))
            {
                if (Phpfox::getService("jobposting.job.process")->addApplication($job_id,$aVals))
                {
                    $this->url()->permalink('jobposting', $job_id, $aJob['title'], true);
                }
            }
			
			$this->template()->assign(array(
				'aForms' => $aVals
			));
		}
		
		$module_resume = PHpfox::isModule('resume');
		
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($aJob['company_id']);
	
		if($module_resume){
			$aResumes = $oServiceJobs->getResume(Phpfox::getUserId());	
		}
        
        $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($aJob['company_id']);
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                unset($aFields[$k]);
            }
        }
		if(isset($aVals['custom']) && count($aVals['custom']))
        {
            $aFields = $this->_setValuesCustomForm($aFields,$aVals['custom']);
        }
        $this->template()->setTitle($aJob['title'].' - '._p('apply_job'))
        ->setBreadCrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'))
        ->setBreadCrumb($aJob['title'], $this->url()->permalink('jobposting', $job_id, $aJob['title']))
        ->setBreadcrumb(_p('apply_job'), $this->url()->makeUrl('jobposting').$aJob['job_id']."/", true)
        ;
		
		$this->template()->assign(array(
			'module_resume' => $module_resume,
			'aJob' => $aJob,
			'aResumes' => $aResumes, 
			'aCompany' => $aCompany,
            'aFields' => $aFields,
            'resumeaddlink' => Phpfox::getLib("url")->makeUrl('resume.add'),
            'jobposting_maximum_upload_size_resume' => Phpfox::getParam('jobposting.jobposting_maximum_upload_size_resume'),
            'defaultFullName' => Phpfox::getUserBy('full_name')
		));	
		
		$this -> template() -> setHeader(array(
	   		'global.css' => 'module_jobposting',
	   		'feed.js' => 'module_feed',
	   		'quick_edit.js' => 'static_script',
            'jobposting.js' => 'module_jobposting',
	  	));

        Phpfox::getService('jobposting.helper')->buildMenu();
    }

    private function _verifyApplyForm($aVals, $iCompanyId)
    {

        #Custom fields
        $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($iCompanyId);       
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                continue;
            }
            
            if($aField['is_required'] && empty($aVals['custom'][$aField['field_id']]))
            {  
                //$this->template()->assign(array('aFields' => $aFields));
                return Phpfox_Error::set(_p('custom_field_is_required', array('custom_field' => _p($aField['phrase_var_name']))));
            }
        }
        
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($iCompanyId);
		if($aCompany){
			if($aCompany['candidate_name_enable']==1 && $aCompany['candidate_name_require']==1 && strlen(trim($aVals['name']))==0)
			{
				return Phpfox_Error::set(_p('your_name_is_required'));
			}
			
			if($aCompany['candidate_email_enable']==1 && $aCompany['candidate_email_require']==1 && strlen(trim($aVals['email']))==0)
			{
				return Phpfox_Error::set(_p('your_email_is_required'));
			}
			
			if($aCompany['candidate_telephone_enable']==1 && $aCompany['candidate_telephone_require']==1 && empty(trim($aVals['telephone'])))
			{
				return Phpfox_Error::set(_p('your_telephone_is_required'));
			}

			if($aCompany['candidate_photo_enable']==1 && $aCompany['candidate_photo_require']==1 && strlen(trim($_FILES['image']['name']))==0)
			{
				return Phpfox_Error::set(_p('your_photo_is_required'));
			}
		}
        
        #Require resume
        $bIsResume = true;
        switch($aVals['resume_type'])
        {
            case '0':
                if (empty($_FILES['resume']['name']))
                {
                    $bIsResume = false;
                }
                break;
            case '1':
                if (empty($aVals['list_resume']))
                {
                    $bIsResume = false;
                }
                break;
            default:
                $bIsResume = false;
        }
        
		// if (!$bIsResume)
  //       {
  //           return Phpfox_Error::set(_p('resume_is_required'));
  //       }
		
 		$email_pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i';
        if(!preg_match($email_pattern, $aVals['email']))
        {
            return Phpfox_Error::set(_p('email_format_is_not_valid'));
        }
        return true;
    }
    private function _setValuesCustomForm($aFields, $aVals)
    {
        
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                continue;
            }
            if(isset($aVals[$aField['field_id']])){
                $aFields[$k]['value'] = $aVals[$aField['field_id']];
            }
        }
        return $aFields;
    }
}

?>