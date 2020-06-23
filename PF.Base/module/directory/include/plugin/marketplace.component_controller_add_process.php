<?php 
;

if (Phpfox::isModule('directory')){
    $sModule = $this->request()->get('module', false);
    $iItem = $this->request()->get('item', false);
    if($sModule == 'directory' && (int)$iItem > 0){
        $this->template()->assign(array(
            'yndirectory_module' => $sModule,
            'yndirectory_item' => $iItem,
        )); 
        if ($aCallback = Phpfox::callback($sModule . '.getMarketplaceDetails', array('module_id' => $sModule, 'item_id' => $iItem))){
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('marketplace.marketplace'), $aCallback['url_home_photo'])
                ->setBreadcrumb(($bIsEdit ? _p('marketplace.editing_listing') . ': ' . $aListing['title'] : _p('marketplace.create_a_listing')), $this->url()->makeUrl('marketplace.add'), true)
                ;      
        }                
    }
}

;
?>