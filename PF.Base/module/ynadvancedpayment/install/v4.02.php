<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 *
 */

function ynap_install402()
{
    $oDatabase = Phpfox::getLib('database');

    //itransact gateway
      $gatewayObj = $oDatabase->select("ppi.*")
                  ->from(Phpfox::getT('api_gateway'), 'ppi')
                  ->where(' `gateway_id` LIKE  \'itransact\' ')
                  ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
      $aInsert = array(
           'gateway_id' => 'itransact',
           'title' => 'iTransact',
           'description' => 'iTransact is a 20 year old, full service payment processing company powered by the largest processors on the planet servicing millions, while at the same time providing personal, world-class customer support and assistance.',
           'is_active' => 0,
           'is_test' => 1,
           'setting' => '', 
      );
      $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

    //Skrill gateway
    $gatewayObj = $oDatabase->select("ppi.*")
        ->from(Phpfox::getT('api_gateway'), 'ppi')
        ->where(' `gateway_id` LIKE  \'skrill\' ')
        ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
        $aInsert = array(
            'gateway_id' => 'skrill',
            'title' => 'Skrill',
            'description' => 'Skrill has been making digital payments simple, secure and quick since 2001. We’re an acknowledged world-leader in developing global payment solutions for people’s business and pleasure, whether they’re depositing funds on a gaming site, buying online or sending money to family and friends.',
            'is_active' => 0,
            'is_test' => 1,
            'setting' => '',
        );
        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

    //WebMoney gateway
    $gatewayObj = $oDatabase->select("ppi.*")
        ->from(Phpfox::getT('api_gateway'), 'ppi')
        ->where(' `gateway_id` LIKE  \'webmoney\' ')
        ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
        $aInsert = array(
            'gateway_id' => 'webmoney',
            'title' => 'WebMoney',
            'description' => 'WebMoney offers services which will allow you to keep track of your funds, attract funding, resolve disputes and make secure transactions.',
            'is_active' => 0,
            'is_test' => 1,
            'setting' => '',
        );
        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

    //Stripe gateway
    $gatewayObj = $oDatabase->select("ppi.*")
        ->from(Phpfox::getT('api_gateway'), 'ppi')
        ->where(' `gateway_id` LIKE  \'stripe\' ')
        ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
        $aInsert = array(
            'gateway_id' => 'stripe',
            'title' => 'Stripe',
            'description' => 'Stripe is the best way to accept payments online and in mobile apps. We handle billions of dollars every year for forward-thinking businesses around the world.',
            'is_active' => 0,
            'is_test' => 1,
            'setting' => '',
        );
        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

    //BitPay gateway
    $gatewayObj = $oDatabase->select("ppi.*")
        ->from(Phpfox::getT('api_gateway'), 'ppi')
        ->where(' `gateway_id` LIKE  \'bitpay\' ')
        ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
        $aInsert = array(
            'gateway_id' => 'bitpay',
            'title' => 'BitPay',
            'description' => 'BitPay was founded in 2011, while Bitcoin was still in its infancy. We saw the potential for bitcoin to revolutionize the financial industry, making payments faster, more secure, and less expensive on a global scale.',
            'is_active' => 0,
            'is_test' => 1,
            'setting' => '',
        );
        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

//  Temporary remove Heideilpay in v4.02
//    //HeidelPay gateway
//    $gatewayObj = $oDatabase->select("ppi.*")
//        ->from(Phpfox::getT('api_gateway'), 'ppi')
//        ->where(' `gateway_id` LIKE  \'heidelpay\' ')
//        ->execute('getSlaveRow');
//
//    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
//        $aInsert = array(
//            'gateway_id' => 'heidelpay',
//            'title' => 'HeidelPay',
//            'description' => 'Heidelberger Payment GmbH is a leading payment institution for online payment methods, authorized and regulated by the Federal Financial Supervisory Authority BaFin. Our company offers the full range of services for worldwide electronic payment processing.',
//            'is_active' => 0,
//            'is_test' => 1,
//            'setting' => '',
//        );
//        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
//    }

    //Braintree gateway
    $gatewayObj = $oDatabase->select("ppi.*")
        ->from(Phpfox::getT('api_gateway'), 'ppi')
        ->where(' `gateway_id` LIKE  \'braintree\' ')
        ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
        $aInsert = array(
            'gateway_id' => 'braintree',
            'title' => 'Braintree',
            'description' => 'We provide the global commerce tools people need to build businesses, accept payments, and enable commerce for their users. It’s the simplest way to get paid for your great ideas ',
            'is_active' => 0,
            'is_test' => 1,
            'setting' => '',
        );
        $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }
    $iTotalPlugin = db()
        ->select('COUNT(plugin_id)')
        ->from(Phpfox::getT('plugin'))
        ->where(array(
            'module_id' => 'core',
            'product_id' => 'phpfox',
            'call_name' => 'admincp.service_module_process_updateactivity',
            'title' => 'Advanced Payment Plugin Update'
        ))
        ->execute('getField');
    if ($iTotalPlugin == 0) {
        // add plugin to support update display block when enable/disable app
        db()->insert(':plugin', array(
            'module_id' => 'core',
            'product_id' => 'phpfox',
            'call_name' => 'admincp.service_module_process_updateactivity',
            'title' => 'Advanced Payment Plugin Update',
            'php_code' => '<?php
                    $module_id = $this->database()->escape($iId);
                    $is_active = (int)($iType == \'1\' ? 1 : 0);
                    $aListPayment = [\'authorizenet\',\'ccbill\',\'gopay\',\'itransact\',\'skrill\',\'webmoney\',\'stripe\',\'bitpay\',\'braintree\',\'heidelpay\'];
                    if ($module_id == \'ynadvancedpayment\') {
                        if ($is_active != 1) {
                            $aActiveGateway = db()->select(\'gateway_id\')
                                                ->from(\':api_gateway\')
                                                ->where(\'gateway_id IN ("\'.implode(\'","\',$aListPayment).\'") AND is_active = 1\')
                                                ->execute(\'getSlaveRows\');
                            if (count($aActiveGateway)) {
                                storage()->set(\'ynadvancedpayment_active_payment\',$aActiveGateway);
                                db()->update(\':api_gateway\',[\'is_active\' => 0],\'gateway_id IN ("\'.implode(\'","\',$aListPayment).\'")\');
                            }
                        } else {
                            $aLastActive = storage()->get(\'ynadvancedpayment_active_payment\');
                            storage()->del(\'ynadvancedpayment_active_payment\');
                            $aPayments = $aLastActive->value;
                            if (count($aPayments)) {
                                foreach ($aPayments as $aPayment) {
                                    db()->update(\':api_gateway\',[\'is_active\' => 1],\'gateway_id ="\'.$aPayment->gateway_id.\'"\');
                                }
                            }
                        }
                    }
                ?>',
            'is_active' => 1
        ));
    }
}

ynap_install402();
