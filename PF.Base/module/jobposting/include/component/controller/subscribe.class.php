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

class JobPosting_Component_Controller_Subscribe extends Phpfox_Component 
{
	public function process(){
		$aIndustryBlock1 = PHpfox::getService('jobposting.category')->get(3);
		$this->template()->assign(array(
				'aIndustryBlock1' => $aIndustryBlock1,
			)
		);	
		
		
	
		$req3 = $this->request()->get('req3');
		
		if($req3=="add" || $req3=="edit")
		{
			
			if ($aVals = $this->request()->getArray('val')){
				if($req3=="add")
					Phpfox::getService('jobposting.job.process')->addSubscribe($aVals);
				else {
					
					
					Phpfox::getService('jobposting.job.process')->updateSubscribe($aVals);
				}
			}
			Phpfox::getLib("url")->send("jobposting",array(),_p('update_subscribe_job_successfully'));
		}
		$aSubscribe = PHpfox::getService("jobposting.job")->getSubscribe();
		if($aSubscribe)
		{
			$req3 = 'edit';
		}	
		else
		{
			$aSubscribe = array();
			$aSubscribe['time_expire'] = PHPFOX_TIME;
			$aSubscribe['end_month'] = $aSubscribe['time_expire_month'] = Phpfox::getTime('n', $aSubscribe['time_expire'],false);
			$aSubscribe['end_day'] = $aSubscribe['time_expire_day'] = Phpfox::getTime('j', $aSubscribe['time_expire'], false);
			$aSubscribe['end_year'] = $aSubscribe['time_expire_year'] = Phpfox::getTime('Y', $aSubscribe['time_expire'], false);
			$aSubscribe['searchdate'] = $aSubscribe['time_expire_month']."/".$aSubscribe['time_expire_day']."/".$aSubscribe['time_expire_year'];
			
			$aList = explode("/", $aSubscribe['searchdate']);
			if(count($aList)==3){
				$aSubscribe['end_day'] = $aList[1];
				$aSubscribe['end_month'] = $aList[0];
				$aSubscribe['end_year'] = $aList[2];
			}
			$req3 = 'add';	
		}
        $aSubscribe['from_day'] = Phpfox::getTime('j');
        $aSubscribe['from_month'] = Phpfox::getTime('n');
        $aSubscribe['from_year'] = Phpfox::getTime('Y');
        $this -> template() -> setHeader(array(
			'global.css' => 'module_jobposting',
			
		))->assign(array(
			'aForms' => $aSubscribe,
			'req3' => $req3,
		));

		Phpfox::getService('jobposting.helper')->buildMenu();
	}
}

?>