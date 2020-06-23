<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: api.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Donation_Service_Cache extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_oCache = $this->cache();

        $this->_sPrefix = 'donation' . PHPFOX_DS;

        //key list to handle all key, to know what to clean
        $this->_sKeyList = 'donation.key.list.all';

        if (!is_dir(PHPFOX_DIR . 'file/cache/' . $this->_sPrefix))
        {
            mkdir(PHPFOX_DIR . 'file/cache/' . $this->_sPrefix, 0777);
        }
    }

    /*
     * get cache with key
     */

    public function get($sKey)
    {
        return false;
        $sOriginalKey = $sKey;
        $sKey = $this->buildKey($sKey);
        $this->_oCache->set($sKey);
        $aResult = $this->_oCache->get($sKey);

        //check if cache is expired and delete cache
        if (isset($aResult['expired']))
        {//cached expired is set
            $iExpired = $aResult['expired'];
            if ($iExpired < PHPFOX_TIME)
                $this->remove($sOriginalKey);
        }

        if (isset($aResult['data']))
        {
            return $aResult['data'];
        }
        return false;
    }

    /*
     * set cache
     * cache will be auto deleted if timeout
     * @params: expired ; count by seconds;
     */

    public function set($sKey, $oValue, $iExpired = '')
    {
        return false;
        $sKey = $this->buildKey($sKey);
        $aValue['data'] = $oValue;

        if ($iExpired !== '')
        { 
            //has set expired time
            $aValue['expired'] = (int) PHPFOX_TIME + (int) $iExpired;
        }
        $this->_oCache->set($sKey);
        $this->_oCache->save($sKey, $aValue);

        //if is key list not append
        if ($sKey != $this->buildKey($this->_sKeyList))
            $this->appendKeyList($sKey);
    }

    public function remove($sKey)
    {
        return false;
        $sOriginalKey = $sKey;

        $sKey = $this->buildKey($sKey);

        $this->_oCache->set($sKey);
        $this->_oCache->remove($sKey);
        $this->removeKeyList($sOriginalKey);
    }

    /*
     * remove all cache in list
     * @param $sReg: regular expression pattern not include // Example: /1/
     */

    public function removeAll($sPattern = '')
    {
        return false;
        $aKeyList = $this->get($this->_sKeyList);

        if (is_array($aKeyList) && count($aKeyList) > 0)
        {
            foreach ($aKeyList as $sKey)
            {
                if ($sPattern === '')
                {
                    $sKey = $this->removeKey($sKey);
                    $this->remove($sKey);
                }
                else
                {
                    $bResult = preg_match('/' . $sPattern . '/', $sKey);
                    if ($bResult)
                    {
                        $sKey = $this->removeKey($sKey);
                        $this->remove($sKey);
                    }
                }
            }
        }
    }

    /*
     * append key to key list
     */

    public function appendKeyList($sKey)
    {
        return false;
        $sKeyList = $this->_sKeyList;
        $aResult = $this->get($sKeyList);

        if (count($aResult) > 0)
            $aResult[$sKey] = $sKey;
        else
        {
            $aResult = array();
            $aResult[$sKey] = $sKey;
        }
        $aRet['data'] = $aResult;

        $sKeyList = $this->buildKey($sKeyList);
        $this->_oCache->set($sKeyList);
        $this->_oCache->save($sKeyList, $aRet);
    }

    public function removeKeyList($sKey)
    {
        return false;
        $sKeyList = $this->_sKeyList;
        $sKey = $this->buildKey($sKey);

        $aResult = $this->get($sKeyList);

        if (isset($aResult) && count($aResult) >= 0)
        {
            unset($aResult[$sKey]);
            if (count($aResult) >= 0)
            {
                $this->set($sKeyList, $aResult);
            }
        }
    }

    private function buildKey($sKey)
    {
        return false;
        return $this->_sPrefix . $sKey;
    }

    private function removeKey($sKey)
    {
        return false;
        return str_replace($this->_sPrefix, '', $sKey);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing 
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('suggestion.service_cache__call'))
        {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>