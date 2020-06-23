<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Modules extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialpublishers_modules');
    }

    public function getModules($bDisplay = true)
    {
        $sQuery = "";
        $aModules = $this->database()->select('*')
                ->from($this->_sTable)
                ->where($sQuery)
                ->execute('getRows');
        $aResults = array();
        $aRemoveModules = array();

        if (count($aModules))
        {
            foreach ($aModules as $iKey => $aModule)
            {
                if (phpfox::hasCallback($aModule['module_id'], 'getPublishersSetting'))
                {
                    $aResults = array_merge($aResults, phpfox::Callback($aModule['module_id'] . '.getPublishersSetting'));
                    continue;
                }
                if (strpos($aModule['module_id'], '_') !== false)
                {
                    $sModule = explode('_', $aModule['module_id']);
                    $sModule = $sModule[0];
                    if (phpfox::hasCallback($sModule, 'getPublishersSetting'))
                    {
                        $aResults = array_merge($aResults, phpfox::Callback($sModule . '.getPublishersSetting'));
                        continue;
                    }
                }
                if (!phpfox::isModule($aModule['module_id']) && $aModule['module_id'] != "pages_comment" && $aModule['module_id'] != "feed_comment" && $aModule['module_id'] != "status")
                {
                    if (!phpfox::hasCallback($aModule['module_id'], 'getPublishersSetting'))
                    {
                        $aRemoveModules[$aModule['module_id']] = $aModule;
                    }
                    else
                    {
                        $aResults[$aModule['module_id']] = phpfox::hasCallback($aModule['module_id'], 'getPublishersSetting');
                    }
                    continue;
                }
                else
                {

                    if (($aModule['is_active'] == 1 && $bDisplay == true) || $bDisplay == false)
                    {
                        $aResults[$aModule['module_id']] = $aModules[$iKey];
                    }
                    else
                    {
                        $aRemoveModules[$aModule['module_id']] = $aModules[$iKey];
                    }
                }
            }
        }
        unset($aResults['link']);
        return $aResults;
    }

    public function getModule($sModule = "")
    {
        if ($sModule == "")
        {
            return array();
        }
        //because karaoke module_id and feed type name difference , we have to make it sync
        if ($sModule == "karaoke_song" || $sModule == "karaoke_recording")
        {
            $sModule = "karaoke";
        }
        // same with karaoke module
        if ($sModule == "musicsharing_album" || $sModule == "musicsharing_playlist")
        {
            $sModule = 'musicsharing';
        }
        //same here
        if ($sModule == "musicstore_album" || $sModule == "musicstore_playlist")
        {
            $sModule = 'musicstore';
        }
		
		if ($sModule == "jobposting_company" || $sModule == "jobposting_job")
        {
            $sModule = 'jobposting';
        }
		if ($sModule == "ultimatevideo_video" || $sModule == "ultimatevideo_playlist")
        {
            $sModule = 'ultimatevideo';
        }
        if ($sModule == "ynsocialstore_store" || $sModule == "ynsocialstore_product")
        {
            $sModule = 'ynsocialstore';
        }
        $aModule = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('module_id = "' . $sModule . '"')
                ->execute('getRow');
        if (isset($aModule['module_id']))
        {
            return $aModule;
        }
        if (phpfox::hasCallback($sModule, 'getPublishersSetting'))
        {
            $a3rdModules = phpfox::callback($sModule . '.getPublishersSetting');
            return $a3rdModules[$sModule];
        }
        return $aModule;
    }

    public function updateSettings($aVals)
    {
        //update facebook, twitter, linkedin of all modules/apps to 0 for case if admin not check three of them, so cannot get value of checkbox to update
        $this->database()->update($this->_sTable,[
            'facebook' => 0,
            'twitter' => 0,
            'linkedin' => 0
        ],'module_id IS NOT NULL');

        foreach ($aVals as $sKey => $aModule)
        {
            $aUpdate['facebook'] = isset($aModule['facebook']) ? $aModule['facebook'] : 0;
            $aUpdate['twitter'] = isset($aModule['twitter']) ? $aModule['twitter'] : 0;
            $aUpdate['linkedin'] = isset($aModule['linkedin']) ? $aModule['linkedin'] : 0;
            $aExistModule = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('module_id = "' . $sKey . '"')
                    ->execute('getRow');
            if (isset($aExistModule['module_id']))
            {
                $this->database()->update($this->_sTable, $aUpdate, 'module_id ="' . $sKey . '"');
            }
            else
            {
                $aUpdate['module_id'] = $sKey;

                if (phpfox::hasCallback($sKey, 'getPublishersSetting'))
                {
                    $aSetting = phpfox::callback($sKey . '.getPublishersSetting');
                    $aUpdate['module_id'] = $sKey;
                    $aUpdate['product_id'] = $aSetting[$sKey]['product_id'];
                    $aUpdate['title'] = $aSetting[$sKey]['title'];
                    $this->database()->insert($this->_sTable, $aUpdate);
                }
            }
        }
        return true;
    }

    public function updateModuleSetting($iModuleId,$iActive)
    { 
        $aUpdate['is_active'] = $iActive;

        $aExistModule = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('module_id = "' . $iModuleId . '"')
                ->execute('getRow');
    
        if (isset($aExistModule['module_id']))
        {
            $this->database()->update($this->_sTable, $aUpdate, 'module_id ="' . $iModuleId . '"');
        }
        return true;
    }

    //user group setting
    public function getUserModuleSettings($iUserId = null, $sModuleId = "")
    {
        $iUserId = Phpfox::getService('socialpublishers')->getRealUser((int) $iUserId);
        //sm.is_active,sm.facebook as smfacebook,sm.twitter as smtwitter, sm.linkedin as smlinkedin,ss.facebook,ss.twitter,ss.linkedin,ss.no_ask,ss.auto_publish,ss.module,ss.user_id
        $aResults = $this->database()->select('*')
                ->from(phpfox::getT('socialpublishers_settings'), 'ss')
                ->where('ss.user_id = ' . (int) $iUserId . ' AND ss.module = "' . $sModuleId . '"')
                ->execute('getRow');
        return $aResults;
    }

    public function insertUserSetting($iUserId = null, $sModule = "", $aVal)
    {
        $aInsert = array(
            'user_id' => $iUserId,
            'module' => $sModule,
            'facebook' => isset($aVal['facebook']) ? $aVal['facebook'] : 0,
            'linkedin' => isset($aVal['linkedin']) ? $aVal['linkedin'] : 0,
            'twitter' => isset($aVal['twitter']) ? $aVal['twitter'] : 0,
            'auto_publish' => 0,
            'no_ask' => isset($aVal['no_ask']) ? $aVal['no_ask'] : 0,
        );
        $this->database()->insert(phpfox::getT('socialpublishers_settings'), $aInsert);
    }

    public function updateUserSetting($iUserId = null, $sModule = "", $aVal)
    {
        $aInsert = array(
            'facebook' => isset($aVal['facebook']) ? $aVal['facebook'] : 0,
            'linkedin' => isset($aVal['linkedin']) ? $aVal['linkedin'] : 0,
            'twitter' => isset($aVal['twitter']) ? $aVal['twitter'] : 0,
            'auto_publish' => 0,
            'no_ask' => isset($aVal['no_ask']) ? $aVal['no_ask'] : 0,
        );
        $this->database()->update(phpfox::getT('socialpublishers_settings'), $aInsert, 'user_id = ' . (int) $iUserId . ' AND module = "' . $sModule . '"');
    }

    public function updateUserSettings($iUserId = null, $aVals)
    {

        foreach ($aVals as $sKey => $aVal)
        {
            if ($aVal['is_insert'] == 1)
            {
                $this->insertUserSetting($iUserId, $sKey, $aVal);
            }
            else
            {
                $this->updateUserSetting($iUserId, $sKey, $aVal);
            }
        }
        
        return true;
    }

    //end
}

?>
