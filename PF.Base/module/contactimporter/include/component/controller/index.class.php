<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_Index extends Phpfox_Component
{

    public function process()
    {
        if (!Phpfox::isModule('socialbridge')) {
            return Phpfox_Error::display('missing Social Bridge Module', E_USER_ERROR);
        }
        $request = $this->request();
        Phpfox::isUser(true);

        if ($request->get('success') || $request->get('fail')) {
            $iSuccess = $request->get('success');
            $iFail = $request->get('fail');
            $sResultMessage = _p('you_have_successfully_sent_sucess_invitations_and_fail_invitations_have_failed', array('fail' => $iFail, 'success' => $iSuccess));
            Phpfox::addMessage($sResultMessage);
        }

        $require_invite = $request->get('req2') == 'require-invite';
        $signup_success = $request->get('req2') == 'signup-success';

        if (isset($_POST['denied']) && $_POST['denied'] == 1) {
            Phpfox::getLib('url')->send('contactimporter');
            exit;
        }

        $step = 'get_contact';

        /**
         * assign javascript language phrases
         */
        $this->template()->setPhrase(array(
            'contactimporter.you_can_send',
            'contactimporter.are_you_sure_you_want_to_delete',
            'contactimporter.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds_belong_to',
            'contactimporter.invitations_per_time',
            'contactimporter.you_have_selected',
            'contactimporter.contacts',
            'contactimporter.select_current_page',
            'contactimporter.unselect_current_page',
            'contactimporter.your_email_is_empty',
            'contactimporter.this_mail_domain_is_not_supported',
            'contactimporter.email_should_not_be_left_blank',
            'contactimporter.no_contacts_were_selected',
            'contactimporter.updating',
            'contactimporter.typing_manual',
        ));
        Phpfox::getService('contactimporter')->buildSectionMenu();
        /**
         * @commented by namnv
         * I don't have ideal with following line, it looks stupid but it works.
         */

        $iUserId = Phpfox::getUserId();
        $oService = Phpfox::getService('contactimporter');
        $iLimit = $iMaxInvitation = $oService->getMaxInvitation();
        $sProvider = $this->request()->get('req2');
        if (!$sProvider && isset($_POST['provider_box'])) {
            $sProvider = $_POST['provider_box'];
        }

        $iRemain = 0;
        $iQuota = 10;
        if ($sProvider == 'twitter' || $sProvider == 'linkedin') {
            $iRemain = Phpfox::getService('contactimporter')->getNumberOfRemainingInvitationInPeriod($sProvider);
            if ($iRemain < 0) {
                $iRemain = 0;
            }

            if ($iMaxInvitation > $iRemain) {
                $iMaxInvitation = $iRemain;
            }
            $aQuota = Phpfox::getService('contactimporter')->getQuota($sProvider);
            $iQuota = $aQuota['number'];
        }

        $sProviderName = ucfirst($sProvider);
        $iProviderQuota = $iRemain;

        $this->template()->assign(array(
            'require_invite' => $require_invite,
            'signup_success' => $signup_success,
            'sProviderName' => $sProviderName,
            'iProviderQuota' => $iProviderQuota,
            'iQuota' => $iQuota,
            'max_invitation' => $iMaxInvitation,
            'yn_max_invitation' => $iMaxInvitation,
            'sSecurityToken' => Phpfox::getService('log.session')->getToken(),
            'contactimporter_link' => phpfox::getLib('url')->makeUrl('contactimporter'),
            'homepage' => phpfox::getParam('core.path'),
            'errors' => null,
            'aUsers' => NULL,
            'invite_list_sorts' => NULL,
            'core_url' => Phpfox::getParam('core.path'),
            'sIniviteLink' => Phpfox::getLib('url')->makeUrl('contactimporter.inviteuser', array('id' => $iUserId)),
            'provider_lists' => Phpfox::getService('contactimporter')->getAllowProviders(),
            'facebookInviteLink' => Phpfox::getLib('url')->makeUrl('contactimporter.inviteuser', array('id' => $iUserId)),
        ));

        $oProvider = NULL;

        /**
         * get provider object
         */
        if ($sProvider) {
            $oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
        }

        if (is_object($oProvider)) {
            $step = 'get_invite';
            /**
             * get list of service
             */
            $iPage = $request->get('page', 1);
            try {
                $aContactsResults = $oProvider->getContacts($iPage, $iLimit);
            } catch (Exception $ex) {
                $this->url()->send('contactimporter', null, $ex->getMessage());
                exit(0);
            }

            $aErrors = isset($aContactsResults['aErrors']) ? $aContactsResults['aErrors'] : array();
            $aCoreRequest = $request->get('core', array());
            $iCnt = isset($aContactsResults['iCnt']) ? $aContactsResults['iCnt'] : 0;

            if (isset($aErrors['login']) && $aErrors['login']) {
                if (!$aCoreRequest || !isset($aCoreRequest['ajax']))
                    $this->url()->send('contactimporter', null, $aErrors['login']);
                exit(0);
            }
            if (isset($aErrors['contacts']) && $aErrors['contacts']) {
                if (!$aCoreRequest || !isset($aCoreRequest['ajax']))
                    $this->url()->send('contactimporter', null, $aErrors['contacts']);
                exit(0);
            }

            if (isset($aContactsResults['aInviteLists']) && !$aContactsResults['aInviteLists']) {
                if (!$aCoreRequest || !isset($aCoreRequest['ajax']))
                    $this->url()->send('contactimporter', null, _p('you_have_sent_the_invitations_to_all_of_your_friends'));
                exit;
            }

            $this->template()->assign(array(
                'step' => 'get_invite',
                'iLimit' => $iLimit,
                'iPage' => $iPage,
                'sProvider' => $sProvider,
                'errors' => $aErrors,
                'sCoreUrl' => Phpfox::getParam('core.path'),
                'sIniviteLink' => Phpfox::getLib('url')->makeUrl('contactimporter.inviteuser', array('id' => Phpfox::getUserId())),
                'plugType' => $oService->getPluginType($sProvider),
            ))->assign($aContactsResults);

            $aSocialProviders = array('facebook', 'twitter', 'linkedin');

            if (in_array($sProvider, $aSocialProviders)) {
                Phpfox::getLib('pager')->set(array(
                    'page' => $iPage,
                    'size' => $iLimit,
                    'count' => $iCnt,
                    'popup' => true,
                ));
            }


        }
        $this->template()->setTitle(_p('contact_importer'));
        $this->template()->setBreadcrumb(_p('contact_importer'));
        $this->template()->setHeader(array(
            'rtl.css' => 'module_contactimporter',
            'jquery.autocomplete.js' => 'module_contactimporter',
            'jquery.autocomplete.css' => 'module_contactimporter',
            'jquery.scrollTo-min.js' => 'module_contactimporter',
            'contactimporter.js' => 'module_contactimporter',
            'jquery.localscroll-min.js' => 'module_contactimporter',
            'init.js' => 'module_contactimporter',
            'slide.js' => 'module_contactimporter',
        ));

        $settings = phpfox::getService('socialbridge.providers')->getProvider('facebook');

        $this->template()->assign(array(
            'user_id' => Phpfox::getUserId(),
            'fbAIP' => !empty($settings['params']['app_id']) ? $settings['params']['app_id'] : "",
            'step' => $step
        ));

        $this->template()->setHeader(array('pager.css' => 'style_css'));
        if (Phpfox::getLib('request')->get('task') == 'skip') {
            return;
        }
    }

}

?>