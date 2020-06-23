<?php

namespace Apps\YNC_StatusBg\Service;

use Phpfox;
use Phpfox_Service;


class Callback extends Phpfox_Service
{
    public function getUploadParams($aParams = null)
    {
        return Phpfox::getService('yncstatusbg')->getUploadParams($aParams);
    }

}