<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_Applyjobpackage extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iJob = $this->request()->get('id');
        
		$aPackages = Phpfox::getService('jobposting.applyjobpackage')->getBoughtPackages(Phpfox::getUserId(), true);
        $aTobuyPackages = Phpfox::getService('jobposting.applyjobpackage')->getToBuyPackages(Phpfox::getUserId());
		
		$iCnt = count((array)$aPackages) + count((array)$aTobuyPackages);
		if ($iCnt == 0)
		{
			return Phpfox_Error::display(_p('unable_to_find_any_package'));
		}
		
        $this->template()->assign(array(
            'aPackages' => $aPackages,
            'aTobuyPackages' => $aTobuyPackages,
            'iJob' => $iJob,
        ));

    }

}
