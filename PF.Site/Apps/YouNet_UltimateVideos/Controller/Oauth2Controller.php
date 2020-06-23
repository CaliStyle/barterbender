<?php


namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox;

require_once dirname(dirname(__FILE__)) . '/Google/autoload.php';


defined('PHPFOX') or exit('NO DICE!');

class Oauth2Controller extends \Phpfox_Component
{
    public function process()
    {

        $video_id = $this->request()->getInt('req3');
        if (!isset($_SESSION['ynultimatevideo_youtube_video'])) {
            $_SESSION['ynultimatevideo_youtube_video'] = $video_id;
        }
        $token = '';
        if (isset($_SESSION['ynultimatevideo_youtube_token']))
            $token = $_SESSION['ynultimatevideo_youtube_token'];
        $OAUTH2_CLIENT_ID = setting('ynuv_youtube_client_id', "");
        $OAUTH2_CLIENT_SECRET = setting('ynuv_youtube_client_secret', "");

        $client = new \Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setAccessType('offline');
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"];
        }
        $redirect = Phpfox::permalink('ultimatevideo.oauth2', null);
        $client->setRedirectUri($redirect);

        if ($token) {
            $client->setAccessToken($token);
            if ($client->isAccessTokenExpired()) {
                unset($_SESSION['ynultimatevideo_youtube_token']);
                $state = mt_rand();
                $client->setState($state);
                $_SESSION['state'] = $state;
                $authUrl = $client->createAuthUrl();
                $this->url()->send($authUrl);
            }
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $token = $client->getAccessToken();
        }

        if (!$token) {
            $state = mt_rand();
            $client->setState($state);
            $_SESSION['state'] = $state;
            $authUrl = $client->createAuthUrl();
            $this->url()->send($authUrl);
        } else {
            $_SESSION['ynultimatevideo_youtube_token'] = $token;
            if (isset($_SESSION['ynultimatevideo_youtube_video']) && !$video_id) {
                $video_id = $_SESSION['ynultimatevideo_youtube_video'];
            }
            unset($_SESSION['ynultimatevideo_youtube_video']);
            //Save token
            Phpfox::getLib('database')->update(Phpfox::getT('ynultimatevideo_videos'), ['user_token' => $token], 'video_id =' . $video_id);
            // redirect to manage page
            $this->url()->send('ultimatevideo', ['view' => 'my']);
        }

        return 'true';
    }
}