<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Request_Money_Form extends Phpfox_Component {

    public function process()
    {
        Phpfox::isUser(true);
        
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);
        
        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney();

        $sAvailableAmount = $sCurrencySymbol . $aCreditMoney['creditmoney_remain_amount'];
        $sMaximumSetting = Phpfox::getParam('ecommerce.ecommerce_maximum_amount_to_request');
        if((float)$aCreditMoney['creditmoney_remain_amount'] <= (float)$sMaximumSetting)
        {
             $sMaximum =$aCreditMoney['creditmoney_remain_amount'];  
        }
        else 
        {
            $sMaximum = $sMaximumSetting;
        }
        $sMinimum = $sCurrencySymbol . (float) Phpfox::getParam('ecommerce.ecommerce_minimum_amount_to_request');
        
        $this->template()->assign(array(
            'sAvailableAmount' => $sAvailableAmount,
            'sCurrencySymbol' => $sCurrencySymbol,
            'sMaximum' => $sCurrencySymbol . (float)$sMaximum,
            'sMinimum' => $sMinimum,
            'sMax' => $sMaximum,
                ));
    }

}

?>

