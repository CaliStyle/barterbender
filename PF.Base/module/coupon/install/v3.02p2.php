<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.02
 *
 */

function ync_install302p2()
{
    $oDatabase = Phpfox::getLib('database');

    $oDatabase->query("UPDATE `".Phpfox::getT('coupon')."` 
      SET `discount_currency` = 'USD' WHERE `discount_type` = 'price' AND (`discount_currency` IS NULL OR `discount_currency` = '');");
}

ync_install302p2();

?>