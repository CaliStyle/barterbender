<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Sellersettings_Sellersettings extends Phpfox_Service {

    public function get($iUserId)
    {
        $aSetting = $this->database()
                ->select('s.*')
                ->from(Phpfox::getT('ecommerce_auction_sellersetting'), 's')
                ->where('s.user_id = ' . (int) $iUserId)
                ->execute('getRow');
        if (!$aSetting)
        {
            return array();
        }
        
        $aSellerSettings = (array) json_decode($aSetting['data_seller_setting']);
        
        return $aSellerSettings;
    }
    
    public function getSellerSettings($aUserId)
    {
        if (!$aUserId || !is_array($aUserId))
        {
            return array();
        }
            
        $aSettings = $this->database()
                ->select('s.*')
                ->from(Phpfox::getT('ecommerce_auction_sellersetting'), 's')
                ->where('s.user_id IN (' . implode(',', $aUserId) . ')')
                ->execute('getRows');
        
        if (!$aSettings)
        {
            return array();
        }
        
        $aSellerSettings = array();
        foreach ($aSettings as $iKey => $aSetting)
        {
            $aSellerSettings[$aSetting['user_id']] = (array) json_decode($aSetting['data_seller_setting']);
        }
        
        return $aSellerSettings;
    }
}

?>
