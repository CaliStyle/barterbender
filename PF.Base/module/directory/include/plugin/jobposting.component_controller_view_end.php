<?php 
;

if (Phpfox::isModule('directory') && isset($aJob['job_id']) && (int)$aJob['job_id'] > 0){
    $aYnDirectoryModuleData = Phpfox::getService('directory')->getItemOfModuleInBusiness($aJob['job_id'], 15, 'jobposting');
    if(isset($aYnDirectoryModuleData['data_id'])){
        if ($aCallback = Phpfox::callback('directory.getJobDetails', array('module_id' => 'directory', 'item_id' => $aYnDirectoryModuleData['business_id']))){
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('jobposting.job_posting'), $aCallback['url_home_photo'])
                ->setBreadCrumb('', '', true)
                ;                          
        }
    }
}

;
?>