<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialBridge_Service_Provider_Abstract extends Phpfox_Service
{
    /**
     * @var object
     */
    protected $_api = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var array
     */
    protected $_profile = null;

    /**
     * register session token
     *
     * @param $token
     * @param $profile
     * @param null $iUserId
     * @return $this
     */
    public function setTokenData($token, $profile, $iUserId = null)
    {
        Phpfox::getService('socialbridge')->setTokenData($this->_name, $token, $profile, $iUserId);

        return $this;
    }

    /**
     * retried session token
     * @param null $iUserId
     * @return null|array
     */
    public function getTokenData($iUserId = null)
    {
        return Phpfox::getService('socialbridge')->getTokenData($this->_name, $iUserId);
    }

    /**
     * clear session token
     * @param null $iUserId
     * @return SocialBridge_Service_Provider_Abstract
     */
    public function removeTokenData($iUserId = null)
    {
        Phpfox::getService('socialbridge')->removeTokenData($this->_name, $iUserId);

        return $this;
    }

    /**
     * get auth url from callback
     * @param string $callbackUrl
     * @param int $bRedirect
     * @return string
     */
    public function getAuthUrl($callbackUrl = '', $bRedirect = 1)
    {
        return Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/php/' . $this->_name . '.php?' . http_build_query(array(
                'callbackUrl' => urlencode($callbackUrl),
                'bredirect' => $bRedirect
            ), null, '&');
    }

    /**
     * set api object
     * @param object $api
     * @return SocialBridge_Service_Provider_Abstract
     */
    public function setApi($api)
    {
        $this->_api = $api;

        return $this;
    }

    /**
     * create api object
     */
    function getApi()
    {
        throw new Exception("This method must be override");
    }

    /**
     * reset current api
     * @return void
     */
    function resetApi()
    {
        $this->_api = null;
    }

    /**
     * get services setting defined by setting object
     * @return array
     */
    function getSetting()
    {
        return Phpfox::getService('socialbridge')->getSetting($this->_name);
    }

    public function getShortBitlyUrl($sLongUrl)
    {
        try {
            $sLongUrl = urlencode($sLongUrl);
            $url = "http://api.bitly.com/v3/shorten?login=myshortlinkng&apiKey=R_0201be3efbcc7a1a0a0d1816802081d8&longUrl={$sLongUrl}&format=json";
            $result = @file_get_contents($url);
            $obj = json_decode($result, true);

            return ($obj['status_code'] == '200' ? $obj['data']['url'] : "");
        } catch (Exception $e) {
            return $sLongUrl;
        }
    }

    public function generateResult($message, $bResult)
    {
        return array(
            'result' => $bResult,
            'message' => $message
        );
    }
}
