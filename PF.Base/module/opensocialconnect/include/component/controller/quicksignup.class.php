<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


class OpenSocialConnect_Component_Controller_QuickSignUp extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (Phpfox::isUser()) {
            Phpfox::getLib('url')->send('');
        }

        $sService = $this->request()->get('service');
        $oService = Phpfox::getService('opensocialconnect');
        $aService = Phpfox::getService('opensocialconnect.providers')->getProvider($sService);

        $aValidation['email'] = array(
            'def' => 'email',
            'title' => _p('user.provide_a_valid_email_address')
        );
        $aValidation['user_name'] = array(
            'def' => 'username',
            'title' => _p('user.provide_a_valid_user_name', array(
                'min' => Phpfox::getParam('user.min_length_for_username'),
                'max' => Phpfox::getParam('user.max_length_for_username')
            ))
        );
        $aValidation['full_name'] = _p('user.provide_your_full_name');

        if (Phpfox::getParam('user.new_user_terms_confirmation')) {
            $aValidation['agree'] = array(
                'def' => 'checkbox',
                'title' => _p('user.check_our_agreement_in_order_to_join_our_site')
            );
        }

        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

        if ($aVals = $this->request()->getArray('val')) {
            if (Phpfox::VERSION >= '3.7.6') {
                $aVals['user_name'] = str_replace(' ', '-', $aVals['user_name']);
                $aVals['user_name'] = str_replace('_', '-', $aVals['user_name']);
            } else {
                $aVals['user_name'] = str_replace(' ', '_', $aVals['user_name']);
            }

            Phpfox::getService('user.validate')->user($aVals['user_name']);
            Phpfox::getService('user.validate')->email($aVals['email']);
            if (isset($aVals['autopassword'])) {
                $aVals['password'] = substr(time(), 0, 6);
            } else {
                if (strlen($aVals['password']) < 5) {
                    Phpfox_Error::set(_p('user.provide_a_valid_password'));
                }
            }

            $sService = $aVals['service'];

            if ($oValid->isValid($aVals) && Phpfox_Error::isPassed()) {
                if ($iId = Phpfox::getService('opensocialconnect')->addUser($aVals)) {
                    $aVals['service_id'] = $aService['service_id'];
                    $aService = Phpfox::getService('opensocialconnect.providers')->getProvider($sService);
                    Phpfox::getService('opensocialconnect')->addAgent($iId, $aVals);
                    Phpfox::getService('opensocialconnect.providers')->updateStatistics($sService, 'signup');

                    if (Phpfox::getService('user.auth')->login($aVals['email'], $aVals['password'])) {


                        if (Phpfox::getParam("user.approve_users") == true) {
                            Phpfox::getService("user.auth")->logout();
                            Phpfox::getLib('url')->send('user.pending',['s' => 0, 'v' => 1]);
                        }
                        //login success
                        if (Phpfox::isModule('socialbridge')) {
                            if (($sService == 'facebook')) {
                                $aToken = Phpfox::getService('socialbridge.libs')->getFBAccessToken();
                                $aExtra = $_SESSION['quick_signup'][$sService]['user'];
                                Phpfox::getService('socialbridge.agents')->addToken($iId, $sService, $aToken, $aExtra);
                                Phpfox::getService('opensocialconnect')->updateBridgeToken($iId, $sService);
                            }
                            if ($sService == 'linkedin' || $sService == 'twitter') {
                                list($aToken, $aExtra) = Phpfox::getService('socialbridge.provider.' . $sService)->getTokenData(Phpfox::getUserId());
                                Phpfox::getService('socialbridge.agents')->addToken($iId, $sService, $aToken, $aExtra);
                                Phpfox::getService('opensocialconnect')->updateBridgeToken($iId, $sService);
                            }
                        }

                        Phpfox::getService('opensocialconnect')->clearSignupSessionData();
                        //end
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
                                $sRedirect = Phpfox::getService('opensocialconnect')->getRedirect($sService);
                                $this->url()->send($sRedirect);
                            }
                        }
                    }
                }
            }
        }

        $sTitle = _p('user.sign_and_start_using_site',
            array('site' => Phpfox::getParam('core.site_title')));

        if (!isset($_SESSION['quick_signup'][$sService])) {
            $this->url()->send('');
        } else {
            $aData = $_SESSION['quick_signup'][$sService];
        }

        $aForm = array(
            'full_name' => isset($aData['user']['full_name']) ? $aData['user']['full_name'] : "",
            'email' => isset($aData['user']['email']) ? $aData['user']['email'] : "",
            'user_name' => isset($aData['user']['user_name']) ? $aData['user']['user_name'] : ""
        );
        $aForm['large_img_url'] = isset($aData['user']['img_url']) ? $aData['user']['img_url'] : '';
        if ($sService != 'twitter' && !empty($aForm['large_img_url'])) {
            $aForm['large_img_url'] = $aForm['large_img_url'] . '?type=large';
        }

        if (isset($aData['user']['email']) && !empty($aData['user']['email']) && $this->request()->get('syns') != "no") {
            $aUser = $oService->getUserByEmail($aData['user']['email']);

            if ($aUser) {
                $this->template()->assign(array(
                    'step' => 'syncuser',
                    'aData' => $aData,
                    'iSyncUserId' => $aUser['user_id'],
                    'sEmail' => $aData['user']['email'],
                ));
            }
        }

        $this->template()->setTitle($sTitle)
            ->setPhrase(array('user.continue'))
            ->setHeader('cache', array('register.css' => 'module_user',))
            ->assign(array(
                'sSiteTitle' => Phpfox::getParam('core.site_title'),
                'aService' => $aService,
                'aForms' => $aForm,
                'aData' => $aData,
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
            ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_controller_quicksignup_clean')) ? eval($sPlugin) : false);
    }

}

?>