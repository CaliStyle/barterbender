<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01p2
 *
 */

function ync_install301p2()
{
    $oDatabase = Phpfox::getLib('database') ;

    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Publishing fee : $\{publish_fee\}', `text_default` = 'Publishing fee : $\{publish_fee\}'
            WHERE `var_name` = 'publishing_fee'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
    
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Total fee : $\{total_fee\}', `text_default` = 'Total fee : $\{total_fee\}'
            WHERE `var_name` = 'total_fee'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Features this coupon : $\{feature_fee\}', `text_default` = 'Features this coupon : $\{feature_fee\}'
            WHERE `var_name` = 'features_this_coupon'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Are you sure you want to publish this coupon with total $\{total_fee\}?', `text_default` = 'Are you sure you want to publish this coupon with total $\{total_fee\}?'
            WHERE `var_name` = 'confirm_publish_coupon'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
    $oDatabase->query("
        UPDATE `" . Phpfox::getT('language_phrase') . "`
            SET `text` = 'Set to True, if admin want coupon to be automatically approved after published', `text_default` = 'Set to True, if admin want coupon to be automatically approved after published'
            WHERE `var_name` = 'user_setting_auto_approve_after_publish'
            AND `module_id` = 'coupon'
            AND `product_id` = 'younet_coupon'
            AND `language_id` = 'en'
    ");
        
    }

ync_install301p2();

?>