<?php
if(PHpfox::isUser() && Phpfox::getLib('module')->getFullControllerName() != 'admincp.index') {
    if ($iId == 3 || $iId == 1) {
        $aBlocks[$iId][] = 'socialad.ad.display-list-' . $iId;
    } else {
        $sSaModuleId = Phpfox::getLib('module')->getModuleName();
        $aSaQuery = array(
            'user_id' => Phpfox::getUserId(),
            'block_id' => $iId,
            'module_id' => $sSaModuleId
        );
        Phpfox::getService('socialad.ad')->displayAdsOnBlock($aSaQuery);
        $aBlocks[$iId][] = 'socialad.ad.display-list';
    }
}
