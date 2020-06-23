<?php

namespace Apps\YNC_Comment\Service;

use Phpfox;
use Phpfox_Service;


class Callback extends Phpfox_Service
{
    public function getUploadParams($aParams = null)
    {
        return Phpfox::getService('ynccomment')->getUploadParams($aParams);
    }

    public function getUploadParamsComment($aParams = null)
    {
        return Phpfox::getService('ynccomment')->getUploadParamsComment($aParams);
    }
}