	
<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Component_Block_Settings extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $oModules = phpfox::getService('socialpublishers.modules');
        $aModules = $oModules->getModules(true);
        $oProvider = Phpfox::getService('socialbridge.providers');
        $bIsFacebook = $oProvider->getProvider('facebook', true);
        $bIsTwitter = $oProvider->getProvider('twitter', true);
        $bIsLinkedIn = $oProvider->getProvider('linkedin', true);

        if (count($aModules))
        {
            foreach ($aModules as $iKey => $aModule)
            {
                if (!isset($bIsFacebook['service_id']))
                {
                    $aModules[$iKey]['facebook'] = 0;
                }

                if (!isset($bIsTwitter['service_id']))
                {
                    $aModules[$iKey]['twitter'] = 0;
                }

                if (!isset($bIsLinkedIn['service_id']))
                {
                    $aModules[$iKey]['linkedin'] = 0;
                }
				
                $aModules[$iKey]['user_setting'] = $oModules->getUserModuleSettings(phpfox::getUserId(), $aModule['module_id']);
                if (!phpfox::isModule($aModule['module_id']) && $aModule['module_id'] != "pages_comment" && $aModule['module_id'] != "feed_comment" && $aModule['module_id'] != "status")
                {
                    $bCallBack = false;
                    if (strpos($aModule['module_id'], '_') !== false)
                    {
                        $sModule = explode('_', $aModule['module_id']);
                        $sModule = $sModule[0];
                        if (phpfox::hasCallback($sModule, 'getPublishersSetting'))
                        {
                            $bCallBack = true;
                        }
                    }
                    if (!phpfox::hasCallback($aModule['module_id'], 'getPublishersSetting') && $bCallBack === false)
                    {
                        unset($aModules[$iKey]);
                    }
                    else
                    {
                        if (count($aModules[$iKey]['user_setting']) <= 0)
                        {
                            $aModules[$iKey]['user_setting']['facebook'] = isset($aModules[$iKey]['facebook']) ? $aModules[$iKey]['facebook'] : 1;
                            $aModules[$iKey]['user_setting']['twitter'] = isset($aModules[$iKey]['twitter']) ? $aModules[$iKey]['twitter'] : 1;
                            $aModules[$iKey]['user_setting']['linkedin'] = isset($aModules[$iKey]['linkedin']) ? $aModules[$iKey]['linkedin'] : 1;
                            $aModules[$iKey]['user_setting']['no_ask'] = 0;
                            
                            $aModules[$iKey]['is_insert'] = 1;
                        }
                        else
                        {
                            $aModules[$iKey]['is_insert'] = 0;
                        }
                    }
                    continue;
                }
                
                if (count($aModules[$iKey]['user_setting']) <= 0)
                {
                    $aModules[$iKey]['user_setting']['facebook'] = isset($aModules[$iKey]['facebook']) ? $aModules[$iKey]['facebook'] : 1;
                    $aModules[$iKey]['user_setting']['twitter'] = isset($aModules[$iKey]['twitter']) ? $aModules[$iKey]['twitter'] : 1;
                    $aModules[$iKey]['user_setting']['linkedin'] = isset($aModules[$iKey]['linkedin']) ? $aModules[$iKey]['linkedin'] : 1;
                    $aModules[$iKey]['user_setting']['no_ask'] = 0;
                    
                    $aModules[$iKey]['is_insert'] = 1;
                }
                else
                {
                    $aModules[$iKey]['is_insert'] = 0;
                }
            }
        }

        $this->template()->assign(array('aModules' => $aModules));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('core.component_block_news_clean')) ? eval($sPlugin) : false);
    }

}

?>
