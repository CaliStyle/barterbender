<?php 
;

if (Phpfox::isModule('directory')){
    $sModule = $this->request()->get('module', false);
    $iItem = $this->request()->get('item', false);
    if($sModule == 'directory' && (int)$iItem > 0){
        $bCanAddJobInBusiness = Phpfox::getService('directory.permission')->canAddJobInBusiness($iItem, $bRedirect = false);
        if($bCanAddJobInBusiness == false){
            $this->url()->send('subscribe', null, _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
        }
        
        $this->template()->assign(array(
            'yndirectory_module' => $sModule,
            'yndirectory_item' => $iItem,
        )); 
        if ($aCallback = Phpfox::callback($sModule . '.getJobDetails', array('module_id' => $sModule, 'item_id' => $iItem))){
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('jobposting.job_posting'), $aCallback['url_home_photo'])
                ->setBreadcrumb(_p('jobposting.job_posting'), $this->url()->makeUrl('jobposting'), true)
                ;      
        }                
    }
}

;
?>