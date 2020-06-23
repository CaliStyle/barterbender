<?php

defined('PHPFOX') or exit('NO DICE!');

require_once __DIR__ . PHPFOX_DS . '..' . PHPFOX_DS . 'libs' . PHPFOX_DS . 'twitter.php';

class Socialbridge_Service_Provider_Twitter extends SocialBridge_Service_Provider_Abstract
{
    protected $_name = 'twitter';

    /**
     * get api object
     * @param null $iUserId
     * @param bool $bIsCache
     * @return object|Twitter
     */
    public function getApi($iUserId = null, $bIsCache = true)
    {
        if (null == $this->_api || !$bIsCache) {

            $aConfig = $this->getSetting();

            $this->_api = new Twitter($aConfig['consumer_key'], $aConfig['consumer_secret']);

            list($token, $profile) = $this->getTokenData($iUserId);

            if ($token && is_array($token)) {
                $this->_api->setOAuthToken($token['oauth_token']);
                $this->_api->setOAuthTokenSecret($token['oauth_token_secret']);
                $this->_profile = $profile;
            }
        }

        return $this->_api;
    }

    /**
     * get connect twitter API
     * @return array object
     */
    public function getProfile()
    {
        if (null == $this->_profile) {
            $oTwitter = $this->getApi();
            $me = $oTwitter->accountVerifyCredentials();
            $me['user_name'] = @$me['screen_name'];
            $me['full_name'] = @$me['name'];
            $me['identity'] = isset($me['id_str']) ? $me['id_str'] : 0;
            $me['service'] = 'twitter';
            $me['img_url'] = isset($me['profile_image_url']) ? $me['profile_image_url'] : "";
            $me['background_url'] = isset($me['profile_background_image_url']) ? $me['profile_background_image_url'] : "";
            $me['followers_count'] = isset($me['followers_count']) ? $me['followers_count'] : 0;
            $this->_profile = $me;
        }

        return $this->_profile;
    }

    /**
     * get list of conntected twitters friends of current viewer
     * alias to get contacts
     * @TODO get a large of contact etc: 100,000 followers
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @param int $sCursor
     * @return array
     * @throws Exception
     */
    public function getFriends($iPage = 1, $iLimit = 50, $sCursor = -1)
    {
        return $this->getContacts($iPage, $iLimit);
    }

