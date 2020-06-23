<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


class OpenSocialConnect_Component_Controller_Sync extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (Phpfox::getParam('core.url_rewrite') >= 2) {
            $params = $_REQUEST;
        } else {
            $cur_url = $_SERVER['REQUEST_URI'];
            $cur_url = str_replace('/index.php?do=', '', $cur_url);
            parse_str($cur_url, $params);
        }

        if (!isset($params['service'])) {
            $params['service'] = Phpfox::getLib('request')->get('service');
        }

        $oService = Phpfox::getService('opensocialconnect');

        $bApiProvider = false;

        $sService = isset($params['service']) ? strtolower($params['service']) : null;

        $oBridge = Phpfox::getService('socialbridge');

        if ($oBridge->hasProvider($sService)) {
            $oProvider = $oBridge->getProvider($sService);
            $data = $oProvider->getProfile();
            $params = array_merge($params, $data);
        }

        $sIdentity = isset($params['identity']) ? $params['identity'] : null;

        $aUser = $oService->getUserByIdentityAndService($sIdentity, $sService);

        if ($aUser) {
            list($bLoginOK, $aUser) = $oService->loginByEmail($aUser['email']);
            // user does not exists anymore, please visit to signup.
            if (isset($_SESSION['urlRedirect'])) {
                $sUrlRedirect = $_SESSION['urlRedirect'];
            } else {
                $sUrlRedirect = Phpfox::getParam('core.path');
                Phpfox::getService('opensocialconnect.providers')->updateStatistics($sService, 'login');
                Phpfox::getService('opensocialconnect')->updateBridgeToken($aUser['user_id'], $sService);
            }

            $this->template()->assign(array(
                'step' => 'checksignon',
                'sUrlRedirect' => $sUrlRedirect,
            ));
        } else {
            $aProviderFields = Phpfox::getService('opensocialconnect')->getProviderFields($sService);
            $tempParams = array();
            if (count($aProviderFields)) {
                foreach ($aProviderFields as $fiels) {
                    if ($fiels['field'] != 'all' && isset($params[$fiels['field']])) {
                        $tempParams[$fiels['question']] = $params[$fiels['field']];
                    }
                }
            }

            foreach ($tempParams as $key => $value) {
                $params[$key] = $value;
            }

            // saved data to session
            $oService->setSignupSessionData($sService, array(
                'service' => $sService,
                'identity' => $sIdentity,
                'birthday' => isset($params['birthday']) ? $params['birthday'] : '',
                'birthday_search' => isset($params['birthday_search']) ? $params['birthday_search'] : '',
                'gender' => isset($params['gender']) ? $params['gender'] : '',
                'user' => $params
            ));

            $sUrlRedirect = $this->url()->makeUrl('opensocialconnect.quicksignup',
                array('service' => $params['service']));

            $this->template()->assign(array(
                'step' => 'checksignup',
                'sUrlRedirect' => $sUrlRedirect,
            ));
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }

}
