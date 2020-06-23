<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 07/02/2017
 * Time: 15:50
 */
defined('PHPFOX') or exit('NO DICE!');

function ynd_install401p10()
{
    $oDatabase = Phpfox::getLib('database') ;

    //Integrate with advanced blog
    $oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('directory_module')."`(`module_id`, `module_phrase`, `module_name`, `module_type`, `module_description`, `module_landing`) VALUES
		(20, '{phrase var=&#039;ynblog&#039;}', 'ynblog', 'module', '', 0);
		");

    $oDatabase->query("
	INSERT IGNORE INTO `".Phpfox::getT('directory_business_memberrolesetting')."`(`setting_id`, `setting_name`, `setting_title`, `data`) VALUES
		(37, 'share_a_ynblog', '{phrase var=&#039;directory.share_a_ynblog&#039;}',
             '{
             \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
             \"no\":\"{phrase var=&#039;directory.no&#039;}\"
              }'
         ),
         (38, 'view_browse_ynblogs', '{phrase var=&#039;directory.view_browse_ynblogs&#039;}',
             '{
             \"yes\":\"{phrase var=&#039;directory.yes&#039;}\",
             \"no\":\"{phrase var=&#039;directory.no&#039;}\"
              }'
         );
		");

    $oDatabase = Phpfox::getLib('database');
    $aMemberRoles = $oDatabase->select('role_id')->from(Phpfox::getT('directory_business_memberrole'))->execute('getRows');
    $isHasSetting = $oDatabase->select('COUNT(data_id)')->from(Phpfox::getT('directory_business_memberrolesettingdata'))->where('setting_id IN (37, 38) ')->execute('getField');

    if (!empty($aMemberRoles) && empty($isHasSetting)) {
        foreach ($aMemberRoles as $aMemberRole)
        {
            $oDatabase->insert(Phpfox::getT('directory_business_memberrolesettingdata'), array(
                'setting_id' => 37,
                'role_id' => intval($aMemberRole['role_id']),
                'status' => 'yes'
            ));

            $oDatabase->insert(Phpfox::getT('directory_business_memberrolesettingdata'), array(
                'setting_id' => 38,
                'role_id' => intval($aMemberRole['role_id']),
                'status' => 'yes'
            ));
        }
    }


    $aBusinessModules = $oDatabase->select('business_id')->from(Phpfox::getT('directory_business_module'))->where('module_name = \'blogs\'')->execute('getSlaveRows');
    $isHasBusinessModules = $oDatabase->select('COUNT(data_id)')->from(Phpfox::getT('directory_business_module'))->where('module_name = \'ynblog\'')->execute('getField');

    if (!empty($aBusinessModules) && empty($isHasBusinessModules)) {
        foreach ($aBusinessModules as $aBusinessModule)
        {
            $oDatabase->insert(Phpfox::getT('directory_business_module'), [
                'business_id' => $aBusinessModule['business_id'],
                'module_id' => 20, //See in service directory function getDefaultModulesInBusiness
                'contentpage' => '',
                'contentpage_parsed' => '',
                'module_phrase' => '{phrase var=&#039;ynblog&#039;}',
                'module_name' => 'ynblog',
                'module_type' => 'module',
                'module_description' => '',
                'is_show' => 1,
                'module_landing' => 0,
            ]);
        }
    }
    $oDatabase->query("UPDATE `". Phpfox::getT('setting')."` SET `is_hidden` = 1 WHERE `module_id` = 'directory' AND `var_name` = 'categories_to_show_at_first';");
    $oDatabase->query("UPDATE `".Phpfox::getT('directory_module')."` SET `module_phrase` = '{phrase var=&#039;video_channel&#039;}' WHERE `module_name` = 'videos';");
}
ynd_install401p10();