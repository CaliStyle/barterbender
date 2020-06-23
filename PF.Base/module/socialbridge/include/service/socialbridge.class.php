<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Socialbridge_Service_Socialbridge extends Phpfox_Service
{
    protected $_aSupporteds = array();

    /**
     * @param array
     */
    protected $_aViewerTokenData = null;

    /**
     * @param array
     */
    protected $_aSettings = array();

    /**
     * @param bool
     */
    protected $_bInitSetting = false;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_sTable = phpfox::getT('socialbridge_services');
        $this->initSetting();
    }

    public function getAuthUrl($sService, $callbackUrl, $bRedirect = 1)
    {
        return $this->getProvider($sService)->getAuthUrl($callbackUrl, $bRedirect);
    }

    /**
     * init setting once only.
     * @TODO should apply cache to improve performance.
     * @return void
     */
    protected function initSetting()
    {
        if ($this->_bInitSetting) {
            return;
        }

        $aRows = $this->database()->select('*')->from($this->_sTable)->execute('getRows');

        foreach ($aRows as $aRow) {
            if ($aRow['params']) {
                $this->_aSettings[$aRow['name']] = (array)@unserialize($aRow['params']);
                $this->_aSupporteds[$aRow['name']] = $aRow['is_active'];
            } else {
                $this->_aSupporteds[$aRow['name']] = false;
            }
        }

        $this->_bInitSetting = true;
    }

    /**
     * set token for particular service
     * @param string $sService service name
     * @param $aToken
     * @param $sProfile
     * @param int $iUserId
     * @return bool
     */
    public function setTokenData($sService, $aToken, $sProfile, $iUserId = null)
    {
        // add identity when we want to map multiple social account with one phpfox-user.
        $found = true;
        $aRow = null;
        $iViewerId = Phpfox::getUserId();

        if (null == $iUserId) {
            $iUserId = $iViewerId;
        }

        if ($iUserId == $iViewerId) {
            $this->_aViewerTokenData[$sService] = array(
                $aToken,
                $sProfile
            );
        }

        $sSessionId = @session_id();

        $sToken = serialize($aToken);
        $aProfile = $sProfile;
        $sProfile = serialize($sProfile);

        $sTable = Phpfox::getT('socialbridge_token');

        $sWhere = "service='{$sService}'";

        if ($iUserId) {
            $sWhere .= " AND (user_id='{$iUserId}' AND session_id='{$sSessionId}')";
        } else {
            if ($sSessionId) {
                $sWhere .= " AND session_id='{$sSessionId}'";
            } else {
                $found = false;
            }
        }

        if ($found) {
            $this->database()->delete($sTable, $sWhere);
        }

        $identity = '';
        if (isset($aProfile['identity'])) {
            $identity = $aProfile['identity'];
        }

        $this->database()->insert($sTable, array(
            'user_id' => $iUserId,
            'session_id' => $sSessionId,
            'service' => $sService,
            'identity' => $identity,
            'token' => $sToken,
            'profile' => $sProfile,
            'timestamp' => time(),
        ));

        return true;
    }

    /**
     * general used
     * @param int $iUserId
     * @param string $sService
     * @return array
     */
    protected function getUserTokenData($sService, $iUserId)
    {
        $sWhere = 'service = "' . $sService . '" AND user_id = ' . $iUserId;
        $aRow = $this->database()->select('*')->from(Phpfox::getT('socialbridge_token'))->where($sWhere)->execute('getSlaveRow');

        if ($aRow) {
            return array(
                @unserialize($aRow['token']),
                @unserialize($aRow['profile'])
            );
        }

        return array(
            null,
            null
        );
    }

    /**
     * remove user token data
     * @param null $sService
     * @param int $iUserId
     * @return TRUE
     */
    protected function removeUserTokenData($sService = null, $iUserId = null)
    {
        if ($sService) {
            $sWhere = "service = '{$sService}' AND user_id='{$iUserId}'";
        } else {
            $sWhere = "user_id='{$iUserId}'";
        }

        $this->database()->delete(Phpfox::getT('socialbridge_token'), $sWhere);

        return true;
    }

    /**
     * @param $sService
     * @param null $iUserId
     * @return array
     */
    public function getTokenData($sService, $iUserId = null)
    {
        $iActualUserId = $this->getActualUserId();
        // load once with static cache
        $iViewerId = $iActualUserId;

        if (null == $iUserId) {
            $iUserId = $iViewerId;
        }
        if ($iUserId != $iViewerId) {
            return $this->getUserTokenData($sService, $iUserId);
        }

        if (null == $this->_aViewerTokenData) {
            $aRows = null;
            $found = true;

            $iUserId = $iActualUserId;

            $sSessionId = @session_id();

            $sTable = Phpfox::getT('socialbridge_token');

            $sWhere = "1";

            if (null != $iUserId) {
                $sWhere .= " AND user_id='{$iUserId}'";
            } else {
                if (null != $sSessionId) {
                    $sWhere .= " AND session_id='{$sSessionId}'";
                } else {
                    $found = false;
                }
            }

            if ($found) {
                $aRows = $this->database()->select('*')->from($sTable)->where($sWhere)->execute('getSlaveRows');
            }

            // prevent reload within a request

            $this->_aViewerTokenData = array();

            if ($aRows) {
                foreach ($aRows as $aRow) {
                    $this->_aViewerTokenData[$aRow['service']] = array(
                        @unserialize($aRow['token']),
                        @unserialize($aRow['profile'])
                    );
                }
            }
        }

        return isset($this->_aViewerTokenData[$sService]) ? $this->_aViewerTokenData[$sService] : array(
            null,
            null
        );
    }

    /**
     * @param null $sService
     * @param null $iUserId
     * @return string/array
     */
    public function removeTokenData($sService = null, $iUserId = null)
    {
        $aRow = null;
        $iViewerId = Phpfox::getUserId();

        if (null == $iUserId) {
            $iUserId = $iViewerId;
        }

        if ($iUserId != $iViewerId) {
            return $this->removeUserTokenData($sService, $iUserId);
        }

        $sSessionId = @session_id();

        $sTable = Phpfox::getT('socialbridge_token');

        if ($sService) {
            $sWhere = "service='{$sService}'";
        } else {
            $sWhere = " 1 ";
        }

        if (null != $iUserId) {
            $sWhere .= " AND (user_id='{$iUserId}')";
        } else {
            if (null != $sSessionId) {
                $sWhere .= " AND (session_id='{$sSessionId}')";
            }
        }

        $this->database()->delete($sTable, $sWhere);

        if ($iViewerId == $iUserId) {

            if ($sService) {
                // reset viewer token data registers
                $this->_aViewerTokenData[$sService] = array(
                    null,
                    null
                );
            } else {
                $this->_aViewerTokenData = array();
            }
        }

        return true;
    }

    /**
     * get provider wrapper object by name
     * @param string $sService available facebook,twitter,linkedin
     * @return array
     */
    public function getSetting($sService)
    {
        return isset($this->_aSettings[$sService]) ? $this->_aSettings[$sService] : array();
    }

    /**
     * if provider supported $sName
     * @param string $sService
     * @return true|false
     */
    public function hasProvider($sService)
    {
        return isset($this->_aSupporteds[$sService]) ? $this->_aSupporteds[$sService] : false;
    }

    /**
     * load provider object
     * @param $sService
     * @return SocialBridge_Service_Provider_Abstract
     */
    public function getProvider($sService)
    {
        static $providers = array();

        $sService = strtolower($sService);

        if (!isset($providers[$sService])) {
            if (!$this->hasProvider($sService)) {
                return null;
            }
            $providers[$sService] = Phpfox::getService('socialbridge.provider.' . $sService);
        }

        return $providers[$sService];
    }

    /**
     * get active providers data
     * @param int $iUserId
     * @return array
     */
    public function getAllProviderData($iUserId = null)
    {
        static $aResult = null;

        if (null == $aResult) {
            if ($iUserId == null) {
                $iUserId = Phpfox::getUserId();
            }

            $aResult = array();

            foreach ($this->_aSupporteds as $sService => $isActive) {
                if ($isActive) {
                    list($token, $aProfile) = $this->getTokenData($sService, $iUserId);

                    $aResult[$sService] = array(
                        'service' => $sService,
                        // backward compatible with ealier version
                        'name' => $sService,
                        'connected' => (bool)$aProfile,
                        'profile' => $aProfile,
                        'token' => $token,
                    );
                }
            }

        }

        return $aResult;
    }

    //Get profile info
    public function getPostedProfile($sService = "", $aUserProfileId = null)
    {
        switch ($sService) {
            case 'facebook' :
                return Phpfox::getService('socialbridge.provider.facebook')->getPostedProfile($aUserProfileId);
            case 'twitter' :
                return Phpfox::getService('socialbridge.provider.twitter')->getPostedProfile($aUserProfileId);
            default :
                return phpfox::getParam('core.path');
        }
    }

    /**
     * Check is in timeline mode
     * @return bool
     */

    public function timeline()
    {
        return false;
    }

    /**
     * post message to current conntected id
     * @param string $sService
     * @param array $aVal
     * @return TRUE // always.
     */
    public function post($sService, $aVal)
    {
        return $this->getProvider($sService)->post($aVal);
    }

    public function getActualUserId()
    {
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            $aPage = Phpfox::getService('pages')->getPage(Phpfox::getUserBy('profile_page_id'));

            return $aPage['user_id'];
        } else {

            return Phpfox::getUserId();
        }
    }

    public function getStaticPath()
    {
        $sCorePath = Phpfox::getParam('core.path');
        $sCorePath = str_replace("index.php" . PHPFOX_DS, "", $sCorePath);
        $sCorePath .= 'PF.Base' . PHPFOX_DS;

        return $sCorePath;
    }
}
