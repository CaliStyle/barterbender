<?php

namespace Apps\YNC_WebPush\Ajax;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Error;

class Ajax extends Phpfox_Ajax
{

    public function handleGrantedToken()
    {
        $sToken = $this->get('token');
        $sBrowser = $this->get('browser');
        if (!$sToken) {
            return false;
        }
        if (Phpfox::getService('yncwebpush.token.process')->addBrowserToken($sToken, $sBrowser)) {

        }
    }

    public function showRequestBanner()
    {
        Phpfox::getBlock('yncwebpush.request-banner',
            ['token' => $this->get('token', null), 'browser' => $this->get('browser', '')]);
        $this->call('$(\'body\').prepend(\'' . $this->getContent() . '\');');
        $this->call('$(\'#js_ync_webpush_request_banner\').fadeIn(500);');
    }

    public function updateSkipTime()
    {
        $iUserId = Phpfox::getUserId();
        $sCookie = Phpfox::getCookie('ync_web_push_' . $iUserId);
        if (!empty($sCookie)) {
            $aCookie = json_decode($sCookie, true);
            $aNewCookie = [
                'skipped' => isset($aCookie['skipped']) ? $aCookie['skipped'] + 1 : 1,
                'last_skip' => PHPFOX_TIME
            ];
            Phpfox::setCookie('ync_web_push_' . $iUserId, json_encode($aNewCookie));
        } else {
            $aNewCookie = [
                'skipped' => 1,
                'last_skip' => PHPFOX_TIME
            ];
            Phpfox::setCookie('ync_web_push_' . $iUserId, json_encode($aNewCookie));
        }
        return true;
    }

    public function loadTemplateDetail()
    {
        $iId = $this->get('id');
        $aTemplate = Phpfox::getService('yncwebpush.template')->getForEdit($iId);
        if (!$aTemplate) {
            return Phpfox_Error::set(_p('this_template_does_not_exist_or_has_been_deleted'));
        }
        $this->template()->assign([
            'aItem' => $aTemplate
        ])->getTemplate('yncwebpush.block.admincp.template-detail');
        $this->call('$(\'#js_ync_webpush_detail_template\').html(\'' . $this->getContent() . '\')');

        return true;
    }

    public function selectTemplate()
    {
        $iId = $this->get('template_id');
        $aTemplate = [];
        if ($iId) {
            $aTemplate = Phpfox::getService('yncwebpush.template')->getForEdit($iId);
        }
        $this->template()->assign([
            'aForms' => $aTemplate
        ])->getTemplate('yncwebpush.block.admincp.add-template-info');
        $this->call('$(\'#js_template_detail\').html(\'' . $this->getContent() . '\'); ');
        $this->call('$Core.loadInit();');
    }

    public function getSubscriberOfNotification()
    {
        $iId = $this->get('id');
        if (!$iId) {
            return false;
        }
        Phpfox::getBlock('yncwebpush.subscribers-notification', ['id' => $iId]);
        return true;
    }

    public function checkTokenExpired()
    {
        $sToken = $this->get('token');
        $sBrowser = $this->get('browser');
        if (!$sToken) {
            return false;
        }
        echo json_encode([
            'token_valid' => Phpfox::getService('yncwebpush.token.process')->cronCheckExpiredToken(null, $sBrowser, false, $sToken)
        ]);
        exit;
    }
}