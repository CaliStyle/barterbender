<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/28/16
 * Time: 16:54
 */
defined('PHPFOX') or exit('NO DICE!');

function ynd_install401p8()
{
    $oDatabase = Phpfox::getLib('database') ;
    $oDatabase->query("UPDATE `". Phpfox::getT('user_group_setting')."` SET `is_hidden` = 1 WHERE `module_id` = 'directory' AND `name` = 'can_review_business';");
    $oDatabase->query("UPDATE `". Phpfox::getT('setting')."` SET `is_hidden` = 1 WHERE `module_id` = 'directory' AND `var_name` = 'google_api_key_location';");
}
ynd_install401p8();