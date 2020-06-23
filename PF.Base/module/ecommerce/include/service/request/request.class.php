<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Request_Request extends Phpfox_Service {

    public function getTotalPendingAmount()
    {
        return $this->database()
                ->select('SUM(ecmr.creditmoneyrequest_amount)')
                ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ecmr')
                ->where('ecmr.user_id = ' . Phpfox::getUserId() . ' AND ecmr.creditmoneyrequest_status = "pending"')
                ->execute('getSlaveField');
    }
    
    public function getTotalReceivedAmount()
    {
        return $this->database()
                ->select('SUM(ecmr.creditmoneyrequest_amount)')
                ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ecmr')
                ->where('ecmr.user_id = ' . Phpfox::getUserId() . ' AND ecmr.creditmoneyrequest_status = "approved"')
                ->execute('getSlaveField');
    }
    
    public function get($iRequestId)
    {
        return $this->database()
                ->select('ecmr.*')
                ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ecmr')
                ->where('ecmr.creditmoneyrequest_id = ' . (int) $iRequestId)
                ->execute('getRow');
    }

    public function getRequest($aConds, $sSort = 'ecmr.creditmoneyrequest_id ASC', $iPage = '', $iLimit = '')
    {
        $sQuery = $this->database()
                ->select('ecmr.*')
                ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ecmr')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ecmr.user_id')
                ->where($aConds)
                ->execute('');
        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
        $aRows = array();
        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('ecmr.*,'.Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ecmr')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ecmr.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
        }
        
        return array($iCnt, $aRows);

    }

}

?>
