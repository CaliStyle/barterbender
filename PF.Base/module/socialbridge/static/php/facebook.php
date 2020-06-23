<?php

require_once "cli.php";

$sService = 'facebook';

$fbScope = 'email';

try {
    $sService = 'facebook';

    $Provider = Phpfox::getService('socialbridge')->getProvider($sService);

    // $oApi -- FacebookSBYN Object
    $oApi = $Provider->getApi();

    $aParams = $_REQUEST;

    $aParams['service'] = $sService;

    if (isset($aParams['bredirect'])) {
        $_SESSION['bredirect'] = $aParams['bredirect'];
    }

    if (isset($aParams['callbackUrl'])) {
        $_SESSION['callbackUrl'] = urldecode($aParams['callbackUrl']);
    }

    $bRedirect = isset($_SESSION['bredirect']) ? $_SESSION['bredirect'] : 1;
    $sRedirectUrl = isset($_SESSION['callbackUrl']) ? $_SESSION['callbackUrl'] : '';
    $sConnected = '';

    if (isset($_REQUEST['code'])) {
        // 	execute when FB return access token
        $oApi->setAccessToken(null);
        $oApi->destroySession();
        $access_token = $oApi->getUserAccessToken();

        if ($oApi->setExtendedAccessToken()) {
            $access_token = $oApi->getPersistenData('access_token');
        }

        if (!$access_token) {
            $aParams = array(
                'scope' => $fbScope,
            );
            $url = $oApi->getLoginUrl($aParams);
            if ($oApi->getRedirectCounter() >= 2) {
                processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected, true);
            } else {
                Phpfox::getLib('url')->send($url);
            }

        }

        $oApi->setAccessToken($access_token);
        $aProfile = $Provider->getProfile();
        // 	insert into table "phpfox_socialbridge_token"
        $Provider->setTokenData($access_token, $aProfile);

        $sConnected = _p('socialbridge.connected_as',
                array('full_name' => '')) . ' ' . $aProfile['full_name'];

        processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);

    } else {
        $aParams = array(
            'scope' => $fbScope,
        );
        // popup displayed, relative library will get 'current path' which is redirect_uri
        $url = $oApi->getLoginUrl($aParams);
        if ($oApi->getRedirectCounter() >= 2) {
            processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected, true);
        } else {
            if (isset($_REQUEST['error'])
                && isset($_REQUEST['error_code'])
                && $_REQUEST['error'] == 'access_denied'
                && $_REQUEST['error_code'] == '200'
            ) {
                processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);
            } else {
                Phpfox::getLib('url')->send($url);
            }

        }
        exit;
    }


} catch (Exception $e) {
    /**
     * @TODO: some time facebook does not accept our access token, it's strange issue
     */
    if ($e->getMessage() == "Invalid OAuth access token.") {
        $aParams = array(
            'scope' => $fbScope,
        );
        $url = $oApi->getLoginUrl($aParams);
        if ($oApi->getRedirectCounter() >= 2) {
            processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected, true);
        } else {
            Phpfox::getLib('url')->send($url);
        }
        exit;
    } else {
        echo $e->getMessage();
        echo _p('socialbridge.please_enter_your_api');
        exit;
    }
}
