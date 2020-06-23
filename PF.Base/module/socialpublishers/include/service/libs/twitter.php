<?php
defined('PHPFOX') or exit('NO DICE!');

require_once(PHPFOX_DIR_LIB . 'twitter' . PHPFOX_DS . 'EpiCurl.php');
require_once(PHPFOX_DIR_LIB . 'twitter' . PHPFOX_DS . 'EpiOAuth.php');
require_once(PHPFOX_DIR_LIB . 'twitter' . PHPFOX_DS . 'EpiTwitter.php');

class YNSPEpiTwitter extends EpiTwitter
{

    protected $requestTokenUrl = 'http://api.twitter.com/oauth/request_token/1';
    protected $accessTokenUrl = 'http://api.twitter.com/oauth/access_token/1';
    protected $authorizeUrl = 'http://api.twitter.com/oauth/authorize/1';
    protected $apiUrl = 'http://api.twitter.com/1';

}
class TwitterSPLN
{
	private $_oTwitter = null;
	private $_sToken = null;
	private $_sSecret = null;
	
	public function __construct($aConfig)
	{
		$this->_oTwitter = new YNSPEpiTwitter($aConfig['consumer_key'],$aConfig['consumer_secret']);
	}	
	public function post($sMessage,$aTwitter)
	{
		if (!empty($aTwitter['token']))
		{
            $this->_oTwitter->setToken($aTwitter['token'], $aTwitter['secret']);
			$update_status = $this->_oTwitter->post_statusesUpdate(array('status'  => $sMessage));	
			$temp = $update_status->response;	
            return $temp;		
		}		
	}
    public function getAccessToken()
    {
        $this->_oTwitter->getAccessToken();
    }
	public function getToken()
	{
		return $this->_sToken;
	}
	
	public function getSecret()
	{
		return $this->_sSecret;
	}
	
	public function getUser($sToken)
	{
		$this->_oTwitter->setToken($sToken);
		$token = $this->_oTwitter->getAccessToken();
		$this->_oTwitter->setToken($token->oauth_token, $token->oauth_token_secret);		
		
		$mReturn = $this->_oTwitter->get_accountVerify_credentials();
		
		$this->_sToken = $token->oauth_token;
		$this->_sSecret = $token->oauth_token_secret;
		
		return (array) $mReturn->response;
	}
	
	public function getUrl()
	{
		Phpfox_Error::skip(true);
		$mReturn = $this->_oTwitter->getAuthorizationUrl();
		Phpfox_Error::skip(false);
		return $mReturn;
	}
}

?>