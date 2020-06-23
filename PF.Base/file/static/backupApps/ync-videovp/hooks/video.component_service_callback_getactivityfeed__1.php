<?php

$oYvvpService = Phpfox::getService('yncvideovp');

if (!empty($aRow['embed_code'])) {
    $aRow['embed_code'] = $oYvvpService->addJSApiParam($aRow['embed_code']);
}

