<?php

require_once "cli.php";

if (!function_exists('curPageURL')) {
    function curPageURL()
    {
	    $scheme = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https' : 'http';
	    $port = $_SERVER["SERVER_PORT"];
	    $host = $_SERVER["SERVER_NAME"];
	    $path = $_SERVER["REQUEST_URI"];

	    if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80'))
	    {
		    $host = "$host:$port";
	    }
	    return "$scheme://$host$path";
    }

}

$sService = 'linkedin';


try {
    if (isset($aParams['bredirect'])) {
        $_SESSION['bredirect'] = $aParams['bredirect'];
    }

    if (isset($_GET['callbackUrl'])) {
        $_SESSION['callbackUrl'] = urldecode($_GET['callbackUrl']);
    }


    $bRedirect = isset($_SESSION['bredirect']) ? $_SESSION['bredirect'] : 1;
    $sRedirectUrl = isset($_SESSION['callbackUrl']) ? $_SESSION['callbackUrl'] : '';
    $sConnected = '';

    $Provider = Phpfox::getService('socialbridge')->getProvider($sService);

    $Provider->removeTokenData();
    $oLinkedIn = $Provider->getApi();

    // check for response from LinkedIn
    $lResponse = isset($_GET['lResponse']) ? $_GET['lResponse'] : '';


    if (isset($_GET['error'])) {
        // get error, display error
        echo "<h1>ERROR</h1> <p>{$_GET['error_description']}</p>";
    } elseif ($lResponse == '') {
        $oLinkedIn->setAccessToken(null);
        // LinkedIn hasn't sent us a response, the user is initiating the
        // connection

        if (isset($_GET['code'])) {
            $access_token = $oLinkedIn->fetchAccessToken($_GET['code'], $_SESSION['linkedin']['linkedin_redirect_uri']);
            unset($_SESSION['linkedin']['linkedin_redirect_uri']);

            $_SESSION['linkedin']['access_token'] = $access_token;

            // add secret_token to support all pludins are using secret_token to check authetication (In oauth2.0 not use secrect token).
            $_SESSION['linkedin']['secret_token'] = $_SESSION['linkedin']['access_token'];
            $oLinkedIn->setAccessToken($access_token['access_token']);
            $profile = $Provider->getProfile();
            $Provider->setTokenData($access_token['access_token'], $profile);

            $sConnected = _p('socialbridge.connected_as',
                    array('full_name' => '')) . ' ' . $profile['full_name'];
            processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);
            exit;
        } elseif ((!isset($_SESSION['linkedin']) || (isset($_SESSION['linkedin']) && !isset($_SESSION['linkedin']['access_token'])))) {
            $redirect_uri = curPageURL();
            $_SESSION['linkedin']['linkedin_redirect_uri'] = $redirect_uri;
            $scope = 'r_liteprofile,r_emailaddress,w_member_social';
            if (!empty($_GET['scope'])) {
                $scope = $_GET['scope'];
            }
            $url = $oLinkedIn->getAuthorizationUrl($redirect_uri, 'NOSTATE', $scope);
            header('location:' . $url);
        } elseif ((isset($_SESSION['linkedin']) && (isset($_SESSION['linkedin']['access_token'])))) {
            $access_token = $_SESSION['linkedin']['access_token'];
            $oLinkedIn->setAccessToken($access_token['access_token']);
            $profile = $Provider->getProfile();
            $Provider->setTokenData($access_token['access_token'], $profile);

            $sConnected = _p('socialbridge.connected_as',
                    array('full_name' => '')) . ' ' . $profile['full_name'];
            processRedirectAndExit($sService, $bRedirect, $sRedirectUrl, $sConnected);
            exit;
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
    echo _p('socialbridge.please_enter_your_linkedin_api');
    exit;
}