<?php

require_once "cli.php";

if (defined('DEBUG') && DEBUG) {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$sService = 'twitter';

try {
    $Provider = Phpfox::getService('socialbridge')->getProvider($sService);
    $Provider->removeTokenData();
    $oTwitter = $Provider->getApi();
    $aParams = $_REQUEST;

    if (isset($aParams['bredirect'])) {
        $_SESSION['bredirect'] = $aParams['bredirect'];
    }

    if (isset($aParams['callbackUrl'])) {
        $_SESSION['callbackUrl'] = urldecode($aParams['callbackUrl']);
    }

    $bRedirect = isset($_SESSION['bredirect']) ? $_SESSION['bredirect'] : 1;
    $sRedirectUrl = isset($_SESSION['callbackUrl']) ? $_SESSION['callbackUrl'] : '';
    $sConnected = '';

    if (isset($_GET['denied'])) {
        processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);
    }

    if (isset($aParams['oauth_token']) && $aParams['oauth_token'] && isset($aParams['oauth_verifier']) && $aParams['oauth_verifier']) {
        $oauth_token = $aParams['oauth_token'];
        $oauth_verifier = $aParams['oauth_verifier'];

        $response = $oTwitter->oAuthAccessToken($oauth_token, $oauth_verifier);

        $oTwitter->setOAuthToken($response['oauth_token']);
        $oTwitter->setOAuthTokenSecret($response['oauth_token_secret']);

        $profile = $Provider->getProfile();

        $Provider->setTokenData(array(
            'oauth_token' => $response['oauth_token'],
            'oauth_token_secret' => $response['oauth_token_secret'],
            'user_id' => $response['user_id'],
            'screen_name' => $response['screen_name'],
        ), $profile);

        $sConnected = _p('socialbridge.connected_as',
                array('full_name' => '')) . ' ' . $profile['full_name'];
    } else {
        $callback = Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/php/twitter.php';
        $response = $oTwitter->oAuthRequestToken($callback);
        $oTwitter->oAuthAuthorize($response['oauth_token']);
    }

    processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);
} catch (Exception $e) {
    exit;
}