    /**
     * get list of twitter friends of current viewer
     * @TODO get a large of contact etc: 100,000 followers
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @return array
     * @throws Exception
     */
    public function getContacts($iPage = 1, $iLimit = 50)
    {
        $oTwitter = $this->getApi();
        $iOffset = (($iPage - 1) * $iLimit) % 5000;
        if ($iOffset > 5000) {
            $iOffset = 0;
        }
        $aProfile = $this->getProfile();

        $iCount = $aProfile['followers_count'];
        $iUID = $aProfile['identity'];

        $sCursor = ($iOffset > 0 && isset($_SESSION['twitter']['cursor'][$iOffset])) ? $_SESSION['twitter']['cursor'][$iOffset] : -1;

        if (isset($_SESSION['twitter']['data'][$iOffset])) {
            $aFriendIds = $_SESSION['twitter']['data'][$iOffset];
        } else {
            do {
                $yncData = $oTwitter->followersIds($iUID, null, $sCursor);
                $aFriendIds = $yncData['ids'];
                $sNextCursor = $yncData['next_cursor'];

                if (!count($aFriendIds)) {
                    $sCursor = $sNextCursor;
                }
            } while (count($aFriendIds) == 0 && $sNextCursor > 0);

            if ($sNextCursor) {
                $_SESSION['twitter']['cursor'][$iOffset] = $sNextCursor;
            }

            $_SESSION['twitter']['data'][$iOffset] = $aFriendIds;
        }

        if (count($aFriendIds)) {
            // preg match count friends.
            $aFriendIds = array_slice($aFriendIds, $iOffset, $iLimit + 1);

            // process for large
            $aSlices = array_chunk($aFriendIds, 100);
            $aFriends = [];
            foreach ($aSlices as $aSlice) {
                $aFriends = array_merge($aFriends, $oTwitter->usersLookup($aSlice, null));
            }

            unset($_SESSION['yncontactimporter_fullValue']['twitter']);
            unset($_SESSION['yncontactimporter']['twitter']);
            foreach ($aFriends as $iKey => $item) {
                if (Phpfox::getService('contactimporter')->checkInviteIdExist($item['id'], Phpfox::getUserId(),
                    'twitter')) {
                    unset($aFriends[$iKey]);
                    $iCount--;
                } else {
                    $_SESSION['yncontactimporter']['twitter'][] = $item['id'];

                    $_SESSION['yncontactimporter_fullValue']['twitter'][] = array(
                        'id' => $item['id'],
                        'name' => $item['id'],
                        'pic' => $item['profile_image_url'],
                    );
                }
            }

            if (count($aFriends) == 0) {
                $aErrors['contacts'] = _p('contactimporter.you_have_sent_the_invitations_to_all_of_your_friends');
            }
            $aFriends = Phpfox::getService('contactimporter')->processSocialRows($aFriends);
            $aJoineds = Phpfox::getService('contactimporter')->checkSocialJoined($aFriendIds);
        } else {
            $aErrors['contacts'] = _p('contactimporter.there_is_not_contact_in_your_account');
        }

        return array(
            'iCnt' => $iCount,
            'iInvited' => '',
            'aInviteLists' => empty($aFriends) ? [] : $aFriends,
            'aJoineds' => empty($aJoineds) ? [] : $aJoineds,
            'aInvalids' => empty($aInvalids) ? [] : $aInvalids,
            'aErrors' => empty($aErrors) ? [] : $aErrors,
        );
    }

    /**
     * @param int $iUserId
     * @param string $sRecipient
     * @param string $sSubject
     * @param string $sMessage
     * @param string $sLink
     * @return true|false
     * @throws Exception
     */
    public function sendInvitation($iUserId, $sRecipient, $sSubject, $sMessage, $sLink)
    {
        /**
         * be care if this network does not install contact importer
         */

        $sMessage = str_replace("{full_user_name}", Phpfox::getUserBy('full_name'), $sMessage);

        if (!Phpfox::isModule('contactimporter')) {
            return false;
        }

        $iReturn = true;
        $oTwitter = $this->getApi();

        if ($oTwitter) {
            if ($iUserId) {
                // we should use shorten url to reduce inviation.
                $sMessage = substr($sMessage, 0, 120 - strlen($sLink)) . ' ' . $sLink;
                try {
                    $result = $oTwitter->directMessagesNew($sRecipient, null, $sMessage);
                    if (isset($result['errors'])) {
                        return $this->generateResult($result, false);
                    }

                    return $this->generateResult($result, true);
                } catch (Exception $e) {
                    return $this->generateResult($e->getMessage(), false);
                }
            }
        }

        return $iReturn;
    }

    /**
     * post a message to twitter
     * @param $aVals
     * @return array
     */
    public function post($aVals)
    {
        $aVals['status'] = preg_replace(array('/\[x=\d+\]/', '/\[\/x\]/'), '', $aVals['status']);
        $sMessage = html_entity_decode($aVals['status'], ENT_COMPAT, "UTF-8");

        //$sMessage = phpfox::getLib('parse.input')->clean($sMessage);
        if (false === strpos($aVals['url'], '://')) {
            $aVals['url'] = 'http://' . $aVals['url'] . '/';
        }
        str_replace('www.', '', $aVals['url']);

        $sBitlyUrl = $this->getShortBitlyUrl($aVals['url']);

        $iLen = strlen($sBitlyUrl);

        if (strlen($sMessage) > 139 - $iLen) {
            $sMessage = substr($sMessage, 0, 130 - $iLen) . '...';
        }
        $sMessage = $sMessage . ' ' . $sBitlyUrl;
        $oTwitter = $this->getApi();
        $sType = $aVals['type'];
        if (!isset($aVals['picture'])) {
            $aVals['picture'] = '';
        }

        $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . Phpfox::getUserId());
        $aFeed = Phpfox::getLib('cache')->get($sIdCache);

