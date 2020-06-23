<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Creditmoney_Creditmoney extends Phpfox_Service {

    public function getCreditMoney($iUserId = 0)
    {
        if(!$iUserId){
            $iUserId = Phpfox::getUserId();
        }
        $aRow = $this->database()
                ->select('ecm.*')
                ->from(Phpfox::getT('ecommerce_creditmoney'), 'ecm')
                ->where('ecm.creditmoney_user_id = ' . (int)$iUserId)
                ->execute('getRow');
        
        if (!$aRow)
        {
            $iCreditMoneyId = Phpfox::getService('ecommerce.creditmoney.process')->autoCreateCreditMoney($iUserId);
            
            $aRow = array(
                'creditmoney_id' => $iCreditMoneyId,
                'creditmoney_user_id' => (int)$iUserId,
                'creditmoney_total_amount' => 0.0,
                'creditmoney_remain_amount' => 0.0,
                'creditmoney_creation_datetime' => PHPFOX_TIME,
                'creditmoney_modification_datetime' => 0,
                'creditmoney_description' => ''
            );
        }
        
        return $aRow;
    }


}

?>
