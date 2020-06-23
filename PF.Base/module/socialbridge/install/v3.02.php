<?php
Phpfox_Error::skip(true);
defined('PHPFOX') or exit('NO DICE!');

function socialbridge_agents()
{
    $sTable = Phpfox::getT('socialbridge_agents');

    if (Phpfox::isModule('socialstream'))
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $sTable . "` (
            `agent_id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) unsigned NOT NULL,
            `identity` varchar(128) NULL,
            `service_id` int(11) unsigned NOT NULL,
            `ordering` int(11) unsigned NULL,
            `token` text,
            `params` text,
            `full_name` varchar(255) DEFAULT NULL,
            `user_name` varchar(75) NULL,
            `img_url` varchar(255) NULL,
            `privacy` tinyint(1) unsigned DEFAULT '0',
            `last_feed` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`agent_id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1";
    }
    else
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $sTable . "` (
            `agent_id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) unsigned NOT NULL,
            `identity` varchar(128) NULL,
            `service_id` int(11) unsigned NOT NULL,
            `ordering` int(11) unsigned NULL,
            `token` text,
            `params` text,
            `full_name` varchar(255) DEFAULT NULL,
            `user_name` varchar(75) NULL,
            `img_url` varchar(255) NULL,            
            PRIMARY KEY (`agent_id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1";
    }

    Phpfox::getLib('phpfox.database')->query($sql);
}

function socialbridge_services_setting()
{
    $sTable = Phpfox::getT('socialbridge_services_setting');

    $sql = "CREATE TABLE IF NOT EXISTS `" . $sTable . "` (
              `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `user_id` int(10) unsigned NOT NULL,
              `service_id` int(10) unsigned NOT NULL,
              `module_id` varchar(255) NOT NULL,
              `is_active` tinyint(1) unsigned DEFAULT '1',
              PRIMARY KEY (`setting_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1";

    Phpfox::getLib('phpfox.database')->query($sql);
}

socialbridge_agents();
socialbridge_services_setting();

function addSetting($sService = "", $sParams = "", $iStatus = 0)
{
    if ($sService == "")
    {
        return false;
    }

    (($sPlugin = Phpfox_Plugin::get('socialbridge.service_libs_addsetting_start')) ? eval($sPlugin) : false);
    Phpfox::getLib('phpfox.database')->update(phpfox::getT('socialbridge_services'), array(
        'params' => $sParams,
        'is_active' => $iStatus
            ), 'name ="' . $sService . '"');
}

function migrate($aProviders, &$aSkip)
{
    if (count($aProviders) && $aProviders)
    {
        foreach ($aProviders as $aProvider)
        {
            $sName = $aProvider['name'];

            if ($aSkip[$sName] == false)
            {
                if ($aProvider['params'] && phpfox::getLib('parse.format')->isSerialized($aProvider['params']))
                {
                    $aProvider['params'] = unserialize($aProvider['params']);
                    $aSkip[$sName] = true;
                }
                $aProvider['params'] = serialize($aProvider['params']);
                addSetting($aProvider['name'], $aProvider['params'], $aProvider['is_active']);
            }
        }
    }
}

function autoMigrateSettings()
{
    $aFacebook = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialbridge_services'))->where("name = 'facebook'")->execute('getRow');
    $aTwitter = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialbridge_services'))->where("name = 'twitter'")->execute('getRow');
    $aLinkedIn = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialbridge_services'))->where("name = 'linkedin'")->execute('getRow');
    $aSkip = array(
        'facebook' => false,
        'twitter' => false,
        'linkedin' => false
    );

    if (isset($aFacebook['params']) && !empty($aFacebook['params']) && phpfox::getLib('parse.format')->isSerialized($aFacebook['params']))
    {
        $aSkip['facebook'] = true;
    }

    if (isset($aTwitter['params']) && !empty($aTwitter['params']) && phpfox::getLib('parse.format')->isSerialized($aTwitter['params']))
    {
        $aSkip['twitter'] = true;
    }

    if (isset($aLinkedIn['params']) && !empty($aLinkedIn['params']) && phpfox::getLib('parse.format')->isSerialized($aLinkedIn['params']))
    {
        $aSkip['linkedin'] = true;
    }

    if ($aSkip['facebook'] == true && $aSkip['linkedin'] == true && $aSkip['twitter'] == true)
    {
        return true;
    }

    $aProviders = array();
    if (phpfox::isModule('socialpublishers'))
    {
        $aProviders = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialpublishers_services'))->order('ordering ASC')->execute('getRows');
    }
    migrate($aProviders, $aSkip);

    if (phpfox::isModule('opensocialconnect'))
    {
        $aProviders = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialconnect_services'))->where("name = 'facebook'")->execute('getRows');
    }
    migrate($aProviders, $aSkip);

    if (phpfox::isModule('socialstream'))
    {
        $aProviders = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialstream_services'))->order('ordering ASC')->execute('getRows');
    }
    migrate($aProviders, $aSkip);

    if (phpfox::isModule('socialmediaimporter'))
    {
        $aProviders = Phpfox::getLib('phpfox.database')->select('*')->from(phpfox::getT('socialmediaimporter_services'))->order('ordering ASC')->execute('getRows');
    }
    migrate($aProviders, $aSkip);
}

