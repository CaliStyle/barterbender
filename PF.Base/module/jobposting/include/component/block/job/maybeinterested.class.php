<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_MaybeInterested extends Phpfox_Component{

	public function process ()
	{
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

		$oJobs = PHpfox::getService("jobposting.job");
		$order = 'job.time_stamp desc';
		$Conds = null;
		$aSubscribe = $oJobs->getSubscribe();
		if(isset($aSubscribe['subscribe_id']))
		{
			$Conds = $oJobs->implementConditions($aSubscribe);
		}	 
		$aBlockJobs = $oJobs->getBlockJob($Conds, $order, $iLimit);
		if(count($aBlockJobs)==0)
		{
			return false;
			$aBlockJobs = Phpfox::getService('jobposting.job')->getBlockJob(null, $order, $iLimit);
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('jobs_you_may_be_interested_in'),
				'aBlockJobs' => $aBlockJobs
			)
		);
		
		return 'block';
	}

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Maybe Interested Jobs Limit'),
                'description' => _p('Define the limit of how many maybe interested jobs can be displayed when viewing the social store section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Maybe Interested Jobs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}