<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_hotposting extends Phpfox_Component{

	public function process ()
	{
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

		$order = 'job.total_application desc';
		$aBlockJobs = Phpfox::getService('jobposting.job')->getBlockJob(null, $order, $iLimit);
		if(count($aBlockJobs)==0) {
			return false;
		}
		$this->template()->assign(array(
				'sHeader' => _p('hot_job_posting'),
				'aBlockJobs' => $aBlockJobs,
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
                'info' => _p('Hot Jobs Limit'),
                'description' => _p('Define the limit of how many hot jobs can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"Hot Jobs Limit" must be greater than or equal to 0'
            ]
        ];
    }

}