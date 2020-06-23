<?php
session_start();
require_once "cli.php";
require_once 'twitter.php' ;

if (isset($_SESSION['contactimporter']['twitter']))
{
    unset($_SESSION['contactimporter']['twitter']);
}

$aParams = $_REQUEST;
$aParams['service'] = 'twitter';
$sUrl = phpfox::getLib('url')->makeUrl('contactimporter.twitter');    
$aSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('twitter'); 
if ($aSetting == null)
{
    echo _p('please_enter_your_twitter_api');
    exit;
}
$aSetting = Phpfox::getService('contactimporter.setting')->getApiSetting('twitter'); 
$aSetting['api_params'] = unserialize($aSetting['api_params']);
$aConfig = array('consumer_key' => $aSetting['api_params']['appid'], 'consumer_secret' => $aSetting['api_params']['secret']);
$oTwitter = new Twitter($aSetting['api_params']['appid'], $aSetting['api_params']['secret']);
if (isset($aParams['oauth_token']) && $aParams['oauth_token'] && isset($aParams['oauth_verifier'])&& $aParams['oauth_verifier'] )
{	
    $_SESSION['twitter']['oauth_token'] = $oauth_token = $aParams['oauth_token'];
    $_SESSION['twitter']['oauth_verifier'] = $oauth_verifier = $aParams['oauth_verifier'];
    $response = $oTwitter->oAuthAccessToken($oauth_token, $oauth_verifier);   
    $_SESSION['twitter']['oauth_token'] = $response['oauth_token']; 
    $_SESSION['twitter']['oauth_token_secret'] = $response['oauth_token_secret']; 
    $_SESSION['twitter']['user_id'] = $user_id = $response['user_id'];
    $_SESSION['twitter']['screen_name'] = $user_id = $response['screen_name'];
    if ($user_id)
    {
        $aUser = $oTwitter->accountVerifyCredentials();
        $_SESSION['twitter']['followers_count'] = isset($aUser['followers_count']) ? $aUser['followers_count'] : 0;
    }    
}
else
{
    $oTwitter->oAuthRequestToken(phpfox::getParam('core.path') . 'module/contactimporter/static/tw.php');
    $oTwitter->oAuthAuthorize();    
}
echo "<script>opener.parent.location.href = '".$sUrl."';</script>" ;
echo "<script>self.close();</script>" ;
?>