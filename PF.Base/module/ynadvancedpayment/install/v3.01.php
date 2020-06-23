<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 *
 */

function ynap_install301()
{
    $oDatabase = Phpfox::getLib('database');

    $gatewayObj = $oDatabase->select("ppi.*")
                    ->from(Phpfox::getT('api_gateway'), 'ppi')
                    ->where(' `gateway_id` LIKE  \'authorizenet\' ')
                    ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id'])){
      // transaction_settings is: 
      // AUTH_CAPTURE : Authorization and Capture
      // AUTH_ONLY : Authorization Only
      // CAPTURE_ONLY : Capture Only
      // CREDIT : Credit (Refund)
      // PRIOR_AUTH_CAPTURE : Prior Authorization and Capture
      // VOID : Void
      $aInsert = array(
           'gateway_id' => 'authorizenet',
           'title' => 'Authorize.Net',
           'description' => 'Authorize.Net has been a leading provider of payment gateway services, managing the submission of billions of transactions to the processing networks on behalf of merchant customers. Authorize.Net is a solution of CyberSource Corporation, a wholly owned subsidiary of Visa',
           'is_active' => 0,
           'is_test' => 0,
           'setting' => 'a:3:{s:9:"api_login";s:0:"";s:15:"transaction_key";s:0:"";s:20:"transaction_settings";s:12:"AUTH_CAPTURE";}', 
      );
      $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

    $oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('ynadvancedpayment_subscriptions')."` (
      `subscription_id` int(11) unsigned NOT NULL auto_increment,
      `user_id` int(10) unsigned NOT NULL,
      `getaway_subscription_id` int(11) unsigned NOT NULL,
      `creation_date` int(10) unsigned DEFAULT NULL,
      `modified_date` int(10) unsigned DEFAULT NULL,
      `gateway_id` varchar(75) NOT NULL,
      `package_id` int(10) unsigned default NULL,
      `purchase_id` int(10) unsigned default NULL,
      PRIMARY KEY  (`subscription_id`)
    );");

    $gatewayObj = $oDatabase->select("ppi.*")
                    ->from(Phpfox::getT('api_gateway'), 'ppi')
                    ->where(' `gateway_id` LIKE  \'ccbill\' ')
                    ->execute('getSlaveRow');

    if(!isset($gatewayObj) || !isset($gatewayObj['gateway_id']))
    {
      $aInsert = array(
           'gateway_id' => 'ccbill',
           'title' => 'CC Bill',
           'description' => 'CCBill - As a trusted leader in global payment solutions since 1998, CCBill processes more than a billion dollars in transactions each year and is one of the largest third-party payment processors.',
           'is_active' => 0,
           'is_test' => 0,
           'setting' => 'a:4:{s:13:"ccbill_accnum";s:0:"";s:16:"ccbill_subaccnum";s:0:"";s:11:"ccbill_salt";s:0:"";s:14:"ccbill_form_id";s:0:"";}', 
      );
      $oDatabase->insert(Phpfox::getT('api_gateway'), $aInsert);
    }

}

ynap_install301();

?>