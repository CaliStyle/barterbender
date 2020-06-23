<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Sellersettings_Process extends Phpfox_Service {

    public function add($aVals)
    {
        $aInsert = array(
            'user_id' => Phpfox::getUserId(),
            'data_seller_setting' => json_encode($aVals)
        );
        
        $iSettingId = $this->database()->insert(Phpfox::getT('ecommerce_auction_sellersetting'), $aInsert);
        
        return $iSettingId;
    }

    public function update($aVals)
    {
        $aUpdate = array(
            'data_seller_setting' => json_encode($aVals)
        );
        
        return $this->database()->update(Phpfox::getT('ecommerce_auction_sellersetting'), $aUpdate, 'user_id = ' . Phpfox::getUserId());
    }
}

?>