autoMigrateSettings();

function migrateUsersAgent()
{
    $sTable = Phpfox::getT('socialbridge_agents');

    /*     * * IMPORT FROM SOCIAL PUBLISHERS MODULE ** */

    if (phpfox::isModule('socialpublishers'))
    {
        $aAgentIds = Phpfox::getLib('phpfox.database')->select('DISTINCT user_id')->from(phpfox::getT('socialbridge_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook"')->execute('getRows');
        $sIds = '0';
        foreach ($aAgentIds as $aAgentId)
        {
            $sIds .= $aAgentId['user_id'] . ',';
        }
        $sIds = rtrim($sIds, ',');
        if (empty($sIds))
        {
            $sIds = '0';
        }
        $aAgents = Phpfox::getLib('phpfox.database')->select('DISTINCT sa.*')->from(phpfox::getT('socialpublishers_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook" AND sa.user_id NOT IN (' . $sIds . ')')->execute('getRows');

        if (count($aAgents))
        {
            foreach ($aAgents as $aAgent)
            {
                if(isset($aAgent['agent_id']))
                {
                    unset($aAgent['agent_id']);
                }                                                   
                if(isset($aAgent['privacy']))
                {
                    unset($aAgent['privacy']);
                }                                   
                if(isset($aAgent['last_feed']))
                {
                    unset($aAgent['last_feed']);
                }                                   
                if(isset($aAgent['cron_id']))
                {
                    unset($aAgent['cron_id']);
                }
                Phpfox::getLib('phpfox.database')->insert($sTable, $aAgent);
            }
        }
    }

    /*     * * IMPORT FROM SOCIAL STREAM MODULE ** */
    if (phpfox::isModule('socialstream'))
    {
        $aAgentIds = Phpfox::getLib('phpfox.database')->select('DISTINCT user_id')->from(phpfox::getT('socialbridge_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook"')->execute('getRows');
        $sIds = '';
        foreach ($aAgentIds as $aAgentId)
        {
            $sIds .= $aAgentId['user_id'] . ',';
        }
        $sIds = rtrim($sIds, ',');

        if (empty($sIds))
        {
            $sIds = '0';
        }
        $aAgents = Phpfox::getLib('phpfox.database')->select('DISTINCT sa.*')->from(phpfox::getT('socialstream_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook" AND sa.user_id NOT IN (' . $sIds . ')')->execute('getRows');
        if (count($aAgents))
        {
            foreach ($aAgents as $aAgent)
            {
                if(isset($aAgent['agent_id']))
                {
                    unset($aAgent['agent_id']);
                }                                                   
                if(isset($aAgent['privacy']))
                {
                    unset($aAgent['privacy']);
                }                                   
                if(isset($aAgent['last_feed']))
                {
                    unset($aAgent['last_feed']);
                }                                   
                if(isset($aAgent['cron_id']))
                {
                    unset($aAgent['cron_id']);
                }                   
                Phpfox::getLib('phpfox.database')->insert($sTable, $aAgent);
            }
        }
    }

    /*     * * IMPORT FROM SOCIAL MEDIA IMPORTER MODULE ** */
    if (phpfox::isModule('socialmediaimporter'))
    {
        $aAgentIds = Phpfox::getLib('phpfox.database')->select('DISTINCT user_id')->from(phpfox::getT('socialbridge_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook"')->execute('getRows');
        $sIds = '';
        foreach ($aAgentIds as $aAgentId)
        {
            $sIds .= $aAgentId['user_id'] . ',';
        }
        $sIds = rtrim($sIds, ',');
        if (empty($sIds))
        {
            $sIds = '0';
        }
        $aAgents = Phpfox::getLib('phpfox.database')->select('DISTINCT sa.*')->from(phpfox::getT('socialmediaimporter_agents'), 'sa')->join(Phpfox::getT('socialbridge_services'), 'sbs', 'sbs.service_id = sa.service_id')->where('sbs.name = "facebook" AND sa.user_id NOT IN (' . $sIds . ')')->execute('getRows');
        if (count($aAgents))
        {
            foreach ($aAgents as $aAgent)
            {
                if(isset($aAgent['agent_id']))
                {
                    unset($aAgent['agent_id']);
                }                                                   
                if(isset($aAgent['privacy']))
                {
                    unset($aAgent['privacy']);
                }                                   
                if(isset($aAgent['last_feed']))
                {
                    unset($aAgent['last_feed']);
                }                                   
                if(isset($aAgent['cron_id']))
                {
                    unset($aAgent['cron_id']);
                }               
                Phpfox::getLib('phpfox.database')->insert($sTable, $aAgent);
            }
        }
    }
}

migrateUsersAgent();
Phpfox_Error::skip(false);
?>
