<?php

require_once "cli.php";
if(!class_exists('YNCFacebook'))
{
	require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' .PHPFOX_DS. 'Apiconnection' . PHPFOX_DS . 'facebook.php');
}
session_start();
if (isset($_SESSION['contactimporter']['facebook']))
{
    unset($_SESSION['contactimporter']['facebook']);
}
$sUrl = phpfox::getLib('url')->makeUrl('contactimporter.facebook');
$aFbSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('facebook');
if ($aFbSetting == null)
{
    echo _p('please_enter_your_facebook_api');
    exit;
}
$aFbSetting['api_params'] = unserialize($aFbSetting['api_params']);
$oFacebook = new YNCFacebook(array(
            'appId' => $aFbSetting['api_params']['appid'],
            'secret' => $aFbSetting['api_params']['secret'],
            'cookie' => true,
        ));
$session = $oFacebook->getAccessToken();
$oFacebook->setAccessToken($session);
$_SESSION['contactimporter']['yn_contactimporter_facebook_session_' . Phpfox::getUserId()] = $session;
/*
$oCache = Phpfox::getLib('cache');
$sCacheId = $oCache->set('yn_contactimporter_facebook_session_' . Phpfox::getUserId());
$oCache->save($sCacheId, $session);
 */
echo "<script>opener.parent.location.href = '" . $sUrl . "';</script>";
echo "<script>self.close();</script>";
?>