<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_apply extends Phpfox_Component{

	public function process ()
	{
		$aResumes = array();
		$job_id = $this->getParam('job_id');
		$module_resume = PHpfox::isModule('resume');
		
		$oServiceJobs = Phpfox::getService('jobposting.job');
		
		$aJob = $oServiceJobs->getJobByJobId($job_id);
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
		
		$this->template()->assign(array(
			'module_resume' => $module_resume,
			'aJob' => $aJob,
			'aResumes' => $aResumes, 
			'aCompany' => $aCompany,
            'aFields' => $aFields,
            'resumeaddlink' => Phpfox::getLib("url")->makeUrl('resume.add'),
		));	
		
		return 'block';
	}
}