
<?php
;

if(Phpfox::isUser() && Phpfox::isModule('ynadvancedpayment'))
{
    if (!empty($aGateways))
    {
        foreach ($aGateways as $keyaGateways => $valueaGateways) {
            switch ($valueaGateways['gateway_id']) {
                case 'authorizenet':                        
                    $authorizenet_guideline_value = _p('ynadvancedpayment.ynadvancedpayment_authorizenet_guideline'
                            , array(
                                'silentpost_url' => Phpfox::getParam('core.path_file') . 'module/ynadvancedpayment/static/php/silentpost.php',
                            )
                        );
                    $aGateways[$keyaGateways]['custom']['authorizenet_guideline'] = array(
                        'phrase' => _p('ynadvancedpayment.authorize_guideline'),
                        'type' => 'textarea',
                        'phrase_info' => '',
                        'user_value' => $authorizenet_guideline_value, 
                    );                    
                    break;

                case 'ccbill':    
                    $ccbill_guideline_value = _p('ynadvancedpayment.ynadvancedpayment_guideline'
                            , array(
                                'success_url' => Phpfox::getParam('core.path') . 'api/gateway/callback/ccbill/status_ccbill-success/', 
                                'failure_url' => Phpfox::getParam('core.path') . 'api/gateway/callback/ccbill/status_ccbill-fail/', 
                            )
                        );
                    $aGateways[$keyaGateways]['custom']['ccbill_guideline'] = array(
                        'phrase' => _p('ynadvancedpayment.ccbill_guideline'),
                        'type' => 'textarea',
                        'phrase_info' => '',
                        'user_value' => $ccbill_guideline_value, 
                    );                    
                    break;

                case 'itransact':
                    $itransact_guideline_value = _p('ynadvancedpayment_itransact_guideline_user',[
                        'postback_url' =>  Phpfox::getParam('core.path_file') . 'module/ynadvancedpayment/static/php/postback.php'
                    ]);
                    $aGateways[$keyaGateways]['custom']['itransact_guideline'] = array(
                        'phrase' => _p('itransact_guideline'),
                        'type' => 'textarea',
                        'phrase_info' => '',
                        'user_value' => $itransact_guideline_value,
                    );
                    break;
            }
        }
    }        
}

;
?>

