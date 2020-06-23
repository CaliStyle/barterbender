<?php 
;

if (Phpfox::isModule('directory') && isset($aListing['listing_id']) && (int)$aListing['listing_id'] > 0){
    $aYnDirectoryModuleData = Phpfox::getService('directory')->getItemOfModuleInBusiness($aListing['listing_id'], 16, 'advancedmarketplace');
    if(isset($aYnDirectoryModuleData['data_id'])){
        if ($aCallback = Phpfox::callback('directory.getMarketplaceDetails', array('module_id' => 'directory', 'item_id' => $aYnDirectoryModuleData['business_id']))){
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('advancedmarketplace.advanced_advancedmarketplace'), $aCallback['url_home_photo'])
                ->setBreadcrumb($aListing['title'] . ($aListing['view_id'] == '2' ? ' (' . _p('advancedmarketplace.sold') . ')' : ''), $this->url()->permalink('advancedmarketplace.detail', $aListing['listing_id'], $aListing['title']), true)
                ;                          
        }
    }
}

;
?>