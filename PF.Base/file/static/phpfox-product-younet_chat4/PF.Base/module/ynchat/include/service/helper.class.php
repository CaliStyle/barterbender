<?php
defined('PHPFOX') or exit('NO DICE!');
class Ynchat_Service_Helper extends Phpfox_Service
{
    private $_bIsMobile = false;

    private static $_aBrowser = array();

	public function __construct()
	{
    
	}

    /**
     * Show datetime in interface
     */
    public function convertToUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;
        
        $iTime = $iTime + $iTimeZoneOffsetInSecond;
        
        return $iTime;
    }
    
    /**
     * Store datetime in server with GMT0
     */
    public function convertFromUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;
        
        $iTime = $iTime - $iTimeZoneOffsetInSecond;
        
        return $iTime;
    }

	public function convertTime($iTimeStamp, $format = '') {
		if(!$iTimeStamp) {
			return 'none';
		}
		return date($format, $iTimeStamp);
	}

    public function isNumeric($val){
        if(empty($val)){
            return false;
        }

        if (!is_numeric($val))
        {
            return false;
        }       

        return true;
    }

    public function price($sPrice)
    {
        if (empty($sPrice))
        {
            return '0.00';
        }
        
        $sPrice = str_replace(array(' ', ','), '', $sPrice);
        $aParts = explode('.', $sPrice);        
        if (count($aParts) > 2)
        {
            $iCnt = 0;
            $sPrice = '';
            foreach ($aParts as $sPart)
            {
                $iCnt++;
                $sPrice .= (count($aParts) == $iCnt ? '.' : '') . $sPart;
            }
        }       
        
        return $sPrice;
    }

    public function getUserParam($sParam, $iUserId) {

        $iGroupId = $this->getUserBy('user_group_id', $iUserId);

        return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

    }
    
    private function getUserBy($sVar, $iUserId ) {

        $result = $this->_getUserInfo($iUserId);
        if (isset($result[$sVar]))
        {
            return $result[$sVar];
        }

        return false;
    }
    
    private function _getUserInfo($iUserId) {
        $aRow = $this->database()->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id = ' . $iUserId)
            ->execute('getRow');
        if(!$aRow) {
            return false;
        }

        $aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
        $aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities 
        $aRow['language'] = $aRow['language_id']; 
        return $aRow;
        // $this->_aUser = $aRow;
    }

    /**
     * Returns an encrypted & utf8-encoded
     */
    public function encrypt($q, $cryptKey) {
//        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
//        return( $qEncoded );

        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = $cryptKey;
        $secret_iv = $cryptKey . '_iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_encrypt($q, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    /**
     * Returns decrypted original string
     */
    public function decrypt($q, $cryptKey) {
//        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
//        return( $qDecoded );

        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = $cryptKey;
        $secret_iv = $cryptKey . '_iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_decrypt(base64_decode($q), $encrypt_method, $key, 0, $iv);

        return $output;
    }

    public function getBrowser()
    {
        static $sAgent;
        
        if ($sAgent)
        {
            return $sAgent;
        }
        
        $sAgent = $this->getServer('HTTP_USER_AGENT');      
        
        if (preg_match("/Firefox\/(.*)/i", $sAgent, $aMatches) && isset($aMatches[1]))
        {
            $sAgent = 'Firefox ' . $aMatches[1];
        }
        elseif (preg_match("/MSIE (.*);/i", $sAgent, $aMatches))
        {
            if(preg_match("/Phone\s?O?S?\s?(.*)/i", $aMatches[1]))
            {
                $this->_bIsMobile = true;
                $aParts = explode(' ', trim($aMatches[1]));
                $sAgent = 'MSIE Windows Phone ' . $aParts[0];
            }
            else
            {
                $aParts = explode(';', $aMatches[1]);
                $sAgent = 'IE ' . $aParts[0];
                self::$_aBrowser['ie'][substr($aParts[0], 0, 1)] = true;
            }
        }
        elseif (preg_match("/Opera\/(.*)/i", $sAgent, $aMatches))
        {
            if(preg_match("/mini/i", $aMatches[1]))
            {
                $this->_bIsMobile = true;
                $aParts = explode(' ', trim($aMatches[1]));
                $sAgent = 'Opera Mini ' . $aParts[0];
            }
            else
            {
                $aParts = explode(' ', trim($aMatches[1]));
                $sAgent = 'Opera ' . $aParts[0];
            }
        }
        elseif (preg_match('/\s+?chrome\/([0-9.]{1,10})/i', $sAgent, $aMatches))
        {
            if (preg_match('/android/i', $sAgent))
            {
                $this->_bIsMobile = true;
                $sAgent = 'Android';
            }
            else
            {
                $aParts = explode(' ', trim($aMatches[1]));
                $sAgent = 'Chrome ' . $aParts[0];
            }
        }
        elseif (preg_match('/android/i', $sAgent))
        {
            $this->_bIsMobile = true;
            $sAgent = 'Android';            
        }    
        elseif (preg_match('/opera mini/i', $sAgent))
        {
            $this->_bIsMobile = true;
            $sAgent = 'Opera Mini';         
        }   
        elseif (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|fennec|plucker|xiino|blazer|elaine)/i', $sAgent))
        {
            $this->_bIsMobile = true;
            $sAgent = 'Palm';           
        }       
        elseif (preg_match('/blackberry/i', $sAgent))
        {
            $this->_bIsMobile = true;
            $sAgent = 'Blackberry';
        }       
        elseif (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|windows phone)/i', $sAgent))
        {
            $this->_bIsMobile = true;
            $sAgent = 'Windows Smartphone';
        }       
        elseif (preg_match("/Version\/(.*) Safari\/(.*)/i", $sAgent, $aMatches) && isset($aMatches[1]))
        {
            if (preg_match("/iPhone/i", $sAgent) || preg_match("/ipod/i", $sAgent) || preg_match("/iPad/i", $sAgent))
            {
                $aParts = explode(' ', trim($aMatches[1]));
                $sAgent = 'Safari iPhone ' . $aParts[0];    
                $this->_bIsMobile = true;
            }
            else 
            {
                $sAgent = 'Safari ' . $aMatches[1];
            }
        }
        elseif (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $sAgent))
        {
            $this->_bIsMobile = true;
        }
        
        return $sAgent;        
    }    

    public function isMobile(){
        return $this->_bIsMobile;
    }

    public function getServer($sVar)
    {
        switch($sVar)
        {
            case 'SERVER_NAME':
                $sVar = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? 'HTTP_X_FORWARDED_HOST' : $sVar);
                break;
            case 'HTTP_HOST':
                $sVar = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? 'HTTP_X_FORWARDED_HOST' : $sVar);
                break;
            case 'REMOTE_ADDR':
                return $this->getIp();
                break;
            case 'PHPFOX_SERVER_ID':
                if (!Phpfox::getParam(array('balancer', 'enabled')))
                {
                    if (Phpfox::getParam('core.allow_cdn'))
                    {
                        return Phpfox::getLib('cdn')->getServerId();
                    }
                    
                    return 0;
                }
                $aServers = Phpfox::getParam(array('balancer', 'servers'));
                $iServerIp = $this->getServer('SERVER_ADDR');
                if (isset($aServers[$iServerIp]['id']))
                {
                    return $aServers[$iServerIp]['id'];
                }
                return 0;
                break;
        }
        return (isset($_SERVER[$sVar]) ? $_SERVER[$sVar] : '');
    }    

    public function getIp($bReturnNum = false)
    {
        if (PHP_SAPI == 'cli')
        {
            return 0;
        }
        
        $sAltIP = $_SERVER['REMOTE_ADDR'];
 
        if (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $sAltIP = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $aMatches))
        {
            foreach ($aMatches[0] AS $sIP)
            {
                if (!preg_match("#^(10|172\.16|192\.168)\.#", $sIP))
                {
                    $sAltIP = $sIP;
                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_FROM']))
        {
            $sAltIP = $_SERVER['HTTP_FROM'];
        }
        
        if ($bReturnNum === true)
        {
            $sAltIP = str_replace('.', '', $sAltIP);
        }
 
        return $sAltIP;
    }        
}
?>