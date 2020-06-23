<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

defined('SOCIALCONNECT_CENTRALIZE_URL') or define('SOCIALCONNECT_CENTRALIZE_URL',
    'http://openid.younetid.com/auth/phpfox.php');

class OpenSocialConnect_Service_Opensocialconnect extends Phpfox_Service
{
    static private $_aDisableSupported = array('hyves' => 1);

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('socialconnect_services');
    }

    /**
     * get salt code string
     * @return string
     */
    private function _getSalt($iTotal = 3)
    {
        $sSalt = '';
        for ($i = 0; $i < $iTotal; $i++) {
            $sSalt .= chr(rand(33, 91));
        }

        return $sSalt;
    }

    /**
     * @param $sService
     * @return int
     */
    function getServiceId($sService)
    {
        $aService = Phpfox::getService('opensocialconnect.providers')->getProvider($sService);

        return intval($aService['service_id']);
    }

    /**
     * @param int $iUserId
     * @param array $aData
     * @return void
     */
    public function addAgent($iUserId, $aData)
    {
        $sService = $aData['service'];

        if ($sService == 'facebook' or $sService == 'facebook_') {
            $this->database()->insert(Phpfox::getT('cache'), array(
                'cache_data' => '{"user_id":' . $iUserId . ', "email":"' . $aData['email'] . '"}',
                'file_name' => 'fb_users_' . $aData['identity'],
                'data_size' => 0,
                'time_stamp' => PHPFOX_TIME
            ));
        }

        $iServiceId = $this->getServiceId($sService);

        $aInsert = array(
            'user_id' => $iUserId,
            'identity' => $aData['identity'],
            'service_id' => $iServiceId,
            'status' => 'login',
            'login' => '1',
            'data' => serialize($aData),
            'token_data' => serialize($aData),
            'token' => PHPFOX_TIME,
            'created_time' => PHPFOX_TIME,
            'login_time' => date('Y-m-d H:i:s'),
            'logout_time' => date('Y-m-d H:i:s'),
            'ordering' => 999
        );

        $this->database()->insert(Phpfox::getT('socialconnect_agents'), $aInsert);
    }

    /**
     * get agent by identity and service
     * @param string $sIdentity
     * @param string $sService
     * @param string $iServiceId OPTIONAL
     * @return array|NULL
     */
    public function getAgentByIdentityAndService($sIdentity, $sService, $iServiceId = null)
    {
        if (!$sIdentity) {
            return false;
        }

        if ($sService == 'facebook') {
            $aAgent = db()->select('sag.*')->from(Phpfox::getT('cache'),
                'sag')->where("file_name='fb_users_{$sIdentity}'")->limit(1)->order('time_stamp DESC')->execute('getRow');
            if ($aAgent) {
                $cache_data = json_decode($aAgent['cache_data']);
                $aAgent['user_id'] = $cache_data->user_id;

                return $aAgent;
            }
        }

        if (null == $iServiceId) {
            $iServiceId = $this->getServiceId($sService);
        }

        $aAgent = db()->select('*')->from(Phpfox::getT('socialconnect_agents'),
            'sag')->where("identity = '{$sIdentity}' AND service_id ='{$iServiceId}'")->execute('getRow');

        // data is dirty, for some reason, we  should clear to make it work faster for further request
        if ($aAgent && !$aAgent['user_id']) {
            $this->database()->delete(Phpfox::getT('socialconnect_agents'),
                "identity='{$sIdentity}' and service_id='{$iServiceId}'");

            return false;
        }

        return $aAgent;
    }

    public function getUserByIdentityAndService($sIdentity, $sService, $iServiceId = null)
    {
        $aAgent = $this->getAgentByIdentityAndService($sIdentity, $sService, $iServiceId);

        // if agent does not exsits
        if (!$aAgent) {
            return false;
        }

        // check if user associate with user_id is exsits
        $iUserId = intval($aAgent['user_id']);

        if ($iUserId) {
            /**
             * get user by user id
             * @see class User_Service_User::getUser
             */
            $aUser = Phpfox::getService('user')->getUser($iUserId);

            if ($aUser) {
                return $aUser;
            }
        }

        return false;
    }

    /**
     * get user by email
     * @param string $sEmail
     * @return array|NULL
     */
    public function getUserByEmail($sEmail)
    {
        return $this->database()->select('u.*')->from(Phpfox::getT('user'),
            'u')->where("u.email = '" . $this->database()->escape($sEmail) . "'")->execute('getSlaveRow');
    }

    /**
     * wrap call to User_Service_Auth::login
     * @param string $sEmail
     */
    public function loginByEmail($sEmail)
    {
        //signature: login($sLogin, $sPassword, $bRemember = false, $sType = 'email', $bNoPasswordCheck = false)
        return Phpfox::getService('user.auth')->login($sEmail, 'password', true, 'email', true);
    }

    public function addUser($aVals, $iUserGroupId = null)
    {
        if (!defined('PHPFOX_INSTALLER') && !Phpfox::getParam('user.allow_user_registration')) {
            return Phpfox_Error::display(_p('user.user_registration_has_been_disabled'));
        }

        $oParseInput = Phpfox::getLib('parse.input');
        $sSalt = $this->_getSalt();

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        if (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('user.validate_full_name')) {
            if (!Phpfox::getLib('validator')->check($aVals['full_name'], array('html', 'url'))) {
                return Phpfox_Error::set(_p('user.not_a_valid_name'));
            }
        }

        if (!defined('PHPFOX_INSTALLER') && !Phpfox::getService('ban')->check('display_name', $aVals['full_name'])) {
            Phpfox_Error::set(_p('user.this_display_name_is_not_allowed_to_be_used'));
        }
        if (!defined('PHPFOX_INSTALLER')) {
            if (!defined('PHPFOX_SKIP_EMAIL_INSERT')) {
                if (!Phpfox::getLib('mail')->checkEmail($aVals['email'])) {
                    return Phpfox_Error::set(_p('user.email_is_not_valid'));
                }
            }

            if (Phpfox::getLib('parse.format')->isEmpty($aVals['full_name'])) {
                Phpfox_Error::set(_p('user.provide_a_name_that_is_not_representing_an_empty_name'));
            }
        }

        $aVals['gender'] = isset($aVals['gender']) ? $aVals['gender'] : 0;

        $aInsert = array(
            'user_group_id' => ($iUserGroupId === null ? NORMAL_USER_ID : $iUserGroupId),
            'full_name' => $oParseInput->clean($aVals['full_name'], 255),
            'password' => Phpfox::getLib('hash')->setHash($aVals['password'], $sSalt),
            'password_salt' => $sSalt,
            'email' => $aVals['email'],
            'joined' => PHPFOX_TIME,
            'gender' => (defined('PHPFOX_INSTALLER') || (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('core.registration_enable_gender')) ? $aVals['gender'] : 0),
            'birthday' => isset($aVals['birthday']) ? $aVals['birthday'] : null,
            'birthday_search' => !empty($aVals['birthday_search']) ? $aVals['birthday_search'] : 0,
            'country_iso' => (defined('PHPFOX_INSTALLER') || (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('core.registration_enable_location')) ? $aVals['country_iso'] : null),
            'language_id' => ((!defined('PHPFOX_INSTALLER') && Phpfox::getLib('session')->get('language_id')) ? Phpfox::getLib('session')->get('language_id') : null),
            'time_zone' => (isset($aVals['time_zone']) && (defined('PHPFOX_INSTALLER') || (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('core.registration_enable_timezone'))) ? $aVals['time_zone'] : null),
            'last_ip_address' => Phpfox::getIp(),
            'last_activity' => PHPFOX_TIME
        );

        if (!defined('PHPFOX_INSTALLER') && (!Phpfox::getParam('opensocialconnect.allow_user_account_to_be_approved_automatically') || (Phpfox::getParam("user.approve_users") == true))) {
            $aInsert['view_id'] = '1';
        }

        if (Phpfox::VERSION >= '3.7.6') {
            $aVals['user_name'] = str_replace(' ', '-', $aVals['user_name']);
            $aVals['user_name'] = str_replace('_', '-', $aVals['user_name']);
        } else {
            $aVals['user_name'] = str_replace(' ', '_', $aVals['user_name']);
        }
        $aInsert['user_name'] = $oParseInput->clean($aVals['user_name']);

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_start')) ? eval($sPlugin) : false);

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $iId = $this->database()->insert(Phpfox::getT('user'), $aInsert);
        $aInsert['user_id'] = $iId;
        $aExtras = array('user_id' => $iId);

        #Add user image from social
        if ($iId && isset($aVals['large_img_url'])) {
            if ($sImagePath = $this->processUserImage($aVals['large_img_url'], $iId)) {
                $this->database()->update(Phpfox::getT('user'), array(
                    'user_image' => $sImagePath,
                    'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                ), 'user_id = ' . $iId);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_extra')) ? eval($sPlugin) : false);

        $array = array(
            'user_activity',
            'user_field',
            'user_space',
            'user_count'
        );

        // please clean elder data because some time user_activity has dirty data. etc: user_activity ac =  5600, but user_id ac =  1345, and it throws error!.
        foreach ($array as $table) {
            $table = Phpfox::getT($table);
            $this->database()->delete($table, 'user_id=' . intval($iId));
            $this->database()->insert($table, $aExtras);
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_feed')) ? eval($sPlugin) : false);

        if (isset($aVals['country_child_id'])) {
            Phpfox::getService('user.field.process')->update($iId, 'country_child_id', $aVals['country_child_id']);
        }

        if (!defined('PHPFOX_INSTALLER')) {
            $iFriendId = (int)Phpfox::getParam('user.on_signup_new_friend');
            if ($iFriendId > 0) {
                $this->database()->insert(Phpfox::getT('friend'), array(
                    'list_id' => 0,
                    'user_id' => $iId,
                    'friend_user_id' => $iFriendId,
                    'time_stamp' => PHPFOX_TIME
                ));

                $this->database()->insert(Phpfox::getT('friend'), array(
                    'list_id' => 0,
                    'user_id' => $iFriendId,
                    'friend_user_id' => $iId,
                    'time_stamp' => PHPFOX_TIME
                ));

                Phpfox::getService('friend.process')->updateFriendCount($iId, $iFriendId);
                Phpfox::getService('friend.process')->updateFriendCount($iFriendId, $iId);
            }
            if ($sPlugin = Phpfox_Plugin::get('user.service_process_add_check_1')) {
                eval($sPlugin);
            }
            if (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('user.verify_email_at_signup') == false && !isset($bDoNotSendWelcomeEmail)) {
                if (isset($aVals['autopassword'])) {
                    Phpfox::getLib('mail')->to($iId)->subject(array(
                        'core.welcome_email_subject',
                        array('site' => Phpfox::getParam('core.site_title'))
                    ))->message(array(
                        'opensocialconnect.welcome_email_content_sign_up',
                        array('email' => $aVals['email'], 'password' => $aVals['password'])
                    ))->send();
                } else {
                    Phpfox::getLib('mail')->to($iId)->subject(array(
                        'core.welcome_email_subject',
                        array('site' => Phpfox::getParam('core.site_title'))
                    ))->message(array('core.welcome_email_content'))->send();
                }

            }

            switch (Phpfox::getParam('user.on_register_privacy_setting')) {
                case 'network':
                    $iPrivacySetting = '1';
                    break;
                case 'friends_only':
                    $iPrivacySetting = '2';
                    break;
                case 'no_one':
                    $iPrivacySetting = '4';
                    break;
                default:
                    break;
            }

            if (isset($iPrivacySetting)) {
                $this->database()->insert(Phpfox::getT('user_privacy'), array(
                    'user_id' => $iId,
                    'user_privacy' => 'profile.view_profile',
                    'user_value' => $iPrivacySetting
                ));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('user.service_process_add_end')) ? eval($sPlugin) : false);

        if (!empty($aCustom)) {
            if (!Phpfox::getService('custom.process')->updateFields($iId, $iId, $aCustom, true)) {
                return false;
            }
        }

        $this->database()->insert(Phpfox::getT('user_ip'), array(
            'user_id' => $iId,
            'type_id' => 'register',
            'ip_address' => Phpfox::getIp(),
            'time_stamp' => PHPFOX_TIME
        ));

        if (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('user.verify_email_at_signup') && !isset($bSkipVerifyEmail)) {
            $aVals['user_id'] = $iId;
            $sHash = Phpfox::getService('user.verify')->getVerifyHash($aVals);
            $this->database()->insert(Phpfox::getT('user_verify'), array(
                'user_id' => $iId,
                'hash_code' => $sHash,
                'time_stamp' => Phpfox::getTime(),
                'email' => $aVals['email']
            ));
            $sLink = Phpfox::getLib('url')->makeUrl('user.verify', array('link' => $sHash));
            // send email
            if (isset($aVals['autopassword'])) {
                Phpfox::getLib('mail')->to($iId)->subject(array(
                    'user.please_verify_your_email_for_site_title',
                    array('site_title' => Phpfox::getParam('core.site_title'))
                ))->message(array(
                    'opensocialconnect.you_registered_an_account_on_site_title_before_being_able_to_use_your_account_you_need_to_verify_tha',
                    array(
                        'site_title' => Phpfox::getParam('core.site_title'),
                        'link' => $sLink,
                        'email' => $aVals['email'],
                        'password' => $aVals['password'],
                    )
                ))->send();

            } else {
                Phpfox::getLib('mail')->to($iId)->subject(array(
                    'user.please_verify_your_email_for_site_title',
                    array('site_title' => Phpfox::getParam('core.site_title'))
                ))->message(array(
                    'user.you_registered_an_account_on_site_title_before_being_able_to_use_your_account_you_need_to_verify_that_this_is_your_email_address_by_clicking_here_a_href_link_link_a',
                    array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)
                ))->send();
            }

        }

        if (!defined('PHPFOX_INSTALLER') && Phpfox::isModule('subscribe') && Phpfox::getParam('subscribe.enable_subscription_packages') && !empty($aVals['package_id'])) {
            $aPackage = Phpfox::getService('subscribe')->getPackage($aVals['package_id']);
            if (isset($aPackage['package_id'])) {
                $iPurchaseId = Phpfox::getService('subscribe.purchase.process')->add(array(
                    'package_id' => $aPackage['package_id'],
                    'currency_id' => $aPackage['default_currency_id'],
                    'price' => $aPackage['default_cost']
                ), $iId);

                $iDefaultCost = (int)str_replace('.', '', $aPackage['default_cost']);

                if ($iPurchaseId) {
                    if ($iDefaultCost > 0) {
                        define('PHPFOX_MUST_PAY_FIRST', $iPurchaseId);

                        Phpfox::getService('user.field.process')->update($iId, 'subscribe_id', $iPurchaseId);

                        return array(Phpfox::getLib('url')->makeUrl('subscribe.register', array('id' => $iPurchaseId)));
                    } else {
                        Phpfox::getService('subscribe.purchase.process')->update($iPurchaseId, $aPackage['package_id'],
                            'completed', $iId, $aPackage['user_group_id'], $aPackage['fail_user_group']);
                    }
                } else {
                    return false;
                }
            }
        }

        return $iId;
    }

    public function processUserImage($sImgUrl, $iUserId)
    {
        $oFile = Phpfox::getLib('file');
        $oImage = Phpfox::getLib('image');

        $sFileName = md5($iUserId . PHPFOX_TIME . uniqid());
        $sFileDir = $oFile->getBuiltDir(Phpfox::getParam('core.dir_user'));
        $sFilePath = $sFileDir . $sFileName . '%s.jpg';
        $sImagePath = str_replace(Phpfox::getParam('core.dir_user'), '', $sFilePath);

        $this->fetchImage($sImgUrl, sprintf($sFilePath, ''));
        //put file to CDN
        if (Phpfox::getParam('core.allow_cdn')) {
            Phpfox::getLib('cdn')->put(sprintf($sFilePath, ''));
        }

        $iFileSize = filesize(sprintf($sFilePath, ''));
        if ($iFileSize) {
            foreach (Phpfox::getParam('user.user_pic_sizes') as $iSize) {
                $oImage->createThumbnail(sprintf($sFilePath, ''), sprintf($sFilePath, '_' . $iSize), $iSize, $iSize);
                $oImage->createThumbnail(sprintf($sFilePath, ''), sprintf($sFilePath, '_' . $iSize . '_square'), $iSize,
                    $iSize, false);
            }

            return $sImagePath;
        }

        return false;
    }

    public function fetchImage($photo_url, $tmpfile)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $photo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        @file_put_contents($tmpfile, $data);
    }

    /**
     * get return url of service
     * @param string $sService
     * @return string
     */
    public function getReturnUrl($sService)
    {
        $oBridge = Phpfox::getService('socialbridge');

        if ($oBridge->hasProvider($sService)) {
            $sCallbackUrl = Phpfox::getLib('url')->makeUrl('opensocialconnect.sync.service_' . $sService);
            $sUrl = $oBridge->getProvider($sService)->getAuthUrl($sCallbackUrl);
        } else {
            $sReturnUrl = Phpfox::getLib('url')->makeUrl('opensocialconnect.sync.service_' . $sService);
            $sUrl = SOCIALCONNECT_CENTRALIZE_URL . '?' . http_build_query(array(
                    'service' => $sService,
                    'returnurl' => $sReturnUrl,
                ));
        }

        return $sUrl;

    }

    /**
     * clear quick signup session after signup success
     * @return void
     */
    public function clearSignupSessionData()
    {
        if (isset($_SESSION['quick_signup'])) {
            unset($_SESSION['quick_signup']);
        }
    }

    /**
     * register signup session data
     * @param string $sService ;
     * @param array $data
     * @return void
     */
    public function setSignupSessionData($sService, $data)
    {
        $_SESSION['quick_signup'][$sService] = $data;
    }

    /**
     * get signup session data
     * @return array
     */
    public function getSignupSessionData($sService)
    {
        if (isset($_SESSION['quick_signup'][$sService])) {
            return $_SESSION['quick_signup'][$sService];
        }

        return array();
    }

    /**
     * add setting
     * @param string $sService
     * @param string $sParams
     * @return void
     */
    public function addSettings($sService = "", $sParams = "")
    {
        if ($sService == "") {
            return false;
        }
        $this->database()->update($this->_sTable, array('params' => $sParams), 'name ="' . $sService . '"');
    }

    /**
     * should cache this query because every request.
     * @param int $iLimit
     * @param int $iLimitSelected
     * @param bool $bDisplay =  1
     * @return array
     */
    public function getEnabledProviders($iLimit = 5, $iLimitSelected = 20, $bDisplay = true)
    {
        $sCacheId = $this->cache()->set(array(
            'opensocialconnect',
            'getEnabledProviders_' . $iLimit . '_' . $iLimitSelected
        ));
        if (false == ($aProviders = $this->cache()->get($sCacheId))) {
            $aProviders = array();

            $aRows = $this->database()->select('*')->from($this->_sTable)->where('is_active=1')->order('ordering ASC')->limit($iLimitSelected)->execute('getSlaveRows');

            $oBridge = null;
            if (Phpfox::isModule('socialbridge')) {
                $oBridge = Phpfox::getService('socialbridge');
            }

            foreach ($aRows as $index => $aRow) {
                if (!isset(self::$_aDisableSupported[$aRow['name']])) {
                    if ($aRow['name'] == 'facebook' || $aRow['name'] == 'twitter' || $aRow['name'] == 'linkedin') {
                        if (Phpfox::isModule('socialbridge')) {
                            if ($oBridge->hasProvider($aRow['name'])) {
                                $aProviders[] = $aRow;
                            }
                        }
                    } else {
                        $aProviders[] = $aRow;
                    }
                }
            }

            $this->cache()->save($sCacheId, $aProviders);
        }

        return $aProviders;
    }

    /**
     * update user_id for socialbridge token after login success
     * @param int $iUserId , string $sService
     */
    public function updateBridgeToken($iUserId, $sService)
    {
        $sSessionId = @session_id();
        if (null != $sSessionId) {
            return $this->database()->update(Phpfox::getT('socialbridge_token'), array('user_id' => $iUserId),
                'user_id = 0 AND session_id = "' . $sSessionId . '" AND service = "' . $sService . '"');
        }

        return false;
    }

    /**
     * where to go after signup success
     * @param string $sService
     * @return string
     */
    public function getRedirect($sService)
    {
        if (Phpfox::isModule('contactimporter')) {
            switch ($sService) {
                case 'facebook':
                    $sProvider = 'facebook_';
                    $sCallback = 'facebook';
                    break;
                case 'google':
                    $sService = 'gmail';
                    $sProvider = 'gmail';
                    $sCallback = 'gmail';
                    break;
                case 'live':
                    $sProvider = 'hotmail';
                    $sCallback = 'hotmail';
                    break;
                case 'flickr2':
                    $sService = 'flickr';
                    $sProvider = 'flickr';
                    $sCallback = 'flickr';
                    break;
                default:
                    $sProvider = $sService;
                    $sCallback = $sService;
            }

            $aProviders = Phpfox::getService('contactimporter')->getAllowProviders();
            foreach ($aProviders as $k => $aProvider) {
                if ($aProvider['name'] == $sProvider) {
                    if (Phpfox::getService('socialbridge')->hasProvider($sService)) {
                        return 'contactimporter.' . $sService;
                    } else {
                        $sCentralizeUrl = Phpfox::getService('contactimporter')->getCentralizeUrl();
                        $sCallbackUrl = urlencode(Phpfox::getLib('url')->makeUrl('contactimporter.' . $sCallback));
                        $tokenName = Phpfox::getTokenName() . '[security_token]';
                        $sSecurityToken = Phpfox::getService('log.session')->getToken();

                        return $sCentralizeUrl . '?service=' . $sService . '&login=1&security_token=' . $sSecurityToken . '&token_name=' . $tokenName . '&callbackUrl=' . $sCallbackUrl;
                    }
                }
            }
        }

        return '';
    }

    public function getProviderOptions($provider)
    {
        $aRows = $this->database()
            ->select('*')
            ->from(Phpfox::getT('socialconnect_options'))
            ->where("service = '" . $provider . "'")
            ->execute('getRows');

        if ($aRows) {
            return $aRows;
        }

        return null;

    }

    public function getProviderFields($provider)
    {
        $aRows = $this->database()
            ->select('*')
            ->from(Phpfox::getT('socialconnect_fields'))
            ->where("service = '" . $provider . "'")
            ->execute('getRows');

        if ($aRows) {
            return $aRows;
        }

        return null;

    }

    public function updateProviderFields($aVals)
    {
        $provider = $aVals['provider'];
        unset($aVals['provider']);
        foreach ($aVals as $question => $field) {
            $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('socialconnect_fields'))
                ->where("service = '" . $provider . "' AND question = '$question'")
                ->execute('getRow');
            if ($aRow) {
                $this->database()->update(Phpfox::getT("socialconnect_fields"), array('field' => $field),
                    "question = '$question' AND service = '" . $provider . "'");
            } else {
                $this->database()->insert(Phpfox::getT('socialconnect_fields'),
                    array('service' => $provider, 'question' => $question, 'field' => $field));
            }
        }
    }
}

?>
