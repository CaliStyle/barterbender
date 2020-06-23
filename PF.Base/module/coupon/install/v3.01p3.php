<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01p3
 *
 */

function ync_install301p3()
{
    $oDatabase = Phpfox::getLib('database') ;
    
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Can edit own coupon (on draft)', `text_default` = 'Can edit own coupon (on draft)'
            WHERE `var_name` = 'user_setting_can_edit_own_coupon'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
    
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Can edit coupon created by other users (apply on administrator groups)', `text_default` = 'Can edit coupon created by other users (apply on administrator groups)'
            WHERE `var_name` = 'user_setting_can_edit_other_user_coupon'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
        
    }

ync_install301p3();

?>