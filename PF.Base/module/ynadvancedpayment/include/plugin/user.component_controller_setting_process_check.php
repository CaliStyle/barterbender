
<?php
;

    if(Phpfox::isUser() && Phpfox::isModule('ynadvancedpayment'))
    {
        if(isset($aVals['gateway_detail'])){
            if(isset($aVals['gateway_detail']['authorizenet']) && isset($aVals['gateway_detail']['authorizenet']['authorizenet_guideline'])){
                unset($aVals['gateway_detail']['authorizenet']['authorizenet_guideline']);
            }
            if(isset($aVals['gateway_detail']['ccbill']) && isset($aVals['gateway_detail']['ccbill']['ccbill_guideline'])){
                unset($aVals['gateway_detail']['ccbill']['ccbill_guideline']);
            }
            if(isset($aVals['gateway_detail']['itransact']) && isset($aVals['gateway_detail']['itransact']['itransact_guideline'])){
                unset($aVals['gateway_detail']['itransact']['itransact_guideline']);
            }
        }
    }

;
?>

