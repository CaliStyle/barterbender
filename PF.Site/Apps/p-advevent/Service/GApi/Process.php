<?php
namespace Apps\P_AdvEvent\Service\GApi;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent_gapi');
    }

    /**
     * @param $aVals
     * @return int
     */
    public function add($aVals)
    {
        $aInsert = [];
        $aInsert['oauth2_client_id'] = $aVals['oauth2_client_id'];
        $aInsert['oauth2_client_secret'] = $aVals['oauth2_client_secret'];
        $aInsert['developer_key'] = $aVals['developer_key'];

        $iId = $this->database()->insert($this->_sTable, $aInsert);
        return $iId;
    }

    /**
     * @param $aVals
     * @param $iId
     * @return bool|resource
     */
    public function update($aVals, $iId)
    {
        $aUpdate = [];
        $aUpdate['oauth2_client_id'] = $aVals['oauth2_client_id'];
        $aUpdate['oauth2_client_secret'] = $aVals['oauth2_client_secret'];
        $aUpdate['developer_key'] = $aVals['developer_key'];

        $uId = $this->database()->update($this->_sTable, $aUpdate, 'id='.(int)$iId);
        return $uId;
    }

    /**
     * @param $iId
     * @return bool
     */
    public function delete($iId)
    {
        return $this->database()->delete($this->_sTable, 'id='.(int)$iId);
    }
}