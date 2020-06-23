<?php
namespace Apps\P_AdvEvent\Service\GApi;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class GApi extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent_gapi');
    }

    /**
     * @return array|int|string
     */
    public function getForManage()
    {
        $aGapi = $this->database()->select('*')->from($this->_sTable)->limit(1)->execute('getRow');
        return $aGapi;
    }
}