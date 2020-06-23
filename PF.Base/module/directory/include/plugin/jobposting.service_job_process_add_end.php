<?php 
;

if(Phpfox::isModule('directory')){
	if(isset($aVals['yndirectory_module']) && $aVals['yndirectory_module'] == 'directory' && isset($aVals['yndirectory_item']) && (int)$aVals['yndirectory_item'] > 0 && isset($iId) && (int)$iId > 0){
		Phpfox::getService('directory.process')->addItemOfModuleToBusiness(array(
            'module_id' => 15, // jobs
            'business_id' => $aVals['yndirectory_item'],
            'core_module_id' => 'jobposting',
            'item_id' => $iId,
            'status' => 'inactive',
		));
	}
}

;
?>