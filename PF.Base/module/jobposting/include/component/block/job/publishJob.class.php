<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_PublishJob extends Phpfox_Component
{
    public function process()
    {
        $iJob = $this->request()->get('id');
        $iCompanyId = $this->request()->get('company_id');
        $bCanFeature = Phpfox::getService('jobposting.permission')->canFeaturePublishedJob($iJob);

		if (!$iCompanyId)
		{
			return Phpfox_Error::display(_p('you_have_not_created_a_company'));
		}
		
		$aPackages = Phpfox::getService('jobposting.package')->getBoughtPackages($iCompanyId, true);
        $aTobuyPackages = Phpfox::getService('jobposting.package')->getToBuyPackages($iCompanyId);
		
		$iCnt = count((array)$aPackages) + count((array)$aTobuyPackages);
		if ($iCnt == 0)
		{
			return Phpfox_Error::display(_p('unable_to_find_any_package'));
		}
		
        $this->template()->assign(array(
            'aPackages' => $aPackages,
            'aTobuyPackages' => $aTobuyPackages,
            'featurefee' => PHpfox::getService('jobposting.helper')->getTextParseCurrency(Phpfox::getParam("jobposting.fee_feature_job")),
            'iJob' => $iJob,
            'bCanFeature' => $bCanFeature
        ));
    }
}
