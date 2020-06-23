<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Creditmoney_Process extends Phpfox_Service {

    public function autoCreateCreditMoney($iUserId = 0)
    {
        if(!$iUserId){
            $iUserId = Phpfox::getUserId();
        }
        $aInsert = array(
            'creditmoney_user_id' => $iUserId,
            'creditmoney_total_amount' => 0.0,
            'creditmoney_remain_amount' => 0.0,
            'creditmoney_creation_datetime' => PHPFOX_TIME,
            'creditmoney_modification_datetime' => 0,
            'creditmoney_description' => ''
        );
        
        return $this->database()->insert(Phpfox::getT('ecommerce_creditmoney'), $aInsert);
    }

    public function updateRemainingAmount($iCreditMoneyId, $fRemainingAmount)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_creditmoney'), array('creditmoney_remain_amount' => $fRemainingAmount), 'creditmoney_id = ' . (int) $iCreditMoneyId);
    }
}

?>