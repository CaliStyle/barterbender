<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class OpenSocialConnect_Component_Controller_Syncuser extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $oService = Phpfox::getService('opensocialconnect');
        if ($aVals = $this->request()->get('val')) {
            if ($this->request()->get('synchronize') == _p('opensocialconnect.synchronize') || $this->request()->get('g-recaptcha-response') != '') {
                $sService = $aVals['service'];

                if (!isset($_SESSION['quick_signup'][$sService])) {
                    $this->url()->send('');
                } else {
                    $aData = $oService->getSignupSessionData($sService);
                }

                $aService = Phpfox::getService('opensocialconnect.providers')->getProvider($sService);

                $aVals['service_id'] = $aService['service_id'];

                $iId = $aVals['user_id'];

                Phpfox::getService('opensocialconnect')->addAgent($iId, $aVals);
                Phpfox::getService('opensocialconnect.providers')->updateStatistics($sService, 'sync');

                $aExistUser = $oService->getUserByIdentityAndService($aVals['identity'], $sService,
                    $aVals['service_id']);

                $bLogin = false;

                if (isset($aExistUser['email']) && $aExistUser['email'] != "") {
                    list($bLogin, $aUser) = $oService->loginByEmail($aExistUser['email']);

                    if (Phpfox::isModule('socialbridge')) {
                        if (($sService == 'facebook')) {
                            $aToken = Phpfox::getService('socialbridge.libs')->getFBAccessToken();
                            $aExtra = $_SESSION['quick_signup'][$sService]['user'];
                            Phpfox::getService('socialbridge.agents')->addToken($iId, $sService, $aToken, $aExtra);
                            Phpfox::getService('opensocialconnect')->updateBridgeToken($iId, $sService);
                        }

                        if ($sService == 'linkedin' || $sService == 'twitter') {
                            list($aToken, $aExtra) = Phpfox::getService('socialbridge.provider.twitter')->getTokenData();
                            Phpfox::getService('socialbridge.agents')->addToken($iId, $sService, $aToken, $aExtra);
                            Phpfox::getService('opensocialconnect')->updateBridgeToken($iId, $sService);
                        }
                    }
                }

                Phpfox::getService('opensocialconnect')->clearSignupSessionData();

                if ($bLogin) {
                    if (is_array($iId)) {
                        (($sPlugin = Phpfox_Plugin::get('user.component_controller_register_3')) ? eval($sPlugin) : false);
                        $this->url()->forward($iId[0]);
                    } else {
                        $sRedirect = Phpfox::getParam('user.redirect_after_signup');
                        if (!empty($sRedirect)) {
                            (($sPlugin = Phpfox_Plugin::get('user.component_controller_register_4')) ? eval($sPlugin) : false);
                            $this->url()->send($sRedirect);
                        }
                        if (Phpfox::getParam('user.multi_step_registration_form') && is_array(Phpfox::getParam('user.registration_steps')) && count(Phpfox::getParam('user.registration_steps'))) {
                            $aUrls = Phpfox::getParam('user.registration_steps');

                            (($sPlugin = Phpfox_Plugin::get('user.component_controller_register_5')) ? eval($sPlugin) : false);
                            $this->url()->send($aUrls[0], 'register');
                        } else {
                            (($sPlugin = Phpfox_Plugin::get('user.component_controller_register_6')) ? eval($sPlugin) : false);
                            $this->url()->send('');
                        }
                    }
                }
            }
            if ($this->request()->get('cancel') == _p('core.no')) {
                $sUrlRedirect = $this->url()->makeUrl('opensocialconnect.quicksignup',
                    array('service' => $aVals['service'], 'syns' => 'no'));
                $this->url()->send($sUrlRedirect);
            }
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

?>