        if (isset($aFeed['url'])) {
            $aVals['picture'] = $aFeed['url'];
        }
        if (isset($aFeed['iItemId']) && $aFeed) {
            $iItemId = $aFeed['iItemId'];
            if ($sType == 'link') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('link'))->where('link_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image']) && $aRow['image']) {
                    $aVals['picture'] = $aRow['image'];
                }
            }

            if ($sType == 'resume') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('resume_basicinfo'))->where('resume_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . "/resume/" . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'photo') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('photo'))->where('photo_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['destination']) && $aRow['destination']) {
                    $sUrlImage = Phpfox::getParam('photo.url_photo') . $aRow['destination'];
                    $aVals['picture'] = sprintf($sUrlImage, '_1024');
                }
            }

            if ($sType == 'contest') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('contest'))->where('contest_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . "/contest/" . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'poll') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('poll'))->where('poll_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('poll.url_image') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'quiz') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('quiz'))->where('quiz_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('quiz.url_image') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'video') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('video'))->where('video_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('video.url_image') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'coupon') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('coupon'))->where('coupon_id = ' . $iItemId)->execute('getRow');

                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'directory') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('directory_business'))->where('business_id = ' . $iItemId)->execute('getRow');

                if (isset($aRow['logo_path']) && $aRow['logo_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['logo_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'advancedphoto') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('photo'))->where('photo_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['destination']) && $aRow['destination']) {
                    $sUrlImage = Phpfox::getParam('photo.url_photo') . $aRow['destination'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'advancedmarketplace') {
                $aImageRow = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT('advancedmarketplace_image'))
                    ->where('listing_id = ' . $iItemId)
                    ->execute('getRow');

                if (isset($aImageRow['image_path']) && $aImageRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . 'advancedmarketplace' . PHPFOX_DS . $aImageRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'karaoke_song') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('karaoke_song'))->where('song_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_file') . 'karaoke/image' . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '_thumb_225x225');
                }
            }

            if ($sType == 'karaoke_recording') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('karaoke_recording'))->where('recording_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_file') . 'karaoke/image' . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '_thumb_225x225');
                }
            }

            if ($sType == 'musicsharing_album' || $sType == "musicstore_album") {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('m2bmusic_album'))->where('album_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['album_image']) && $aRow['album_image']) {
                    if ($sType == 'musicsharing_album') {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicsharing' . PHPFOX_DS . $aRow['album_image'];
                    }
                    if ($sType == 'musicstore_album') {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicstore' . PHPFOX_DS . $aRow['album_image'];
                    }

                    $aVals['picture'] = empty($sUrlImage) ? '' : sprintf($sUrlImage, '');
                }
            }

            if ($sType == 'musicsharing_playlist' || $sType == "musicstore_playlist") {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('m2bmusic_playlist'))->where('playlist_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['playlist_image']) && $aRow['playlist_image']) {
                    if ($sType == 'musicsharing_playlist') {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicsharing' . PHPFOX_DS . $aRow['playlist_image'];
                    }
                    if ($sType == 'musicstore_playlist') {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicstore' . PHPFOX_DS . $aRow['playlist_image'];
                    }
                    $aVals['picture'] = empty($sUrlImage) ? '' : sprintf($sUrlImage, '_thumb_345x250');
                }
            }
            if ($sType == 'ultimatevideo_video') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynultimatevideo_videos'))->where('video_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '_500');
                }
            }
            if ($sType == 'ultimatevideo_playlist') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynultimatevideo_playlists'))->where('playlist_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '_500');
                }
            }

            if ($sType == 'ynblog') {
                $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynblog_blogs'))->where('blog_id = ' . $iItemId)->execute('getRow');
                if (isset($aRow['image_path']) && $aRow['image_path']) {
                    $sUrlImage = Phpfox::getParam('core.url_pic') . 'ynadvancedblog/' . $aRow['image_path'];
                    $aVals['picture'] = sprintf($sUrlImage, '_grid');
                }
            }
        }

        if (isset($aVals['picture']) && $aVals['picture']) {
            $content = file_get_contents($aVals['picture']);
            $contentBase64 = base64_encode($content);
            $response = $oTwitter->statusesUpdateWithMedia($sMessage, $contentBase64);
        } else {
            $response = $oTwitter->statusesUpdate($sMessage);
        }

        return $response;
    }

    public function getStatusHomeTimeline($aTwitter, $aParams)
    {
        $oTwitter = $this->getApi();

        #$oTwitter -> setToken($aTwitter['token'], $aTwitter['secret']);
        $update_status = $oTwitter->get_statusesHome_timeline($aParams);
        $temp = $update_status->response;

        return $temp;

    }

    public function getStatusUserTimeline($aTwitter, $aParams)
    {
        $oTwitter = $this->getApi();

        #$oTwitter -> setToken($aTwitter['token'], $aTwitter['secret']);
        $update_status = $oTwitter->get_statusesUser_timeline($aParams);
        $temp = $update_status->response;

        return $temp;

    }

    public function getAccessToken()
    {
        return $this->getApi()->getAccessToken();
    }

    public function getUrl()
    {
        Phpfox_Error::skip(true);
        $mReturn = $this->getApi()->getAuthorizationUrl();
        Phpfox_Error::skip(false);

        return $mReturn;
    }

    public function getFeeds($iLasFeedTimestamp, $iLimit, $iPage, $sIdentity, $iUserId = null)
    {
        $aGetParams = array(
            'exclude_replies' => 0,
            'include_rts' => 1,
            'include_entities' => 1
        );
        if ($iLasFeedTimestamp > 0) {
            $aGetParams['since_id'] = $iLasFeedTimestamp;
            $aGetParams['page'] = $iPage;
        } else {
            $aGetParams['count'] = $iLimit;
        }
        // signature
        //$id = null, $userId = null, $screenName = null, $sinceId = null, $maxId = null, $count = null, $page = null, $skipUser = false

        $oObject = $this->getApi($iUserId, false);
        $aDatas = $oObject->statusesHomeTimeline($count = $iLimit, $sinceId = $iLasFeedTimestamp, $maxId = null, null,
            null, null, null);

        return $aDatas;
    }

    public function getPostedProfile($iUserProfileId)
    {
        $oTwitter = $this->getApi();
        $me = $oTwitter->usersShow($iUserProfileId);
        $aUserProfile['user_name'] = isset($me['screen_name']) ? $me['screen_name'] : "";
        $aUserProfile['full_name'] = isset($me['name']) ? $me['name'] : "";
        $aUserProfile['identity'] = isset($me['id_str']) ? $me['id_str'] : 0;
        $aUserProfile['service'] = 'twitter';
        $aUserProfile['img_url'] = isset($me['profile_image_url']) ? $me['profile_image_url'] : "";

        return $aUserProfile;
    }


    /**
     * @param string $status , string[optional] $inReplyToStatusId, float[optional] $lat, float[optional] $long, string[optional] $placeId, bool[optional] $displayCoordinates
     * @param null $inReplyToStatusId
     * @param null $lat
     * @param null $long
     * @param null $placeId
     * @param bool $displayCoordinates
     * @return array
     */
    public function statusesUpdate(
        $status,
        $inReplyToStatusId = null,
        $lat = null,
        $long = null,
        $placeId = null,
        $displayCoordinates = false
    ) {
        $oObject = $this->getApi();

        return $oObject->statusesUpdate($status, $inReplyToStatusId, $lat, $long, $placeId, $displayCoordinates);
    }


}
