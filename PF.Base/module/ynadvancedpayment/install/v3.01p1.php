<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 *
 */

function ynap_install301p1()
{
    $oDatabase = Phpfox::getLib('database');

    //gopay gateway
      $gatewayObj = $oDatabase->select("ppi.*")
                  ->from(Phpfox::getT('api_gateway'), 'ppi')
                  ->where(' `gateway_id` LIKE  \'gopay\' ')
                  ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
      $aInsert = array(
           'gateway_id' => 'gopay',
           'title' => 'GoPay',
           'description' => 'GoPay is payment gateway operating in Czech Republic and Slovakia.',
           'is_active' => 0,
           'is_test' => 1,
           'setting' => '', 
      );
      $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

}

ynap_install301p1();

?>