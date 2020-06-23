<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_buttonapply extends Phpfox_Component{

	public function process ()
	{
		$canApplyJob = 0;
		
		if(Phpfox::getUserParam('jobposting.can_apply_job') && PHPFOX_TIME < $this->getParam('time_expire'))
		{
			$canApplyJob = 1;
		}
	
        $this->template()->assign(array(
            'canApplyJob' => $canApplyJob,
            'canApplyJobWithoutFee' => Phpfox::getUserParam('jobposting.can_apply_job_without_fee')
        ));			
	}
}