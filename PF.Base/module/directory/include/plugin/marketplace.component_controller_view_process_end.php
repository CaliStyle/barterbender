<?php 
;

if (Phpfox::isModule('directory') && isset($aListing['listing_id']) && (int)$aListing['listing_id'] > 0){
    $aYnDirectoryModuleData = Phpfox::getService('directory')->getItemOfModuleInBusiness($aListing['listing_id'], 16, 'marketplace');
    if(isset($aYnDirectoryModuleData['data_id'])){
        if ($aCallback = Phpfox::callback('directory.getMarketplaceDetails', array('module_id' => 'directory', 'item_id' => $aYnDirectoryModuleData['business_id']))){
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('marketplace.marketplace'), $aCallback['url_home_photo'])
                ->setBreadcrumb($aListing['title'] . ($aListing['view_id'] == '2' ? ' (' . _p('marketplace.sold') . ')' : ''), $this->url()->permalink('marketplace', $aListing['listing_id'], $aListing['title']), true)
                ;                          
        }
    }
}

;
?>