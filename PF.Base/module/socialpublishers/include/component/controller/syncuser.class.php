<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Component_Controller_SyncUser extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        phpfox::isUser(true);
        $bRedirect = $this->request()->get('redirect');

        $sService = $this->request()->get('service');
        $sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
        if (phpfox::getParam('core.url_rewrite') >= 2)
        {
            $aParams = $_REQUEST;
        }
        else
        {
            $sRequestUri = $_SERVER['REQUEST_URI'];
            $sRequestUri = str_replace('/index.php?do=', '', $sRequestUri);
            parse_str($sRequestUri, $aParams);
            if (count($aParams) <= 0)
            {
                return false;
            }
            foreach ($aParams as $v => $k)
            {
                $aParams['identity'] = $k;
                break;
            }
        }
        if (isset($aParams['redirect']))
        {
            $bRedirect = $aParams['redirect'];
        }
        if (isset($aParams['service']) && $aParams['service'] == 'facebook' && isset($params['sccreturn']))
        {
            $this->template()->assign(array(
                'bRedirect' => 1,
                'sUrlRedirect' => phpfox::getLib('url')->makeUrl('socialpublishers.setting'),
                    )
            );
            return false;
        }
        if (isset($aParams['service']) && $aParams['service'] == 'facebook')
        {
            $aSession = Phpfox::getService('socialbridge.libs')->getFBAccessToken();
            $aExtra = phpfox::getService('socialpublishers')->getProfile($aParams['service'], $aSession);
            phpfox::getService('socialpublishers')->addToken(phpfox::getUserId(), $aParams['service'], $aSession, $aExtra);
            $sUrlRedirect = phpfox::getLib('url')->makeUrl('socialpublishers.setting');
        }

        $this->template()->assign(
                array(
                    'bRedirect' => $bRedirect,
                    'sUrlRedirect' => $sUrlRedirect,
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_sync_clean')) ? eval($sPlugin) : false);
    }

}

?>