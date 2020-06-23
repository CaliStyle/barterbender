<?php
;
if (Phpfox::isModule('opensocialconnect')) {
    try {
        $service = Phpfox::getService('socialbridge')->getProvider('facebook');

        if ($service) {
            $oApi = $service->getApi();

            if ($oApi) {

                $oApi->clearAllPersistentData();
            }
        }
    } catch (Exception $e) {

    }
};